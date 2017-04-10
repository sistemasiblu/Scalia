<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class VentaEDIEstimadoInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $periodo = DB::select("SELECT idPeriodo AS id, nombrePeriodo AS nombre FROM ".\Session::get("baseDatosCompania").".Periodo ORDER BY fechaInicialPeriodo DESC");
        $periodo = $this->convertirArray($periodo);

        $marca = DB::select("SELECT idMarca AS id, nombreMarca AS nombre FROM ".\Session::get("baseDatosCompania").".Marca ORDER BY nombreMarca");
        $marca = $this->convertirArray($marca);

        $tipoproducto = DB::select("SELECT idTipoProducto AS id, nombreTipoProducto AS nombre FROM ".\Session::get("baseDatosCompania").".TipoProducto ORDER BY nombreTipoProducto");
        $tipoproducto = $this->convertirArray($tipoproducto);

        $tiponegocio = DB::select("SELECT idTipoNegocio AS id, nombreTipoNegocio AS nombre FROM ".\Session::get("baseDatosCompania").".TipoNegocio ORDER BY nombreTipoNegocio");
        $tiponegocio = $this->convertirArray($tiponegocio);

        $temporada = DB::select("SELECT idTemporada AS id, nombreTemporada AS nombre FROM ".\Session::get("baseDatosCompania").".Temporada ORDER BY nombreTemporada");
        $temporada = $this->convertirArray($temporada);

        $tercero = DB::select("SELECT idTercero AS id, CONCAT(nombre1Tercero, ' ', nombre2Tercero) AS nombre FROM ".\Session::get("baseDatosCompania").".Tercero WHERE tipoTercero LIKE '%*01*%' ORDER BY nombre1Tercero, nombre2Tercero");
        $tercero = $this->convertirArray($tercero);

        $categoria = DB::select("SELECT idCategoria AS id, nombreCategoria AS nombre FROM ".\Session::get("baseDatosCompania").".Categoria ORDER BY nombreCategoria");
        $categoria = $this->convertirArray($categoria);

        $esquemaproducto = DB::select("SELECT idEsquemaProducto AS id, nombreEsquemaProducto AS nombre FROM ".\Session::get("baseDatosCompania").".EsquemaProducto ORDER BY nombreEsquemaProducto");
        $esquemaproducto = $this->convertirArray($esquemaproducto);

        $bodega = DB::select("SELECT idBodega AS id, nombreBodega AS nombre FROM ".\Session::get("baseDatosCompania").".Bodega ORDER BY nombreBodega");
        $bodega = $this->convertirArray($bodega);

        return view('VentaEDIEstimadoInfo',compact('marca','tipoproducto','tiponegocio','temporada','tercero','categoria','esquemaproducto','periodo','bodega'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
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
            

            $condicion = isset($_GET["condicion"]) ?  $_GET["condicion"] : '';
            $condicion = str_replace('*', '%', $condicion);
            $idPeriodo = isset($_GET["idPeriodo"]) ?  $_GET["idPeriodo"] : '';

            //(SELECT idPeriodo FROM ".\Session::get("baseDatosCompania").".Periodo WHERE fechaInicialPeriodo <= '2016-09-01' and fechaFinalPeriodo >= '2016-09-01' )

// LEFT JOIN scalia.inventarioedidetalle 
//                 ON P.codigoBarrasProducto = inventarioedidetalle.eanProductoInventarioEDI
//                 LEFT JOIN scalia.inventarioedi
//                 ON inventarioedidetalle.InventarioEDI_idInventarioEDI = inventarioedi.idInventarioEDI AND fechaInicialInventarioEDI = 
//                     (
//                         SELECT MAX(inventarioedi.fechaInicialInventarioEDI) 
//                         FROM scalia.inventarioedi 
//                         WHERE DATE_FORMAT(fechaInicialInventarioEDI ,\"%Y-%m\") = 
//                             (SELECT DATE_FORMAT(fechaInicialPeriodo ,\"%Y-%m\") 
//                             FROM ".\Session::get("baseDatosCompania").".Periodo 
//                             WHERE idPeriodo = $idPeriodo) 

//                     )
                    
            // Consultamos la tabla de inventarios EDI relacionada con Inventarios SAYA
            $consulta = DB::select(
                "SELECT referenciaProducto, codigoBarrasProducto, nombreLargoProducto, 
                    cantidadInventarioEDIDetalle, 
                    cantidadVentaEDIDetalle,
                    Inventario.*,
                    diasVentaEDIEstimado as diasVenta,
                    fechaInicioVentaEDIEstimado as fechaInicialVenta,
                    DATEDIFF(NOW(),fechaInicioVentaEDIEstimado) as diasInventario,
                    diasVentaEDIEstimado -  DATEDIFF(NOW(),fechaInicioVentaEDIEstimado) as diasMetaVenta,
                    cantidadFisica*diasVentaEDIEstimado/IF(cantidadCompra = 0,1,cantidadCompra) as diasEstimadosVenta,
                    (cantidadCompra-cantidadFisica)*7/DATEDIFF(NOW(),fechaInicioVentaEDIEstimado) as unidadesPromedioSemana
                    -- Tomamos como principal los productos, para que se consulten todos, indiferentemente si estan o no en el inventario EDI o en el de SAYA
                FROM ".\Session::get("baseDatosCompania").".Producto P
                LEFT JOIN scalia.ventaediestimado V
                on P.idProducto = V.Producto_idProducto
                LEFT JOIN ".\Session::get("baseDatosCompania").".Marca M
                ON P.Marca_idMarca = M.idMarca
                LEFT JOIN ".\Session::get("baseDatosCompania").".TipoProducto TP
                ON P.TipoProducto_idTipoProducto = TP.idTipoProducto
                LEFT JOIN ".\Session::get("baseDatosCompania").".TipoNegocio TN
                ON P.TipoNegocio_idTipoNegocio = TN.idTipoNegocio
                LEFT JOIN ".\Session::get("baseDatosCompania").".Temporada Temp
                ON P.Temporada_idTemporada = Temp.idTemporada
                LEFT JOIN ".\Session::get("baseDatosCompania").".Categoria C
                ON P.Categoria_idCategoria = C.idCategoria
                LEFT JOIN ".\Session::get("baseDatosCompania").".EsquemaProducto EP
                ON P.EsquemaProducto_idEsquemaProducto = EP.idEsquemaProducto
                
                LEFT JOIN  
                (
                    -- Consultamos la tabla de inventario del período actual (todas las bodegas y todos los productos)
                    -- totalizando las cantidades por producto
                    SELECT 
                        Producto_idProducto,
                        codigoAlternoBodega,
                        SUM(facturaCompraInventario) as cantidadCompra,
                        SUM(facturaVentaInventario) as cantidadVenta,
                        SUM(pedidoPendienteInventario + pedidoPendienteInicialInventario) as pedidoPendiente, 
                        SUM(compraPendienteInventario + compraPendienteInicialInventario) as compraPendiente, 
                        SUM(cantidadFisicaInventario) as cantidadFisica
                    FROM ".\Session::get("baseDatosCompania").".Inventario I
                    left join ".\Session::get("baseDatosCompania").".Bodega B
                    on I.Bodega_idBodega = B.idBodega
                    WHERE Periodo_idPeriodo = $idPeriodo
                    GROUP BY Producto_idProducto
                ) Inventario
                ON P.idProducto = Inventario.Producto_idProducto

                LEFT JOIN  
                (
                    -- Consultamos la tabla de inventario EDI del período actual
                    -- totalizando las cantidades por producto
                    SELECT 
                        eanProductoInventarioEDI as eanProductoInventarioEDI,
                        SUM(cantidadInventarioEDIDetalle) as cantidadInventarioEDIDetalle
                    FROM scalia.inventarioedidetalle ID
                    left join scalia.inventarioedi I
                    on ID.InventarioEDI_idInventarioEDI = I.idInventarioEDI
                    WHERE I.fechaInicialInventarioEDI >= 
                        (SELECT fechaInicialPeriodo
                        FROM ".\Session::get("baseDatosCompania").".Periodo 
                        WHERE idPeriodo = $idPeriodo) 
                            AND
                        I.fechaFinalInventarioEDI <= 
                        (SELECT fechaFinalPeriodo 
                        FROM ".\Session::get("baseDatosCompania").".Periodo 
                        WHERE idPeriodo = $idPeriodo)
                    GROUP BY eanProductoInventarioEDI
                ) InventarioEDI
                ON P.codigoBarrasProducto = InventarioEDI.eanProductoInventarioEDI

                LEFT JOIN  
                (
                    -- Consultamos la tabla de inventario del período actual (todas las bodegas y todos los productos)
                    -- totalizando las cantidades por producto
                    SELECT 
                        eanProductoVentaEDI as eanProductoVentaEDI,
                        SUM(cantidadVentaEDIDetalle) as cantidadVentaEDIDetalle
                    FROM scalia.ventaedidetalle VD
                    left join scalia.ventaedi V
                    on VD.VentaEDI_idVentaEDI = V.idVentaEDI
                    WHERE V.fechaInicialVentaEDI >= 
                        (SELECT fechaInicialPeriodo
                        FROM ".\Session::get("baseDatosCompania").".Periodo 
                        WHERE idPeriodo = $idPeriodo) 
                        AND
                        V.fechaFinalVentaEDI <= 
                        (SELECT fechaFinalPeriodo 
                        FROM ".\Session::get("baseDatosCompania").".Periodo 
                        WHERE idPeriodo = $idPeriodo)
                    GROUP BY eanProductoVentaEDI
                ) VentaEDI
                ON P.codigoBarrasProducto = VentaEDI.eanProductoVentaEDI
                -- Tomamos un solo reporte de inventarios EDI y productos de tipo PT (Producto Terminado)
                where  $condicion and 
                 ((cantidadCompra+cantidadVenta+pedidoPendiente+compraPendiente+cantidadFisica) + IFNULL(cantidadInventarioEDIDetalle,0) + IFNULL(cantidadVentaEDIDetalle,0)) > 0
                GROUP BY Inventario.Producto_idProducto
                ");

            
            return view('formatos.impresionVentaEDIEstimado',['consulta'=>$consulta]);
        }
    
        if(isset($_GET['accion']) and $_GET['accion'] == 'dashboard')
        {
            
            $idDocumentoCRM= $_GET['idDocumentoCRM'];

            return view('dashboardcrm',compact('idDocumentoCRM'));
        }
    }

    function convertirArray($dato)
    {
        $nuevo = array();
        // $nuevo[0] = 'Todos';
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

}
