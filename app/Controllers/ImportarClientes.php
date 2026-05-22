<?php

namespace App\Controllers;

use App\Libraries\GoogleSheetsService;

class ImportarClientes extends BaseController
{
    public function index(): string
    {
        return view('importar_clientes/index', [
            'title' => 'Importar Clientes desde Sheets'
        ]);
    }

    public function fetchSheetData()
    {
        try {
            $googleSheetsService = new GoogleSheetsService();
            // Leemos todas las hojas del documento y las agrupamos
            $datos = $googleSheetsService->leerTodasLasHojas();
            
            if (isset($datos['error'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $datos['error']
                ]);
            }

            if (empty($datos)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'La hoja está vacía.'
                ]);
            }

            // Separamos la cabecera del cuerpo de los datos
            $headers = array_shift($datos);
            
            // Retornamos cabecera y filas (quitando filas completamente vacías si existen)
            $filasLimpias = array_filter($datos, function($fila) {
                // Filtra filas que al menos tengan algún dato
                return count(array_filter($fila)) > 0;
            });

            return $this->response->setJSON([
                'status'  => 'success',
                'headers' => $headers,
                'data'    => array_values($filasLimpias) // reindexar array
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
