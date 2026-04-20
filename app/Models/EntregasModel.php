<?php namespace App\Models;

    use CodeIgniter\Model;

    class EntregasModel extends Model
    {
        protected $table      = 'entregas';
        protected $primaryKey = 'id';

        protected $useAutoIncrement = true;

        protected $returnType     = 'array';

        protected $allowedFields = ['id','titulo', 'hora_inicio', 'usuario_id', 'tarea_id', 'cliente_id', 'tiempo_estimado', 'estado', 'link_google_drive', 'fecha_hora_entrega', 'observaciones', 'created_at', 'updated_at', 'user_id_add', 'user_id_update', 'user_id_delete'];

        protected $useTimestamps = true;
        protected $createdField  = 'created_at';
        protected $updatedField  = 'updated_at';

    }

?>