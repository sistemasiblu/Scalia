<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\TrasladoDocumentoRequest;
use App\Http\Controllers\Controller;
use DB;
use Config;
include public_path().'/ajax/consultarPermisos.php';

class TrasladoDocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);

        if($datos != null)
            return view('trasladodocumentogrid', compact('datos'));
        else
            return view('accesodenegado');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //******************************************
        //
        // CONEXION A LA BASE DE DATOS DE SAYA IBLU
        //
        //******************************************
        $idSistemaInformacion = 1;

        $conexBD = DB::table('sistemainformacion')
        ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
        ->where('idSistemaInformacion', "=", $idSistemaInformacion)
        ->get();


        $conexBD = get_object_vars($conexBD[0]);
        // print_r($conexBD);
       
        Config::set( 'database.connections.'.$conexBD['bdSistemaInformacion'], array 
        ( 
            'driver'     =>  $conexBD['motorbdSistemaInformacion'], 
            'host'       =>  $conexBD['ipSistemaInformacion'], 
            'port'       =>  $conexBD['puertoSistemaInformacion'], 
            'database'   =>  $conexBD['bdSistemaInformacion'], 
            'username'   =>  $conexBD['usuarioSistemaInformacion'], 
            'password'   =>  $conexBD['claveSistemaInformacion'], 
            'charset'    =>  'utf8', 
            'collation'  =>  'utf8_unicode_ci', 
            'prefix'     =>  ''
        )); 

        $conexion = DB::connection($conexBD['bdSistemaInformacion'])->getDatabaseName();

        $documento = DB::connection($conexBD['bdSistemaInformacion'])->select(
            "SELECT idDocumento as id, nombreDocumento as nombre
            FROM Documento
            ORDER BY nombreDocumento");
        $documento = $this->convertirArray($documento);

        $documentoconcepto = DB::connection($conexBD['bdSistemaInformacion'])->select(
            "SELECT idDocumentoConcepto as id, nombreDocumentoConcepto as nombre
            FROM DocumentoConcepto
            ORDER BY nombreDocumentoConcepto");
        $documentoconcepto = $this->convertirArray($documentoconcepto);

        $tercero = DB::connection($conexBD['bdSistemaInformacion'])->select(
            "SELECT idTercero as id, nombre1Tercero as nombre
            FROM Tercero
            WHERE tipoTercero like '%*01*%' or tipoTercero like '%*02*%'
            ORDER BY nombre1Tercero");
        $tercero = $this->convertirArray($tercero);

        $usuario = \App\User::where('id', "=", \Session::get('idUsuario'))->lists('id','name');
        $sistemainformacion = \App\SistemaInformacion::where('webSistemaInformacion', "=", '1')->lists('nombreSistemaInformacion','idSistemaInformacion');
        return view('trasladodocumento',compact('usuario','sistemainformacion','documento','documentoconcepto','tercero'));
    }

    function convertirArray($dato)
    {
        $nuevo = array();
        $nuevo[0] = 'Seleccione';
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

    public function ejecutarInterface()
    {
        echo 'entro a la funcion';
        include 'http://'.$_SERVER['SERVER_NAME'].'/pruebap3/clases/interfacedatos.class.php';
        echo 'intancié la funcion';
        $interface = new InterfaceDatos();  
        echo 'paso la instancia';
        $errores = "";
        $idBdOrigen = $request['SistemaInformacion_idOrigen'];
        $idBdDestino = $request['SistemaInformacion_idDestino'];

        //******************************************
        //
        // CONEXION A LA BASE DE DATOS 
        //
        //******************************************
        $idSistemaInformacion = $request['SistemaInformacion_idOrigen'];

        $conexBD = DB::table('sistemainformacion')
        ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
        ->where('idSistemaInformacion', "=", $idSistemaInformacion)
        ->get();


        $conexBD = get_object_vars($conexBD[0]);
       
        Config::set( 'database.connections.'.$conexBD['bdSistemaInformacion'], array 
        ( 
            'driver'     =>  $conexBD['motorbdSistemaInformacion'], 
            'host'       =>  $conexBD['ipSistemaInformacion'], 
            'port'       =>  $conexBD['puertoSistemaInformacion'], 
            'database'   =>  $conexBD['bdSistemaInformacion'], 
            'username'   =>  $conexBD['usuarioSistemaInformacion'], 
            'password'   =>  $conexBD['claveSistemaInformacion'], 
            'charset'    =>  'utf8', 
            'collation'  =>  'utf8_unicode_ci', 
            'prefix'     =>  ''
        )); 

        $conexion = DB::connection($conexBD['bdSistemaInformacion'])->getDatabaseName();
        print_r($conexion);

        #Consulto el periodo actual en que voy a realizar el traslado y el resultado lo convierto a string
        $periodo = DB::connection($datos['bdSistemaInformacion'])->Select('SELECT idPeriodo FROM Periodo
        where fechaInicialPeriodo <= curdate()
        and fechaFinalPeriodo >= curdate()');

        $idPeriodo = get_object_vars($periodo[0]);

        print_r($idPeriodo);
        exit;

        for ($i=0; $i < count($request['documentoOrigenTrasladoDocumentoDetalle']); $i++) 
        { 
            $idMovimientoOrigen = $request['Movimiento_idOrigen'][$i];
            $idDocumentoDestino = $request['Documento_idDestino'][$i];
            $idDocumentoConceptoDestino = $request['DocumentoConcepto_idDestino'][$i];
            $idTerceroDestino = $request['Tercero_idDestino'][$i];

            $consulta = DB::connection($conexBD['bdSistemaInformacion'])->select(
            "SELECT M.*, MD.* 
            from ".$idBdOrigen.".Movimiento M 
            left join ".$idBdOrigen.".MovimientoDetalle MD 
            on M.idMovimiento = MD.Movimiento_idMovimiento 
            where idMovimiento = $idMovimientoOrigen");

            #Convierto el array en string
            $consultaMov = get_object_vars($consulta[0]);

            $encabezado[0]["Documento_idDocumento"] = $idDocumentoDestino; 
            $encabezado[0]["DocumentoConcepto_idDocumentoConcepto"] = $idDocumentoConceptoDestino;
            $encabezado[0]["fechaElaboracionMovimiento"] = $datosEncabezado[0]["fechaPedido"]; //PREGUNTARRRRRRRRR!!!!!!!!!!!!
            $encabezado[0]["numeroMovimiento"] = ""; //PREGUNTARRRRRRRRR!!!!!!!!!!!!
            $encabezado[0]["Tercero_idTercero"] = $idTerceroDestino; #En este caso, aplica el mismo tercero para los dos tipos
            $encabezado[0]["Tercero_idPrincipal"] = $idTerceroDestino;
            $encabezado[0]["Periodo_idPeriodo"] = $idPeriodo; #Se pone el id de periodo que se consultó anteriormente
            $encabezado[0]["tipoMovimiento"] = 'NORMAL'; #En este caso siempre será normal
            $encabezado[0]["observacionMovimiento"] = $consultaMov["observacionMovimiento"];

            for($cont = 0; $cont < count($consulta); $cont++)
            {
                $detalle[$cont]["numeroMovimiento"] = $_POST["numOrden"]; //PREGUNTARRRRRRRRR!!!!!!!!!!!!
                $detalle[$cont]["Producto_idProducto"] = $consultaMov["Producto_idProducto"];
                $detalle[$cont]["cantidadMovimientoDetalle"] = $consultaMov["cantidadPedidoDetalle"];
                $detalle[$cont]["precioListaMovimientoDetalle"] = $consultaMov["precioPedidoDetalle"];
                $detalle[$cont]["valorBrutoMovimientoDetalle"] = $consultaMov["precioPedidoDetalle"];
                $detalle[$cont]["Documento_idDocumento"] = $idDocumentoDestino;
            }
        
            $resolved = $interfaz = $interface->llenarPropiedadesMovimiento($encabezado, $detalle);

            for($i = 0; $i < count($resolved); $i++)
            {
                $errores .= $resolved[$i]['error'].","; 
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrasladoDocumentoRequest $request)
    {
        if($request['respuesta'] != 'falso')
        {
            \App\TrasladoDocumento::create([
            'numeroTrasladoDocumento' => $request['numeroTrasladoDocumento'],
            'descripcionTrasladoDocumento' => $request['descripcionTrasladoDocumento'],
            'Users_id' => \Session::get('idUsuario'),
            'fechaElaboracionTrasladoDocumento' => $request['fechaElaboracionTrasladoDocumento'],
            'estadoTrasladoDocumento' => $request['estadoTrasladoDocumento'],
            'fechaTrasladoDocumento' => $request['fechaTrasladoDocumento'],
            'SistemaInformacion_idOrigen' => $request['SistemaInformacion_idOrigen'],
            'SistemaInformacion_idDestino' => $request['SistemaInformacion_idDestino']
            ]);

            $trasladodocumento = \App\TrasladoDocumento::All()->last();

            for ($i=0; $i < count($request['documentoOrigenTrasladoDocumentoDetalle']); $i++) 
            { 
                \App\TrasladoDocumentoDetalle::create([
                'TrasladoDocumento_idTrasladoDocumento' => $trasladodocumento->idTrasladoDocumento,
                'Documento_idOrigen' => $request['Documento_idOrigen'][$i],
                'documentoOrigenTrasladoDocumentoDetalle' => $request['documentoOrigenTrasladoDocumentoDetalle'][$i],
                'DocumentoConcepto_idOrigen' => $request['DocumentoConcepto_idOrigen'][$i],
                'documentoConceptoOrigenTrasladoDocumentoDetalle' => $request['documentoConceptoOrigenTrasladoDocumentoDetalle'][$i],
                'Movimiento_idOrigen' => $request['Movimiento_idOrigen'][$i],
                'numeroOrigenTrasladoDocumentoDetalle' => $request['numeroOrigenTrasladoDocumentoDetalle'][$i],
                'Tercero_idOrigen' => $request['Tercero_idOrigen'][$i],
                'terceroOrigenTrasladoDocumentoDetalle' => $request['terceroOrigenTrasladoDocumentoDetalle'][$i],
                'fechaOrigenTrasladoDocumentoDetalle' => $request['fechaOrigenTrasladoDocumentoDetalle'][$i],
                'Documento_idDestino' => $request['Documento_idDestino'][$i],
                'documentoDestinoTrasladoDocumentoDetalle' => $request['documentoDestinoTrasladoDocumentoDetalle'][$i],
                'DocumentoConcepto_idDestino' => $request['DocumentoConcepto_idDestino'][$i],
                'documentoConceptoDestinoTrasladoDocumentoDetalle' => $request['documentoConceptoDestinoTrasladoDocumentoDetalle'][$i],
                'Tercero_idDestino' => $request['Tercero_idDestino'][$i],
                'terceroDestinoTrasladoDocumentoDetalle' => $request['terceroDestinoTrasladoDocumentoDetalle'][$i],
                'observacionTrasladoDocumentoDetalle' => $request['observacionTrasladoDocumentoDetalle'][$i]
                ]);
            }

            if ($request['estadoTrasladoDocumento'] == 'Finalizado') 
            {
                $this->ejecutarInterface();
            }

            return redirect('/trasladodocumento');
        }
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
        //******************************************
        //
        // CONEXION A LA BASE DE DATOS DE SAYA IBLU
        //
        //******************************************
        $idSistemaInformacion = 1;

        $conexBD = DB::table('sistemainformacion')
        ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
        ->where('idSistemaInformacion', "=", $idSistemaInformacion)
        ->get();


        $conexBD = get_object_vars($conexBD[0]);
        // print_r($conexBD);
       
        Config::set( 'database.connections.'.$conexBD['bdSistemaInformacion'], array 
        ( 
            'driver'     =>  $conexBD['motorbdSistemaInformacion'], 
            'host'       =>  $conexBD['ipSistemaInformacion'], 
            'port'       =>  $conexBD['puertoSistemaInformacion'], 
            'database'   =>  $conexBD['bdSistemaInformacion'], 
            'username'   =>  $conexBD['usuarioSistemaInformacion'], 
            'password'   =>  $conexBD['claveSistemaInformacion'], 
            'charset'    =>  'utf8', 
            'collation'  =>  'utf8_unicode_ci', 
            'prefix'     =>  ''
        )); 

        $conexion = DB::connection($conexBD['bdSistemaInformacion'])->getDatabaseName();

        $documento = DB::connection($conexBD['bdSistemaInformacion'])->select(
            "SELECT idDocumento as id, nombreDocumento as nombre
            FROM Documento
            ORDER BY nombreDocumento");
        $documento = $this->convertirArray($documento);

        $documentoconcepto = DB::connection($conexBD['bdSistemaInformacion'])->select(
            "SELECT idDocumentoConcepto as id, nombreDocumentoConcepto as nombre
            FROM DocumentoConcepto
            ORDER BY nombreDocumentoConcepto");
        $documentoconcepto = $this->convertirArray($documentoconcepto);

        $tercero = DB::connection($conexBD['bdSistemaInformacion'])->select(
            "SELECT idTercero as id, nombre1Tercero as nombre
            FROM Tercero
            WHERE tipoTercero like '%*01*%' or tipoTercero like '%*02*%'
            ORDER BY nombre1Tercero");
        $tercero = $this->convertirArray($tercero);

        $trasladodocumento = \App\TrasladoDocumento::find($id);
        $usuario = \App\User::where('id', "=", \Session::get('idUsuario'))->lists('id','name');
        $sistemainformacion = \App\SistemaInformacion::where('webSistemaInformacion', "=", '1')->lists('nombreSistemaInformacion','idSistemaInformacion');
        return view('trasladodocumento',compact('usuario','sistemainformacion','documento','documentoconcepto','tercero'),['trasladodocumento'=>$trasladodocumento]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TrasladoDocumentoRequest $request, $id)
    {
        if($request['respuesta'] != 'falso')
        {
            $trasladodocumento = \App\TrasladoDocumento::find($id);
            $trasladodocumento->fill($request->all());
            $trasladodocumento->Users_id = \Session::get('idUsuario');
            $trasladodocumento->save();

            $idsEliminar = explode(',', $request['eliminarTrasladoDocumentoDetalle']);
            \App\TrasladoDocumentoDetalle::whereIn('idTrasladoDocumentoDetalle',$idsEliminar)->delete();
            for($i = 0; $i < count($request['documentoOrigenTrasladoDocumentoDetalle']); $i++)
            {
                $index = array(
                    'idTrasladoDocumentoDetalle' => $request['idTrasladoDocumentoDetalle'][$i]);

                $data= array(
                'TrasladoDocumento_idTrasladoDocumento' => $trasladodocumento->idTrasladoDocumento,
                'Documento_idOrigen' => $request['Documento_idOrigen'][$i],
                'documentoOrigenTrasladoDocumentoDetalle' => $request['documentoOrigenTrasladoDocumentoDetalle'][$i],
                'DocumentoConcepto_idOrigen' => $request['DocumentoConcepto_idOrigen'][$i],
                'documentoConceptoOrigenTrasladoDocumentoDetalle' => $request['documentoConceptoOrigenTrasladoDocumentoDetalle'][$i],
                'Movimiento_idOrigen' => $request['Movimiento_idOrigen'][$i],
                'numeroOrigenTrasladoDocumentoDetalle' => $request['numeroOrigenTrasladoDocumentoDetalle'][$i],
                'Tercero_idOrigen' => $request['Tercero_idOrigen'][$i],
                'terceroOrigenTrasladoDocumentoDetalle' => $request['terceroOrigenTrasladoDocumentoDetalle'][$i],
                'fechaOrigenTrasladoDocumentoDetalle' => $request['fechaOrigenTrasladoDocumentoDetalle'][$i],
                'Documento_idDestino' => $request['Documento_idDestino'][$i],
                'documentoDestinoTrasladoDocumentoDetalle' => $request['documentoDestinoTrasladoDocumentoDetalle'][$i],
                'DocumentoConcepto_idDestino' => $request['DocumentoConcepto_idDestino'][$i],
                'documentoConceptoDestinoTrasladoDocumentoDetalle' => $request['documentoConceptoDestinoTrasladoDocumentoDetalle'][$i],
                'Tercero_idDestino' => $request['Tercero_idDestino'][$i],
                'terceroDestinoTrasladoDocumentoDetalle' => $request['terceroDestinoTrasladoDocumentoDetalle'][$i],
                'observacionTrasladoDocumentoDetalle' => $request['observacionTrasladoDocumentoDetalle'][$i]);
                
                $save = \App\TrasladoDocumentoDetalle::updateOrCreate($index, $data);
            }

            if ($request['estadoTrasladoDocumento'] == 'Finalizado') 
            {
                $this->ejecutarInterface();
            }

            return redirect('/trasladodocumento');
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
        \App\TrasladoDocumento::destroy($id);
        return redirect('/trasladodocumento');
    }
}
