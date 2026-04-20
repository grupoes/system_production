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
        // Si es domingo (7), no hay asignación por defecto usualmente en este sistema
        $defaultUserId = null;
        if ($dayOfWeek <= 6) {
            $asignacion = $db->table('asignacion_dias')
                ->where('dia_id', $dayOfWeek)
                ->get()->getRowArray();
            
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
            p.titulo_prospecto,
            t.nombre as tarea,
            STRING_AGG(pers.nombres || ' ' || pers.apellidos, ', ') as prospecto_cliente
        ");
        $builder->join('prospectos p', 'p.id = a.prospecto_id');
        $builder->join('tarea t', 't.id = a.tarea_id');
        $builder->join('prospecto_persona pp', 'pp.prospecto_id = p.id', 'left');
        $builder->join('personas pers', 'pers.id = pp.persona_id', 'left');
        $builder->where('a.estado_progreso', 'PENDIENTE');
        $builder->groupBy('a.id, p.titulo_prospecto, t.nombre');
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
        $builder = $db->table('usuarios u');
        $builder->select("u.id, (p.nombres || ' ' || p.apellidos) as nombre");
        $builder->join('personas p', 'p.id = u.persona_id');
        $builder->where('u.estado', true);
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
            hu.categoria
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
            $events[] = [
                'id'    => $item['id'],
                'title' => $item['tarea'] ?? 'Actividad',
                'start' => $item['fecha'] . 'T' . $item['hora_inicio'],
                'end'   => $item['fecha'] . 'T' . $item['hora_fin'],
                'color' => $item['categoria'] === 'PROSPECTO' ? '#6366f1' : '#10b981'
            ];
        }

        return $this->response->setJSON($events);
    }
}
