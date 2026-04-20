<?php

namespace App\Controllers;

use App\Models\FeriadoModel;

class Feriados extends BaseController
{
    public function index(): string
    {
        return view('feriados/index');
    }

    public function getList()
    {
        $model = new FeriadoModel();
        $search = $this->request->getGet('search');
        $anio = $this->request->getGet('anio');
        $limit = $this->request->getGet('limit') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $limit;

        $builder = $model->where('estado', true);

        if (!empty($anio)) {
            $builder->where("TO_CHAR(fecha, 'YYYY') =", $anio);
        }

        if (!empty($search)) {
            $builder->like('nombre', $search, 'both', true);
        }

        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('fecha', 'DESC')->findAll($limit, $offset);

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
        $model = new FeriadoModel();
        $data = $model->find($id);
        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Feriado no encontrado']);
    }

    public function save()
    {
        $model = new FeriadoModel();
        $id = $this->request->getPost('id_feriado');
        
        $data = [
            'nombre'       => strtoupper($this->request->getPost('nombre')),
            'fecha'        => $this->request->getPost('fecha'),
            'tipo'         => $this->request->getPost('tipo'),
            'es_laborable' => $this->request->getPost('es_laborable') ? true : false,
            'estado'       => true
        ];

        if ($id) {
            if ($model->update($id, $data)) {
                $msg = 'Feriado actualizado correctamente';
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al actualizar', 'errors' => $model->errors()]);
            }
        } else {
            if ($model->insert($data)) {
                $msg = 'Feriado registrado correctamente';
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al registrar', 'errors' => $model->errors()]);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function delete($id)
    {
        $model = new FeriadoModel();
        if ($model->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Feriado eliminado correctamente']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar']);
    }
}
