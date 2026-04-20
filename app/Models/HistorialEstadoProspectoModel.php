<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialEstadoProspectoModel extends Model
{
    protected $table      = 'historial_estados_prospecto';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'prospecto_id', 'usuario_id', 'estado', 'fecha_inicio', 'fecha_fin', 'usuario_id', 'comentario'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
