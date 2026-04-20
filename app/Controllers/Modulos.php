<?php

namespace App\Controllers;

class Modulos extends BaseController
{
    public function index(): string
    {
        return view('modulos/index');
    }

    public function getPadres()
    {
        $modelo = new \App\Models\ModulosModel();
        $padres = $modelo->where('idpadre', 0)
                         ->where('estado', true)
                         ->orderBy('orden', 'ASC')
                         ->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $padres]);
    }

    public function getModulos()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('modulos m');
        $builder->select('m.*, p.modulo as nombre_padre');
        $builder->join('modulos p', 'm.idpadre = p.id', 'left');
        
        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('m.modulo', $search, 'both', null, true)
                    ->orLike('m.url', $search, 'both', null, true)
                    ->orLike('p.modulo', $search, 'both', null, true)
                    ->groupEnd();
        }

        $builder->where('m.estado', true);
        
        $totalRecords = $builder->countAllResults(false);

        $limit = $this->request->getGet('limit') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $limit;

        // Ordenar primero por el padre (para agrupar) y luego por su propio orden
        // Usamos false para evitar que CodeIgniter intente parsear la coma del COALESCE
        $builder->orderBy('COALESCE(p.id, m.id)', 'ASC', false);
        $builder->orderBy('m.idpadre', 'ASC');
        $builder->orderBy('m.orden', 'ASC');
        
        $builder->limit($limit, $offset);
        $modulos = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $modulos,
            'total' => $totalRecords,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function getModulo($id)
    {
        $modelo = new \App\Models\ModulosModel();
        $modulo = $modelo->find($id);

        if ($modulo) {
            return $this->response->setJSON(['status' => 'success', 'data' => $modulo]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Módulo no encontrado.']);
    }

    public function save()
    {
        $modelo = new \App\Models\ModulosModel();

        $id = $this->request->getPost('id_modulo');
        
        $data = [
            'modulo'  => $this->request->getPost('modulo'),
            'icono'   => $this->request->getPost('icono') ?: '',
            'idpadre' => $this->request->getPost('idpadre') ?: 0,
            'url'     => $this->request->getPost('url') ?: '#',
            'orden'   => $this->request->getPost('orden') ?: 1
        ];

        if (!empty($id)) {
            if ($modelo->update($id, $data)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Módulo actualizado correctamente.']);
            }
        } else {
            $data['estado'] = true;
            if ($modelo->insert($data)) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Módulo registrado correctamente.']);
            }
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error al guardar el módulo.']);
    }

    public function delete($id)
    {
        $modelo = new \App\Models\ModulosModel();
        $modulo = $modelo->find($id);

        if (!$modulo) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Módulo no encontrado.']);
        }

        $force = $this->request->getGet('force');

        // Check if it's a parent and has active children
        if ($modulo['idpadre'] == 0) {
            $children = $modelo->where('idpadre', $id)->where('estado', true)->findAll();
            
            if (count($children) > 0 && $force !== 'true') {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'has_children' => true, 
                    'message' => 'Este módulo tiene ' . count($children) . ' submódulo(s) activo(s).'
                ]);
            }

            if ($force === 'true') {
                // Soft delete children
                $db = \Config\Database::connect();
                $db->table('modulos')->where('idpadre', $id)->update(['estado' => false]);
            }
        }

        // Soft delete the module itself
        if ($modelo->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Módulo eliminado correctamente.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar el módulo.']);
    }
}
