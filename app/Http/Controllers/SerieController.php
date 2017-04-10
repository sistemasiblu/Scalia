<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\SerieRequest;
use App\Http\Controllers\Controller;
use File;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class SerieController extends Controller
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
            return view('seriegrid', compact('datos'));
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
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');

        return view('serie',compact('idRol','nombreRol'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SerieRequest $request)
    {
        \App\Serie::create([
        'codigoSerie' => $request['codigoSerie'],
        'nombreSerie' => $request['nombreSerie'],
        'directorioSerie' => $request['directorioSerie'],
        'Compania_idCompania' => \Session::get("idCompania")
        ]);

        $serie = \App\Serie::All()->last();
        for($i = 0; $i < count($request['Rol_idRol']); $i++)
        {
            \App\SeriePermiso::create([
            'Serie_idSerie' => $serie->idSerie,
            'Rol_idRol' => $request['Rol_idRol'][$i],
            ]);
        }

        return redirect('/serie');
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
        $serie = \App\Serie::find($id);
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');
        return view('serie',compact('idRol','nombreRol'), ['serie' => $serie]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SerieRequest $request, $id)
    {
        $serie = \App\Serie::find($id);
        $serie->fill($request->all());
        $serie->save();

        $idsEliminar = explode(',', $request['eliminarSeriePermiso']);
        \App\SeriePermiso::whereIn('idSeriePermiso',$idsEliminar)->delete();
        for($i = 0; $i < count($request['Rol_idRol']); $i++)
        {
            $indice = array(
                'idSeriePermiso' => $request['idSeriePermiso'][$i]);

            $datos= array(
                'Serie_idSerie' => $serie->idSerie,
                'Rol_idRol' => $request['Rol_idRol'][$i]
                );

            $guardar = \App\SeriePermiso::updateOrCreate($indice, $datos);
        }

        return redirect('/serie');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Serie::destroy($id);
        return redirect('/serie');
    }
}
