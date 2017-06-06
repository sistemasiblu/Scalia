<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class KioskoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('kiosko');
    }

    public function Produccion()
    {
        return view('kioskoproduccion');
    }

    public function ProduccionFichaTecnica()
    {
        return view('kioskoproduccionfichatecnica');
    }

    public function ProduccionOrdenProduccion()
    {
        return view('kioskoproduccionordenproduccion');
    }

    public function ProduccionOrdenCompra()
    {
        return view('kioskoproduccionordencompra');
    }

    public function GestionHumana()
    {
        return view('kioskogestionhumana');
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
        //--------------------------------------------
        // I M P R E S I O N  F I C H A  T E C N I C A 
        // -------------------------------------------
        if ($request['formato'] == 'FichaTecnica') 
        {        
            $fichatecnica = DB::Select('SELECT idFichaTecnica from Iblu.FichaTecnica 
                where referenciaBaseFichaTecnica = "'.$request["referencia"].'"'); 

            if (empty($fichatecnica)) 
            {
                return;
            }

                $idFichaTecnica = get_object_vars($fichatecnica[0]);

                // REALIZO LA CONSULTA PARA OBTENER LOS CAMPOS PARA EL INFORME DE FICHA TECNICA
                $encabezado = DB::Select('
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
                        precioFichaTecnica,
                        group_concat(DISTINCT nombre1Color) as nombre1Color,
                        group_concat(DISTINCT nombre1Talla) as nombre1Talla
                    FROM
                        Iblu.FichaTecnica AS FT
                            LEFT JOIN
                        Iblu.Marca AS M ON FT.Marca_idMarca = M.idMarca
                            LEFT JOIN
                        Iblu.TipoNegocio AS TN ON FT.TipoNegocio_idTipoNegocio = TN.idTipoNegocio
                            LEFT JOIN
                        Iblu.Temporada AS Te ON FT.Temporada_idTemporada = Te.idTemporada
                            LEFT JOIN
                        Iblu.Categoria AS C ON FT.Categoria_idCategoria = C.idCategoria
                            LEFT JOIN
                        Iblu.Tercero AS T ON FT.Tercero_idCliente = T.idTercero
                            LEFT JOIN
                        Iblu.FichaTecnicaColor AS FTC ON FTC.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                            LEFT JOIN
                        Iblu.Color AS Co ON FTC.Color_idColor = Co.idColor
                            LEFT JOIN
                        Iblu.FichaTecnicaTalla AS FTT ON FTT.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                            LEFT JOIN
                        Iblu.Talla AS Tll ON FTT.Talla_idTalla = Tll.idTalla
                            LEFT JOIN
                        Iblu.Composicion AS Comp ON FT.Composicion_idComposicion = Comp.idComposicion
                        LEFT JOIN
                        Iblu.TipoProducto AS TP ON FT.TipoProducto_idTipoProducto = TP.idTipoProducto
                    WHERE
                        idFichaTecnica = '.$idFichaTecnica['idFichaTecnica']); 

                // REALIZO LA CONSULTA PARA OBTENER LOS CAMPOS PARA EL INFORME DE FICHA TECNICA IMAGEN
                $imagen = DB::Select('
                    SELECT 
                        nombreFichaTecnicaImagen,
                        imagenFichaTecnicaImagen,
                        observacionFichaTecnicaImagen
                    FROM
                        Iblu.FichaTecnica AS FT
                            LEFT JOIN
                        Iblu.FichaTecnicaImagen AS FTI ON FTI.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                    WHERE
                        idFichaTecnica = '.$idFichaTecnica['idFichaTecnica']);

                // REALIZO LA CONSULTA PARA OBTENER LOS PROCESOS ESPECIALES DE LA FICHA TECNICA
                $procesos = DB::Select('
                    SELECT 
                        nombreFichaTecnicaProceso,
                        imagen1FichaTecnicaProceso,
                        imagen2FichaTecnicaProceso,
                        imagen3FichaTecnicaProceso,
                        observacionFichaTecnicaProceso,
                        idFichaTecnicaProceso
                    FROM
                        Iblu.FichaTecnicaProceso
                    WHERE
                        FichaTecnica_idFichaTecnica = '.$idFichaTecnica['idFichaTecnica']);

                if ($procesos != NULL) 
                {
                    $idFichaTecnicaProceso = get_object_vars($procesos[0]);

                    // REALIZO LA CONSULTA PARA OBTENER LOS PROCESOS ESPECIALES DEL COLOR DE LA FICHA TECNICA 
                    $procesoscolor = DB::Select('
                        SELECT 
                            cf.nombre1Color as colorFondo, 
                            cp.nombre1Color as colorProceso, 
                            nombreCentroProduccionTecnica
                        FROM
                            Iblu.FichaTecnicaProcesoColor ftpc
                                LEFT JOIN
                            Iblu.Color cf ON ftpc.Color_idFondo = cf.idColor
                                LEFT JOIN
                            Iblu.Color cp ON ftpc.Color_idProceso = cp.idColor
                                LEFT JOIN
                            Iblu.CentroProduccionTecnica cpt ON ftpc.CentroProduccionTecnica_idCentroProduccionTecnica = cpt.idCentroProduccionTecnica
                        WHERE
                            FichaTecnicaProceso_idFichaTecnicaProceso = '.$idFichaTecnicaProceso["idFichaTecnicaProceso"]);
                }


                // REALIZO LA CONSULTA PARA OBTENER LOS COMPONENTES DE LA FICHA TÉCNICA
                $componentes = DB::Select('
                    SELECT 
                        componenteFichaTecnicaComponente, 
                        tipoFichaTecnicaComponente, 
                        tejidoFichaTecnicaComponente, 
                        pesoFichaTecnicaComponente, 
                        composicionFichaTecnicaComponente 
                    FROM 
                        Iblu.FichaTecnicaComponente 
                    WHERE
                        FichaTecnica_idFichaTecnica = '.$idFichaTecnica['idFichaTecnica']);

                // OBSERVACIONES DE LA FICHA TÉCNICA
                $observaciones = DB::Select('
                    SELECT 
                        observacionesFichaTecnica, 
                        observacionConstruccionFichaTecnica 
                    FROM 
                        Iblu.FichaTecnica 
                    WHERE 
                        idFichaTecnica = '.$idFichaTecnica['idFichaTecnica']);

                // REALIZO LA CONSULTA PARA LAS ESPECIFICACIONES DE HILOS Y SESGOS
                $especificacioneshs = DB::Select('
                    SELECT 
                        tipoFichaTecnicaEspecificacion, 
                        nombreFichaTecnicaEspecificacion, 
                        especificacionFichaTecnicaEspecificacion, 
                        observacionFichaTecnicaEspecificacion 
                    FROM 
                        Iblu.FichaTecnicaEspecificacion
                    WHERE 
                        FichaTecnica_idFichaTecnica = '.$idFichaTecnica['idFichaTecnica']);

                // REALIZO LA CONSULTA PARA LA TABLA DE MEDIDA ANTES DEL PROCESO
                $regTallas = '';
                $tallas = DB::Select('
                    SELECT 
                        idTalla, 
                        codigoAlternoTalla, 
                        nombre1Talla 
                    FROM
                        Iblu.FichaTecnicaMedidaTalla FTMT 
                            LEFT JOIN 
                        Iblu.Talla T on FTMT.Talla_idTalla = T.idTalla 
                    WHERE
                        FichaTecnica_idFichaTecnica = '.$idFichaTecnica['idFichaTecnica'].' 
                    GROUP BY
                        idTalla 
                    ORDER BY 
                        ordenTalla');


                for($tal = 0; $tal < count($tallas); $tal++)
                {
                    $talla = get_object_vars($tallas[$tal]);

                    $regTallas .= "SUM(IF(idTalla ".($talla["idTalla"] == ''? ' IS NULL': " = ".$talla["idTalla"]).", valorFichaTecnicaMedidaTalla, 0)) as T_".($talla["idTalla"] == '' ? 0: $talla["idTalla"]).', ';
                }

                $medidas = DB::Select('
                    SELECT 
                        nombreParteMedida,
                        observacionFichaTecnicaMedida,
                        toleranciaFichaTecnicaMedida,
                        escalaFichaTecnicaMedida,'.
                        $regTallas.'
                        imagenMedida1FichaTecnica
                    FROM 
                        Iblu.FichaTecnicaMedida FTM 
                            LEFT JOIN
                        Iblu.ParteMedida PM ON FTM.ParteMedida_idParteMedida = PM.idParteMedida
                            LEFT JOIN
                        Iblu.FichaTecnicaMedidaTalla FTMT ON FTM.idFichaTecnicaMedida = FTMT.FichaTecnicaMedida_idFichaTecnicaMedida
                            LEFT JOIN
                        Iblu.Talla T ON FTMT.Talla_idTalla = T.idTalla
                            LEFT JOIN 
                        Iblu.FichaTecnica FT ON FTM.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                    WHERE FTM.FichaTecnica_idFichaTecnica = '.$idFichaTecnica['idFichaTecnica']. '
                    AND tipoFichaTecnicaMedida = 1 
                    GROUP BY nombreParteMedida');

                // MATERIAS PRIMAS POR CENTRO DE PRODUCCIÓN
                $materias = DB::Select('
                    SELECT 
                        nombreCentroProduccion,
                        referenciaProducto,
                        nombreCortoProducto,
                        nombre1Color,
                        tipoProductoMaterial,
                        consumoMaterialConversionProductoMaterial,
                        consumoProductoProductoMaterial,
                        observacionProductoMaterial,
                        imagen1Producto
                    FROM
                        Iblu.FichaTecnicaMaterial FTM
                            LEFT JOIN
                        Iblu.CentroProduccion CP ON FTM.CentroProduccion_idCentroProduccion = CP.idCentroProduccion
                            LEFT JOIN
                        Iblu.Producto P ON FTM.Producto_idMaterial = P.idProducto
                            LEFT JOIN
                        Iblu.Color C ON FTM.Color_idColorMaterial = C.idColor
                    WHERE
                        FTM.FichaTecnica_idFichaTecnica = '.$idFichaTecnica['idFichaTecnica'].' 
                    ORDER BY nombreCentroProduccion , referenciaProducto');

                // REALIZO LA CONSULTA PARA OBTENER LOS CAMPOS PARA EL INFORME DE CENTRO DE PRODUCCION DE LA FICHA TECNICA
                $centroproduccion = DB::Select('
                    SELECT 
                        nombreCentroProduccion,
                        costoEstimadoFichaTecnicaCentroProduccion,
                        observacionFichaTecnicaCentroProduccion
                    FROM
                        Iblu.FichaTecnica AS FT
                            LEFT JOIN
                        Iblu.FichaTecnicaCentroProduccion AS FTCP ON FTCP.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                            LEFT JOIN
                        Iblu.CentroProduccion AS CP ON FTCP.CentroProduccion_idCentroProduccion = CP.idCentroProduccion
                    WHERE
                        idFichaTecnica = '.$idFichaTecnica['idFichaTecnica']);

                return view('formatos.impresionConsultaFichaTecnica',compact('encabezado','imagen','centroproduccion', 'componentes', 'observaciones', 'especificacioneshs', 'medidas', 'tallas', 'materias', 'procesos', 'procesoscolor'));
        }

        //--------------------------------------------------
        // I M P R E S I O N  O R D E N  P R O D U C C I O N 
        // -------------------------------------------------

        if ($request['formato'] == 'OrdenProduccion') 
        {
            $op = DB::Select('SELECT idOrdenProduccion from Iblu.OrdenProduccion
                    where numeroOrdenProduccion like "%'.$request["referencia"].'%"');

            if (empty($op)) 
            {
                echo 'No existe este número de OP.';
                return;
            }

            $idOP = get_object_vars($op[0]);

            $regTallas = '';
            $tallas = DB::Select('
                SELECT idTalla, codigoAlternoTalla, nombre1Talla
                    From Iblu.OrdenProduccionProducto OP
                    left join Iblu.Producto P
                    on OP.Producto_idProducto = P.idProducto
                    left join Iblu.Talla T
                    on P.Talla_idTalla = T.idTalla
                    Where OrdenProduccion_idOrdenProduccion = "'.$idOP["idOrdenProduccion"].'"
                    group by idTalla
                    Order by ordenTalla');


            for($tal = 0; $tal < count($tallas); $tal++)
            {
                $talla = get_object_vars($tallas[$tal]);

                $regTallas .= "SUM(IF(idTalla ".($talla["idTalla"] == ''? ' IS NULL': " = ".$talla["idTalla"]).", cantidadOrdenProduccionProducto, 0)) as T_".($talla["idTalla"] == '' ? 0: $talla["idTalla"]).', ';
            }

            $datosproduccion = DB::Select('
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
                    Iblu.OrdenProduccion AS OP
                        LEFT JOIN
                    Iblu.Tercero AS T ON OP.Tercero_idTercero = T.idTercero
                        LEFT JOIN
                    Iblu.OrdenProduccionProducto AS OPP ON OPP.OrdenProduccion_idOrdenProduccion = OP.idOrdenProduccion
                        LEFT JOIN
                    Iblu.Producto AS P ON OPP.Producto_idProducto = P.idProducto
                        LEFT JOIN
                    Iblu.Color AS C ON P.Color_idColor = C.idColor
                        LEFT JOIN
                    Iblu.Talla AS Tll ON P.Talla_idTalla = Tll.idTalla
                        LEFT JOIN
                    Iblu.Temporada AS Temp ON P.Temporada_idTemporada = Temp.idTemporada
                        LEFT JOIN
                    Iblu.Marca AS M ON P.Marca_idMarca = M.idMarca
                        LEFT JOIN
                    Iblu.Composicion AS Comp ON P.Composicion_idComposicion = Comp.idComposicion
                        LEFT JOIN
                    Iblu.FichaTecnica AS FT ON P.FichaTecnica_idFichaTecnica = FT.idFichaTecnica
                WHERE
                    idOrdenProduccion = '.$idOP['idOrdenProduccion'].
                ' GROUP BY (nombre1Color) 
                ORDER BY (nombre1Color)');

            return view('formatos.impresionConsultaProduccion',compact('datosproduccion','tallas'));
        }

        //-------------------------------------------
        // I M P R E S I O N  O R D E N  C O M P R A 
        // ------------------------------------------

        if ($request['formato'] == 'OrdenCompra') 
        {
            $regTallas = '';
            $tallas = DB::Select('
                SELECT idTalla, codigoAlternoTalla, nombre1Talla
                    From Iblu.MovimientoDetalle MD
                    left join Iblu.Producto P
                    on MD.Producto_idProducto = P.idProducto
                    left join Iblu.Talla T
                    on P.Talla_idTalla = T.idTalla
                    left join Iblu.Movimiento M
                    on MD.Movimiento_idMovimiento = M.idMovimiento
                    where numeroMovimiento In ("'.str_replace(',', '","', $request["referencia"]).'")
                    and Documento_idDocumento = 14
                    group by idTalla
                    Order by ordenTalla');

            for($tal = 0; $tal < count($tallas); $tal++)
            {
                $talla = get_object_vars($tallas[$tal]);

                $regTallas .= "SUM(IF(idTalla ".($talla["idTalla"] == ''? ' IS NULL': " = ".$talla["idTalla"]).", cantidadMovimientoDetalle, 0)) as T_".($talla["idTalla"] == '' ? 0: $talla["idTalla"]).', ';
            }

            $datosmovimiento = DB::Select('
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
                    Iblu.Movimiento AS M
                        LEFT JOIN
                    Iblu.MovimientoDetalle AS MD ON MD.Movimiento_idMovimiento = M.idMovimiento
                        LEFT JOIN
                    Iblu.Producto AS P ON MD.Producto_idProducto = P.idProducto
                        LEFT JOIN
                    Iblu.Color AS Co ON P.Color_idColor = Co.idColor
                        LEFT JOIN
                    Iblu.Talla AS Tll ON P.Talla_idTalla = Tll.idTalla
                        LEFT JOIN
                    Iblu.Tercero AS T ON M.Tercero_idTercero = T.idTercero
                        LEFT JOIN
                    Iblu.Ciudad AS C ON T.Ciudad_idCiudad = C.idCiudad
                WHERE
                    numeroMovimiento In ("'.str_replace(',', '","', $request["referencia"]).'")
                    and Documento_idDocumento = 14
                GROUP BY numeroMovimiento, codigoAlternoProducto, nombre1Color
                ORDER BY numeroMovimiento , codigoAlternoProducto, nombre1Color');

            return view('formatos.impresionConsultaMovimiento',compact('datosmovimiento','tallas'));
        }

        //---------------------------------------------
        // I M P R E S I O N  R E C I B O  D E  P A G O 
        // --------------------------------------------
        if ($request['formato'] == 'recibo') 
        {   
            $tercero = DB::Select('SELECT idTercero FROM '.\Session::get("baseDatosCompania").'.Tercero WHERE documentoTercero = '.$request['documentoU']);

            $mail = ($_GET['mail'] != '') ? $_GET['mail'] : '';

            if (empty($tercero)) 
            {
                return;
            }

            $idTercero = get_object_vars($tercero[0]);

            $recibo = DB::Select("
                SELECT 
                    idLiquidacionNomina,
                    idLiquidacionNominaDetalle,
                    ConceptoNomina_idConceptoNomina,
                    nombreLiquidacionNomina,
                    numeroLiquidacionNomina,
                    fechaInicioLiquidacionNomina,
                    fechaFinLiquidacionNomina,
                    documentoTercero,
                    valorContrato,
                    nombreCargo,
                    nombre1Tercero,
                    fechaInicioContrato,
                    nombreCentroTrabajo,
                    naturalezaConceptoNomina,
                    nombreConceptoNomina,
                    horasLiquidacionNominaDetalle,
                    baseLiquidacionNominaDetalle,
                    porcentajeLiquidacionNominaDetalle,
                    SUM(valorLiquidacionNominaDetalle) AS valorLiquidacionNominaDetalle
                FROM
                    ".\Session::get("baseDatosCompania").".LiquidacionNomina ln
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".LiquidacionNominaDetalle lnd ON ln.idLiquidacionNomina = lnd.LiquidacionNomina_idLiquidacionNomina
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".ConceptoNomina cn ON lnd.ConceptoNomina_idConceptoNomina = cn.idConceptoNomina
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".Tercero t ON lnd.Tercero_idEmpleado = t.idTercero
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".Contrato ct ON lnd.Contrato_idContrato = ct.idContrato
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".Cargo c ON t.Cargo_idCargo = c.idCargo
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".CentroTrabajo ctb ON t.CentroTrabajo_idCentroTrabajo = ctb.idCentroTrabajo
                WHERE(
                    ".substr($request['condicion'],0,-3).")
                        AND naturalezaConceptoNomina  IN('DEDUCCION','DEVENGADO')
                        AND idTercero = ".$idTercero['idTercero']."
                GROUP BY ConceptoNomina_idConceptoNomina, idLiquidacionNomina
                ORDER BY numeroLiquidacionNomina, naturalezaConceptoNomina DESC");

            return view('formatos.impresionReciboPago',compact('recibo', 'mail'));
        }

        //--------------------------------------------------------
        // I M P R E S I O N  C E R T I F I C A D O  L A B O R A L 
        // -------------------------------------------------------
        if ($request['formato'] == 'certificado') 
        {   
            $tercero = DB::Select('SELECT idTercero FROM '.\Session::get("baseDatosCompania").'.Tercero WHERE documentoTercero = '.$request['documentoU'].' AND fechaNacimientoTercero = "'.$request['condicion'].'"');

            $mail = ($_GET['mail'] != '') ? $_GET['mail'] : '';

            if (empty($tercero)) 
            {
                echo '<script>alert("Los datos no coinciden.")</script>';
                return;
            }

            $idTercero = get_object_vars($tercero[0]);

            $certificado = DB::Select("
                SELECT
                    sexoTercero,
                    nombreIdentificacion,
                    nombre1Tercero,
                    documentoTercero,
                    codigoAlternoContrato,
                    nombreTipoContrato,
                    valorContrato,
                    nombreCargo,
                    fechaInicioContrato,
                    IF(fechaTerminacionContrato != '0000-00-00',
                        fechaTerminacionContrato,
                        IF(fechaVencimientoContrato = '0000-00-00',
                            'Vigente',
                            fechaVencimientoContrato)) AS fechaTerminacionContrato,
                    nombreCausaTerminacionContrato,
                        '".$request['destinatario']."' as destinatarioCertificado
                FROM
                    ".\Session::get("baseDatosCompania").".Tercero t
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".TipoIdentificacion ti ON t.TipoIdentificacion_idIdentificacion = ti.idIdentificacion
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".Contrato ct ON t.idTercero = ct.Tercero_idCliente
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".Cargo c ON t.Cargo_idCargo = c.idCargo
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".CentroTrabajo ctb ON t.CentroTrabajo_idCentroTrabajo = ctb.idCentroTrabajo
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".TipoContrato tc ON ct.TipoContrato_idTipoContrato = tc.idTipoContrato
                        LEFT JOIN
                    ".\Session::get("baseDatosCompania").".CausaTerminacionContrato ctc ON ct.CausaTerminacionContrato_idCausaTerminacionContrato = ctc.idCausaTerminacionContrato
                WHERE
                    idTercero = ".$idTercero['idTercero']."
                ORDER BY fechaInicioContrato ASC
                ");

            return view('formatos.impresionCertificadoLaboral',compact('certificado', 'mail'));
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
