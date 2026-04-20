<?php

namespace App\Models;

use CodeIgniter\Model;

class institucionModel extends Model
{
    protected $table      = 'institucion';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'nombre', 'abreviatura', 'estado', 'sector', 'tipo'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}