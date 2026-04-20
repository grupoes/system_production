<?php

namespace App\Controllers;

use App\Models\CarreraModel;
use App\Models\InstitucionModel;

class Carreras extends BaseController
{
    public function index(): string
    {
        return view('carreras/index');
    }

    public function getCarreras()
    {
        $model = new CarreraModel();
        $search = $this->request->getGet('search');
        $limit = $this->request->getGet('limit') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $limit;

        $db = \Config\Database::connect();
        $builder = $db->table('carreras c');
        $builder->select('c.*, i.nombre as institucion_nombre');
        $builder->join('institucion i', 'i.id = c.institucion_id', 'left');
        $builder->where('c.estado', true);

        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('c.nombre', $search, 'both', true);
            $builder->orLike('i.nombre', $search, 'both', true);
            $builder->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('c.nombre', 'ASC')->get($limit, $offset)->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data,
            'total' => $total,
            'limit' => (int)$limit,
            'page' => (int)$page
        ]);
    }

    public function getCarrera($id)
    {
        $model = new CarreraModel();
        $data = $model->find($id);
        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Carrera no encontrada']);
    }

    public function save()
    {
        $model = new CarreraModel();
        $id = $this->request->getPost('id_carrera');
        
        $data = [
            'nombre' => strtoupper($this->request->getPost('nombre')),
            'institucion_id' => $this->request->getPost('institucion_id'),
            'estado' => true
        ];

        if ($id) {
            $model->update($id, $data);
            $msg = 'Carrera actualizada correctamente';
        } else {
            $model->insert($data);
            $msg = 'Carrera registrada correctamente';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function delete($id)
    {
        $model = new CarreraModel();
        if ($model->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Carrera eliminada correctamente']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar']);
    }
}
