<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicioModel extends Model
{
    protected $table      = 'servicios';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'codigo',
        'cliente_id',
        'captador_id',
        'tipo_servicio_id',
        'titulo',
        'descripcion',
        'horas_estimadas',
        'fecha_registro',
        'fecha_inicio',
        'fecha_limite',
        'fecha_entrega_calculada',
        'fecha_entrega_real',
        'jefe_produccion_id',
        'auxiliar_produccion_id',
        'estado',
        'prioridad',
        'observaciones'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'cliente_id' => 'required|integer',
        'captador_id' => 'required|integer',
        'tipo_servicio_id' => 'required|integer',
        'titulo' => 'required|min_length[3]|max_length[255]',
        'horas_estimadas' => 'required|decimal|greater_than[0]'
    ];

    protected $validationMessages = [
        'titulo' => [
            'required' => 'El título del servicio es obligatorio',
            'min_length' => 'El título debe tener al menos 3 caracteres'
        ],
        'horas_estimadas' => [
            'required' => 'Las horas estimadas son obligatorias',
            'greater_than' => 'Las horas estimadas deben ser mayor a 0'
        ]
    ];

    /**
     * Generar código único para el servicio
     */
    public function generarCodigo()
    {
        $year = date('Y');
        $month = date('m');

        // Obtener el último código del mes
        $ultimoServicio = $this->like('codigo', "SRV-{$year}{$month}", 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($ultimoServicio) {
            // Extraer el número secuencial
            $partes = explode('-', $ultimoServicio['codigo']);
            $secuencial = intval(substr($partes[1], 6)) + 1;
        } else {
            $secuencial = 1;
        }

        return sprintf('SRV-%s%s%04d', $year, $month, $secuencial);
    }

    /**
     * Obtener servicios con información completa (usando la vista)
     */
    public function obtenerServiciosCompletos($filtros = [])
    {
        $db = \Config\Database::connect();
        $builder = $db->table('vista_servicios_completa');

        if (isset($filtros['estado']) && !empty($filtros['estado'])) {
            $builder->where('estado', $filtros['estado']);
        }

        if (isset($filtros['auxiliar_id']) && !empty($filtros['auxiliar_id'])) {
            $builder->where('auxiliar_id', $filtros['auxiliar_id']);
        }

        if (isset($filtros['jefe_id']) && !empty($filtros['jefe_id'])) {
            $builder->where('jefe_produccion', $filtros['jefe_id']);
        }

        if (isset($filtros['prioridad']) && !empty($filtros['prioridad'])) {
            $builder->where('prioridad', $filtros['prioridad']);
        }

        if (isset($filtros['alerta']) && !empty($filtros['alerta'])) {
            $builder->where('alerta', $filtros['alerta']);
        }

        return $builder->orderBy('fecha_limite', 'ASC')->get()->getResultArray();
    }

    /**
     * Obtener servicios por auxiliar
     */
    public function obtenerPorAuxiliar($auxiliarId, $soloActivos = true)
    {
        $builder = $this->select('servicios.*, clientes.nombres as cliente_nombres, clientes.apellidos as cliente_apellidos, tipos_servicio.nombre as tipo_servicio_nombre')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('tipos_servicio', 'tipos_servicio.id = servicios.tipo_servicio_id')
            ->where('servicios.auxiliar_produccion_id', $auxiliarId);

        if ($soloActivos) {
            $builder->whereNotIn('servicios.estado', ['Completado', 'Entregado']);
        }

        return $builder->orderBy('servicios.fecha_limite', 'ASC')->findAll();
    }

    /**
     * Obtener servicios por jefe de producción
     */
    public function obtenerPorJefe($jefeId, $soloActivos = true)
    {
        $builder = $this->select('servicios.*, clientes.nombres as cliente_nombres, clientes.apellidos as cliente_apellidos, tipos_servicio.nombre as tipo_servicio_nombre')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('tipos_servicio', 'tipos_servicio.id = servicios.tipo_servicio_id')
            ->where('servicios.jefe_produccion_id', $jefeId);

        if ($soloActivos) {
            $builder->whereNotIn('servicios.estado', ['Completado', 'Entregado']);
        }

        return $builder->orderBy('servicios.fecha_limite', 'ASC')->findAll();
    }

    /**
     * Obtener estadísticas generales
     */
    public function obtenerEstadisticas()
    {
        $db = \Config\Database::connect();

        $stats = [
            'total' => $this->countAll(),
            'pendientes' => $this->where('estado', 'Pendiente')->countAllResults(false),
            'en_proceso' => $this->where('estado', 'En Proceso')->countAllResults(false),
            'en_revision' => $this->where('estado', 'En Revisión')->countAllResults(false),
            'completados' => $this->where('estado', 'Completado')->countAllResults(false),
            'entregados' => $this->where('estado', 'Entregado')->countAllResults(false),
            'atrasados' => $this->where('fecha_limite <', date('Y-m-d'))
                ->whereNotIn('estado', ['Completado', 'Entregado'])
                ->countAllResults(false),
            'proximos_vencer' => $this->where('fecha_limite >=', date('Y-m-d'))
                ->where('fecha_limite <=', date('Y-m-d', strtotime('+3 days')))
                ->whereNotIn('estado', ['Completado', 'Entregado'])
                ->countAllResults(false)
        ];

        return $stats;
    }

    /**
     * Obtener próximos vencimientos
     */
    public function obtenerProximosVencimientos($dias = 7, $limite = 10)
    {
        return $this->select('servicios.*, clientes.nombres as cliente_nombres, clientes.apellidos as cliente_apellidos, 
                             CONCAT(aux.nombres, " ", aux.apellidos) as auxiliar_nombre')
            ->join('clientes', 'clientes.id = servicios.cliente_id')
            ->join('usuarios aux', 'aux.id = servicios.auxiliar_produccion_id', 'left')
            ->where('servicios.fecha_limite >=', date('Y-m-d'))
            ->where('servicios.fecha_limite <=', date('Y-m-d', strtotime("+{$dias} days")))
            ->whereNotIn('servicios.estado', ['Completado', 'Entregado'])
            ->orderBy('servicios.fecha_limite', 'ASC')
            ->limit($limite)
            ->findAll();
    }

    /**
     * Obtener carga de trabajo por auxiliar
     */
    public function obtenerCargaPorAuxiliar()
    {
        $db = \Config\Database::connect();
        return $db->table('vista_carga_auxiliares')->get()->getResultArray();
    }
}
