<?php

namespace App\Models;

use CodeIgniter\Model;  

class DiaModel extends Model
{
    protected $table      = 'dias';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'dia', 'estado'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
