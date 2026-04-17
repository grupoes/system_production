<?php

namespace App\Controllers;

class Permisos extends BaseController
{
    public function index(): string
    {
        return view('permisos/index');
    }
}
