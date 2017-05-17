<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UbicacionDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('ubicaciondocumentogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $dependencialocalizacion = \App\DependenciaLocalizacion::All()->lists()
        $tiposoporte = \App\TipoSoporteDocumental::All()->lists('nombreTipoSoporteDocumental','idTipoSoporteDocumental');
        $compania = \App\Compania::All()->lists('nombreCompania','idCompania');
        $dependenciaproductora = \App\Dependencia::All()->lists('nombreDependencia','idDependencia');
        return view('ubicaciondocumento',compact('tiposoporte','compania','dependenciaproductora'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\UbicacionDocumento::create([
            'tipoUbicacionDocumento' => $request['tipoUbicacionDocumento'],
            'DependenciaLocalizacion_idDependenciaLocalizacion' => $request['DependenciaLocalizacion_idDependenciaLocalizacion'],
            'posicionUbicacionDocumento' => $request['posicionUbicacionDocumento'],
            'numeroLegajoUbicacionDocumento' => $request['numeroLegajoUbicacionDocumento'],
            'numeroFolioUbicacionDocumento' => $request['numeroFolioUbicacionDocumento'],
            'descripcionUbicacionDocumento' => $request['descripcionUbicacionDocumento'],
            'Tercero_idTercero' => $request['Tercero_idTercero'],
            'fechaInicialUbicacionDocumento' => $request['fechaInicialUbicacionDocumento'],
            'fechaFinalUbicacionDocumento' => $request['fechaFinalUbicacionDocumento'],
            'TipoSoporteDocumental_idTipoSoporteDocumental' => $request['TipoSoporteDocumental_idTipoSoporteDocumental'],
            'Dependencia_idProductora' => $request['Dependencia_idProductora'],
            'Compania_idCompania' => $request['Compania_idCompania'],
            'observacionUbicacionDocumento' => $request['observacionUbicacionDocumento'],
            'estadoUbicacionDocumento' => $request['estadoUbicacionDocumento']
        ]);

        return redirect('ubicaciondocumento');
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
        $ubicaciondocumento = \App\UbicacionDocumento::find($id);
        $tiposoporte = \App\TipoSoporteDocumental::All()->lists('nombreTipoSoporteDocumental','idTipoSoporteDocumental');
        $compania = \App\Compania::All()->lists('nombreCompania','idCompania');
        $dependenciaproductora = \App\Dependencia::All()->lists('nombreDependencia','idDependencia');
        return view('ubicaciondocumento',compact('tiposoporte','compania','dependenciaproductora'), ['ubicaciondocumento' => $ubicaciondocumento]);
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
        $ubicaciondocumento = \App\UbicacionDocumento::find($id);
        $ubicaciondocumento -> fill($request->all());
        $ubicaciondocumento -> save();

        return redirect('ubicaciondocumento');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\UbicacionDocumento::destroy($id);
        return redirect('ubicaciondocumento');
    }
}
