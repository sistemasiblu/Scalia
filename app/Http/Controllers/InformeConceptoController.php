<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class InformeConceptoController extends Controller
{
    
    public function indexInformeConceptoGrid()
    {
        return view('informeconceptogridselect'); 
    }

}
