<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialServicioModel extends Model
{
    protected $table      = 'historial_servicios';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'servicio_id',
        'usuario_id',
        'estado_anterior',
        'estado_nuevo',
        'comentario'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    /**
     * Obtener historial de un servicio
     */
    public function obtenerPorServicio($servicioId)
    {
        return $this->select('historial_servicios.*, usuarios.nombres, usuarios.apellidos')
            ->join('usuarios', 'usuarios.id = historial_servicios.usuario_id')
            ->where('historial_servicios.servicio_id', $servicioId)
            ->orderBy('historial_servicios.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Registrar cambio de estado
     */
    public function registrarCambio($servicioId, $usuarioId, $estadoAnterior, $estadoNuevo, $comentario = '')
    {
        return $this->insert([
            'servicio_id' => $servicioId,
            'usuario_id' => $usuarioId,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'comentario' => $comentario
        ]);
    }
}
