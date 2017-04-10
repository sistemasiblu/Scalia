<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\SubSerieRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class SubSerieController extends Controller
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
            return view('subseriegrid', compact('datos'));
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
        $serie = \App\Serie::All()->lists('nombreSerie','idSerie');
        $idDocumento = \App\Documento::All()->lists('idDocumento');
        $nombreDocumento = \App\Documento::All()->lists('nombreDocumento');
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');

        return view('subserie',compact('serie','idDocumento','nombreDocumento','idRol','nombreRol'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubSerieRequest $request)
    {
        if($request['respuesta'] != 'falso')
        {
            \App\SubSerie::create([
            'codigoSubSerie' => $request['codigoSubSerie'],
            'nombreSubSerie' => $request['nombreSubSerie'],
            'directorioSubSerie' => $request['directorioSubSerie'],
            'Serie_idSerie' => $request['Serie_idSerie'],
            'Compania_idCompania' => \Session::get('idCompania'),
            ]);

            $subserie = \App\SubSerie::All()->last();
            for($i = 0; $i < count($request['Documento_idDocumento']); $i++)
            {
                \App\SubSerieDetalle::create([
                'SubSerie_idSubSerie' => $subserie->idSubSerie,
                'Documento_idDocumento' => $request['Documento_idDocumento'][$i],
                ]);
            }

            for($i = 0; $i < count($request['Rol_idRol']); $i++)
            {
                \App\SubSeriePermiso::create([
                'SubSerie_idSubSerie' => $subserie->idSubSerie,
                'Rol_idRol' => $request['Rol_idRol'][$i],
                ]);
            }
            return redirect('/subserie');
        }
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
        $subserie = \App\SubSerie::find($id);
        $serie = \App\Serie::All()->lists('nombreSerie','idSerie');
        $idDocumento = \App\Documento::All()->lists('idDocumento');
        $nombreDocumento = \App\Documento::All()->lists('nombreDocumento');
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');

        return view('subserie',compact('serie','idDocumento','nombreDocumento','idRol','nombreRol'), ['subserie' => $subserie]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubSerieRequest $request, $id)
    {
        if($request['respuesta'] != 'falso')
        {
            $subserie = \App\SubSerie::find($id);
            $subserie->fill($request->all());
            $subserie->save();

            $idsEliminar = explode(',', $request['eliminarSubSerieDetalle']);
            \App\SubSerieDetalle::whereIn('idSubSerieDetalle',$idsEliminar)->delete();
            for($i = 0; $i < count($request['Documento_idDocumento']); $i++)
            {
                $indice = array(
                    'idSubSerieDetalle' => $request['idSubSerieDetalle'][$i]);

                $datos= array(
                    'SubSerie_idSubSerie' => $subserie->idSubSerie,
                    'Documento_idDocumento' => $request['Documento_idDocumento'][$i]
                    );

                $guardar = \App\SubSerieDetalle::updateOrCreate($indice, $datos);
            }

            $idsEliminar = explode(',', $request['eliminarSubSeriePermiso']);
            \App\SubSeriePermiso::whereIn('idSubSeriePermiso',$idsEliminar)->delete();
            for($i = 0; $i < count($request['Rol_idRol']); $i++)
            {
                $indice = array(
                    'idSubSeriePermiso' => $request['idSubSeriePermiso'][$i]);

                $datos= array(
                    'SubSerie_idSubSerie' => $subserie->idSubSerie,
                    'Rol_idRol' => $request['Rol_idRol'][$i]
                    );

                $guardar = \App\SubSeriePermiso::updateOrCreate($indice, $datos);
            }

            return redirect('/subserie');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\SubSerie::destroy($id);
        return redirect('/subserie');
    }
}
