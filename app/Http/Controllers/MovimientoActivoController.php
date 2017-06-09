<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\MovimientoActivoRequest;
use Response;
use Illuminate\Routing\Route;
use DB;
use Mail;
use Carbon\Carbon;
use Session;
include public_path().'/ajax/consultarPermisosActivos.php';


class MovimientoActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       /*echo basename($_SERVER["PHP_SELF"]);
       $self = $_SERVER['PHP_SELF']; 
       echo $_SERVER["PHP_SELF"];
       return;*/
      @$idTransaccion = $_GET["idTransaccionActivo"];
        /*$vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisosMovimientos($idTransaccion);*/
        $idRechazo=\App\RechazoActivo::lists('idRechazoActivo');
        $nombreRechazo=\App\RechazoActivo::lists('nombreRechazoActivo');


      $datos = consultarPermisosActivos($idTransaccion);
      return view('movimientoactivogrid',compact('datos','idRechazo','nombreRechazo'));

    }    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

   
    $idTransaccion = $_GET["idTransaccionActivo"];
    $estado = \App\TransaccionActivo::where('idTransaccionActivo','=',$idTransaccion)->lists('estadoTransaccionActivo');

    $idLocalizacion = \App\Localizacion::All()->lists('idLocalizacion');
    $nombreLocalizacion = \App\localizacion::All()->lists('nombreLocalizacion');
    $documentoInterno=\App\TransaccionActivo::lists('nombreTransaccionActivo','idTransaccionActivo');
    $compania=\Session::get("nombreCompania");
    //$usercrea = DB::table($compania.".Tercero")->lists('nombre1Tercero as nombreCompletoTercero','idTercero');
    //$usercrea=\Session::get("idUsuario");
    $users=\App\User::all()->lists('name','id');
    $tercero = DB::table($compania.".Tercero")->lists('nombre1Tercero as nombreCompletoTercero','idTercero');
    $concepto=\App\ConceptoActivo::lists('nombreConceptoActivo','idConceptoActivo');
    
/*
   

  
  /*$conceptos = DB::select('select conceptoactivo.nombreConceptoActivo,conceptoactivo.idConceptoActivo from transaccionactivo 
    inner join transaccionconcepto 
    on transaccionconcepto.TransaccionActivo_idTransaccionActivo=transaccionactivo.idTransaccionActivo          inner join conceptoactivo 
    on transaccionconcepto.ConceptoActivo_idConceptoActivo=conceptoactivo.idConceptoActivo
    where transaccionconcepto.TransaccionActivo_idTransaccionActivo='.$idTransaccion);
  for ($i=0 ; $i < count( $conceptos); $i++) 
    {  
        $concepto[] =get_object_vars($conceptos[$i]);
    }
*/
         

/* $conceptos = DB::table('transaccionactivo')
            ->join('transaccionconcepto', 'transaccionconcepto.TransaccionActivo_idTransaccionActivo', '=', 'transaccionactivo.idTransaccionActivo')
            ->join('conceptoactivo', 'transaccionconcepto.ConceptoActivo_idConceptoActivo', '=', 'conceptoactivo.idConceptoActivo')
            ->select('conceptoactivo.nombreConceptoActivo','transaccionconcepto.ConceptoActivo_idConceptoActivo')
            ->where('transaccionconcepto.TransaccionActivo_idTransaccionActivo','=',$idTransaccion)
            ->get();*/

       //$concepto = get_object_vars($conceptos); 

           

/* for ($i=0 ; $i < count( $conceptos); $i++) 
    {  
        $concepto = get_object_vars($conceptos[$i]);
    }
*/
     

    $transaccionactivo=\App\TransaccionActivo::lists('nombreTransaccionActivo','idTransaccionActivo');
    
    return view('movimientoactivo',compact('concepto','transaccionactivo','nombrelocalizacion','idLocalizacion','users','tercero','documentoInterno','idTransaccion','idLocalizacion','nombreLocalizacion','estado'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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


    
    \App\MovimientoActivo::create(
    [
        
    'numeroMovimientoActivo'=>$numero,
    'fechaElaboracionMovimientoActivo'=>$request['fechaElaboracionMovimientoActivo'],
    'fechaInicioMovimientoActivo'=>($request['fechaInicioMovimientoActivo']  != '' ? $request['fechaInicioMovimientoActivo']: null), 
    'fechaFinMovimientoActivo'=>($request['fechaFinMovimientoActivo'] != '' ? $request['fechaFinMovimientoActivo']: null),
    'Tercero_idTercero'=>($request['Tercero_idTercero'] != '' ? $request['Tercero_idTercero']: null),
    'TransaccionActivo_idTransaccionActivo'=>$request['TransaccionActivo_idTransaccionActivo'],
    'ConceptoActivo_idConceptoActivo'=>$request['ConceptoActivo_idConceptoActivo'],
    'documentoInternoMovimientoActivo'=>($request['documentoInternoMovimientoActivo'] != '' ? $request['documentoInternoMovimientoActivo']: null) , 
    'documentoExternoMovimientoActivo'=>$request['documentoExternoMovimientoActivo'],
    'TransaccionActivo_idDocumentoInterno'=>($request['TransaccionActivo_idDocumentoInterno'] != '' ? $request['TransaccionActivo_idDocumentoInterno']: null),
    'estadoMovimientoActivo'=>$request['estadoMovimientoActivo'],
    'observacionMovimientoActivo'=>$request['observacionMovimientoActivo'], 
    'totalUnidadesMovimientoActivo'=>$request['totalUnidadesMovimientoActivo'], 
    'totalArticulosMovimientoActivo'=>$request['totalArticulosMovimientoActivo'],
    'Users_idCrea'=>\Session::get('idUsuario'),
    'Users_idCambioEstado'=>($request['Users_idCambioEstado'] != '' ? $request['Users_idCambioEstado']: null),
    'fechaCambioEstado'=>($request['fechaCambioEstado'] != '' ? $request['fechaCambioEstado']: null),
    'Compania_idCompania'=>Session::get('idCompania'),

    ]);
    
  
    $movimientoultimo = \App\MovimientoActivo::All()->last();

   if($request['estadoMovimientoActivo']=='Aprobado Total')
   {
    $estado='Aprobado';
   }
   else
   $estado="";

    for ($i=0 ; $i < count($request['idMovimientoActivoDetalle']); $i++)
    {

        \App\MovimientoActivoDetalle::create([
        
         'MovimientoActivo_idMovimientoActivo'=>$movimientoultimo->idMovimientoActivo,
         'Localizacion_idOrigen'=>$request['nombreLocalizacionO'][$i],
         'Localizacion_idDestino'=>$request['nombreLocalizacionD'][$i],
         'Activo_idActivo'=>$request['idActivo'][$i],
         'cantidadMovimientoActivoDetalle'=>$request['cantidadMovimientoActivoDetalle'][$i],
         'observacionMovimientoActivoDetalle'=>$request['observacionMovimientoActivoDetalle'][$i],
         'MovimientoActivo_idDocumentoInterno'=>($request['MovimientoActivo_idDocumentoInterno'][$i]  != '' ? $request['MovimientoActivo_idDocumentoInterno']: null), 
         'estadoMovimientoActivoDetalle'=>$estado,


         ]); 


        $this->AfectarInventario($movimientoultimo->idMovimientoActivo,'C');

    }
    //return redirect('/movimientoactivo?idTransaccionActivo='.$request['TransaccionActivo_idTransaccionActivo']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        if(isset($_GET['accion']) and $_GET['accion'] == 'imprimir')
        {
            //$movimientocrm = \App\MovimientoCRM::find($id);

            

            $idTransaccionActivo= $_GET['idTransaccionActivo'];
            
            $formatoHTML = $this->generarHTMLFormato($id, $idTransaccionActivo);
            
            return view('formatos.formatomovimientoactivo',['movimientoactivo'=>$formatoHTML], compact('idTransaccionActivo'));
        }
    
        if(isset($_GET['accion']) and $_GET['accion'] == 'dashboard')
        {
            
            $idDocumentoCRM= $_GET['idDocumentoCRM'];

            return view('dashboardcrm',compact('idDocumentoCRM'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    $idTr = $_GET["idTransaccionActivo"];
    //echo $id;
    $estado = \App\TransaccionActivo::where('idTransaccionActivo','=',$idTr)->lists('estadoTransaccionActivo');

    $movimientoactivod = DB::select(
    "select idMovimientoActivoDetalle, MovimientoActivo_idMovimientoActivo, Localizacion_idOrigen as nombreLocalizacionO, Localizacion_idDestino as nombreLocalizacionD, Activo_idActivo as idActivo, cantidadMovimientoActivoDetalle, observacionMovimientoActivoDetalle, codigoActivo, nombreActivo, serieActivo, MovimientoActivo_idDocumentoInterno 
    from movimientoactivodetalle M 
    inner join activo
    on M.Activo_idActivo=activo.idActivo
    where M.MovimientoActivo_idMovimientoActivo=".$id);

    $movimientoactivodetalle= array();
    for($i = 0; $i < count($movimientoactivod); $i++) 
    {
      $movimientoactivodetalle[] = get_object_vars($movimientoactivod[$i]);
    }

    //echo json_encode($movimientoactivodetalle);

    $movimientoactivo = \App\MovimientoActivo::find($id);
    //$movimientoactivodetalle =\App\MovimientoActivo::All();
    

    
    
    $idTransaccion = $_GET["idTransaccionActivo"];
    $transaccionactivo=\App\TransaccionActivo::lists('nombreTransaccionActivo','idTransaccionActivo');
    $idLocalizacion=\App\Localizacion::All()->lists('idLocalizacion');
    $nombreLocalizacion=\App\Localizacion::All()->lists('nombreLocalizacion');
    $documentoInterno=\App\TransaccionActivo::lists('nombreTransaccionActivo','idTransaccionActivo');
    $compania=\Session::get("nombreCompania");
    //$usercrea = DB::table($compania.".Tercero")->lists('nombre1Tercero as nombreCompletoTercero','idTercero');
    $users=\App\User::All()->lists('name','id');
    $tercero = DB::table($compania.".Tercero")->lists('nombre1Tercero as nombreCompletoTercero','idTercero');
    //$tipoactivo=\App\TipoActivo::lists('nombreTipoActivo','idTipoActivo')->prepend('Selecciona');
    $concepto=\App\ConceptoActivo::lists('nombreConceptoActivo','idConceptoActivo');
   
    /*$concepto = DB::table('transaccionactivo')
            ->join('transaccionconcepto', 'transaccionconcepto.TransaccionActivo_idTransaccionActivo', '=', 'transaccionactivo.idTransaccionActivo')
            ->join('conceptoactivo', 'transaccionconcepto.ConceptoActivo_idConceptoActivo', '=', 'conceptoactivo.idConceptoActivo')
            ->select('conceptoactivo.nombreConceptoActivo,conceptoactivo.idConceptoActivo')
            ->where('transaccionconcepto.TransaccionActivo_idTransaccionActivo','=',$idTransaccion)
            ->get();*/


    $transaccionactivo=\App\TransaccionActivo::lists('nombreTransaccionActivo','idTransaccionActivo');
    

        //$documento = \App\DocumentoCRM::where('idDocumentoCRM','=',$idDocumento)->lists('GrupoEstado_idGrupoEstado');
       
    

    return view('movimientoactivo',compact('movimientoactivo','movimientoactivodetalle','concepto','transaccionactivo','localizacion','users','tercero','documentoInterno','idTransaccion','idLocalizacion','nombreLocalizacion','estado'));




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

        $this->AfectarInventario($id,'D');



       $accionTranAct = DB::select(
    "SELECT accionTransaccionActivo
        FROM  transaccionactivo T 
        LEFT JOIN movimientoactivo M
        on T.idTransaccionActivo = M.TransaccionActivo_idTransaccionActivo 
        where   T.Compania_idCompania = ".\Session::get('idCompania')." and idMovimientoActivo=$id and 
        TransaccionActivo_idTransaccionActivo = ".$request['TransaccionActivo_idTransaccionActivo']);

    $tipo = get_object_vars($accionTranAct[0])["accionTransaccionActivo"];


        $movimientoactivo = \App\MovimientoActivo::find($id);
        $movimientoactivo->fill($request->all());


        $movimientoactivo->Tercero_idTercero = ($request['Tercero_idTercero'] != '' ? $request['Tercero_idTercero'] : null);
        $movimientoactivo->ConceptoActivo_idConceptoActivo = ($request['ConceptoActivo_idConceptoActivo'] != '' ? $request['ConceptoActivo_idConceptoActivo'] : null);
        $movimientoactivo->documentoInternoMovimientoActivo = ($request['documentoInternoMovimientoActivo'] != '' ? $request['documentoInternoMovimientoActivo'] : null);
        $movimientoactivo->TransaccionActivo_idDocumentoInterno = ($request['TransaccionActivo_idDocumentoInterno'] != '' ? $request['TransaccionActivo_idDocumentoInterno'] : null);
        $movimientoactivo->Users_idCambioEstado = ($request['Users_idCambioEstado'] != '' ? $request['Users_idCambioEstado'] : null);
        $movimientoactivo->fechaCambioEstado = ($request['fechaCambioEstado'] != '' ? $request['fechaCambioEstado'] : null);

        $movimientoactivo->save();

        $movimientoultimo = \App\MovimientoActivo::All()->last();

        
        $idsmovdetalleEliminar = explode(',', $request['movimientoEliminar']);
       /*print_r($idsmovdetalleEliminar);
        return;*/
        \App\MovimientoActivoDetalle::whereIn('idMovimientoActivoDetalle',$idsmovdetalleEliminar)->delete();
        for ($i=0 ; $i < count($request['idMovimientoActivoDetalle']); $i++)
        {
           $indice = array(
            'idMovimientoActivoDetalle' => $request['idMovimientoActivoDetalle'][$i]);

           $data = array(
         'MovimientoActivo_idMovimientoActivo'=>$id,
         'Localizacion_idOrigen'=>$request['nombreLocalizacionO'][$i],
         'Localizacion_idDestino'=>$request['nombreLocalizacionD'][$i],
         'Activo_idActivo'=>$request['idActivo'][$i],
         'cantidadMovimientoActivoDetalle'=>$request['cantidadMovimientoActivoDetalle'][$i],
         'observacionMovimientoActivoDetalle'=>$request['observacionMovimientoActivoDetalle'][$i],
         'MovimientoActivo_idDocumentoInterno'=>($request['MovimientoActivo_idDocumentoInterno'][$i]  != '' ? $request['MovimientoActivo_idDocumentoInterno']: null),
         'RechazoActivo_idRechazoActivo'=>($request['RechazoActivo_idRechazoActivo'][$i]  != '' ? $request['RechazoActivo_idRechazoActivo']: null),

            );

        
              
        $respuesta = \App\MovimientoActivoDetalle::updateorcreate($indice, $data);
        }
          

        $this->AfectarInventario($id,'C');

        $this->EnviarCorreo($request['idUsuarioCrea'],$request['numeroMovimientoActivo'],$movimientoactivo->idMovimientoActivo, $request['TransaccionActivo_idTransaccionActivo']);

         /*$correos = DB::select('
            SELECT  email as correoElectronicoTercero
                FROM    users U 
                WHERE   (U.Tercero_idAsociado = '.$request['idUsuarioCrea'].'
                        and
                        email != "" )
            UNION DISTINCT
            SELECT  email as correoElectronicoTercero 
                FROM transaccionrol TR
                LEFT JOIN users U
                ON TR.Rol_idRol = U.Rol_idRol
                WHERE   autorizarTransaccionRol = 1 and 
                        TransaccionActivo_idTransaccionActivo = '.$request['TransaccionActivo_idTransaccionActivo'].' and 
                        U.Tercero_idAsociado IS NOT NULL and 
                        email IS NOT NULL and 
                        email != "" and 
                        U.Compania_idCompania = '.\Session::get("idCompania"));
        $datos['correos'] = array();
        for($c = 0; $c < count($correos); $c++)
        {
            $datos['correos'][] = get_object_vars($correos[$c])['correoElectronicoTercero'];
        }

        if(count($correos) > 0)
        {
            $solicitante = DB::table(\Session::get("baseDatosCompania").'.Tercero')->lists('nombre1Tercero as nombreCompletoTercero');
            $idTransaccionActivo= $request['TransaccionActivo_idTransaccionActivo'];
            $datos['asunto'] = Session::get('baseDatosCompania').' Modificación Inventario Activo: '.$request['nombreTransaccionActivo'];
            $datos['mensaje'] ='Se ha generado el reporte del estado de Inventario de Activos:'.$request['nombreTransaccionActivo'];
            //$datos['mensaje'] .=',Generado por:'.$solicitante[0]["nombreCompletoTercero"];
            $datos['mensaje'] .=', los detalles los encontrará en el archivo adjunto.';

            $contenidoArchivo =  '<!DOCTYPE html>
            <html>
            <head>

                <meta http-equiv="content-type" content="text/html; charset=UTF-8">
                <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

                

                <title>Scalia</title>
            </head>
            <body>
                <p> '.$this->generarHTMLFormato($movimientoactivo->idMovimientoActivo, $idTransaccionActivo).'</p>
            </body>
            </html>';
            $nombreAdj=$request['nombreTransaccionActivo'].'.html';
            $adj=fopen($nombreAdj,"w");
            fputs($adj, $contenidoArchivo );
            fclose($adj);

            $datos['adjunto'] = $nombreAdj;
             Mail::send('correomovimientoactivo',$datos,function($msj) use ($datos)
            {
                
                $msj->to($datos['correos']);
                $msj->subject($datos['asunto']);
                
                $msj->attach($datos['adjunto']);
                
            });
            unlink($nombreAdj);
        }
*/

    //return redirect('/movimientoactivo?idTransaccionActivo='.$request['TransaccionActivo_idTransaccionActivo']);
    

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   

        DB::select(
                    'delete from MovimientoActivoDetalle
                    WHERE MovimientoActivo_idMovimientoActivo = '.$id);

        \App\MovimientoActivoDetalle::destroy($id);

        $movimientoactivo = DB::select(
                    'SELECT TransaccionActivo_idTransaccionActivo
                    FROM movimientoactivo
                    WHERE idMovimientoActivo = '.$id);

        $movimientoactivo = get_object_vars($movimientoactivo[0]);
        
        \App\MovimientoActivo::destroy($id);
        return redirect('/movimientoactivo?idTransaccionActivo='.$movimientoactivo['TransaccionActivo_idTransaccionActivo']);

    }


    function EnviarCorreo($user,$transaccion,$movimiento,$idTrans)
    {

     $correos = DB::select('
            SELECT  email as correoElectronicoTercero
                FROM    users U 
                WHERE   (U.Tercero_idAsociado = '.$user.'
                        and
                        email != "" )
            UNION DISTINCT
            SELECT  email as correoElectronicoTercero 
                FROM transaccionrol TR
                LEFT JOIN users U
                ON TR.Rol_idRol = U.Rol_idRol
                WHERE   autorizarTransaccionRol = 1 and 
                        TransaccionActivo_idTransaccionActivo = '.$transaccion.' and 
                        U.Tercero_idAsociado IS NOT NULL and 
                        email IS NOT NULL and 
                        email != "" and 
                        U.Compania_idCompania = '.\Session::get("idCompania"));
        $datos['correos'] = array();
        for($c = 0; $c < count($correos); $c++)
        {
            $datos['correos'][] = get_object_vars($correos[$c])['correoElectronicoTercero'];
        }

        if(count($correos) > 0)
        {
            $solicitante = DB::table(\Session::get("baseDatosCompania").'.Tercero')->lists('nombre1Tercero as nombreCompletoTercero');
            $idTransaccionActivo= $transaccion;
            $datos['asunto'] = Session::get('baseDatosCompania').' Modificación Inventario Activo: '.$transaccion;
            $datos['mensaje'] ='Se ha generado el reporte del estado de Inventario de Activos:'.$transaccion;
            //$datos['mensaje'] .=',Generado por:'.$solicitante[0]["nombreCompletoTercero"];
            $datos['mensaje'] .=', los detalles los encontrará en el archivo adjunto.';

            $contenidoArchivo =  '<!DOCTYPE html>
            <html>
            <head>

                <meta http-equiv="content-type" content="text/html; charset=UTF-8">
                <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

                

                <title>Scalia</title>
            </head>
            <body>
                <p> '.$this->generarHTMLFormato($movimiento,$idTrans).'</p>
            </body>
            </html>';
            $nombreAdj=$transaccion.'.html';
            $adj=fopen($nombreAdj,"w");
            fputs($adj, $contenidoArchivo );
            fclose($adj);

            $datos['adjunto'] = $nombreAdj;
             Mail::send('correomovimientoactivo',$datos,function($msj) use ($datos)
            {
                
                $msj->to($datos['correos']);
                $msj->subject($datos['asunto']);
                
                $msj->attach($datos['adjunto']);
                
            });
            unlink($nombreAdj);
        }


    }


function llamarDescripcionActivo()
{

    $id = (isset($_GET['codigoActivo']) ? $_GET['codigoActivo'] : 0);
    $datos = DB::select(
    "select idActivo,
    serieActivo,nombreActivo
    from activo
    where codigoActivo=".$id);

    $informe= array();
    for($i = 0; $i < count($datos); $i++) 
    {
      $informe[] = get_object_vars($datos[$i]);
    }

    echo json_encode($informe);

}

   function MostrarDetalleActivo()
{

    $id = (isset($_GET['idActivo']) ? $_GET['idActivo'] : 0);
    $datos = DB::select(
    "select * from activo
    where idActivo=".$id);
    
   //echo "contador datos ".count($datos);
if(count($datos)!=0)

{
    $datos= get_object_vars($datos[0]); 
     $html = "<b>Tipo Activo: </b>".$datos["nombreActivo"]."<br>".
           "<b>Marca: </b>".$datos["marcaActivo"]."<br>".
           "<b>Modelo: </b>".$datos["modeloActivo"]."<br>".
           "<b>Serie: </b>".$datos["serieActivo"]." <br>";
    
      $datos1 = DB::select(
        "SELECT nombreTipoActivoCaracteristica, descripcionActivoCaracteristica
            FROM scalia.activocaracteristica ac
            left join tipoactivocaracteristica tac
            on ac.TipoActivoCaracteristica_idTipoActivoCaracteristica = tac.idTipoActivoCaracteristica
        where Activo_idActivo=".$id);


        $html.= "<h3 data-toggle='collapse' data-target='#caracteristicas'><u>Caracteristicas</u></h3>
                    <div id='caracteristicas' class='collapse'>";

        for ($i=0; $i < count($datos1); $i++) 
        { 
            $datos = get_object_vars($datos1[$i]); 
            $html.= 
            "<b>".$datos["nombreTipoActivoCaracteristica"]."</b>:  ".
            $datos["descripcionActivoCaracteristica"]."<br>";
        }
    $html.= "</div>";


}




          
    
echo json_encode($html);

}



 protected function generarHTMLFormato($idmovimientoactivo, $id)
{

$movimientoactivo = DB::select(
"SELECT 
movimientoactivo.numeroMovimientoActivo,
movimientoactivo.fechaElaboracionMovimientoActivo, 
movimientoactivo.fechaInicioMovimientoActivo, 
movimientoactivo.fechaFinMovimientoActivo, 
Tercero.nombre1Tercero as tercero,
transaccion.nombreTransaccionActivo as transaccion,
conceptoactivo.nombreConceptoActivo,
movimientoactivo.documentoInternoMovimientoActivo,
movimientoactivo.documentoExternoMovimientoActivo,
transaccion.idTransaccionActivo as documentoInterno,
movimientoactivo.estadoMovimientoActivo,
movimientoactivo.observacionMovimientoActivo,
movimientoactivo.totalUnidadesMovimientoActivo,
movimientoactivo.totalArticulosMovimientoActivo,
UsersCrea.name as UsersCrea,
UsersCambia.name as UsersCambia,
movimientoactivo.fechaCambioEstado, 
compania.nombreCompania
                
FROM
movimientoactivo
    LEFT JOIN
".\Session::get("baseDatosCompania").".Tercero 
 ON movimientoactivo.Tercero_idTercero = Tercero.idTercero
LEFT JOIN
transaccionactivo 
ON movimientoactivo.TransaccionActivo_idTransaccionActivo = transaccionactivo.idTransaccionActivo
LEFT JOIN transaccionactivo transaccion
 ON movimientoactivo.TransaccionActivo_idTransaccionActivo = transaccionactivo.idTransaccionActivo 
LEFT JOIN transaccionactivo documentoInterno
 ON movimientoactivo.TransaccionActivo_idTransaccionActivo = transaccionactivo.idTransaccionActivo
LEFT JOIN conceptoactivo
 ON movimientoactivo.ConceptoActivo_idConceptoActivo = conceptoactivo.idConceptoActivo
LEFT JOIN users UsersCrea
 ON movimientoactivo.Users_idCrea = UsersCrea.id
LEFT JOIN users UsersCambia
 ON movimientoactivo.Users_idCrea = UsersCambia.id
LEFT JOIN compania
 ON movimientoactivo.Compania_idCompania = compania.idCompania 
WHERE idMovimientoActivo = $idmovimientoactivo");

 /*$movimientoactivo = DB::select(
    "SELECT * 
    FROM movimientoactivo 
    WHERE idMovimientoActivo = $idmovimientoactivo");*/


$html = '';
        

$campos = DB::select(
"select idCampoTransaccion,codigoTransaccionActivo,nombreTransaccionActivo,tipoNumeracionTransaccionActivo,longitudTransaccionActivo,
desdeTransaccionActivo,hastaTransaccionActivo,tipoCampoTransaccion,nombreCampoTransaccion,
descripcionCampoTransaccion,relacionTablaCampoTransaccion,relacionIdCampoTransaccion,
relacionNombreCampoTransaccion, relacionAliasCampoTransaccion
from transaccionactivo
left join transaccionactivocampo
on transaccionactivo.idTransaccionActivo=transaccionactivocampo.TransaccionActivo_idTransaccionActivo
left join campotransaccion
on transaccionactivocampo.CampoTransaccion_idCampoTransaccion=campotransaccion.idCampoTransaccion
where transaccionactivo.idTransaccionActivo=".$id);
//print_r($campos);

$datos = array();
$camposVista = '';
for($i = 0; $i < count($campos); $i++)
{
    $datos = get_object_vars($campos[$i]); 
    $camposVista = $datos["nombreCampoTransaccion"].',';
    $transaccion = $datos["nombreTransaccionActivo"];
}


$idMovimientoActivo = (isset($movimientoactivo->idMovimientoActivo) ? $movimientoactivo->idMovimientoActivo : 0);
//$idTransaccion = $_GET["idTransaccionActivo"];



        $movimiento = array();
        for($i = 0; $i < count($movimientoactivo); $i++)
        {
            $movimiento[] = get_object_vars($movimientoactivo[$i]); 
        }


    $movimientoactivod = DB::select(
    "select idMovimientoActivoDetalle, MovimientoActivo_idMovimientoActivo, LocO.nombreLocalizacion as nombreLocalizacionO, LocD.nombreLocalizacion as nombreLocalizacionD, Activo_idActivo as idActivo, cantidadMovimientoActivoDetalle, observacionMovimientoActivoDetalle, codigoActivo, nombreActivo, serieActivo, MovimientoActivo_idDocumentoInterno 
    from movimientoactivodetalle M 
    inner join activo
    on M.Activo_idActivo=activo.idActivo
    left join localizacion LocO
    on M.Localizacion_idOrigen=LocO.idLocalizacion
    left join localizacion LocD
    on M.Localizacion_idDestino=LocD.idLocalizacion
    where M.MovimientoActivo_idMovimientoActivo=$idmovimientoactivo");

                 
    $movimientoactivodetalle=array();
    for($i = 0; $i < count($movimientoactivod); $i++) 
    {
      $movimientoactivodetalle[] = get_object_vars($movimientoactivod[$i]);

  
    }
   
  

    $html .= '<div id="form-section" >
                <fieldset id="movimientoactivo-form-fieldset"> 

                         <center>
                         <div class="container">
                         <b><h3>'.$transaccion.'</b></h3>
                         </div>
                         </center>
                        <br>


                          
                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Número</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["numeroMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Fecha Elaboracion</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["fechaElaboracionMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Tercero</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["tercero"].
                                    '</div>
                                </div>
                            </div>



                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Usuario Creador</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["UsersCrea"].
                                    '</div>
                                </div>
                            </div>
                             

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Tipo Documento</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["transaccion"].
                                    '</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Num Documento Interno</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["documentoInterno"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Num Documento Externo</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["documentoExternoMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Fecha Inicio</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["fechaInicioMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Fecha Fin</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["fechaFinMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Estado</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["estadoMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    <b>Concepto</b>
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["nombreConceptoActivo"].
                                    '</div>
                                </div>
                            </div></div>

              
                    
              
             
             <center>
             <br><h3><b>ACTIVOS</b></h3>
          
<div class="panel-body">
            <table class="table table-bordered" >
                <tr style="background-color:#337ab7;color:white;">
                    <td style="width: 150px;">Loc origen</td>
                    <td style="width: 150px;">Loc Destino</td>
                    <td style="width: 130px;">Codigo</td>
                    <td style="width: 130px;">Serial</td>
                    <td style="width: 280px;">Descripcion</td>
                    <td style="width: 100px;">Cantidad</td>
                    <td style="width: 230px;">Observacion</td> 
                </tr>';

            for ($c=0 ;$c<count($movimientoactivodetalle) ;$c++)
            {                   
                $html .= '<tr><td>'.$movimientoactivodetalle[$c]['nombreLocalizacionO'].'</td>';
                $html .= '<td>'.$movimientoactivodetalle[$c]['nombreLocalizacionO'].'</td>';
                $html .= '<td>'.$movimientoactivodetalle[$c]["codigoActivo"].'</td>';
                $html .= '<td>'.$movimientoactivodetalle[$c]["serieActivo"].'</td>';
                $html .= '<td>'.$movimientoactivodetalle[$c]["nombreActivo"].'</td>';
                $html .= '<td>'.$movimientoactivodetalle[$c]["cantidadMovimientoActivoDetalle"].'</td>';
                $html .= '<td>'.$movimientoactivodetalle[$c]["observacionMovimientoActivoDetalle"].'</td>';
                $html .= '</tr>';
             } 

                                                   
            $html.='</table>
    

</div>


    <div style="margin-top:1cm;position:center;width:98%;" class="panel panel-primary">
      <div class="panel-heading">
      Observaciones
      </div>
      <div class="panel-body">
       
      </div>
    </div>
    

                   
              



                    
                        


                   


            </fieldset>
        </div>';

                                 
    
                    
       
                        
                    
                    
       

            return  $html;

    }


function AprobacionActivos()
{

    @$id=$_GET['idMovimientoActivo'];


    //$activo = \App\Activo::All();
    
    $activo = DB::select(
     "SELECT idMovimientoActivoDetalle,MovimientoActivo_idMovimientoActivo,idActivo,
    codigoActivo,serieActivo,nombreActivo,observacionMovimientoActivoDetalle
    FROM movimientoactivodetalle
    INNER JOIN activo
    ON movimientoactivodetalle.Activo_idActivo=activo.idActivo
    WHERE
    MovimientoActivo_idMovimientoActivo=".$id);

    $activos=array();
   
    if(count($activo)!=0)

    {

     for ($i=0 ; $i < count( $activo); $i++) 
    {  
        $activos[] = get_object_vars($activo[$i]);
    
    }

    $rechazo=\App\RechazoActivo::all();



    }
    echo json_encode($activos);





    //return array($activos,$rechazo);

  
}


    function VerificacionComponentes()
    {

        @$id=$_GET['idActivo'];

        //$activo = \App\Activo::All();
        $parteactivo = DB::select(
         "SELECT 
        codigoActivo,serieActivo,nombreActivo
        FROM activocomponente
        Inner join activo
        ON activocomponente.Activo_idActivo=activo.idActivo
        WHERE  
        activocomponente.Activo_idComponente=".$id);

        $partes = array();
        if(count($parteactivo)!=0)

        {
              
               
             for ($i=0 ; $i < count( $parteactivo); $i++) 
            {  
                $partes[] = get_object_vars($parteactivo[$i]);
            
            }

            
              
        }
        echo json_encode($partes);

    }




function ActualizarMovimientoActivo()
{

    @$datos=$_GET['valores'];
    //print_r(json_encode($datos));

   $idMovActD=array();
   $contV=0;
   $contA=0;
   $contR=0;

   for ($i=0 ; $i < count($datos); $i++)
    {

  
    if ( $datos[$i][2]=="")

    {
      $rechazo="null";
   
    }

    else
    {

    $rechazo=$datos[$i][2];

    }

   
    
    $idActivo = str_replace('"','',$datos[$i][0]);
    $estadoD= str_replace('"','',$datos[$i][1]);
    //$rechazo= str_replace('"','',$datos[$i][2]);
    $idMovAct = str_replace('"','',$datos[$i][3]);
    $idMovActD=$datos[$i][4];

  

   $movAct=DB::SELECT(
    "update movimientoactivodetalle 
    set estadoMovimientoActivoDetalle='".$estadoD."', RechazoActivo_idRechazoActivo=".$rechazo." 
    where idMovimientoActivoDetalle IN($idMovActD)");

    

     if ($estadoD=="") 
    {   $contV++;
    }

     if ($estadoD=="Aprobado") 
    {
        $contA++;
    }

    if ($estadoD=="Rechazado") 
    {
        $contR++;
    }


    }

$estadoE="";
    if($contR==count($datos))
   {
        $estadoE="Rechazado";
   
   }
    elseif($contA==count($datos))
    {
    
        $estadoE="Aprobado Total";

    }

    elseif($contA>count($datos) or $contA=$contR)
    {
           
   
    $estadoE="Aprobado Parcial";
   
    }


    
   $movimientoactivo = \App\MovimientoActivo::find($idMovAct);
       $movimientoactivo->update(
       [
         'estadoMovimientoActivo'=>$estadoE,
            'Users_idCambioEstado'=>Session::get('idUsuario'),
            'fechaCambioEstado'=>Carbon::now(),
         ]);

    $movimientoactivo->save();
    echo json_encode("Se ha cambiado el estado a:".$estadoE);


/*echo $estadoE;

    echo "<BR>Aprobados:".$contA;

    echo "Rechazados:".$contR;
    echo "Vacios:".$contV;*/
      

       
          $this->AfectarInventario($idMovAct,'C');
   
       
    }

    // Parámetros
    // $idMov: Id del movimiento que se va a actualizar en el inventario
    // $accion: puede contener 2 valores:
    //      C: Carga (Sumar la cantidad al inventario)
    //      D: Descarga (Restar la cantidad del inventario)
    function AfectarInventario($idMov,$accion)
    {

 

        $parametro=DB::select(" 
                SELECT 
                    estadoMovimientoActivo,
                    accionTransaccionActivo
                FROM movimientoactivo
                inner join transaccionactivo
                on movimientoactivo.TransaccionActivo_idTransaccionActivo=transaccionactivo.idTransaccionActivo
                WHERE idMovimientoActivo=".$idMov);

        if(count($parametro) == 0)
        {
            echo 'No se encuentra el movimiento con id '.$idMov;
            return;
        }
        $parametro = get_object_vars($parametro[0]);

        // si el estado no es aprobado, debe retornar
        if($parametro["estadoMovimientoActivo"] != "Aprobado Total" )
        {
            echo 'El movimiento no está aprobado, id '.$idMov;
            return;
        }

        if($parametro["accionTransaccionActivo"] == "No Afecta" )
        {
            echo 'El movimiento no afecta Inventario, id '.$idMov;
            return;
        }

        // si la accion es entrada, guardamos la cantidad en el campo entradasInventarioActivo, 
        // si la accion es salida, guardamos la cantidad en el campo salidasFinalInventarioActivo
        $Entrada= "(".($parametro["accionTransaccionActivo"] == 'Entrada' ? 'cantidadMovimientoActivoDetalle' : '0')."*".($accion == 'D' ? '(-1)' : '(1)').") ";
        $Salida=  "(".($parametro["accionTransaccionActivo"] == 'Salida' ? 'cantidadMovimientoActivoDetalle' : '0')."*".($accion == 'D' ? '(-1)' : '(1)').")";




        $insertar=DB::SELECT(" 
            INSERT INTO inventarioactivo 
            (Periodo_idPeriodo, Activo_idActivo, Localizacion_idLocalizacion, 
            saldoInicialInventarioActivo, entradasInventarioActivo, salidasInventarioActivo, saldoFinalInventarioActivo) 
            (SELECT 
                idPeriodo AS Periodo,
                movimientoactivodetalle.Activo_idActivo,
                movimientoactivodetalle.Localizacion_idOrigen,
                0, $Entrada as Entrada, $Salida as Salida,0
            FROM movimientoactivodetalle
            inner join movimientoactivo
            on movimientoactivodetalle.MovimientoActivo_idMovimientoActivo=movimientoactivo.idMovimientoActivo 
            left join Iblu.Periodo
            on movimientoactivo.fechaElaboracionMovimientoActivo>=fechaInicialPeriodo and movimientoactivo.fechaElaboracionMovimientoActivo<=fechaFinalPeriodo
            WHERE MovimientoActivo_idMovimientoActivo=".$idMov.")
            on duplicate key update
            saldoInicialInventarioActivo=0, 
            entradasInventarioActivo=$Entrada,
            salidasInventarioActivo=$Salida,
            saldoFinalInventarioActivo=saldoInicialInventarioActivo+entradasInventarioActivo-salidasInventarioActivo");

     

   


    }

}
