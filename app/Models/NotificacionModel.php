<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificacionModel extends Model
{
    protected $table      = 'notificaciones';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    protected $allowedFields = [
        'usuario_id',
        'remitente_id',
        'titulo',
        'mensaje',
        'tipo',
        'prioridad',
        'es_leida',
        'fecha_lectura'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';
}
