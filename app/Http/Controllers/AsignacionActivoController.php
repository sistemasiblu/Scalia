<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
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

         //$idTer= DB::select("select  idTercero from $compania.Tercero where tipoTercero like '*05*'");
        //$nombreTer= DB::select("select  nombre1Tercero from $compania.Tercero where tipoTercero like '*05*'");
        //$nombreTer= DB::table($compania.".Tercero")->where('tipoTercero','like','*05*')->lists('nombre1Tercero');
  /* $idTercero=Array();
       for ($i=0 ; $i < count( $idTer); $i++) 
    {  
        $idTercero[] = get_object_vars($idTer[$i]);
    }
    */
    /*$nombreTercero=Array();
     for ($i=0 ; $i < count( $nombreTer); $i++) 
    {  
        $nombreTercero[] = get_object_vars($nombreTer[$i]);
    }*/



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
