<?php

namespace App\Controllers;

use App\Models\TareaModel;
use App\Models\TareaRolesModel;
use App\Models\RolModel;

class Tareas extends BaseController
{
    public function index(): string
    {
        return view('tareas/index');
    }

    public function getList()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tarea t');
        $builder->select('t.*');
        $builder->where('t.estado', true);
        
        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $builder->like('t.nombre', $search, 'both', true);
        }

        $limit = $this->request->getGet('limit') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $limit;

        $total = $builder->countAllResults(false);
        $tareas = $builder->orderBy('t.id', 'DESC')->get($limit, $offset)->getResultArray();

        // Obtener roles para cada tarea
        foreach ($tareas as &$tarea) {
            $tarea['roles'] = $db->table('tareas_roles tr')
                ->select('tr.*, r.nombre as rol_nombre')
                ->join('roles r', 'r.id = tr.rol_id')
                ->where('tr.tarea_id', $tarea['id'])
                ->where('tr.estado', true)
                ->get()->getResultArray();
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $tareas,
            'total' => $total,
            'limit' => (int)$limit,
            'page' => (int)$page
        ]);
    }

    public function get($id)
    {
        $model = new TareaModel();
        $tarea = $model->find($id);
        
        if ($tarea) {
            $db = \Config\Database::connect();
            $tarea['roles'] = $db->table('tareas_roles tr')
                ->where('tarea_id', $id)
                ->where('estado', true)
                ->get()->getResultArray();
                
            return $this->response->setJSON(['status' => 'success', 'data' => $tarea]);
        }
        
        return $this->response->setJSON(['status' => 'error', 'message' => 'Tarea no encontrada']);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        $model = new TareaModel();
        $trModel = new TareaRolesModel();

        $id = $this->request->getPost('id_tarea');
        $nombre = strtoupper($this->request->getPost('nombre'));
        
        $hrs = (int)$this->request->getPost('estimado_hrs');
        $min = (int)$this->request->getPost('estimado_min');
        $total_minutos = ($hrs * 60) + $min;

        $tipo_tarea = $this->request->getPost('tipo_tarea');
        
        $dataTarea = [
            'nombre' => $nombre,
            'horas_estimadas' => $total_minutos > 0 ? $total_minutos : null,
            'tipo_tarea' => $tipo_tarea,
            'estado' => true
        ];

        $db->transStart();

        if ($id) {
            $model->update($id, $dataTarea);
            // Desactivar roles anteriores para reemplazarlos
            $trModel->where('tarea_id', $id)->update(null, ['estado' => false]);
            $tareaId = $id;
        } else {
            $tareaId = $model->insert($dataTarea);
        }

        $roles_ids = $this->request->getPost('roles'); // Array de IDs de roles
        $prioridades = $this->request->getPost('prioridades'); // Array asociativo rol_id => prioridad

        if (!empty($roles_ids)) {
            foreach ($roles_ids as $rolId) {
                $prioridad = isset($prioridades[$rolId]) ? $prioridades[$rolId] : 'COMPLEMENTARIA';
                
                // Verificar si ya existe (para reactivar si es edición)
                $existente = $trModel->where('tarea_id', $tareaId)->where('rol_id', $rolId)->first();
                
                if ($existente) {
                    $trModel->update($existente['id'], [
                        'prioridad' => $prioridad,
                        'estado' => true
                    ]);
                } else {
                    $trModel->insert([
                        'tarea_id' => $tareaId,
                        'rol_id' => $rolId,
                        'prioridad' => $prioridad,
                        'estado' => true
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al guardar la tarea']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Tarea guardada correctamente']);
    }

    public function delete($id)
    {
        $model = new TareaModel();
        if ($model->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Tarea eliminada correctamente']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar']);
    }

    public function getRoles()
    {
        $model = new RolModel();
        $roles = $model->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $roles]);
    }

    public function getTipos()
    {
        $model = new \App\Models\TipoTareaModel();
        $tipos = $model->where('estado', true)->orderBy('tipo', 'ASC')->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $tipos]);
    }
}
