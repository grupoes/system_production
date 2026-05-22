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
}
