<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use DB;
use redirect;

class ProgramacionMantenimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('programacionmantenimientogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        
        $tipoaccion=\App\TipoAccion::lists('nombreTipoAccion','idTipoAccion');
        $tipoactivo=\App\TipoActivo::lists('nombreTipoActivo','idTipoActivo');
        $protmantenimiento=\App\ProtocoloMantenimiento::lists('nombreProtocoloMantenimiento','idProtocoloMantenimiento');
        $localizacion=\App\Localizacion::lists('nombreLocalizacion','idLocalizacion');


        return view('programacionmantenimiento', compact('tipoaccion','tiposervicio','protmantenimiento','localizacion','tipoactivo'));
        


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        \App\ProgramacionMantenimiento::create(
        [

        'ProtocoloMantenimiento_idProtocoloMantenimiento'=>$request['ProtocoloMantenimiento_idProtocoloMantenimiento'],
        'TipoActivo_idTipoActivo'=>$request['TipoActivo_idTipoActivo'], 
        'TipoAccion_idTipoAccion'=>$request['TipoAccion_idTipoAccion'],
        'Dependencia_idDependencia'=>$request['Dependencia_idDependencia'],
        'fechaInicialProgramacionMantenimiento'=>$request['fechaInicialProgramacionMantenimiento'],
        'fechaMaximaProgramacionMantenimiento'=>$request['fechaMaximaProgramacionMantenimiento'],
        'Compania_idCompania'=>Session::get('idCompania'),

        ]);
        
   
       

        

       

       return Redirect('/programacionmantenimiento');
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
        /* $pmantenimiento=DB::Select(
        "select 
       idProgramacionMantenimiento, ProtocoloMantenimiento_idProtocoloMantenimiento, TipoActivo_idTipoActivo, TipoAccion_idTipoAccion, Dependencia_idDependencia, fechaInicialProgramacionMantenimiento, fechaMaximaProgramacionMantenimiento from programacionmantenimiento where idProgramacionMantenimiento".$id);
         for ($i=0 ; $i < count( $pmantenimiento); $i++) 
    {  
    }*/


        $prmantenimiento = \App\ProgramacionMantenimiento::find($id);
        $tipoaccion=\App\TipoAccion::lists('nombreTipoAccion','idTipoAccion');
        $tipoactivo=\App\TipoActivo::lists('nombreTipoActivo','idTipoActivo');
        $protmantenimiento=\App\ProtocoloMantenimiento::lists('nombreProtocoloMantenimiento','idProtocoloMantenimiento');
        $localizacion=\App\Localizacion::lists('nombreLocalizacion','idLocalizacion');


       
        return view('programacionmantenimiento', compact('prmantenimiento','tipoaccion','tiposervicio','protmantenimiento','localizacion','tipoactivo'));

        return Redirect('/planmantenimiento');

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
        $activo=\App\ProgramacionMantenimiento::find($id);
        $activo->fill($request->all());
        $activo->save();

         return redirect('/programacionmantenimiento');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\ProgramacionMantenimiento::destroy($id);
        return redirect('/programacionmantenimiento');
    }
}
