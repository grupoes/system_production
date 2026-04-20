<?php

namespace App\Models;

use CodeIgniter\Model;

class FeriadoModel extends Model
{
    protected $table      = 'feriados';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'id',
        'nombre',
        'fecha',
        'tipo',
        'es_laborable',
        'estado'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[150]',
        'fecha' => 'required|valid_date',
        'tipo' => 'required|in_list[Nacional,Regional,Local]'
    ];

    /**
     * Obtener feriados activos
     */
    public function obtenerActivos()
    {
        return $this->where('estado', 1)
            ->orderBy('fecha', 'ASC')
            ->findAll();
    }

    /**
     * Obtener feriados de un año específico
     */
    public function obtenerPorAnio($anio)
    {
        return $this->where('YEAR(fecha)', $anio)
            ->where('estado', 1)
            ->orderBy('fecha', 'ASC')
            ->findAll();
    }

    /**
     * Verificar si una fecha es feriado
     */
    public function esFeriado($fecha)
    {
        $feriado = $this->where('fecha', $fecha)
            ->where('estado', 1)
            ->where('es_laborable', 0)
            ->first();

        return $feriado !== null;
    }

    /**
     * Obtener feriados en un rango de fechas
     */
    public function obtenerEnRango($fechaInicio, $fechaFin)
    {
        return $this->where('fecha >=', $fechaInicio)
            ->where('fecha <=', $fechaFin)
            ->where('estado', 1)
            ->orderBy('fecha', 'ASC')
            ->findAll();
    }
}
