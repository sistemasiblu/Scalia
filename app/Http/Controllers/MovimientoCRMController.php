<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\MovimientoCRMRequest;
use Response;
use Illuminate\Routing\Route;
use DB;
use Mail;
use Session;
include public_path().'/ajax/consultarPermisosCRM.php';
// use Illuminate\Database\Eloquent\Collection::take();

class MovimientoCRMController extends Controller
{
    public function indexMovimientocrmVacantegridselect()
    {
        return view('MovimientocrmVacantegridselect');
        
    }


    public function index()
    {

        $idDocumento = $_GET["idDocumentoCRM"];
        $documento = \App\DocumentoCRM::where('idDocumentoCRM','=',$idDocumento)->lists('GrupoEstado_idGrupoEstado');

        
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisosCRM($idDocumento);

        $supervisor = DB::table(\Session::get("baseDatosCompania").'.Tercero')->where('tipoTercero','like','%05%')->lists('nombre1Tercero as nombreCompletoTercero','idTercero');
        // $asesor = DB::table(\Session::get("baseDatosCompania").'.Tercero')
        //         ->where('tipoTercero','like','%05%')
        //         ->lists('nombre1Tercero as nombreCompletoTercero','idTercero');

        $asesores = DB::table('grupoestadoasesor')
                ->leftjoin(\Session::get("baseDatosCompania").'.Tercero', 'Tercero_idAsesor', '=', 'idTercero')->where('GrupoEstado_idGrupoEstado','=',$documento[0])
                ->lists('nombre1Tercero as nombreCompletoTercero','idTercero');

        $compania=\Session::get('baseDatosCompania');
        // $asesore = DB::select(
        //  "select idGrupoEstadoAsesor,GrupoEstado_idGrupoEstado,  tercero.idTercero, tercero.nombre1Tercero 
        //   from grupoestadoasesor 
        //  left join ".$compania.".Tercero as tercero
        //   on grupoestadoasesor.Tercero_idAsesor=tercero.idTercero");

                
            // for($i = 0; $i < count($asesore); $i++) 
            // {
            //   //$asesores= get_object_vars($asesore['$i']);
            //   $asesores= $asesore[][2];

            // }

            // echo json_encode($asesores);

        /*$asesores = DB::select("
            select idGrupoEstadoAsesor,GrupoEstado_idGrupoEstado, tercero.nombre1Tercero 
          from grupoestadoasesor 
         left join ".$compania.".Tercero as tercero
          on grupoestadoasesor.Tercero_idAsesor=tercero.idTercero")->where('tipoTercero','like','%05%')->lists('nombre1Tercero as nombreCompletoTercero','idTercero');*/



        // $supervisor = \App\Tercero::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('nombreCompletoTercero','idTercero');
        // $asesor = \App\Tercero::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('nombreCompletoTercero','idTercero');
        $acuerdoservicio = \App\AcuerdoServicio::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreAcuerdoServicio','idAcuerdoServicio');
        if($datos != null)
            return view('movimientocrmgrid', compact('datos','supervisor','asesor','acuerdoservicio','asesores'));
        else
            return view('movimientocrmgrid', compact('supervisor','asesor','acuerdoservicio','asesores'));
    }


    //Funcion para subir archivos con dropzone
    public function uploadFiles(Request $request) 
    {
 
        $input = Input::all();
 
        $rules = array(
        );
 
        $validation = Validator::make($input, $rules);
 
        if ($validation->fails()) {
            return Response::make($validation->errors->first(), 400);
        }
        
        $destinationPath = public_path() . '/imagenes/repositorio/temporal'; //Guardo en la carpeta  temporal

        $extension = Input::file('file')->getClientOriginalExtension(); 
        $fileName = Input::file('file')->getClientOriginalName(); // nombre de archivo
        $upload_success = Input::file('file')->move($destinationPath, $fileName);
 
        if ($upload_success) {
            return Response::json('success', 200);
        } 
        else {
            return Response::json('error', 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $idDocumento = $_GET["idDocumentoCRM"];
        $documento = \App\DocumentoCRM::where('idDocumentoCRM','=',$idDocumento)->lists('GrupoEstado_idGrupoEstado');
        
        // consultamos los maestros asociados a la compania
        $solicitante = DB::table(\Session::get("baseDatosCompania").'.Tercero')->lists('nombre1Tercero as nombreCompletoTercero','idTercero');
        // $solicitante = \App\Tercero::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('nombreCompletoTercero','idTercero');
        $lineanegocio = \App\LineaNegocio::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('nombreLineaNegocio','idLineaNegocio');
        
        // consultamos las tablas maestras que estan asociadas al grupo de estados, filtrando por el IDde grupo asociado al documentoCRM
        $estado = \App\EstadoCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreEstadoCRM','idEstadoCRM');
        $evento = \App\EventoCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreEventoCRM','idEventoCRM');
        $categoria = \App\CategoriaCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreCategoriaCRM','idCategoriaCRM');
        $origen = \App\OrigenCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreOrigenCRM','idOrigenCRM');

        $clasificacion=\App\ClasificacionCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreClasificacionCRM','idClasificacionCRM');
        

        

       return view('movimientocrm',compact('solicitante', 'categoria','documento','lineanegocio','origen','estado', 'evento','clasificacion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(MovimientoCRMRequest $request)
    {
        $numero = DB::select(
            "SELECT CONCAT(REPEAT('0', longitudDocumentoCRM - LENGTH(ultimo+1)), (ultimo+1)) as nuevo
            FROM 
            (
                SELECT IFNULL( MAX(numeroMovimientoCRM) , 0) as ultimo, longitudDocumentoCRM
                FROM  documentocrm D 
                LEFT JOIN movimientocrm M
                on D.idDocumentoCRM = M.DocumentoCRM_idDocumentoCRM
                where   D.Compania_idCompania = ".\Session::get('idCompania')." and 
                        DocumentoCRM_idDocumentoCRM = ".$request['DocumentoCRM_idDocumentoCRM']."
            ) temp");

        $numero = get_object_vars($numero[0])["nuevo"];
        \App\MovimientoCRM::create([
            'numeroMovimientoCRM' => $numero,
            'asuntoMovimientoCRM' => $request['asuntoMovimientoCRM'],
            'fechaSolicitudMovimientoCRM' => $request['fechaSolicitudMovimientoCRM'],
            'fechaEstimadaSolucionMovimientoCRM' => $request['fechaEstimadaSolucionMovimientoCRM'],
            'fechaVencimientoMovimientoCRM' => $request['fechaVencimientoMovimientoCRM'],
            'fechaRealSolucionMovimientoCRM' => $request['fechaRealSolucionMovimientoCRM'],
            'prioridadMovimientoCRM' => $request['prioridadMovimientoCRM'],
            'diasEstimadosSolucionMovimientoCRM' => $request['diasEstimadosSolucionMovimientoCRM'],
            'diasRealesSolucionMovimientoCRM' => $request['diasRealesSolucionMovimientoCRM'],
            'valorMovimientoCRM' => $request['valorMovimientoCRM'],
            'Tercero_idSolicitante' => ($request['Tercero_idSolicitante'] != ''  ? $request['Tercero_idSolicitante'] : null),
            'Tercero_idSupervisor' => ($request['Tercero_idSupervisor'] != '' ? $request['Tercero_idSupervisor'] : null),
            'Tercero_idAsesor' => ($request['Tercero_idAsesor'] != '' ? $request['Tercero_idAsesor'] : null),
            'CategoriaCRM_idCategoriaCRM' => ($request['CategoriaCRM_idCategoriaCRM'] != '' ? $request['CategoriaCRM_idCategoriaCRM'] : null),
            'EventoCRM_idEventoCRM' => ($request['EventoCRM_idEventoCRM'] != '' ? $request['EventoCRM_idEventoCRM'] : null),
            'DocumentoCRM_idDocumentoCRM' => ($request['DocumentoCRM_idDocumentoCRM'] != '' ? $request['DocumentoCRM_idDocumentoCRM'] : null),
            'LineaNegocio_idLineaNegocio' => ($request['LineaNegocio_idLineaNegocio'] != '' ? $request['LineaNegocio_idLineaNegocio'] : null),
            'OrigenCRM_idOrigenCRM' => ($request['OrigenCRM_idOrigenCRM'] != '' ? $request['OrigenCRM_idOrigenCRM'] : null),
            'EstadoCRM_idEstadoCRM' => ($request['EstadoCRM_idEstadoCRM'] != '' ? $request['EstadoCRM_idEstadoCRM'] : null),
            'AcuerdoServicio_idAcuerdoServicio' => ($request['AcuerdoServicio_idAcuerdoServicio'] != '' ? $request['AcuerdoServicio_idAcuerdoServicio'] : null),
            'ClasificacionCRM_idClasificacionCRM' => ($request['ClasificacionCRM_idClasificacionCRM'] != '' ? $request['ClasificacionCRM_idClasificacionCRM'] : null),
            'ClasificacionCRMDetalle_idClasificacionCRMDetalle' => ($request['ClasificacionCRMDetalle_idClasificacionCRMDetalle'] != '' ? $request['ClasificacionCRMDetalle_idClasificacionCRMDetalle'] : null),

            'detallesMovimientoCRM' => $request['detallesMovimientoCRM'],
            'solucionMovimientoCRM' => $request['solucionMovimientoCRM'],
            'Compania_idCompania' => \Session::get('idCompania')
            ]);

        $movimientocrm = \App\MovimientoCRM::All()->last();

        $movCRMUltimo = \App\MovimientoCRM::All()->last();

        echo count($request['idMovimientoCRMNota']);
        for ($i=0 ; $i < count($request['idMovimientoCRMNota']); $i++)
        {
            \App\MovimientoCRMNota::create([
           
            'MovimientoCRM_idMovimientoCRM'=>$movCRMUltimo ->idMovimientoCRM,
            'Users_idUsuario'=>$request['Users_idUsuario'][$i],
            'fechaMovimientoCRMNota'=>$request['fechaMovimientoCRMNota'][$i],
            'observacionMovimientoCRMNota'=>$request['observacionMovimientoCRMNota'][$i]
             ]); 

        }

        $this->grabarDetalle($movimientocrm->idMovimientoCRM, $request);

        $arrayImage = $request['archivoMovimientoCRMArray'];
        $arrayImage = substr($arrayImage, 0, strlen($arrayImage)-1);
        $arrayImage = explode(",", $arrayImage);
        $ruta = '';
       for ($i=0; $i < count($arrayImage) ; $i++) 
        { 
            if ($arrayImage[$i] != '' || $arrayImage[$i] != 0) 
            {
                $origen = public_path() . '/imagenes/repositorio/temporal/'.$arrayImage[$i];
                $destinationPath = public_path() . '/imagenes/movimientocrm/'.$arrayImage[$i];
                $ruta = '/movimientocrm/'.$arrayImage[$i];
               
                if (file_exists($origen))
                {
                    copy($origen, $destinationPath);
                    unlink($origen);
                }   
                else
                {
                    echo "No existe el archivo";
                }
                \App\MovimientoCRMArchivo::create([
                'MovimientoCRM_idMovimientoCRM' => $movimientocrm->idMovimientoCRM,
                'rutaMovimientoCRMArchivo' => $ruta
               ]);
            }

        }

        // en esta parte es el guardado de la multiregistro VACANTES
        //Primero consultar el ultimo id guardado
        // $MovimientoCRM = \App\MovimientoCRM::All()->last();
        //for para guardar cada registro de la multiregistro

        for ($i=0; $i < count($request['nombreCargo']); $i++) 
        { 
             \App\MovimientoCRMCargos::create([
            'MovimientoCRM_idMovimientoCRM' => $movimientocrm->idMovimientoCRM,
            'Cargo_idCargo' => $request['Cargo_idCargo'][$i],
            'vacantesMovimientoCRMCargo' => $request['vacantesMovimientoCRMCargo'][$i],
            'fechaEstimadaMovimientoCRMCargo' => $request['fechaEstimadaMovimientoCRMCargo'][$i]
            ]);
        }

         echo '/movimientocrm?idDocumentoCRM='.$request['DocumentoCRM_idDocumentoCRM'];
        return;
        //********************************
        //
        // Envio de Correo con MovimientoCRM
        //
        //********************************
        // consultamos el correo del usuario logueado y los correos de los usuarios aprobadores de este documento
        $correos = DB::select('
            SELECT  email as correoElectronicoTercero
                FROM    users U 
                WHERE   (U.Tercero_idAsociado = '.$request['Tercero_idSolicitante'].' '.
                        ($request['Tercero_idAsesor'] != '' ? ' or U.Tercero_idAsociado = '.$request['Tercero_idAsesor'] : '').
                        ') and
                        email != "" 
            UNION DISTINCT
            SELECT  email as correoElectronicoTercero 
                FROM documentocrmrol DR
                LEFT JOIN users U
                ON DR.Rol_idRol = U.Rol_idRol
                WHERE   aprobarDocumentoCRMRol = 1 and 
                        DocumentoCRM_idDocumentoCRM = '.$request['DocumentoCRM_idDocumentoCRM'].' and 
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
            $idDocumentoCRM= $request['DocumentoCRM_idDocumentoCRM'];
            $datos['asunto'] = Session::get('baseDatosCompania').' Nuevo Caso CRM: '.$request['asuntoMovimientoCRM'];
            $datos['mensaje'] ='Se ha generado el reporte del caso CRM:'.$request['asuntoMovimientoCRM'];
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
                <p> '.$this->generarHTMLFormato($movimientocrm->idMovimientoCRM, $idDocumentoCRM).'</p>
            </body>
            </html>';
            $nombreAdj=$request['asuntoMovimientoCRM'].'.html';
            $adj=fopen($nombreAdj,"w");
            fputs($adj, $contenidoArchivo );
            fclose($adj);

            $datos['adjunto'] = $nombreAdj;
             Mail::send('correocrm',$datos,function($msj) use ($datos)
            {
            	
                $msj->to($datos['correos']);
                $msj->subject($datos['asunto']);
                
                $msj->attach($datos['adjunto']);
                
            });
            unlink($nombreAdj);
        }


        //return redirect('/movimientocrm?idDocumentoCRM='.$request['DocumentoCRM_idDocumentoCRM']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if(isset($_GET['accion']) and $_GET['accion'] == 'imprimir')
        {
            //$movimientocrm = \App\MovimientoCRM::find($id);

            

            $idDocumentoCRM= $_GET['idDocumentoCRM'];
            
            $formatoHTML = $this->generarHTMLFormato($id, $idDocumentoCRM);
            
            return view('formatos.formatomovimientocrm',['movimientocrm'=>$formatoHTML], compact('idDocumentoCRM'));
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
     * @return Response
     */
    public function edit($id)
    {
        $movimientocrm = \App\MovimientoCRM::find($id);

        $idDocumento = $_GET["idDocumentoCRM"];
        $documento = \App\DocumentoCRM::where('idDocumentoCRM','=',$idDocumento)->lists('GrupoEstado_idGrupoEstado');
       

        // consultamos los maestros asociados a la compania
        // consultamos los maestros asociados a la compania
       /* $notacrm= DB::Select('
            SELECT idMovimientoCRMNota, MovimientoCRM_idMovimientoCRM, Users_idUsuario, fechaMovimientoCRMNota, observacionMovimientoCRMNota
            FROM movimientocrmnota 
            WHERE MovimientoCRM_idMovimientoCRM = '.$id);

             for ($i=0 ; $i < count( $notacrm); $i++) 
        {  
            $nota[] = get_object_vars($notacrm[$i]);
        }
        */
        $nota= \App\MovimientoCRMNota::where('MovimientoCRM_idMovimientoCRM','=',$id)->get();
          

        $solicitante = DB::table(\Session::get("baseDatosCompania").'.Tercero')->lists('nombre1Tercero as nombreCompletoTercero','idTercero');
        $lineanegocio = \App\LineaNegocio::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('nombreLineaNegocio','idLineaNegocio');
        
        // consultamos las tablas maestras que estan asociadas al grupo de estados, filtrando por el IDde grupo asociado al documentoCRM
        $estado = \App\EstadoCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreEstadoCRM','idEstadoCRM');
        $evento = \App\EventoCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreEventoCRM','idEventoCRM');
        $categoria = \App\CategoriaCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreCategoriaCRM','idCategoriaCRM');
        $origen = \App\OrigenCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreOrigenCRM','idOrigenCRM');
         $clasificacion=\App\ClasificacionCRM::where('GrupoEstado_idGrupoEstado','=',$documento[0])->lists('nombreClasificacionCRM','idClasificacionCRM');
        // Consulto  los necesarios el FROM en la tabla principal movimientocrmcargo, luego los envio al blade para llenar los respectivos Datos
        $movimientocrmcargo = DB::Select('
            SELECT idMovimientoCRMCargo,nombreCargo,Cargo_idCargo,vacantesMovimientoCRMCargo,salarioBaseCargo,fechaEstimadaMovimientoCRMCargo
            FROM movimientocrmcargo MC 
            left join  movimientocrm M 
            on MC.MovimientoCRM_idMovimientoCRM = M.idMovimientoCRM 
            left join cargo C  
            on MC.Cargo_idCargo = C.idCargo
            WHERE idMovimientoCRM = '.$id);


        // print_r($movimientocrmcargo);



        return view('movimientocrm',compact('solicitante', 'categoria','documento','lineanegocio','origen','estado', 'evento','movimientocrmcargo','clasificacion','nota'),['movimientocrm'=>$movimientocrm]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(MovimientoCRMRequest $request, $id)
    {

        $movimientocrm = \App\MovimientoCRM::find($id);
        $movimientocrm->fill($request->all());
        $movimientocrm->Tercero_idSolicitante = ($request['Tercero_idSolicitante'] != ''  ? $request['Tercero_idSolicitante'] : null);
        $movimientocrm->Tercero_idSupervisor = ($request['Tercero_idSupervisor'] != '' ? $request['Tercero_idSupervisor'] : null);
        $movimientocrm->Tercero_idAsesor = ($request['Tercero_idAsesor'] != '' ? $request['Tercero_idAsesor'] : null);
        $movimientocrm->CategoriaCRM_idCategoriaCRM = ($request['CategoriaCRM_idCategoriaCRM'] != '' ? $request['CategoriaCRM_idCategoriaCRM'] : null);
        $movimientocrm->EventoCRM_idEventoCRM = ($request['EventoCRM_idEventoCRM'] != '' ? $request['EventoCRM_idEventoCRM'] : null);
        $movimientocrm->DocumentoCRM_idDocumentoCRM = ($request['DocumentoCRM_idDocumentoCRM'] != '' ? $request['DocumentoCRM_idDocumentoCRM'] : null);
        $movimientocrm->LineaNegocio_idLineaNegocio = ($request['LineaNegocio_idLineaNegocio'] != '' ? $request['LineaNegocio_idLineaNegocio'] : null);
        $movimientocrm->OrigenCRM_idOrigenCRM = ($request['OrigenCRM_idOrigenCRM'] != '' ? $request['OrigenCRM_idOrigenCRM'] : null);
        $movimientocrm->EstadoCRM_idEstadoCRM = ($request['EstadoCRM_idEstadoCRM'] != '' ? $request['EstadoCRM_idEstadoCRM'] : null);
        $movimientocrm->AcuerdoServicio_idAcuerdoServicio = ($request['AcuerdoServicio_idAcuerdoServicio'] != '' ? $request['AcuerdoServicio_idAcuerdoServicio'] : null);
        $movimientocrm->ClasificacionCRM_idClasificacionCRM = ($request['ClasificacionCRM_idClasificacionCRM'] != '' ? $request['ClasificacionCRM_idClasificacionCRM'] : null);
        $movimientocrm->ClasificacionCRMDetalle_idClasificacionCRMDetalle = ($request['ClasificacionCRMDetalle_idClasificacionCRMDetalle'] != '' ? $request['ClasificacionCRMDetalle_idClasificacionCRMDetalle'] : null);



        $movimientocrm->save();
         echo '/movimientocrm?idDocumentoCRM='.$request['DocumentoCRM_idDocumentoCRM'];
return;

        $this->grabarDetalle($id, $request);
        // HAGO UN INSERT A LOS NUEVOS ARCHIVOS SUBIDOS EN EL DROPZONE
        if ($request['archivoMovimientoCRMArray'] != '') 
        {
            $arrayImage = $request['archivoMovimientoCRMArray'];
            $arrayImage = substr($arrayImage, 0, strlen($arrayImage)-1);
            $arrayImage = explode(",", $arrayImage);
            $ruta = '';

            for($i = 0; $i < count($arrayImage); $i++)
            {
                if ($arrayImage[$i] != '' || $arrayImage[$i] != 0) 
                {
                    $origen = public_path() . '/imagenes/repositorio/temporal/'.$arrayImage[$i];
                    $destinationPath = public_path() . '/imagenes/movimientocrm/'.$arrayImage[$i];
                    
                    if (file_exists($origen))
                    {
                        copy($origen, $destinationPath);
                        unlink($origen);
                        $ruta = '/movimientocrm/'.$arrayImage[$i];

                        DB::table('movimientocrmarchivo')->insert([
                            'idMovimientoCRMArchivo' => '0', 
                            'MovimientoCRM_idMovimientoCRM' =>$id,
                            'rutaMovimientoCRMArchivo' => $ruta]);
                    }   
                    else
                    {
                        echo "No existe el archivo";
                    }
                }
            }
        }

        // ELIMINO LOS ARCHIVOS
        $idsEliminar = $request['eliminarArchivo'];
        $idsEliminar = substr($idsEliminar, 0, strlen($idsEliminar)-1);
        if($idsEliminar != '')
        {
            $idsEliminar = explode(',',$idsEliminar);
            \App\MovimientoCRMArchivo::whereIn('idMovimientoCRMArchivo',$idsEliminar)->delete();
        }

        //  .. .. .. .. .. .. .. .. .. ... .. .. .. .. ... ... ... ... . .. . .. . .. . . .. . . . . . .
        $idsEliminar = explode("," , $request['eliminardocumentocrmcargo']);
        //Eliminar registros de la multiregistro
        \App\MovimientoCRMCargos::whereIn('idMovimientoCRMCargo', $idsEliminar)->delete();

        


        // Guardamos el detalle de los modulos
        for($i = 0; $i < count($request['idMovimientoCRMCargo']); $i++)
        {
             $indice = array(
                'idMovimientoCRMCargo' => $request['idMovimientoCRMCargo'][$i]);

            $data = array(
                'MovimientoCRM_idMovimientoCRM' => $id,
                'Cargo_idCargo' => $request['Cargo_idCargo'][$i],
                'vacantesMovimientoCRMCargo' => $request['vacantesMovimientoCRMCargo'][$i],
                'fechaEstimadaMovimientoCRMCargo' => $request['fechaEstimadaMovimientoCRMCargo'][$i]);


            $guardar = \App\MovimientoCRMCargos::updateOrCreate($indice, $data);
        } 



         $correos = DB::select('
            SELECT  email as correoElectronicoTercero
                FROM    users U 
                WHERE   (U.Tercero_idAsociado = '.$request['Tercero_idSolicitante'].' '.
                        ($request['Tercero_idAsesor'] != '' ? ' or U.Tercero_idAsociado = '.$request['Tercero_idAsesor'] : '').
                        ') and
                        email != "" 
            UNION DISTINCT
            SELECT  email as correoElectronicoTercero 
                FROM documentocrmrol DR
                LEFT JOIN users U
                ON DR.Rol_idRol = U.Rol_idRol
                WHERE   aprobarDocumentoCRMRol = 1 and 
                        DocumentoCRM_idDocumentoCRM = '.$request['DocumentoCRM_idDocumentoCRM'].' and 
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
            $idDocumentoCRM= $request['DocumentoCRM_idDocumentoCRM'];
            $datos['asunto'] = Session::get('baseDatosCompania').' Modificación Caso CRM: '.$request['asuntoMovimientoCRM'];
            $datos['mensaje'] ='Se ha generado el reporte del caso CRM:'.$request['asuntoMovimientoCRM'];
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
                <p> '.$this->generarHTMLFormato($movimientocrm->idMovimientoCRM, $idDocumentoCRM).'</p>
            </body>
            </html>';
            $nombreAdj=$request['asuntoMovimientoCRM'].'.html';
            $adj=fopen($nombreAdj,"w");
            fputs($adj, $contenidoArchivo );
            fclose($adj);

            $datos['adjunto'] = $nombreAdj;
             Mail::send('correocrm',$datos,function($msj) use ($datos)
            {
                
                $msj->to($datos['correos']);
                $msj->subject($datos['asunto']);
                
                $msj->attach($datos['adjunto']);
                
            });
            unlink($nombreAdj);
        }

        
        
      
        //return redirect('/movimientocrm?idDocumentoCRM='.$request['DocumentoCRM_idDocumentoCRM']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        // con el id de movimiento consultamos que documento crm es 
        // para ponerlo en el parametro del redirect
        $documentocrm = DB::select(
                    'SELECT DocumentoCRM_idDocumentoCRM
                    FROM movimientocrm
                    WHERE idMovimientoCRM = '.$id);

        $documentocrm = get_object_vars($documentocrm[0]);
        
        \App\MovimientoCRM::destroy($id);
        return redirect('/movimientocrm?idDocumentoCRM='.$documentocrm['DocumentoCRM_idDocumentoCRM']);
    }

    public function grabarDetalle($id, $request)
    {
        $diasEstimados = DB::update(
            "UPDATE
             movimientocrm 
            SET diasEstimadosSolucionMovimientoCRM = HOUR(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, fechaSolicitudMovimientoCRM, fechaEstimadaSolucionMovimientoCRM)))/24 
            WHERE fechaEstimadaSolucionMovimientoCRM != '0000-00-00 00:00:00' AND 
                idMovimientoCRM = ".$id);

        $diasReales = DB::update(
            "UPDATE
             movimientocrm 
            LEFT JOIN estadocrm ON EstadoCRM_idEstadoCRM = idEstadoCRM
            SET fechaRealSolucionMovimientoCRM = NOW(),
                diasRealesSolucionMovimientoCRM = HOUR(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, fechaSolicitudMovimientoCRM, NOW())))/24 
            WHERE tipoEstadoCRM IN ('Exitoso','Fallido','Cancelado') AND 
                IFNULL(diasRealesSolucionMovimientoCRM,0) = 0 AND 
                idMovimientoCRM = ".$id);

        $contadorAsistente = count($request['nombreMovimientoCRMAsistente']);
        for($i = 0; $i < $contadorAsistente; $i++)
        {

            $indice = array(
                'idMovimientoCRMAsistente' => $request['idMovimientoCRMAsistente'][$i]);

            $data = array(
                'MovimientoCRM_idMovimientoCRM' => $id,
            'nombreMovimientoCRMAsistente' => $request['nombreMovimientoCRMAsistente'][$i],
            'cargoMovimientoCRMAsistente' => $request['cargoMovimientoCRMAsistente'][$i],
            'telefonoMovimientoCRMAsistente' => $request['telefonoMovimientoCRMAsistente'][$i],
            'correoElectronicoMovimientoCRMAsistente' => $request['correoElectronicoMovimientoCRMAsistente'][$i]);

            $respuesta = \App\MovimientoCRMAsistente::updateOrCreate($indice, $data);

        }

        $movCRMUltimo = \App\MovimientoCRM::All()->last();

        echo count($request['idMovimientoCRMNota']);
        for ($i=0 ; $i < count($request['idMovimientoCRMNota']); $i++)
        {
            \App\MovimientoCRMNota::create([
           
            'MovimientoCRM_idMovimientoCRM'=>$movCRMUltimo ->idMovimientoCRM,
            'Users_idUsuario'=>$request['Users_idUsuario'][$i],
            'fechaMovimientoCRMNota'=>$request['fechaMovimientoCRMNota'][$i],
            'observacionMovimientoCRMNota'=>$request['observacionMovimientoCRMNota'][$i]
             ]); 

        }


    }

    public function guardarAsesorMovimientoCRM()
    {
        $movimientocrm = \App\MovimientoCRM::find($_POST["idMovimientoCRM"]);
        $movimientocrm->Tercero_idSupervisor = $_POST["idSupervisor"];
        $movimientocrm->Tercero_idAsesor = $_POST["idAsesor"];
        $movimientocrm->AcuerdoServicio_idAcuerdoServicio = ($_POST["idAcuerdo"] == '' ? null :  $_POST["idAcuerdo"]);
        $movimientocrm->diasEstimadosSolucionMovimientoCRM = $_POST["diasAcuerdo"];
        $movimientocrm->save();

        // consultamos los campos del movimiento necesarios apra enviar correo
        $movimiento = DB::select('
            SELECT  idMovimientoCRM, asuntoMovimientoCRM, detallesMovimientoCRM,
                    Tercero_idSolicitante, Tercero_idAsesor, DocumentoCRM_idDocumentoCRM
                FROM  movimientocrm
                WHERE idMovimientoCRM = '.$_POST["idMovimientoCRM"]);

        
        $datosmovimiento[] = get_object_vars($movimiento[0]);
        

        $correos = DB::select('
            SELECT  email as correoElectronicoTercero
                FROM    users U 
                WHERE   (U.Tercero_idAsociado = '.$datosmovimiento[0]['Tercero_idSolicitante'].' '.
                        ($datosmovimiento[0]['Tercero_idAsesor'] != '' ? ' or U.Tercero_idAsociado = '.$datosmovimiento[0]['Tercero_idAsesor'] : '').
                        ') and
                        email != "" 
            UNION DISTINCT
            SELECT  email as correoElectronicoTercero 
                FROM documentocrmrol DR
                LEFT JOIN users U
                ON DR.Rol_idRol = U.Rol_idRol
                WHERE   aprobarDocumentoCRMRol = 1 and 
                        DocumentoCRM_idDocumentoCRM = '.$datosmovimiento[0]['DocumentoCRM_idDocumentoCRM'].' and 
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
            $idDocumentoCRM= $datosmovimiento[0]['DocumentoCRM_idDocumentoCRM'];
            $datos['asunto'] = Session::get('baseDatosCompania').' Asignacion Asesor Caso CRM: '.$datosmovimiento[0]['asuntoMovimientoCRM'];
            $datos['mensaje'] ='Se ha generado el reporte del caso CRM:'.$datosmovimiento[0]['asuntoMovimientoCRM'];
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
                <p> '.$this->generarHTMLFormato($movimientocrm->idMovimientoCRM, $idDocumentoCRM).'</p>
            </body>
            </html>';
            $nombreAdj=$datosmovimiento[0]['asuntoMovimientoCRM'].'.html';
            $adj=fopen($nombreAdj,"w");
            fputs($adj, $contenidoArchivo );
            fclose($adj);

            $datos['adjunto'] = $nombreAdj;
             Mail::send('correocrm',$datos,function($msj) use ($datos)
            {
                
                $msj->to($datos['correos']);
                $msj->subject($datos['asunto']);
                
                $msj->attach($datos['adjunto']);
                
            });
            unlink($nombreAdj);
        }
        


        echo json_encode(array(true, 'Se ha guardado exitosamente'));
    }

    public function consultarAsesorMovimientoCRM()
    {
        $movimientocrm = DB::select(
            'SELECT Tercero_idSupervisor, 
                    nombre1Tercero as nombreCompletoSupervisor,
                    Tercero_idAsesor, 
                    AcuerdoServicio_idAcuerdoServicio, 
                    diasEstimadosSolucionMovimientoCRM
            FROM movimientocrm M
            LEFT JOIN '.\Session::get("baseDatosCompania").'.Tercero T
            ON M.Tercero_idSupervisor = T.idTercero
            WHERE idMovimientoCRM = '.$_POST["idMovimientoCRM"]);

        $movimientocrm = get_object_vars($movimientocrm[0]);

        echo json_encode($movimientocrm);
    }

    public function consultarDiasAcuerdoServicio()
    {
        $acuerdo = DB::select(
            'SELECT tiempoAcuerdoServicio
            FROM acuerdoservicio
            WHERE idAcuerdoServicio = '.$_POST["idAcuerdo"]);

        $acuerdo = get_object_vars($acuerdo[0]);

        echo json_encode($acuerdo);
    }

    public function Subclasificacion()
    {
     
        $id = (isset($_GET['idClasificacionCRM']) ? $_GET['idClasificacionCRM'] : 0);
        
        $subclasificacion = DB::select(
        'SELECT nombreClasificacionCRMDetalle, idClasificacionCRMDetalle
        FROM clasificacioncrmdetalle
        WHERE ClasificacionCRM_idClasificacionCRM = '.$id);

         $informe= array();
        for($i = 0; $i < count($subclasificacion); $i++) 
        {
          $informe[] = get_object_vars($subclasificacion[$i]);
        }

        echo json_encode($informe);
   
    }


    public function enviarcorreo(){
    $movimientocrm = \App\MovimientoCRM::find($id);

    }


    protected function generarHTMLFormato($idmovimientocrm, $id)
    {
        $movimientocrm = DB::select(
            "SELECT 
                solicitante.documentoTercero AS documentoSolicitante,
                solicitante.nombre1Tercero nombreSolicitante,
                supervisor.documentoTercero AS documentoSupervisor,
                supervisor.nombre1Tercero AS nombreSupervisor,
                asesor.documentoTercero AS documentoAsesor,
                asesor.nombre1Tercero AS nombreAsesor,
                categoriacrm.nombreCategoriaCRM,
                documentocrm.nombreDocumentoCRM,
                origencrm.nombreOrigenCRM,
                estadocrm.nombreEstadoCRM,
                lineanegocio.nombreLineaNegocio,
                acuerdoservicio.nombreAcuerdoServicio,
                acuerdoservicio.tiempoAcuerdoServicio,
                acuerdoservicio.unidadTiempoAcuerdoServicio,
                eventocrm.nombreEventoCRM,
                movimientocrm.numeroMovimientoCRM,
                movimientocrm.asuntoMovimientoCRM,
                movimientocrm.fechaSolicitudMovimientoCRM,
                movimientocrm.fechaEstimadaSolucionMovimientoCRM,
                movimientocrm.fechaVencimientoMovimientoCRM,
                movimientocrm.fechaRealSolucionMovimientoCRM,
                movimientocrm.prioridadMovimientoCRM,
                movimientocrm.diasEstimadosSolucionMovimientoCRM,
                movimientocrm.diasRealesSolucionMovimientoCRM,
                movimientocrm.detallesMovimientoCRM,
                movimientocrm.solucionMovimientoCRM,
                movimientocrm.valorMovimientoCRM
            FROM
                movimientocrm
                    LEFT JOIN
                ".\Session::get("baseDatosCompania").".Tercero solicitante ON movimientocrm.Tercero_idSolicitante = solicitante.idTercero
                    LEFT JOIN
                ".\Session::get("baseDatosCompania").".Tercero supervisor ON movimientocrm.Tercero_idSupervisor = supervisor.idTercero
                    LEFT JOIN
                ".\Session::get("baseDatosCompania").".Tercero asesor ON movimientocrm.Tercero_idAsesor = asesor.idTercero
                    LEFT JOIN
                categoriacrm ON movimientocrm.CategoriaCRM_idCategoriaCRM = categoriacrm.idCategoriaCRM
                    LEFT JOIN
                documentocrm ON movimientocrm.DocumentoCRM_idDocumentoCRM = documentocrm.idDocumentoCRM
                    LEFT JOIN
                lineanegocio ON movimientocrm.LineaNegocio_idLineaNegocio = lineanegocio.idLineaNegocio
                    LEFT JOIN
                origencrm ON movimientocrm.OrigenCRM_idOrigenCRM = origencrm.idOrigenCRM
                    LEFT JOIN
                estadocrm ON movimientocrm.EstadoCRM_idEstadoCRM = estadocrm.idEstadoCRM
                    LEFT JOIN
                acuerdoservicio ON movimientocrm.AcuerdoServicio_idAcuerdoServicio = acuerdoservicio.idAcuerdoServicio
                    LEFT JOIN
                eventocrm ON movimientocrm.EventoCRM_idEventoCRM = eventocrm.idEventoCRM
                    LEFT JOIN
                clasificacioncrm ON movimientocrm.ClasificacionCRM_idClasificacionCRM = clasificacioncrm.idClasificacionCRM
                    LEFT JOIN
                clasificacioncrmdetalle ON movimientocrm.ClasificacionCRMDetalle_idClasificacionCRMDetalle = clasificacioncrmdetalle.idClasificacionCRMDetalle
                WHERE idMovimientoCRM = $idmovimientocrm");

        
        
        $html = '';
        

        $campos = DB::select(
            'SELECT codigoDocumentoCRM, nombreDocumentoCRM, nombreCampoCRM,descripcionCampoCRM, 
                    mostrarGridDocumentoCRMCampo, relacionTablaCampoCRM, relacionNombreCampoCRM, relacionAliasCampoCRM
            FROM documentocrm
            left join documentocrmcampo
            on documentocrm.idDocumentoCRM = documentocrmcampo.DocumentoCRM_idDocumentoCRM
            left join campocrm
            on documentocrmcampo.CampoCRM_idCampoCRM = campocrm.idCampoCRM
            where documentocrm.idDocumentoCRM = '.$id.' and mostrarVistaDocumentoCRMCampo = 1');


        $datos = array();
        $camposVista = '';
        for($i = 0; $i < count($campos); $i++)
        {
            $datos = get_object_vars($campos[$i]); 
            
            $camposVista .= $datos["nombreCampoCRM"].',';
        }

        $idMovimientoCRMA = (isset($movimientocrm->idMovimientoCRM) ? $movimientocrm->idMovimientoCRM : 0);



        $movimiento = array();
        for($i = 0; $i < count($movimientocrm); $i++)
        {
            $movimiento[] = get_object_vars($movimientocrm[$i]); 
        }


        $html .= '<div id="form-section" >
                        <fieldset id="movimientocrm-form-fieldset"> 
                         <center><div class="container"><b><h3>'
                             .$movimiento[0]["nombreDocumentoCRM"].
                        '</b></h3></center></div><br>

                            <div class="form-group" id="test">
                                <div class="col-sm-6">
                                    <div class="col-sm-4">
                                Número
                            </div>
                                <div class="col-sm-8">
                                    <div class="input-group">'
                                    .$movimiento[0]["numeroMovimientoCRM"].
                                '</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="col-sm-4">
                                Asunto
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">'
                                    .$movimiento[0]["asuntoMovimientoCRM"].
                                '</div>
                                </div>
                            </div>'
                        ;
                        
        if(strpos($camposVista, 'OrigenCRM_idOrigenCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Origen 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreOrigenCRM"].'
                                </div>
                            </div>
                        </div>';
                            }
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                F. Elaboración
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["fechaSolicitudMovimientoCRM"].'
                                </div>
                            </div>
                        </div>';

        if(strpos($camposVista, 'fechaEstimadaSolucionMovimientoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Estimada 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["fechaEstimadaSolucionMovimientoCRM"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'fechaVencimientoMovimientoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                F. Vencimiento 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["fechaVencimientoMovimientoCRM"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'fechaRealSolucionMovimientoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                F. Real Solución 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["fechaRealSolucionMovimientoCRM"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'AcuerdoServicio_idAcuerdoServicio') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Acuerdo de Servicio 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreAcuerdoServicio"].'

                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'diasEstimadosSolucionMovimientoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Días Est. Solución
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["diasEstimadosSolucionMovimientoCRM"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'diasRealesSolucionMovimientoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Días Reales Solución 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["diasRealesSolucionMovimientoCRM"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'prioridadMovimientoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Prioridad 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["prioridadMovimientoCRM"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'Tercero_idSolicitante') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Solicitante 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreSolicitante"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'Tercero_idSupervisor') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Supervisor 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreSupervisor"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'Tercero_idAsesor') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Asesor 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreAsesor"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'CategoriaCRM_idCategoriaCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Categoría 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreCategoriaCRM"].'

                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'EventoCRM_idEventoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Evento / Campaña 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreEventoCRM"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'LineaNegocio_idLineaNegocio') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Línea de Negocio 
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreLineaNegocio"].'
                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'valorMovimientoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Valor
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["valorMovimientoCRM"].'

                                </div>
                            </div>
                        </div>';
                            }

        if(strpos($camposVista, 'EstadoCRM_idEstadoCRM') !== false)
                            { 
                        
            $html .= '<div class="col-sm-6">
                            <div class="col-sm-4">
                                Estado
                            </div>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    '.$movimiento[0]["nombreEstadoCRM"].'

                                </div>
                            </div>
                        </div>';
                            }

    if(strpos($camposVista, 'detallesMovimientoCRM') !== false)
                        { 
                    
            $html .= '<div id="detalles" class="panel panel-primary">
                        <div class="col-sm-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <i class="fa fa-pencil-square-o"></i> 
                                    Detalles 
                                </div>
                                <div class="panel-body">
                                    
                                    <div class="col-sm-12">
                                          '.$movimiento[0]["detallesMovimientoCRM"].'
                                    </div>

                                </div>
                            </div>
                        </div>
                      </div>';
                        }
                    
    if(strpos($camposVista, 'solucionMovimientoCRM') !== false)
                        { 
                    
        $html .= '<div id="solucion" class="panel panel-primary">
                        <div class="col-sm-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <i class="fa fa-pencil-square-o"></i> 
                                    Solución 
                                </div>
                                <div class="panel-body">
                                    
                                    <div class="col-sm-12">
                                          '.$movimiento[0]["solucionMovimientoCRM"].'
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>';
                        }

    $html .= '<div id="adjunto" class="panel panel-primary">
        <div class="col-sm-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <i class="fa fa-pencil-square-o"></i> 
                    Archivos Adjuntos 
                </div>
                <div class="panel-body">
                    <div class="col-sm-12">';
                 
                       $archivoSave = DB::Select('SELECT * from movimientocrmarchivo where MovimientoCRM_idMovimientoCRM = '.$idmovimientocrm);

                                for ($i=0; $i <count($archivoSave) ; $i++) 
                                { 
                                    $archivoS = get_object_vars($archivoSave[$i]);

                                    $html .= '
                                    <a title="Visualizar" target="_blank" 
                                        href="http://'.$_SERVER["HTTP_HOST"].'/imagenes'.$archivoS['rutaMovimientoCRMArchivo'].'">- '
                                    .str_replace('/movimientocrm/','',$archivoS['rutaMovimientoCRMArchivo']).'
                                    <br></a>';
                                                            
                                }'

                    </div>
                </div>
            </div>
        </div>
    </div>';                     
if(strpos($camposVista, 'asistentesMovimientoCRM') !== false)
                        { 
        
        $html .= '<div id="solucion" class="panel panel-primary">
                        <div class="col-sm-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <i class="fa fa-pencil-square-o"></i> 
                                    Asistentes 
                                </div>
                                <div class="panel-body">
                                    
                                    <div class="col-sm-12">
                                        <table >
                                            <tr>
                                                <td style="width: 330px;">Nombre</td>
                                                <td style="width: 270px;">Cargo</td>
                                                <td style="width: 150px;">Tel&eacute;fono</td>
                                                <td style="width: 230px;">Correo</td>
                                            </tr>';
                                            
                                                
                                                $asistentes = DB::select(
                                                        'SELECT nombreMovimientoCRMAsistente, cargoMovimientoCRMAsistente, telefonoMovimientoCRMAsistente, correoElectronicoMovimientoCRMAsistente
                                                        FROM movimientocrmasistente
                                                        where MovimientoCRM_idMovimientoCRM = '.$idmovimientocrm);
print_r($asistentes);

                                                for($i = 0; $i < count($asistentes); $i++)
                                                {
                                                    //$datos = get_object_vars($campos[$i]); 
                                                    
                                                    $html .= '<tr><td>'.get_object_vars($asistentes[$i])['nombreMovimientoCRMAsistente'].'</td>';
                                                    $html .= '<td>'.get_object_vars($asistentes[$i])['cargoMovimientoCRMAsistente'].'</td>';
                                                    $html .= '<td>'.get_object_vars($asistentes[$i])['telefonoMovimientoCRMAsistente'].'</td>';
                                                    $html .= '<td>'.get_object_vars($asistentes[$i])['correoElectronicoMovimientoCRMAsistente'].'</td>';
                                                    $html .= '</tr>';
                                                }
                                            
                            $html .= '</table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>';
                        }
                    
                    
        $html .= '</div>
                </fieldset> 
            </div> 
            </body>
            </html>'; 

            return  $html;

    }
    
       
}
