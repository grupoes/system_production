<?php

namespace App\Models;

use CodeIgniter\Model;

class ModulosModel extends Model
{
    protected $table      = 'modulos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'modulo', 'url', 'icono', 'orden', 'estado', 'idpadre'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
