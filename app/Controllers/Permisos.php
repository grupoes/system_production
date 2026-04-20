<?php

namespace App\Controllers;

class Permisos extends BaseController
{
    public function index(): string
    {
        return view('permisos/index');
    }

    public function getRoles()
    {
        $model = new \App\Models\RolModel();
        $roles = $model->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $roles]);
    }

    public function getRol($id)
    {
        $model = new \App\Models\RolModel();
        $rol = $model->find($id);
        if ($rol) {
            return $this->response->setJSON(['status' => 'success', 'data' => $rol]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Rol no encontrado']);
    }

    public function saveRol()
    {
        $model = new \App\Models\RolModel();
        $id = $this->request->getPost('id_rol');
        $data = [
            'nombre' => strtoupper($this->request->getPost('nombre')),
            'estado' => true
        ];

        if ($id) {
            $model->update($id, $data);
            $msg = 'Rol actualizado correctamente';
        } else {
            $model->insert($data);
            $msg = 'Rol creado correctamente';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function deleteRol($id)
    {
        $model = new \App\Models\RolModel();
        if ($model->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Rol eliminado correctamente']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar el rol']);
    }

    public function getMatrixPermisos($rolId)
    {
        $db = \Config\Database::connect();
        
        // 1. Obtener todos los módulos padres
        $modulosPadres = $db->table('modulos')
            ->where('idpadre', 0)
            ->where('estado', true)
            ->orderBy('orden', 'ASC')
            ->get()->getResultArray();

        $matrix = [];

        foreach ($modulosPadres as $padre) {
            // 2. Obtener submódulos (o el mismo si no tiene hijos, pero generalmente buscamos hijos)
            $submodulos = $db->table('modulos')
                ->where('idpadre', $padre['id'])
                ->where('estado', true)
                ->orderBy('orden', 'ASC')
                ->get()->getResultArray();

            $submodulosData = [];

            foreach ($submodulos as $sub) {
                // 3. Obtener acciones disponibles para este submódulo (desde acciones_modulos)
                $accionesDisponibles = $db->table('acciones_modulos am')
                    ->select('a.id as accion_id, a.nombre_accion, a.descripcion')
                    ->join('acciones a', 'a.id = am.accion_id')
                    ->where('am.modulo_id', $sub['id'])
                    ->where('a.estado', true)
                    ->get()->getResultArray();

                // 4. Verificar cuáles de estas acciones tiene el rol (desde permisos)
                foreach ($accionesDisponibles as &$acc) {
                    $permiso = $db->table('permisos')
                        ->where('rol_id', $rolId)
                        ->where('modulo_id', $sub['id'])
                        ->where('accion_id', $acc['accion_id'])
                        ->get()->getRow();
                    
                    $acc['tiene_permiso'] = $permiso ? true : false;
                }

                $sub['acciones'] = $accionesDisponibles;
                $submodulosData[] = $sub;
            }

            $padre['submodulos'] = $submodulosData;
            $matrix[] = $padre;
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $matrix]);
    }

    public function togglePermiso()
    {
        $model = new \App\Models\PermisosModel();
        $rolId = $this->request->getPost('rol_id');
        $moduloId = $this->request->getPost('modulo_id');
        $accionId = $this->request->getPost('accion_id');
        $estado = $this->request->getPost('estado'); // true/false o 1/0

        $where = [
            'rol_id' => $rolId,
            'modulo_id' => $moduloId,
            'accion_id' => $accionId
        ];

        $existe = $model->where($where)->first();

        if ($estado == 'true' || $estado == '1') {
            if (!$existe) {
                $model->insert($where);
            }
        } else {
            if ($existe) {
                $model->where($where)->delete();
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Permiso actualizado']);
    }
}
