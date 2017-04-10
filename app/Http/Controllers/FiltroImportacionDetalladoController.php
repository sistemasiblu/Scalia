<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class FiltroImportacionDetalladoController extends Controller
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

        return view('filtroimportaciondetallado', 
            compact( 'temporada', 'compra', 'cliente', 'proveedor', 'puerto')); 
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

    function consultarImportacionDetallado()
    {
        $where = (isset($_GET["condicion"]) and $_GET["condicion"] != '') ? ' WHERE '.$_GET["condicion"] : '';

        $andFactura = (isset($_GET["condicionFactura"]) and $_GET["condicionFactura"] != '') ? ' AND '.$_GET["condicionFactura"] : '';

        $join = (isset($_GET["join"]) and $_GET["join"] != '') ? $_GET["join"] : '';

        $consulta = DB::Select('
            SELECT 
                Pedido.idMovimiento,
                idCompra,
                fechaCompra,
                nombreDocumentoImportacion,
                nombreTemporadaCompra,
                nombreProveedorCompra,
                nombreClienteCompra,
                numeroCompra,
                nombreCiudadCompra,
                fechaDeliveryCompra,
                eventoCompra,
                compradorVendedorCompra,
                numeroEmbarque,
                IF(numeroEmbarque IS NULL, "NO", "SI") AS embarque,
                fechaElaboracionEmbarque,
                facturaEmbarqueDetalle,
                observacionEmbarqueDetalle,
                blEmbarqueDetalle,
                volumenFacturaEmbarqueDetalle,
                fechaReservaEmbarqueDetalle,
                fechaRealEmbarqueDetalle,
                fechaLlegadaZonaFrancaEmbarqueDetalle,
                Categoria.codigoAlterno1Categoria,
                Categoria.nombreCategoria,
                Marca.nombreMarca,
                EsquemaProducto.nombreEsquemaProducto,
                Producto.referenciaProducto,
                Producto.nombreLargoProducto,
                PedidoDetalle.cantidadMovimientoDetalle AS cantidadPedido,
                COALESCE(fact.cantidadFactura, 0) AS cantidadFactura,
                (PedidoDetalle.cantidadMovimientoDetalle - COALESCE(fact.cantidadFactura, 0)) AS cantidadPendiente,
                (IF(fact.cantidadFactura = 0,
                    1,
                    fact.cantidadFactura) / COALESCE(PedidoDetalle.cantidadMovimientoDetalle,
                        0)) * 100 AS cumplimientoCantidad,
                (PedidoDetalle.cantidadMovimientoDetalle * PedidoDetalle.precioListaMovimientoDetalle) AS valorPedido,
                COALESCE(fact.valorFactura, 0) AS valorFactura,
                ((COALESCE(PedidoDetalle.cantidadMovimientoDetalle,
                        0) * COALESCE(PedidoDetalle.precioListaMovimientoDetalle,
                        0)) - COALESCE(fact.valorFactura, 0)) AS valorPendiente,
                (IF(fact.valorFactura = 0,
                    1,
                    fact.valorFactura) / (PedidoDetalle.cantidadMovimientoDetalle * PedidoDetalle.precioListaMovimientoDetalle) * 100) AS cumplimientoValor,
                PedidoDetalle.precioListaMovimientoDetalle
            FROM
                (SELECT 
                    idCompra,
                        fechaCompra,
                        nombreTemporadaCompra,
                        nombreProveedorCompra,
                        nombreClienteCompra,
                        numeroCompra,
                        nombreCiudadCompra,
                        fechaDeliveryCompra,
                        eventoCompra,
                        compradorVendedorCompra,
                        nombreDocumentoImportacion,
                        Tercero_idProveedor,
                        Tercero_idCliente,
                        Temporada_idTemporada,
                        Ciudad_idPuerto
                FROM
                    (SELECT 
                    idCompra,
                        fechaCompra,
                        nombreTemporadaCompra,
                        nombreProveedorCompra,
                        nombreClienteCompra,
                        numeroCompra,
                        nombreCiudadCompra,
                        fechaDeliveryCompra,
                        eventoCompra,
                        compradorVendedorCompra,
                        nombreDocumentoImportacion,
                        Tercero_idProveedor,
                        Tercero_idCliente,
                        Temporada_idTemporada,
                        Ciudad_idPuerto
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
                Iblu.Movimiento Pedido ON comp.numeroCompra = Pedido.numeroMovimiento
                    AND Pedido.Documento_idDocumento = 28
                    LEFT JOIN
                Iblu.MovimientoDetalle PedidoDetalle ON Iblu.Pedido.idMovimiento = PedidoDetalle.Movimiento_idMovimiento
                    LEFT JOIN
                Iblu.MovimientoDocumentoRef ON Pedido.idMovimiento = Iblu.MovimientoDocumentoRef.Movimiento_idDocumentoRef
                    LEFT JOIN
                (SELECT 
                    idMovimiento,
                        Producto_idProducto,
                        SUM(cantidadMovimientoDetalle) AS cantidadFactura,
                        SUM(cantidadMovimientoDetalle * precioListaMovimientoDetalle) AS valorFactura
                FROM
                    Iblu.Movimiento Factura
                LEFT JOIN Iblu.MovimientoDetalle FacturaDetalle ON FacturaDetalle.Movimiento_idMovimiento = Factura.idMovimiento
                LEFT JOIN Iblu.Producto Producto ON FacturaDetalle.Producto_idProducto = Producto.idProducto
                WHERE
                    Factura.Documento_idDocumento IN (20 , 35, 17, 97, 112, 183, 86, 92)
                       '.$andFactura.'
                GROUP BY idMovimiento , Producto_idProducto) AS fact ON fact.idMovimiento = MovimientoDocumentoRef.Movimiento_idMovimiento
                    AND PedidoDetalle.Producto_idProducto = fact.Producto_idProducto
                    LEFT JOIN
                Iblu.Producto ON Producto.idProducto = PedidoDetalle.Producto_idProducto
                    LEFT JOIN 
                Iblu.Categoria ON Producto.Categoria_idCategoria = Categoria.idCategoria 
                    LEFT JOIN
                Iblu.Marca ON Producto.Marca_idMarca = Marca.idMarca
                    LEFT JOIN
                Iblu.EsquemaProducto ON EsquemaProducto.idEsquemaProducto = Producto.EsquemaProducto_idEsquemaProducto
                '.$join.'
                '.$where.'
            GROUP BY numeroCompra , referenciaProducto
            ORDER BY nombreDocumentoImportacion , nombreClienteCompra ASC, numeroCompra');

            if ($_GET["agrupado"] == 1) 
                return view('formatos.impresionImportacionDetalladoCompra',compact('consulta'));
            else
                return view('formatos.impresionImportacionDetalladoListado',compact('consulta'));
    }
    
}
