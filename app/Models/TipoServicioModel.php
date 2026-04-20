<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoServicioModel extends Model
{
    protected $table      = 'tipos_servicio';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'id',
        'nombre',
        'descripcion',
        'horas_estimadas_base',
        'estado'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    /**
     * Obtener tipos de servicio activos
     */
    public function obtenerActivos()
    {
        return $this->where('estado', 1)
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }
}
