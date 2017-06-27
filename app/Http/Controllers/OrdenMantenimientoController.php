<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class OrdenMantenimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        


        return view('ordenmantenimientogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $compania=\Session::get("nombreCompania");
        $tipoaccion=\App\TipoAccion::lists('nombreTipoAccion','idTipoAccion');
        $tiposervicio=\App\TipoServicio::lists('nombreTipoServicio','idTipoServicio');
        $localizacion=\App\localizacion::lists('nombrelocalizacion','idlocalizacion');
        $protocolo=\App\ProtocoloMantenimiento::lists('nombreProtocoloMantenimiento','idProtocoloMantenimiento');
        $tercero= DB::table($compania.".Tercero")->lists('nombre1Tercero','idTercero');


        return view('ordenmantenimiento',compact('tipoaccion','tiposervicio','localizacion','protocolo','tercero'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\OrdenMantenimiento::create(
    [
        
   
    'ProgramacionMantenimiento_idProgramacionMantenimiento'=>'null',
    'numeroOrdenMantenimiento'=>$request['numeroOrdenMantenimiento'],
    'fechaElaboracionOrdenMantenimiento'=>$request['fechaElaboracionOrdenMantenimiento'],
    'asuntoOrdenMantenimiento'=>$request['asuntoOrdenMantenimiento'],
    'urlOrdenMantenimiento'=>'null',
    'fechaHoraInicioOrdenMantenimiento'=>$request['fechaHoraInicioOrdenMantenimiento'],
    'fechaHoraFinOrdenMantenimiento'=>$request['fechaHoraFinOrdenMantenimiento'],
    'Localización_idLocalización'=>$request['Localización_idLocalización'],
    'ProtocoloMantenimiento_idProtocoloMantenimiento'=>$request['ProtocoloMantenimiento_idProtocoloMantenimiento'],
    'TipoAccion_idTipoAccion'=>$request['TipoAccion_idTipoAccion'],
    'TipoServicio_idTipoServicio'=>$request['TipoServicio_idTipoServicio'],
    'Tercero_idProveedor'=>$request['Tercero_idProveedor'],
    'estadoOrdenMantenimiento'=>$request['estadoOrdenMantenimiento'],
    'observacionOrdenMantenimiento'=>$request['observacionOrdenMantenimiento']

    ]);
    
  
    /*$ordenMultimo = \App\OrdenMantenimiento::All()->last();


    for ($i=0 ; $i < count($request['idOrdenMantenimientoDetalle']); $i++)
    {

        \App\OrdenMantenimientoDetalle::create(
        [
        
        'OrdenMantenimiento_idOrdenMantenimiento'=>$asignacionultimo->idOrdenMantenimiento,
        'MovimientoActivo_idMovimientoActivo'=>$request['MovimientoActivo_idMovimientoActivo'][$i],
        'Activo_idActivo'=>$request['Activo_idActivo'][$i],
        'Localizacion_idLocalizacion'=>$request['idLocalizacion'][$i],
        'Tercero_idResponsable'=>$request['Tercero_idResponsable'][$i],

        ]); 

       

    }
*/
    return redirect('/ordenmantenimiento');
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
        $compania=\Session::get("nombreCompania");

        $ordenmantenimiento = \App\OrdenMantenimiento::find($id);
        $tipoaccion=\App\TipoAccion::lists('nombreTipoAccion','idTipoAccion');
        $tiposervicio=\App\TipoServicio::lists('nombreTipoServicio','idTipoServicio');
        $localizacion=\App\localizacion::lists('nombrelocalizacion','idlocalizacion');
        $protocolo=\App\ProtocoloMantenimiento::lists('nombreProtocoloMantenimiento','idProtocoloMantenimiento');
        $tercero= DB::table($compania.".Tercero")->lists('nombre1Tercero','idTercero');


        return view('ordenmantenimiento',compact('ordenmantenimiento','tipoaccion','tiposervicio','localizacion','protocolo','tercero'));
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
        $ordenmantenimiento = \App\OrdenMantenimiento::find($id);
        $ordenmantenimiento->fill($request->all());
        $ordenmantenimiento->save();

         
        $idsdetalleEliminar = explode(',', $request['detalleEliminar']);
      
        \App\OrdenMantenimientoDetalle::whereIn('idOrdenMantenimientoDetalle',$idsdetalleEliminar)->delete();
        for ($i=0 ; $i < count($request['idOrdenMantenimientoDetalle']); $i++)
        {
        
        $indice = array(
        'idOrdenMantenimientoDetalle' => $request['idOrdenMantenimientoDetalle'][$i]);

        $data = array
        (

        'OrdenMantenimiento_idOrdenMantenimiento'=>$id,
        'MovimientoActivo_idMovimientoActivo'=>$request['MovimientoActivo_idMovimientoActivo'][$i],
        'Activo_idActivo'=>$request['Activo_idActivo'][$i],
        'Localizacion_idLocalizacion'=>$request['idLocalizacion'][$i],
        'Tercero_idResponsable'=>$request['Tercero_idResponsable'][$i],

        );

        
              
        $respuesta = \App\OrdenMantenimientoDetalle::updateorcreate($indice, $data);
        }

         return redirect('/ordenmantenimiento');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\OrdenMantenimiento::destroy($id);
        return redirect('/ordenmantenimiento');
    }


    public function llamarActivos()
{
    $localizacion = (isset($_GET['idLocalizacion']) ? $_GET['idLocalizacion'] : 0);
    $protocolo = (isset($_GET['idProtocolo']) ? $_GET['idProtocolo'] : 0);

    $datos = DB::select
    (
        "SELECT idActivo,codigoActivo,nombreActivo,Localizacion_idLocalizacion
        FROM inventarioactivo
        inner join activo
        on inventarioactivo.Activo_idActivo=activo.idActivo
        inner join tipoactivo
        on activo.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo
        WHERE saldoFinalInventarioActivo>0 and TipoActivo.idTipoActivo= 
            (SELECT  TipoActivo_idTipoActivo 
            from ProtocoloMantenimiento
            where idProtocoloMantenimiento = $protocolo)
        and inventarioActivo.Localizacion_idLocalizacion=$localizacion"
            
    );

    $informe = array();
    for($i = 0; $i < count($datos); $i++) 
    {
        $informe[] = get_object_vars($datos[$i]);
    }

    echo json_encode($informe);

}

}
