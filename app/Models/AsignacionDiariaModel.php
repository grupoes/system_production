<?php

namespace App\Models;

use CodeIgniter\Model;

class AsignacionDiariaModel extends Model
{
    protected $table      = 'asignacion_dias';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'dia_id', 'usuario_id'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
