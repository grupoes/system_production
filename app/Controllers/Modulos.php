<?php

namespace App\Controllers;

class Modulos extends BaseController
{
    public function index(): string
    {
        return view('modulos/index');
    }
}
