<?php

namespace App\Models;

use CodeIgniter\Model;

class HorasExtrasModel extends Model
{
    protected $table      = 'horas_extras';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'actividad_id', 'usuario_id', 'fecha', 'minutos', 'tipo', 'estado'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}