<?php

namespace App\Models;

use CodeIgniter\Model;

class ActividadEstadoHistorialModel extends Model
{
    protected $table      = 'actividad_estado_historial';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'actividad_id', 'estado_progreso', 'fecha_inicio', 'fecha_fin', 'duracion_segundos'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
