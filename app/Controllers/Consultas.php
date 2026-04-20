<?php

namespace App\Controllers;

use App\Libraries\ApiConsulta;

class Consultas extends BaseController
{
    /**
     * Endpoint para consultar DNI o RUC vía AJAX.
     * Ruta: /consultas/documento/(:segment)/(:segment)
     */
    public function documento($tipo, $numero)
    {
        $api = new ApiConsulta();
        $resultado = $api->consultar($tipo, $numero);

        return $this->response->setJSON($resultado);
    }
}
