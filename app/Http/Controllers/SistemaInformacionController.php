<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\SistemaInformacionRequest;
use App\Http\Controllers\Controller;
use Config;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class SistemaInformacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);

        if($datos != null)
            return view('sistemainformaciongrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sistemainformacion');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SistemaInformacionRequest $request)
    {

        \App\SistemaInformacion::create([
            'codigoSistemaInformacion' => $request['codigoSistemaInformacion'],
            'nombreSistemaInformacion' => $request['nombreSistemaInformacion'],
            'webSistemaInformacion' => isset($request['webSistemaInformacion']) ? 1 : 0,
            'ipSistemaInformacion' => $request['ipSistemaInformacion'],
            'puertoSistemaInformacion' => $request['puertoSistemaInformacion'],
            'usuarioSistemaInformacion' => $request['usuarioSistemaInformacion'],
            'claveSistemaInformacion' => $request['claveSistemaInformacion'],
            'bdSistemaInformacion' => $request['bdSistemaInformacion'],
            'motorbdSistemaInformacion' => $request['motorbdSistemaInformacion']
            ]);

        return redirect('/sistemainformacion');
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
        $sistemainformacion = \App\SistemaInformacion::find($id);
        return view ('sistemainformacion',['sistemainformacion'=>$sistemainformacion]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SistemaInformacionRequest $request, $id)
    {
        $sistemainformacion = \App\SistemaInformacion::find($id);
        $sistemainformacion->fill($request->all());
        $sistemainformacion->webSistemaInformacion = isset($request['webSistemaInformacion']) ? 1 : 0;
        $sistemainformacion->save();


        return redirect('/sistemainformacion');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\SistemaInformacion::destroy($id);
        return redirect('/sistemainformacion');
    }
}
