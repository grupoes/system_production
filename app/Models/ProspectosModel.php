<?php

namespace App\Models;

use CodeIgniter\Model;

class ProspectosModel extends Model
{
    protected $table      = 'prospectos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = ['id', 'fecha_contacto', 'origen_id', 'usuario_venta_id', 'nivel_academico_id', 'carrera_id', 'estado', 'fecha_entrega', 'contenido', 'link_drive', 'created_at', 'updated_at', 'prioridad', 'estado_cliente', 'responsable_id', 'titulo_prospecto'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
