<?php

namespace App\Libraries;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetsService
{
    protected $client;
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = env('GOOGLE_SHEET_ID');
        
        $this->client = new Client();
        $this->client->setApplicationName('Control Produccion - Google Sheets Integration');
        $this->client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        
        $credentialsPath = FCPATH . '../' . env('GOOGLE_CREDENTIALS_PATH');
        
        if (file_exists($credentialsPath)) {
            $this->client->setAuthConfig($credentialsPath);
            $this->client->useApplicationDefaultCredentials();
        } else {
            throw new \Exception("No se encontró el archivo de credenciales en: " . $credentialsPath);
        }

        $this->service = new Sheets($this->client);
    }

    /**
     * Lee un rango específico de la hoja de cálculo.
     * Ejemplo: leerHoja('Hoja 1!A1:D10')
     */
    public function leerHoja($rango = 'Hoja 1')
    {
        try {
            $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $rango);
            return $response->getValues();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Lee TODAS las hojas del documento y las agrupa en una sola tabla.
     */
    public function leerTodasLasHojas()
    {
        try {
            // 1. Obtener la metadata del documento para sacar los nombres de todas las hojas
            $spreadsheet = $this->service->spreadsheets->get($this->spreadsheetId);
            $sheets = $spreadsheet->getSheets();
            
            $sheetNames = [];
            foreach ($sheets as $sheet) {
                $sheetNames[] = $sheet->getProperties()->getTitle();
            }

            if (empty($sheetNames)) {
                return [];
            }

            // 2. Traer los datos de todas las hojas en una sola petición (batchGet)
            $response = $this->service->spreadsheets_values->batchGet($this->spreadsheetId, ['ranges' => $sheetNames]);
            $valueRanges = $response->getValueRanges();

            $allData = [];
            $headers = null;
            $originalHeaderCount = 0;

            foreach ($valueRanges as $index => $valueRange) {
                $sheetName = $sheetNames[$index];
                $values = $valueRange->getValues();
                if (empty($values)) continue;

                // Si es la primera hoja con datos, guardamos su cabecera
                if ($headers === null) {
                    $headers = array_shift($values);
                    $originalHeaderCount = count($headers);
                    $headers[] = 'Hoja Origen';
                    $allData[] = $headers;
                } else {
                    // Omitimos la primera fila (cabecera) de las siguientes hojas
                    array_shift($values);
                }

                // Agregamos todas las filas de la hoja actual
                foreach ($values as $row) {
                    // Rellenar la fila si tiene menos columnas que la cabecera original
                    $paddedRow = array_pad($row, $originalHeaderCount, '');
                    $paddedRow[] = $sheetName;
                    $allData[] = $paddedRow;
                }
            }

            return $allData;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
