<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\EmbarqueRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';
include public_path().'/ajax/actualizarCartera.php';
use Mail;
use Input;
use File;
use Validator;
use Response;
use Session;
use Config;

class EmbarqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos("");

        if($datos != null)
            return view('embarquegrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    public function indexConsultaEmbarque()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos("");

        if($datos != null)
            return view('consultaembarquegrid', compact('datos'));
        else
            return view('accesodenegado');

    }

    public function indexCompraGrid()
    {
        return view('compragridselect'); 
    }

    //Lamo al dropzone
    public function indexdropzone() 
    {
        return view('dropzone');
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
        
        $destinationPath = public_path() . '/repositorio/temporal'; //Guardo en la carpeta  temporal

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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $documentoimportacion = \App\DocumentoImportacion::All()->lists('nombreDocumentoImportacion', 'idDocumentoImportacion');
        $idcompra = \App\Compra::All()->lists('nombreCompra','idCompra');

        return view('embarque',compact('documentoimportacion','compra'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmbarqueRequest $request)
    {
        if($request['respuesta'] != 'falso')
        {
            \App\Embarque::create([
            'numeroEmbarque' => $request['numeroEmbarque'],
            'sufijoEmbarque' => $request['sufijoEmbarque'],
            'fechaElaboracionEmbarque' => $request['fechaElaboracionEmbarque'],
            'tipoTransporteEmbarque' => $request['tipoTransporteEmbarque'],
            'TipoTransporte_idTipoTransporte' => $request['TipoTransporte_idTipoTransporte'],
            'puertoCargaEmbarque' => $request['puertoCargaEmbarque'],
            'Ciudad_idPuerto_Carga' => $request['Ciudad_idPuerto_Carga'],
            'puertoDescargaEmbarque' => $request['puertoDescargaEmbarque'],
            'Ciudad_idPuerto_Descarga' => $request['Ciudad_idPuerto_Descarga'],
            'Tercero_idTransportador' => $request['Tercero_idTransportador'],
            'agenteCargaEmbarque' => $request['agenteCargaEmbarque'],
            'Tercero_idAgenteCarga' => $request['Tercero_idAgenteCarga'],
            'navieraEmbarque' => $request['navieraEmbarque'],
            'Tercero_idNaviera' => $request['Tercero_idNaviera'],
            'fechaRealEmbarque' => $request['fechaRealEmbarque'],
            'bodegaEmbarque' => ($request['bodegaEmbarque'] == 'on') ? 1: 0,
            'otmEmbarque' => ($request['otmEmbarque'] == 'on') ? 1: 0,
            'volumenTotalEmbarque' => $request['volumenTotalEmbarque'],
            'valorTotalEmbarque' => $request['valorTotalEmbarque'],
            'unidadTotalEmbarque' => $request['unidadTotalEmbarque'],
            'pesoTotalEmbarque' => $request['pesoTotalEmbarque'],
            'bultoTotalEmbarque' => $request['bultoTotalEmbarque'],
            'DocumentoImportacion_idDocumentoImportacion' => $request['DocumentoImportacion_idDocumentoImportacion']
             ]);

            $embarque = \App\Embarque::All()->last();

            $this->grabarDetalle($embarque->idEmbarque, $request);

            return redirect('/embarque?idDocumento='.$request['DocumentoImportacion_idDocumentoImportacion']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // SE HACE UN SWITCH PARA DEFINIR UN FORMATO PARA LA COMPRA QUE SE ABRE CON LA PI EN EL MULTIREGISTRO O PARA EL EMBARQUE

        #Por request recibo el tipo de formato a mostrar, ya sea el de compra o el de embarque y el id del documento
        $tipo = $request['tipo'];
        $idDocumento = $request['documento'];

        switch ($tipo) 
        {
            case 'compra':
                $numeroCompra = $request['numero'];

                $compra = DB::Select('
                    SELECT 
                    *
                    FROM
                        (SELECT 
                            *
                        FROM
                            (SELECT 
                            *
                        FROM
                            compra
                        WHERE
                            DocumentoImportacion_idDocumentoImportacion = '.$idDocumento.' and numeroCompra = "'.$numeroCompra.'" and idCompra = '.$id.') AS comp
                        GROUP BY numeroCompra, numeroVersionCompra
                        ORDER BY numeroVersionCompra DESC) AS compp
                    GROUP BY numeroCompra');

                return view('formatos.impresionProgramacionImportaciones',compact('compra'));
            break;

            case 'embarque':

                $embarque = DB::Select('
                    SELECT 
                        *
                    FROM
                        embarque
                    WHERE
                        idEmbarque = '.$id.'
                           AND DocumentoImportacion_idDocumentoImportacion = '.$idDocumento);

                $embarquedetalle = DB::Select('SELECT 
                    *
                FROM
                    embarquedetalle ed
                        LEFT JOIN
                    compra c ON c.idCompra = ed.Compra_idCompra
                WHERE
                    Embarque_idEmbarque = '.$id);

                return view('formatos.impresionEmbarque',compact('embarque','embarquedetalle'));
            break;
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
        $embarque = \App\Embarque::find($id);
        $documentoimportacion = \App\DocumentoImportacion::All()->lists('nombreDocumentoImportacion', 'idDocumentoImportacion');
        $compra = \App\Compra::All()->lists('nombreCompra','idCompra');
        
        return view('embarque',compact('documentoimportacion','compra'),['embarque' => $embarque]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EmbarqueRequest $request, $id)
    {
        if($request['respuesta'] != 'falso')
        {
            $embarque = \App\Embarque::find($id);
            $embarque->fill($request->all());
            $embarque->bodegaEmbarque = isset($request['bodegaEmbarque']) ? 1 : 0;
            $embarque->otmEmbarque = isset($request['otmEmbarque']) ? 1 : 0;
            $embarque->save();

            $this->grabarDetalle($id, $request);

            return redirect('/embarque?idDocumento='.$request['DocumentoImportacion_idDocumentoImportacion']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        \App\Embarque::destroy($id);
        return redirect('/embarque?idDocumento='.$request['DocumentoImportacion_idDocumentoImportacion']);
    }

    public function grabarDetalle($id, $request)
    {
        $idsEliminar = explode(',', $request['eliminarEmbarqueDetalle']);
        \App\EmbarqueDetalle::whereIn('idEmbarqueDetalle',$idsEliminar)->delete();
        $contadorEmbarque = count($request['valorEmbarqueDetalle']);
        for($i = 0; $i < $contadorEmbarque; $i++)
        {
            $index = array(
                'idEmbarqueDetalle' => $request['idEmbarqueDetalle'][$i]);

            $data= array(
                'Embarque_idEmbarque' => $id,
                'Compra_idCompra' => $request['Compra_idCompra'][$i],
                'proformaEmbarqueDetalle' => $request['proformaEmbarqueDetalle'][$i],
                'volumenEmbarqueDetalle' => $request['volumenEmbarqueDetalle'][$i],
                'valorEmbarqueDetalle' => $request['valorEmbarqueDetalle'][$i],
                'unidadEmbarqueDetalle' => $request['unidadEmbarqueDetalle'][$i],
                'pesoEmbarqueDetalle' => $request['pesoEmbarqueDetalle'][$i],
                'bultoEmbarqueDetalle' => $request['bultoEmbarqueDetalle'][$i],
                'facturaEmbarqueDetalle' => $request['facturaEmbarqueDetalle'][$i],
                'volumenFacturaEmbarqueDetalle' => $request['volumenFacturaEmbarqueDetalle'][$i],
                'valorFacturaEmbarqueDetalle' => $request['valorFacturaEmbarqueDetalle'][$i],
                'unidadFacturaEmbarqueDetalle' => $request['unidadFacturaEmbarqueDetalle'][$i],
                'pesoFacturaEmbarqueDetalle' => $request['pesoFacturaEmbarqueDetalle'][$i],
                'bultoFacturaEmbarqueDetalle' => $request['bultoFacturaEmbarqueDetalle'][$i],
                'fechaReservaEmbarqueDetalle' => $request['fechaReservaEmbarqueDetalle'][$i],
                'fechaRealEmbarqueDetalle' => $request['fechaRealEmbarqueDetalle'][$i],
                'fechaMaximaEmbarqueDetalle' => $request['fechaMaximaEmbarqueDetalle'][$i],
                'fechaLlegadaZonaFrancaEmbarqueDetalle' => $request['fechaLlegadaZonaFrancaEmbarqueDetalle'][$i],
                'compradorEmbarqueDetalle' => $request['compradorEmbarqueDetalle'][$i],
                'eventoEmbarqueDetalle' => $request['eventoEmbarqueDetalle'][$i],
                'dolarEmbarqueDetalle' => $request['dolarEmbarqueDetalle'][$i],
                'fechaArriboPuertoEstimadaEmbarqueDetalle' => $request['fechaArriboPuertoEstimadaEmbarqueDetalle'][$i],
                'fechaArriboPuertoEmbarqueDetalle' => $request['fechaArriboPuertoEmbarqueDetalle'][$i],
                'soportePagoEmbarqueDetalle' => $request['soportePagoEmbarqueDetalle'][$i],
                'compradorVendedorEmbarqueDetalle' => $request['compradorVendedorEmbarqueDetalle'][$i],
                'cantidadContenedorEmbarqueDetalle' => $request['cantidadContenedorEmbarqueDetalle'][$i],
                'tipoContenedorEmbarqueDetalle' => $request['tipoContenedorEmbarqueDetalle'][$i],
                'numeroContenedorEmbarqueDetalle' => $request['numeroContenedorEmbarqueDetalle'][$i],
                'blEmbarqueDetalle' => $request['blEmbarqueDetalle'][$i],
                'numeroCourrierEmbarqueDetalle' => $request['numeroCourrierEmbarqueDetalle'][$i],
                'pagoEmbarqueDetalle' => $request['pagoEmbarqueDetalle'][$i],
                'originalEmbarqueDetalle' => $request['originalEmbarqueDetalle'][$i],
                'descripcionEmbarqueDetalle' => $request['descripcionEmbarqueDetalle'][$i],
                'pagoCorreoEmbarqueDetalle' => $request['pagoCorreoEmbarqueDetalle'][$i],
                'fileEmbarqueDetalle' => $request['fileEmbarqueDetalle'][$i],
                'observacionEmbarqueDetalle' => $request['observacionEmbarqueDetalle'][$i]);
            
            $save = \App\EmbarqueDetalle::updateOrCreate($index, $data);

        }

        $campos = 'nombreTemporadaCompra, nombreProveedorCompra, numeroCompra, volumenCompra, valorCompra, cantidadCompra, cantidadEmbarcada, Faltante, pesoCompra, bultoCompra, fechaDeliveryCompra, idCompra, numeroVersionCompra, nombreClienteCompra, estadoCompra';

        $compra = DB::Select('
            SELECT '.
                $campos. '
            FROM
                (SELECT 
                    nombreTemporadaCompra, nombreProveedorCompra, numeroCompra, volumenCompra, valorCompra, cantidadCompra, pesoCompra, bultoCompra, fechaDeliveryCompra, idCompra, numeroVersionCompra, nombreClienteCompra, SUM(COALESCE(case when unidadFacturaEmbarqueDetalle = 0 then null else unidadFacturaEmbarqueDetalle end,unidadEmbarqueDetalle,0)) AS cantidadEmbarcada,
                        cantidadCompra - SUM(COALESCE(case when unidadFacturaEmbarqueDetalle = 0 then null else unidadFacturaEmbarqueDetalle end,unidadEmbarqueDetalle,0)) AS Faltante, estadoCompra
                FROM
                    compra c
                LEFT JOIN embarquedetalle ed ON ed.Compra_idCompra = c.idCompra
                LEFT JOIN embarque e ON ed.Embarque_idEmbarque = e.idEmbarque
                WHERE
                    c.DocumentoImportacion_idDocumentoImportacion = '.$request['DocumentoImportacion_idDocumentoImportacion'].' 
                GROUP BY numeroCompra , numeroVersionCompra
                ORDER BY numeroCompra , numeroVersionCompra DESC) AS comp
                GROUP BY numeroCompra
                HAVING estadoCompra = "Abierto" and Faltante <= 0');
                

            for ($i=0; $i < count($compra); $i++) 
            { 
                $idCompra = get_object_vars($compra[$i]);

                $update = DB::Select('UPDATE compra SET estadoCompra = "Cerrado"
                where idCompra = '.$idCompra['idCompra']);    
            }

            $this->enviarCorreoPagos($id);
            $this->enviarCorreoBodega($id);
            $this->enviarCorreoOTM($id);
            
    }

    public function enviarCorreoPagos($id)
    {
        $pagos = DB::Select('
            SELECT proformaEmbarqueDetalle, numeroCompra as numeroCompraEmbarqueDetalle, nombreClienteCompra, valorEmbarqueDetalle, facturaEmbarqueDetalle, valorFacturaEmbarqueDetalle, bultoFacturaEmbarqueDetalle, fechaArriboPuertoEstimadaEmbarqueDetalle, compradorVendedorEmbarqueDetalle, nombreProveedorCompra as proveedorTemporadaEmbarqueDetalle, numeroContenedorEmbarqueDetalle, dolarEmbarqueDetalle, pagoEmbarqueDetalle, pagoCorreoEmbarqueDetalle, descripcionEmbarqueDetalle, observacionEmbarqueDetalle, numeroEmbarque, navieraEmbarque, bodegaEmbarque, bodegaCorreoEmbarque, otmEmbarque, otmCorreoEmbarque, valorForwardCompra
            FROM embarquedetalle ed
            LEFT JOIN embarque e
            ON ed.Embarque_idEmbarque = e.idEmbarque
            LEFT JOIN compra c
            ON ed.Compra_idCompra = c.idCompra
            WHERE Embarque_idEmbarque = '.$id.'
            AND pagoEmbarqueDetalle = 1
            AND pagoCorreoEmbarqueDetalle = 0');

        if (count($pagos) == 0) 
        {
            return;
        }

        $datos = array();
        // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
        for ($i = 0, $c = count($pagos); $i < $c; ++$i) 
        {
            $datos[$i] = (array) $pagos[$i];
        }

        $i = 0;
        $detalle = count($datos);

        $styleTableEnc = 'style="border: 1px solid; background-color: #255986; color: white;"';
        $styleTableBody = 'style="border: 1px solid;"';
        $styleTableBodyN = 'style="border: 1px solid;  text-align: right;"';


        #Consulto los datos del correo tales como destinatario, asunto y mensaje
        $email = DB::Select('SELECT * from correoembarque where tipoCorreoEmbarque = "Pago"');
        $mail = get_object_vars($email[0]);
        $para = explode(';', $mail['destinatarioCorreoEmbarque']);

        $idSistemaInformacion = 1;

        $sinfo = DB::table('sistemainformacion')
        ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
        ->where('idSistemaInformacion', "=", $idSistemaInformacion)
        ->get();


        $sinfo = get_object_vars($sinfo[0]);
           
           Config::set( 'database.connections.'.$sinfo['bdSistemaInformacion'], array 
            ( 
                'driver'     =>  $sinfo['motorbdSistemaInformacion'], 
                'host'       =>  $sinfo['ipSistemaInformacion'], 
                'port'       =>  $sinfo['puertoSistemaInformacion'], 
                'database'   =>  $sinfo['bdSistemaInformacion'], 
                'username'   =>  $sinfo['usuarioSistemaInformacion'], 
                'password'   =>  $sinfo['claveSistemaInformacion'], 
                'charset'    =>  'utf8', 
                'collation'  =>  'utf8_unicode_ci', 
                'prefix'     =>  ''
            )); 

        $conexion = DB::connection($sinfo['bdSistemaInformacion'])->getDatabaseName();

        

        #En el array de mail, en la posicion 'mensaje' empiezo a acumular el cuerpo del mensaje
        $mail['mensaje'] = $mail['mensajeCorreoEmbarque'].'</br></br>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
        <table cellspacing="0" class="table table-striped table-bordered table-hover" style="width:100%;">
            <tr>
                <th colspan="11" style=" background-color:#255986; color:white;">Embarque: '.$datos[0]['numeroEmbarque'].'</th>
            </tr>
            <tr>
                <th '.$styleTableEnc.'>N° Embarque</th>
                <th '.$styleTableEnc.'>Proveedor</th>
                <th '.$styleTableEnc.'>Cliente</th>
                <th '.$styleTableEnc.'>Factura</th>
                <th '.$styleTableEnc.'>Valor</th>
                <th '.$styleTableEnc.'>IM</th>
                <th '.$styleTableEnc.'>Forward</th>
                <th '.$styleTableEnc.'>Dolar</th>
                <th '.$styleTableEnc.'>Descripción</th>
                <th '.$styleTableEnc.'>Arribo a puerto estimado</th>
                <th '.$styleTableEnc.'>Observación</th>
              </tr>';

            while ($i < $detalle)
            {
                #Consulto el numero de la IM en caso tal de que no exista en saya imprimo "No existe"    
                $numIM = DB::connection($sinfo['bdSistemaInformacion'])->Select('SELECT if(numeroMovimiento != "", numeroMovimiento,"No existe") as numeroMovimiento from Movimiento where numeroReferenciaExternoMovimiento = "'.$datos[$i]['facturaEmbarqueDetalle'].'" and Documento_idDocumento = 20');

                #Convierto el array a string validando de que exista o no el registro
                if($numIM != null)
                    $IM = get_object_vars($numIM[0]);
                else
                    $IM['numeroMovimiento'] = 'No Existe';

                $mail["mensaje"] .= '
                <tr>
                    <td '.$styleTableBody.'>'.$datos[$i]["numeroEmbarque"].'</td>
                    <td '.$styleTableBody.'>'.$datos[$i]["proveedorTemporadaEmbarqueDetalle"].'</td>
                    <td '.$styleTableBody.'>'.$datos[$i]["nombreClienteCompra"].'</td>
                    <td '.$styleTableBody.'>'.$datos[$i]["facturaEmbarqueDetalle"].'</td>
                    <td '.$styleTableBodyN.'>'.$datos[$i]["valorFacturaEmbarqueDetalle"].'</td>
                    <td '.$styleTableBody.'>'.$IM['numeroMovimiento'].'</td>
                    <td '.$styleTableBodyN.'>'.$datos[$i]['valorForwardCompra'].'</td>
                    <td '.$styleTableBodyN.'>'.$datos[$i]["dolarEmbarqueDetalle"].'</td>
                    <td '.$styleTableBody.'>'.$datos[$i]["descripcionEmbarqueDetalle"].'</td>
                    <td '.$styleTableBody.'>'.$datos[$i]["fechaArriboPuertoEstimadaEmbarqueDetalle"].'</td>
                    <td '.$styleTableBody.'>'.$datos[$i]["observacionEmbarqueDetalle"].'</td>
                </tr>';

                $i++;
            }
                $mail["mensaje"] .= 
                '
        </table>'; 

        $adjunto = DB::Select(
            'SELECT 
                archivoRadicadoVersion
            FROM
                documentoimportacioncorreo 
                    LEFT JOIN
                documento ON documentoimportacioncorreo.Documento_idDocumento = documento.idDocumento
                    LEFT JOIN
                documentopropiedad ON documento.idDocumento = documentopropiedad.Documento_idDocumento and indiceDocumentoPropiedad = 1
                    LEFT JOIN
                radicado ON documento.idDocumento = radicado.Documento_idDocumento 
                    LEFT JOIN
                radicadodocumentopropiedad on radicado.idRadicado = radicadodocumentopropiedad.Radicado_idRadicado 
                    LEFT JOIN
                radicadoversion on radicado.idRadicado = radicadoversion.Radicado_idRadicado
            where tipoDocumentoImportacionCorreo = "Pagos"
            and valorRadicadoDocumentoPropiedad = "'.$datos[0]["numeroEmbarque"].'"
            GROUP BY radicadoversion.Radicado_idRadicado');


        Mail::send('emails.contact',$mail,function($msj) use ($mail, $para, $adjunto)
        {
            $msj->to($para);
            $msj->subject($mail['asuntoCorreoEmbarque']);

            if (count($adjunto) > 0) 
            {
                for($i=0; $i < count($adjunto); $i++)
                {
                    $archivos = get_object_vars($adjunto[$i]);
                    $msj->attach($archivos['archivoRadicadoVersion']);
                }
            }

        }); 

        DB::Select('UPDATE embarquedetalle SET pagoCorreoEmbarqueDetalle = 1
                    where Embarque_idEmbarque = '.$id); 
        
    }

    public function enviarCorreoBodega($id)
    {
        $contenedores = DB::Select('
            SELECT numeroContenedorEmbarqueDetalle
            FROM embarquedetalle
            WHERE Embarque_idEmbarque = '.$id);

        $total = count($contenedores);
        $posicion = (int)$total-1;
        
        for ($j=0; $j < $total; $j++) 
        { 
            $numeroCont = get_object_vars($contenedores[$j]);
            

            $bodega = DB::Select('
            SELECT numeroEmbarque, nombreTemporadaCompra as nombreTemporadaEmbarqueDetalle, nombreProveedorCompra as proveedorTemporadaEmbarqueDetalle, numeroCompra as numeroCompraEmbarqueDetalle, valorEmbarqueDetalle, facturaEmbarqueDetalle, valorFacturaEmbarqueDetalle, unidadFacturaEmbarqueDetalle, bultoFacturaEmbarqueDetalle, navieraEmbarque, descripcionEmbarqueDetalle, compradorVendedorEmbarqueDetalle, formaPagoClienteCompra, dolarEmbarqueDetalle, blEmbarqueDetalle, numeroContenedorEmbarqueDetalle, bodegaEmbarque, bodegaCorreoEmbarque
            FROM embarquedetalle ed
            LEFT JOIN embarque e
            ON ed.Embarque_idEmbarque = e.idEmbarque
            LEFT JOIN compra c
            ON ed.Compra_idCompra = c.idCompra
            WHERE numeroContenedorEmbarqueDetalle = "'.$numeroCont["numeroContenedorEmbarqueDetalle"].'"
            AND bodegaCorreoEmbarque != 1
            AND bodegaEmbarque != 0');

            $styleTableEnc = 'style="border: 1px solid; background-color: #255986; color: white;"';
            $styleTableBody = 'style="border: 1px solid;"';
            $styleTableBodyN = 'style="border: 1px solid;  text-align: right;"';

            $datos = array();
            // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
            for ($i = 0, $c = count($bodega); $i < $c; ++$i) 
            {
                $datos[$i] = (array) $bodega[$i];
            }

            $i = 0;
            $detalle = count($bodega);
            

            while ($i < $detalle) 
            {
                //CONFIGURACIÓN DEL CUERPO DEL MENSAJE PARA BODEGAS
                $embarque = $datos[$i]['numeroEmbarque'];

                $email = DB::Select('SELECT * from correoembarque where tipoCorreoEmbarque = "Bodega"');
                $mail = get_object_vars($email[0]);
                $para = explode(';', $mail['destinatarioCorreoEmbarque']);

                    $mail['mensaje'] = $mail['mensajeCorreoEmbarque'].'
                    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
                    <table cellspacing="0" class="table table-striped table-bordered table-hover" style="width:100%;">
                        <tr>
                            <th colspan="16" style=" background-color:#255986; color:white;">Contenedor: '.$datos[$i]['numeroContenedorEmbarqueDetalle'].'</th>
                        </tr>
                          <tr>
                            <th '.$styleTableEnc.'>N° Embarque</th>
                            <th '.$styleTableEnc.'>Compra</th>
                            <th '.$styleTableEnc.'>Proveedor</th>
                            <th '.$styleTableEnc.'>PI</th>
                            <th '.$styleTableEnc.'>Valor</th>
                            <th '.$styleTableEnc.'>Factura</th>
                            <th '.$styleTableEnc.'>Valor</th>
                            <th '.$styleTableEnc.'>Unidades</th>
                            <th '.$styleTableEnc.'>Bultos</th>
                            <th '.$styleTableEnc.'>Naviera</th>
                            <th '.$styleTableEnc.'>Descripción</th>
                            <th '.$styleTableEnc.'>Cliente</th>
                            <th '.$styleTableEnc.'>Forma de pago</th>
                            <th '.$styleTableEnc.'>Dolar</th>
                            <th '.$styleTableEnc.'>BL</th>
                            <th '.$styleTableEnc.'>Contenedor</th>
                          </tr>';

                            while ($i < $detalle and $embarque == $datos[$i]['numeroEmbarque']) 
                            {
                                $mail["mensaje"] .= '
                                <tr>
                                    <td '.$styleTableBody.'>'.$datos[$i]["numeroEmbarque"].'</td>
                                    <td '.$styleTableBody.'>'.$datos[$i]["nombreTemporadaEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBody.'>'.$datos[$i]["proveedorTemporadaEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBodyN.'>'.$datos[$i]["numeroCompraEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBodyN.'>'.$datos[$i]["valorEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBody.'>'.$datos[$i]["facturaEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBodyN.'>'.$datos[$i]["valorFacturaEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBodyN.'>'.$datos[$i]["unidadFacturaEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBodyN.'>'.$datos[$i]["bultoFacturaEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBody.'>'.$datos[$i]["navieraEmbarque"].'</td>
                                    <td '.$styleTableBody.'>'.$datos[$i]["descripcionEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBody.'>'.$datos[$i]["compradorVendedorEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBody.'>'.$datos[$i]['formaPagoClienteCompra'].'</td>
                                    <td '.$styleTableBodyN.'>'.$datos[$i]["dolarEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBodyN.'>'.$datos[$i]["blEmbarqueDetalle"].'</td>
                                    <td '.$styleTableBody.'>'.$datos[$i]["numeroContenedorEmbarqueDetalle"].'</td>   
                                </tr>';

                                $adjunto = DB::Select(
                                'SELECT 
                                    archivoRadicadoVersion
                                FROM
                                    documentoimportacioncorreo 
                                        LEFT JOIN
                                    documento ON documentoimportacioncorreo.Documento_idDocumento = documento.idDocumento
                                        LEFT JOIN
                                    documentopropiedad ON documento.idDocumento = documentopropiedad.Documento_idDocumento and indiceDocumentoPropiedad = 1
                                        LEFT JOIN
                                    radicado ON documento.idDocumento = radicado.Documento_idDocumento 
                                        LEFT JOIN
                                    radicadodocumentopropiedad on radicado.idRadicado = radicadodocumentopropiedad.Radicado_idRadicado
                                        LEFT JOIN
                                    radicadoversion on radicado.idRadicado = radicadoversion.Radicado_idRadicado
                                where tipoDocumentoImportacionCorreo = "Bodega"
                                and valorRadicadoDocumentoPropiedad = "'.$datos[$i]["numeroEmbarque"].'"
                                GROUP BY radicadoversion.Radicado_idRadicado');

                                $i++;
                            }
                            $mail["mensaje"] .= 
                            '
                    </table>';   


                Mail::send('emails.contact',$mail,function($msj) use ($mail, $para, $adjunto)
                {
                    $msj->to($para);
                    $msj->subject($mail['asuntoCorreoEmbarque']);
                    
                    if (count($adjunto) > 0) 
                    {
                        for($i=0; $i < count($adjunto); $i++)
                        {
                            $archivos = get_object_vars($adjunto[$i]);
                            $msj->attach($archivos['archivoRadicadoVersion']);
                        }
                    }
                    
                });  
                if ($j == $posicion) 
                {
                    DB::Select('UPDATE embarque SET bodegaCorreoEmbarque = 1
                            where idEmbarque = '.$id);
                }
            }
        }
        
    }

    public function enviarCorreoOTM($id)
    {
        $otm = DB::Select('
            SELECT numeroEmbarque, nombreProveedorCompra as proveedorTemporadaEmbarqueDetalle, facturaEmbarqueDetalle, valorFacturaEmbarqueDetalle, bultoFacturaEmbarqueDetalle, blEmbarqueDetalle, numeroContenedorEmbarqueDetalle, descripcionEmbarqueDetalle, otmEmbarque, otmCorreoEmbarque
            FROM embarquedetalle ed
            LEFT JOIN embarque e
            ON ed.Embarque_idEmbarque = e.idEmbarque
            LEFT JOIN compra c
            ON ed.Compra_idCompra = c.idCompra
            WHERE Embarque_idEmbarque = '.$id);

        $datos = array();
        // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
        for ($i = 0, $c = count($otm); $i < $c; ++$i) 
        {
            $datos[$i] = (array) $otm[$i];
        }

        $i = 0;
        $detalle = count($datos);
        $styleTableEnc = 'style="border: 1px solid; background-color: #255986; color: white;"';
        $styleTableBody = 'style="border: 1px solid;"';
        $styleTableBodyN = 'style="border: 1px solid;  text-align: right;"';

        while ($i < $detalle) 
        {
            //CONFIGURACIÓN DEL CUERPO DEL MENSAJE PARA OTM
            if ($datos[$i]['otmEmbarque'] == 1 and $datos[$i]['otmCorreoEmbarque'] != 1)
            {
                $bl = $datos[$i]["blEmbarqueDetalle"];

                #Consulto los datos del correo tales como destinatario, asunto y mensaje
                $email = DB::Select('SELECT * from correoembarque where tipoCorreoEmbarque = "OTM"');
                $mail = get_object_vars($email[0]);
                $para = explode(';', $mail['destinatarioCorreoEmbarque']);

                #En el array de mail, en la posicion 'mensaje' empiezo a acumular el cuerpo del mensaje
                    $mail['mensaje'] = $mail['mensajeCorreoEmbarque'].'</br></br>
                    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
                    <table cellspacing="0" class="table table-striped table-bordered table-hover" style="width:100%;">
                        <tr>
                            <th colspan="8" style=" background-color:#255986; color:white;">Documento: '.$datos[$i]['blEmbarqueDetalle'].'</th>
                        </tr>
                        <tr>
                            <th '.$styleTableEnc.'>N° Embarque</th>
                            <th '.$styleTableEnc.'>Proveedor</th>
                            <th '.$styleTableEnc.'>Factura</th>
                            <th '.$styleTableEnc.'>Valor</th>
                            <th '.$styleTableEnc.'>Bultos</th>
                            <th '.$styleTableEnc.'>BL</th>
                            <th '.$styleTableEnc.'>Contenedor</th>
                            <th '.$styleTableEnc.'>Observacion</th>
                          </tr>';

                        while ($i < $detalle and $bl == $datos[$i]["blEmbarqueDetalle"])
                        {
                            $mail["mensaje"] .= '
                            <tr>
                                <td '.$styleTableBody.'>'.$datos[$i]["numeroEmbarque"].'</td>
                                <td '.$styleTableBody.'>'.$datos[$i]["proveedorTemporadaEmbarqueDetalle"].'</td>
                                <td '.$styleTableBody.'>'.$datos[$i]["facturaEmbarqueDetalle"].'</td>
                                <td '.$styleTableBodyN.'>'.$datos[$i]["valorFacturaEmbarqueDetalle"].'</td>
                                <td '.$styleTableBodyN.'>'.$datos[$i]["bultoFacturaEmbarqueDetalle"].'</td>
                                <td '.$styleTableBody.'>'.$datos[$i]["blEmbarqueDetalle"].'</td>
                                <td '.$styleTableBody.'>'.$datos[$i]["numeroContenedorEmbarqueDetalle"].'</td>
                                <td '.$styleTableBody.'>'.$datos[$i]["descripcionEmbarqueDetalle"].'</td>
                            </tr>';

                            $adjunto = DB::Select(
                                'SELECT
                                    archivoRadicadoVersion
                                FROM
                                    documentoimportacioncorreo 
                                        LEFT JOIN
                                    documento ON documentoimportacioncorreo.Documento_idDocumento = documento.idDocumento
                                        LEFT JOIN
                                    documentopropiedad ON documento.idDocumento = documentopropiedad.Documento_idDocumento and indiceDocumentoPropiedad = 1
                                        LEFT JOIN
                                    radicado ON documento.idDocumento = radicado.Documento_idDocumento 
                                        LEFT JOIN
                                    radicadodocumentopropiedad on radicado.idRadicado = radicadodocumentopropiedad.Radicado_idRadicado 
                                        LEFT JOIN
                                    radicadoversion on radicado.idRadicado = radicadoversion.Radicado_idRadicado
                                where tipoDocumentoImportacionCorreo = "OTM"
                                and valorRadicadoDocumentoPropiedad = "'.$datos[$i]["numeroEmbarque"].'"
                                GROUP BY radicadoversion.Radicado_idRadicado');

                            $i++;
                        }
                            $mail["mensaje"] .= 
                            '
                    </table>';    
               
                Mail::send('emails.contact',$mail,function($msj) use ($mail, $para, $adjunto)
                {
                    $msj->to($para);
                    $msj->subject($mail['asuntoCorreoEmbarque']);
                    if (count($adjunto) > 0) 
                    {
                        for($i=0; $i < count($adjunto); $i++)
                        {
                            $archivos = get_object_vars($adjunto[$i]);
                            $msj->attach($archivos['archivoRadicadoVersion']);
                        }
                    }
                    
                }); 

                DB::Select('UPDATE embarque SET otmCorreoEmbarque = 1
                            where idEmbarque = '.$id); 
            }
            else
            {
                $i++;
            }
        }
    }
}
