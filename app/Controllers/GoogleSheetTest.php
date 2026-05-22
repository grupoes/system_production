<?php

namespace App\Controllers;

use App\Libraries\GoogleSheetsService;

class GoogleSheetTest extends BaseController
{
    public function index()
    {
        $googleSheetsService = new GoogleSheetsService();
        
        // Asumiendo que quieres leer la 'Hoja 1' o como se llame tu pestaña. 
        // Cámbialo si tu pestaña tiene otro nombre.
        $datos = $googleSheetsService->leerHoja('Hoja 1'); 

        echo "<h2>Datos obtenidos de Google Sheets:</h2>";
        
        if (isset($datos['error'])) {
            echo "<p style='color: red;'><strong>Error:</strong> " . $datos['error'] . "</p>";
            echo "<p>Asegúrate de haber configurado el archivo `credentials.json` en la ruta especificada en el archivo `.env`.</p>";
            return;
        }

        if (empty($datos)) {
            echo "No se encontraron datos en la hoja especificada.";
        } else {
            echo "<pre>";
            print_r($datos);
            echo "</pre>";
        }
    }
}
