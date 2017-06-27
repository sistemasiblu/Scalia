<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AsignacionActivoRequest;
use App\Http\Controllers\Controller;
use DB;

class AsignacionActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('asignacionactivogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {  
        $compania=\Session::get("nombreCompania");
        $idTercero= DB::table($compania.".Tercero")->where('tipoTercero','like','*05*')->lists('idTercero');
        $nombreTercero= DB::table($compania.".Tercero")->where('tipoTercero','like','*05*')->lists('nombre1Tercero');

    $idTercero=json_encode($idTercero);
    $nombreTercero=json_encode($nombreTercero);
   

    

        $transaccionactivo=\App\TransaccionActivo::lists('nombreTransaccionActivo','idTransaccionActivo');
        return view('asignacionactivo',compact('transaccionactivo','idTercero','nombreTercero'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AsignacionActivoRequest $request)
    {

    $numero = DB::select(
    "SELECT CONCAT(REPEAT('0', longitudTransaccionActivo - LENGTH(ultimo+1)), (ultimo+1)) as nuevo
    FROM 
    (
        SELECT IFNULL( MAX(numeroMovimientoActivo) , 0) as ultimo, longitudTransaccionActivo
        FROM  transaccionactivo T 
        LEFT JOIN movimientoactivo M
        on T.idTransaccionActivo = M.TransaccionActivo_idTransaccionActivo
        where   T.Compania_idCompania = ".\Session::get('idCompania')." and 
                TransaccionActivo_idTransaccionActivo = ".$request['TransaccionActivo_idTransaccionActivo']."
    ) temp");

    $numero = get_object_vars($numero[0])["nuevo"];
    
    \App\AsignacionActivo::create(
    [
        
    'numeroAsignacionActivo'=>$numero,
    'fechaHoraAsignacionActivo'=>$request['fechaHoraAsignacionActivo'],
    'TransaccionActivo_idTransaccionActivo'=>$request['TransaccionActivo_idTransaccionActivo'],
    'documentoInternoAsignacionActivo'=>$request['documentoInternoAsignacionActivo'],
    'Users_idCrea'=>$request['Users_idCrea'],

    ]);
    
  
    $asignacionultimo = \App\AsignacionActivo::All()->last();


    for ($i=0 ; $i < count($request['idAsignacionActivoDetalle']); $i++)
    {

        \App\AsignacionActivoDetalle::create(
        [
        
        'AsignacionActivo_idAsignacionActivo'=>$asignacionultimo->idAsignacionActivo,
        'MovimientoActivo_idMovimientoActivo'=>$request['MovimientoActivo_idMovimientoActivo'][$i],
        'Activo_idActivo'=>$request['Activo_idActivo'][$i],
        'Localizacion_idLocalizacion'=>$request['idLocalizacion'][$i],
        'Tercero_idResponsable'=>$request['Tercero_idResponsable'][$i],

        ]); 

       

    }

    return redirect('/asignacionactivo');




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
       
    $asignacionactivo = \App\AsignacionActivo::find($id);
    
    $compania=\Session::get("nombreCompania");
    $idTercero= DB::table($compania.".Tercero")->where('tipoTercero','like','*05*')->lists('idTercero');
    $nombreTercero= DB::table($compania.".Tercero")->where('tipoTercero','like','*05*')->lists('nombre1Tercero');

    $idTercero=json_encode($idTercero);
    $nombreTercero=json_encode($nombreTercero);
    $transaccionactivo=\App\TransaccionActivo::lists('nombreTransaccionActivo','idTransaccionActivo');

    $asignacionactivod = DB::select(
    "SELECT 
    idAsignacionActivoDetalle,
    AsignacionActivo_idAsignacionActivo,
    MovimientoActivo_idMovimientoActivo,
    Activo_idActivo,
    codigoActivo,
    serieActivo,
    nombreActivo,
    idLocalizacion,
    nombreLocalizacion,
    Tercero_idResponsable
    FROM asignacionactivodetalle AD
    INNER JOIN asignacionactivo 
      ON AD.AsignacionActivo_idAsignacionActivo = asignacionactivo.idAsignacionActivo
    INNER JOIN activo 
      ON AD.Activo_idActivo = activo.idActivo
    INNER JOIN localizacion 
      ON AD.Localizacion_idLocalizacion = localizacion.idLocalizacion
    WHERE
    AsignacionActivo_idAsignacionActivo =$id");

   
    $asignacionactivodetalle= array();
    for($i = 0; $i < count($asignacionactivod); $i++) 
    {
      $asignacionactivodetalle[] = get_object_vars($asignacionactivod[$i]);
    }

    return view('asignacionactivo',compact('transaccionactivo','idTercero','nombreTercero','asignacionactivo','asignacionactivodetalle'));
    

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AsignacionActivoRequest $request, $id)
    {
        $asignacionactivo = \App\AsignacionActivo::find($id);
        $asignacionactivo->fill($request->all());
        $asignacionactivo->save();

         
        $idsdetalleEliminar = explode(',', $request['detalleEliminar']);
      
        \App\AsignacionActivoDetalle::whereIn('idAsignacionActivoDetalle',$idsdetalleEliminar)->delete();
        for ($i=0 ; $i < count($request['idAsignacionActivoDetalle']); $i++)
        {
        
        $indice = array(
        'idAsignacionActivoDetalle' => $request['idAsignacionActivoDetalle'][$i]);

        $data = array
        (

        'AsignacionActivo_idAsignacionActivo'=>$id,
        'MovimientoActivo_idMovimientoActivo'=>$request['MovimientoActivo_idMovimientoActivo'][$i],
        'Activo_idActivo'=>$request['Activo_idActivo'][$i],
        'Localizacion_idLocalizacion'=>$request['idLocalizacion'][$i],
        'Tercero_idResponsable'=>$request['Tercero_idResponsable'][$i],

        );

        
              
        $respuesta = \App\AsignacionActivoDetalle::updateorcreate($indice, $data);
        }

         return redirect('/asignacionactivo');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\AsignacionActivo::destroy($id);
        return redirect('/asignacionactivo');

    }
}
