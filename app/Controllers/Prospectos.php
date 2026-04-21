<?php

namespace App\Controllers;

use App\Models\ProspectosModel;
use App\Models\PersonaModel;
use App\Models\ProspectoPersonaModel;
use App\Models\InstitucionModel;
use App\Models\CarreraModel;
use App\Models\NivelAcademicoModel;
use App\Models\OrigenModel;
use App\Models\TareaModel;
use App\Models\NotificacionModel;

class Prospectos extends BaseController
{
    public function index(): string
    {
        return view('prospectos/index');
    }

    public function getList()
    {
        $page = $this->request->getVar('page') ?? 1;
        $limit = $this->request->getVar('limit') ?? 10;
        $search = $this->request->getVar('search');

        $db = \Config\Database::connect();
        $builder = $db->table('prospectos p');
        $builder->select("
            p.id, 
            p.created_at, 
            p.estado_cliente,
            i.nombre as universidad, 
            c.nombre as carrera,
            (v_pers.nombres || ' ' || v_pers.apellidos) as vendedor,
            STRING_AGG(c_pers.nombres || ' ' || c_pers.apellidos || ' (' || c_pers.celular || ')', '<br>') as contactos
        ");
        $builder->join('carreras c', 'c.id = p.carrera_id', 'left');
        $builder->join('institucion i', 'i.id = c.institucion_id', 'left');
        $builder->join('usuarios v_user', 'v_user.id = p.usuario_venta_id', 'left');
        $builder->join('personas v_pers', 'v_pers.id = v_user.persona_id', 'left');
        $builder->join('prospecto_persona pp', 'pp.prospecto_id = p.id', 'left');
        $builder->join('personas c_pers', 'c_pers.id = pp.persona_id', 'left');

        if (!empty($search)) {
            $s = $db->escapeLikeString($search);
            $builder->groupStart()
                ->where("i.nombre ILIKE '%$s%'")
                ->orWhere("c.nombre ILIKE '%$s%'")
                ->orWhere("(v_pers.nombres || ' ' || v_pers.apellidos) ILIKE '%$s%'")
                ->orWhere("(c_pers.nombres || ' ' || c_pers.apellidos) ILIKE '%$s%'")
                ->orWhere("c_pers.celular ILIKE '%$s%'")
                ->orWhere("CAST(p.created_at AS TEXT) ILIKE '%$s%'")
                ->groupEnd();
        }

        $builder->where('p.estado_cliente', 'pendiente');
        $builder->groupBy('p.id, i.nombre, c.nombre, v_pers.nombres, v_pers.apellidos');
        $builder->orderBy('p.id', 'DESC');

        // Paginación manual para el builder
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $limit;
        $data = $builder->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'status'   => 'success',
            'data'     => $data,
            'total'    => $total,
            'page'     => $page,
            'limit'    => $limit,
            'lastPage' => ceil($total / $limit)
        ]);
    }

    public function getProspectoDetalle($id)
    {
        $db = \Config\Database::connect();

        // Datos generales del prospecto
        $prospecto = $db->table('prospectos p')
            ->select("
                p.id,
                p.titulo_prospecto,
                p.prioridad,
                p.link_drive,
                p.contenido as observaciones,
                p.fecha_contacto,
                p.fecha_entrega,
                p.estado_cliente,
                i.nombre as universidad,
                c.nombre as carrera,
                na.nombre as nivel_academico,
                o.nombre as origen,
                (v_pers.nombres || ' ' || v_pers.apellidos) as vendedor
            ")
            ->join('carreras c', 'c.id = p.carrera_id', 'left')
            ->join('institucion i', 'i.id = c.institucion_id', 'left')
            ->join('nivel_academico na', 'na.id = p.nivel_academico_id', 'left')
            ->join('origen o', 'o.id = p.origen_id', 'left')
            ->join('usuarios v_user', 'v_user.id = p.usuario_venta_id', 'left')
            ->join('personas v_pers', 'v_pers.id = v_user.persona_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (!$prospecto) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Prospecto no encontrado.']);
        }

        // Contactos
        $contactos = $db->table('prospecto_persona pp')
            ->select('pers.nombres, pers.apellidos, pers.celular')
            ->join('personas pers', 'pers.id = pp.persona_id')
            ->where('pp.prospecto_id', $id)
            ->get()->getResultArray();

        // Actividades (tareas) con detalle completo
        $actividades = $db->table('actividades a')
            ->select("
                a.id,
                a.prioridad,
                a.estado_progreso,
                a.color,
                a.fecha_inicio,
                a.hora_inicio,
                a.tiempo_estimado_minutos,
                t.nombre as tarea,
                (u_pers.nombres || ' ' || u_pers.apellidos) as auxiliar,
                a.created_at
            ")
            ->join('tarea t', 't.id = a.tarea_id', 'left')
            ->join('usuarios u', 'u.id = a.usuario_id', 'left')
            ->join('personas u_pers', 'u_pers.id = u.persona_id', 'left')
            ->where('a.prospecto_id', $id)
            ->where('a.estado', true)
            ->orderBy('a.id', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status'      => 'success',
            'prospecto'   => $prospecto,
            'contactos'   => $contactos,
            'actividades' => $actividades
        ]);
    }

    public function registro(): string
    {
        $id = $this->request->getVar('id');
        $data = ['title' => 'Registrar Potencial Cliente'];

        if ($id) {
            $db = \Config\Database::connect();

            // Datos del prospecto + universidad_id (desde carrera)
            $prospecto = $db->table('prospectos p')
                ->select('p.*, c.institucion_id as universidad_id')
                ->join('carreras c', 'c.id = p.carrera_id', 'left')
                ->where('p.id', $id)
                ->get()->getRowArray();

            if ($prospecto) {
                $data['prospecto'] = $prospecto;
                $data['title'] = 'Editar Potencial Cliente';

                // Tarea vinculada
                $actividad = $db->table('actividades')
                    ->where('prospecto_id', $id)
                    ->get()->getRowArray();
                $data['actividad'] = $actividad;

                // Contactos (personas)
                $contactos = $db->table('prospecto_persona pp')
                    ->select('pers.*')
                    ->join('personas pers', 'pers.id = pp.persona_id')
                    ->where('pp.prospecto_id', $id)
                    ->get()->getResultArray();
                $data['contactos'] = $contactos;
            }
        }

        return view('prospectos/registro', $data);
    }

    public function getDataForm()
    {
        $universidades = (new InstitucionModel())->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        $niveles = (new NivelAcademicoModel())->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        $origenes = (new OrigenModel())->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        $tareas = (new TareaModel())->where('estado', true)->orderBy('nombre', 'ASC')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'universidades' => $universidades,
            'niveles' => $niveles,
            'origenes' => $origenes,
            'tareas' => $tareas
        ]);
    }

    public function getCarreras($universidadId)
    {
        $model = new CarreraModel();
        $data = $model->where('institucion_id', $universidadId)->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        $prospectoModel = new ProspectosModel();
        $personaModel = new PersonaModel();
        $ppModel = new ProspectoPersonaModel();
        $actividadModel = new \App\Models\ActividadesModel();
        $notificacionModel = new NotificacionModel();
        $historialModel = new \App\Models\HistorialEstadoProspectoModel();

        $id = $this->request->getPost('id');
        $tareaId = $this->request->getPost('tarea_id');

        if (empty($tareaId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe seleccionar una tarea válida.']);
        }

        $tarea = (new TareaModel())->find($tareaId);
        if (!$tarea) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'La tarea seleccionada no existe.']);
        }

        $tiempoEstimado = $tarea['horas_estimadas'] ?? 0;

        $db->transException(true)->transStart();
        try {
            // 1. Datos del Prospecto
            $fechaEntrega = $this->request->getPost('fecha_entrega');
            $dataProspecto = [
                'titulo_prospecto'   => strtoupper($this->request->getPost('titulo_trabajo')),
                'origen_id'          => $this->request->getPost('origen_id'),
                'nivel_academico_id' => $this->request->getPost('nivel_id') ?: null,
                'carrera_id'         => $this->request->getPost('carrera_id') ?: null,
                'fecha_entrega'      => !empty($fechaEntrega) ? $fechaEntrega : null,
                'link_drive'         => $this->request->getPost('link_drive') ?: null,
                'contenido'          => $this->request->getPost('observaciones'),
                'prioridad'          => $this->request->getPost('prioridad') ?? 'NORMAL',
            ];

            if (!empty($id)) {
                $prospectoModel->update($id, $dataProspecto);
                $prospectoId = $id;

                // Si se forzó el estado a CLIENTE
                $nuevoEstado = $this->request->getPost('nuevo_estado_cliente');
                if ($nuevoEstado === 'cliente') {
                    // Cerrar estado anterior (PROSPECTO -> CLIENTE)
                    $historialModel->where('prospecto_id', $id)
                        ->where('fecha_fin', null)
                        ->set(['fecha_fin' => date('Y-m-d H:i:s')])
                        ->update();

                    $prospectoModel->update($id, ['estado_cliente' => 'cliente']);
                    
                    // Historial de Estado (CLIENTE)
                    $historialModel->insert([
                        'prospecto_id' => $id,
                        'estado'       => 'CLIENTE',
                        'fecha_inicio' => date('Y-m-d H:i:s'),
                        'usuario_id'   => session()->get('id'),
                        'comentario'   => 'EL USUARIO ' . session()->get('nombre') . ' CONVIRTIÓ EL PROSPECTO EN CLIENTE'
                    ]);
                }

                $colorPrioridad = '#24BF17'; // NORMAL
                if ($dataProspecto['prioridad'] === 'ALTA') $colorPrioridad = '#F54927';
                elseif ($dataProspecto['prioridad'] === 'BAJA') $colorPrioridad = '#3B82F6';

                $actividadModel->where('prospecto_id', $id)->set([
                    'tarea_id'                => $tareaId,
                    'prioridad'               => $dataProspecto['prioridad'],
                    'color'                   => $colorPrioridad,
                    'tiempo_estimado_minutos' => $tiempoEstimado
                ])->update();

                $ppModel->where('prospecto_id', $id)->delete();
            } else {
                $dataProspecto['fecha_contacto'] = date('Y-m-d');
                $dataProspecto['estado']           = true;
                $dataProspecto['usuario_venta_id'] = session()->get('id');
                $dataProspecto['estado_cliente']   = 'pendiente';

                $prospectoId = $prospectoModel->insert($dataProspecto);
                if (!$prospectoId) throw new \Exception('Error prospecto: ' . json_encode($prospectoModel->errors()));

                // Historial de Estado
                $historialModel->insert([
                    'prospecto_id' => $prospectoId,
                    'estado'       => 'PROSPECTO',
                    'fecha_inicio' => date('Y-m-d H:i:s'),
                    'usuario_id'   => session()->get('id'),
                    'comentario'   => 'EL USUARIO ' . session()->get('nombre') . ' REGISTRÓ AL POTENCIAL CLIENTE'
                ]);

                $colorPrioridad = '#24BF17'; // NORMAL
                if ($dataProspecto['prioridad'] === 'ALTA') $colorPrioridad = '#F54927';
                elseif ($dataProspecto['prioridad'] === 'BAJA') $colorPrioridad = '#3B82F6';

                $actividadModel->insert([
                    'prospecto_id'            => $prospectoId,
                    'usuario_id'              => session()->get('id'),
                    'tarea_id'                => $tareaId,
                    'estado'                  => true,
                    'prioridad'               => $dataProspecto['prioridad'],
                    'color'                   => $colorPrioridad,
                    'estado_progreso'         => 'PENDIENTE',
                    'fecha_inicio'            => date('Y-m-d'),
                    'tiempo_estimado_minutos' => $tiempoEstimado
                ]);

                // Notificaciones
                $usuariosCC = $db->table('usuarios')->where(['rol_id' => 5, 'estado' => true])->get()->getResultArray();
                foreach ($usuariosCC as $u) {
                    $notificacionModel->insert([
                        'usuario_id'   => $u['id'],
                        'remitente_id' => session()->get('id'),
                        'titulo'       => 'NUEVO PROSPECTO',
                        'mensaje'      => $tarea['nombre'] ?? 'Sin tarea',
                        'tipo'         => 'INFO',
                        'prioridad'    => 1,
                        'es_leida'     => false
                    ]);
                }
            }

            // 3. Contactos
            $nombresArr = $this->request->getPost('nombres');
            $apellidosArr = $this->request->getPost('apellidos');
            $celularesArr = $this->request->getPost('celulares');

            if (is_array($nombresArr)) {
                foreach ($nombresArr as $i => $nombre) {
                    $celular = $celularesArr[$i] ?? '';
                    $apellido = $apellidosArr[$i] ?? '';

                    if (!empty($nombre) || !empty($celular)) {
                        $personaId = $personaModel->insert([
                            'nombres'   => strtoupper($nombre),
                            'apellidos' => strtoupper($apellido),
                            'celular'   => $celular,
                            'estado'    => true
                        ]);

                        if ($personaId) {
                            $ppModel->insert([
                                'persona_id'   => $personaId,
                                'prospecto_id' => $prospectoId
                            ]);
                        }
                    }
                }
            }

            $db->transComplete();
            return $this->response->setJSON(['status' => 'success', 'message' => 'Registro completado con éxito.']);
        } catch (\Throwable $th) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error DB: ' . $th->getMessage()
            ]);
        }
    }

    public function saveCarrera()
    {
        $nombre = $this->request->getPost('nombre');
        $universidadId = $this->request->getPost('universidad_id');

        if (empty($nombre) || empty($universidadId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos obligatorios.']);
        }

        $model = new \App\Models\CarreraModel();
        $id = $model->insert([
            'nombre'         => strtoupper($nombre),
            'institucion_id' => $universidadId,
            'estado'         => true
        ]);

        if ($id) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Carrera registrada correctamente.',
                'id'      => $id,
                'nombre'  => strtoupper($nombre)
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo registrar la carrera.']);
    }

    public function clientesIndex()
    {
        return view('prospectos/clientes_index', [
            'title' => 'Lista de Clientes'
        ]);
    }

    public function getClientesList()
    {
        $db = \Config\Database::connect();
        
        $page = $this->request->getVar('page') ?: 1;
        $limit = $this->request->getVar('limit') ?: 10;
        $search = $this->request->getVar('search') ?: '';
        $offset = ($page - 1) * $limit;

        $builder = $db->table('prospectos p');
        $builder->select("
            p.id, 
            p.titulo_prospecto as titulo,
            i.nombre as universidad, 
            c.nombre as carrera,
            h.fecha_inicio as registro_cliente,
            STRING_AGG(c_pers.nombres || ' ' || c_pers.apellidos || ' (' || c_pers.celular || ')', '<br>') as contactos
        ");
        $builder->join('carreras c', 'c.id = p.carrera_id', 'left');
        $builder->join('institucion i', 'i.id = c.institucion_id', 'left');
        $builder->join('prospecto_persona pp', 'pp.prospecto_id = p.id', 'left');
        $builder->join('personas c_pers', 'c_pers.id = pp.persona_id', 'left');
        $builder->join('historial_estados_prospecto h', "h.prospecto_id = p.id AND h.estado = 'CLIENTE'", 'left');
        
        $builder->where('p.estado_cliente', 'cliente');

        if (!empty($search)) {
            $s = $db->escapeLikeString($search);
            $builder->groupStart()
                ->where("p.titulo_prospecto ILIKE '%$s%'")
                ->orWhere("i.nombre ILIKE '%$s%'")
                ->orWhere("c.nombre ILIKE '%$s%'")
                ->orWhere("c_pers.nombres ILIKE '%$s%'")
                ->orWhere("c_pers.apellidos ILIKE '%$s%'")
                ->orWhere("c_pers.celular ILIKE '%$s%'")
                ->groupEnd();
        }
        
        $builder->groupBy('p.id, i.nombre, c.nombre, h.fecha_inicio');
        $builder->orderBy('h.fecha_inicio', 'DESC');
        
        // Count for pagination
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults();

        $data = $builder->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'status'   => 'success',
            'data'     => $data,
            'total'    => $total,
            'page'     => $page,
            'limit'    => $limit,
            'lastPage' => ceil($total / $limit)
        ]);
    }

    public function getScheduleData()
    {
        $db = \Config\Database::connect();
        
        // Tareas
        $tareas = $db->table('tarea')->where('estado', true)->orderBy('nombre', 'ASC')->get()->getResultArray();
        
        // Auxiliares (Usuarios)
        $dayOfWeek = date('N');
        $auxiliares = $db->table('usuarios u')
            ->select("
                u.id, 
                (p.nombres || ' ' || p.apellidos) as nombre,
                CASE WHEN EXISTS (
                    SELECT 1 FROM horario_usuario hu
                    WHERE hu.usuario_id = u.id AND hu.fecha >= CURRENT_DATE AND hu.estado = true AND hu.categoria = 'PRODUCCION'
                ) THEN 1 ELSE 0 END as tiene_horario
            ")
            ->join('personas p', 'p.id = u.persona_id')
            ->where('u.estado', true)
            ->orderBy('p.nombres', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status'     => 'success',
            'tareas'     => $tareas,
            'auxiliares' => $auxiliares
        ]);
    }

    public function saveSchedule()
    {
        $prospectoId = $this->request->getPost('prospecto_id');
        $tareaId = $this->request->getPost('tarea_id');
        $usuarioId = $this->request->getPost('usuario_id');
        $prioridad = $this->request->getPost('prioridad') ?: 'NORMAL';
        $fecha = $this->request->getPost('fecha');
        $hora = $this->request->getPost('hora');

        if (!$prospectoId || !$tareaId || !$usuarioId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos obligatorios.']);
        }

        if ($fecha && $hora) {
            $dayOfWeek = date('N', strtotime($fecha)); // 1 (Mon) to 7 (Sun)
            $timeFormat = date('H:i', strtotime($hora));
            
            $isValidTime = false;
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Lunes a Viernes
                if (($timeFormat >= '08:00' && $timeFormat <= '13:00') || ($timeFormat >= '15:00' && $timeFormat <= '19:00')) {
                    $isValidTime = true;
                }
            } elseif ($dayOfWeek == 6) { // Sábado
                if ($timeFormat >= '08:00' && $timeFormat <= '13:00') {
                    $isValidTime = true;
                }
            }

            if (!$isValidTime) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Horario no permitido. Lunes a Viernes: 08:00 a 13:00 y 15:00 a 19:00. Sábados: 08:00 a 13:00.'
                ]);
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $actividadModel = new \App\Models\ActividadesModel();
            $tarea = $db->table('tarea')->where('id', $tareaId)->get()->getRowArray();
            $minutos = $tarea ? (int)$tarea['horas_estimadas'] : 0;
            
            // Si no se proporcionó fecha ni hora, verificar si ya tiene horario para auto-programar
            $tieneHorario = false;
            if (empty($fecha) || empty($hora)) {
                $tieneHorario = $db->table('horario_usuario')
                    ->where('usuario_id', $usuarioId)
                    ->where('fecha >=', date('Y-m-d'))
                    ->where('estado', true)
                    ->where('categoria', 'PRODUCCION')
                    ->countAllResults() > 0;

                if (!$tieneHorario) {
                    throw new \Exception('Debe proporcionar la fecha y hora de inicio manualmente, ya que el auxiliar no tiene un horario de producción previo.');
                }
            } else {
                $tieneHorario = $db->table('horario_usuario')
                    ->where('usuario_id', $usuarioId)
                    ->where('fecha >=', date('Y-m-d'))
                    ->where('estado', true)
                    ->where('categoria', 'PRODUCCION')
                    ->countAllResults() > 0;
            }

            $horariosAInsertar = [];
            $actividadFechaInicio = $fecha ?: date('Y-m-d');
            $actividadHoraInicio = $hora ?: date('H:i:s');

            if ($tieneHorario && $minutos > 0 && (empty($fecha) || empty($hora))) {
                // Autoprogramar
                $lastSchedule = $db->table('horario_usuario')
                    ->where('usuario_id', $usuarioId)
                    ->where('fecha >=', date('Y-m-d'))
                    ->where('estado', true)
                    ->orderBy('fecha', 'DESC')
                    ->orderBy('hora_fin', 'DESC')
                    ->get()->getRowArray();
                
                $currDate = $lastSchedule['fecha'];
                $currTime = $lastSchedule['hora_fin'];
                $minsRemaining = $minutos;

                while ($minsRemaining > 0) {
                    $dayOfWeek = date('N', strtotime($currDate));
                    
                    if ($dayOfWeek == 7) { // Domingo
                        $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                        $currTime = '08:00:00';
                        continue;
                    }

                    if ($currTime < '08:00:00') {
                        $currTime = '08:00:00';
                    }

                    if ($dayOfWeek <= 5) { // Lunes a Viernes
                        if ($currTime >= '13:00:00' && $currTime < '15:00:00') {
                            $currTime = '15:00:00';
                        } elseif ($currTime >= '19:00:00') {
                            $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                            $currTime = '08:00:00';
                            continue;
                        }
                    } elseif ($dayOfWeek == 6) { // Sábado
                        if ($currTime >= '13:00:00') {
                            $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                            $currTime = '08:00:00';
                            continue;
                        }
                    }

                    $blockEnd = '13:00:00';
                    if ($dayOfWeek <= 5 && $currTime >= '15:00:00') {
                        $blockEnd = '19:00:00';
                    }

                    $availableMins = round((strtotime("$currDate $blockEnd") - strtotime("$currDate $currTime")) / 60);
                    
                    if ($availableMins <= 0) {
                        $currTime = $blockEnd;
                        continue;
                    }

                    $minsToUse = min($minsRemaining, $availableMins);
                    $endTime = date('H:i:s', strtotime("$currDate $currTime + $minsToUse minutes"));
                    
                    $horariosAInsertar[] = [
                        'usuario_id'   => $usuarioId,
                        'fecha'        => $currDate,
                        'hora_inicio'  => $currTime,
                        'hora_fin'     => $endTime,
                        'categoria'    => 'PRODUCCION',
                        'tipo'         => 'programado',
                        'estado'       => true,
                        'created_at'   => date('Y-m-d H:i:s')
                    ];
                    
                    if (empty($horariosAInsertar) || count($horariosAInsertar) == 1) {
                        $actividadFechaInicio = $currDate;
                        $actividadHoraInicio = $currTime;
                    }

                    $minsRemaining -= $minsToUse;
                    $currTime = $endTime;
                }
            } elseif (!empty($fecha) && !empty($hora) && $minutos > 0) {
                // Programación manual: fragmentation igual que autoprogramación
                $currDate = $fecha;
                $currTime = date('H:i:s', strtotime($hora));
                $minsRemaining = $minutos;
                $firstBlock = true;

                while ($minsRemaining > 0) {
                    $dayOfWeek = date('N', strtotime($currDate));

                    if ($dayOfWeek == 7) {
                        $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                        $currTime = '08:00:00';
                        continue;
                    }

                    if ($currTime < '08:00:00') $currTime = '08:00:00';

                    if ($dayOfWeek <= 5) {
                        if ($currTime >= '13:00:00' && $currTime < '15:00:00') {
                            $currTime = '15:00:00';
                        } elseif ($currTime >= '19:00:00') {
                            $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                            $currTime = '08:00:00';
                            continue;
                        }
                    } elseif ($dayOfWeek == 6) {
                        if ($currTime >= '13:00:00') {
                            $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                            $currTime = '08:00:00';
                            continue;
                        }
                    }

                    $blockEnd = '13:00:00';
                    if ($dayOfWeek <= 5 && $currTime >= '15:00:00') {
                        $blockEnd = '19:00:00';
                    }

                    $availableMins = round((strtotime("$currDate $blockEnd") - strtotime("$currDate $currTime")) / 60);

                    if ($availableMins <= 0) {
                        $currTime = $blockEnd;
                        continue;
                    }

                    $minsToUse = min($minsRemaining, $availableMins);
                    $endTime = date('H:i:s', strtotime("$currDate $currTime + $minsToUse minutes"));

                    $horariosAInsertar[] = [
                        'usuario_id'  => $usuarioId,
                        'fecha'       => $currDate,
                        'hora_inicio' => $currTime,
                        'hora_fin'    => $endTime,
                        'categoria'   => 'PRODUCCION',
                        'tipo'        => 'programado',
                        'estado'      => true,
                        'created_at'  => date('Y-m-d H:i:s')
                    ];

                    if ($firstBlock) {
                        $actividadFechaInicio = $currDate;
                        $actividadHoraInicio  = $currTime;
                        $firstBlock = false;
                    }

                    $minsRemaining -= $minsToUse;
                    $currTime = $endTime;
                }
            }

            $colorPrioridad = '#24BF17'; // NORMAL
            if ($prioridad === 'ALTA') $colorPrioridad = '#F54927';
            elseif ($prioridad === 'BAJA') $colorPrioridad = '#3B82F6';

            // Insertar Actividad
            $actividadId = $actividadModel->insert([
                'prospecto_id'            => $prospectoId,
                'usuario_id'              => $usuarioId,
                'tarea_id'                => $tareaId,
                'tiempo_estimado_minutos' => $minutos,
                'estado_progreso'         => 'PENDIENTE',
                'prioridad'               => $prioridad,
                'color'                   => $colorPrioridad,
                'estado'                  => true,
                'fecha_inicio'            => $actividadFechaInicio,
                'hora_inicio'             => $actividadHoraInicio,
                'created_at'              => date('Y-m-d H:i:s')
            ]);

            // Insertar Horarios programados
            foreach ($horariosAInsertar as $horario) {
                $horario['actividad_id'] = $actividadId;
                $db->table('horario_usuario')->insert($horario);
            }

            $db->transComplete();
            if ($db->transStatus() === false) throw new \Exception('Error al guardar la programación.');

            return $this->response->setJSON(['status' => 'success', 'message' => 'Actividad programada con éxito.']);
        } catch (\Throwable $th) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }
}
