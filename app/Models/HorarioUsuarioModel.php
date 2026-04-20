<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioUsuarioModel extends Model
{
    protected $table      = 'horario_usuario';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'actividad_id', 'usuario_id', 'fecha', 'hora_inicio', 'hora_fin', 'estado', 'created_at', 'updated_at', 'duracion_minutos', 'tipo', 'orden', 'categoria'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';
}
