<?php

namespace App\Models;

use CodeIgniter\Model;

class OrigenModel extends Model
{
    protected $table      = 'origen';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'nombre', 'descripcion', 'estado'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
