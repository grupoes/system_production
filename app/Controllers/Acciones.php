<?php

namespace App\Controllers;

class Acciones extends BaseController
{
    public function index(): string
    {
        return view('acciones/index');
    }

    public function configuracionAccionesModulos(): string
    {
        return view('acciones/configuracion-acciones-modulos');
    }

    public function getAcciones()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('acciones');
        
        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('nombre_accion', $search, 'both', null, true)
                    ->orLike('descripcion', $search, 'both', null, true)
                    ->groupEnd();
        }

        $builder->where('estado', true);
        $totalRecords = $builder->countAllResults(false);

        $limit = $this->request->getGet('limit') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $limit;

        $builder->orderBy('id', 'DESC');
        $builder->limit($limit, $offset);
        $acciones = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $acciones,
            'total' => $totalRecords,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function getAccion($id)
    {
        $modelo = new \App\Models\AccionesModel();
        $accion = $modelo->find($id);

        if ($accion) {
            return $this->response->setJSON(['status' => 'success', 'data' => $accion]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Acción no encontrada.']);
    }

    public function save()
    {
        $modelo = new \App\Models\AccionesModel();
        $id = $this->request->getPost('id_accion');
        
        $data = [
            'nombre_accion' => $this->request->getPost('nombre_accion'),
            'descripcion'   => $this->request->getPost('descripcion') ?: ''
        ];

        if (!empty($id)) {
            if ($modelo->update($id, $data)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Acción actualizada correctamente.']);
            }
        } else {
            $data['estado'] = true;
            if ($modelo->insert($data)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Acción registrada correctamente.']);
            }
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al guardar la acción.']);
    }

    public function delete($id)
    {
        $modelo = new \App\Models\AccionesModel();
        if ($modelo->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Acción eliminada correctamente.']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar la acción.']);
    }

    /**
     * Retorna los módulos (sin padre = idpadre 0) con conteo de acciones configuradas.
     * Usado para el sidebar de configuración.
     */
    public function getModulosConfiguracion()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('modulos m');
        $builder->select('m.id, m.modulo, m.icono, m.url, COUNT(am.id) as total_acciones');
        $builder->join('acciones_modulos am', 'am.modulo_id = m.id', 'left');
        $builder->where('m.estado', true);
        $builder->where('m.idpadre', 0);
        $builder->groupBy('m.id, m.modulo, m.icono, m.url, m.orden');
        $builder->orderBy('m.orden', 'ASC');
        $modulos = $builder->get()->getResultArray();
        return $this->response->setJSON(['status' => 'success', 'data' => $modulos]);
    }

    public function getHijosDeModulo($padreId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('modulos m');
        $builder->select('m.id, m.modulo, m.icono, m.url, m.orden');
        $builder->where('m.estado', true);
        $builder->where('m.idpadre', $padreId);
        $builder->orderBy('m.orden', 'ASC');
        $hijos = $builder->get()->getResultArray();

        // Si no tiene hijos, devolvemos el módulo padre mismo
        if (empty($hijos)) {
            $padre = $db->table('modulos')->where('id', $padreId)->get()->getRowArray();
            if ($padre) {
                $hijos = [['id' => $padre['id'], 'modulo' => $padre['modulo'], 'icono' => $padre['icono'], 'url' => $padre['url'], 'orden' => $padre['orden']]];
            }
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $hijos]);
    }

    /**
     * Retorna todas las acciones activas indicando cuáles ya están asignadas al módulo.
     */
    public function getAccionesDeModulo($moduloId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('acciones a');
        $builder->select('a.id, a.nombre_accion, a.descripcion, CASE WHEN am.id IS NOT NULL THEN true ELSE false END as asignada', false);
        $builder->join('acciones_modulos am', "am.accion_id = a.id AND am.modulo_id = {$moduloId}", 'left');
        $builder->where('a.estado', true);
        $builder->orderBy('a.nombre_accion', 'ASC');
        $acciones = $builder->get()->getResultArray();
        return $this->response->setJSON(['status' => 'success', 'data' => $acciones]);
    }

    /**
     * Guarda (sobreescribe) la configuración de acciones de un módulo.
     * Recibe: modulo_id (int) y accion_ids[] (array de ids).
     */
    public function saveAccionesModulo()
    {
        $db = \Config\Database::connect();
        $moduloId = $this->request->getPost('modulo_id');
        $accionIds = $this->request->getPost('accion_ids') ?? [];

        if (empty($moduloId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Módulo no especificado.']);
        }

        // Borramos la configuración anterior del módulo
        $db->table('acciones_modulos')->where('modulo_id', $moduloId)->delete();

        // Insertamos las nuevas
        if (!empty($accionIds)) {
            $modelo = new \App\Models\AccionesModulosModel();
            $inserts = [];
            foreach ($accionIds as $accionId) {
                $inserts[] = ['modulo_id' => $moduloId, 'accion_id' => $accionId];
            }
            $modelo->insertBatch($inserts);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Configuración guardada correctamente.']);
    }
}
