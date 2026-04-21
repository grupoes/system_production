<?php

namespace App\Controllers;

use App\Models\ProspectosModel;
use App\Models\ActividadesModel;

class ControlCarga extends BaseController
{
    public function index(): string
    {
        // Obtener el usuario asignado para hoy por defecto
        $db = \Config\Database::connect();
        
        // PHP day of week (1 = Lunes, 7 = Domingo)
        $dayOfWeek = date('N'); 
        
        // En la tabla dias: 1 = LUNES, ..., 6 = SÁBADO
        $defaultUserId = null;
        
        if ($dayOfWeek <= 6) {
            $builder = $db->table('asignacion_dias ad');
            $builder->select("usuario_id");
            $builder->where('ad.dia_id', $dayOfWeek);
            $builder->orderBy('ad.id', 'ASC');
            
            $asignacion = $builder->get()->getRowArray();
            
            if ($asignacion) {
                $defaultUserId = $asignacion['usuario_id'];
            }
        }

        return view('control_carga/index', [
            'defaultUserId' => $defaultUserId
        ]);
    }

    public function getPendingActivities()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('actividades a');
        $builder->select("
            a.id, 
            a.prioridad, 
            a.estado_progreso,
            p.titulo_prospecto as titulo,
            p.link_drive,
            p.contenido as observaciones,
            t.nombre as tarea,
            t.horas_estimadas as minutos,
            i.nombre as universidad,
            c.nombre as carrera,
            na.nombre as nivel_academico,
            o.nombre as contacto_origen,
            STRING_AGG(DISTINCT pers.nombres || ' ' || pers.apellidos || ' (' || pers.celular || ')', ', ') as prospecto_cliente,
            (pv.nombres || ' ' || pv.apellidos) as vendedor
        ");
        $builder->join('prospectos p', 'p.id = a.prospecto_id');
        $builder->join('tarea t', 't.id = a.tarea_id');
        $builder->join('carreras c', 'c.id = p.carrera_id', 'left');
        $builder->join('institucion i', 'i.id = c.institucion_id', 'left');
        $builder->join('nivel_academico na', 'na.id = p.nivel_academico_id', 'left');
        $builder->join('origen o', 'o.id = p.origen_id', 'left');
        $builder->join('prospecto_persona pp', 'pp.prospecto_id = p.id', 'left');
        $builder->join('personas pers', 'pers.id = pp.persona_id', 'left');
        $builder->join('usuarios uv', 'uv.id = p.usuario_venta_id', 'left');
        $builder->join('personas pv', 'pv.id = uv.persona_id', 'left');
        
        $builder->where('a.estado_progreso', 'PENDIENTE');
        $builder->where('p.estado_cliente', 'pendiente');
        $builder->groupBy('a.id, p.titulo_prospecto, p.link_drive, p.contenido, t.nombre, t.horas_estimadas, i.nombre, c.nombre, na.nombre, o.nombre, pv.nombres, pv.apellidos');
        $builder->orderBy('a.id', 'DESC');
        
        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function getUsers()
    {
        $db = \Config\Database::connect();
        $dayOfWeek = date('N');

        $builder = $db->table('usuarios u');
        $builder->select("
            u.id, 
            (p.nombres || ' ' || p.apellidos) as nombre,
            CASE WHEN EXISTS (
                SELECT 1 FROM asignacion_dias ad 
                WHERE ad.usuario_id = u.id AND ad.dia_id = $dayOfWeek
            ) THEN 1 ELSE 0 END as es_responsable
        ");
        $builder->join('personas p', 'p.id = u.persona_id');
        $builder->where('u.estado', true);
        $builder->orderBy('es_responsable', 'DESC');
        $builder->orderBy('p.nombres', 'ASC');
        
        $data = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    public function getUserSchedule()
    {
        $usuarioId = $this->request->getVar('usuario_id');
        $start = $this->request->getVar('start');
        $end = $this->request->getVar('end');

        if (!$usuarioId) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('horario_usuario hu');
        $builder->select("
            hu.id, 
            hu.fecha, 
            hu.hora_inicio, 
            hu.hora_fin,
            t.nombre as tarea,
            hu.categoria,
            a.color as actividad_color
        ");
        $builder->join('actividades a', 'a.id = hu.actividad_id', 'left');
        $builder->join('tarea t', 't.id = a.tarea_id', 'left');
        $builder->where('hu.usuario_id', $usuarioId);
        $builder->where('hu.fecha >=', date('Y-m-d', strtotime($start)));
        $builder->where('hu.fecha <=', date('Y-m-d', strtotime($end)));
        $builder->where('hu.estado', true);
        
        $data = $builder->get()->getResultArray();

        $events = [];
        foreach ($data as $item) {
            $defaultColor = $item['categoria'] === 'PROSPECTO' ? '#6366f1' : '#10b981';
            $events[] = [
                'id'    => $item['id'],
                'title' => $item['tarea'] ?? 'Actividad',
                'start' => $item['fecha'] . 'T' . $item['hora_inicio'],
                'end'   => $item['fecha'] . 'T' . $item['hora_fin'],
                'color' => !empty($item['actividad_color']) ? $item['actividad_color'] : $defaultColor
            ];
        }

        return $this->response->setJSON($events);
    }

    public function saveAsignacion()
    {
        $db = \Config\Database::connect();
        
        $actividadId = $this->request->getPost('actividad_id');
        $usuarioId = $this->request->getPost('usuario_id');
        $fecha = $this->request->getPost('fecha');
        $hora = $this->request->getPost('hora');

        // 1. Validar hora en el pasado
        $nowDate = date('Y-m-d');
        $nowTime = date('H:i');
        if ($fecha < $nowDate || ($fecha == $nowDate && $hora < $nowTime)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No puede asignar una actividad en una hora pasada.']);
        }

        $db->transStart();
        try {
            $actividadModel = new \App\Models\ActividadesModel();
            $actividad = $actividadModel->select('actividades.*, p.fecha_entrega')
                                        ->join('prospectos p', 'p.id = actividades.prospecto_id', 'left')
                                        ->where('actividades.id', $actividadId)
                                        ->get()->getRowArray();
                                        
            if (!$actividad) throw new \Exception("Actividad no encontrada");

            $minutos = (int)$actividad['tiempo_estimado_minutos'];

            // Get all existing schedules from $fecha and $hora onwards for this user
            $existingSchedules = $db->table('horario_usuario hu')
                ->select('hu.*, a.prioridad, p.fecha_entrega')
                ->join('actividades a', 'a.id = hu.actividad_id')
                ->join('prospectos p', 'p.id = a.prospecto_id', 'left')
                ->where('hu.usuario_id', $usuarioId)
                ->where('hu.estado', true)
                ->groupStart()
                    ->where('hu.fecha >', $fecha)
                    ->orGroupStart()
                        ->where('hu.fecha', $fecha)
                        ->where('hu.hora_fin >', $hora)
                    ->groupEnd()
                ->groupEnd()
                ->orderBy('hu.fecha', 'ASC')
                ->orderBy('hu.hora_inicio', 'ASC')
                ->get()->getResultArray();

            $fixedBlocks = [];
            $blocksToSchedule = [];
            
            // First, add the new VENTAS block to schedule
            $blocksToSchedule[] = [
                'id' => 'NEW',
                'actividad_id' => $actividadId,
                'minutos' => $minutos,
                'fecha_entrega' => $actividad['fecha_entrega'],
                'categoria' => 'VENTAS'
            ];

            $blocksToDelete = [];

            foreach ($existingSchedules as $sch) {
                $blocksToDelete[] = $sch['id'];

                if ($sch['prioridad'] === 'ALTA') {
                    $fixedBlocks[] = [
                        'fecha' => $sch['fecha'],
                        'hora_inicio' => $sch['hora_inicio'],
                        'hora_fin' => $sch['hora_fin'],
                        'actividad_id' => $sch['actividad_id'],
                        'categoria' => $sch['categoria']
                    ];
                } else {
                    $schStart = strtotime($sch['fecha'] . ' ' . $sch['hora_inicio']);
                    $schEnd = strtotime($sch['fecha'] . ' ' . $sch['hora_fin']);
                    $reqStart = strtotime($fecha . ' ' . $hora);
                    
                    if ($schStart < $reqStart && $schEnd > $reqStart) {
                        $keptMins = round(($reqStart - $schStart) / 60);
                        if ($keptMins > 0) {
                            $fixedBlocks[] = [
                                'fecha' => $sch['fecha'],
                                'hora_inicio' => $sch['hora_inicio'],
                                'hora_fin' => date('H:i:s', $reqStart),
                                'actividad_id' => $sch['actividad_id'],
                                'categoria' => $sch['categoria']
                            ];
                        }
                        $remainingMins = round(($schEnd - $reqStart) / 60);
                        if ($remainingMins > 0) {
                            $blocksToSchedule[] = [
                                'id' => $sch['id'],
                                'actividad_id' => $sch['actividad_id'],
                                'minutos' => $remainingMins,
                                'fecha_entrega' => $sch['fecha_entrega'],
                                'categoria' => $sch['categoria']
                            ];
                        }
                    } else {
                        $duration = round(($schEnd - $schStart) / 60);
                        $blocksToSchedule[] = [
                            'id' => $sch['id'],
                            'actividad_id' => $sch['actividad_id'],
                            'minutos' => $duration,
                            'fecha_entrega' => $sch['fecha_entrega'],
                            'categoria' => $sch['categoria']
                        ];
                    }
                }
            }

            // Consolidar bloques de la misma actividad para evitar fragmentos innecesarios
            $mergedBlocks = [];
            foreach ($blocksToSchedule as $block) {
                $aid = $block['actividad_id'];
                if (isset($mergedBlocks[$aid])) {
                    $mergedBlocks[$aid]['minutos'] += $block['minutos'];
                } else {
                    $mergedBlocks[$aid] = $block;
                }
            }
            $blocksToSchedule = array_values($mergedBlocks);

            $cursorDate = $fecha;
            $cursorTime = $hora;

            $newSchedules = [];

            foreach ($blocksToSchedule as $block) {
                $minsRemaining = $block['minutos'];
                
                while ($minsRemaining > 0) {
                    $dayOfWeek = date('N', strtotime($cursorDate));
                    if ($dayOfWeek == 7) {
                        $cursorDate = date('Y-m-d', strtotime("$cursorDate + 1 day"));
                        $cursorTime = '08:00:00';
                        continue;
                    }

                    if ($cursorTime < '08:00:00') $cursorTime = '08:00:00';

                    if ($dayOfWeek <= 5) {
                        if ($cursorTime >= '13:00:00' && $cursorTime < '15:00:00') {
                            $cursorTime = '15:00:00';
                        } elseif ($cursorTime >= '19:00:00') {
                            $cursorDate = date('Y-m-d', strtotime("$cursorDate + 1 day"));
                            $cursorTime = '08:00:00';
                            continue;
                        }
                    } elseif ($dayOfWeek == 6) {
                        if ($cursorTime >= '13:00:00') {
                            $cursorDate = date('Y-m-d', strtotime("$cursorDate + 1 day"));
                            $cursorTime = '08:00:00';
                            continue;
                        }
                    }

                    $blockEnd = '13:00:00';
                    if ($dayOfWeek <= 5 && $cursorTime >= '15:00:00') {
                        $blockEnd = '19:00:00';
                    }

                    $cursorStamp = strtotime("$cursorDate $cursorTime");
                    $blockEndStamp = strtotime("$cursorDate $blockEnd");
                    
                    foreach ($fixedBlocks as $fb) {
                        $fbStart = strtotime($fb['fecha'] . ' ' . $fb['hora_inicio']);
                        $fbEnd = strtotime($fb['fecha'] . ' ' . $fb['hora_fin']);
                        
                        if ($fbStart <= $cursorStamp && $fbEnd > $cursorStamp) {
                            $cursorTime = $fb['hora_fin'];
                            $cursorStamp = strtotime("$cursorDate $cursorTime");
                            continue 2;
                        }
                        
                        if ($fbStart > $cursorStamp && $fbStart < $blockEndStamp) {
                            $blockEndStamp = $fbStart;
                            $blockEnd = $fb['hora_inicio'];
                        }
                    }

                    $availableMins = round(($blockEndStamp - $cursorStamp) / 60);
                    if ($availableMins <= 0) {
                        $cursorTime = $blockEnd;
                        continue;
                    }

                    $minsToUse = min($minsRemaining, $availableMins);
                    $endTime = date('H:i:s', strtotime("$cursorDate $cursorTime + $minsToUse minutes"));

                    if (!empty($block['fecha_entrega'])) {
                        if ($cursorDate > $block['fecha_entrega']) {
                            throw new \Exception("No se puede reprogramar el cronograma. La actividad desplazada con ID " . $block['actividad_id'] . " superaría su fecha de entrega (" . $block['fecha_entrega'] . ").");
                        }
                    }

                    $newSchedules[] = [
                        'usuario_id'   => $usuarioId,
                        'actividad_id' => $block['actividad_id'],
                        'fecha'        => $cursorDate,
                        'hora_inicio'  => $cursorTime,
                        'hora_fin'     => $endTime,
                        'categoria'    => $block['categoria'],
                        'tipo'         => 'programado',
                        'estado'       => true,
                        'created_at'   => date('Y-m-d H:i:s')
                    ];

                    $minsRemaining -= $minsToUse;
                    $cursorTime = $endTime;
                }
            }

            if (!empty($blocksToDelete)) {
                $db->table('horario_usuario')->whereIn('id', $blocksToDelete)->delete();
            }

            foreach ($newSchedules as $ns) {
                $db->table('horario_usuario')->insert($ns);
            }

            foreach ($fixedBlocks as $fb) {
                $db->table('horario_usuario')->insert([
                    'usuario_id' => $usuarioId,
                    'actividad_id' => $fb['actividad_id'],
                    'fecha' => $fb['fecha'],
                    'hora_inicio' => $fb['hora_inicio'],
                    'hora_fin' => $fb['hora_fin'],
                    'categoria' => $fb['categoria'],
                    'tipo' => 'programado',
                    'estado' => true,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Update the new activity status and color
            $actividadModel->update($actividadId, [
                'usuario_id' => $usuarioId,
                'estado_progreso' => 'PROGRAMADO',
                'fecha_inicio' => $fecha,
                'hora_inicio' => $hora,
                'color' => '#8B5CF6' // Purple color for VENTAS
            ]);

            $db->transComplete();
            if ($db->transStatus() === false) throw new \Exception("Error al guardar en la base de datos.");

            return $this->response->setJSON(['status' => 'success', 'message' => 'Actividad de VENTAS asignada. Cronograma reprogramado con éxito.']);
        } catch (\Throwable $th) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
}
