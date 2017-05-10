<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\TipoActivoRequest;
use App\Http\Controllers\Controller;

class TipoActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tipoactivogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tipoactivo');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoActivoRequest $request)
    {

        \App\TipoActivo::create(
        [
            'codigoTipoActivo'=>$request['codigoTipoActivo'],
            'nombreTipoActivo'=>$request['nombreTipoActivo'],
        ]);

        $tipoactivocaracteristica = \App\TipoActivo::All()->last();

         
        for ($i=0 ; $i < count($request['idTipoActivoCaracteristica']); $i++)
        {
            \App\TipoActivoCaracteristica::create(
            [
                'TipoActivo_idTipoActivo'=>$tipoactivocaracteristica->idTipoActivo,
                'nombreTipoActivoCaracteristica'=>$request['nombreTipoActivoCaracteristica'][$i],
            ]); 
        }

        for ($i=0 ; $i < count($request['idTipoActivoDocumento']); $i++)
        {
            \App\TipoActivoDocumento::create(
            [
                'TipoActivo_idTipoActivo'=>$tipoactivocaracteristica->idTipoActivo,
                'descripcionTipoActivoDocumento'=>$request['descripcionTipoActivoDocumento'][$i],
                'serialTipoActivoDocumento'=>$request['serialTipoActivoDocumento'][$i],
                'tipoTipoActivoDocumento'=>$request['tipoTipoActivoDocumento'][$i],
                'vigenciaTipoActivoDocumento'=>$request['vigenciaTipoActivoDocumento'][$i],
                'costoTipoActivoDocumento'=>$request['costoTipoActivoDocumento'][$i],
            ]); 

        }
        return Redirect('/tipoactivo');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $tipoactivo=\App\TipoActivo::find($id);
       return view('tipoactivo',['tipoactivo'=>$tipoactivo]);
       return redirect('/tipoactivo');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoActivoRequest $request, $id)
    {
        $tipoactivo=\App\TipoActivo::find($id);
        $tipoactivo->fill($request->all());
        $tipoactivo->save();
      
        $idsCaracteristicaEliminar = explode(',', $request['caracteristicaEliminar']);
        \App\TipoActivoCaracteristica::whereIn('idTipoActivoCaracteristica',$idsCaracteristicaEliminar)->delete();
        for ($i=0 ; $i < count($request['idTipoActivoCaracteristica']); $i++)
        {
             $indice = array(
                'idTipoActivoCaracteristica' => $request['idTipoActivoCaracteristica'][$i]);

             $data = array(
                'TipoActivo_idTipoActivo' => $id, 
                'nombreTipoActivoCaracteristica' => $request['nombreTipoActivoCaracteristica'][$i]);

             $respuesta = \App\TipoActivoCaracteristica::updateorcreate($indice, $data);
        }

        $idsDocumentoEliminar = explode(',', $request['documentoEliminar']);
        \App\TipoActivoDocumento::whereIn('idTipoActivoDocumento',$idsDocumentoEliminar)->delete();

        for ($i=0 ; $i < count($request['idTipoActivoDocumento']); $i++)
        {
             $indice = array(
                'idTipoActivoDocumento' => $request['idTipoActivoDocumento'][$i]);

             $data = array(
                'TipoActivo_idTipoActivo' => $id, 
                'descripcionTipoActivoDocumento'=>$request['descripcionTipoActivoDocumento'][$i],
                 'tipoTipoActivoDocumento'=>$request['tipoTipoActivoDocumento'][$i],
                'vigenciaTipoActivoDocumento'=>$request['vigenciaTipoActivoDocumento'][$i],
                'costoTipoActivoDocumento'=>$request['costoTipoActivoDocumento'][$i]);
             $respuesta = \App\TipoActivoDocumento::updateorcreate($indice, $data);
        }
        return redirect('/tipoactivo');
 }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { 
        \App\TipoActivo::destroy($id);
        return redirect('/tipoactivo');
    }


    

}
