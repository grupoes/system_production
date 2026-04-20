<?php

namespace App\Models;

use CodeIgniter\Model;

class TareaRolesModel extends Model
{
    protected $table      = 'tareas_roles';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'tarea_id', 'rol_id', 'prioridad', 'estado'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
