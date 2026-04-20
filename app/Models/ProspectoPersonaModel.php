<?php

namespace App\Models;

use CodeIgniter\Model;

class ProspectoPersonaModel extends Model
{
    protected $table      = 'prospecto_persona';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'persona_id', 'prospecto_id'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}