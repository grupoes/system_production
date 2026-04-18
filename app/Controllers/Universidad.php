<?php

namespace App\Controllers;

class Universidad extends BaseController
{
    public function index(): string
    {
        return view('universidad/index');
    }
}
