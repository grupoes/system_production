<?php

namespace App\Models;

use CodeIgniter\Model;

class ResignacionesModel extends Model
{
    protected $table      = 'resignaciones';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'usuario_id', 'prospecto_id', 'usuario_reasignar_id', 'estado', 'name_tarea', 'created_at', 'updated_at', 'fecha_reasignado', 'usuario_id_remitente'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}