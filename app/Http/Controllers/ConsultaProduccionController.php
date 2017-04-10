<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Config;

class ConsultaProduccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('consultaproducciongrid');
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
    public function show($id, Request $request)
    {
        $tipo = $request['tipo'];
        // ****************** REALIZO LA CONEXIÓN A SAYA ***********************
        $idSistemaInformacion = 1;

        $datos = DB::table('sistemainformacion')
        ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
        ->where('idSistemaInformacion', "=", $idSistemaInformacion)
        ->get();


        $datos = get_object_vars($datos[0]);
           
           Config::set( 'database.connections.'.$datos['bdSistemaInformacion'], array 
            ( 
                'driver'     =>  $datos['motorbdSistemaInformacion'], 
                'host'       =>  $datos['ipSistemaInformacion'], 
                'port'       =>  $datos['puertoSistemaInformacion'], 
                'database'   =>  $datos['bdSistemaInformacion'], 
                'username'   =>  $datos['usuarioSistemaInformacion'], 
                'password'   =>  $datos['claveSistemaInformacion'], 
                'charset'    =>  'utf8', 
                'collation'  =>  'utf8_unicode_ci', 
                'prefix'     =>  ''
            )); 

        $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();

        $tablas = DB::connection($datos['bdSistemaInformacion'])->select('SHOW FULL TABLES FROM '. $datos['bdSistemaInformacion']);

        // *************** TERMINO LA CONEXIÓN CON SAYA *****************

        // SE HACE UN SWITCH Y DEPENDIENDO DE TIPO (FICHA TECNICA, PRODUCCION Y MOVIMIENTO SE REALIZAN DIFERENTES CONSULTAS)

        switch ($tipo) 
        {
            // CONSULTA PARA EL FORMATO DE IMPRESION DE FICHA TECNICA
            case 'FichaTecnica':
                // CONSULTO EL ID DE LA FICHA TECNICA PARA EL INFORMA DE FICHA TECNICA
                $fichatecnica = DB::connection($datos['bdSistemaInformacion'])->Select('SELECT FichaTecnica_idFichaTecnica from Producto 
                where codigoAlternoProducto = "'.$id.'"'); 

                $idFichaTecnica = get_object_vars($fichatecnica[0]);

                // REALIZO LA CONSULTA PARA OBTENER LOS CAMPOS PARA EL INFORME DE FICHA TECNICA
                $encabezado = DB::connection($datos['bdSistemaInformacion'])->Select('
                    SELECT 
                        idFichaTecnica,
                        nombre1Tercero,
                        nombreTipoNegocio,
                        nombreTemporada,
                        referenciaBaseFichaTecnica,
                        nombreLargoFichaTecnica,
                        nombreMarca,
                        nombreCategoria,
                        nombreTipoProducto,
                        nombreComposicion,
                        codigoAlternoFichaTecnica,
                        numeroMoldeFichaTecnica,
                        areaMoldeFichaTecnica,
                        group_concat(DISTINCT nombre1Color) as nombre1Color,
                        group_concat(DISTINCT nombre1Talla) as nombre1Talla
                    FROM
                        FichaTecnica AS FT
                            LEFT JOIN
                        Marca AS M ON FT.Marca_idMarca = M.idMarca
                            LEFT JOIN
                        TipoNegocio AS TN ON FT.TipoNegocio_idTipoNegocio = TN.idTipoNegocio
                            LEFT JOIN
                        Temporada AS Te ON FT.Temporada_idTemporada = Te.idTemporada
                            LEFT JOIN
                        Categoria AS C ON FT.Categoria_idCategoria = C.idCategoria
                            LEFT JOIN
                        Tercero AS T ON FT.Tercero_idCliente = T.idTercero
                            LEFT JOIN
                        FichaTecnicaColor AS FTC ON FTC.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                            LEFT JOIN
                        Color AS Co ON FTC.Color_idColor = Co.idColor
                            LEFT JOIN
                        FichaTecnicaTalla AS FTT ON FTT.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                            LEFT JOIN
                        Talla AS Tll ON FTT.Talla_idTalla = Tll.idTalla
                            LEFT JOIN
                        Composicion AS Comp ON FT.Composicion_idComposicion = Comp.idComposicion
                        LEFT JOIN
                        TipoProducto AS TP ON FT.TipoProducto_idTipoProducto = TP.idTipoProducto
                    WHERE
                        idFichaTecnica = '.$idFichaTecnica['FichaTecnica_idFichaTecnica']); 

                // REALIZO LA CONSULTA PARA OBTENER LOS CAMPOS PARA EL INFORME DE FICHA TECNICA IMAGEN
                $imagen = DB::connection($datos['bdSistemaInformacion'])->Select('
                    SELECT 
                        nombreFichaTecnicaImagen,
                        imagenFichaTecnicaImagen,
                        observacionFichaTecnicaImagen
                    FROM
                        FichaTecnica AS FT
                            LEFT JOIN
                        FichaTecnicaImagen AS FTI ON FTI.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                    WHERE
                        idFichaTecnica = '.$idFichaTecnica['FichaTecnica_idFichaTecnica']);

                // REALIZO LA CONSULTA PARA OBTENER LOS CAMPOS PARA EL INFORME DE CENTRO DE PRODUCCION DE LA FICHA TECNICA
                $centroproduccion = DB::connection($datos['bdSistemaInformacion'])->Select('
                    SELECT 
                        nombreCentroProduccion,
                        costoEstimadoFichaTecnicaCentroProduccion,
                        observacionFichaTecnicaCentroProduccion
                    FROM
                        FichaTecnica AS FT
                            LEFT JOIN
                        FichaTecnicaCentroProduccion AS FTCP ON FTCP.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                            LEFT JOIN
                        CentroProduccion AS CP ON FTCP.CentroProduccion_idCentroProduccion = CP.idCentroProduccion
                    WHERE
                        idFichaTecnica = '.$idFichaTecnica['FichaTecnica_idFichaTecnica']);

                return view('formatos.impresionConsultaFichaTecnica',compact('encabezado','imagen','centroproduccion', 'datos'));
            break;

            // CONSULTA PARA EL FORMATO DE IMPRESION DE ORDEN DE PRODUCCION
            case 'Produccion':
                // CONSULTO EL ID DE LA OP TENIENDO COMO CONDICION EL NUMERO DE LA OP
                $op = DB::connection($datos['bdSistemaInformacion'])->Select('SELECT idOrdenProduccion from OrdenProduccion
                    where numeroOrdenProduccion = "'.$id.'"'); 

                $idOP = get_object_vars($op[0]);

                $regTallas = '';
                $tallas = DB::connection($datos['bdSistemaInformacion'])->Select('
                    SELECT idTalla, codigoAlternoTalla, nombre1Talla
                        From OrdenProduccionProducto OP
                        left join Producto P
                        on OP.Producto_idProducto = P.idProducto
                        left join Talla T
                        on P.Talla_idTalla = T.idTalla
                        Where OrdenProduccion_idOrdenProduccion = "'.$idOP["idOrdenProduccion"].'"
                        group by idTalla
                        Order by ordenTalla');


                for($tal = 0; $tal < count($tallas); $tal++)
                {
                    $talla = get_object_vars($tallas[$tal]);

                    $regTallas .= "SUM(IF(idTalla ".($talla["idTalla"] == ''? ' IS NULL': " = ".$talla["idTalla"]).", cantidadOrdenProduccionProducto, 0)) as T_".($talla["idTalla"] == '' ? 0: $talla["idTalla"]).', ';
                }

                 $datosproduccion = DB::connection($datos['bdSistemaInformacion'])->Select('
                    SELECT 
                        -- Datos generales
                        numeroOrdenProduccion,
                        documentoReferenciaOrdenProduccion,
                        fechaElaboracionOrdenProduccion,
                        fechaEstimadaEntregaOrdenProduccion,
                        nombre1Tercero,
                        documentoTercero,
                        direccionTercero,
                        telefono1Tercero,
                        nombreTemporada,
                        numeroLiquidacionCorteOrdenProduccion,
                        responsableOrdenProduccion,
                        -- Informacion del producto
                        referenciaBaseFichaTecnica,
                        codigoAlternoProducto,
                        nombreLargoProducto,
                        nombreComposicion,
                        nombreMarca,
                        numeroMoldeFichaTecnica,
                        nombre1Color,
                        nombre1Talla,'.
                        $regTallas.'
                        observacionOrdenProduccion
                        
                    FROM
                        OrdenProduccion AS OP
                            LEFT JOIN
                        Tercero AS T ON OP.Tercero_idTercero = T.idTercero
                            LEFT JOIN
                        OrdenProduccionProducto AS OPP ON OPP.OrdenProduccion_idOrdenProduccion = OP.idOrdenProduccion
                            LEFT JOIN
                        Producto AS P ON OPP.Producto_idProducto = P.idProducto
                            LEFT JOIN
                        Color AS C ON P.Color_idColor = C.idColor
                            LEFT JOIN
                        Talla AS Tll ON P.Talla_idTalla = Tll.idTalla
                            LEFT JOIN
                        Temporada AS Temp ON P.Temporada_idTemporada = Temp.idTemporada
                            LEFT JOIN
                        Marca AS M ON P.Marca_idMarca = M.idMarca
                            LEFT JOIN
                        Composicion AS Comp ON P.Composicion_idComposicion = Comp.idComposicion
                            LEFT JOIN
                        FichaTecnica AS FT ON P.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                    WHERE
                        idOrdenProduccion = '.$idOP['idOrdenProduccion'].
                    ' GROUP BY (nombre1Color) 
                    ORDER BY (nombre1Color)');

                return view('formatos.impresionConsultaProduccion',compact('datosproduccion','tallas'));
            break;

            case 'Movimiento':
            
                $regTallas = '';
                $tallas = DB::connection($datos['bdSistemaInformacion'])->Select('
                    SELECT idTalla, codigoAlternoTalla, nombre1Talla
                        From MovimientoDetalle MD
                        left join Producto P
                        on MD.Producto_idProducto = P.idProducto
                        left join Talla T
                        on P.Talla_idTalla = T.idTalla
                        left join Movimiento M
                        on MD.Movimiento_idMovimiento = M.idMovimiento
                        where numeroMovimiento In ("'.str_replace(',', '","', $id).'")
                        and Documento_idDocumento = 14
                        group by idTalla
                        Order by ordenTalla');

                for($tal = 0; $tal < count($tallas); $tal++)
                {
                    $talla = get_object_vars($tallas[$tal]);

                    $regTallas .= "SUM(IF(idTalla ".($talla["idTalla"] == ''? ' IS NULL': " = ".$talla["idTalla"]).", cantidadMovimientoDetalle, 0)) as T_".($talla["idTalla"] == '' ? 0: $talla["idTalla"]).', ';
                }

                

                $datosmovimiento = DB::connection($datos['bdSistemaInformacion'])->Select('
                    SELECT 
                        numeroMovimiento,
                        fechaElaboracionMovimiento,
                        fechaMinimaMovimiento,
                        fechaMaximaMovimiento,
                        nombre1Tercero,
                        documentoTercero,
                        direccionTercero,
                        telefono1Tercero,
                        nombreCiudad,
                        numeroReferenciaExternoMovimiento,
                        codigoAlternoProducto,
                        referenciaProducto,'.
                        $regTallas.'
                        nombre1Color,
                        nombreLargoProducto,
                        cantidadMovimientoDetalle,
                        valorBrutoMovimientoDetalle,
                        totalUnidadesMovimiento,
                        valorTotalMovimiento,
                        observacionMovimiento
                    FROM
                        Movimiento AS M
                            LEFT JOIN
                        MovimientoDetalle AS MD ON MD.Movimiento_idMovimiento = M.idMovimiento
                            LEFT JOIN
                        Producto AS P ON MD.Producto_idProducto = P.idProducto
                            LEFT JOIN
                        Color AS Co ON P.Color_idColor = Co.idColor
                            LEFT JOIN
                        Talla AS Tll ON P.Talla_idTalla = Tll.idTalla
                            LEFT JOIN
                        Tercero AS T ON M.Tercero_idTercero = T.idTercero
                            LEFT JOIN
                        Ciudad AS C ON T.Ciudad_idCiudad = C.idCiudad
                    WHERE
                        numeroMovimiento In ("'.str_replace(',', '","', $id).'")
                        and Documento_idDocumento = 14
                    GROUP BY numeroMovimiento, codigoAlternoProducto, nombre1Color
                    ORDER BY numeroMovimiento , codigoAlternoProducto, nombre1Color');
  
                return view('formatos.impresionConsultaMovimiento',compact('datosmovimiento','tallas'));
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
