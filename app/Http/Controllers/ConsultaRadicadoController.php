<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ConsultaRadicadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dependencia = \App\Dependencia::All()->lists('nombreDependencia','idDependencia');
        $serie = \App\Serie::All()->lists('nombreSerie','idSerie');
        $subserie = \App\SubSerie::All()->lists('nombreSubSerie','idSubSerie');
        $documento = \App\Documento::where('tipoDocumento', "=", 2)->lists('nombreDocumento','idDocumento');     
        return view('consultaradicado',compact('dependencia','serie','subserie','documento'));        
    }

    public function indexEtiquetaGridConsulta()
    {
        return view('etiquetagridselect');
    }

    public function indexGridMetadatos()
    {
        return view('gridMetadatosConsulta');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**x
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
