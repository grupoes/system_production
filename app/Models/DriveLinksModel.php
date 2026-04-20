<?php

namespace App\Models;

use CodeIgniter\Model;

class DriveLinksModel extends Model
{
    protected $table      = 'drive_links';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'usuario_id', 'prospecto_id', 'link_drive', 'created_at'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
