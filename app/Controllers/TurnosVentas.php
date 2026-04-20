<?php

namespace App\Controllers;

use App\Models\AsignacionDiariaModel;
use App\Models\DiaModel;
use App\Models\UsuarioModel;

class TurnosVentas extends BaseController
{
    public function index(): string
    {
        return view('turnos_ventas/index');
    }

    public function getDias()
    {
        $model = new DiaModel();
        $data = $model->where('estado', true)->orderBy('id', 'ASC')->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    public function getAsignaciones($diaId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('asignacion_dias ad');
        $builder->select('ad.id, ad.usuario_id, u.usuario, p.nombres, p.apellidos, r.nombre as rol');
        $builder->join('usuarios u', 'u.id = ad.usuario_id');
        $builder->join('personas p', 'p.id = u.persona_id');
        $builder->join('roles r', 'r.id = u.rol_id');
        $builder->where('ad.dia_id', $diaId);
        
        $data = $builder->get()->getResultArray();
        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    public function saveAsignacion()
    {
        $model = new AsignacionDiariaModel();
        $diaId = $this->request->getPost('dia_id');
        $usuarioId = $this->request->getPost('usuario_id');

        // Verificar si ya existe
        $existe = $model->where('dia_id', $diaId)->where('usuario_id', $usuarioId)->first();
        if ($existe) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Este usuario ya está asignado a este día']);
        }

        $model->insert([
            'dia_id' => $diaId,
            'usuario_id' => $usuarioId
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario asignado correctamente']);
    }

    public function removeAsignacion($id)
    {
        $model = new AsignacionDiariaModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Asignación removida']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al remover asignación']);
    }

    public function getUsuarios()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('usuarios u');
        $builder->select('u.id, u.usuario, p.nombres, p.apellidos, r.nombre as rol');
        $builder->join('personas p', 'p.id = u.persona_id');
        $builder->join('roles r', 'r.id = u.rol_id');
        $builder->where('u.estado', true);
        
        $data = $builder->get()->getResultArray();
        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }
}
