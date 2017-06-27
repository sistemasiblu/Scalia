<?php
namespace App\Http\Controllers;
use Illuminate\Support\CollectionStdClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Requests\activoRequest;
use App\Http\Controllers\Controller;

class ActivoController extends Controller
{
/**
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\Response
 */
public function index()
{
    return view('activogrid');
}

/**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
 */
public function create()
{

    $tipoactivo=\App\TipoActivo::lists('nombreTipoActivo','idTipoActivo');
    return view('activo',['tipoactivo'=>$tipoactivo]);
    
}
/**
 * Store a newly created resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */
public function store(activoRequest $request)
{
    \App\Activo::create(
    [

    'codigoActivo'=>$request['codigoActivo'],
    'nombreActivo'=>$request['nombreActivo'],
    'TipoActivo_idTipoActivo'=>$request['TipoActivo_idTipoActivo'],
    'codigobarraActivo'=>$request['codigobarraActivo'],
    'estadoActivo'=>$request['estadoActivo'],
    'clasificacionActivo'=>$request['clasificacionActivo'],
    'marcaActivo'=>$request['marcaActivo'],
    'serieActivo'=>$request['serieActivo'],
    'pesoActivo'=>$request['pesoActivo'],
    'altoActivo'=>$request['altoActivo'],
    'anchoActivo'=>$request['anchoActivo'],
    'modeloActivo'=>$request['modeloActivo'],
    'largoActivo'=>$request['largoActivo'],
    'volumenActivo'=>$request['volumenActivo'],

    ]);

    $activoultimo = \App\Activo::All()->last();

    echo count($request['idActivoParte']);
    for ($i=0 ; $i < count($request['idActivoParte']); $i++)
    {
        \App\ActivoParte::create([
         'Activo_idActivo'=>$activoultimo->idActivo,
         'Activo_idParte'=>$request['Activo_idParte'][$i],               
         ]); 

    }

    for ($i=0 ; $i < count($request['idActivoComponente']); $i++)
    {
        \App\ActivoComponente::create(
        [

        'Activo_idActivo'=>$activoultimo->idActivo,
        'Activo_idComponente'=>$request['Activo_idComponente'][$i],
        'cantidadActivoComponente' => $request['cantidadActivoComponente'][$i],
        ]); 

    }

    for ($i=0 ; $i < count($request['idActivoCaracteristica']); $i++)
    {
        \App\ActivoCaracteristica::create(
        [
        'Activo_idActivo'=>$activoultimo->idActivo,
        'TipoActivoCaracteristica_idTipoActivoCaracteristica'=>$request['idTipoActivoCaracteristica'][$i],
        'descripcionActivoCaracteristica'=>$request['descripcionActivoCaracteristica'][$i],

        ]); 

    }

    for ($i=0 ; $i < count($request['idActivoDocumento']); $i++)
    {
        \App\ActivoDocumento::create(
        [
        'Activo_idActivo'=>$activoultimo->idActivo,
        'TipoActivoDocumento_idTipoActivoDocumento'=>$request['idTipoActivoDocumento'][$i],
        'versionActivoDocumento'=>$request['versionActivoDocumento'][$i],
        'proveedorActivoDocumento'=>$request['proveedorActivoDocumento'][$i],
        'serialActivoDocumento'=>$request['serialActivoDocumento'][$i],
        'fechainicialActivoDocumento'=>$request['fechainicialActivoDocumento'][$i],
        
        ]); 

    }

        return Redirect('/activo');
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
    $actCaracteristica=DB::Select(
        "select 
        idActivoCaracteristica, nombreTipoActivoCaracteristica, descripcionActivoCaracteristica
        from activo 
        inner join tipoactivo 
        on activo.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo 
        inner join tipoactivocaracteristica 
        on tipoactivocaracteristica.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo 
        inner join activocaracteristica 
        on activocaracteristica.TipoActivoCaracteristica_idTipoActivoCaracteristica=tipoactivocaracteristica.idTipoActivoCaracteristica 
        where activo_idActivo=".$id);

    for ($i=0 ; $i < count( $actCaracteristica); $i++) 
    {  
        $activoCaracteristica[] = get_object_vars($actCaracteristica[$i]);
    }

    $actDocumento=DB::Select(
        "select 
        activodocumento.idActivoDocumento, tipoactivodocumento.descripcionTipoActivoDocumento,
        activodocumento.versionActivoDocumento, activodocumento.proveedorActivoDocumento,
        activodocumento.serialActivoDocumento, tipoactivodocumento.tipoTipoActivoDocumento,
        activodocumento.fechainicialActivoDocumento, tipoactivodocumento.costoTipoActivoDocumento
        from activo 
        inner join tipoactivo 
        on activo.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo 
        inner join tipoactivodocumento
        on tipoactivodocumento.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo 
        inner join activodocumento 
        on activodocumento.TipoActivoDocumento_idTipoActivoDocumento=tipoactivodocumento.idTipoActivoDocumento 
        where activo_idActivo=".$id);

    for ($i=0 ; $i < count( $actDocumento); $i++) 
    {  
        $activoDocumento[] = get_object_vars($actDocumento[$i]);
    }

    $actParte=DB::Select(
        "select 
        activoparte.idActivoParte,activoparte.Activo_idParte, activo.nombreActivo as nombreActivoParte
        from activoparte
       inner join activo
        on activoparte.Activo_idParte=activo.idActivo
        where Activo_idActivo=".$id);

    for ($i=0 ; $i < count( $actParte); $i++) 
    {  
        $activoParte[] = get_object_vars($actParte[$i]);
    }

    $actComponente=DB::Select(
        "select 
        activocomponente.idActivoComponente,  activocomponente.Activo_idComponente,activo.nombreActivo as nombreActivoComponente,
        activocomponente.cantidadActivoComponente
        from activo 
        inner join activocomponente 
        on activocomponente.Activo_idComponente=activo.idActivo 
        where activo_idActivo=".$id);

    for ($i=0 ; $i < count( $actComponente); $i++) 
    {  
        $activoComponente[] = get_object_vars($actComponente[$i]);
    }

    
    $tipoactivo=\App\TipoActivo::lists('nombreTipoActivo','IdTipoActivo');
    $activo = \App\Activo::find($id);


    return view('activo',['activo'=>$activo],compact('tipoactivo','activoCaracteristica','activoDocumento','activoParte','activoComponente'));
   

    return redirect('/activo');
}

/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\Response
 */
public function update(activoRequest $request, $id)
{

    $activo=\App\Activo::find($id);
    $activo->fill($request->all());
    $activo->save();

    $idsParteEliminar = explode(',', $request['parteEliminar']);
    \App\ActivoParte::whereIn('idActivoParte',$idsParteEliminar)->delete();
    for ($i=0 ; $i < count($request['idActivoParte']); $i++)
    {
       $indice = array(
        'idActivoParte' => $request['idActivoParte'][$i]);

       $data = array(
        'Activo_idActivo' => $id, 
        'Activo_idParte'=>$request['Activo_idParte'][$i],

        );

       $respuesta = \App\ActivoParte::updateorcreate($indice, $data);
    } 


    $idsComponenteEliminar = explode(',', $request['componenteEliminar']);
    \App\ActivoComponente::whereIn('idActivoComponente',$idsComponenteEliminar)->delete();
    for ($i=0 ; $i < count($request['idActivoComponente']); $i++)
    {
       $indice = array(
        'idActivoComponente' => $request['idActivoComponente'][$i]);

       $data = array(
        'Activo_idActivo' =>$id,
        'Activo_idComponente'=>$request['Activo_idComponente'][$i],
        'cantidadActivoComponente' => $request['cantidadActivoComponente'][$i]);
       $respuesta = \App\ActivoComponente::updateorcreate($indice, $data);
    }

    $idsCaracteristicaEliminar = explode(',', $request['caracteristicaEliminar']);
    \App\ActivoCaracteristica::whereIn('idActivoCaracteristica',$idsCaracteristicaEliminar)->delete();
    for ($i=0 ; $i < count($request['idActivoCaracteristica']); $i++)
    {
       $indice = array(
        'idActivoCaracteristica' => $request['idActivoCaracteristica'][$i]);

       $data = array(
        'Activo_idActivo' => $id, 
        'TipoActivoCaracteristica_idTipoActivoCaracteristica'=>$request['idTipoActivoCaracteristica'][$i],
        'descripcionActivoCaracteristica' => $request['descripcionActivoCaracteristica'][$i]);

       $respuesta = \App\ActivoCaracteristica::updateorcreate($indice, $data);
    }

    $idsDocumentoEliminar = explode(',', $request['documentoEliminar']);
    \App\ActivoDocumento::whereIn('idActivoDocumento',$idsDocumentoEliminar)->delete();

    for ($i=0 ; $i < count($request['idActivoDocumento']); $i++)
    {
       $indice = array(
        'idActivoDocumento' => $request['idActivoDocumento'][$i]);

       $data = array(
        'Activo_idActivo' => $id, 
        'TipoActivoDocumento_idTipoActivoDocumento'=>$request['idTipoActivoDocumento'][$i],
        'versionActivoDocumento'=>$request['versionActivoDocumento'][$i],
        'proveedorActivoDocumento'=>$request['proveedorActivoDocumento'][$i],
        'serialActivoDocumento'=>$request['serialActivoDocumento'][$i],

        'fechainicialActivoDocumento'=>$request['fechainicialActivoDocumento'][$i]);
       $respuesta = \App\ActivoDocumento::updateorcreate($indice, $data);

      return redirect('/activo');
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
    \App\Activo::destroy($id);
    return redirect('/activo');
}


public function llamarCaracteristicasTipoActivo()
{
    $id = (isset($_GET['idTipoActivo']) ? $_GET['idTipoActivo'] : 0);

    $datos = DB::select(
       "select 0 as idActivoCaracteristica, idTipoActivoCaracteristica, 
       nombreTipoActivoCaracteristica, 
       '' as descripcionActivoCaracteristica
       from tipoactivocaracteristica 
       where TipoActivo_idTipoActivo = ".$id);

    $informe = array();
    for($i = 0; $i < count($datos); $i++) 
    {
        $informe[] = get_object_vars($datos[$i]);
    }

    echo json_encode($informe);

}

function llamarDocumentosTipoActivo()
{

    $id = (isset($_GET['idTipoActivo']) ? $_GET['idTipoActivo'] : 0);
    $datos = DB::select(
    "select 
    idTipoActivoDocumento,descripcionTipoActivoDocumento,'' as
    VersionActivoDocumento,'' as ProveedorActivoDocumento,
    '' as SerialActivoDocumento, tipoTipoActivoDocumento,
    '' as fechainicialActivoDocumento, costoTipoActivoDocumento
    from tipoactivodocumento
    where TipoActivo_idTipoActivo=".$id);

    $informe= array();
    for($i = 0; $i < count($datos); $i++) 
    {
      $informe[] = get_object_vars($datos[$i]);
    }

    echo json_encode($informe);

}

}

