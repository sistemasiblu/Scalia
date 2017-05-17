<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FiltroImportacionController extends Controller
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


        $puerto = DB::Select(
            "SELECT nombreCiudadCompra as nombre, Ciudad_idPuerto as id
            FROM compra
            WHERE Ciudad_idPuerto IS NOT NULL
            ORDER BY nombreCiudadCompra");
        $puerto = $this->convertirArray($puerto);

        $documento = DB::Select(
            "SELECT nombreDocumentoImportacion as nombre, idDocumentoImportacion as id
            FROM documentoimportacion");
        $documento = $this->convertirArray($documento);

        return view('filtroimportacion', 
            compact( 'temporada', 'compra', 'cliente', 'proveedor', 'puerto', 'documento'));    
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

    function gridFiltroImportacion()
    {
        return view('filtroimportaciongrid');
    }

    function consultarImportacion()
    {
        $join = (isset($_GET["join"]) and $_GET["join"] != '') ? $_GET["join"] : '';

        $where = (isset($_GET["condicion"]) and $_GET["condicion"] != '') ? 'WHERE '.$_GET["condicion"] : '';

        $consulta = DB::Select('
            SELECT 
                idCompra,
                nombreTemporadaCompra,
                nombreProveedorCompra,
                nombreClienteCompra,
                numeroCompra,
                valorCompra,
                SUM(valorFacturaEmbarqueDetalle) AS valorFaltante,
                cantidadCompra,
                SUM(unidadFacturaEmbarqueDetalle) as cantidadFaltante,
                nombreCiudadCompra,
                volumenCompra,
                fechaDeliveryCompra,
                fechaVencimientoForward,
                tiempoBodegaCompra,
                diaPagoClienteCompra,
                fechaReservaEmbarqueDetalle,
                fechaRealEmbarque, 
                fechaArriboPuertoEstimadaEmbarqueDetalle,
                diasCiudadTipoTransporte,
                fechaLlegadaZonaFrancaEmbarqueDetalle,
                (((fechaVencimientoForward - INTERVAL diasCiudadTipoTransporte DAY) - INTERVAL tiempoBodegaCompra DAY) - INTERVAL IFNULL(diasFormaPago, 0) DAY) AS fechaMaximaCliente,
                (((fechaRealEmbarque - INTERVAL tiempoBodegaCompra DAY) - INTERVAL diaPagoClienteCompra DAY) - INTERVAL diasCiudadTipoTransporte DAY) AS fechaMaximaEmbarqueCumplirForward,
                comp.Temporada_idTemporada,
                Tercero_idCliente,
                Tercero_idProveedor,
                Ciudad_idPuerto,
                fechaCompra,
                nombreDocumentoImportacion,
                IF(pagoEmbarqueDetalle = 1, "SI", "") AS pagoEmbarqueDetalle,
                IF(pagoEmbarqueDetalle = 1, valorFacturaEmbarqueDetalle, "") AS valorFacturaEmbarqueDetallePagada,
                IF(idForward IS NULL, "", "SI") as idForward,
                estadoCompra
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
                        estadoCompra
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
                        estadoCompra
                FROM
                    compra c
                LEFT JOIN documentoimportacion di ON di.idDocumentoImportacion = c.DocumentoImportacion_idDocumentoImportacion
                GROUP BY numeroCompra , numeroVersionCompra
                ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
                GROUP BY numeroCompra) AS comp
                    LEFT JOIN
                embarquedetalle ed ON comp.idCompra = ed.Compra_idCompra
                    LEFT JOIN
                embarque e ON e.idEmbarque = ed.Embarque_idEmbarque
                    LEFT JOIN
                forwarddetalle fd ON comp.idCompra = fd.Compra_idCompra
                    LEFT JOIN
                forward f ON fd.Forward_idForward = f.idForward
                    LEFT JOIN
                Iblu.CiudadTipoTransporte ctt ON ctt.Ciudad_idCiudad = e.Ciudad_idPuerto_Carga
                    LEFT JOIN
                Iblu.FormaPago fp on comp.formaPagoClienteCompra = fp.nombreFormaPago
                '.$join.'
                '.$where. '
            GROUP BY numeroCompra, numeroEmbarque
            ORDER BY nombreDocumentoImportacion, nombreClienteCompra asc');
        
        return view('formatos.impresionImportacion',compact('consulta'));
    }
}
