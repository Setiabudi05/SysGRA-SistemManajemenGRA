<?php

namespace App\Http\Controllers;

// PASTIKAN SEMUA 'use' INI ADA
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // <-- INI YANG PENTING

// PASTIKAN 'extends BaseController'
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}