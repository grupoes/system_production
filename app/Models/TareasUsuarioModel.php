<?php

namespace App\Models;

use CodeIgniter\Model;

class TareasUsuarioModel extends Model
{
    protected $table      = 'tareas_usuarios';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'tarea_id', 'usuario_id', 'activo', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
