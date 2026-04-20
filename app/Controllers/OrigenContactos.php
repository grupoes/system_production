<?php

namespace App\Controllers;

use App\Models\OrigenModel;

class OrigenContactos extends BaseController
{
    public function index(): string
    {
        return view('origen_contactos/index');
    }

    public function getList()
    {
        $model = new OrigenModel();
        $search = $this->request->getGet('search');
        $limit = $this->request->getGet('limit') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $limit;

        $builder = $model->where('estado', true);

        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('nombre', $search, 'both', true);
            $builder->orLike('descripcion', $search, 'both', true);
            $builder->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('nombre', 'ASC')->findAll($limit, $offset);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data,
            'total' => $total,
            'limit' => (int)$limit,
            'page' => (int)$page
        ]);
    }

    public function get($id)
    {
        $model = new OrigenModel();
        $data = $model->find($id);
        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Origen no encontrado']);
    }

    public function save()
    {
        $model = new OrigenModel();
        $id = $this->request->getPost('id_origen');
        
        $data = [
            'nombre' => strtoupper($this->request->getPost('nombre')),
            'descripcion' => $this->request->getPost('descripcion'),
            'estado' => true
        ];

        if ($id) {
            $model->update($id, $data);
            $msg = 'Origen de contacto actualizado correctamente';
        } else {
            $model->insert($data);
            $msg = 'Origen de contacto registrado correctamente';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function delete($id)
    {
        $model = new OrigenModel();
        if ($model->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Origen eliminado correctamente']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar']);
    }
}
