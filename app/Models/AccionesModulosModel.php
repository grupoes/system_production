<?php

namespace App\Models;

use CodeIgniter\Model;  

class AccionesModulosModel extends Model
{
    protected $table      = 'acciones_modulos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'modulo_id', 'accion_id'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
