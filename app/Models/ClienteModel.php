<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table      = 'clientes';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';

    protected $allowedFields = [
        'id',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'empresa',
        'direccion',
        'estado'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nombres'   => 'required|min_length[2]|max_length[100]',
        'apellidos' => 'required|min_length[2]|max_length[100]',
        'email'     => 'permit_empty|valid_email|max_length[150]',
        'telefono'  => 'permit_empty|max_length[20]'
    ];

    protected $validationMessages = [
        'nombres' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener al menos 2 caracteres'
        ],
        'apellidos' => [
            'required' => 'Los apellidos son obligatorios',
            'min_length' => 'Los apellidos deben tener al menos 2 caracteres'
        ],
        'email' => [
            'valid_email' => 'Debe proporcionar un email válido'
        ]
    ];

    /**
     * Buscar clientes por nombre, apellido o email
     */
    public function buscarClientes($termino)
    {
        return $this->like('nombres', $termino)
            ->orLike('apellidos', $termino)
            ->orLike('email', $termino)
            ->where('estado', 1)
            ->findAll();
    }

    /**
     * Obtener clientes activos
     */
    public function obtenerActivos()
    {
        return $this->where('estado', 1)
            ->orderBy('nombres', 'ASC')
            ->findAll();
    }
}
