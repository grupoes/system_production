<?php

namespace App\Models;

use CodeIgniter\Model;

class ActividadesModel extends Model
{
    protected $table      = 'actividades';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'prospecto_id', 'usuario_id', 'estado', 'prioridad', 'created_at', 'updated_at', 'color', 'estado_progreso', 'tarea_id', 'tiempo_estimado_minutos', 'tiempo_real_minutos', 'fecha_inicio', 'hora_inicio', 'tipo_horario', 'modalidad_horario', 'fecha_canje', 'hora_inicio_canje', 'hora_fin_canje'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';
}
