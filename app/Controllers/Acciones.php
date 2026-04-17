<?php

namespace App\Controllers;

class Acciones extends BaseController
{
    public function index(): string
    {
        return view('acciones/index');
    }

    public function configuracionAccionesModulos(): string
    {
        return view('acciones/configuracion-acciones-modulos');
    }
}
