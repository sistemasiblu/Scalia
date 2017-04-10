<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ConsultaRadicadoFiltroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $idDependencia = \App\Dependencia::All()->lists('idDependencia');
        $nombreDependencia = \App\Dependencia::All()->lists('nombreDependencia');
        $idDocumento = \App\Documento::where('tipoDocumento', "=", 2)->lists('idDocumento');
        $nombreDocumento = \App\Documento::where('tipoDocumento', "=", 2)->lists('nombreDocumento');
        $idMetadato = \App\Metadato::All()->lists('idMetadato');
        $tituloMetadato = \App\Metadato::All()->lists('tituloMetadato');
        
        return view('consultaradicadofiltro',compact('consultaradicadofiltro','nombreDependencia','idDependencia','nombreDocumento','idDocumento','idMetadato','tituloMetadato'));   
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

    /**
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
