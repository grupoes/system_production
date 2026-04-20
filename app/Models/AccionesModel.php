<?php

namespace App\Models;

use CodeIgniter\Model;

class AccionesModel extends Model
{
    protected $table      = 'acciones';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'nombre_accion', 'descripcion', 'estado'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
