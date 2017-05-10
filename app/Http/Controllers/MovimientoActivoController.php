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
    //$tipoactivo=\App\TipoActivo::lists('nombreTipoActivo','idTipoActivo')->prepend('Selecciona');
    $concepto=\App\ConceptoActivo::lists('nombreConceptoActivo','idConceptoActivo');
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
          

        $this->AfectarInventario($id,'D');

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
    "SELECT * 
    FROM movimientoactivo 
    WHERE idMovimientoActivo = $idmovimientoactivo");


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
}


$idMovimientoActivo = (isset($movimientoactivo->idMovimientoActivo) ? $movimientoactivo->idMovimientoActivo : 0);
$idTransaccion = $_GET["idTransaccionActivo"];



        $movimiento = array();
        for($i = 0; $i < count($movimientoactivo); $i++)
        {
            $movimiento[] = get_object_vars($movimientoactivo[$i]); 
        }


    $html .= '<div id="form-section" >
                <fieldset id="movimientoactivo-form-fieldset"> 

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Número
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["numeroMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Fecha Elaboracion
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["fechaElaboracionMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Tercero
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["Tercero_idTercero"].
                                    '</div>
                                </div>
                            </div>



                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Usuario Creador
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["Users_idCrea"].
                                    '</div>
                                </div>
                            </div>
                             

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Tipo Documento
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["Users_idCrea"].
                                    '</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Num Documento Interno
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["Users_idCrea"].
                                    '</div>
                                </div>
                            </div>



                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Num Documento Externo
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["Users_idCrea"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Fecha Inicio
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["Users_idCrea"].
                                    '</div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Fecha Fin
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["Users_idCrea"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Estado
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["estadoMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="col-sm-4">
                                    Concepto
                                </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["estadoMovimientoActivo"].
                                    '</div>
                                </div>
                            </div>


                <div id="detalles" class="panel panel-primary">
                    <div class="col-sm-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <i class="fa fa-pencil-square-o"></i> 
                                Detalles 
                            </div>
                            <div class="panel-body">
                                <div class="col-sm-12">
                                    <table class="display table-bordered" >
                                            <tr class="class="panel-heading" >
                                                <td style="width: 330px;">Loc origen</td>
                                                <td style="width: 270px;">Loc Destino</td>
                                                <td style="width: 150px;">Codigo</td>
                                                <td style="width: 230px;">Serial</td>
                                                <td style="width: 230px;">Descripcion</td>
                                                <td style="width: 230px;">Cantidad</td>
                                                <td style="width: 230px;">Observacion</td> 
                                            </tr>
                                        <div class="panel-body">';
                                            $html .= '<tr><td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '</tr>
                                        </div>    
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="detalles" class="panel panel-primary">
                    <div class="col-sm-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <i class="fa fa-pencil-square-o"></i> 
                                Detalles 
                            </div>
                            <div class="panel-body">
                                <div class="col-sm-12">
                                    <table class="display table-bordered" >
                                            <tr class="class="btn-primary" >
                                                <td style="width: 330px;">Loc origen</td>
                                                <td style="width: 270px;">Loc Destino</td>
                                                <td style="width: 150px;">Codigo</td>
                                                <td style="width: 230px;">Serial</td>
                                                <td style="width: 230px;">Descripcion</td>
                                                <td style="width: 230px;">Cantidad</td>
                                                <td style="width: 230px;">Observacion</td> 
                                            </tr>
                                        <div class="panel-body">';
                                            $html .= '<tr><td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '<td>'.$movimiento[0]["estadoMovimientoActivo"].'</td>';
                                            $html .= '</tr>
                                        </div>    
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                    <div id="detalles" class="panel panel-primary">
                        <div class="col-sm-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <i class="fa fa-pencil-square-o"></i> 
                                    Detalles 
                                </div>
                                <div class="panel-body">
                                    
                                    <div class="col-sm-12">
                                          '.$movimiento[0]["observacionMovimientoActivo"].'
                                    </div>

                                </div>
                            </div>
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
   
     for ($i=0 ; $i < count( $activo); $i++) 
    {  
        $activos[] = get_object_vars($activo[$i]);
    
    }

    $rechazo=\App\RechazoActivo::all();


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
    
    $idActivo = str_replace('"','',$datos[$i][0]);
    $estadoD= str_replace('"','',$datos[$i][1]);
    $rechazo= str_replace('"','',$datos[$i][2]);
    $idMovAct = str_replace('"','',$datos[$i][3]);
    $idMovActD=$datos[$i][4];

  /* $movAct=DB::SELECT(
    "update movimientoactivodetalle 
    set estadoMovimientoActivoDetalle='".$datos[$i][1]."', RechazoActivo_idRechazoActivo=".$datos[$i][2]." 
    where idMovimientoActivoDetalle IN($idMovActD)");*/

    $estadoD= ($estadoD != '' ? $estadoD : null);
    $rechazo= ($rechazo != '' ? $rechazo : null);

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

     
    echo $estadoE;

    echo "<BR>Aprobados:".$contA;

    echo "Rechazados:".$contR;
    echo "Vacios:".$contV;


             





   
    
    // $movimientoactivo = \App\MovimientoActivo::find($idMovAct);
    //     $movimientoactivo->update(
    //     [
    //         'estadoMovimientoActivo'=>$estadoE,
    //         'Users_idCambioEstado'=>Session::get('idUsuario'),
    //         'fechaCambioEstado'=>Carbon::now(),
    //     ]);

    //     $movimientoactivo->save();


      

        /*echo "<br><br><center>
        <h1>Se han Guardado los Cambios</h1>";*/
        /* ?>
        <script>
        setTimeout("location.href='http://190.248.133.146:8000/scalia'",1000)
        </script>  
        <?php*/


        


       
              
       
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
        if($parametro["estadoMovimientoActivo"] != "Aprobado" )
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
        $Entrada=        "(".($parametro["accionTransaccionActivo"] == 'Entrada' ? 'cantidadMovimientoActivoDetalle' : '0')."*".($accion == 'D' ? '(-1)' : '(1)').") ";
        $Salida=         "(".($parametro["accionTransaccionActivo"] == 'Salida' ? 'cantidadMovimientoActivoDetalle' : '0')."*".($accion == 'D' ? '(-1)' : '(1)').")";




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

        echo " 
            INSERT INTO inventarioactivo 
            (Periodo_idPeriodo, Activo_idActivo, Localizacion_idLocalizacion, 
            saldoInicialInventarioActivo, entradasInventarioActivo, salidasInventarioActivo, saldoFinalInventarioActivo) 
            SELECT 
                MONTH(fechaElaboracionMovimientoActivo) AS Periodo,
                movimientoactivodetalle.Activo_idActivo,
                movimientoactivodetalle.Localizacion_idOrigen,
                0, $Entrada, $Salida,0
            FROM movimientoactivodetalle
            inner join movimientoactivo
            on movimientoactivodetalle.MovimientoActivo_idMovimientoActivo=movimientoactivo.idMovimientoActivo 
            left join Iblu.Periodo
            on movimientoactivo.fechaElaboracionMovimientoActivo>=fechaInicioPerido and movimientoactivo.fechaElaboracionMovimientoActivo<=fechafinperiodo
            on duplicate key update
            saldoInicialInventarioActivo=0, 
            entradasInventarioActivo=$Entrada,
            salidasInventarioActivo=$Salida,
            saldoFinalInventarioActivo=saldoInicialInventarioActivo+entradasInventarioActivo-salidasInventarioActivo

            WHERE MovimientoActivo_idMovimientoActivo=".$idMov;

   


    }

}
