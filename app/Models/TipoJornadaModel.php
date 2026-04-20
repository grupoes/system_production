<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoJornadaModel extends Model
{
    protected $table      = 'tipo_jornada';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'nombre_jornada', 'estado'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
