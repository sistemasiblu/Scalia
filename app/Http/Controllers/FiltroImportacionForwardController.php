<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class FiltroImportacionForwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $temporada = DB::Select(
            "SELECT nombreTemporadaCompra as nombre, Temporada_idTemporada as id
            FROM compra
            WHERE Temporada_idTemporada IS NOT NULL
            ORDER BY nombreTemporadaCompra");
        $temporada = $this->convertirArray($temporada);


        $compra = DB::Select(
            "SELECT numeroCompra as nombre, numeroCompra as id
            FROM compra
            ORDER BY numeroCompra");
        $compra = $this->convertirArray($compra);

        $cliente = DB::Select(
            "SELECT nombreClienteCompra as nombre, Tercero_idCliente as id
            FROM compra
            WHERE Tercero_idCliente IS NOT NULL
            ORDER BY nombreClienteCompra");
        $cliente = $this->convertirArray($cliente);

        $proveedor = DB::Select(
            "SELECT nombreProveedorCompra as nombre, Tercero_idProveedor as id
            FROM compra
            WHERE Tercero_idProveedor IS NOT NULL
            ORDER BY nombreProveedorCompra");
        $proveedor = $this->convertirArray($proveedor);

        $documento = DB::Select(
            "SELECT nombreDocumentoImportacion as nombre, idDocumentoImportacion as id
            FROM documentoimportacion");
        $documento = $this->convertirArray($documento);

        return view('filtroimportacionforward', 
            compact( 'temporada', 'compra', 'cliente', 'proveedor', 'documento')); 
    }

    function convertirArray($dato)
    {
        $nuevo = array();
        $nuevo[0] = 'Todos';
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

    function consultarImportacionForward()
    {
        $where = (isset($_GET["condicion"]) and $_GET["condicion"] != '') ? 'WHERE '.$_GET["condicion"] : '';

        $consulta = DB::Select("
            SELECT 
                nombreClienteCompra,
                numeroCompra,
                nombreTemporadaCompra,
                fechaFinalTemporada,
                nombreProveedorCompra,
                valorCompra,
                SUM(valorFacturaEmbarqueDetalle) AS valorEmbarcado,
                valorCompra - SUM(valorFacturaEmbarqueDetalle) AS valorDiferencia,
                IF(valorFacturaEmbarqueDetallePagada > 0, 'SI', 'NO') AS reportePagoEmbarqueDetalle,
                valorFacturaEmbarqueDetallePagada,
                SUM(valorFacturaEmbarqueDetalle) - IFNULL(valorFacturaEmbarqueDetallePagada, 0) AS valorFacturaEmbarqueDetallePendiente,
                GROUP_CONCAT(facturaEmbarqueDetalle) AS facturaEmbarqueDetalle,
                IF(idForward IS NULL, '', 'SI') AS idForward,
                numeroForward,
                fechaVencimientoForward,
                tiempoBodegaCompra,
                diaPagoClienteCompra,
                fechaReservaEmbarqueDetalle,
                fechaElaboracionEmbarque,
                (((fechaRealEmbarque - INTERVAL tiempoBodegaCompra DAY) - INTERVAL diaPagoClienteCompra DAY) - INTERVAL diasCiudadTipoTransporte DAY) AS fechaMaximaDespachoCompra,
                estadoCompra,
                idCompra,
                comp.Temporada_idTemporada,
                comp.Tercero_idCliente,
                comp.Tercero_idProveedor,
                comp.DocumentoImportacion_idDocumentoImportacion,
                comp.nombreDocumentoImportacion
            FROM
                (SELECT 
                    idCompra,
                        nombreTemporadaCompra,
                        nombreProveedorCompra,
                        nombreClienteCompra,
                        numeroCompra,
                        valorCompra,
                        cantidadCompra,
                        nombreCiudadCompra,
                        volumenCompra,
                        fechaDeliveryCompra,
                        tiempoBodegaCompra,
                        diaPagoClienteCompra,
                        Temporada_idTemporada,
                        Tercero_idCliente,
                        Tercero_idProveedor,
                        Ciudad_idPuerto,
                        fechaCompra,
                        nombreDocumentoImportacion,
                        formaPagoClienteCompra,
                        estadoCompra,
                        DocumentoImportacion_idDocumentoImportacion
                FROM
                    (SELECT 
                    numeroVersionCompra,
                        idCompra,
                        nombreTemporadaCompra,
                        nombreProveedorCompra,
                        nombreClienteCompra,
                        numeroCompra,
                        valorCompra,
                        cantidadCompra,
                        nombreCiudadCompra,
                        volumenCompra,
                        fechaDeliveryCompra,
                        tiempoBodegaCompra,
                        diaPagoClienteCompra,
                        Temporada_idTemporada,
                        Tercero_idCliente,
                        Tercero_idProveedor,
                        Ciudad_idPuerto,
                        fechaCompra,
                        nombreDocumentoImportacion,
                        formaPagoClienteCompra,
                        estadoCompra,
                        DocumentoImportacion_idDocumentoImportacion
                FROM
                    compra c
                LEFT JOIN documentoimportacion di ON di.idDocumentoImportacion = c.DocumentoImportacion_idDocumentoImportacion
                GROUP BY numeroCompra , numeroVersionCompra
                ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
                GROUP BY numeroCompra) AS comp
                    LEFT JOIN
                embarquedetalle ed ON comp.idCompra = ed.Compra_idCompra
                    LEFT JOIN
                (SELECT 
                    SUM(valorFacturaEmbarqueDetalle) AS valorFacturaEmbarqueDetallePagada,
                    Compra_idCompra
                FROM
                    embarquedetalle edp
                WHERE
                    pagoEmbarqueDetalle = 1
                GROUP BY Compra_idCompra) edp ON comp.idCompra = edp.Compra_idCompra
                    LEFT JOIN
                embarque e ON e.idEmbarque = ed.Embarque_idEmbarque
                    LEFT JOIN
                (SELECT 
                    Compra_idCompra,
                        idForward,
                        GROUP_CONCAT(DISTINCT numeroForward) AS numeroForward,
                        GROUP_CONCAT(DISTINCT fechaVencimientoForward) AS fechaVencimientoForward
                FROM
                    forwarddetalle fd
                LEFT JOIN forward f ON fd.Forward_idForward = f.idForward
                GROUP BY fd.Compra_idCompra) fd ON comp.idCompra = fd.Compra_idCompra
                    LEFT JOIN
                Iblu.CiudadTipoTransporte ctt ON ctt.Ciudad_idCiudad = e.Ciudad_idPuerto_Carga
                    LEFT JOIN
                Iblu.Temporada t ON comp.Temporada_idTemporada = t.idTemporada
            $where
            GROUP BY numeroCompra
            ORDER BY nombreDocumentoImportacion , nombreClienteCompra ASC");
        
        return view('formatos.impresionImportacionForward',compact('consulta'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
