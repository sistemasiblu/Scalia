<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CompraRequest;
use App\Http\Controllers\Controller;
use DB;
use Config;
use Mail;
// use App\Http\Providers\Validator;
include public_path().'/ajax/consultarPermisos.php';
include public_path().'/ajax/actualizarCartera.php';

class CompraController extends Controller 
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
            return view('compragrid', compact('datos'));
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
        $documentoimportacion = \App\DocumentoImportacion::All()->lists('nombreDocumentoImportacion', 'idDocumentoImportacion');

        $tercero = DB::Select(
            "SELECT nombre1Tercero as nombre, idTercero as id
            FROM Iblu.Tercero
            WHERE idTercero IS NOT NULL 
            AND tipoTercero like '%*01**02*%'
            AND tipoTercero not like '%18%'
            ORDER BY nombre1Tercero");
        $tercero = $this->convertirArray($tercero);

        $evento = DB::Select(
            "SELECT nombreEvento as nombre, idEvento as id
            FROM Iblu.Evento
            WHERE idEvento IS NOT NULL
            ORDER BY nombreEvento");
        $evento = $this->convertirArray($evento);

        $tipodocumento = DB::Select(
            "SELECT idIdentificacion as id, nombreIdentificacion as nombre
            FROM Iblu.TipoIdentificacion");    
        $tipodocumento = $this->convertirArray($tipodocumento);

        return view('compra',compact('evento','tipodocumento','tercero'),['documentoimportacion'=>$documentoimportacion]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompraRequest $request)
    {
        \App\Compra::create([
        'fechaCompra' => $request['fechaCompra'],
        'Temporada_idTemporada' => ($request['Temporada_idTemporada'] == '' or $request['Temporada_idTemporada'] == 0 ? NULL : $request['Temporada_idTemporada']),
        'nombreTemporadaCompra' => $request['nombreTemporadaCompra'],
        'Tercero_idProveedor' => ($request['Tercero_idProveedor'] == '' or $request['Tercero_idProveedor'] == 0 ? NULL : $request['Tercero_idProveedor']),
        'nombreProveedorCompra' => $request['nombreProveedorCompra'],
        'Movimiento_idMovimiento' => ($request['Movimiento_idMovimiento'] == '' or $request['Movimiento_idMovimiento'] == 0 ? NULL : $request['Movimiento_idMovimiento']),
        'numeroCompra' => $request['numeroCompra'],
        'Tercero_idCliente' => ($request['Tercero_idCliente'] == '' or $request['Tercero_idCliente'] == 0 ? NULL : $request['Tercero_idCliente']),
        'nombreClienteCompra' => $request['nombreClienteCompra'],
        'formaPagoClienteCompra' => $request['formaPagoClienteCompra'],
        'eventoCompra' => $request['eventoCompra'],
        'compradorVendedorCompra' => $request['compradorVendedorCompra'],
        'Tercero_idVendedor' => ($request['Tercero_idVendedor'] == '' or $request['Tercero_idVendedor'] == 0 ? NULL : $request['Tercero_idVendedor']),
        'valorCompra' => $request['valorCompra'],
        'FormaPago_idFormaPago' => ($request['FormaPago_idFormaPago'] == '' or $request['FormaPago_idFormaPago'] == 0 ? NULL : $request['FormaPago_idFormaPago']),
        'formaPagoProveedorCompra' => $request['formaPagoProveedorCompra'],
        'cantidadCompra' => $request['cantidadCompra'],
        'codigoUnidadMedidaCompra' => $request['unidadMedida'],
        'pesoCompra' => $request['pesoCompra'],
        'volumenCompra' => $request['volumenCompra'],
        'bultoCompra' => $request['bultoCompra'],
        'Ciudad_idPuerto' => ($request['Ciudad_idPuerto'] == '' or $request['Ciudad_idPuerto'] == 0 ? NULL : $request['Ciudad_idPuerto']),
        'nombreCiudadCompra' => $request['nombreCiudadCompra'],
        'fechaDeliveryCompra' => $request['fechaDeliveryCompra'],        
        'fechaForwardCompra' => $request['fechaForwardCompra'],
        'valorForwardCompra' => $request['valorForwardCompra'],
        'diaPagoClienteCompra' => $request['diaPagoClienteCompra'],
        'tiempoBodegaCompra' => $request['tiempoBodegaCompra'],
        'fechaMaximaDespachoCompra' => $request['fechaMaximaDespachoCompra'],
        'observacionCompra' => $request['observacionCompra'],
        'numeroVersionCompra' => $request['numeroVersionInicialCompra'],
        'estadoCompra' => "Abierto",
        'envioCorreoCompra' => "0",
        'DocumentoImportacion_idDocumentoImportacion' => $request['DocumentoImportacion_idDocumentoImportacion'],
        'Usuario_idUsuario' => \Session::get('idUsuario')
        ]);

        $compra = \App\Compra::All()->last();

        // Despues de guardar cargamos los nuevos datos de la cartera
            actualizarCartera('carga','compra',$compra->idCompra, '', $request['fechaCompra'], $request['valorCompra']);

        return redirect('/compra?idDocumento='.$request['DocumentoImportacion_idDocumentoImportacion']);
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
        $compra = \App\Compra::find($id);
        $tercero = DB::Select(
            "SELECT nombre1Tercero as nombre, idTercero as id
            FROM Iblu.Tercero
            WHERE idTercero IS NOT NULL 
            AND tipoTercero like '%*01**02*%'
            AND tipoTercero not like '%18%'
            ORDER BY nombre1Tercero");
        $tercero = $this->convertirArray($tercero);

        $evento = DB::Select(
            "SELECT nombreEvento as nombre, idEvento as id
            FROM Iblu.Evento
            WHERE idEvento IS NOT NULL
            ORDER BY nombreEvento");
        $evento = $this->convertirArray($evento);

        $tipodocumento = DB::Select(
            "SELECT idIdentificacion as id, nombreIdentificacion as nombre
            FROM Iblu.TipoIdentificacion");    
        $tipodocumento = $this->convertirArray($tipodocumento);

        $iddocumentoimportacion = \App\DocumentoImportacion::All()->lists('idDocumentoImportacion');
        $nombredocumentoimportacion = \App\DocumentoImportacion::All()->lists('nombreDocumentoImportacion');
        return view('compra',compact('iddocumentoimportacion','nombredocumentoimportacion','evento','tipodocumento','tercero'),['compra'=>$compra]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompraRequest $request, $id)
    {
        if ($request['idCompra'] != 0) 
        {     
            // Antes de guardar descargamos con los datos no modificados
            // actualizarCartera('descarga','compra', $request['idCompra'], '', $request['fechaCompra'], $request['valorCompra']);

            $index = array(
                'idCompra' => $request['idCompra']);

            $data= array(
                'fechaCompra' => $request['fechaCompra'],
                'Temporada_idTemporada' => $request['Temporada_idTemporada'],
                'nombreTemporadaCompra' => $request['nombreTemporadaCompra'],
                'Tercero_idProveedor' => $request['Tercero_idProveedor'],
                'nombreProveedorCompra' => $request['nombreProveedorCompra'],
                'Movimiento_idMovimiento' => ($request['Movimiento_idMovimiento'] == '' or $request['Movimiento_idMovimiento'] == 0 ? NULL : $request['Movimiento_idMovimiento']),
                'numeroCompra' => $request['numeroCompra'],
                'Tercero_idCliente' => $request['Tercero_idCliente'],
                'nombreClienteCompra' => $request['nombreClienteCompra'],
                'formaPagoClienteCompra' => $request['formaPagoClienteCompra'],
                'eventoCompra' => $request['eventoCompra'],
                'compradorVendedorCompra' => $request['compradorVendedorCompra'],
                'Tercero_idVendedor' => $request['Tercero_idVendedor'],
                'valorCompra' => $request['valorCompra'],
                'FormaPago_idFormaPago' => $request['FormaPago_idFormaPago'],
                'formaPagoProveedorCompra' => $request['formaPagoProveedorCompra'],
                'cantidadCompra' => $request['cantidadCompra'],
                'codigoUnidadMedidaCompra' => $request['unidadMedida'],
                'pesoCompra' => $request['pesoCompra'],
                'volumenCompra' => $request['volumenCompra'],
                'bultoCompra' => $request['bultoCompra'],
                'Ciudad_idPuerto' => $request['Ciudad_idPuerto'],
                'nombreCiudadCompra' => $request['nombreCiudadCompra'],
                'fechaDeliveryCompra' => $request['fechaDeliveryCompra'],        
                'fechaForwardCompra' => $request['fechaForwardCompra'],
                'fechaMaximaDespachoCompra' => $request['fechaMaximaDespachoCompra'],
                'valorForwardCompra' => $request['valorForwardCompra'],
                'diaPagoClienteCompra' => $request['diaPagoClienteCompra'],
                'tiempoBodegaCompra' => $request['tiempoBodegaCompra'],
                'observacionCompra' => $request['observacionCompra'],
                'numeroVersionCompra' => $request['numeroVersionCompra'],
                'estadoCompra' => $request['estadoCompra'],
                'envioCorreoCompra' => $request['envioCorreoCompra'],
                'DocumentoImportacion_idDocumentoImportacion' => $request['DocumentoImportacion_idDocumentoImportacion'],
                'Usuario_idUsuario' => \Session::get('idUsuario'));
            
            $save = \App\Compra::updateOrCreate($index, $data);

            DB::Select('UPDATE forwarddetalle set valorForwardDetalle = '.$request['valorCompra']. ' where Compra_idCompra = '.$request['idCompra']);

            // Despues de guardar los datos modificados, cargamos los nuevos datos de la cartera
            // actualizarCartera('carga','compra',$request['idCompra'], '', $request['fechaCompra'], $request['valorCompra']);
        } 
        else
        {
            //Como estoy guardando una nueva versión y este nuevo número contiene un asterisco "*" lo elimino para poder guardarlo
            $versionCompra = substr($request['numeroVersionMaximaCompra'], 0, -1);

            \App\Compra::create([
            'fechaCompra' => $request['fechaCompra'],
            'Temporada_idTemporada' => $request['Temporada_idTemporada'],
            'nombreTemporadaCompra' => $request['nombreTemporadaCompra'],
            'Tercero_idProveedor' => $request['Tercero_idProveedor'],
            'nombreProveedorCompra' => $request['nombreProveedorCompra'],
            'Movimiento_idMovimiento' => ($request['Movimiento_idMovimiento'] == '' or $request['Movimiento_idMovimiento'] == 0 ? NULL : $request['Movimiento_idMovimiento']),
            'numeroCompra' => $request['numeroCompra'],
            'Tercero_idCliente' => $request['Tercero_idCliente'],
            'nombreClienteCompra' => $request['nombreClienteCompra'],
            'formaPagoClienteCompra' => $request['formaPagoClienteCompra'],
            'eventoCompra' => $request['eventoCompra'],
            'compradorVendedorCompra' => $request['compradorVendedorCompra'],
            'Tercero_idVendedor' => $request['Tercero_idVendedor'],
            'valorCompra' => $request['valorCompra'],
            'FormaPago_idFormaPago' => $request['FormaPago_idFormaPago'],
            'formaPagoProveedorCompra' => $request['formaPagoProveedorCompra'],
            'cantidadCompra' => $request['cantidadCompra'],
            'codigoUnidadMedidaCompra' => $request['unidadMedida'],
            'pesoCompra' => $request['pesoCompra'],
            'volumenCompra' => $request['volumenCompra'],
            'bultoCompra' => $request['bultoCompra'],
            'Ciudad_idPuerto' => $request['Ciudad_idPuerto'],
            'nombreCiudadCompra' => $request['nombreCiudadCompra'],
            'fechaDeliveryCompra' => $request['fechaDeliveryCompra'],        
            'fechaForwardCompra' => $request['fechaForwardCompra'],
            'fechaMaximaDespachoCompra' => $request['fechaMaximaDespachoCompra'],
            'valorForwardCompra' => $request['valorForwardCompra'],
            'diaPagoClienteCompra' => $request['diaPagoClienteCompra'],
            'tiempoBodegaCompra' => $request['tiempoBodegaCompra'],
            'observacionCompra' => $request['observacionCompra'],
            'numeroVersionCompra' => $versionCompra,
            'estadoCompra' => $request['estadoCompra'],
            'envioCorreoCompra' => $request['envioCorreoCompra'],
            'DocumentoImportacion_idDocumentoImportacion' => $request['DocumentoImportacion_idDocumentoImportacion'],
            'Usuario_idUsuario' => \Session::get('idUsuario')]);
        }

        

        return redirect('/compra?idDocumento='.$request['DocumentoImportacion_idDocumentoImportacion']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // actualizarCartera('descarga','compra',$request['idCompra'], 0, $request['fechaCompra'], $request['valorCompra']);

        \App\Compra::destroy($id);
        return redirect('/compra?idDocumento='.$request['DocumentoImportacion_idDocumentoImportacion']);
    }
}
