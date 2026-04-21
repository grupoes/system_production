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
        $builder->select('t.*, tt.tipo as tipo_tarea_nombre');
        $builder->join('tipo_tarea tt', 'tt.id = t.tipo_tarea', 'left');
        $builder->where('t.estado', true);
        
        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $builder->like('t.nombre', $search, 'both', true);
            $builder->orLike('tt.tipo', $search, 'both', true);
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
        $tipo_tarea = $this->request->getPost('tipo_tarea');
        
        if (empty($nombre)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'El nombre de la tarea es obligatorio.']);
        }
        
        if (empty($tipo_tarea)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'El tipo/categoría de la tarea es obligatorio.']);
        }

        $hrs = (int)$this->request->getPost('estimado_hrs');
        $min = (int)$this->request->getPost('estimado_min');
        $total_minutos = ($hrs * 60) + $min;

        $dataTarea = [
            'nombre' => $nombre,
            'horas_estimadas' => $total_minutos > 0 ? $total_minutos : null,
            'tipo_tarea' => $tipo_tarea,
            'estado' => true
        ];

        $db->transStart();

        if ($id) {
            if (!$model->update($id, $dataTarea)) {
                $db->transRollback();
                $errs = $model->errors() ?: [$db->error()['message']];
                $msg = 'Error al actualizar: ' . implode(', ', $errs);
                return $this->response->setJSON(['status' => 'error', 'message' => $msg ?: 'Error desconocido al actualizar.']);
            }
            // Desactivar roles anteriores para reemplazarlos
            $trModel->where('tarea_id', $id)->update(null, ['estado' => false]);
            $tareaId = $id;
        } else {
            $tareaId = $model->insert($dataTarea);
            if (!$tareaId) {
                $db->transRollback();
                $errs = $model->errors() ?: [$db->error()['message']];
                $msg = 'Error al crear: ' . implode(', ', array_filter($errs));
                return $this->response->setJSON(['status' => 'error', 'message' => $msg ?: 'Error al crear: revisa los campos enviados.']);
            }
        }

        $roles_ids = $this->request->getPost('roles'); // Array de IDs de roles
        $prioridades = $this->request->getPost('prioridades'); // Array asociativo rol_id => prioridad

        if (!empty($roles_ids)) {
            foreach ($roles_ids as $rolId) {
                $prioridad = isset($prioridades[$rolId]) ? (int)$prioridades[$rolId] : 0;
                
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
            $dbError = $db->error();
            $trErrors = $trModel->errors();
            $errorMsg = !empty($dbError['message']) ? $dbError['message'] : implode(', ', $trErrors);
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error de base de datos al guardar la tarea: ' . $errorMsg]);
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
