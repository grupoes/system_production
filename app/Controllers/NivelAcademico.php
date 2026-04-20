<?php

namespace App\Controllers;

use App\Models\NivelAcademicoModel;

class NivelAcademico extends BaseController
{
    public function index(): string
    {
        return view('nivel_academico/index');
    }

    public function getList()
    {
        $model = new NivelAcademicoModel();
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
        $model = new NivelAcademicoModel();
        $data = $model->find($id);
        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Nivel académico no encontrado']);
    }

    public function save()
    {
        $model = new NivelAcademicoModel();
        $id = $this->request->getPost('id_nivel');
        
        $data = [
            'nombre' => strtoupper($this->request->getPost('nombre')),
            'descripcion' => $this->request->getPost('descripcion'),
            'estado' => true
        ];

        if ($id) {
            $model->update($id, $data);
            $msg = 'Nivel académico actualizado correctamente';
        } else {
            $model->insert($data);
            $msg = 'Nivel académico registrado correctamente';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function delete($id)
    {
        $model = new NivelAcademicoModel();
        if ($model->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Nivel académico eliminado correctamente']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar']);
    }
}
