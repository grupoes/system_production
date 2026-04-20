<?php

namespace App\Libraries;

class ApiConsulta
{
    protected $token = "facturalaya_erickpeso_05jFE7sAOudi8j0";

    /**
     * Consulta DNI o RUC a través de la API externa.
     * 
     * @param string $tipo 'dni' o 'ruc'
     * @param string $numero El número de documento
     * @return array
     */
    public function consultar($tipo, $numero)
    {
        $tipo = strtolower($tipo);
        
        if ($tipo == 'dni') {
            $ruta = "https://facturalahoy.com/api/persona/" . $numero . '/' . $this->token . '/completa';
        } elseif ($tipo == 'ruc') {
            $ruta = "https://facturalahoy.com/api/empresa/" . $numero . '/' . $this->token . '/completa';
        } else {
            return [
                'respuesta' => 'error',
                'titulo'    => 'Error',
                'mensaje'   => 'Tipo de Documento Desconocido'
            ];
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $ruta,
            CURLOPT_USERAGENT => 'Consulta Datos',
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 400,
            CURLOPT_FAILONERROR => true
        ));

        $data = curl_exec($curl);
        $error_msg = curl_error($curl);
        curl_close($curl);

        if ($error_msg) {
            return [
                'respuesta'     => 'error',
                'titulo'        => 'Error',
                'encontrado'    => false,
                'mensaje'       => 'Error en Api de Búsqueda',
                'errores_curl'  => $error_msg
            ];
        }

        $data_resp = json_decode($data);
        if (!isset($data_resp->respuesta) || $data_resp->respuesta == 'error') {
            return [
                'respuesta'  => 'error',
                'titulo'     => 'Error',
                'encontrado' => false,
                'data_resp'  => $data_resp
            ];
        }

        return [
            'respuesta'  => 'ok',
            'encontrado' => true,
            'api'        => true,
            'data'       => $data_resp
        ];
    }
}
