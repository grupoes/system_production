<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoTareaModel extends Model
{
    protected $table      = 'tipo_tarea';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'tipo', 'estado', 'color'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
