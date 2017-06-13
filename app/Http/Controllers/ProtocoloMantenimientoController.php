<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use redirect;

class ProtocoloMantenimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('protocolomantenimientogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tipoactivo=\App\TipoActivo::lists('nombreTipoActivo','idTipoActivo');
        $tipoaccion=\App\TipoAccion::lists('nombreTipoAccion','idTipoAccion');
        $idFrecuencia=\App\FrecuenciaMedicion::lists('idFrecuenciaMedicion');
        $nombreFrecuencia=\App\FrecuenciaMedicion::lists('nombreFrecuenciaMedicion');
        $idTipoServicio=\App\tiposervicio::lists('idTipoServicio');
        $nombreTipoServicio=\App\tiposervicio::lists('nombreTipoServicio');

        return view('protocolomantenimiento',compact('tipoactivo','tipoaccion','idFrecuencia','nombreFrecuencia','idTipoServicio','nombreTipoServicio'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    \App\ProtocoloMantenimiento::create(
    [
        
   
    'nombreProtocoloMantenimiento'=>$request['nombreProtocoloMantenimiento'],
    'TipoActivo_idTipoActivo'=>$request['TipoActivo_idTipoActivo'],
    'TipoAccion_idTipoAccion'=>$request['TipoAccion_idTipoAccion'],

    ]);
    
  
    $protocolooultimo = \App\ProtocoloMantenimiento::All()->last();

   

    for ($i=0 ; $i < count($request['idProtocoloMantenimientoTarea']); $i++)
    {

        \App\ProtocoloMantenimientoTarea::create([
        
        'ProtocoloMantenimiento_idProtocoloMantenimiento'=>$protocolooultimo->idProtocoloMantenimiento,
        'descripcionProtocoloMantenimientoTarea'=>$request['descripcionProtocoloMantenimientoTarea'][$i],
        'minutosProtocoloMantenimientoTarea'=>$request['minutosProtocoloMantenimientoTarea'][$i],
        'FrecuenciaMedicion_idFrecuenciaMedicion'=>$request['FrecuenciaMedicion_idFrecuenciaMedicion'][$i],
        'TipoServicio_idTipoServicio'=>$request['TipoServicio_idTipoServicio'][$i],
        'requiereParoProtocoloMantenimientoTarea'=>$request['requiereParoProtocoloMantenimientoTarea'][$i],

         ]); 


       

    }

    return redirect('/protocolomantenimiento');

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

       $protocolomantenimiento=\App\ProtocoloMantenimiento::find($id);
       $protMant=DB::SELECT("Select idProtocoloMantenimientoTarea, ProtocoloMantenimiento_idProtocoloMantenimiento, 
descripcionProtocoloMantenimientoTarea, minutosProtocoloMantenimientoTarea, 
FrecuenciaMedicion_idFrecuenciaMedicion, TipoServicio_idTipoServicio, requiereParoProtocoloMantenimientoTarea
        from protocolomantenimiento
        inner join protocolomantenimientotarea
        on protocolomantenimientotarea.ProtocoloMantenimiento_idProtocoloMantenimiento=protocolomantenimiento.idProtocoloMantenimiento
        inner join frecuenciamedicion
        on protocolomantenimientotarea.FrecuenciaMedicion_idFrecuenciaMedicion=frecuenciamedicion.idFrecuenciaMedicion
        inner join tiposervicio
        on protocolomantenimientotarea.TipoServicio_idTipoServicio=tiposervicio.idTipoServicio
        where idProtocoloMantenimiento=$id");
      

       $protMantTarea= array();
    for($i = 0; $i < count($protMant); $i++) 
    {
      $protMantTarea[] = get_object_vars($protMant[$i]);
    }


       $tipoactivo=\App\TipoActivo::lists('nombreTipoActivo','idTipoActivo');
       $tipoaccion=\App\TipoAccion::lists('nombreTipoAccion','idTipoAccion');
       $idFrecuencia=\App\FrecuenciaMedicion::lists('idFrecuenciaMedicion');
        $nombreFrecuencia=\App\FrecuenciaMedicion::lists('nombreFrecuenciaMedicion');
        $idTipoServicio=\App\tiposervicio::lists('idTipoServicio');
        $nombreTipoServicio=\App\tiposervicio::lists('nombreTipoServicio');

    return view('protocolomantenimiento',compact('protocolomantenimiento','tipoactivo','tipoaccion','protMantTarea','idFrecuencia','nombreFrecuencia','idTipoServicio','nombreTipoServicio'));
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
        $protocolomantenimiento = \App\ProtocoloMantenimiento::find($id);
        $protocolomantenimiento->fill($request->all());
        $protocolomantenimiento->save();


        $idsprotocoloEliminar = explode(',', $request['protocoloEliminar']);
       
        \App\ProtocoloMantenimientoTarea::whereIn('idProtocoloMantenimientoTarea',$idsprotocoloEliminar)->delete();
        for ($i=0 ; $i < count($request['idProtocoloMantenimientoTarea']); $i++)
        {
           $indice = array(
            'idProtocoloMantenimientoTarea' => $request['idProtocoloMantenimientoTarea'][$i]);

           $data = array(
         'ProtocoloMantenimiento_idProtocoloMantenimiento'=>$id,
        'descripcionProtocoloMantenimientoTarea'=>$request['descripcionProtocoloMantenimientoTarea'][$i],
        'minutosProtocoloMantenimientoTarea'=>$request['minutosProtocoloMantenimientoTarea'][$i],
        'FrecuenciaMedicion_idFrecuenciaMedicion'=>$request['FrecuenciaMedicion_idFrecuenciaMedicion'][$i],
        'TipoServicio_idTipoServicio'=>$request['TipoServicio_idTipoServicio'][$i],
        'requiereParoProtocoloMantenimientoTarea'=>$request['requiereParoProtocoloMantenimientoTarea'][$i],

            );

        
              
        $respuesta = \App\ProtocoloMantenimientoTarea::updateorcreate($indice, $data);
        }

            return redirect('/protocolomantenimiento');

    }
          


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        \App\ProtocoloMantenimiento::destroy($id);
        return redirect('/protocolomantenimiento');

    }
}
