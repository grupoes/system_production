<?php

namespace App\Controllers;

use App\Libraries\GoogleSheetsService;

class ImportarClientes extends BaseController
{
    public function index(): string
    {
        return view('importar_clientes/index', [
            'title' => 'Importar Clientes desde Sheets'
        ]);
    }

    public function fetchSheetData()
    {
        try {
            $googleSheetsService = new GoogleSheetsService();
            // Leemos todas las hojas del documento y las agrupamos
            $datos = $googleSheetsService->leerTodasLasHojas();
            
            if (isset($datos['error'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $datos['error']
                ]);
            }

            if (empty($datos)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'La hoja está vacía.'
                ]);
            }

            // Separamos la cabecera del cuerpo de los datos
            $headers = array_shift($datos);
            
            // Retornamos cabecera y filas (quitando filas completamente vacías si existen)
            $filasLimpias = array_filter($datos, function($fila) {
                // Filtra filas que al menos tengan algún dato
                return count(array_filter($fila)) > 0;
            });

            return $this->response->setJSON([
                'status'  => 'success',
                'headers' => $headers,
                'data'    => array_values($filasLimpias) // reindexar array
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAuxiliarSchedule()
    {
        $usuarioId = $this->request->getGet('usuario_id');
        if (empty($usuarioId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID de auxiliar requerido.']);
        }

        $db = \Config\Database::connect();
        
        // Buscar el último horario del auxiliar a partir de hoy
        $lastSchedule = $db->table('horario_usuario')
            ->where('usuario_id', $usuarioId)
            ->where('fecha >=', date('Y-m-d'))
            ->where('estado', true)
            ->orderBy('fecha', 'DESC')
            ->orderBy('hora_fin', 'DESC')
            ->get()->getRowArray();

        if ($lastSchedule) {
            $nextDate = $lastSchedule['fecha'];
            $nextTime = $lastSchedule['hora_fin'];
            
            // Ajustar según horario de trabajo
            $adjust = true;
            while ($adjust) {
                $adjust = false;
                $dayOfWeek = date('N', strtotime($nextDate));
                if ($dayOfWeek == 7) { // Domingo -> ir a lunes 08:00
                    $nextDate = date('Y-m-d', strtotime("$nextDate + 1 day"));
                    $nextTime = '08:00';
                    $adjust = true;
                } elseif ($dayOfWeek <= 5) { // Lunes a viernes
                    if ($nextTime >= '19:00:00') {
                        $nextDate = date('Y-m-d', strtotime("$nextDate + 1 day"));
                        $nextTime = '08:00';
                        $adjust = true;
                    } elseif ($nextTime >= '13:00:00' && $nextTime < '15:00:00') {
                        $nextTime = '15:00';
                    } elseif ($nextTime < '08:00:00') {
                        $nextTime = '08:00';
                    }
                } elseif ($dayOfWeek == 6) { // Sábado
                    if ($nextTime >= '13:00:00') {
                        $nextDate = date('Y-m-d', strtotime("$nextDate + 2 days")); // saltar domingo
                        $nextTime = '08:00';
                        $adjust = true;
                    } elseif ($nextTime < '08:00:00') {
                        $nextTime = '08:00';
                    }
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'fecha'  => $nextDate,
                'hora'   => date('H:i', strtotime($nextTime))
            ]);
        }

        // Si no tiene horario, retornar hoy con hora 08:00
        return $this->response->setJSON([
            'status' => 'success',
            'fecha'  => date('Y-m-d'),
            'hora'   => '08:00'
        ]);
    }

    public function saveSchedule()
    {
        $db = \Config\Database::connect();
        
        $clienteName = $this->request->getPost('cliente');
        $dni         = $this->request->getPost('dni');
        $celular     = $this->request->getPost('celular');
        $nivel       = $this->request->getPost('nivel');
        $carreraName = $this->request->getPost('carrera');
        $univName    = $this->request->getPost('universidad');
        $linkDrive   = $this->request->getPost('link_drive');
        $fEntrega    = $this->request->getPost('fecha_entrega');
        $jefe        = $this->request->getPost('jefe');
        
        $auxiliarId  = $this->request->getPost('auxiliar_id');
        $tareaId     = $this->request->getPost('tarea_id');
        $tiempoStr   = $this->request->getPost('tiempo'); // de input-tiempo
        $fechaInicio = $this->request->getPost('fecha_inicio'); // dia de empiezo
        $horaInicio  = $this->request->getPost('hora_inicio'); // hora
        $descripcion = $this->request->getPost('descripcion'); // columna actividades

        if (empty($clienteName)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'El nombre del cliente es obligatorio.']);
        }
        if (empty($auxiliarId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe seleccionar un auxiliar.']);
        }
        if (empty($tareaId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe seleccionar una tarea.']);
        }
        if (empty($fechaInicio) || empty($horaInicio)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Debe seleccionar día y hora de empiezo.']);
        }

        // Validar horario de inicio
        $dayOfWeek = date('N', strtotime($fechaInicio));
        $timeFormat = date('H:i', strtotime($horaInicio));
        $isValidTime = false;
        if ($dayOfWeek >= 1 && $dayOfWeek <= 5) { // Lunes a Viernes
            if (($timeFormat >= '08:00' && $timeFormat <= '13:00') || ($timeFormat >= '15:00' && $timeFormat <= '19:00')) {
                $isValidTime = true;
            }
        } elseif ($dayOfWeek == 6) { // Sábado
            if ($timeFormat >= '08:00' && $timeFormat <= '13:00') {
                $isValidTime = true;
            }
        }
        if (!$isValidTime) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Horario de inicio no permitido. Lunes a Viernes: 08:00 a 13:00 y 15:00 a 19:00. Sábados: 08:00 a 13:00.'
            ]);
        }

        $minutos = $this->parseTiempoAMinutos($tiempoStr);
        if ($minutos <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'El tiempo estimado debe ser mayor a 0 minutos.']);
        }

        $db->transStart();
        try {
            // 1. Resolver Persona
            $persona = null;
            if (!empty($dni)) {
                $persona = $db->table('personas')->where('numero_documento', $dni)->get()->getRowArray();
            }
            if (!$persona && !empty($celular)) {
                $persona = $db->table('personas')->where('celular', $celular)->get()->getRowArray();
            }
            
            if (!$persona) {
                // Separar nombres y apellidos
                $parts = explode(' ', trim($clienteName), 2);
                $nombres = $parts[0] ?? '';
                $apellidos = $parts[1] ?? '';
                
                $db->table('personas')->insert([
                    'nombres'          => strtoupper($nombres),
                    'apellidos'        => strtoupper($apellidos),
                    'numero_documento' => $dni ?: null,
                    'celular'          => $celular ?: null,
                    'estado'           => true
                ]);
                $personaId = $db->insertID();
            } else {
                $personaId = $persona['id'];
                // Opcionalmente actualizar celular / DNI si estaban vacíos
                $updateData = [];
                if (empty($persona['numero_documento']) && !empty($dni)) $updateData['numero_documento'] = $dni;
                if (empty($persona['celular']) && !empty($celular)) $updateData['celular'] = $celular;
                if (!empty($updateData)) {
                    $db->table('personas')->where('id', $personaId)->update($updateData);
                }
            }

            // 2. Resolver Universidad (Institución)
            $univId = null;
            if (!empty($univName)) {
                $univ = $db->table('institucion')->where('LOWER(nombre)', strtolower(trim($univName)))->get()->getRowArray();
                if ($univ) {
                    $univId = $univ['id'];
                } else {
                    $db->table('institucion')->insert([
                        'nombre' => strtoupper(trim($univName)),
                        'estado' => true
                    ]);
                    $univId = $db->insertID();
                }
            }

            // 3. Resolver Carrera
            $carreraId = null;
            if (!empty($carreraName) && $univId) {
                $carrera = $db->table('carreras')
                    ->where('LOWER(nombre)', strtolower(trim($carreraName)))
                    ->where('institucion_id', $univId)
                    ->get()->getRowArray();
                if ($carrera) {
                    $carreraId = $carrera['id'];
                } else {
                    $db->table('carreras')->insert([
                        'nombre'         => strtoupper(trim($carreraName)),
                        'institucion_id' => $univId,
                        'estado'         => true
                    ]);
                    $carreraId = $db->insertID();
                }
            }

            // 4. Resolver Nivel Académico
            $nivelId = null;
            $nivelStr = trim($nivel);
            if (!empty($nivelStr)) {
                $nivelAcademico = $db->table('nivel_academico')
                    ->where('LOWER(nombre)', strtolower($nivelStr))
                    ->get()->getRowArray();
                if ($nivelAcademico) {
                    $nivelId = $nivelAcademico['id'];
                } else {
                    // Buscar por like
                    $nivelAcademico = $db->table('nivel_academico')
                        ->like('nombre', $nivelStr, 'both', null, true)
                        ->get()->getRowArray();
                    if ($nivelAcademico) {
                        $nivelId = $nivelAcademico['id'];
                    }
                }
            }

            // 5. Resolver Origen
            $origenId = null;
            $origen = $db->table('origen')->where('LOWER(nombre)', 'google sheets')->get()->getRowArray();
            if ($origen) {
                $origenId = $origen['id'];
            } else {
                $origen = $db->table('origen')->where('estado', true)->get()->getRowArray();
                if ($origen) {
                    $origenId = $origen['id'];
                } else {
                    $db->table('origen')->insert([
                        'nombre' => 'GOOGLE SHEETS',
                        'descripcion' => 'Importado desde Google Sheets',
                        'estado' => true
                    ]);
                    $origenId = $db->insertID();
                }
            }

            // 6. Resolver Jefe/Vendedor
            $vendedorId = session()->get('id') ?: 1; // fallback a 1 si no hay sesión
            $jefeStr = trim($jefe);
            if (!empty($jefeStr)) {
                $personaJefe = $db->table('personas p')
                    ->select('u.id')
                    ->join('usuarios u', 'u.persona_id = p.id')
                    ->like('p.nombres', $jefeStr, 'both', null, true)
                    ->orLike('p.apellidos', $jefeStr, 'both', null, true)
                    ->get()->getRowArray();
                if ($personaJefe) {
                    $vendedorId = $personaJefe['id'];
                }
            }

            // 7. Insertar Prospecto
            $fechaEntregaVal = null;
            if (!empty($fEntrega)) {
                // Intentar formatear la fecha
                $timestamp = strtotime($fEntrega);
                if ($timestamp) {
                    $fechaEntregaVal = date('Y-m-d', $timestamp);
                }
            }

            $tituloProspecto = !empty($descripcion) ? strtoupper(trim($descripcion)) : ('TRABAJO DE ' . strtoupper($clienteName));

            $db->table('prospectos')->insert([
                'titulo_prospecto'   => $tituloProspecto,
                'origen_id'          => $origenId,
                'nivel_academico_id' => $nivelId ?: null,
                'carrera_id'         => $carreraId ?: null,
                'fecha_entrega'      => $fechaEntregaVal,
                'link_drive'         => $linkDrive ?: null,
                'contenido'          => 'Importado desde Google Sheets. F. Entrega: ' . $fEntrega,
                'prioridad'          => 'NORMAL',
                'fecha_contacto'     => date('Y-m-d'),
                'estado'             => true,
                'usuario_venta_id'   => $vendedorId,
                'estado_cliente'     => 'cliente', // Importado directamente como cliente
                'responsable_id'     => $auxiliarId,
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s')
            ]);
            
            $prospectoId = $db->insertID();

            // Registrar Historial
            $db->table('historial_estado_prospecto')->insert([
                'prospecto_id' => $prospectoId,
                'estado'       => 'CLIENTE',
                'fecha_inicio' => date('Y-m-d H:i:s'),
                'usuario_id'   => session()->get('id') ?: 1,
                'comentario'   => 'CLIENTE IMPORTADO Y PROGRAMADO DESDE GOOGLE SHEETS POR ' . (session()->get('nombre') ?: 'SYSTEM')
            ]);

            // Vincular Prospecto Persona
            $db->table('prospecto_persona')->insert([
                'persona_id'   => $personaId,
                'prospecto_id' => $prospectoId
            ]);

            // 8. Programación de Actividad y Horarios
            // Lógica de fragmentación
            $currDate = $fechaInicio;
            $currTime = date('H:i:s', strtotime($horaInicio));
            $minsRemaining = $minutos;
            $horariosAInsertar = [];
            $firstBlock = true;
            $actividadFechaInicio = $currDate;
            $actividadHoraInicio = $currTime;

            while ($minsRemaining > 0) {
                $dayOfWeek = date('N', strtotime($currDate));

                if ($dayOfWeek == 7) { // Domingo
                    $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                    $currTime = '08:00:00';
                    continue;
                }

                if ($currTime < '08:00:00') $currTime = '08:00:00';

                if ($dayOfWeek <= 5) { // Lunes a Viernes
                    if ($currTime >= '13:00:00' && $currTime < '15:00:00') {
                        $currTime = '15:00:00';
                    } elseif ($currTime >= '19:00:00') {
                        $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                        $currTime = '08:00:00';
                        continue;
                    }
                } elseif ($dayOfWeek == 6) { // Sábado
                    if ($currTime >= '13:00:00') {
                        $currDate = date('Y-m-d', strtotime("$currDate + 1 day"));
                        $currTime = '08:00:00';
                        continue;
                    }
                }

                $blockEnd = '13:00:00';
                if ($dayOfWeek <= 5 && $currTime >= '15:00:00') {
                    $blockEnd = '19:00:00';
                }

                $availableMins = round((strtotime("$currDate $blockEnd") - strtotime("$currDate $currTime")) / 60);

                if ($availableMins <= 0) {
                    $currTime = $blockEnd;
                    continue;
                }

                $minsToUse = min($minsRemaining, $availableMins);
                $endTime = date('H:i:s', strtotime("$currDate $currTime + $minsToUse minutes"));

                $horariosAInsertar[] = [
                    'usuario_id'  => $auxiliarId,
                    'fecha'       => $currDate,
                    'hora_inicio' => $currTime,
                    'hora_fin'    => $endTime,
                    'categoria'   => 'PRODUCCION',
                    'tipo'        => 'programado',
                    'estado'      => true,
                    'created_at'  => date('Y-m-d H:i:s')
                ];

                if ($firstBlock) {
                    $actividadFechaInicio = $currDate;
                    $actividadHoraInicio  = $currTime;
                    $firstBlock = false;
                }

                $minsRemaining -= $minsToUse;
                $currTime = $endTime;
            }

            // Insertar Actividad
            $colorPrioridad = '#24BF17'; // NORMAL
            $db->table('actividades')->insert([
                'prospecto_id'            => $prospectoId,
                'usuario_id'              => $auxiliarId,
                'tarea_id'                => $tareaId,
                'tiempo_estimado_minutos' => $minutos,
                'estado_progreso'         => 'PENDIENTE',
                'prioridad'               => 'NORMAL',
                'color'                   => $colorPrioridad,
                'estado'                  => true,
                'fecha_inicio'            => $actividadFechaInicio,
                'hora_inicio'             => $actividadHoraInicio,
                'created_at'              => date('Y-m-d H:i:s')
            ]);
            $actividadId = $db->insertID();

            // Insertar Horarios programados
            foreach ($horariosAInsertar as $horario) {
                $horario['actividad_id'] = $actividadId;
                $db->table('horario_usuario')->insert($horario);
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \Exception('Error al confirmar la transacción de base de datos.');
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Cliente importado y programado con éxito.']);

        } catch (\Throwable $th) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $th->getMessage()]);
        }
    }

    private function parseTiempoAMinutos($tiempoStr)
    {
        $tiempoStr = trim($tiempoStr);
        if (empty($tiempoStr)) return 0;
        
        // Si es solo un número, asumimos que son minutos
        if (is_numeric($tiempoStr)) {
            return (int)$tiempoStr;
        }
        
        // Parsear formatos como "2H 30m", "2h 30m", "2H", "30m", "1.5H"
        $minutos = 0;
        // Horas
        if (preg_match('/(\d+(?:\.\d+)?)\s*[hH]/', $tiempoStr, $matches)) {
            $minutos += floatval($matches[1]) * 60;
        }
        // Minutos
        if (preg_match('/(\d+)\s*[mM]/', $tiempoStr, $matches)) {
            $minutos += intval($matches[1]);
        }
        
        return (int)round($minutos);
    }
}
