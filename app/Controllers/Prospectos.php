<?php

namespace App\Controllers;

use App\Models\ProspectosModel;
use App\Models\PersonaModel;
use App\Models\ProspectoPersonaModel;
use App\Models\InstitucionModel;
use App\Models\CarreraModel;
use App\Models\NivelAcademicoModel;
use App\Models\OrigenModel;
use App\Models\TareaModel;
use App\Models\NotificacionModel;

class Prospectos extends BaseController
{
    public function index(): string
    {
        return view('prospectos/index');
    }

    public function getList()
    {
        $page = $this->request->getVar('page') ?? 1;
        $limit = $this->request->getVar('limit') ?? 10;
        $search = $this->request->getVar('search');

        $db = \Config\Database::connect();
        $builder = $db->table('prospectos p');
        $builder->select("
            p.id, 
            p.created_at, 
            p.estado_cliente,
            i.nombre as universidad, 
            c.nombre as carrera,
            t.nombre as tarea,
            (v_pers.nombres || ' ' || v_pers.apellidos) as vendedor,
            STRING_AGG(c_pers.nombres || ' ' || c_pers.apellidos || ' (' || c_pers.celular || ')', '<br>') as contactos
        ");
        $builder->join('carreras c', 'c.id = p.carrera_id', 'left');
        $builder->join('institucion i', 'i.id = c.institucion_id', 'left');
        $builder->join('usuarios v_user', 'v_user.id = p.usuario_venta_id', 'left');
        $builder->join('personas v_pers', 'v_pers.id = v_user.persona_id', 'left');
        $builder->join('prospecto_persona pp', 'pp.prospecto_id = p.id', 'left');
        $builder->join('personas c_pers', 'c_pers.id = pp.persona_id', 'left');
        $builder->join('actividades a', 'a.prospecto_id = p.id', 'left');
        $builder->join('tarea t', 't.id = a.tarea_id', 'left');

        if (!empty($search)) {
            $s = $db->escapeLikeString($search);
            $builder->groupStart()
                ->where("i.nombre ILIKE '%$s%'")
                ->orWhere("c.nombre ILIKE '%$s%'")
                ->orWhere("t.nombre ILIKE '%$s%'")
                ->orWhere("(v_pers.nombres || ' ' || v_pers.apellidos) ILIKE '%$s%'")
                ->orWhere("(c_pers.nombres || ' ' || c_pers.apellidos) ILIKE '%$s%'")
                ->orWhere("c_pers.celular ILIKE '%$s%'")
                ->orWhere("CAST(p.created_at AS TEXT) ILIKE '%$s%'")
                ->groupEnd();
        }

        $builder->groupBy('p.id, i.nombre, c.nombre, t.nombre, v_pers.nombres, v_pers.apellidos');
        $builder->orderBy('p.id', 'DESC');

        // Paginación manual para el builder
        $total = $builder->countAllResults(false);
        $offset = ($page - 1) * $limit;
        $data = $builder->limit($limit, $offset)->get()->getResultArray();

        return $this->response->setJSON([
            'status'   => 'success',
            'data'     => $data,
            'total'    => $total,
            'page'     => $page,
            'limit'    => $limit,
            'lastPage' => ceil($total / $limit)
        ]);
    }

    public function registro(): string
    {
        $id = $this->request->getVar('id');
        $data = ['title' => 'Registrar Potencial Cliente'];

        if ($id) {
            $db = \Config\Database::connect();

            // Datos del prospecto + universidad_id (desde carrera)
            $prospecto = $db->table('prospectos p')
                ->select('p.*, c.institucion_id as universidad_id')
                ->join('carreras c', 'c.id = p.carrera_id', 'left')
                ->where('p.id', $id)
                ->get()->getRowArray();

            if ($prospecto) {
                $data['prospecto'] = $prospecto;
                $data['title'] = 'Editar Potencial Cliente';

                // Tarea vinculada
                $actividad = $db->table('actividades')
                    ->where('prospecto_id', $id)
                    ->get()->getRowArray();
                $data['actividad'] = $actividad;

                // Contactos (personas)
                $contactos = $db->table('prospecto_persona pp')
                    ->select('pers.*')
                    ->join('personas pers', 'pers.id = pp.persona_id')
                    ->where('pp.prospecto_id', $id)
                    ->get()->getResultArray();
                $data['contactos'] = $contactos;
            }
        }

        return view('prospectos/registro', $data);
    }

    public function getDataForm()
    {
        $universidades = (new InstitucionModel())->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        $niveles = (new NivelAcademicoModel())->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        $origenes = (new OrigenModel())->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        $tareas = (new TareaModel())->where('estado', true)->orderBy('nombre', 'ASC')->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'universidades' => $universidades,
            'niveles' => $niveles,
            'origenes' => $origenes,
            'tareas' => $tareas
        ]);
    }

    public function getCarreras($universidadId)
    {
        $model = new CarreraModel();
        $data = $model->where('institucion_id', $universidadId)->where('estado', true)->orderBy('nombre', 'ASC')->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        $prospectoModel = new ProspectosModel();
        $personaModel = new PersonaModel();
        $ppModel = new ProspectoPersonaModel();
        $actividadModel = new \App\Models\ActividadesModel();
        $notificacionModel = new NotificacionModel();
        $historialModel = new \App\Models\HistorialEstadoProspectoModel();

        $id = $this->request->getPost('id');
        $tareaId = $this->request->getPost('tarea_id');

        if (empty($tareaId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe seleccionar una tarea válida.']);
        }

        $tarea = (new TareaModel())->find($tareaId);
        if (!$tarea) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'La tarea seleccionada no existe.']);
        }

        $tiempoEstimado = $tarea['horas_estimadas'] ?? 0;

        $db->transException(true)->transStart();
        try {
            // 1. Datos del Prospecto
            $fechaEntrega = $this->request->getPost('fecha_entrega');
            $dataProspecto = [
                'titulo_prospecto'   => strtoupper($this->request->getPost('titulo_trabajo')),
                'origen_id'          => $this->request->getPost('origen_id'),
                'nivel_academico_id' => $this->request->getPost('nivel_id') ?: null,
                'carrera_id'         => $this->request->getPost('carrera_id') ?: null,
                'fecha_entrega'      => !empty($fechaEntrega) ? $fechaEntrega : null,
                'link_drive'         => $this->request->getPost('link_drive') ?: null,
                'contenido'          => $this->request->getPost('observaciones'),
                'prioridad'          => $this->request->getPost('prioridad') ?? 'NORMAL',
            ];

            if (!empty($id)) {
                $prospectoModel->update($id, $dataProspecto);
                $prospectoId = $id;

                // Si se forzó el estado a CLIENTE
                $nuevoEstado = $this->request->getPost('nuevo_estado_cliente');
                if ($nuevoEstado === 'cliente') {
                    // Cerrar estado anterior (PROSPECTO -> CLIENTE)
                    $historialModel->where('prospecto_id', $id)
                        ->where('fecha_fin', null)
                        ->set(['fecha_fin' => date('Y-m-d H:i:s')])
                        ->update();

                    $prospectoModel->update($id, ['estado_cliente' => 'cliente']);
                    
                    // Historial de Estado (CLIENTE)
                    $historialModel->insert([
                        'prospecto_id' => $id,
                        'estado'       => 'CLIENTE',
                        'fecha_inicio' => date('Y-m-d H:i:s'),
                        'usuario_id'   => session()->get('id'),
                        'comentario'   => 'EL USUARIO ' . session()->get('nombre') . ' CONVIRTIÓ EL PROSPECTO EN CLIENTE'
                    ]);
                }

                $actividadModel->where('prospecto_id', $id)->set([
                    'tarea_id'                => $tareaId,
                    'prioridad'               => $dataProspecto['prioridad'],
                    'tiempo_estimado_minutos' => $tiempoEstimado
                ])->update();

                $ppModel->where('prospecto_id', $id)->delete();
            } else {
                $dataProspecto['fecha_contacto'] = date('Y-m-d');
                $dataProspecto['estado']           = true;
                $dataProspecto['usuario_venta_id'] = session()->get('id');
                $dataProspecto['estado_cliente']   = 'pendiente';

                $prospectoId = $prospectoModel->insert($dataProspecto);
                if (!$prospectoId) throw new \Exception('Error prospecto: ' . json_encode($prospectoModel->errors()));

                // Historial de Estado
                $historialModel->insert([
                    'prospecto_id' => $prospectoId,
                    'estado'       => 'PROSPECTO',
                    'fecha_inicio' => date('Y-m-d H:i:s'),
                    'usuario_id'   => session()->get('id'),
                    'comentario'   => 'EL USUARIO ' . session()->get('nombre') . ' REGISTRÓ AL POTENCIAL CLIENTE'
                ]);

                $actividadModel->insert([
                    'prospecto_id'            => $prospectoId,
                    'usuario_id'              => session()->get('id'),
                    'tarea_id'                => $tareaId,
                    'estado'                  => true,
                    'prioridad'               => $dataProspecto['prioridad'],
                    'estado_progreso'         => 'PENDIENTE',
                    'fecha_inicio'            => date('Y-m-d'),
                    'tiempo_estimado_minutos' => $tiempoEstimado
                ]);

                // Notificaciones
                $usuariosCC = $db->table('usuarios')->where(['rol_id' => 5, 'estado' => true])->get()->getResultArray();
                foreach ($usuariosCC as $u) {
                    $notificacionModel->insert([
                        'usuario_id'   => $u['id'],
                        'remitente_id' => session()->get('id'),
                        'titulo'       => 'NUEVO PROSPECTO',
                        'mensaje'      => $tarea['nombre'] ?? 'Sin tarea',
                        'tipo'         => 'INFO',
                        'prioridad'    => 1,
                        'es_leida'     => false
                    ]);
                }
            }

            // 3. Contactos
            $nombresArr = $this->request->getPost('nombres');
            $apellidosArr = $this->request->getPost('apellidos');
            $celularesArr = $this->request->getPost('celulares');

            if (is_array($nombresArr)) {
                foreach ($nombresArr as $i => $nombre) {
                    $celular = $celularesArr[$i] ?? '';
                    $apellido = $apellidosArr[$i] ?? '';

                    if (!empty($nombre) || !empty($celular)) {
                        $personaId = $personaModel->insert([
                            'nombres'   => strtoupper($nombre),
                            'apellidos' => strtoupper($apellido),
                            'celular'   => $celular,
                            'estado'    => true
                        ]);

                        if ($personaId) {
                            $ppModel->insert([
                                'persona_id'   => $personaId,
                                'prospecto_id' => $prospectoId
                            ]);
                        }
                    }
                }
            }

            $db->transComplete();
            return $this->response->setJSON(['status' => 'success', 'message' => 'Registro completado con éxito.']);
        } catch (\Throwable $th) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error DB: ' . $th->getMessage()
            ]);
        }
    }

    public function saveCarrera()
    {
        $nombre = $this->request->getPost('nombre');
        $universidadId = $this->request->getPost('universidad_id');

        if (empty($nombre) || empty($universidadId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Faltan datos obligatorios.']);
        }

        $model = new \App\Models\CarreraModel();
        $id = $model->insert([
            'nombre'         => strtoupper($nombre),
            'institucion_id' => $universidadId,
            'estado'         => true
        ]);

        if ($id) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Carrera registrada correctamente.',
                'id'      => $id,
                'nombre'  => strtoupper($nombre)
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo registrar la carrera.']);
    }
}
