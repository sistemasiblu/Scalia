<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\CollectionStdClass;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\clasificacionCRMRequest;

use Session;


class ClasificacionCRMController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('clasificacioncrmgrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $grupoestado=\App\GrupoEstado::lists('nombreGrupoEstado','idGrupoEstado');

        return view('clasificacioncrm',compact('grupoestado'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(clasificacionCRMRequest $request)
    {

        \App\ClasificacionCRM::create(
        [
            'codigoClasificacionCRM'=>$request['codigoClasificacionCRM'],
            'nombreClasificacionCRM'=>$request['nombreClasificacionCRM'],
            'GrupoEstado_idGrupoEstado'=>$request['GrupoEstado_idGrupoEstado'],
            'Compania_idCompania'=>Session::get('idCompania'),
        ]);

        $clasificacionultimo = \App\ClasificacionCRM::All()->last();
       
        for ($i=0 ; $i < count($request['idClasificacionCRMDetalle']); $i++)
        {

        \App\ClasificacionCRMDetalle::create(
        [
           'codigoClasificacionCRMDetalle'=>$request['codigoClasificacionCRMDetalle'][$i],
           'nombreClasificacionCRMDetalle'=>$request['nombreClasificacionCRMDetalle'][$i],
           'ClasificacionCRM_idClasificacionCRM'=>$clasificacionultimo->idClasificacionCRM,
        ]); 

        }

        return redirect('/clasificacioncrm');
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
    
    $grupoestado=\App\GrupoEstado::lists('nombreGrupoEstado','idGrupoEstado');
    $clasificacioncrm = \App\ClasificacionCRM::find($id);

    return view('clasificacioncrm',compact('clasificacioncrm','grupoestado'));
    return redirect('/clasificacioncrm');
    
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
   
     */

    public function update(clasificacionCRMRequest $request, $id)
    {
    
        $clasificacioncrm=\App\ClasificacionCRM::find($id);
        $clasificacioncrm->fill($request->all());
        $clasificacioncrm->save();

        $idssubClasEliminar = explode(',', $request['clasificacioncrmDetalleEliminar']);
        \App\ClasificacionCRMDetalle::whereIn('idClasificacionCRMDetalle',$idssubClasEliminar)->delete();

        for ($i=0 ; $i < count($request['idClasificacionCRMDetalle']); $i++)
        {
            $indice = array
            (
                'idClasificacionCRMDetalle' => $request['idClasificacionCRMDetalle'][$i]
            );

            $data = array
            (
                'codigoClasificacionCRMDetalle'=>$request['codigoClasificacionCRMDetalle'][$i],
                'nombreClasificacionCRMDetalle'=>$request['nombreClasificacionCRMDetalle'][$i],
                'ClasificacionCRM_idClasificacionCRM'=>$id,
            );

            $respuesta = \App\ClasificacionCRMDetalle::updateorcreate($indice, $data);
        } 
           
        return redirect('/clasificacioncrm');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\ClasificacionCRM::destroy($id);
        return redirect('/clasificacioncrm');
    }
}
