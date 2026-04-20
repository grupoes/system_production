<?php

namespace App\Controllers;

class Usuarios extends BaseController
{
    public function index(): string
    {
        $tipoJornadaModel = new \App\Models\TipoJornadaModel();
        $rolModel = new \App\Models\RolModel();

        $data = [
            'tiposJornada' => $tipoJornadaModel->where('estado', true)->findAll(),
            'roles'        => $rolModel->where('estado', true)->findAll(),
            'title'        => 'Gestión de Usuarios'
        ];

        return view('usuarios/index', $data);
    }

    public function getUsuarios()
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        
        $page = $this->request->getVar('page') ?? 1;
        $limit = $this->request->getVar('limit') ?? 10;
        $search = $this->request->getVar('search');

        $builder = $usuarioModel->select('usuarios.id, personas.numero_documento, personas.nombres, personas.apellidos, usuarios.usuario as correo, roles.nombre as rol_nombre')
            ->join('personas', 'personas.id = usuarios.persona_id')
            ->join('roles', 'roles.id = usuarios.rol_id', 'left')
            ->where('usuarios.estado', true);

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('personas.nombres', $search, 'both', null, true)
                    ->orLike('personas.apellidos', $search, 'both', null, true)
                    ->orLike('personas.numero_documento', $search, 'both', null, true)
                    ->orLike('usuarios.usuario', $search, 'both', null, true)
                    ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $usuarios = $builder->orderBy('usuarios.id', 'DESC')->paginate($limit, 'default', $page);

        return $this->response->setJSON([
            'status'   => 'success',
            'data'     => $usuarios,
            'total'    => $total,
            'page'     => $page,
            'limit'    => $limit,
            'lastPage' => ceil($total / $limit)
        ]);
    }

    public function getUsuario($id)
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        $personaModel = new \App\Models\PersonaModel();
        $horarioDetalleModel = new \App\Models\HorarioJornadaDetalleModel();

        $usuario = $usuarioModel->find($id);
        if (!$usuario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado']);
        }

        $persona = $personaModel->find($usuario['persona_id']);
        
        $horarios = $horarioDetalleModel->where('usuario_id', $id)->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'usuario' => $usuario,
            'persona' => $persona,
            'horarios' => $horarios
        ]);
    }

    public function delete($id)
    {
        $usuarioModel = new \App\Models\UsuarioModel();
        
        $usuario = $usuarioModel->find($id);
        if (!$usuario) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }

        if ($usuarioModel->update($id, ['estado' => false])) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario eliminado correctamente.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo eliminar el usuario.']);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $personaModel = new \App\Models\PersonaModel();
        $usuarioModel = new \App\Models\UsuarioModel();
        $tipoJornadaModel = new \App\Models\TipoJornadaModel();
        $horarioDetalleModel = new \App\Models\HorarioJornadaDetalleModel();

        $docMap = ['DNI' => 1, 'CE' => 2, 'PASSPORT' => 3];
        $tipoDocId = $docMap[$this->request->getPost('tipo_doc')] ?? 1;

        $idUsuarioEdit = $this->request->getPost('id_usuario');
        $idPersonaEdit = $this->request->getPost('id_persona');

        // 1. Guardar o Actualizar Persona
        $personaData = [
            'nombres'           => $this->request->getPost('nombre'),
            'apellidos'         => $this->request->getPost('apellidos'),
            'numero_documento'  => $this->request->getPost('num_doc'),
            'email'             => $this->request->getPost('email'),
            'celular'           => $this->request->getPost('telefono'),
            'direccion'         => $this->request->getPost('ciudad'),
            'fecha_nacimiento'  => $this->request->getPost('fecha_nacimiento') ?: null,
            'tipoDocumento_id'  => $tipoDocId
        ];

        if (!empty($idPersonaEdit)) {
            $personaModel->update($idPersonaEdit, $personaData);
            $personaId = $idPersonaEdit;
        } else {
            $personaData['estado'] = true;
            $personaId = $personaModel->insert($personaData);
        }

        if (!$personaId) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al registrar los datos personales.']);
        }

        // 2. Guardar o Actualizar Usuario
        $usuarioData = [
            'usuario'           => $this->request->getPost('email'),
            'rol_id'            => $this->request->getPost('rol'),
            'tipo_jornada_id'   => $this->request->getPost('tipo_jornada')
        ];

        // Solo actualizar clave si se envió (para edición) o si es nuevo
        $clave = $this->request->getPost('password');
        if (!empty($clave)) {
            $usuarioData['clave'] = $clave;
        }

        if (!empty($idUsuarioEdit)) {
            $usuarioModel->update($idUsuarioEdit, $usuarioData);
            $usuarioId = $idUsuarioEdit;
            $mensaje = 'Usuario actualizado correctamente.';
        } else {
            $usuarioData['persona_id'] = $personaId;
            $usuarioData['estado'] = true;
            // Validar clave obligatoria en inserción
            if (empty($clave)) {
                $db->transRollback();
                return $this->response->setJSON(['status' => 'error', 'message' => 'La contraseña es obligatoria para usuarios nuevos.']);
            }
            $usuarioId = $usuarioModel->insert($usuarioData);
            $mensaje = 'Usuario registrado correctamente.';
        }

        if (!$usuarioId) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al registrar la cuenta de usuario.']);
        }

        // 3. Guardar Horario
        // Primero eliminar horarios anteriores si es actualización
        if (!empty($idUsuarioEdit)) {
            $db->table('horario_jornada_detalle')->where('usuario_id', $usuarioId)->delete();
        }

        $tipoJornadaId = $this->request->getPost('tipo_jornada');
        
        if (!empty($tipoJornadaId)) {
            $jornada = $tipoJornadaModel->find($tipoJornadaId);

            if ($jornada && isset($jornada['nombre_jornada']) && strtoupper((string)$jornada['nombre_jornada']) !== 'FREELANCE') {
            $diasMap = [
                'lunes'     => 1,
                'martes'    => 2,
                'miércoles' => 3,
                'jueves'    => 4,
                'viernes'   => 5,
                'sábado'    => 6
            ];
            
            foreach ($diasMap as $dia => $diaInt) {
                // Turno 1
                if ($this->request->getPost("active1_$dia")) {
                    $horarioDetalleModel->insert([
                        'usuario_id'  => $usuarioId,
                        'dia_semana'  => $diaInt,
                        'hora_inicio' => $this->request->getPost("start1_$dia"),
                        'hora_fin'    => $this->request->getPost("end1_$dia"),
                        'estado'      => true
                    ]);
                }
                
                // Turno 2
                if ($this->request->getPost("active2_$dia")) {
                    $horarioDetalleModel->insert([
                        'usuario_id'  => $usuarioId,
                        'dia_semana'  => $diaInt,
                        'hora_inicio' => $this->request->getPost("start2_$dia"),
                        'hora_fin'    => $this->request->getPost("end2_$dia"),
                        'estado'      => true
                    ]);
                }
            }
        }
        } // Fin de if (!empty($tipoJornadaId))

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Ocurrió un error inesperado al procesar el registro.']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $mensaje]);
    }
}
