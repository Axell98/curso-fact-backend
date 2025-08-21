<?php

namespace App\Http\Controllers\Sale;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Greenter\GreenterService;

class FacturacionEletronicaController extends Controller
{
    protected $greenter_service;
    public function __construct(GreenterService $greenter_service) {
        $this->greenter_service = $greenter_service;
    }
    
}
