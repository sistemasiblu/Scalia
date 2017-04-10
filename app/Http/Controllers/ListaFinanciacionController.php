<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class ListaFinanciacionController extends Controller
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
            return view('listafinanciaciongrid', compact('datos'));
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
        return view('listafinanciacion');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\ListaFinanciacion::create([
        'codigoListaFinanciacion' => $request['codigoListaFinanciacion'],
        'nombreListaFinanciacion' => $request['nombreListaFinanciacion'],
        'codigoSayaListaFinanciacion' => $request['codigoSayaListaFinanciacion'],
        'tipoListaFinanciacion' => $request['tipoListaFinanciacion']
        ]);

        return redirect('/listafinanciacion');
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
        $listafinanciacion = \App\ListaFinanciacion::find($id);
        return view('listafinanciacion',['listafinanciacion' => $listafinanciacion]);
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
        $listafinanciacion = \App\ListaFinanciacion::find($id);
        $listafinanciacion->fill($request->all());
        $listafinanciacion->save();
        
        return redirect('/listafinanciacion');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\listafinanciacion::destroy($id);
        return redirect('/listafinanciacion');
    }
}
