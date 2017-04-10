<?php

    /**
     * @author Yilmar Jesus Martinez Mosquera, Estiben Ramirez
     * @copyright Avance Integral S.A.S - 2015
     * @license software Comercial
     * @version 4.2.0.32
     * @link http://www.avanceintegral.com
     * Fecha última modificacion: 2016-01-18
     * Estado: Cerrado
     */
    class InterfaceDatos {

        function InterfaceDatos() {
            if (!isset($_SESSION["empresa"]))
                session_start();
        }

        function fecha_dmyAymd($fecha) {
            $fecha = substr($fecha, 6, 4) . '-' . substr($fecha, 3, 2) . '-' . substr($fecha, 0, 2);
            return $fecha;
        }

        function fecha_ymdAymd($fecha) {
            $fecha = substr($fecha, 0, 4) . '-' . substr($fecha, 4, 2) . '-' . substr($fecha, 6, 2);
            return $fecha;
        }

        function fechaExcelAPhp($fechaReal) {
            //include('../clases/PHPExcel/Classes/PHPExcel.php');
            // convertimos la fecha de formato EXCEL a formato UNIX
            // si el dato es numerico entero, lo convertimos a fecha
            if (is_int($fechaReal))
                $fechaPHP = date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal)));
            else
                $fechaPHP = $fechaReal;

            return $fechaPHP;
        }

        function extraerCampoEdifact($linea, $grupo, $campo) {
            // funcionamiento
            // el segmento edi contiene Grupos separados con el signo +
            // y contiene campos dentro de esos grupos, cada campo separado con el signo :
            // en los parametros se envia cual es el numero del grupo y cual es el numero del campo dentro de ese grupo


            $actual = 0;
            while (!empty($linea) and $actual < $grupo) {
                // se extrae la informacion del grupo, que incia en el primer signo + que indica la variable $grupo
                $ini = strpos($linea, '+') !== false ? strpos($linea, '+') + 1 : 1;
                $fin = strpos($linea, '+', $ini) !== false ? strpos($linea, '+', $ini) : 1000;
                $caracteres = $fin - $ini;
                $contenidoGrupo = substr($linea, $ini, $caracteres);
                $linea = substr($linea, $ini + $caracteres);

                $actual++;
            }

            if (empty($linea) and $actual < $grupo) {
                $contenidoGrupo = '';
            }


            $actual = 0;
            while (!empty($contenidoGrupo) and $actual < $campo) {
                // Ahora con la informacion que tenemos del grupo, extraemos el campo indicado en la variable $campo
                $ini = 0;
                $fin = strpos($contenidoGrupo, ':', $ini) !== false ? strpos($contenidoGrupo, ':', $ini) : 1000;
                $caracteres = $fin - $ini;
                //echo $ini. ' - ' .$fin."<br>";
                $contenidoCampo = substr($contenidoGrupo, $ini, $caracteres);
                $contenidoGrupo = substr($contenidoGrupo, $ini + $caracteres + 1);
                //echo $linea."<br>";
                $actual++;
            }

            if (empty($contenidoGrupo) and $actual < $campo) {
                $contenidoCampo = '';
            }

            return $contenidoCampo;
        }

        function moverArchivo($origen, $destino) {
            copy($origen, $destino);
            unlink($origen);
        }

        function eliminarArchivo($origen) {
            unlink($origen);
        }

        function calcularvencimiento($fecha, $dias) {
            $aFinMes = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

            $anio = substr($fecha, 0, 4);
            $mes = substr($fecha, 5, 2);
            $dia = substr($fecha, 8, 2);

            $diaspendientes = $dias;

            while ($diaspendientes > 0) {
                // verificamos cuantos dias tiene el mes actual
                $ultimo_dia = $aFinMes[$mes - 1];

                // cualculamos cuantos dias faltan para terminar el mes actual
                $resto = $ultimo_dia - $dia;

                // si los dias que faltan (resto) para terminar el mes son mas que los pendientes
                // le restamos al pendiente los dias de resto
                if ($resto < $diaspendientes) {
                    $dia = 0;
                    $diaspendientes = $diaspendientes - $resto;
                    $mes++;

                    if ($mes == 13) {
                        $anio++;
                        $mes = 1;
                    }
                } else {
                    $dia = $dia + $diaspendientes;
                    $diaspendientes = 0;
                }
            }

            // con el ANIO, el MES y el DIA, armamos el formato de la fecha
            $fecha_final = $anio . '-' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '-' . str_pad($dia, 2, '0', STR_PAD_LEFT);

            return $fecha_final;
        }

        // PROCESO DE IMPORTACION DE ORDENES DE COMPRA EDIFACT


        function importarOrdenCompraEdifact($ruta, $documento, $concepto) {
            set_time_limit(0);
            //echo $externo;
            require_once('../clases/documentocomercial.class.php');
            if (!isset($documentocomercial))
                $documentocomercial = new Documento();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/moneda.class.php');
            $moneda = new Moneda();
            require_once('../clases/formapago.class.php');
            $formapago = new FormaPago();
            require_once('../clases/incoterm.class.php');
            $incoterm = new Incoterm();
            require_once('../clases/producto.class.php');
            $producto = new Producto();
            require_once('../clases/periodo.class.php');
            $periodo = new Periodo();
            require_once('../clases/etiquetamarcacion.class.php');
            $etiquetaproducto = new EtiquetaProducto();

            $archivo = fopen($ruta, "r");
            //$archivo = fopen('OC12.edi', "r");

            $contenido = fgets($archivo);

            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;

            // creamos una variable para el control de predistribucion crossdocking en el segmento LOC
            $localizacion = false;

            while (!empty($contenido)) {
                $inicio = 1;
                $fin = strpos($contenido, "'");
                $linea = substr($contenido, 0, $fin);
                $contenido = substr($contenido, $fin + 1);
                //echo $linea."<br>";

                $segmento = substr($linea, 0, 3);

                switch ($segmento) {
                    case 'BGM' :
                        $posEnc++;
                        $encabezado[$posEnc]["Documento_idDocumento"] = $documento;
                        // validamos el documento
                        if (!empty($encabezado[$posEnc]["Documento_idDocumento"]))
                            $datos = $documentocomercial->ConsultarVistaDocumento("idDocumento =  '" . $encabezado[$posEnc]["Documento_idDocumento"] . "'");

                        //$encabezado[$posEnc]["estadoWMSMovimiento"] = isset($datos[0]["idDocumento"]) ? $datos[0]["estadoWMSDocumento"] : 'ABIERTO';

                        $encabezado[$posEnc]["DocumentoConcepto_idDocumentoConcepto"] = $concepto;
                        $encabezado[$posEnc]["tipoMovimiento"] = (substr($this->extraerCampoEdifact($linea, 1, 1), 0, 1) == 'Y' ? 'PREDISTRIBUIDA' : 'NORMAL');
                        $encabezado[$posEnc]["numeroMovimiento"] = $this->extraerCampoEdifact($linea, 2, 1);
                        $encabezado[$posEnc]["tipoReferenciaExternoMovimiento"] = 1;
                        $encabezado[$posEnc]["numeroReferenciaExternoMovimiento"] = $this->extraerCampoEdifact($linea, 2, 1);
                        $encabezado[$posEnc]["Tercero_idPrincipal"] = 0;
                        break;

                    case 'DTM' :
                        $tipo = $this->extraerCampoEdifact($linea, 1, 1);
                        $fecha = $this->extraerCampoEdifact($linea, 1, 2);
                        $formato = $this->extraerCampoEdifact($linea, 1, 3);

                        switch ($formato) {
                            case '203' :
                                $fecha = substr($fecha, 0, 4) . '/' . substr($fecha, 4, 2) . '/' . substr($fecha, 6, 2); //. ' ' . substr($fecha,8,2). ':' . substr($fecha,10,2)
                                break;
                            case '102' :
                                $fecha = substr($fecha, 0, 4) . '/' . substr($fecha, 4, 2) . '/' . substr($fecha, 6, 2);
                                break;
                        }

                        switch ($tipo) {
                            case '137' :
                                //$encabezado[$posEnc]["fechaElaboracionMovimiento"] = $fecha;
                                $encabezado[$posEnc]["fechaElaboracionMovimiento"] = date("Y-m-d");
                                $encabezado[$posEnc]["horaElaboracionMovimiento"] = date("H:i:s");
                                $encabezado[$posEnc]["fechaSolicitudMovimiento"] = $fecha;

                                // validamos el periodo
                                $periodo->idPeriodo = 0;
                                $periodo->ConsultarPeriodo("fechaInicialPeriodo <=  '".date("Y-m-d")."' and fechaFinalPeriodo >=  '".date("Y-m-d")."'  and estadoPeriodo = 'ACTIVO'");
                                $encabezado[$posEnc]["Periodo_idPeriodo"] = $periodo->idPeriodo;
                                break;
                            case '64' :
                                $encabezado[$posEnc]["fechaMinimaMovimiento"] = $fecha;
                                break;
                            case '63' :
                                $encabezado[$posEnc]["fechaMaximaMovimiento"] = $fecha;
                                break;
                        }

                        break;

                    // NAME AND ADDRESS
                    // Nombres y direcciones de las partes
                    // NAD+BY+7701234567897::9´
                    case 'NAD' :

                        $tipo = $this->extraerCampoEdifact($linea, 1, 1);
                        $ean = $this->extraerCampoEdifact($linea, 2, 1);

                        // consultamos el EAN en la tabla de terceros para obtener el ID y el documento (nit)
                        $datoTercero = $tercero->ConsultarVistaTercero("codigoBarrasTercero = '$ean'");
                        $idTercero = (isset($datoTercero[0]["idTercero"]) ? $datoTercero[0]["idTercero"] : 0);
                        $documentoTercero = (isset($datoTercero[0]["documentoTercero"]) ? $datoTercero[0]["documentoTercero"] : '');



                        switch ($tipo) {
                            case 'BY' :
                                // para el punto de venta del documento, buscamos el tercero principal
                                $tercero->idTercero = 0;
                                $tercero->ConsultarIdTercero("documentoTercero = '" . $documentoTercero . "' and tipoTercero not like '%*18*%'");
                                $encabezado[$posEnc]["Tercero_idPrincipal"] = $tercero->idTercero;
                                $encabezado[$posEnc]["Tercero_idTercero"] = $idTercero;
                                $encabezado[$posEnc]["eanTercero"] = $ean;

                                break;

                            case 'SG' :

                                $encabezado[$posEnc]["Tercero_idSeccion"] = $idTercero;
                                $encabezado[$posEnc]["eanSeccion"] = $ean;

                                break;

                            case 'SN' :

                                $encabezado[$posEnc]["Tercero_idTercero"] = $idTercero;
                                $encabezado[$posEnc]["eanTercero"] = $ean;

                                break;
                            case 'DP' :
                                $encabezado[$posEnc]["Tercero_idEntrega"] = $idTercero;
                                $encabezado[$posEnc]["eanEntrega"] = $ean;

                                break;
                            // si el documento tiene el NAD ITO (a quien se factura), este se convierte en el Tercero Cliente
                            // si no esta este segmento, simplemente quedara con el NAD BY
                            case 'ITO' :
                                //$encabezado[$posEnc]["Tercero_idTercero"] = $tercero->idTercero;
                                //$encabezado[$posEnc]["eanTercero"] = $ean;

                                break;
                        }

                        break;

                    // CURRENCIES
                    // monedas
                    // CUX+2:GBP:9+3:BEF:4´
                    case 'CUX' :
                        $mon = $this->extraerCampoEdifact($linea, 2, 1);

                        // consultamos la moneda  en la tabla de monedas para obtener el ID
                        $moneda->ConsultarMoneda("codigoAlternoMoneda = '$mon'");

                        $encabezado[$posEnc]["Moneda_idMoneda"] = $moneda->idMoneda;

                        break;

                    // PAYMENT TERMS BASIS
                    // Terminos de pago
                    // PAT+1++9:3:D:75
                    case 'PAT' :
                        $dias = $this->extraerCampoEdifact($linea, 3, 4);
                        //echo "Dias forma pago: ".$dias."---";
                        // consultamos la forma de pago  en la tabla de formapago para obtener el ID
                        $formapago->ConsultarFormaPago("diasFormaPago = '$dias'");

                        $encabezado[$posEnc]["FormaPago_idFormaPago"] = $formapago->idFormaPago;

                        break;

                    // PERCENTAGE DETAILS
                    // Detalles de porcentajes
                    // PCD+12:2.5:13´
                    case 'PCD' :
                        $tipo = $this->extraerCampoEdifact($linea, 1, 1);

                        switch ($tipo) {
                            case '12' :
                                $encabezado[$posEnc]["porcentajeDescuentoMovimiento"] = (float) $this->extraerCampoEdifact($linea, 1, 2);
                                break;
                        }

                        break;

                    // TERMS OF DELIVERY OR TRANSPORT
                    // terminos de envio y transporte (INCOTERMS)
                    // TOD+3++CIF´
                    case 'PCD' :
                        $inco = $this->extraerCampoEdifact($linea, 3, 1);

                        // consultamos el Incoterm  en la tabla de incoterms para obtener el ID
                        $incoterm->ConsultarIncoterm("codigoAlternoIncoterm = '$inco'");

                        switch ($tipo) {
                            case '12' :
                                $encabezado[$posEnc]["Incoterm_idIncoterm"] = $incoterm->idIncoterm;
                                break;
                        }

                        break;

                    // ALLOWANCE OF CHARGES
                    // Cargos y descuentos por niveles
                    // ALC+A+++1´
                    case 'ALC' :
                        // el sistema no esta preparado para importar este tipo de descuentos

                        break;
                    case 'PCD' :
                        // el sistema no esta preparado para importar este tipo de descuentos

                        break;

                    //
                    // Observaciones del documento
                    // FTX+PUR+++PRENDA COLGADA'
                    case 'FTX' :
                        $tipo = $this->extraerCampoEdifact($linea, 1, 1);
                        switch ($tipo) {
                            case 'PUR' :
                                $encabezado[$posEnc]["observacionMovimiento"] = $this->extraerCampoEdifact($linea, 4, 1);
                                break;
                        }

                        break;

                    // LINE ITEM
                    // linea de productos
                    // LIN+1+4+541234511111:EN´
                    case 'LIN' :
                        // cada que se encuentre un LIN se adiciona un registro de detalle
                        $posDet++;


                        $prod = $this->extraerCampoEdifact($linea, 3, 1);
                        $tipoprod = $this->extraerCampoEdifact($linea, 3, 2);

                        $localizacion = false;
                        $contLocalizacion = 0;
                        $posPrecio = 0;
                        $precioLBL = array();

                        if ($tipoprod == 'EN') {

                            $prod = strlen($prod) == 13 ? $prod : str_pad($prod, 13, '0', STR_PAD_LEFT);
                        } else {
                            if ($tipoprod == 'UP') {

                                $prod = strlen($prod) == 12 ? $prod : str_pad($prod, 12, '0', STR_PAD_LEFT);
                            }
                        }


                        //echo $prod;
                        // consultamos el codigo de barras  en la tabla de Productos para obtener el ID
                        $producto->idProducto = 0;
                        $producto->ConsultarProducto("codigoBarrasProducto = '$prod'");
                        if ($producto->idProducto != 0) {
                            $idproducto = $producto->idProducto;
                        } else {
                            $datos = $producto->ConsultarVistaProductoTercero("codigoBarrasProductoTercero = '$prod'");
                            $idproducto = isset($datos[0]['Producto_idProducto']) ? $datos[0]['Producto_idProducto'] : 0;
                        }
                        $detalle[$posDet]["numeroMovimiento"] = $encabezado[$posEnc]["numeroMovimiento"];
                        $detalle[$posDet]["Documento_idDocumento"] = $encabezado[$posEnc]["Documento_idDocumento"];
                        $detalle[$posDet]["Producto_idProducto"] = $idproducto;
                        $detalle[$posDet]["eanProducto"] = $prod;


                        break;

                    // ADDITIONAL PRODUCT ID
                    // Informacion adicional del producto
                    // PIA+1+51028:AT´
                    case 'PIA' :

                        $tipo = $this->extraerCampoEdifact($linea, 2, 2);

                        switch ($tipo) {
                            case 'AT' : // PLU o Referencia del cliente
                                $detalle[$posDet]["etiquetaReferenciaClienteMovimientoDetalle"] = $this->extraerCampoEdifact($linea, 2, 1);
                                break;

                            case 'ST' : // Informacion de la tela (lo ponemos en el campo de observaciones de detalle
                                $detalle[$posDet]["observacionMovimientoDetalle"] = $this->extraerCampoEdifact($linea, 2, 1);
                                break;
                        }

                        break;

                    // QUANTITY
                    // Cantidad
                    // QTY+21:120:NAR´
                    case 'QTY' :


                        $tipo = $this->extraerCampoEdifact($linea, 1, 1);

                        switch ($tipo) {
                            case '21' : // Cantidad ordenada
                                $detalle[$posDet]["cantidadMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 1, 2);
                                break;

                            case '192' : // Cantidad Bonificada
                                $detalle[$posDet]["cantidadBonificadaMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 1, 2);
                                break;

                            case '11' : // Cantidad Predistribuida
                                $detalle[$posDet]["cantidadMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 1, 2);
                                break;
                        }

                        break;


                    // PRICE DETAILS
                    // Detalle de precios
                    // PRI+AAA:1200´ ( Precio neto)
                    // PRI+AAA:1500:LBL1001´ ( Precio neto de etiqueta normal a punto de entrega 001)
                    case 'PRI' :

                        $tipo = $this->extraerCampoEdifact($linea, 1, 1);
                        $etiqueta = trim(substr($this->extraerCampoEdifact($linea, 1, 4), 0, 3));
                        if (empty($etiqueta)) {

                            switch ($tipo) {
                                case 'AAA' : // Precio Neto
                                    $detalle[$posDet]["valorNetoMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 1, 2);
                                    $detalle[$posDet]["precioListaMovimientoDetalle"] = (!isset($detalle[$posDet]["precioListaMovimientoDetalle"]) or $detalle[$posDet]["precioListaMovimientoDetalle"] == 0) ? (float) $this->extraerCampoEdifact($linea, 1, 2) : $detalle[$posDet]["precioListaMovimientoDetalle"];

                                    break;

                                case 'AAB' : // Precio Bruto
                                    $detalle[$posDet]["valorBrutoMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 1, 2);
                                    $detalle[$posDet]["precioListaMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 1, 2);
                                    break;

                                    // si el segmento PRI tiene un calificado LBL es porque tiene precios diferenciales por cada punto de venta (Crossdocking)
                                    //pero estos precios no son de venta del proveedor a la cadena, sino para marcacion de etiquetas (venta de la cadena al consumidor)
                                    // por lo tanto estos los vamos guardando en un array con el precio y el orden del amacen para el que aplica
                                    //                            case 'NE' : // Precio Maximo al publico
                                    //
                                    //                                break;

                                    $detalle[$posDet]["precioVentaPublicoMovimientoDetalle"] = 0;
                                    $detalle[$posDet]["margenUtilidadMovimientoDetalle"] = 0;
                            }
                        } else {
                            $tipoPrecio = (int) substr($this->extraerCampoEdifact($linea, 1, 5), 0, 6);
                            $precioLBL[$posPrecio]["precioNormal"] = ($tipoPrecio == 1 ? (float) $this->extraerCampoEdifact($linea, 1, 2) : 0);
                            $precioLBL[$posPrecio]["precioOferta"] = ($tipoPrecio != 1 ? (float) $this->extraerCampoEdifact($linea, 1, 2) : 0);
                            $posPrecio++;
                        }
                        break;



                    // PACKAGE
                    // Detalle de empaque
                    // PAC+4++CS´
                    case 'PAC' :

                        // este campo no existe en SAYA
                        $detalle[$posDet]["empaquesMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 1, 1);
                        $detalle[$posDet]["UnidadMedida_idEmpaque"] = $this->extraerCampoEdifact($linea, 3, 1);
                        break;


                    // PACKAGE IDENTIFICATION
                    // Datos de marcacion del producto
                    // PCI+16+01 1:16::20110103:000017900::06:SOMBRE-CAC'
                    case 'PCI' :
                        // si son instrucciones de marcacion, almacenamos los datos
                        if ($this->extraerCampoEdifact($linea, 1, 1) == 16) {
                            $codigoEtiqueta = $this->extraerCampoEdifact($linea, 2, 1);
                            $datoetiqueta = $etiquetaproducto->ConsultarVistaEtiquetaProducto("Tercero_idTercero = " . $encabezado[$posEnc]["Tercero_idPrincipal"] . " and codigoAlternoEtiquetaProducto = '$codigoEtiqueta'");

                            //$detalle[$posDet]["TipoEtiqueta_idTipoEtiqueta"] = $this->extraerCampoEdifact($linea, 1, 1);
                            $detalle[$posDet]["EtiquetaProducto_idEtiquetaProducto"] = (isset($datoetiqueta[0]["idEtiquetaProducto"]) ? $datoetiqueta[0]["idEtiquetaProducto"] : 9);
                            $detalle[$posDet]["etiquetaSeccionMovimientoDetalle"] = $this->extraerCampoEdifact($linea, 2, 2);
                            $detalle[$posDet]["etiquetaClasificacionMovimientoDetalle"] = $this->extraerCampoEdifact($linea, 2, 3);
                            $detalle[$posDet]["etiquetaFechaMovimientoDetalle"] = $this->fecha_ymdAymd($this->extraerCampoEdifact($linea, 2, 4));
                            $detalle[$posDet]["etiquetaPrecioVentaNormalMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 2, 5);
                            $detalle[$posDet]["etiquetaPrecioVentaOfertaMovimientoDetalle"] = (float) $this->extraerCampoEdifact($linea, 2, 6);
                            $detalle[$posDet]["etiquetaLugarExhibicionMovimientoDetalle"] = $this->extraerCampoEdifact($linea, 2, 7);
                            $detalle[$posDet]["etiquetaDescripcion1MovimientoDetalle"] = $this->extraerCampoEdifact($linea, 2, 8);
                            $detalle[$posDet]["etiquetaDescripcion2MovimientoDetalle"] = $this->extraerCampoEdifact($linea, 2, 9);
                            $detalle[$posDet]["etiquetaDescripcion3MovimientoDetalle"] = $this->extraerCampoEdifact($linea, 2, 10);
                        }
                        break;

                    // PLACE/LOCATION IDENTIFICATION
                    // Identificacion del lugar de entrega (predistribucion Crossdocking)
                    // LOC+7+7701234567897::9´
                    case 'LOC' :
                        $tipo = $this->extraerCampoEdifact($linea, 1, 1);
                        $ean = $this->extraerCampoEdifact($linea, 2, 1);
                        $tercero->idTercero = 0;
                        // consultamos el EAN en la tabla de terceros para obtener el ID
                        $tercero->ConsultarIdTercero("codigoBarrasTercero = '$ean'");

                        // si el ean del producto importado, es el mismo que esta en la variable de control de producto
                        // debemos adicionar un nuevo registro para ese prodcuto duplicando todos los campos para poner
                        // este almacen de entrega
                        if ($localizacion == true) {
                            $posDet++;
                            $detalle[$posDet]["numeroMovimiento"] = $detalle[$posDet - 1]["numeroMovimiento"];
                            $detalle[$posDet]["Documento_idDocumento"] = $detalle[$posDet - 1]["Documento_idDocumento"];
                            $detalle[$posDet]["Producto_idProducto"] = $detalle[$posDet - 1]["Producto_idProducto"];
                            //$detalle[$posDet]["referenciaClienteProducto"] = $detalle[$posDet - 1]["referenciaClienteProducto"];
                            $detalle[$posDet]["eanProducto"] = $detalle[$posDet - 1]["eanProducto"];
                            $detalle[$posDet]["cantidadMovimientoDetalle"] = $detalle[$posDet - 1]["cantidadMovimientoDetalle"];
                            $detalle[$posDet]["precioListaMovimientoDetalle"] = $detalle[$posDet - 1]["precioListaMovimientoDetalle"];
                            $detalle[$posDet]["valorBrutoMovimientoDetalle"] = $detalle[$posDet - 1]["valorBrutoMovimientoDetalle"];
                            $detalle[$posDet]["valorNetoMovimientoDetalle"] = $detalle[$posDet - 1]["valorNetoMovimientoDetalle"];
                            $detalle[$posDet]["empaquesMovimientoDetalle"] = $detalle[$posDet - 1]["empaquesMovimientoDetalle"];
                            $detalle[$posDet]["UnidadMedida_idEmpaque"] = $detalle[$posDet - 1]["UnidadMedida_idEmpaque"];

                            $detalle[$posDet]["EtiquetaProducto_idEtiquetaProducto"] = (isset($detalle[$posDet - 1]["EtiquetaProducto_idEtiquetaProducto"]) ? $detalle[$posDet - 1]["EtiquetaProducto_idEtiquetaProducto"] : 0);
                            $detalle[$posDet]["etiquetaSeccionMovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaSeccionMovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaSeccionMovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaClasificacionMovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaClasificacionMovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaClasificacionMovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaFechaMovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaFechaMovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaFechaMovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaPrecioVentaNormalMovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaPrecioVentaNormalMovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaPrecioVentaNormalMovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaPrecioVentaOfertaMovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaPrecioVentaOfertaMovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaPrecioVentaOfertaMovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaLugarExhibicionMovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaLugarExhibicionMovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaLugarExhibicionMovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaDescripcion1MovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaDescripcion1MovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaDescripcion1MovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaDescripcion2MovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaDescripcion2MovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaDescripcion2MovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaDescripcion3MovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaDescripcion3MovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaDescripcion3MovimientoDetalle"] : '');
                            $detalle[$posDet]["etiquetaReferenciaClienteMovimientoDetalle"] = (isset($detalle[$posDet - 1]["etiquetaReferenciaClienteMovimientoDetalle"]) ? $detalle[$posDet - 1]["etiquetaReferenciaClienteMovimientoDetalle"] : '');
                        }

                        if (isset($precioLBL[$contLocalizacion]["precioNormal"])) {
                            $detalle[$posDet]["etiquetaPrecioVentaNormalMovimientoDetalle"] = $precioLBL[$contLocalizacion]["precioNormal"];
                            $detalle[$posDet]["etiquetaPrecioVentaOfertaMovimientoDetalle"] = $precioLBL[$contLocalizacion]["precioOferta"];
                        }

                        // luego de que se adicione o no el registro, llenamos el dato del almacen de predistribucion
                        switch ($tipo) {
                            case '7' :
                                $detalle[$posDet]["Tercero_idAlmacen"] = $tercero->idTercero;
                                $detalle[$posDet]["eanAlmacen"] = $ean;
                                $localizacion = true;
                                $contLocalizacion++;

                                break;
                        }

                        break;


                    // TAXES
                    // Impuestos
                    // TAX+7+VAT+++:::15´
                    // para completar los impuesto se debe poner en el maestro de impuestos un tipo que indique si es IVA, Impoconsumo, Imp al deporte, etc
                    // MONETARY AMOUNT
                    // Valor monetario
                    // MOA+124:324000´
                    // Falta manejo de los descuentos
                    // ALC
                    // PCD
                    // RNG
                    // TOTAL CONTROL
                    // Control total: numero de referencias pedidas (segmentos LIN)
                    // CNT+2:4´
                    // UNT
                    // Fin del mensaje
                    // UNT+30+ME000001´
                }
            }

            //print_r($encabezado);
            //		print_r($detalle);
            //                return;
            //
            fclose($archivo);

            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos comerciales
            // para que las valide e importe al sistema, para esto recorremos cada orden de compra importada para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array
            $retorno = $this->llenarPropiedadesMovimiento($encabezado, $detalle);


            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
            return $retorno;
        }

        function ExportarAvisoDespachoEdifact($ids) {
            // incluimos la clase Tercero
            require_once 'tercero.class.php';
            $tercero = new Tercero();
            // consultamos el tercero dueño del sistema, para los datos del Proveedor
            $datosTercero = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");

            // incluimos la clase Empaque
            require_once 'empaque.class.php';
            $empaque = new Empaque();

            // consultamos los datos de la lista de empaque
            // el orden de los datos debe ser por
            //		* idMovimiento (cada movimiento genera un archivo nuevo)
            //		* numeroEmbalajePadre (grupos de embalajes, como estibas o contenedores)
            //		* numeroEmbalaje (cada uno de los embalajes, como cajas, bultos, etc)
            //		* referenciaProducto
            $datos = $empaque->ConsultarVistaListaEmpaque(
                    "empemb.Empaque_idEmpaque IN ($ids)", "empemb.Movimiento_idMovimiento, embpad.numeroEmbalaje, emb.ssccEmbalaje, pro.referenciaProducto", "empemb.Empaque_idEmpaque, empemb.Movimiento_idMovimiento, numeroEmpaque, fechaElaboracionEmpaque, fechaEntregaEmpaque, mov.numeroMovimiento,
                                                                    ter.codigoBarrasTercero, ter.documentoTercero,
                                                                    ppal.codigoBarrasTercero as codigoBarrasPrincipal, ppal.documentoTercero as documentoPrincipal,
                                                                    ent.codigoBarrasTercero as codigoBarrasEntrega,
                                                                    ent.documentoTercero as documentoEntrega,
                                                                    emb.numeroEmbalaje, emb.ssccEmbalaje,
                                                                    con.indicadorContenedor , con.altoContenedor, con.profundidadContenedor, con.anchoContenedor,
                                                                    tipcon.codigoAlternoTipoContenedor,  tipcon.nombreTipoContenedor,
                                                                    embpad.numeroEmbalaje as numeroEmbalajePadre, embpad.ssccEmbalaje as ssccEmbalajePadre,
                                                                    conpad.indicadorContenedor as indicadorContenedorPadre, conpad.altoContenedor as altoContenedorPadre, conpad.profundidadContenedor as profundidadContenedorPadre, conpad.anchoContenedor as anchoContenedorPadre,
                                                                    tipconpad.codigoAlternoTipoContenedor as codigoAlternoTipoContenedorPadre,  tipconpad.nombreTipoContenedor as nombreTipoContenedorPadre,
                                                                    pro.codigoBarrasProducto,
                                                                    SUM(empemb.cantidadEmpaqueEmbalaje) as cantidadEmpaqueEmbalaje,
                                                                    embar.selloSeguridadEmbarque, embar.placaVehiculoEmbarque, embar.numeroGuiaEmbarque,
                                                                    tra.codigoBarrasTercero as codigoBarrasTransportador, tra.documentoTercero as documentoTransportador,
                                                                    alm.codigoBarrasTercero as codigoBarrasAlmacen,
                                                                    ppal.codigoAlterno2Tercero as codigoBuzonEDI,
                                                                    movfac.numeroMovimiento as numeroFactura", "empemb.Embalaje_idEmbalaje, pro.codigoBarrasProducto");


            // print_r($datos);

            $sw = false;
            If ($datos[0]["codigoBuzonEDI"] == '') {
                echo 'El cliente de esta lista de empaque no tiene configurado el buzón de EDI para el aviso de despacho, Verifique el campo Codigo Alterno 2 en los datos del tercero<br/>';
                $sw = true;
            }

            if (trim($datos[0]["codigoBarrasTransportador"] == '' ? $datos[0]["documentoTransportador"] : $datos[0]["codigoBarrasTransportador"]) == '') {
                echo 'El embarque asociado a la lista de empaque no tiene transportador, debe completar los datos del despacho para generar el aviso<br/>';
                $sw = true;
            }

            if ($datos[0]["placaVehiculoEmbarque"] == '') {
                echo 'El embarque asociado a la lista de empaque no tiene placa del vehiculo, debe completar los datos del despacho para generar el aviso<br/>';
                $sw = true;
            }

            if ($datos[0]["numeroFactura"] == '') {
                echo 'La lista de empaque no tiene asociada una factura de venta, por favor verifique<br/>';
                $sw = true;
            }

            if ($sw == true) {
                echo 'NO SE HA GENERADO EL AVISO DE DESPACHO';
                return;
            }

            // Permite sobreescribir los archivos en el FTP
            $opciones = array('ftp' => array('overwrite' => true));

            //crea un contexto para definir los recursos
            $contexto = stream_context_create($opciones);



            // recorremos la consulta completa, haciendo rompimientos
            $reg = 0;
            $totalreg = count($datos);
            while ($reg < $totalreg) {
                // creamos el primer rompimiento por numero de documento comercial,
                // cada que este cambia, se genera un nuevo archivo
                $docAnterior = $datos[$reg]["Movimiento_idMovimiento"];
                // abrimos los archivos en forma de escritura, truncandolos a longitud cero para que queden vacios y con el permiso de sobreescribirlo
                //$desadv = fopen("../procesos/desadv.txt", "w", 0, $contexto);
                $archivo = "../procesos/edi/nuevos/desadv/desadv_" . $datos[$reg]["numeroEmpaque"] . '-' . $datos[$reg]["numeroMovimiento"] . ".txt";
                $desadv = fopen($archivo, "w", 0);
                //echo $desadv;
                $totalSegmentos = 0;
                while ($reg < $totalreg and $docAnterior == $datos[$reg]["Movimiento_idMovimiento"]) {
                    //$codigoArchivo = $datos[$reg]["Empaque_idEmpaque"] . '_' . $datos[$reg]["Movimiento_idMovimiento"];
                    $codigoArchivo = date("md") . date("Gis");

                    // INFORMACION DE LAS PARTES
                    fputs($desadv, "UNB+UNOA:2+" . $datosTercero[0]["codigoBarrasTercero"] . "+" . $datos[0]["codigoBuzonEDI"] . "+" . date("ymd") . ":" . date("Hi") . "+" . $codigoArchivo . "+        +DESADV'");
                    $totalSegmentos++;

                    // MESSAGE HEADER
                    // Encabezado del Mensaje
                    // UNH+ME000001+DESADV:D:96A:UN:EAN005'
                    fputs($desadv, "UNH+AI000001+DESADV:D:96A:UN:EAN005'");
                    $totalSegmentos++;

                    // BEGINING OF MESSAGE
                    // Inicio del Mensaje
                    // BGM+351+128576'
                    // BGM+351::9+017664+9'
                    fputs($desadv, "BGM+351::9+" . trim($datos[$reg]["numeroEmpaque"]) . "+9'");
                    $totalSegmentos++;

                    // DATE / TIME / PERIOD
                    // Fecha, hora y/o Periodo
                    // DTM+137:19981030:102'
                    fputs($desadv, "DTM+137:" . trim(str_replace("-", "", $datos[$reg]["fechaElaboracionEmpaque"])) . ":102'");
                    fputs($desadv, "DTM+11:" . trim(str_replace("-", "", $datos[$reg]["fechaEntregaEmpaque"])) . ":102'");
                    $totalSegmentos+=2;


                    // REFERENCE
                    // Referencia (Numero de Orden de compra)
                    // RFF+ON:652744'
                    fputs($desadv, "RFF+ON:" . trim($datos[$reg]["numeroMovimiento"]) . "'");
                    $totalSegmentos++;

                    // Referencia (Numero de Factura)
                    //andres
                    fputs($desadv, "RFF+IV:" . trim($datos[$reg]["numeroFactura"]) . "'");
                    $totalSegmentos++;

                    // NAME AND ADDRESS
                    // Nombre y Direccion
                    // NAD+BY+7701234567897::9'
                    // REFERENCE
                    // Referencia (NIt de Socio de Negocios)
                    // RFF+VA:800047326'
                    fputs($desadv, "NAD+BY+" . trim($datos[$reg]["codigoBarrasPrincipal"]) . "::9'");
                    fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoPrincipal"]) . "'");

                    // Empresa Proveedora
                    fputs($desadv, "NAD+SU+" . trim($datosTercero[0]["codigoBarrasTercero"]) . "::9'");
                    fputs($desadv, "RFF+VA:" . trim($datosTercero[0]["documentoTercero"]) . "'");

                    // Sitio de Entrega
                    fputs($desadv, "NAD+DP+" . trim($datos[$reg]["codigoBarrasEntrega"]) . "::9'");
                    fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoEntrega"]) . "'");

                    // Tansportador
                    fputs($desadv, "NAD+CA+" . trim($datos[$reg]["codigoBarrasTransportador"] == '' ? $datos[$reg]["documentoTransportador"] : $datos[$reg]["codigoBarrasTransportador"]) . "::9'");
                    fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoTransportador"]) . "'");
                    $totalSegmentos+=8;

                    // DETAILS OF TRANSPORT
                    // Detalles de Transporte
                    // TDT+20++30+31++++:::MQB871'
                    fputs($desadv, "TDT+20++30+31++++:::" . trim($datos[$reg]["placaVehiculoEmbarque"]) . "'");
                    $totalSegmentos++;



                    // si el primer contenedor padre no es NULL, utilizamos este para indicar el equipamento
                    // de lo contrario, utilizamos el del primer embalaje Hijo
                    if ($datos[$reg]["codigoAlternoTipoContenedorPadre"] != NULL) {
                        // EQUIPMENT DETAILS
                        // Detalles del equipamento
                        // EQD+PA'
                        //fputs($desadv, "EQD+" . trim($datos[$reg]["codigoAlternoTipoContenedorPadre"]) . "'");
                        fputs($desadv, "EQD+BX'");
                        $totalSegmentos++;

                        // MEASUREMENTS
                        // Medidas
                        // MEA+PD+AAB+KGM:1250'
                        // este segmento no se envia cuando son embalajes sueltos, o sea con indicador 0
                        if ($datos[$reg]["indicadorContenedor"] > 0) {
                            fputs($desadv, "MEA+PD+HT+CMT:" . trim((float) $datos[$reg]["altoContenedorPadre"] * 100) . "'");
                            fputs($desadv, "MEA+PD+LN+CMT:" . trim((float) $datos[$reg]["profundidadContenedorPadre"] * 100) . "'");
                            fputs($desadv, "MEA+PD+WD+CMT:" . trim((float) $datos[$reg]["anchoContenedorPadre"] * 100) . "'");
                            $totalSegmentos+=3;
                        }
                    } else {
                        // EQUIPMENT DETAILS
                        // Detalles del equipamento
                        // EQD+PA'
                        //fputs($desadv, "EQD+" . trim($datos[$reg]["codigoAlternoTipoContenedor"]) . "'");
                        fputs($desadv, "EQD+BX'");
                        $totalSegmentos++;

                        // MEASUREMENTS
                        // Medidas
                        // MEA+PD+AAB+KGM:1250'
                        // este segmento no se envia cuando son embalajes sueltos, o sea con indicador 0
                        if ($datos[$reg]["indicadorContenedor"] > 0) {
                            fputs($desadv, "MEA+PD+HT+CMT:" . trim((float) $datos[$reg]["altoContenedor"] * 100) . "'");
                            fputs($desadv, "MEA+PD+LN+CMT:" . trim((float) $datos[$reg]["profundidadContenedor"] * 100) . "'");
                            fputs($desadv, "MEA+PD+WD+CMT:" . trim((float) $datos[$reg]["anchoContenedor"] * 100) . "'");
                            $totalSegmentos+=3;
                        }
                    }



                    // SEAL NUMBER
                    // Numero de Sello del transportador
                    // SEL+21876+CA'
                    if ($datos[$reg]["selloSeguridadEmbarque"] != '') {
                        fputs($desadv, "SEL+" . substr($datos[$reg]["selloSeguridadEmbarque"], 0, 10) . "+CA'");
                        $totalSegmentos++;
                    }

                    // CONSIGNMENT PACKING SEQUENCE
                    // Numero de secuencia del paquete enviado
                    // CPS+2+1'
                    // Envio total (CPS 1)
                    $contadorCPS = 1;
                    $contadorLIN = 0;
                    fputs($desadv, "CPS+" . $contadorCPS . "'");
                    $totalSegmentos++;

                    // PACKAGE
                    // Numero, tipo e identificacion del Empaque
                    // PAC+2++202'
                    // cantidad de estibas del envio total (CPS 1)
                    fputs($desadv, "PAC+1++CS'");
                    $totalSegmentos++;

                    // PACKAGE IDENTIFICATION
                    // Identificacion del empaque (tipo de codigo)
                    // PCI+33E'
                    // fputs($desadv, "PCI+33E'");
                    // $totalSegmentos++;
                    // GOODS IDENTITY NUMBER
                    // Especificacion del numero de identificacion del empaque
                    // GIN+BJ+354123450000000014'
                    //fputs($desadv, "GIN+BJ+354123450000000014'");
                    //fputs($desadv, "GIN+BJ+".trim($datos[$reg]["ssccEmbalajePadre"])."'");
                    //$totalSegmentos++;


                    $padreAnterior = $datos[$reg]["numeroEmbalajePadre"];

                    while ($reg < $totalreg and
                    $docAnterior == $datos[$reg]["Movimiento_idMovimiento"] and
                    $padreAnterior == $datos[$reg]["numeroEmbalajePadre"]) {

                        // CONSIGNMENT PACKING SEQUENCE
                        // Numero de secuencia del paquete enviado
                        // CPS+2+1'
                        // Envio total (CPS 1)
                        $contadorCPS++;
                        fputs($desadv, "CPS+" . $contadorCPS . "+1'");
                        $totalSegmentos++;

                        // PACKAGE
                        // Numero, tipo e identificacion del Empaque
                        // PAC+2++202'
                        // cantidad de empaques del grupo padre
                        fputs($desadv, "PAC+1++CS'");
                        $totalSegmentos++;

                        // PACKAGE IDENTIFICATION
                        // Identificacion del empaque (tipo de codigo)
                        // PCI+33E'
                        fputs($desadv, "PCI+33E'");
                        $totalSegmentos++;

                        // GOODS IDENTITY NUMBER
                        // Especificacion del numero de identificacion del empaque
                        // GIN+BJ+354123450000000014'
                        fputs($desadv, "GIN+BJ+" . trim(substr($datos[$reg]["ssccEmbalaje"], 2)) . "'");
                        $totalSegmentos++;

                        $cajaAnterior = $datos[$reg]["ssccEmbalaje"];
                        while ($reg < $totalreg and
                        $docAnterior == $datos[$reg]["Movimiento_idMovimiento"] and
                        $padreAnterior == $datos[$reg]["numeroEmbalajePadre"] and
                        $cajaAnterior == $datos[$reg]["ssccEmbalaje"]) {
                            // LINE ITEM
                            // Linea de Producto (Item)
                            // LIN+1++7701234567897'
                            $contadorLIN++;
                            fputs($desadv, "LIN+" . $contadorLIN . "++" . trim($datos[$reg]["codigoBarrasProducto"]) . ":EN'");
                            $totalSegmentos++;

                            // PRODUCT ID ADDITIONAL
                            // Identificacion de producto adicional (numero de Lote)
                            // PIA+1+51028:NB'
                            //fputs($desadv, "PIA+1+51028:NB'");
                            //$totalSegmentos++;
                            // QUANTITY
                            // Cantidad de producto
                            // QTY+12:400'
                            fputs($desadv, "QTY+12:" . trim($datos[$reg]["cantidadEmpaqueEmbalaje"]) . ":NAR'");

                            $totalSegmentos++;

                            // DATE / TIME / PERIOD
                            // Fecha, Hora y/o Periodo (Fecha de Vencimiento
                            // DTM+36:19990910:102'
                            //fputs($desadv, "DTM+36:19990910:102'");
                            // $totalSegmentos++;
                            // PLACE/LOCATION IDENTIFICATION
                            // Identificacion del Sitio o Localizacion (lugar de entrega)
                            // LOC+7+7701234567897::9'
                            fputs($desadv, "LOC+7+" . trim($datos[$reg]["codigoBarrasAlmacen"] == '' ? $datos[$reg]["codigoBarrasEntrega"] : $datos[$reg]["codigoBarrasAlmacen"]) . "::9'");
                            $totalSegmentos++;

                            // PACKAGE IDENTIFICATION
                            // Identificacion del empaque (3 = Marcar las referencias del cliente (identificación serial y lote))
                            // PCI+3'
                            //fputs($desadv, "PCI+3'");
                            //$totalSegmentos++;
                            // DATE / TIME / PERIOD
                            // Fecha, Hora y/o Periodo (36 = Fecha de vencimiento)
                            // DTM+36:19990910:102'
                            //fputs($desadv, "DTM+36:19990910:102'");
                            //$totalSegmentos++;
                            // GOODS IDENTITY NUMBER
                            // Especificacion del numero de identificacion del producto (Serie o Lote)
                            // GIN+BN+354123450000000014'
                            //fputs($desadv, "GIN+BN+354123450000000014'");
                            //$totalSegmentos++;
                            $reg++;
                        }
                    }

                    // CONTROL OF TOTALS
                    // Control de totales
                    // CNT+2:12'
                    fputs($desadv, "CNT+2:" . $contadorLIN . "'");
                    $totalSegmentos++;

                    // MESSAGE TRAILER
                    // Para finalizar y chequear la integridad del mensaje (cantidad de segmentos)
                    // UNT+45+ME000001'
                    fputs($desadv, "UNT+" . $totalSegmentos . "+AI000001'");


                    // FIN DE ARCHIVO
                    fputs($desadv, "UNZ+1+" . $codigoArchivo . "'");
                }
                fclose($desadv);

                echo 'Se ha generado el archivo ' . $archivo . '<br>';
            }


            return;
        }

        // fin de la funcion



        function ExportarAvisoDespachoConsolidadoEdifact($ids) {
            // incluimos la clase Tercero
            require_once 'tercero.class.php';
            $tercero = new Tercero();
            // consultamos el tercero dueño del sistema, para los datos del Proveedor
            $datosTercero = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");

            // incluimos la clase Empaque
            require_once 'empaque.class.php';
            $empaque = new Empaque();

            // consultamos los datos de la lista de empaque
            // el orden de los datos debe ser por
            //		* idMovimiento (cada movimiento genera un archivo nuevo)
            //		* numeroEmbalajePadre (grupos de embalajes, como estibas o contenedores)
            //		* numeroEmbalaje (cada uno de los embalajes, como cajas, bultos, etc)
            //		* referenciaProducto
            $datos = $empaque->ConsultarVistaListaEmpaque(
                    "empemb.Empaque_idEmpaque IN ($ids)", "empemb.Movimiento_idMovimiento, embpad.numeroEmbalaje, emb.ssccEmbalaje, pro.referenciaProducto", "empemb.Empaque_idEmpaque, empemb.Movimiento_idMovimiento, numeroEmpaque, fechaElaboracionEmpaque, fechaEntregaEmpaque, mov.numeroMovimiento,
                                                                    ter.codigoBarrasTercero, ter.documentoTercero,
                                                                    ppal.codigoBarrasTercero as codigoBarrasPrincipal, ppal.documentoTercero as documentoPrincipal,
                                                                    ent.codigoBarrasTercero as codigoBarrasEntrega,
                                                                    ent.documentoTercero as documentoEntrega,
                                                                    emb.numeroEmbalaje, emb.ssccEmbalaje,
                                                                    con.indicadorContenedor , con.altoContenedor, con.profundidadContenedor, con.anchoContenedor,
                                                                    tipcon.codigoAlternoTipoContenedor,  tipcon.nombreTipoContenedor,
                                                                    embpad.numeroEmbalaje as numeroEmbalajePadre, embpad.ssccEmbalaje as ssccEmbalajePadre,
                                                                    conpad.indicadorContenedor as indicadorContenedorPadre, conpad.altoContenedor as altoContenedorPadre, conpad.profundidadContenedor as profundidadContenedorPadre, conpad.anchoContenedor as anchoContenedorPadre,
                                                                    tipconpad.codigoAlternoTipoContenedor as codigoAlternoTipoContenedorPadre,  tipconpad.nombreTipoContenedor as nombreTipoContenedorPadre,
                                                                    pro.codigoBarrasProducto,
                                                                    SUM(empemb.cantidadEmpaqueEmbalaje) as cantidadEmpaqueEmbalaje,
                                                                    embar.selloSeguridadEmbarque, embar.placaVehiculoEmbarque, embar.numeroGuiaEmbarque,
                                                                    tra.codigoBarrasTercero as codigoBarrasTransportador, tra.documentoTercero as documentoTransportador,
                                                                    alm.codigoBarrasTercero as codigoBarrasAlmacen,
                                                                    ppal.codigoAlterno2Tercero as codigoBuzonEDI,
                                                                    movfac.numeroMovimiento as numeroFactura", "empemb.Embalaje_idEmbalaje, pro.codigoBarrasProducto");


            // print_r($datos);

            $sw = false;
            If ($datos[0]["codigoBuzonEDI"] == '') {
                echo 'El cliente de esta lista de empaque no tiene configurado el buzón de EDI para el aviso de despacho, Verifique el campo Codigo Alterno 2 en los datos del tercero<br/>';
                $sw = true;
            }

            if (trim($datos[0]["codigoBarrasTransportador"] == '' ? $datos[0]["documentoTransportador"] : $datos[0]["codigoBarrasTransportador"]) == '') {
                echo 'El embarque asociado a la lista de empaque no tiene transportador, debe completar los datos del despacho para generar el aviso<br/>';
                $sw = true;
            }

            if ($datos[0]["placaVehiculoEmbarque"] == '') {
                echo 'El embarque asociado a la lista de empaque no tiene placa del vehiculo, debe completar los datos del despacho para generar el aviso<br/>';
                $sw = true;
            }

            if ($datos[0]["numeroFactura"] == '') {
                echo 'La lista de empaque no tiene asociada una factura de venta, por favor verifique<br/>';
                $sw = true;
            }

            if ($sw == true) {
                echo 'NO SE HA GENERADO EL AVISO DE DESPACHO';
                return;
            }

            // Permite sobreescribir los archivos en el FTP
            $opciones = array('ftp' => array('overwrite' => true));

            //crea un contexto para definir los recursos
            $contexto = stream_context_create($opciones);



            // recorremos la consulta completa, haciendo rompimientos
            $reg = 0;
            $totalreg = count($datos);
            while ($reg < $totalreg) {

                //echo $desadv;
                $totalSegmentos = 0;


                // creamos el primer rompimiento por numero de documento comercial,
                // cada que este cambia, se genera un nuevo archivo
                $docAnterior = $datos[$reg]["Movimiento_idMovimiento"];

                // abrimos los archivos en forma de escritura, truncandolos a longitud cero para que queden vacios y con el permiso de sobreescribirlo
                //$desadv = fopen("../procesos/desadv.txt", "w", 0, $contexto);
                $archivo = "../procesos/edi/nuevos/desadv/desadv_" . $datos[$reg]["numeroEmpaque"] . '-' . $datos[$reg]["numeroMovimiento"] . ".txt";
                $desadv = fopen($archivo, "w", 0);



                //$codigoArchivo = $datos[$reg]["Empaque_idEmpaque"] . '_' . $datos[$reg]["Movimiento_idMovimiento"];
                $codigoArchivo = date("md") . date("Gis");

                // INFORMACION DE LAS PARTES
                fputs($desadv, "UNB+UNOA:2+" . $datosTercero[0]["codigoBarrasTercero"] . "+" . $datos[0]["codigoBuzonEDI"] . "+" . date("ymd") . ":" . date("Hi") . "+" . $codigoArchivo . "+        +DESADV'");
                $totalSegmentos++;

                // MESSAGE HEADER
                // Encabezado del Mensaje
                // UNH+ME000001+DESADV:D:96A:UN:EAN005'
                fputs($desadv, "UNH+AI000001+DESADV:D:96A:UN:EAN005'");
                $totalSegmentos++;

                // BEGINING OF MESSAGE
                // Inicio del Mensaje
                // BGM+351+128576'
                // BGM+351::9+017664+9'
                fputs($desadv, "BGM+351::9+" . trim($datos[$reg]["numeroEmpaque"]) . "+9'");
                $totalSegmentos++;

                // DATE / TIME / PERIOD
                // Fecha, hora y/o Periodo
                // DTM+137:19981030:102'
                fputs($desadv, "DTM+137:" . trim(str_replace("-", "", $datos[$reg]["fechaElaboracionEmpaque"])) . ":102'");
                fputs($desadv, "DTM+11:" . trim(str_replace("-", "", $datos[$reg]["fechaEntregaEmpaque"])) . ":102'");
                $totalSegmentos+=2;


                // REFERENCE
                // Referencia (Numero de Orden de compra)
                // RFF+ON:652744'
                fputs($desadv, "RFF+ON:" . trim($datos[$reg]["numeroMovimiento"]) . "'");
                $totalSegmentos++;

                // Referencia (Numero de Factura)
                //andres
                fputs($desadv, "RFF+IV:" . trim($datos[$reg]["numeroFactura"]) . "'");
                $totalSegmentos++;

                // NAME AND ADDRESS
                // Nombre y Direccion
                // NAD+BY+7701234567897::9'
                // REFERENCE
                // Referencia (NIt de Socio de Negocios)
                // RFF+VA:800047326'
                fputs($desadv, "NAD+BY+" . trim($datos[$reg]["codigoBarrasPrincipal"]) . "::9'");
                fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoPrincipal"]) . "'");

                // Empresa Proveedora
                fputs($desadv, "NAD+SU+" . trim($datosTercero[0]["codigoBarrasTercero"]) . "::9'");
                fputs($desadv, "RFF+VA:" . trim($datosTercero[0]["documentoTercero"]) . "'");

                // Sitio de Entrega
                fputs($desadv, "NAD+DP+" . trim($datos[$reg]["codigoBarrasEntrega"]) . "::9'");
                fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoEntrega"]) . "'");

                // Tansportador
                fputs($desadv, "NAD+CA+" . trim($datos[$reg]["codigoBarrasTransportador"] == '' ? $datos[$reg]["documentoTransportador"] : $datos[$reg]["codigoBarrasTransportador"]) . "::9'");
                fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoTransportador"]) . "'");
                $totalSegmentos+=8;

                // DETAILS OF TRANSPORT
                // Detalles de Transporte
                // TDT+20++30+31++++:::MQB871'
                fputs($desadv, "TDT+20++30+31++++:::" . trim($datos[$reg]["placaVehiculoEmbarque"]) . "'");
                $totalSegmentos++;




                // si el primer contenedor padre no es NULL, utilizamos este para indicar el equipamento
                // de lo contrario, utilizamos el del primer embalaje Hijo
                if ($datos[$reg]["codigoAlternoTipoContenedorPadre"] != NULL) {
                    // EQUIPMENT DETAILS
                    // Detalles del equipamento
                    // EQD+PA'
                    //fputs($desadv, "EQD+" . trim($datos[$reg]["codigoAlternoTipoContenedorPadre"]) . "'");
                    fputs($desadv, "EQD+BX'");
                    $totalSegmentos++;

                    // MEASUREMENTS
                    // Medidas
                    // MEA+PD+AAB+KGM:1250'
                    // este segmento no se envia cuando son embalajes sueltos, o sea con indicador 0
                    if ($datos[$reg]["indicadorContenedorPadre"] > 0) {
                        fputs($desadv, "MEA+PD+HT+CMT:" . trim((float) $datos[$reg]["altoContenedorPadre"] * 100) . "'");
                        fputs($desadv, "MEA+PD+LN+CMT:" . trim((float) $datos[$reg]["profundidadContenedorPadre"] * 100) . "'");
                        fputs($desadv, "MEA+PD+WD+CMT:" . trim((float) $datos[$reg]["anchoContenedorPadre"] * 100) . "'");
                        $totalSegmentos+=3;
                    }
                } else {
                    // EQUIPMENT DETAILS
                    // Detalles del equipamento
                    // EQD+PA'
                    //fputs($desadv, "EQD+" . trim($datos[$reg]["codigoAlternoTipoContenedor"]) . "'");
                    fputs($desadv, "EQD+BX'");
                    $totalSegmentos++;

                    // MEASUREMENTS
                    // Medidas
                    // MEA+PD+AAB+KGM:1250'
                    // este segmento no se envia cuando son embalajes sueltos, o sea con indicador 0
                    if ($datos[$reg]["indicadorContenedor"] > 0) {
                        fputs($desadv, "MEA+PD+HT+CMT:" . trim((float) $datos[$reg]["altoContenedor"] * 100) . "'");
                        fputs($desadv, "MEA+PD+LN+CMT:" . trim((float) $datos[$reg]["profundidadContenedor"] * 100) . "'");
                        fputs($desadv, "MEA+PD+WD+CMT:" . trim((float) $datos[$reg]["anchoContenedor"] * 100) . "'");
                        $totalSegmentos+=3;
                    }
                }



                // SEAL NUMBER
                // Numero de Sello del transportador
                // SEL+21876+CA'
                if ($datos[$reg]["selloSeguridadEmbarque"] != '') {
                    fputs($desadv, "SEL+" . substr($datos[$reg]["selloSeguridadEmbarque"], 0, 10) . "+CA'");
                    $totalSegmentos++;
                }

                // CONSIGNMENT PACKING SEQUENCE
                // Numero de secuencia del paquete enviado
                // CPS+2+1'
                // Envio total (CPS 1)
                $contadorCPS = 1;
                $contadorLIN = 0;
                fputs($desadv, "CPS+" . $contadorCPS . "'");
                $totalSegmentos++;

                // PACKAGE
                // Numero, tipo e identificacion del Empaque
                // PAC+2++202'
                // cantidad de estibas del envio total (CPS 1)
                fputs($desadv, "PAC+1++CS'");
                $totalSegmentos++;

                $contadorCPSPadre = 0;

                while ($reg < $totalreg and $docAnterior == $datos[$reg]["Movimiento_idMovimiento"]) {
                    // PACKAGE IDENTIFICATION
                    // Identificacion del empaque (tipo de codigo)
                    // PCI+33E'
                    // fputs($desadv, "PCI+33E'");
                    // $totalSegmentos++;
                    // GOODS IDENTITY NUMBER
                    // Especificacion del numero de identificacion del empaque
                    // GIN+BJ+354123450000000014'
                    //fputs($desadv, "GIN+BJ+354123450000000014'");
                    //fputs($desadv, "GIN+BJ+".trim($datos[$reg]["ssccEmbalajePadre"])."'");
                    //$totalSegmentos++;
                    //				$padreAnterior = $datos[$reg]["numeroEmbalajePadre"];
                    //
                    //				while($reg < $totalreg and
                    //				$docAnterior == $datos[$reg]["Movimiento_idMovimiento"] and
                    //				$padreAnterior == $datos[$reg]["numeroEmbalajePadre"])
                    //				{
                    // CONSIGNMENT PACKING SEQUENCE
                    // Numero de secuencia del paquete enviado
                    // CPS+2+1'
                    // Envio total (CPS 1)
                    $contadorCPS++;
                    fputs($desadv, "CPS+" . $contadorCPS . "+1'");
                    $totalSegmentos++;

                    $contadorCPSPadre = $contadorCPS;
                    // PACKAGE
                    // Numero, tipo e identificacion del Empaque
                    // PAC+2++202'
                    // cantidad de empaques del grupo padre
                    fputs($desadv, "PAC+1++CS'");
                    $totalSegmentos++;

                    // PACKAGE IDENTIFICATION
                    // Identificacion del empaque (tipo de codigo)
                    // PCI+33E'
                    fputs($desadv, "PCI+33E'");
                    $totalSegmentos++;

                    // GOODS IDENTITY NUMBER
                    // Especificacion del numero de identificacion del empaque
                    // GIN+BJ+354123450000000014'
                    fputs($desadv, "GIN+BJ+" . trim(substr($datos[$reg]["ssccEmbalajePadre"], 2)) . "'");
                    $totalSegmentos++;

                    $padreAnterior = $datos[$reg]["ssccEmbalajePadre"];

                    while ($reg < $totalreg and
                    $docAnterior == $datos[$reg]["Movimiento_idMovimiento"] and
                    $padreAnterior == $datos[$reg]["ssccEmbalajePadre"]) {
                        $contadorCPS++;
                        //                                            fputs($desadv, "CPS+" . $contadorCPS . "+".$contadorCPSPadre."'");
                        fputs($desadv, "CPS+" . $contadorCPS . "+1'");
                        $totalSegmentos++;

                        // PACKAGE
                        // Numero, tipo e identificacion del Empaque
                        // PAC+2++202'
                        // cantidad de empaques del grupo padre
                        fputs($desadv, "PAC+1++CS'");
                        $totalSegmentos++;

                        // PACKAGE IDENTIFICATION
                        // Identificacion del empaque (tipo de codigo)
                        // PCI+33E'
                        fputs($desadv, "PCI+33E'");
                        $totalSegmentos++;

                        // GOODS IDENTITY NUMBER
                        // Especificacion del numero de identificacion del empaque
                        // GIN+BJ+354123450000000014'
                        fputs($desadv, "GIN+BJ+" . trim(substr($datos[$reg]["ssccEmbalaje"], 2)) . "'");
                        $totalSegmentos++;

                        //                                            $embalajeAnterior = $datos[$reg]["ssccEmbalaje"];
                        //                                            while ($reg < $totalreg and
                        //							$docAnterior == $datos[$reg]["Movimiento_idMovimiento"] and
                        //							$padreAnterior == $datos[$reg]["ssccEmbalajePadre"] and
                        //							$embalajeAnterior == $datos[$reg]["ssccEmbalaje"])
                        //                                            {
                        // LINE ITEM
                        // Linea de Producto (Item)
                        // LIN+1++7701234567897'
                        $contadorLIN++;
                        fputs($desadv, "LIN+" . $contadorLIN . "++" . trim($datos[$reg]["codigoBarrasProducto"]) . ":EN'");
                        $totalSegmentos++;

                        // PRODUCT ID ADDITIONAL
                        // Identificacion de producto adicional (numero de Lote)
                        // PIA+1+51028:NB'
                        //fputs($desadv, "PIA+1+51028:NB'");
                        //$totalSegmentos++;
                        // QUANTITY
                        // Cantidad de producto
                        // QTY+12:400'
                        fputs($desadv, "QTY+12:" . trim($datos[$reg]["cantidadEmpaqueEmbalaje"]) . ":NAR'");

                        $totalSegmentos++;

                        // DATE / TIME / PERIOD
                        // Fecha, Hora y/o Periodo (Fecha de Vencimiento
                        // DTM+36:19990910:102'
                        //fputs($desadv, "DTM+36:19990910:102'");
                        // $totalSegmentos++;
                        // PLACE/LOCATION IDENTIFICATION
                        // Identificacion del Sitio o Localizacion (lugar de entrega)
                        // LOC+7+7701234567897::9'
                        fputs($desadv, "LOC+7+" . trim($datos[$reg]["codigoBarrasAlmacen"] == '' ? $datos[$reg]["codigoBarrasEntrega"] : $datos[$reg]["codigoBarrasAlmacen"]) . "::9'");
                        $totalSegmentos++;

                        // PACKAGE IDENTIFICATION
                        // Identificacion del empaque (3 = Marcar las referencias del cliente (identificación serial y lote))
                        // PCI+3'
                        //fputs($desadv, "PCI+3'");
                        //$totalSegmentos++;
                        // DATE / TIME / PERIOD
                        // Fecha, Hora y/o Periodo (36 = Fecha de vencimiento)
                        // DTM+36:19990910:102'
                        //fputs($desadv, "DTM+36:19990910:102'");
                        //$totalSegmentos++;
                        // GOODS IDENTITY NUMBER
                        // Especificacion del numero de identificacion del producto (Serie o Lote)
                        // GIN+BN+354123450000000014'
                        //fputs($desadv, "GIN+BN+354123450000000014'");
                        //$totalSegmentos++;
                        $reg++;
                        //                                            }
                    }
                    //				}
                }


                // CONTROL OF TOTALS
                // Control de totales
                // CNT+2:12'
                fputs($desadv, "CNT+2:" . $contadorLIN . "'");
                $totalSegmentos++;

                // MESSAGE TRAILER
                // Para finalizar y chequear la integridad del mensaje (cantidad de segmentos)
                // UNT+45+ME000001'
                fputs($desadv, "UNT+" . $totalSegmentos . "+AI000001'");


                // FIN DE ARCHIVO
                fputs($desadv, "UNZ+1+" . $codigoArchivo . "'");

                fclose($desadv);

                echo 'Se ha generado el archivo ' . $archivo . '<br>';
            }


            return;
        }

        // fin de la funcion

        function ExportarAvisoDespachoConsolidadoExito($ids) {
            // incluimos la clase Tercero
            require_once 'tercero.class.php';
            $tercero = new Tercero();
            // consultamos el tercero dueño del sistema, para los datos del Proveedor
            $datosTercero = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");

            // incluimos la clase Empaque
            require_once 'empaque.class.php';
            $empaque = new Empaque();

            // consultamos los datos de la lista de empaque
            // el orden de los datos debe ser por
            //		* idMovimiento (cada movimiento genera un archivo nuevo)
            //		* numeroEmbalajePadre (grupos de embalajes, como estibas o contenedores)
            //		* numeroEmbalaje (cada uno de los embalajes, como cajas, bultos, etc)
            //		* referenciaProducto
            $datos = $empaque->ConsultarVistaListaEmpaque(
                    "empemb.Empaque_idEmpaque IN ($ids)", "empemb.Movimiento_idMovimiento, embpad.numeroEmbalaje, pro.referenciaProducto", "empemb.Empaque_idEmpaque, empemb.Movimiento_idMovimiento, numeroEmpaque, fechaElaboracionEmpaque, fechaEntregaEmpaque, mov.numeroMovimiento,
                                                                    ter.codigoBarrasTercero, ter.documentoTercero,
                                                                    ppal.codigoBarrasTercero as codigoBarrasPrincipal, ppal.documentoTercero as documentoPrincipal,
                                                                    ent.codigoBarrasTercero as codigoBarrasEntrega,
                                                                    ent.documentoTercero as documentoEntrega,
                                                                    con.indicadorContenedor , con.altoContenedor, con.profundidadContenedor, con.anchoContenedor,
                                                                    tipcon.codigoAlternoTipoContenedor,  tipcon.nombreTipoContenedor,
                                                                    embpad.numeroEmbalaje as numeroEmbalajePadre, embpad.ssccEmbalaje as ssccEmbalajePadre,
                                                                    conpad.indicadorContenedor as indicadorContenedorPadre, conpad.altoContenedor as altoContenedorPadre, conpad.profundidadContenedor as profundidadContenedorPadre, conpad.anchoContenedor as anchoContenedorPadre,
                                                                    tipconpad.codigoAlternoTipoContenedor as codigoAlternoTipoContenedorPadre,  tipconpad.nombreTipoContenedor as nombreTipoContenedorPadre,
                                                                    pro.codigoBarrasProducto,
                                                                    SUM(empemb.cantidadEmpaqueEmbalaje) as cantidadEmpaqueEmbalaje,
                                                                    embar.selloSeguridadEmbarque, embar.placaVehiculoEmbarque, embar.numeroGuiaEmbarque,
                                                                    tra.codigoBarrasTercero as codigoBarrasTransportador, tra.documentoTercero as documentoTransportador,
                                                                    alm.codigoBarrasTercero as codigoBarrasAlmacen,
                                                                    ppal.codigoAlterno2Tercero as codigoBuzonEDI,
                                                                    movfac.numeroMovimiento as numeroFactura", "pro.codigoBarrasProducto");


            // print_r($datos);

            $sw = false;
            If ($datos[0]["codigoBuzonEDI"] == '') {
                echo 'El cliente de esta lista de empaque no tiene configurado el buzón de EDI para el aviso de despacho, Verifique el campo Codigo Alterno 2 en los datos del tercero<br/>';
                $sw = true;
            }

            if (trim($datos[0]["codigoBarrasTransportador"] == '' ? $datos[0]["documentoTransportador"] : $datos[0]["codigoBarrasTransportador"]) == '') {
                echo 'El embarque asociado a la lista de empaque no tiene transportador, debe completar los datos del despacho para generar el aviso<br/>';
                $sw = true;
            }

            if ($datos[0]["placaVehiculoEmbarque"] == '') {
                echo 'El embarque asociado a la lista de empaque no tiene placa del vehiculo, debe completar los datos del despacho para generar el aviso<br/>';
                $sw = true;
            }

            if ($datos[0]["numeroFactura"] == '') {
                echo 'La lista de empaque no tiene asociada una factura de venta, por favor verifique<br/>';
                $sw = true;
            }

            if ($sw == true) {
                echo 'NO SE HA GENERADO EL AVISO DE DESPACHO';
                return;
            }

            // Permite sobreescribir los archivos en el FTP
            $opciones = array('ftp' => array('overwrite' => true));

            //crea un contexto para definir los recursos
            $contexto = stream_context_create($opciones);



            // recorremos la consulta completa, haciendo rompimientos
            $reg = 0;
            $totalreg = count($datos);
            while ($reg < $totalreg) {

                //echo $desadv;
                $totalSegmentos = 0;


                // creamos el primer rompimiento por numero de documento comercial,
                // cada que este cambia, se genera un nuevo archivo
                $docAnterior = $datos[$reg]["Movimiento_idMovimiento"];

                // abrimos los archivos en forma de escritura, truncandolos a longitud cero para que queden vacios y con el permiso de sobreescribirlo
                //$desadv = fopen("../procesos/desadv.txt", "w", 0, $contexto);
                $archivo = "../procesos/edi/nuevos/desadv/desadv_" . $datos[$reg]["numeroEmpaque"] . '-' . $datos[$reg]["numeroMovimiento"] . ".txt";
                $desadv = fopen($archivo, "w", 0);



                //$codigoArchivo = $datos[$reg]["Empaque_idEmpaque"] . '_' . $datos[$reg]["Movimiento_idMovimiento"];
                $codigoArchivo = date("md") . date("Gis");

                // INFORMACION DE LAS PARTES
                fputs($desadv, "UNB+UNOA:2+" . $datosTercero[0]["codigoBarrasTercero"] . "+" . $datos[0]["codigoBuzonEDI"] . "+" . date("ymd") . ":" . date("Hi") . "+" . $codigoArchivo . "+        +DESADV'");
                $totalSegmentos++;

                // MESSAGE HEADER
                // Encabezado del Mensaje
                // UNH+ME000001+DESADV:D:96A:UN:EAN005'
                fputs($desadv, "UNH+AI000001+DESADV:D:96A:UN:EAN005'");
                $totalSegmentos++;

                // BEGINING OF MESSAGE
                // Inicio del Mensaje
                // BGM+351+128576'
                // BGM+351::9+017664+9'
                fputs($desadv, "BGM+351::9+" . trim($datos[$reg]["numeroEmpaque"]) . "+9'");
                $totalSegmentos++;

                // DATE / TIME / PERIOD
                // Fecha, hora y/o Periodo
                // DTM+137:19981030:102'
                fputs($desadv, "DTM+137:" . trim(str_replace("-", "", $datos[$reg]["fechaElaboracionEmpaque"])) . ":102'");
                fputs($desadv, "DTM+11:" . trim(str_replace("-", "", $datos[$reg]["fechaEntregaEmpaque"])) . ":102'");
                $totalSegmentos+=2;


                // REFERENCE
                // Referencia (Numero de Orden de compra)
                // RFF+ON:652744'
                fputs($desadv, "RFF+ON:" . trim($datos[$reg]["numeroMovimiento"]) . "'");
                $totalSegmentos++;

                // Referencia (Numero de Factura)
                //andres
                fputs($desadv, "RFF+IV:" . trim($datos[$reg]["numeroFactura"]) . "'");
                $totalSegmentos++;

                // NAME AND ADDRESS
                // Nombre y Direccion
                // NAD+BY+7701234567897::9'
                // REFERENCE
                // Referencia (NIt de Socio de Negocios)
                // RFF+VA:800047326'
                fputs($desadv, "NAD+BY+" . trim($datos[$reg]["codigoBarrasPrincipal"]) . "::9'");
                fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoPrincipal"]) . "'");

                // Empresa Proveedora
                fputs($desadv, "NAD+SU+" . trim($datosTercero[0]["codigoBarrasTercero"]) . "::9'");
                fputs($desadv, "RFF+VA:" . trim($datosTercero[0]["documentoTercero"]) . "'");

                // Sitio de Entrega
                fputs($desadv, "NAD+DP+" . trim($datos[$reg]["codigoBarrasEntrega"]) . "::9'");
                fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoEntrega"]) . "'");

                // Tansportador
                fputs($desadv, "NAD+CA+" . trim($datos[$reg]["codigoBarrasTransportador"] == '' ? $datos[$reg]["documentoTransportador"] : $datos[$reg]["codigoBarrasTransportador"]) . "::9'");
                fputs($desadv, "RFF+VA:" . trim($datos[$reg]["documentoTransportador"]) . "'");
                $totalSegmentos+=8;

                // DETAILS OF TRANSPORT
                // Detalles de Transporte
                // TDT+20++30+31++++:::MQB871'
                fputs($desadv, "TDT+20++30+31++++:::" . trim($datos[$reg]["placaVehiculoEmbarque"]) . "'");
                $totalSegmentos++;




                // si el primer contenedor padre no es NULL, utilizamos este para indicar el equipamento
                // de lo contrario, utilizamos el del primer embalaje Hijo
                if ($datos[$reg]["codigoAlternoTipoContenedorPadre"] != NULL) {
                    // EQUIPMENT DETAILS
                    // Detalles del equipamento
                    // EQD+PA'
                    //fputs($desadv, "EQD+" . trim($datos[$reg]["codigoAlternoTipoContenedorPadre"]) . "'");
                    fputs($desadv, "EQD+BX'");
                    $totalSegmentos++;

                    // MEASUREMENTS
                    // Medidas
                    // MEA+PD+AAB+KGM:1250'
                    // este segmento no se envia cuando son embalajes sueltos, o sea con indicador 0
                    if ($datos[$reg]["indicadorContenedorPadre"] > 0) {
                        fputs($desadv, "MEA+PD+HT+CMT:" . trim((float) $datos[$reg]["altoContenedorPadre"] * 100) . "'");
                        fputs($desadv, "MEA+PD+LN+CMT:" . trim((float) $datos[$reg]["profundidadContenedorPadre"] * 100) . "'");
                        fputs($desadv, "MEA+PD+WD+CMT:" . trim((float) $datos[$reg]["anchoContenedorPadre"] * 100) . "'");
                        $totalSegmentos+=3;
                    }
                } else {
                    // EQUIPMENT DETAILS
                    // Detalles del equipamento
                    // EQD+PA'
                    //fputs($desadv, "EQD+" . trim($datos[$reg]["codigoAlternoTipoContenedor"]) . "'");
                    fputs($desadv, "EQD+BX'");
                    $totalSegmentos++;

                    // MEASUREMENTS
                    // Medidas
                    // MEA+PD+AAB+KGM:1250'
                    // este segmento no se envia cuando son embalajes sueltos, o sea con indicador 0
                    if ($datos[$reg]["indicadorContenedor"] > 0) {
                        fputs($desadv, "MEA+PD+HT+CMT:" . trim((float) $datos[$reg]["altoContenedor"] * 100) . "'");
                        fputs($desadv, "MEA+PD+LN+CMT:" . trim((float) $datos[$reg]["profundidadContenedor"] * 100) . "'");
                        fputs($desadv, "MEA+PD+WD+CMT:" . trim((float) $datos[$reg]["anchoContenedor"] * 100) . "'");
                        $totalSegmentos+=3;
                    }
                }



                // SEAL NUMBER
                // Numero de Sello del transportador
                // SEL+21876+CA'
                if ($datos[$reg]["selloSeguridadEmbarque"] != '') {
                    fputs($desadv, "SEL+" . substr($datos[$reg]["selloSeguridadEmbarque"], 0, 10) . "+CA'");
                    $totalSegmentos++;
                }

                // CONSIGNMENT PACKING SEQUENCE
                // Numero de secuencia del paquete enviado
                // CPS+2+1'
                // Envio total (CPS 1)
                $contadorCPS = 1;
                $contadorLIN = 0;
                fputs($desadv, "CPS+" . $contadorCPS . "'");
                $totalSegmentos++;

                // PACKAGE
                // Numero, tipo e identificacion del Empaque
                // PAC+2++202'
                // cantidad de estibas del envio total (CPS 1)
                fputs($desadv, "PAC+1++CS'");
                $totalSegmentos++;

                $contadorCPSPadre = 0;

                while ($reg < $totalreg and $docAnterior == $datos[$reg]["Movimiento_idMovimiento"]) {
                    // PACKAGE IDENTIFICATION
                    // Identificacion del empaque (tipo de codigo)
                    // PCI+33E'
                    // fputs($desadv, "PCI+33E'");
                    // $totalSegmentos++;
                    // GOODS IDENTITY NUMBER
                    // Especificacion del numero de identificacion del empaque
                    // GIN+BJ+354123450000000014'
                    //fputs($desadv, "GIN+BJ+354123450000000014'");
                    //fputs($desadv, "GIN+BJ+".trim($datos[$reg]["ssccEmbalajePadre"])."'");
                    //$totalSegmentos++;
                    //				$padreAnterior = $datos[$reg]["numeroEmbalajePadre"];
                    //
                    //				while($reg < $totalreg and
                    //				$docAnterior == $datos[$reg]["Movimiento_idMovimiento"] and
                    //				$padreAnterior == $datos[$reg]["numeroEmbalajePadre"])
                    //				{
                    // CONSIGNMENT PACKING SEQUENCE
                    // Numero de secuencia del paquete enviado
                    // CPS+2+1'
                    // Envio total (CPS 1)
                    $contadorCPS++;
                    fputs($desadv, "CPS+" . $contadorCPS . "+1'");
                    $totalSegmentos++;

                    $contadorCPSPadre = $contadorCPS;
                    // PACKAGE
                    // Numero, tipo e identificacion del Empaque
                    // PAC+2++202'
                    // cantidad de empaques del grupo padre
                    fputs($desadv, "PAC+1++CS'");
                    $totalSegmentos++;

                    // PACKAGE IDENTIFICATION
                    // Identificacion del empaque (tipo de codigo)
                    // PCI+33E'
                    fputs($desadv, "PCI+33E'");
                    $totalSegmentos++;

                    // GOODS IDENTITY NUMBER
                    // Especificacion del numero de identificacion del empaque
                    // GIN+BJ+354123450000000014'
                    fputs($desadv, "GIN+BJ+" . trim(substr($datos[$reg]["ssccEmbalajePadre"], 2)) . "'");
                    $totalSegmentos++;

                    $padreAnterior = $datos[$reg]["ssccEmbalajePadre"];

                    while ($reg < $totalreg and
                    $docAnterior == $datos[$reg]["Movimiento_idMovimiento"] and
                    $padreAnterior == $datos[$reg]["ssccEmbalajePadre"]) {
                        //                    $contadorCPS++;
                        //                                            fputs($desadv, "CPS+" . $contadorCPS . "+".$contadorCPSPadre."'");
                        //                    fputs($desadv, "CPS+" . $contadorCPS . "+1'");
                        //                    $totalSegmentos++;
                        // PACKAGE
                        // Numero, tipo e identificacion del Empaque
                        // PAC+2++202'
                        // cantidad de empaques del grupo padre
                        //                    fputs($desadv, "PAC+1++CS'");
                        //                    $totalSegmentos++;
                        // PACKAGE IDENTIFICATION
                        // Identificacion del empaque (tipo de codigo)
                        // PCI+33E'
                        //                    fputs($desadv, "PCI+33E'");
                        //                    $totalSegmentos++;
                        // GOODS IDENTITY NUMBER
                        // Especificacion del numero de identificacion del empaque
                        // GIN+BJ+354123450000000014'
                        //                    fputs($desadv, "GIN+BJ+" . trim(substr($datos[$reg]["ssccEmbalaje"], 2)) . "'");
                        //                    $totalSegmentos++;
                        //                                            $embalajeAnterior = $datos[$reg]["ssccEmbalaje"];
                        //                                            while ($reg < $totalreg and
                        //							$docAnterior == $datos[$reg]["Movimiento_idMovimiento"] and
                        //							$padreAnterior == $datos[$reg]["ssccEmbalajePadre"] and
                        //							$embalajeAnterior == $datos[$reg]["ssccEmbalaje"])
                        //                                            {
                        // LINE ITEM
                        // Linea de Producto (Item)
                        // LIN+1++7701234567897'
                        $contadorLIN++;
                        fputs($desadv, "LIN+" . $contadorLIN . "++" . trim($datos[$reg]["codigoBarrasProducto"]) . ":EN'");
                        $totalSegmentos++;

                        // PRODUCT ID ADDITIONAL
                        // Identificacion de producto adicional (numero de Lote)
                        // PIA+1+51028:NB'
                        //fputs($desadv, "PIA+1+51028:NB'");
                        //$totalSegmentos++;
                        // QUANTITY
                        // Cantidad de producto
                        // QTY+12:400'
                        fputs($desadv, "QTY+12:" . trim($datos[$reg]["cantidadEmpaqueEmbalaje"]) . ":NAR'");

                        $totalSegmentos++;

                        // DATE / TIME / PERIOD
                        // Fecha, Hora y/o Periodo (Fecha de Vencimiento
                        // DTM+36:19990910:102'
                        //fputs($desadv, "DTM+36:19990910:102'");
                        // $totalSegmentos++;
                        // PLACE/LOCATION IDENTIFICATION
                        // Identificacion del Sitio o Localizacion (lugar de entrega)
                        // LOC+7+7701234567897::9'
                        fputs($desadv, "LOC+7+" . trim($datos[$reg]["codigoBarrasAlmacen"] == '' ? $datos[$reg]["codigoBarrasEntrega"] : $datos[$reg]["codigoBarrasAlmacen"]) . "::9'");
                        $totalSegmentos++;

                        // PACKAGE IDENTIFICATION
                        // Identificacion del empaque (3 = Marcar las referencias del cliente (identificación serial y lote))
                        // PCI+3'
                        //fputs($desadv, "PCI+3'");
                        //$totalSegmentos++;
                        // DATE / TIME / PERIOD
                        // Fecha, Hora y/o Periodo (36 = Fecha de vencimiento)
                        // DTM+36:19990910:102'
                        //fputs($desadv, "DTM+36:19990910:102'");
                        //$totalSegmentos++;
                        // GOODS IDENTITY NUMBER
                        // Especificacion del numero de identificacion del producto (Serie o Lote)
                        // GIN+BN+354123450000000014'
                        //fputs($desadv, "GIN+BN+354123450000000014'");
                        //$totalSegmentos++;
                        $reg++;
                        //                                            }
                    }
                    //				}
                }


                // CONTROL OF TOTALS
                // Control de totales
                // CNT+2:12'
                fputs($desadv, "CNT+2:" . $contadorLIN . "'");
                $totalSegmentos++;

                // MESSAGE TRAILER
                // Para finalizar y chequear la integridad del mensaje (cantidad de segmentos)
                // UNT+45+ME000001'
                fputs($desadv, "UNT+" . $totalSegmentos . "+AI000001'");


                // FIN DE ARCHIVO
                fputs($desadv, "UNZ+1+" . $codigoArchivo . "'");

                fclose($desadv);

                echo 'Se ha generado el archivo ' . $archivo . '<br>';
            }


            return;
        }

        function ImportarMaestroProductosExcel($ruta) {
            set_time_limit(0);

            require_once('../clases/producto.class.php');
            $producto = new Producto();
            require_once('../clases/talla.class.php');
            $talla = new Talla();
            require_once('../clases/tallacomplemento.class.php');
            $tallacomplemento = new TallaComplemento();
            require_once('../clases/color.class.php');
            $color = new GrupoColor();
            require_once('../clases/fichatecnica.class.php');
            $fichatecnica = new FichaTecnica();
            require_once('../clases/marca.class.php');
            $marca = new Marca();
            require_once('../clases/tipoproducto.class.php');
            $tipoproducto = new TipoProducto();
            require_once('../clases/tiponegocio.class.php');
            $tiponegocio = new TipoNegocio();
            require_once('../clases/temporada.class.php');
            $temporada = new Temporada();
            require_once('../clases/estadoconservacion.class.php');
            $estadoconservacion = new EstadoConservacion();
            require_once('../clases/composicion.class.php');
            $composicion = new Composicion();
            require_once('../clases/posicionarancelaria.class.php');
            $posicionarancelaria = new PosicionArancelaria();
            require_once('../clases/pais.class.php');
            $pais = new Pais();
            require_once('../clases/categoria.class.php');
            $categoria = new Categoria();
            require_once('../clases/clima.class.php');
            $clima = new Clima();
            require_once('../clases/estrategia.class.php');
            $estrategia = new Estrategia();
            require_once('../clases/difusion.class.php');
            $difusion = new Difusion();
            require_once('../clases/seccion.class.php');
            $seccion = new Seccion();
            require_once('../clases/evento.class.php');
            $evento = new Evento();
            require_once('../clases/clienteobjetivo.class.php');
            $clienteobjetivo = new ClienteObjetivo();
            require_once('../clases/esquemaproducto.class.php');
            $esquemaproducto = new EsquemaProducto();
            require_once('../clases/codigobarras.class.php');
            $codigobarras = new CodigoBarras();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/bodega.class.php');
            $bodega = new Bodega();
            require_once('../clases/unidadmedida.class.php');
            $unidadmedida = new UnidadMedida();

            require_once '../clases/segmentooperacion.class.php';
            $segmento = new SegmentoOperacion();

            //Lo de hilos
            require_once('../clases/calibreHilo.class.php');
            $calibreHilo = new CalibreHilo();
            require_once('../clases/tono.class.php');
            $tono = new Tono();
            require_once('../clases/pinta.class.php');
            $pinta = new Pinta();


            //echo '1';
            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            //echo '2';
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta); //echo '3';
            $extension = array_pop($rutacompleta); //echo '4';
            if (!isset($objReader)) {
                if ($extension == 'xlsx') {
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007'); /* echo 'xlsx'; */
                } else {
                    $objReader = PHPExcel_IOFactory::createReader('Excel5'); /* echo 'xls'; */
                }
            }
            //echo '5';
            $objReader->setLoadSheetsOnly('producto'); //echo '6';
            $objReader->setReadDataOnly(true); //echo '7'.$ruta;
            $objPHPExcel = $objReader->load($ruta); //echo '8';

            $objWorksheet = $objPHPExcel->getActiveSheet(); //echo '9';
            $highestRow = $objWorksheet->getHighestRow(); //echo '10';// e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); //echo '11';// e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //echo '12';// e.g.
            // creamos un array para almacenar los campos del archivo
            $referencias = array();
            $posRef = -1;
            //echo '13';

            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, $fila)->getValue() != NULL) 
            {
                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 59
                for ($columna = 0; $columna <= 63; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $referencias[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // tomamos las imagenes del producto y le adicionamos la ruta donde deben estar esta imagenes
                if ($referencias[$posRef]["imagen1Producto"] != '')
                    $referencias[$posRef]["imagen1Producto"] = "../fotosficha/catalogo/" . $referencias[$posRef]["imagen1Producto"];

                if ($referencias[$posRef]["imagen2Producto"] != '')
                    $referencias[$posRef]["imagen2Producto"] = "../fotosficha/catalogo/" . $referencias[$posRef]["imagen2Producto"];

                // cada que llenemos un referencias, hacemos las verificaciones de codigos necesarioos
                // verificamos cuales campos de la clasificacion del prodcuto estan llenos para armar la codificacion de clasificacion
                $referencias[$posRef]["clasificacionProducto"] = '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["PT"]) and $referencias[$posRef]["PT"] != NULL) ? '*01*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["IN"]) and $referencias[$posRef]["IN"] != NULL) ? '*04*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["MP"]) and $referencias[$posRef]["MP"] != NULL) ? '*02*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["SE"]) and $referencias[$posRef]["SE"] != NULL) ? '*03*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["CO"]) and $referencias[$posRef]["CO"] != NULL) ? '*06*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["KI"]) and $referencias[$posRef]["KI"] != NULL) ? '*07*' : '';

                // si el iva incluido esta lleno se cambia por un 1
                $referencias[$posRef]["ivaIncluidoProducto"] = ((!empty($referencias[$posRef]["ivaIncluidoProducto"]) and $referencias[$posRef]["ivaIncluidoProducto"] != NULL) ? 1 : 0);

                $referencias[$posRef]["acumulaPuntosProducto"] = ((!empty($referencias[$posRef]["acumulaPuntosProducto"]) and $referencias[$posRef]["acumulaPuntosProducto"] != NULL) ? 1 : 0);
                $referencias[$posRef]["redimePuntosProducto"] = ((!empty($referencias[$posRef]["redimePuntosProducto"]) and $referencias[$posRef]["redimePuntosProducto"] != NULL) ? 1 : 0);

                // consultamos el EAN del producto en la tabla de productos para obtener el ID
                $producto->idProducto = 0;
                if (!empty($referencias[$posRef]["referenciaProducto"]))
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $referencias[$posRef]["referenciaProducto"] . "'");
                $referencias[$posRef]["idProducto"] = $producto->idProducto;


                // buscamos si el codigo de barras ya existe con otro numero de referencia
                $referencias[$posRef]["errorbarras"] = 0;
                $producto->idProducto = 0;
                if (!empty($referencias[$posRef]["codigoBarrasProducto"])) {
                    $producto->ConsultarIdProducto("codigoBarrasProducto = '" . $referencias[$posRef]["codigoBarrasProducto"] . "' and referenciaProducto != '" . $referencias[$posRef]["referenciaProducto"] . "'");

                    if ($producto->idProducto > 0)
                        $referencias[$posRef]["errorbarras"] = $producto->idProducto;
                }


                // validamos la talla
                $talla->idTalla = 0;
                if (!empty($referencias[$posRef]["codigoTalla"]))
                    $talla->ConsultarTalla("codigoAlternoTalla =  '" . $referencias[$posRef]["codigoTalla"] . "'");
                $referencias[$posRef]["Talla_idTalla"] = $talla->idTalla;



                // validamos la talla complemento
                $tallacomplemento->idTallaComplemento = 0;
                if (!empty($referencias[$posRef]["codigoTallaComplemento"]))
                    $tallacomplemento->ConsultarTallaComplemento("codigoAlternoTallaComplemento =  '" . $referencias[$posRef]["codigoTallaComplemento"] . "'");
                $referencias[$posRef]["TallaComplemento_idTallaComplemento"] = $tallacomplemento->idTallaComplemento;

                // validamos el Color
                if (!empty($referencias[$posRef]["codigoColor"]))
                    $datos = $color->ConsultarVistaColor("codigoAlternoColor =  '" . $referencias[$posRef]["codigoColor"] . "'");
                $referencias[$posRef]["Color_idColor"] = (isset($datos[0]["idColor"]) ? $datos[0]["idColor"] : 0);


                // validamos la referencia de la ficha tecnica
                $fichatecnica->idFichaTecnica = 0;
                if (!empty($referencias[$posRef]["referenciaBaseFichaTecnica"]))
                    $fichatecnica->ConsultarIdFichaTecnica("referenciaBaseFichaTecnica =  '" . $referencias[$posRef]["referenciaBaseFichaTecnica"] . "'");
                $referencias[$posRef]["FichaTecnica_idFichaTecnica"] = $fichatecnica->idFichaTecnica;

                // validamos la Marca
                $marca->idMarca = 0;
                if (!empty($referencias[$posRef]["codigoMarca"]))
                    $marca->ConsultarMarca("codigoAlternoMarca =  '" . $referencias[$posRef]["codigoMarca"] . "'");
                $referencias[$posRef]["Marca_idMarca"] = $marca->idMarca;

                // validamos el tipo de producto
                $tipoproducto->idTipoProducto = 0;
                if (!empty($referencias[$posRef]["codigoTipoProducto"]))
                    $tipoproducto->ConsultarTipoProducto("codigoAlternoTipoProducto =  '" . $referencias[$posRef]["codigoTipoProducto"] . "'");
                $referencias[$posRef]["TipoProducto_idTipoProducto"] = $tipoproducto->idTipoProducto;

                // validamos el tipo de negocio
                if (!empty($referencias[$posRef]["codigoTipoNegocio"]))
                    $datos = $tiponegocio->ConsultarVistaTipoNegocio("codigoAlternoTipoNegocio =  '" . $referencias[$posRef]["codigoTipoNegocio"] . "'");
                $referencias[$posRef]["TipoNegocio_idTipoNegocio"] = (isset($datos[0]["idTipoNegocio"]) ? $datos[0]["idTipoNegocio"] : 0);


                // validamos la Temporada
                $temporada->idTemporada = 0;
                if (!empty($referencias[$posRef]["codigoTemporada"]))
                    $temporada->ConsultarTemporada("codigoAlternoTemporada =  '" . $referencias[$posRef]["codigoTemporada"] . "'");
                $referencias[$posRef]["Temporada_idTemporada"] = $temporada->idTemporada;


                // validamos el estado de conservacion
                $estadoconservacion->idEstadoConservacion = 0;
                if (!empty($referencias[$posRef]["codigoEstadoConservacion"]))
                    $estadoconservacion->ConsultarEstadoConservacion("codigoAlternoEstadoConservacion =  '" . $referencias[$posRef]["codigoEstadoConservacion"] . "'");
                $referencias[$posRef]["EstadoConservacion_idEstadoConservacion"] = $estadoconservacion->idEstadoConservacion;

                // validamos la composicion
                $composicion->idComposicion = 0;
                if (!empty($referencias[$posRef]["codigoComposicion"]))
                    $composicion->ConsultarComposicion("codigoAlternoComposicion =  '" . $referencias[$posRef]["codigoComposicion"] . "'");
                $referencias[$posRef]["Composicion_idComposicion"] = $composicion->idComposicion;

                // validamos la Posicion Arancelaria
                $posicionarancelaria->idPosicionArancelaria = 0;
                if (!empty($referencias[$posRef]["codigoPosicionArancelaria"]))
                    $posicionarancelaria->ConsultarPosicionArancelaria("codigoAlternoPosicionArancelaria =  '" . $referencias[$posRef]["codigoPosicionArancelaria"] . "'");
                $referencias[$posRef]["PosicionArancelaria_idPosicionArancelaria"] = $posicionarancelaria->idPosicionArancelaria;

                // validamos el Pais de Origen
                $pais->idPais = 0;
                if (!empty($referencias[$posRef]["codigoPais"]))
                    $pais->ConsultarPais("codigoAlternoPais =  '" . $referencias[$posRef]["codigoPais"] . "'");
                $referencias[$posRef]["Pais_idPaisOrigen"] = $pais->idPais;

                // validamos la Categoria
                $categoria->idCategoria = 0;
                if (!empty($referencias[$posRef]["codigoCategoria"]))
                    $categoria->ConsultarCategoria("codigoAlterno1Categoria =  '" . $referencias[$posRef]["codigoCategoria"] . "'");
                $referencias[$posRef]["Categoria_idCategoria"] = $categoria->idCategoria;


                // validamos el Clima
                $clima->idClima = 0;
                if (!empty($referencias[$posRef]["codigoClima"]))
                    $clima->ConsultarClima("codigoAlternoClima =  '" . $referencias[$posRef]["codigoClima"] . "'");
                $referencias[$posRef]["Clima_idClima"] = $clima->idClima;

                // validamos el Difusion
                $difusion->idDifusion = 0;
                if (!empty($referencias[$posRef]["codigoDifusion"]))
                    $difusion->ConsultarDifusion("codigoAlternoDifusion =  '" . $referencias[$posRef]["codigoDifusion"] . "'");
                $referencias[$posRef]["Difusion_idDifusion"] = $difusion->idDifusion;

                // validamos el Estrategia
                $estrategia->idEstrategia = 0;
                if (!empty($referencias[$posRef]["codigoEstrategia"]))
                    $estrategia->ConsultarEstrategia("codigoAlternoEstrategia =  '" . $referencias[$posRef]["codigoEstrategia"] . "'");
                $referencias[$posRef]["Estrategia_idEstrategia"] = $estrategia->idEstrategia;

                // validamos el Seccion
                $seccion->idSeccion = 0;
                if (!empty($referencias[$posRef]["codigoSeccion"]))
                    $seccion->ConsultarSeccion("codigoAlternoSeccion =  '" . $referencias[$posRef]["codigoSeccion"] . "'");
                $referencias[$posRef]["Seccion_idSeccion"] = $seccion->idSeccion;

                // validamos el Evento
                $evento->idEvento = 0;
                if (!empty($referencias[$posRef]["codigoEvento"]))
                    $evento->ConsultarEvento("codigoAlternoEvento =  '" . $referencias[$posRef]["codigoEvento"] . "'");
                $referencias[$posRef]["Evento_idEvento"] = $evento->idEvento;

                // validamos el ClienteObjetivo
                $clienteobjetivo->idClienteObjetivo = 0;
                if (!empty($referencias[$posRef]["codigoClienteObjetivo"]))
                    $clienteobjetivo->ConsultarClienteObjetivo("codigoAlternoClienteObjetivo =  '" . $referencias[$posRef]["codigoClienteObjetivo"] . "'");
                $referencias[$posRef]["ClienteObjetivo_idClienteObjetivo"] = $clienteobjetivo->idClienteObjetivo;

                // validamos el esquema de producto
                $esquemaproducto->idEsquemaProducto = 0;
                if (!empty($referencias[$posRef]["codigoEsquemaProducto"]))
                    $esquemaproducto->ConsultarEsquemaProducto("codigoAlternoEsquemaProducto =  '" . $referencias[$posRef]["codigoEsquemaProducto"] . "'");
                $referencias[$posRef]["EsquemaProducto_idEsquemaProducto"] = $esquemaproducto->idEsquemaProducto;
                $referencias[$posRef]["generaCodigoBarrasEsquemaProducto"] = $esquemaproducto->generaCodigoBarrasEsquemaProducto;

                // consultamos el EAN del Cliente en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($referencias[$posRef]["codigoCliente"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $referencias[$posRef]["codigoCliente"] . "' or codigoAlterno1Tercero = '" . $referencias[$posRef]["codigoCliente"] . "'");
                $referencias[$posRef]["Tercero_idCliente"] = $tercero->idTercero;

                // consultamos el EAN del proveedor en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($referencias[$posRef]["codigoProveedor"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $referencias[$posRef]["codigoProveedor"] . "' or codigoAlterno1Tercero = '" . $referencias[$posRef]["codigoProveedor"] . "'");
                $referencias[$posRef]["Tercero_idProveedor"] = $tercero->idTercero;


                /* // validamos la ubicacion en bodega
                  if(!empty($referencias[$posRef]["codigoBodegaUbicacion"]))
                  $datos = $bodega->ConsultarVistaBodega("codigoBodegaUbicacion =  '".$referencias[$posRef]["codigoBodegaUbicacion"]."'");
                  $referencias[$posRef]["BodegaUbicacion_idBodegaUbicacion"] = $datos[0]["idBodegaUbicacion"];
                 */

                // validamos la unidad de medida de compra
                $unidadmedida->idUnidadMedida = 0;
                if (!empty($referencias[$posRef]["codigoUnidadMedidaCompra"]))
                    $unidadmedida->ConsultarUnidadMedida("codigoAlternoUnidadMedida = '" . $referencias[$posRef]["codigoUnidadMedidaCompra"] . "' ");
                $referencias[$posRef]["UnidadMedida_idCompra"] = $unidadmedida->idUnidadMedida;


                // validamos la unidad de medida de venta
                $unidadmedida->idUnidadMedida = 0;
                if (!empty($referencias[$posRef]["codigoUnidadMedidaVenta"]))
                    $unidadmedida->ConsultarUnidadMedida("codigoAlternoUnidadMedida = '" . $referencias[$posRef]["codigoUnidadMedidaVenta"] . "' ");
                $referencias[$posRef]["UnidadMedida_idVenta"] = $unidadmedida->idUnidadMedida;

                $segmento->idSegmentoOperacion = 0;
                if (!empty($referencias[$posRef]["codigoSegmentoOperacion"]))
                    $segmento->ConsultarIdSegmetoOperacion("codigoAlternoSegmentoOperacion = '" . $referencias[$posRef]["codigoSegmentoOperacion"] . "' ");
                $referencias[$posRef]["SegmentoOperacion_idSegmentoOperacion"] = $segmento->idSegmentoOperacion;


                //lo de hilos
                $calibreHilo->idCalibreHilo = 0;
                if (!empty($referencias[$posRef]["codigoCalibreHilo"]))
                    $calibreHilo->ConsultarCalibreHilo("codigoAlternoCalibreHilo = '" . $referencias[$posRef]["codigoCalibreHilo"] . "' ");
                $referencias[$posRef]["CalibreHilo_idCalibreHilo"] = $calibreHilo->idCalibreHilo;

                $tono->idTono = 0;
                if (!empty($referencias[$posRef]["codigoTono"]))
                    $tono->ConsultarTono("codigoAlternoTono = '" . $referencias[$posRef]["codigoTono"] . "' ");
                $referencias[$posRef]["Tono_idTono"] = $tono->idTono;

                $pinta->idPinta = 0;
                if (!empty($referencias[$posRef]["codigoPinta"]))
                    $pinta->ConsultarPinta("codigoAlternoPinta = '" . $referencias[$posRef]["codigoPinta"] . "' ");
                $referencias[$posRef]["Pinta_idPinta"] = $pinta->idPinta;


                $fila++;
            }

            // importamos las pestaña de los kits
            $objReader->setLoadSheetsOnly('Kit');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);


            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $kit = array();
            $posRef = -1;

            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 57
                for ($columna = 0; $columna <= 5; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $kit[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                //			var_dump($kit);
                //                        echo $kit[$posRef]["referenciaProductoKit"];
                //			$producto->idProducto = 0;
                //			if (!empty($kit[$posRef]["referenciaProductoKit"]))
                //				$producto->ConsultarIdProducto("referenciaProducto = '" . $kit[$posRef]["referenciaProductoKit"] . "'");
                //			$kit[$posRef]["ProductoKit_idProductoKit"] = $producto->idProducto;
                //
                //
                //			$producto->idProducto = 0;
                //			if (!empty($kit[$posRef]["referenciaProducto"]))
                //				$producto->ConsultarIdProducto("referenciaProducto = '" . $kit[$posRef]["referenciaProducto"] . "'");
                //			$kit[$posRef]["Producto_idProducto"] = $producto->idProducto;
                $fila++;
            }


            //Importamos la pestaña de equivalencias
            $objReader->setLoadSheetsOnly('Equivalencias');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $equivalencias = array();
            $posRef = -1;

            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 57
                for ($columna = 0; $columna <= 3; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $equivalencias[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }




                $unidadmedida->idUnidadMedida = 0;
                if (!empty($equivalencias[$posRef]["codigoUnidadMedidaEquivalencia"]))
                    $unidadmedida->ConsultarUnidadMedida("codigoAlternoUnidadMedida = '" . $equivalencias[$posRef]["codigoUnidadMedidaEquivalencia"] . "'");
                $equivalencias[$posRef]["UnidadMedida_idUnidadMedida"] = $unidadmedida->idUnidadMedida;



                $fila++;
            }

            //Importamos la pestaña de Terceros
            $objReader->setLoadSheetsOnly('Terceros');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $terceros = array();
            $posRef = -1;

            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 57
                for ($columna = 0; $columna <= 11; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $terceros[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }



                $tercero->idTercero = 0;
                if (!empty($terceros[$posRef]["codigoAlternoTerceroProducto"]))
                    $tercero->ConsultarIdTercero("codigoAlterno1Tercero = '" . $terceros[$posRef]["codigoAlternoTerceroProducto"] . "' or codigoBarrasTercero = '" . $terceros[$posRef]["codigoAlternoTerceroProducto"] . "'");
                $terceros[$posRef]["Tercero_idTercero"] = $tercero->idTercero;

                $unidadmedida->idUnidadMedida = 0;
                if (!empty($terceros[$posRef]["codigoUnidadMedidaCompra"]))
                    $unidadmedida->ConsultarUnidadMedida("codigoAlternoUnidadMedida = '" . $terceros[$posRef]["codigoUnidadMedidaCompra"] . "'");
                $terceros[$posRef]["UnidadMedida_idUnidadMedidaCompra"] = $unidadmedida->idUnidadMedida;

                $unidadmedida->idUnidadMedida = 0;
                if (!empty($terceros[$posRef]["codigoUnidadMedidaVenta"]))
                    $unidadmedida->ConsultarUnidadMedida("codigoAlternoUnidadMedida = '" . $terceros[$posRef]["codigoUnidadMedidaVenta"] . "'");
                $terceros[$posRef]["UnidadMedida_idUnidadMedidaVenta"] = $unidadmedida->idUnidadMedida;


                $fila++;
            }

            //print_r($referencias);
            // importamos las pestaña de los Seriales
            $objReader->setLoadSheetsOnly('Seriales');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);


            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $seriales = array();
            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $posRef++;

                for ($columna = 0; $columna <= 4; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $seriales[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                $producto->idProducto = 0;
                if (!empty($seriales[$posRef]["referenciaProducto"]))
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $seriales[$posRef]["referenciaProducto"] . "'");
                $seriales[$posRef]["Producto_idProducto"] = $producto->idProducto;

                $fila++;
            }


            // importamos las pestaña de los Sustitutos
            $objReader->setLoadSheetsOnly('Sustitutos');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);


            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $sustitutos = array();
            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $posRef++;

                for ($columna = 0; $columna <= 4; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $sustitutos[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                $producto->idProducto = 0;
                if (!empty($sustitutos[$posRef]["referenciaProducto"]))
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $sustitutos[$posRef]["referenciaProducto"] . "'");
                $sustitutos[$posRef]["Producto_idProducto"] = $producto->idProducto;

                $producto->idProducto = 0;
                if (!empty($sustitutos[$posRef]["referenciaProductoSustituto"]))
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $sustitutos[$posRef]["referenciaProductoSustituto"] . "'");
                $sustitutos[$posRef]["Producto_idProductoSustituto"] = $producto->idProducto;

                $fila++;
            }
            //print_r($sustitutos);
            //               p
            //                print_r($kit);
            //                print_r($equivalencias);
            //                print_r($terceros);
            //                return;
            //		print_r($seriales);
            // luego de que tenemos la matriz de referencias llena, las enviamos al proceso de importacion de productos
            // para que los valide e importe al sistema
            $retorno = $this->llenarPropiedadesProducto($referencias, $kit, $equivalencias, $terceros, $seriales, $sustitutos);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($referencias);

            $this->eliminarArchivo($ruta);

            return $retorno;
        }

        function ImportarMaestroActivosExcel($ruta) {

            //esta funcion es para que el proceso tenga un tiempo indefinido de ejecucion
            set_time_limit(0);

            // incluimos las clases necesarias para realizar el proceso de importacion
            //---------------------------------------------------------

            require_once 'activosfijos.class.php';
            require_once 'pais.class.php';
            require_once 'categoria.class.php';
            require_once 'segmentooperacion.class.php';
            require_once 'centrocosto.class.php';
            require_once 'bodega.class.php';
            require_once 'marca.class.php';
            require_once 'retencion.class.php';
            require_once 'impuesto.class.php';
            require_once 'tiponegocio.class.php';
            require_once 'tipoproducto.class.php';
            require_once '../clases/PHPExcel/Classes/PHPExcel.php';

            //---------------------------------------------------------
            // hacemos la instancia de cada una de las clases para llamar las funciones
            $activo = new ActivosFijos();
            $pais = new Pais();
            $categoria = new Categoria();
            $segmento = new SegmentoOperacion();
            $centrocosto = new CentroCosto();
            $bodega = new Bodega();
            $marca = new Marca();
            $retencion = new Retencion();
            $impuesto = new Impuesto();
            $tipoproducto = new TipoProducto();
            $tiponegocio = new TipoNegocio();

            //definimos los arrays con lso que vamos a trabajar
            $activos = array();
            $impuestos = array();
            $retenciones = array();
            $centroscostos = array();
            $caracteristicas = array();
            $adiciones = array();
            $avaluolocal = array();
            $avaluoniif = array();
            $depreciacion = array();


            // dependiendo de la extension del archivo,
            // lo leemos como excel 5.0/95 o como excel 97 o 2010

            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }

            // creamos cada uno del los objetos de la
            // clase excel con los que vamos a trabajar
            //activos
            $objReader->setLoadSheetsOnly('activo');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // Estas variables son para controlar cada una de las posiciones del array
            // que vamos a llenar y la fila por la que va empezar que va ser la cuatro
            // es donde se empieza a llenar toda la informacion
            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != NULL) {
                $posRef++;

                //recorremos los datos del excel
                for ($columna = 0; $columna <= 58; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $activos[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                $activo->idProducto = 0;
                if (!empty($activos[$posRef]["referenciaProducto"]))
                    $activo->ConsultarIdProducto("referenciaProducto =  '" . $activos[$posRef]["referenciaProducto"] . "'");
                $activos[$posRef]["idProducto"] = $activo->idProducto;



                $activos[$posRef]["errorbarras"] = 0;
                $activo->idProducto = 0;
                if (!empty($activos[$posRef]["codigoBarrasProducto"])) {
                    $activo->ConsultarIdProducto("codigoBarrasProducto = '" . $activos[$posRef]["codigoBarrasProducto"] . "' and referenciaProducto != '" . $activos[$posRef]["referenciaProducto"] . "'");

                    if ($activo->idProducto > 0)
                        $activos[$posRef]["errorbarras"] = $activo->idProducto;
                }

                $activos[$posRef]["ivaIncluidoProducto"] = (!empty($activos[$posRef]["ivaIncluidoProducto"]) and $activos[$posRef]["ivaIncluidoProducto"] != NULL ? 1 : 0);

                // validamos la Categoria
                $categoria->idCategoria = 0;
                if (!empty($activos[$posRef]["codigoAlternoCategoria"])) {
                    $datoCategoria = $categoria->ConsultarVistaCategoria("codigoAlterno1Categoria =  '" . $activos[$posRef]["codigoAlternoCategoria"] . "'", "", "idCategoria, vidaUtilCategoria,metodoDepreciacionCategoria");
                }



                $activos[$posRef]["Categoria_idCategoria"] = isset($datoCategoria[0]["idCategoria"]) ? $datoCategoria[0]["idCategoria"] : 0;
                $activos[$posRef]["vidaUtilCategoria"] = isset($datoCategoria[0]["vidaUtilCategoria"]) ? $datoCategoria[0]["vidaUtilCategoria"] : 0;
                $activos[$posRef]["metodoDepreciacionCategoria"] = isset($datoCategoria[0]["metodoDepreciacionCategoria"]) ? $datoCategoria[0]["metodoDepreciacionCategoria"] : '';

                // cada que llenemos un referencias, hacemos las verificaciones de codigos necesarioos
                // verificamos cuales campos de la clasificacion del prodcuto estan llenos para armar la codificacion de clasificacion
                $activos[$posRef]["clasificacionProducto"] = '';
                $activos[$posRef]["clasificacionProducto"] .= (!empty($activos[$posRef]["AF"]) and $activos[$posRef]["AF"] != NULL) ? '*05*' : '';
                $activos[$posRef]["clasificacionProducto"] .= (!empty($activos[$posRef]["CO"]) and $activos[$posRef]["CO"] != NULL) ? '*12*' : '';
                $activos[$posRef]["clasificacionProducto"] .= (!empty($activos[$posRef]["AA"]) and $activos[$posRef]["AA"] != NULL) ? '*08*' : '';
                $activos[$posRef]["clasificacionProducto"] .= (!empty($activos[$posRef]["DA"]) and $activos[$posRef]["DA"] != NULL) ? '*09*' : '';
                $activos[$posRef]["clasificacionProducto"] .= (!empty($activos[$posRef]["MA"]) and $activos[$posRef]["MA"] != NULL) ? '*13*' : '';


                // validamos el Pais de Origen
                $pais->idPais = 0;
                if (!empty($activos[$posRef]["codigoAlternoPais"]))
                    $pais->ConsultarPais("codigoAlternoPais =  '" . $activos[$posRef]["codigoAlternoPais"] . "'");
                $activos[$posRef]["Pais_idPaisOrigen"] = $pais->idPais;


                $segmento->idSegmentoOperacion = 0;
                if (!empty($activos[$posRef]["CodigoAlternoSegmentoOperacion"]))
                    $segmento->ConsultarIdSegmetoOperacion("codigoAlternoSegmentoOperacion =  '" . $activos[$posRef]["CodigoAlternoSegmentoOperacion"] . "'");
                $activos[$posRef]["SegmentoOperacion_idSegmentoOperacion"] = $segmento->idSegmentoOperacion;



                $marca->idMarca = 0;
                if (!empty($activos[$posRef]["CodigoAlternoMarca"]))
                    $marca->ConsultarMarca("CodigoAlternoMarca =  '" . $activos[$posRef]["CodigoAlternoMarca"] . "'");
                $activos[$posRef]["Marca_idMarca"] = $marca->idMarca;

                $tiponegocio->idTipoNegocio = 0;
                if (!empty($activos[$posRef]["codigoAlternoTipoNegocio"]))
                    $tiponegocio->ConsultarIdTipoNegocio("codigoAlternoTipoNegocio =  '" . $activos[$posRef]["codigoAlternoTipoNegocio"] . "'");
                $activos[$posRef]["TipoNegocio_idTipoNegocio"] = $tiponegocio->idTipoNegocio;


                $tipoproducto->idTipoProducto = 0;
                if (!empty($activos[$posRef]["codigoAlternoTipoProducto"]))
                    $tipoproducto->ConsultarTipoProducto("codigoAlternoTipoProducto =  '" . $activos[$posRef]["codigoAlternoTipoProducto"] . "'");
                $activos[$posRef]["TipoProducto_idTipoProducto"] = $tipoproducto->idTipoProducto;




                if ($activos[$posRef]["estadoProducto"] == 'DISPONIBLE EN VENTA') {
                    $activos[$posRef]["estadoProducto"] = 'VENTA';
                } else if ($activos[$posRef]["estadoProducto"] == 'CONSTRUCCION/MONTAJE') {
                    $activos[$posRef]["estadoProducto"] = 'MONTAJE';
                } else if ($activos[$posRef]["estadoProducto"] == 'CONSTRUCCION/MONTAJE') {
                    $activos[$posRef]["estadoProducto"] = 'MONTAJE';
                } else if ($activos[$posRef]["estadoProducto"] == 'TOTALMENTE DEPRECIADO') {
                    $activos[$posRef]["estadoProducto"] = 'DEPRECIADO';
                }


                if ($activos[$posRef]["estadoNIIFProducto"] == 'DISPONIBLE EN VENTA') {
                    $activos[$posRef]["estadoNIIFProducto"] = 'VENTA';
                } else if ($activos[$posRef]["estadoNIIFProducto"] == 'CONSTRUCCION/MONTAJE') {
                    $activos[$posRef]["estadoNIIFProducto"] = 'MONTAJE';
                } else if ($activos[$posRef]["estadoNIIFProducto"] == 'DADA DE BAJA') {
                    $activos[$posRef]["estadoNIIFProducto"] = 'BAJA';
                } else if ($activos[$posRef]["estadoNIIFProducto"] == 'TOTALMENTE DEPRECIADO') {
                    $activos[$posRef]["estadoNIIFProducto"] = 'DEPRECIADO';
                }


                if ($activos[$posRef]["metodoAdquisionNIIFProducto"] == 'Arrendamiento Operativo') {
                    $activos[$posRef]["metodoAdquisionNIIFProducto"] = 'ArrendamientoOperativo';
                } elseif ($activos[$posRef]["metodoAdquisionNIIFProducto"] == 'Arrendamiento Financiero') {
                    $activos[$posRef]["metodoAdquisionNIIFProducto"] = 'ArrendamientoFinanciero';
                }



                if ($activos[$posRef]["metodoAdquisicionLocalProducto"] == 'Arrendamiento Operativo') {
                    $activos[$posRef]["metodoAdquisicionLocalProducto"] = 'ArrendamientoOperativo';
                } elseif ($activos[$posRef]["metodoAdquisicionLocalProducto"] == 'Arrendamiento Financiero') {
                    $activos[$posRef]["metodoAdquisicionLocalProducto"] = 'ArrendamientoFinanciero';
                }


                $fila++;
            }

            //            echo '<pre>';
            //            print_r($activos);
            //            echo '</pre>';
            //impuestos
            $objReader->setLoadSheetsOnly('impuestos');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.


            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 57
                for ($columna = 0; $columna <= 1; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $impuestos[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                $impuesto->idImpuesto = 0;
                if (!empty($impuestos[$posRef]["codigoAlternoImpuesto"]))
                    $impuesto->ConsultarIdImpuesto("codigoAlternoImpuesto = '" . $impuestos[$posRef]["codigoAlternoImpuesto"] . "'");
                $impuestos[$posRef]["idImpuesto"] = $impuesto->idImpuesto;
                $fila++;
            }

            //retenciones
            $objReader->setLoadSheetsOnly('retenciones');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $posRef++;


                for ($columna = 0; $columna <= 1; $columna++) {

                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $retenciones[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }



                $retencion->idRetencion = 0;
                if (!empty($retenciones[$posRef]["referenciaProducto"]))
                    $retencion->ConsultarIdRetencion("codigoAlternoRetencion = '" . $retenciones[$posRef]["codigoAlternoRetencion"] . "'");
                $retenciones[$posRef]["idRetencion"] = $retencion->idRetencion;

                $fila++;
            }

            //
            // deprciaciones
            $objReader->setLoadSheetsOnly('depreciacion');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            $posRef = -1;
            $fila = 4;


            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $posRef++;

                for ($columna = 0; $columna <= 10; $columna++) {

                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $depreciacion[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }



                $activo->idProducto = 0;
                if (!empty($depreciacion[$posRef]["referenciaProducto"]))
                    $activo->ConsultarIdProducto("referenciaProducto = '" . $depreciacion[$posRef]["referenciaProducto"] . "'");
                $depreciacion[$posRef]["idProducto"] = $activo->idProducto;


                $fila++;
            }

            //            echo '<pre>';
            //            print_r($depreciacion);
            //            echo '</pre>';
            //
            $objReader->setLoadSheetsOnly('centrocosto');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            $posRef = -1;

            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {
                //                echo 'entra';
                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 57
                for ($columna = 0; $columna <= 3; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $centroscostos[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }



                $activo->idProducto = 0;
                if (!empty($centroscostos[$posRef]["referenciaProducto"]))
                    $activo->ConsultarIdProducto("referenciaProducto = '" . $centroscostos[$posRef]["referenciaProducto"] . "'");
                $centroscostos[$posRef]["idProducto"] = $activo->idProducto;


                $centrocosto->idCentroCosto = 0;
                if (!empty($centroscostos[$posRef]["codigoAlternoCentroCosto"]))
                    $centrocosto->ConsultarCentroCosto("codigoAlternoCentroCosto = '" . $centroscostos[$posRef]["codigoAlternoCentroCosto"] . "'");
                $centroscostos[$posRef]["CentroCosto_idCentroCosto"] = $centrocosto->idCentroCosto;

                //
                $bodega->idBodega = 0;
                if (!empty($centroscostos[$posRef]["codigoAlternoBodega"]))
                    $bodega->ConsultarBodega("codigoAlternoBodega = '" . $centroscostos[$posRef]["codigoAlternoBodega"] . "'");
                $centroscostos[$posRef]["Bodega_idBodega"] = $bodega->idBodega;


                $fila++;
            }

            //            var_dump($centroscostos);
            //            // avaluos


            $objReader->setLoadSheetsOnly('avaluo local');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {



                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 57
                for ($columna = 0; $columna <= 6; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $avaluolocal[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }



                $activo->idProducto = 0;
                if (!empty($avaluolocal[$posRef]["referenciaProducto"]))
                    $activo->ConsultarIdProducto("referenciaProducto = '" . $avaluolocal[$posRef]["referenciaProducto"] . "'");
                $avaluolocal[$posRef]["idProducto"] = $activo->idProducto;

                $fila++;
            }




            $objReader->setLoadSheetsOnly('avaluo niif');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {



                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 57
                for ($columna = 0; $columna <= 8; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $avaluoniif[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }



                $activo->idProducto = 0;
                if (!empty($avaluoniif[$posRef]["referenciaProducto"]))
                    $activo->ConsultarIdProducto("referenciaProducto = '" . $avaluoniif[$posRef]["referenciaProducto"] . "'");
                $avaluoniif[$posRef]["idProducto"] = $activo->idProducto;

                $fila++;
            }



            //
            $objReader->setLoadSheetsOnly('caracteristicas');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {


                $posRef++;


                for ($columna = 0; $columna <= 4; $columna++) {

                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $caracteristicas[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }



                $activo->idProducto = 0;
                if (!empty($caracteristicas[$posRef]["referenciaProducto"]))
                    $activo->ConsultarIdProducto("referenciaProducto = '" . $caracteristicas[$posRef]["referenciaProducto"] . "'");
                $caracteristicas[$posRef]["idProducto"] = $activo->idProducto;


                $fila++;
            }
            //
            $objReader->setLoadSheetsOnly('adiciones');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            $posRef = -1;

            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {


                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 57
                for ($columna = 0; $columna <= 2; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $adiciones[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }



                $activo->idProducto = 0;
                if (!empty($adiciones[$posRef]["referenciaAdicionProducto"]))
                    $activo->ConsultarIdProducto("referenciaProducto = '" . $adiciones[$posRef]["referenciaAdicionProducto"] . "'");
                $adiciones[$posRef]["idProducto"] = $activo->idProducto;

                $activo->idProducto = 0;
                if (!empty($adiciones[$posRef]["referenciaProducto"]))
                    $activo->ConsultarIdProducto("referenciaProducto = '" . $adiciones[$posRef]["referenciaProducto"] . "'");
                $adiciones[$posRef]["idProductoEncabezado"] = $activo->idProducto;


                $fila++;
            }
            //
            //           var_dump($avaluoniif);
            //           return;
            ////
            $retorno = $this->llenarPropiedadesActivosFijos($activos, $impuestos, $retenciones, $centroscostos, $caracteristicas, $adiciones, $avaluolocal, $avaluoniif, $depreciacion);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($activos);

            $this->eliminarArchivo($ruta);


            return $retorno;
        }

        function llenarPropiedadesActivosFijos($activos, $impuestos, $retenciones, $centroscostos, $caracteristicas, $adiciones, $avaluolocal, $avaluoniif, $depreciacion) {

            // incluimos las clases necesarias para hacer la validacion
            // y el ingreso de los registros de los campso del excel

            require_once 'activosfijos.class.php';
            require_once('categoria.class.php');
            require_once'db.class.php';
            require_once'conf.class.php';


            // hacemos la instancia de cada una de las clases
            $activo = new ActivosFijos();
            $categoria = new Categoria();
            $bd = Db::getInstance();

            // declaramso un array para
            $retorno = array();
            $reg = 0;
            // contamos las cantidad de activos que se van a importar
            $totalreg = (isset($activos[0]["referenciaProducto"]) ? count($activos) : 0);

            while ($reg < $totalreg) {
                //inicializamos los atributos
                $activo->ActivosFijos();

                // llamamos la funcion para que me valide cada una de las referencia
                $nuevoserrores = $this->validarEncabezadoActivos($activos[$reg]["referenciaProducto"], $reg, $activos);
                $totalerr = count($nuevoserrores);
                $errores = array();
                // en esta parta vamos a validar los datos de la compra del activo
                if ($activos[$reg]['numeroFacturaCompraProducto'] != '') {

                    $sqlcompra = "select numeroMovimiento,fechaElaboracionMovimiento,Tercero_idTercero,valorTotalMovimiento
                                                      from Movimiento where numeroMovimiento = '" . $activos[$reg]['numeroFacturaCompraProducto'] . "'";
                    $datoscompra = $bd->ConsultarVista($sqlcompra);
                }


                $select = "select idTercero from Tercero where documentoTercero = " . $activos[$reg]['nombre1Proveedor'];
                $datoTerceroCompra = $bd->ConsultarVista($select);


                if (isset($datoscompra[0]['Tercero_idTercero'])) {
                    $terCom = $datoscompra[0]['Tercero_idTercero'];
                } else {
                    $terCom = isset($datoTerceroCompra[0]['idTercero']) ? $datoTerceroCompra[0]['idTercero'] : 0;
                }

                //
                //                $UNIX_DATE = ($activos[$reg]["fechaCompraProducto"]-25568)*86400;
                //                $EXCEL_DATE = 25569 + ($UNIX_DATE / 86400);
                //                $UNIX_DATE = (($EXCEL_DATE - 25569) * 86400) + 1;
                //                echo date("Y-m-d", $UNIX_DATE);
                $fcompra = '';
                if ($activos[$reg]["fechaCompraProducto"] != '') {
                    $fc = ($activos[$reg]["fechaCompraProducto"] - 25569) * 86400;
                    $fcompra = date('Y-m-d', (strtotime('+1 day', $fc)));
                }



                //                $fcompra = ($activos[$reg]["fechaCompraProducto"]-25569)*86400;
                //                $fcompra = date('Y-m-d',$a);

                $errores = array();
                // en esta parta vamos a validar los datos de la compra del activo
                if ($activos[$reg]['numeroFacturaVentaProducto'] != '') {

                    $sqlventa = "select numeroMovimiento,fechaElaboracionMovimiento,Tercero_idTercero,valorTotalMovimiento
                                                      from Movimiento where numeroMovimiento = '" . $activos[$reg]['numeroFacturaVentaProducto'] . "'";
                    $datosventa = $bd->ConsultarVista($sqlventa);
                }

                $select = "select idTercero from Tercero where documentoTercero = " . $activos[$reg]['nombre1Cliente'];
                $datoTerceroVenta = $bd->ConsultarVista($select);


                if (isset($datosventa[0]['Tercero_idTercero'])) {
                    $terVen = $datoscompra[0]['Tercero_idTercero'];
                } else {
                    $terVen = isset($datoTerceroVenta[0]['idTercero']) ? $datoTerceroVenta[0]['idTercero'] : 0;
                }


                //                $fventa = ($activos[$reg]["fechaVentaProducto"]-25569)*86400;
                //                $fcompra = date('Y-m-d',(strtotime('+1 day', $activos[$reg]["fechaVentaProducto"])));


                $fventa = '';
                if ($activos[$reg]["fechaVentaProducto"] != '') {
                    $fv = ($activos[$reg]["fechaVentaProducto"] - 25569) * 86400;
                    $fventa = date('Y-m-d', (strtotime('+1 day', $fv)));
                }


                $fcreacion = '';
                if ($activos[$reg]["fechaCreacionProducto"] != '') {
                    $fcp = ($activos[$reg]["fechaCreacionProducto"] - 25569) * 86400;

                    $fcreacion = date('Y-m-d', (strtotime('+1 day', $fcp)));
                }





                if ($totalerr == 0) {

                    $activo->idProducto = (isset($activos[$reg]["idProducto"]) ? $activos[$reg]["idProducto"] : 0 );
                    $activo->codigoAlternoProducto = (isset($activos[$reg]["codigoAlternoProducto"]) ? $activos[$reg]["codigoAlternoProducto"] : '');
                    $activo->referenciaProducto = (isset($activos[$reg]["referenciaProducto"]) ? $activos[$reg]["referenciaProducto"] : '');
                    $activo->codigoBarrasProducto = (isset($activos[$reg]["codigoBarrasProducto"]) ? $activos[$reg]["codigoBarrasProducto"] : '');
                    $activo->nombreLargoProducto = (isset($activos[$reg]["nombreLargoProducto"]) ? $activos[$reg]["nombreLargoProducto"] : '');
                    $activo->clasificacionProducto = (isset($activos[$reg]["clasificacionProducto"]) ? $activos[$reg]["clasificacionProducto"] : '');
                    $activo->ivaIncluidoProducto = (isset($activos[$reg]["ivaIncluidoProducto"]) ? $activos[$reg]["ivaIncluidoProducto"] : 0);
                    $activo->Categoria_idCategoria = (isset($activos[$reg]["Categoria_idCategoria"]) ? $activos[$reg]["Categoria_idCategoria"] : 0);
                    $activo->estadoProducto = (isset($activos[$reg]["estadoNIIFProducto"]) ? $activos[$reg]["estadoNIIFProducto"] : 'ACTIVO');
                    $activo->estadoNIIFProducto = (isset($activos[$reg]["estadoProducto"]) ? $activos[$reg]["estadoProducto"] : 'ACTIVO');
                    $activo->Marca_idMarca = (isset($activos[$reg]["Marca_idMarca"]) ? $activos[$reg]["Marca_idMarca"] : '');
                    $activo->modeloProducto = (isset($activos[$reg]["modeloProducto"]) ? $activos[$reg]["modeloProducto"] : '');
                    $activo->serialProducto = (isset($activos[$reg]["serialProducto"]) ? $activos[$reg]["serialProducto"] : '');
                    $activo->Pais_idPaisOrigen = (isset($activos[$reg]["Pais_idPaisOrigen"]) ? $activos[$reg]["Pais_idPaisOrigen"] : 0);
                    $activo->SegmentoOperacion_idSegmentoOperacion = (isset($activos[$reg]["SegmentoOperacion_idSegmentoOperacion"]) ? $activos[$reg]["SegmentoOperacion_idSegmentoOperacion"] : 0);
                    $activo->TipoProducto_idTipoProducto = (isset($activos[$reg]["TipoProducto_idTipoProducto"]) ? $activos[$reg]["TipoProducto_idTipoProducto"] : 0);
                    $activo->TipoNegocio_idTipoNegocio = (isset($activos[$reg]["TipoNegocio_idTipoNegocio"]) ? $activos[$reg]["TipoNegocio_idTipoNegocio"] : 0);

                    $activo->fechaCreacionProducto = (isset($activos[$reg]["fechaCreacionProducto"]) ? $activos[$reg]["fechaCreacionProducto"] : date('Y-m-d'));



                    $activo->metodoAdquisionNIIFProducto = (isset($activos[$reg]["metodoAdquisionNIIFProducto"]) ? $activos[$reg]["metodoAdquisionNIIFProducto"] : '');
                    $activo->metodoAdquisicionLocalProducto = (isset($activos[$reg]["metodoAdquisicionLocalProducto"]) ? $activos[$reg]["metodoAdquisicionLocalProducto"] : '');

                    $activo->valorOpcionCompraProducto = (isset($activos[$reg]["valorOpcionCompraProducto"]) ? $activos[$reg]["valorOpcionCompraProducto"] : '');

                    $activo->opcionCompraProducto = (isset($activos[$reg]["opcionCompraProducto"]) ? $activos[$reg]["opcionCompraProducto"] : '');

                    $activo->pesoBrutoProducto = (isset($activos[$reg]["pesoBrutoProducto"]) ? $activos[$reg]["pesoBrutoProducto"] : 0);
                    $activo->pesoNetoProducto = (isset($activos[$reg]["pesoNetoProducto"]) ? $activos[$reg]["pesoNetoProducto"] : 0);
                    $activo->altoProducto = (isset($activos[$reg]["altoProducto"]) ? $activos[$reg]["altoProducto"] : 0);
                    $activo->anchoProducto = (isset($activos[$reg]["anchoProducto"]) ? $activos[$reg]["anchoProducto"] : 0);
                    $activo->profundidadProducto = (isset($activos[$reg]["profundidadProducto"]) ? $activos[$reg]["profundidadProducto"] : 0);

                    $activo->numeroFacturaCompraProducto = (isset($datoscompra[0]["numeroMovimiento"]) ? $datoscompra[0]["numeroMovimiento"] : $activos[$reg]["numeroFacturaCompraProducto"]);
                    $activo->fechaCompraProducto = (isset($datoscompra[0]["fechaElaboracionMovimiento"]) and $datoscompra[0]["fechaElaboracionMovimiento"] != '0000-00-00') ? $datoscompra[0]["fechaElaboracionMovimiento"] : $activos[$reg]["fechaCompraProducto"];
                    $activo->Tercero_idProveedor = $terCom;

                    //echo $activos[$reg]["valorCompraProducto"].' valor compraaaaaaaaaaaaaaaaa';

                    $activo->valorCompraProducto = (isset($datoscompra[0]['valorTotalMovimiento']) ? $datoscompra[0]['valorTotalMovimiento'] : $activos[$reg]["valorCompraProducto"]);


                    //echo  $activo->valorCompraProducto.' valor compra despues';
                    $activo->valorRescateProducto = (isset($activos[$reg]["valorRescateProducto"]) ? $activos[$reg]["valorRescateProducto"] : 0);
                    $activo->valorRescateNIIFProducto = (isset($activos[$reg]["valorRescateNIIFProducto"]) ? $activos[$reg]["valorRescateNIIFProducto"] : 0);
                    $activo->valorDepreciableProducto = ($activo->valorCompraProducto) - ($activo->valorRescateProducto);
                    $activo->valorDepreciableNIIFProducto = ($activo->valorCompraProducto) - ($activo->valorRescateNIIFProducto);

                    $activo->numeroFacturaVentaProducto = (isset($datosventa[$reg]["numeroMovimiento"]) ? $datosventa[$reg]["numeroMovimiento"] : $activos[$reg]["numeroFacturaVentaProducto"]);





                    $activo->fechaVentaProducto = isset($datosventa[$reg]["fechaELaboracionMovimiento"]) ? $datosventa[$reg]["fechaELaboracionMovimiento"] : $fventa;
                    $activo->Tercero_idCliente = $terVen;
                    $activo->valorVentaProducto = (isset($datosventa[$reg]["valorTotalMovimiento"]) ? $datosventa[$reg]["valorTotalMovimiento"] : $activos[$reg]["valorVentaProducto"]);

                    //depreciacion local


                    $activo->fechaDepreciacionProducto = (isset($activos[$reg]["fechaDepreciacionProducto"]) ? $activos[$reg]["fechaDepreciacionProducto"] : 0);
                    $activo->vidaUtilProducto = (isset($activos[$reg]["vidaUtilProducto"]) ? $activos[$reg]["vidaUtilProducto"] : 0);
                    $activo->depreciacionAcumuladaProducto = (isset($activos[$reg]["depreciacionAcumuladaProducto"]) ? $activos[$reg]["depreciacionAcumuladaProducto"] : 0);
                    $activo->mesesDepreciadosProducto = (isset($activos[$reg]["mesesDepreciadosProducto"]) ? $activos[$reg]["mesesDepreciadosProducto"] : 0);
                    $activo->depreciacionPendienteProducto = (isset($activos[$reg]["depreciacionPendienteProducto"]) ? $activos[$reg]["depreciacionPendienteProducto"] : 0);
                    $activo->mesesPendientesProducto = (isset($activos[$reg]["mesesPendientesProducto"]) ? $activos[$reg]["mesesPendientesProducto"] : 0);



                    $activo->fechaDepreciacionNIIFProducto = (isset($activos[$reg]["fechaDepreciacionNIIFProducto"]) ? $activos[$reg]["fechaDepreciacionNIIFProducto"] : '');
                    $activo->vidaUtilNIIFProducto = (isset($activos[$reg]["vidaUtilNIIFProducto"]) ? $activos[$reg]["vidaUtilNIIFProducto"] : 0);
                    $activo->vidaUtilAvaluoProducto = (isset($activos[$reg]["vidaUtilAvaluoProducto"]) ? $activos[$reg]["vidaUtilAvaluoProducto"] : 0);
                    $activo->depreciacionAcumuladaNIIFProducto = (isset($activos[$reg]["depreciacionAcumuladaNIIFProducto"]) ? $activos[$reg]["depreciacionAcumuladaNIIFProducto"] : 0);
                    $activo->mesesDepreciadosNIIFProducto = (isset($activos[$reg]["mesesDepreciadosNIIFProducto"]) ? $activos[$reg]["mesesDepreciadosNIIFProducto"] : 0);
                    $activo->depreciacionPendienteNIIFProducto = (isset($activos[$reg]["depreciacionPendienteNIIFProducto"]) ? $activos[$reg]["depreciacionPendienteNIIFProducto"] : 0);
                    $activo->mesesPendientesNIIFProducto = (isset($activos[$reg]["mesesPendientesNIIFProducto"]) ? $activos[$reg]["mesesPendientesNIIFProducto"] : 0);

                    //                       echo '<pre>';
                    //                       print_r($activos);
                    //                       echo '</pre>';
                    //
                    if ($activo->idProducto == 0) {
                        $activo->AdicionarActivoFijo();
                    } else {
                        $activo->ModificarActivoFijoExcel();
                    }
                } else {
                    $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                }


                $reg++;
            }



            $errores = array();
            $posicion = 0;

            for ($i2 = 0; $i2 < count($impuestos); $i2++) {
                $bandera = true;

                $sqlref = "select idProducto from Producto where referenciaProducto = '" . $impuestos[$i2]["referenciaProducto"] . "'";
                $datosref = $bd->ConsultarVista($sqlref);

                //                echo $sqlref;

                if (count($datosref) == 0) {
                    $errores[$posicion]["referenciaProducto"] = $impuestos[$i2]['referenciaProducto'];
                    $errores[$posicion]["nombreLargoProducto"] = 'La referencia la que se le desea adicionar el impuesto no existe';
                    $errores[$posicion]["error"] = 'Error';
                    $bandera = false;
                    $posicion++;
                }

                if ($impuestos[$i2]['codigoAlternoImpuesto'] == '') {
                    $errores[$posicion]["referenciaProducto"] = $retenciones[$i2]['referenciaProducto'];
                    $errores[$posicion]["nombreLargoProducto"] = 'El codigo alterno de la retencion no puede estar vacio';
                    $errores[$posicion]["error"] = 'Error';
                    $bandera = false;
                    $posicion++;
                }

                if ($impuestos[$i2]["idImpuesto"] == 0) {
                    $errores[$posicion]["referenciaProducto"] = $impuestos[$i2]['referenciaProducto'];
                    $errores[$posicion]["nombreLargoProducto"] = 'EL impuesto con codigo Alterno ' . $impuestos[$i2]['codigoAlternoImpuesto'] . ' no existe';
                    $errores[$posicion]["error"] = 'Error';
                    $bandera = false;
                    $posicion++;
                }

                if ($bandera == true) {
                    $activo->idProducto = $datosref[0]['idProducto'];
                    $activo->idProductoImpuesto[0] = 0;
                    $activo->Impuesto_idImpuesto[0] = $impuestos[$i2]['idImpuesto'];
                    $activo->EliminarProductoImpuesto("Producto_idProducto = " . $activo->idProducto . " and Impuesto_idImpuesto = " . $impuestos[$i2]['idImpuesto']);
                    $activo->AdicionarProductoImpuesto($activo->idProducto, 0);
                }
            }


            $retorno = array_merge((array) $retorno, (array) $errores);
            $errores = array();
            $posicion = 0;


            for ($i2 = 0; $i2 < count($retenciones); $i2++) {
                $bandera = true;

                $sqlref = "select idProducto from Producto where referenciaProducto = '" . $retenciones[$i2]["referenciaProducto"] . "'";
                $datosref = $bd->ConsultarVista($sqlref);


                if (count($datosref) == 0) {
                    $errores[$posicion]["referenciaProducto"] = $retenciones[$i2]['referenciaProducto'];
                    $errores[$posicion]["nombreLargoProducto"] = 'La referencia la que se le desea adicionar la retencion no existe';
                    $errores[$posicion]["error"] = 'Error';
                    $bandera = false;
                    $posicion++;
                }

                if ($retenciones[$i2]['codigoAlternoRetencion'] == '') {
                    $errores[$posicion]["referenciaProducto"] = $retenciones[$i2]['referenciaProducto'];
                    $errores[$posicion]["nombreLargoProducto"] = 'El codigo alterno de la retencion no puede estar vacio';
                    $errores[$posicion]["error"] = 'Error';
                    $bandera = false;
                    $posicion++;
                }

                if ($retenciones[$i2]["idRetencion"] == 0) {
                    $errores[$posicion]["referenciaProducto"] = $retenciones[$i2]['referenciaProducto'];
                    $errores[$posicion]["nombreLargoProducto"] = 'La retencion con codigo Alterno ' . $retenciones[$i2]['codigoAlternoRetencion'] . ' no existe';
                    $errores[$posicion]["error"] = 'Error';
                    $bandera = false;
                    $posicion++;
                }

                if ($bandera == true) {
                    $activo->idProducto = $datosref[0]['idProducto'];
                    $activo->idProductoRetencion[0] = 0;
                    $activo->Retencion_idRetencion[0] = $retenciones[$i2]['idRetencion'];
                    $activo->AdicionarProductoRetencion($activo->idProducto, 0);
                }
            }


            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $errores);
            $errores = array();



            for ($i2 = 0; $i2 < count($centroscostos); $i2++) {
                $bandera = true;

                $sqlcencos = "select idCentroCosto from CentroCosto where idCentroCosto = '" . $centroscostos[$i2]['CentroCosto_idCentroCosto'] . "'";
                $sqlbod = "select idBodega from Bodega where idBodega = '" . $centroscostos[$i2]["Bodega_idBodega"] . "'";
                $sqlref = "select idProducto from Producto where referenciaProducto = '" . $centroscostos[$i2]["referenciaProducto"] . "'";

                $datacencos = $bd->ConsultarVista($sqlcencos);
                $databog = $bd->ConsultarVista($sqlbod);
                $dataref = $bd->ConsultarVista($sqlref);

                if (count($dataref) == 0) {
                    $errores[$posicion]["referenciaProducto"] = $centroscostos[$i2]["referenciaProducto"];
                    $errores[$posicion]["nombreLargoProducto"] = $centroscostos[$i2]["referenciaProducto"];
                    $errores[$posicion]["error"] = 'La Referencia a la cual se le desea agregar el Centro de Costos no existe';
                    $bandera = false;
                    $posicion++;
                }


                if (count($datacencos) == 0) {
                    $errores[$posicion]["referenciaProducto"] = $centroscostos[$i2]["referenciaProducto"];
                    $errores[$posicion]["nombreLargoProducto"] = $centroscostos[$i2]["codigoAlternoCentroCosto"];
                    $errores[$posicion]["error"] = 'El Centro de Costos con el Codigo Alterno ' . $centroscostos[$i2]["codigoAlternoCentroCosto"] . ' no existe';
                    $bandera = false;
                    $posicion++;
                }

                if (count($databog) == 0) {
                    $errores[$posicion]["referenciaProducto"] = $centroscostos[$i2]["referenciaProducto"];
                    $errores[$posicion]["nombreLargoProducto"] = $centroscostos[$i2]["codigoAlternoCentroCosto"];
                    $errores[$posicion]["error"] = 'La Bodega con el Codigo Alterno ' . $centroscostos[$i2]["codigoAlternoBodega"] . ' no existe';
                    $bandera = false;
                    $posicion++;
                }

                if ($bandera == true) {
                    $activo->idProducto = $dataref[0]['idProducto'];
                    $activo->idProductoCentroCosto[0] = 0;
                    $activo->CentroCosto_idCentroCosto[0] = $centroscostos[$i2]["CentroCosto_idCentroCosto"];
                    $activo->Bodega_idBodega[0] = $centroscostos[$i2]["Bodega_idBodega"];

                    $fcentro = '';
                    if ($centroscostos[$i2]["fechaProductoCentroCosto"] != '') {
                        $fcc = ($centroscostos[$i2]["fechaProductoCentroCosto"] - 25569) * 86400;
                        $fcentro = date('Y-m-d', (strtotime('+1 day', $fcc)));
                    }


                    $activo->fechaProductoCentroCosto[0] = $fcentro;
                    $activo->EliminarProductoCentroCosto("Producto_idProducto = " . $dataref[0]['idProducto'] . " and CentroCosto_idCentroCosto = " . $centroscostos[$i2]["CentroCosto_idCentroCosto"] . " and Bodega_idBodega = " . $centroscostos[$i2]["Bodega_idBodega"]);
                    $activo->AdicionarProductoCentroCosto();
                }
            }


            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $errores);
            $errores = array();


            //            var_dump($adiciones);


            for ($i2 = 0; $i2 < count($adiciones); $i2++) {
                $bandera == true;

                $sqladi = "select ClasificacionProducto from Producto where referenciaProducto = '" . $adiciones[$i2]["referenciaAdicionProducto"] . "' and (ClasificacionProducto like '%*08*%' or ClasificacionProducto like '%*12*%' or ClasificacionProducto like '%*13*%')";
                $datadi = $bd->ConsultarVista($sqladi);


                if ($adiciones[$i2]['idProducto'] == 0) {
                    $errores[$posicion]["referenciaProducto"] = $adiciones[$i2]["referenciaProducto"];
                    $errores[$posicion]["nombreLargoProducto"] = $adiciones[$i2]["referenciaProducto"];
                    $errores[$posicion]["error"] = 'La referencia  ' . $adiciones[$i2]["referenciaProducto"] . ' a la cual se le realizar la adicion no existe';
                    $bandera = false;
                    $posicion++;
                }

                if (count($datadi) == 0) {
                    $errores[$posicion]["referenciaProducto"] = $adiciones[$i2]["referenciaProducto"];
                    $errores[$posicion]["nombreLargoProducto"] = $adiciones[$i2]["referenciaProducto"];
                    $errores[$posicion]["error"] = 'La referencia  ' . $adiciones[$i2]["referenciaProducto"] . ' no es una adicion o un componente';
                    $bandera = false;
                    $posicion++;
                }



                if (!is_numeric($adiciones[$i2]["vidaUtilProductoAdicion"])) {
                    $errores[$posicion]["referenciaProducto"] = $adiciones[$i2]["referenciaProducto"];
                    $errores[$posicion]["nombreLargoProducto"] = $adiciones[$i2]["referenciaProducto"];
                    $errores[$posicion]["error"] = 'la vida util de la adicion no es un campo valido';
                    $bandera = false;
                    $posicion++;
                }

                if ($adiciones[$i2]["vidaUtilProductoAdicion"] == '') {
                    $errores[$posicion]["referenciaProducto"] = $adiciones[$i2]["referenciaProducto"];
                    $errores[$posicion]["nombreLargoProducto"] = $adiciones[$i2]["referenciaProducto"];
                    $errores[$posicion]["error"] = 'la vida util de la adicion no puede estar vacia';
                    $bandera = false;
                    $posicion++;
                }

                if ($bandera == true) {
                    $activo->idProducto =  $adiciones[$i2]['idProductoEncabezado'];
                    $activo->idProductoAdicion[0] = 0;
                    $activo->Producto_idAdicion[0] = $adiciones[$i2]['idProducto'];
                    $activo->metodoDepresiacion[0] = '';
                    $activo->vidaUtilProductoAdicion[0] = $adiciones[$i2]["vidaUtilProductoAdicion"];
                    $activo->EliminarProductoAdicion("Producto_idAdicion = " . $activo->Producto_idAdicion[0] . " and vidaUtilProductoAdicion = " . $activo->vidaUtilProductoAdicion[0]);
                }
            }

            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $errores);
            $errores = array();



            for ($i2 = 0; $i2 < count($caracteristicas); $i2++) {

                $bandera = true;

                if ($caracteristicas[$i2]['idProducto'] == 0) {
                    $errores[$posicion]["referenciaProducto"] = $caracteristicas[$i2]["referenciaProducto"];
                    $errores[$posicion]["nombreLargoProducto"] = $caracteristicas[$i2]["referenciaProducto"];
                    $errores[$posicion]["error"] = 'La referencia  ' . $caracteristicas[$i2]["referenciaProducto"] . ' a la cual se le desea adicionar la caracteristica no existe';
                    $bandera = false;
                    $posicion++;
                }


                if ($bandera == true) {
                    $sql = "select Categoria_idCategoria from Producto where referenciaProducto = '" . $caracteristicas[$i2]['referenciaProducto'] . "'";
                    $datos = $bd->ConsultarVista($sql);

                    if (count($datos) > 0) {

                        $categoria->idCategoria = $datos[0]["Categoria_idCategoria"];

                        $categoria->idCaracteristica = 0;
                        $categoria->nombreCaracteristica = $caracteristicas[$i2]['nombreProductoCaracteristica'];
                        $categoria->pideCantidadCaracteristica = $caracteristicas[$i2]['cantidadProductoCaracteristica'] != '' ? 1 : 0;
                        $categoria->pideDescripcionCaracteristica = $caracteristicas[$i2]['descripcionProductoCaracteristica'] != '' ? 1 : 0;
                        $categoria->pideMedidaCaracteristica = $caracteristicas[$i2]['medidaProductoCaracteristica'] != '' ? 1 : 0;
                        $categoria->AdicionarCaracteristica();

                        $sqlcar = "select max(idCaracteristica) as idCaracteristica from Caracteristica";
                        $dat = $bd->ConsultarVista($sqlcar);

                        if (count($dat) > 0) {
                            $activo->idProductoCaracteristica[0] = 0;
                            $activo->Caracteristica_idCaracteristica[0] = $dat[0]['idCaracteristica'];
                            $activo->cantidadProductoCaracteristica[0] = isset($dat[0]['cantidadProductoCaracteristica']) ? $caracteristicas[$i2]['cantidadProductoCaracteristica'] : 0;
                            $activo->nombreProductoCaracteristica[0] = isset($caracteristicas[$i2]['nombreProductoCaracteristica']) ? $caracteristicas[$i2]['nombreProductoCaracteristica'] : '';
                            $activo->descripcionProductoCaracteristica[0] = isset($caracteristicas[$i2]['descripcionProductoCaracteristica']) ? $caracteristicas[$i2]['descripcionProductoCaracteristica'] : '';
                            $activo->medidaProductoCaracteristica[0] = isset($caracteristicas[$i2]['medidaProductoCaracteristica']) ? $caracteristicas[$i2]['medidaProductoCaracteristica'] : '';
                            $activo->AdicionarProductoCaracteristica();
                        }
                    } else {
                        $errores[$posicion]["referenciaProducto"] = $caracteristicas[$i2]["referenciaProducto"];
                        $errores[$posicion]["nombreLargoProducto"] = $caracteristicas[$i2]["referenciaProducto"];
                        $errores[$posicion]["error"] = 'la referencia ' . $caracteristicas[$i2]["referenciaProducto"] . ' no tiene categoria';
                        $bandera = false;
                        $posicion++;
                    }
                }
            }


            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $errores);
            $errores = array();



            for ($i2 = 0; $i2 < count($depreciacion); $i2++) {


                $sqlref = "select idProducto from Producto where referenciaProducto = '" . $depreciacion[$i2]["referenciaProducto"] . "'";
                $datosref = $bd->ConsultarVista($sqlref);


                $depreciacionAcumulada = 0;
                $mesesDepreciados = 0;
                $depreciacionPendiente = 0;
                $mesesPendientes = 0;

                $depreciacionAcumuladaNIIF = 0;
                $mesesDepreciadosNIIF = 0;
                $depreciacionPendienteNIIF = 0;
                $mesesPendientesNIIF = 0;


                $activo->idProducto = $datosref[0]['idProducto'];
                $activo->idProductoDepreciacion[0] = 0;
                $activo->fechaProductoDepreciacion[0] = (isset($depreciacion[$i2]["fechaProductoDepreciacion"]) ? $depreciacion[$i2]["fechaProductoDepreciacion"] : '');
                $activo->diasProductoDepreciacion[0] = (isset($depreciacion[$i2]["diasProductoDepreciacion"]) ? $depreciacion[$i2]["diasProductoDepreciacion"] : 0);
                $activo->mesProductoDepreciacion[0] = (isset($depreciacion[$i2]["mesProductoDepreciacion"]) ? $depreciacion[$i2]["mesProductoDepreciacion"] : 0);

                $activo->valorMesProductoDepreciacion[0] = (isset($depreciacion[$i2]["valorMesProductoDepreciacion"]) ? $depreciacion[$i2]["valorMesProductoDepreciacion"] : 0);
                $activo->valorAcumuladoProductoDepreciacion[0] = (isset($depreciacion[$i2]["valorAcumuladoProductoDepreciacion"]) ? $depreciacion[$i2]["valorAcumuladoProductoDepreciacion"] : 0);
                $activo->valorContableProductoDepreciacion[0] = (isset($depreciacion[$i2]["valorContableProductoDepreciacion"]) ? $depreciacion[$i2]["valorContableProductoDepreciacion"] : 0);

                $activo->valorMesNIIFProductoDepreciacion[0] = (isset($depreciacion[$i2]["valorMesNIIFProductoDepreciacion"]) ? $depreciacion[$i2]["valorMesNIIFProductoDepreciacion"] : 0);
                $activo->valorAcumuladoNIIFProductoDepreciacion[0] = (isset($depreciacion[$i2]["valorAcumuladoNIIFProductoDepreciacion"]) ? $depreciacion[$i2]["valorAcumuladoNIIFProductoDepreciacion"] : 0);
                $activo->valorRevaluacionNIIFProductoDepreciacion[0] = (isset($depreciacion[$i2]["valorRevaluacionNIIFProductoDepreciacion"]) ? $depreciacion[$i2]["valorRevaluacionNIIFProductoDepreciacion"] : 0);
                $activo->valorContableNIIFProductoDepreciacion[0] = (isset($depreciacion[$i2]["valorContableNIIFProductoDepreciacion"]) ? $depreciacion[$i2]["valorContableNIIFProductoDepreciacion"] : 0);

                $activo->valorRevaluacionNIIFProductoDepreciacion[0] = (isset($depreciacion[$i2]["valorRevaluacionNIIFProductoDepreciacion"]) ? $depreciacion[$i2]["valorRevaluacionNIIFProductoDepreciacion"] : 0);


                $depreciacionAcumulada = $activo->valorAcumuladoProductoDepreciacion[0];
                $mesesDepreciados = $activo->mesProductoDepreciacion[0];
                $depreciacionPendiente = $activo->valorDepreciableProducto[0] - $depreciacionAcumulada;
                $mesesPendientes = (isset($datosref[0]['vidaUtilProducto']) ? $datosref[0]['vidaUtilProducto'] : 0) - $mesesDepreciados;

                $depreciacionAcumuladaNIIF = $activo->valorAcumuladoNIIFProductoDepreciacion[0];
                $mesesDepreciadosNIIF = $activo->mesProductoDepreciacion[0];
                $depreciacionPendienteNIIF = $activo->valorDepreciableProducto[0] - $depreciacionAcumuladaNIIF;
                $mesesPendientesNIIF = (isset($datosref[0]["vidaUtilNIIFProducto"]) ? $datosref[0]["vidaUtilNIIFProducto"] : 0) - $mesesDepreciadosNIIF;

                $activo->depreciacionAcumuladaProducto = $depreciacionAcumulada;
                $activo->mesesDepreciadosProducto = $mesesDepreciados;
                $activo->depreciacionPendienteProducto = $depreciacionPendiente;
                $activo->mesesPendientesProducto = $mesesPendientes;

                $activo->depreciacionAcumuladaNIIFProducto = $depreciacionAcumuladaNIIF;
                $activo->mesesDepreciadosNIIFProducto = $mesesDepreciadosNIIF;
                $activo->depreciacionPendienteNIIFProducto = $depreciacionPendienteNIIF;
                $activo->mesesPendientesNIIFProducto = $mesesPendientesNIIF;

                //                var_dump($activo->idProductoDepreciacion);
                //                exit();


                $activo->AdicionarProductoDepreciacion();
            }


            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $errores);
            $errores = array();


            //            echo '<pre>';
            //            echo '<pre>'.var_dump($avaluolocal).'<pre>';
            //            echo '<pre>';

            for ($i2 = 0; $i2 < count($avaluolocal); $i2++) {

                $activo->idProductoAvaluo[0] = 0;
                $activo->fechaProductoAvaluo[0] = (isset($avaluolocal[$i2]["fechaProductoAvaluo"]) ? $avaluolocal[$i2]["fechaProductoAvaluo"] : 0);
                $activo->valorProductoAvaluo[0] = (isset($avaluolocal[$i2]["valorProductoAvaluo"]) ? $avaluolocal[$i2]["valorProductoAvaluo"] : 0);
                $activo->valorResidualProductoAvaluo[0] = (isset($avaluolocal[$i2]["valorResidualProductoAvaluo"]) ? $avaluolocal[$i2]["valorResidualProductoAvaluo"] : 0);
                $activo->valorDepresiableProductoAvaluo[0] = ($activo->valorProductoAvaluo[$i2]) - ($activo->valorResidualProductoAvaluo[$i2]);
                $activo->vidaUtilProductoAvaluo[0] = (isset($avaluolocal[$i2]["vidaUtilProductoAvaluo"]) ? $avaluolocal[$i2]["vidaUtilProductoAvaluo"] : 0);
                $activo->avaluadorProductoAvaluo[0] = (isset($avaluolocal[$i2]["avaluadorProductoAvaluo"]) ? $avaluolocal[$i2]["avaluadorProductoAvaluo"] : '');
                $activo->observacionProductoAvaluo[0] = (isset($avaluolocal[$i2]["observacionProductoAvaluo"]) ? $avaluolocal[$i2]["observacionProductoAvaluo"] : '');
                $activo->metodoDepreciacionProductoAvaluo[0] = '';
                $activo->tipoProductoAvaluo[0] = 'local';
                $activo->idProducto = $avaluoniif[$i2]["idProducto"];
                $activo->AdicionarProductoAvaluo();
            }

            //            echo '<br>'.'TOTAL AVALUOS '.count($avaluolocal).'<br>';


            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $errores);
            $errores = array();
            //
            //            echo '<pre>';
            //            echo '<pre>'.var_dump($avaluoniif).'<pre>';
            //            echo '<pre>';



            for ($i2 = 0; $i2 < count($avaluoniif); $i2++) {
                $activo->idProductoAvaluo[0] = 0;
                $activo->fechaProductoAvaluo[0] = (isset($avaluoniif[$i2]["fechaProductoAvaluo"]) ? $avaluoniif[$i2]["fechaProductoAvaluo"] : '');
                $activo->valorProductoAvaluo[0] = (isset($avaluoniif[$i2]["valorProductoAvaluo"]) ? $avaluoniif[$i2]["valorProductoAvaluo"] : 0);
                $activo->valorResidualProductoAvaluo[0] = (isset($avaluoniif[$i2]["valorResidualProductoAvaluo"]) ? $avaluoniif[$i2]["valorResidualProductoAvaluo"] : 0);
                $activo->valorDepresiableProductoAvaluo[0] = ($activo->valorProductoAvaluo[$i2]) - ($activo->valorResidualProductoAvaluo[$i2]);
                $activo->vidaUtilProductoAvaluo[0] = (isset($avaluoniif[$i2]["vidaUtilProductoAvaluo"]) ? $avaluoniif[$i2]["vidaUtilProductoAvaluo"] : 0);
                $activo->avaluadorProductoAvaluo[0] = (isset($avaluoniif[$i2]["avaluadorProductoAvaluo"]) ? $avaluoniif[$i2]["avaluadorProductoAvaluo"] : '');
                $activo->observacionProductoAvaluo[0] = (isset($avaluoniif[$i2]["observacionProductoAvaluo"]) ? $avaluoniif[$i2]["observacionProductoAvaluo"] : '');
                $activo->metodoDepreciacionProductoAvaluo[0] = (isset($avaluoniif[$i2]["metodoDepreciacionProductoAvaluo"]) ? $avaluoniif[$i2]["metodoDepreciacionProductoAvaluo"] : '');
                $activo->tipoProductoAvaluo[0] = 'niif';
                $activo->idProducto = $avaluoniif[$i2]["idProducto"];
                $activo->AdicionarProductoAvaluo();
            }

            //            echo '<br>'.'TOTAL AVALUOS '.count($avaluoniif).'<br>';



            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $errores);
            $errores = array();




            return $retorno;
        }

        function llenarPropiedadesProducto($referencias = array(), $kit = array(), $equivalencias = array(), $terceros = array(), $seriales = array(), $sustitutos = array()) {
            set_time_limit(0);
            //echo 'entra';
            //            var_dump($referencias);
            // instanciamos la clase producto y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'producto.class.php';
            $producto = new Producto();
            $retorno = array();
            require_once('../clases/codigobarras.class.php');
            $codigobarras = new CodigoBarras();
            $retorno = array();
            $producto->Producto();
            // contamos los registros del array de productos
            $totalreg = (isset($referencias[0]["referenciaProducto"]) ? count($referencias) : 0);

            //                    var_dump($referencias);
            //print_r($referencias);
            //print_r($sustitutos);
            for ($i = 0; $i < $totalreg; $i++) {
                $newerrors = $this->validarTipoDatoProducto($i, $referencias);

                if (!isset($newerrors[0]["error"])) {
                    //                            echo 'entra';

                    $nuevoserrores = $this->validarProducto($referencias[$i]["referenciaProducto"], $referencias[$i]["codigoBarrasProducto"], $i, $referencias);
                    $totalerr = count($nuevoserrores);

                    if (!isset($nuevoserrores[0]["error"])) {
                        //                                echo 'entra';
                        // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys



                        $producto->idProducto = (isset($referencias[$i]["idProducto"]) ? $referencias[$i]["idProducto"] : 0 );
                        $producto->referenciaProducto = (isset($referencias[$i]["referenciaProducto"]) ? $referencias[$i]["referenciaProducto"] : '');
                        $producto->codigoAlternoProducto = (isset($referencias[$i]["codigoAlternoProducto"]) ? $referencias[$i]["codigoAlternoProducto"] : '');
                        $producto->nombreLargoProducto = (isset($referencias[$i]["nombreLargoProducto"]) ? $referencias[$i]["nombreLargoProducto"] : '');
                        $producto->clasificacionProducto = (isset($referencias[$i]["clasificacionProducto"]) ? $referencias[$i]["clasificacionProducto"] : '');
                        $producto->nombreCortoProducto = (isset($referencias[$i]["nombreCortoProducto"]) ? $referencias[$i]["nombreCortoProducto"] : '');
                        $producto->fechaCreacionProducto = (isset($referencias[$i]["fechaCreacionProducto"]) ? $referencias[$i]["fechaCreacionProducto"] : date("Y-m-d"));
                        $producto->precioProducto = (isset($referencias[$i]["precioProducto"]) ? $referencias[$i]["precioProducto"] : 0);
                        $producto->costoEstandarProducto = (isset($referencias[$i]["costoEstandarProducto"]) ? $referencias[$i]["costoEstandarProducto"] : 0);
                        $producto->estadoProducto = 'ACTIVO';
                        $producto->EstadoConservacion_idEstadoConservacion = (isset($referencias[$i]["EstadoConservacion_idEstadoConservacion"]) ? $referencias[$i]["EstadoConservacion_idEstadoConservacion"] : 0);
                        $producto->TipoProducto_idTipoProducto = (isset($referencias[$i]["TipoProducto_idTipoProducto"]) ? $referencias[$i]["TipoProducto_idTipoProducto"] : 0);
                        $producto->TipoNegocio_idTipoNegocio = (isset($referencias[$i]["TipoNegocio_idTipoNegocio"]) ? $referencias[$i]["TipoNegocio_idTipoNegocio"] : 0);
                        $producto->Talla_idTalla = (isset($referencias[$i]['Talla_idTalla']) ? $referencias[$i]['Talla_idTalla'] : 0);
                        $producto->TallaComplemento_idTallaComplemento = (isset($referencias[$i]['TallaComplemento_idTallaComplemento']) ? $referencias[$i]['TallaComplemento_idTallaComplemento'] : 0);
                        $producto->Area_idArea = (isset($referencias[$i]['Area_idArea']) ? $referencias[$i]['Area_idArea'] : 0);

                        // si esta en blanco el codigo de barras, le generamos uno nuevo si el esquema de producto asociado tiene el chulo de generar codigo de barras
                        //                if (empty($referencias[$i]["codigoBarrasProducto"]))
                        //                {
                        //                    if($referencias[$i]["generaCodigoBarrasEsquemaProducto"] == 1)
                        //                    {
                        //                        $producto->codigoBarrasProducto = $codigobarras->GenerarCodigoBarras();
                        //                    }
                        //                }
                        //                else
                        //                {
                        //                    $producto->codigoBarrasProducto = $referencias[$i]["codigoBarrasProducto"];
                        //                }


                        $producto->codigoBarrasProducto = $referencias[$i]["codigoBarrasProducto"];
                        $producto->Color_idColor = (isset($referencias[$i]['Color_idColor']) ? $referencias[$i]['Color_idColor'] : 0);
                        $producto->Temporada_idTemporada = (isset($referencias[$i]['Temporada_idTemporada']) ? $referencias[$i]['Temporada_idTemporada'] : 0);
                        $producto->FichaTecnica_idFichaTecnica = (isset($referencias[$i]['FichaTecnica_idFichaTecnica']) ? $referencias[$i]['FichaTecnica_idFichaTecnica'] : 0);
                        $producto->PosicionArancelaria_idPosicionArancelaria = (isset($referencias[$i]['PosicionArancelaria_idPosicionArancelaria']) ? $referencias[$i]['PosicionArancelaria_idPosicionArancelaria'] : 0);
                        $producto->Categoria_idCategoria = (isset($referencias[$i]['Categoria_idCategoria']) ? $referencias[$i]['Categoria_idCategoria'] : 0);
                        $producto->UnidadMedida_idCompra = (isset($referencias[$i]['UnidadMedida_idCompra']) ? $referencias[$i]['UnidadMedida_idCompra'] : 0);
                        $producto->UnidadMedida_idVenta = (isset($referencias[$i]['UnidadMedida_idVenta']) ? $referencias[$i]['UnidadMedida_idVenta'] : 0);

                        $producto->Clima_idClima = (isset($referencias[$i]['Clima_idClima']) ? $referencias[$i]['Clima_idClima'] : 0);
                        $producto->Estrategia_idEstrategia = (isset($referencias[$i]['Estrategia_idEstrategia']) ? $referencias[$i]['Estrategia_idEstrategia'] : 0);
                        $producto->Difusion_idDifusion = (isset($referencias[$i]['Difusion_idDifusion']) ? $referencias[$i]['Difusion_idDifusion'] : 0);
                        $producto->Seccion_idSeccion = (isset($referencias[$i]['Seccion_idSeccion']) ? $referencias[$i]['Seccion_idSeccion'] : 0);
                        $producto->Evento_idEvento = (isset($referencias[$i]['Evento_idEvento']) ? $referencias[$i]['Evento_idEvento'] : 0);
                        $producto->ClienteObjetivo_idClienteObjetivo = (isset($referencias[$i]['ClienteObjetivo_idClienteObjetivo']) ? $referencias[$i]['ClienteObjetivo_idClienteObjetivo'] : 0);
                        $producto->EsquemaProducto_idEsquemaProducto = (isset($referencias[$i]['EsquemaProducto_idEsquemaProducto']) ? $referencias[$i]['EsquemaProducto_idEsquemaProducto'] : 0);

                        $producto->Pais_idPaisOrigen = (isset($referencias[$i]['Pais_idPaisOrigen']) ? $referencias[$i]['Pais_idPaisOrigen'] : 0);
                        $producto->diasReposicionProducto = (isset($referencias[$i]['diasReposicionProducto']) ? $referencias[$i]['diasReposicionProducto'] : 0);
                        $producto->puntoPedidoProducto = (isset($referencias[$i]['puntoPedidoProducto']) ? $referencias[$i]['puntoPedidoProducto'] : 0);
                        $producto->cantidadSeguridadProducto = (isset($referencias[$i]['cantidadSeguridadProducto']) ? $referencias[$i]['cantidadSeguridadProducto'] : 0);
                        $producto->stockMinimoProducto = (isset($referencias[$i]['stockMinimoProducto']) ? $referencias[$i]['stockMinimoProducto'] : 0);
                        $producto->Composicion_idComposicion = (isset($referencias[$i]['Composicion_idComposicion']) ? $referencias[$i]['Composicion_idComposicion'] : 0);
                        $producto->Marca_idMarca = (isset($referencias[$i]['Marca_idMarca']) ? $referencias[$i]['Marca_idMarca'] : 0);

                        $producto->SegmentoOperacion_idSegmentoOperacion = (isset($referencias[$i]['SegmentoOperacion_idSegmentoOperacion']) ? $referencias[$i]['SegmentoOperacion_idSegmentoOperacion'] : 0);

                        $producto->Tono_idTono = (isset($referencias[$i]['Tono_idTono']) ? $referencias[$i]['Tono_idTono'] : 0);
                        $producto->Pinta_idPinta = (isset($referencias[$i]['Pinta_idPinta']) ? $referencias[$i]['Pinta_idPinta'] : 0);
                        $producto->CalibreHilo_idCalibreHilo = (isset($referencias[$i]['CalibreHilo_idCalibreHilo']) ? $referencias[$i]['CalibreHilo_idCalibreHilo'] : 0);

                        $producto->stockMaximoProducto = (isset($referencias[$i]['stockMaximoProducto']) ? $referencias[$i]['stockMaximoProducto'] : 0);
                        $producto->diasVidaUtilProducto = (isset($referencias[$i]['diasVidaUtilProducto']) ? $referencias[$i]['diasVidaUtilProducto'] : 0);
                        $producto->altoProducto = (isset($referencias[$i]['altoProducto']) ? $referencias[$i]['altoProducto'] : 0);
                        $producto->anchoProducto = (isset($referencias[$i]['anchoProducto']) ? $referencias[$i]['anchoProducto'] : 0);
                        $producto->profundidadProducto = (isset($referencias[$i]['profundidadProducto']) ? $referencias[$i]['profundidadProducto'] : 0);
                        $producto->pesoBrutoProducto = (isset($referencias[$i]['pesoBrutoProducto']) ? $referencias[$i]['pesoBrutoProducto'] : 0);
                        $producto->pesoNetoProducto = (isset($referencias[$i]['pesoNetoProducto']) ? $referencias[$i]['pesoNetoProducto'] : 0);

                        $producto->cantidadContenidaProducto = ((isset($referencias[$i]['cantidadContenidaProducto']) and $referencias[$i]['cantidadContenidaProducto'] != '' and $referencias[$i]['cantidadContenidaProducto'] != 0) ? $referencias[$i]['cantidadContenidaProducto'] : 1);
                        $producto->Tercero_idCliente = (isset($referencias[$i]['Tercero_idCliente']) ? $referencias[$i]['Tercero_idCliente'] : 0);
                        $producto->referenciaClienteProducto = (isset($referencias[$i]['referenciaClienteProducto']) ? $referencias[$i]['referenciaClienteProducto'] : '');
                        $producto->Tercero_idProveedor = (isset($referencias[$i]['Tercero_idProveedor']) ? $referencias[$i]['Tercero_idProveedor'] : 0);
                        $producto->referenciaProveedorProducto = (isset($referencias[$i]['referenciaProveedorProducto']) ? $referencias[$i]['referenciaProveedorProducto'] : '');
                        $producto->nombreFabricanteProducto = (isset($referencias[$i]['nombreFabricanteProducto']) ? $referencias[$i]['nombreFabricanteProducto'] : '');

                        $producto->BodegaUbicacion_idBodegaUbicacion = 0;
                        $producto->observacionesProducto = (isset($referencias[$i]['observacionesProducto']) ? $referencias[$i]['observacionesProducto'] : '');
                        $producto->imagen1Producto = (isset($referencias[$i]['imagen1Producto']) ? $referencias[$i]['imagen1Producto'] : '');
                        $producto->imagen2Producto = (isset($referencias[$i]['imagen2Producto']) ? $referencias[$i]['imagen2Producto'] : '');

                        $producto->porcentajeIvaProducto = 0;
                        $producto->ivaIncluidoProducto = (isset($referencias[$i]['ivaIncluidoProducto']) ? $referencias[$i]['ivaIncluidoProducto'] : 0);

                        $producto->acumulaPuntosProducto = (isset($referencias[$i]['acumulaPuntosProducto']) ? $referencias[$i]['acumulaPuntosProducto'] : 0);
                        $producto->redimePuntosProducto = (isset($referencias[$i]['redimePuntosProducto']) ? $referencias[$i]['redimePuntosProducto'] : 0);

                        $producto->numeroFacturaCompraProducto = (isset($referencias[$i]['numeroFacturaCompraProducto']) ? $referencias[$i]['numeroFacturaCompraProducto'] : '');
                        $producto->numeroFacturaVentaProducto = (isset($referencias[$i]['numeroFacturaVentaProducto']) ? $referencias[$i]['numeroFacturaVentaProducto'] : '');
                        $producto->valorCompraProducto = (isset($referencias[$i]['valorCompraProducto']) ? $referencias[$i]['valorCompraProducto'] : 0 );
                        $producto->valorVentaProducto = (isset($referencias[$i]['valorVentaProducto']) ? $referencias[$i]['valorVentaProducto'] : 0);
                        $producto->modeloProducto = (isset($referencias[$i]['modeloProducto']) ? $referencias[$i]['modeloProducto'] : '');
                        $producto->serialProducto = (isset($referencias[$i]['serialProducto']) ? $referencias[$i]['serialProducto'] : '');
                        $producto->tipoActivoProducto = (isset($referencias[$i]['tipoActivoProducto']) ? $referencias[$i]['tipoActivoProducto'] : '');
                        $producto->valorRescateProducto = (isset($referencias[$i]['valorRescateProducto']) ? $referencias[$i]['valorRescateProducto'] : 0);
                        $producto->valorDepreciableProducto = (isset($referencias[$i]['valorDepreciableProducto']) ? $referencias[$i]['valorDepreciableProducto'] : 0);

                        $producto->idProductoImpuesto = array();
                        $producto->idProductoRetencion = array();
                        //                                if($referencias[$i]['idProducto'] == 0)
                        //                                {
                        //                                   $producto->fechaCreacionProducto = date("Y-m-d");
                        //                                }
                        //                                else
                        //                                {
                        //                                     $producto->fechaCreacionProducto = $referencias[$i]["fechaCreacionProducto"];
                        //                                }
                        // cada que llenamos un producto, lo cargamos a la base de datos
                        // si el id esta lleno, lo actualizamos, si esta vacio lo insertamos
                        //                                    echo 'luka 2';
                        if ($referencias[$i]['idProducto'] == 0) {
                            //                                            echo 'entra';
                            $producto->AdicionarProducto();
                        } else {
                            //                                        echo 'actualiza';
                            $producto->ModificarProducto();
                        }
                    } else {
                        $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                    }
                } else {
                    $retorno = array_merge((array) $retorno, (array) $newerrors);
                }
            }

            require_once'db.class.php';
            require_once'conf.class.php';
            $bd = Db::getInstance();



            $erroresdetalle = array();
            $posicion = 0;


            //                    $regser = 0;
            //                    $regequ = 0;
            //                    $regkit = 0;
            //                    $regter = 0;

            for ($i2 = 0; $i2 < count($kit); $i2++) {
                $bandera = true;

                $producto->idProducto = 0;
                //                         echo "referenciaProducto = '" . $kit[$i2]["referenciaProducto"] . "'";
                $producto->ConsultarIdProducto("referenciaProducto = '" . $kit[$i2]["referenciaProducto"] . "'");
                $kit[$i2]["Producto_idProducto"] = $producto->idProducto;

                $producto->idProducto = 0;
                $producto->ConsultarIdProducto("referenciaProducto = '" . $kit[$i2]["referenciaProductoKit"] . "'");
                $kit[$i2]["ProductoKit_idProductoKit"] = $producto->idProducto;


                if ($kit[$i2]["Producto_idProducto"] == 0) {

                    $erroresdetalle[$posicion]["referenciaProducto"] = $kit[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["nombreLargoProducto"] = $kit[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["error"] = 'La referencia a la cual se le desea adicionar el kit no existe';
                    $bandera = false;
                    $posicion++;
                }

                if ($bandera == true) {
                    if ($kit[$i2]["ProductoKit_idProductoKit"] == 0) {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $kit[$i2]["referenciaProductoKit"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $kit[$i2]["referenciaProductoKit"];
                        $erroresdetalle[$posicion]["error"] = 'La referencia Componente no existe';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {
                    if ($kit[$i2]["cantidadProductoKit"] < 0 || $kit[$i2]["cantidadProductoKit"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $kit[$i2]["referenciaProductoKit"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $kit[$i2]["referenciaProductoKit"];
                        $erroresdetalle[$posicion]["error"] = 'La cantidad del producto kit debe ser mayor o igual a cero';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {
                    if ($kit[$i2]["porcentajeCostoCompraProductoKit"] < 0 || $kit[$i2]["porcentajeCostoCompraProductoKit"] > 100 || $kit[$i2]["porcentajeCostoCompraProductoKit"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $kit[$i2]["referenciaProductoKit"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $kit[$i2]["referenciaProductoKit"];
                        $erroresdetalle[$posicion]["error"] = 'El porcentaje de costo de compra del kit debe estar entre 0 y 100';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {

                    $sql = "select * from ProductoKit where Producto_idProducto = " .
                            $kit[$i2]["Producto_idProducto"] . " and Producto_idProductoKit = " .
                            $kit[$i2]["ProductoKit_idProductoKit"];

                    $consulta = $bd->ConsultarVista($sql);
                    //
                    if (count($consulta) > 0) {
                        $sql = "delete from ProductoKit where Producto_idProducto = " .
                                $kit[$i2]["Producto_idProducto"] . " and Producto_idProductoKit = " .
                                $kit[$i2]["ProductoKit_idProductoKit"];

                        $bd->ejecutar($sql);
                    }


                    $producto->idProductoKit[0] = 0;
                    $producto->idProducto = $kit[$i2]["Producto_idProducto"];
                    $producto->Producto_idProductoKit[0] = $kit[$i2]["ProductoKit_idProductoKit"];
                    $producto->cantidadProductoKit[0] = $kit[$i2]["cantidadProductoKit"];
                    $producto->porcentajeCostoCompraProductoKit[0] = trim(str_replace(",", ".", number_format(($kit[$i2]["porcentajeCostoCompraProductoKit"] * 100), 2, ',', '')));

                    $producto->AdicionarProductoKit($producto->idProducto, 0);
                }
            }

            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $erroresdetalle);
            $erroresdetalle = array();

            for ($i2 = 0; $i2 < count($equivalencias); $i2++) {
                $bandera = true;

                $producto->idProducto = 0;
                $producto->ConsultarIdProducto("referenciaProducto = '" . $equivalencias[$i2]["referenciaProducto"] . "'");
                $equivalencias[$i2]["Producto_idProducto"] = $producto->idProducto;

                if ($equivalencias[$i2]["Producto_idProducto"] == 0) {
                    $erroresdetalle[$posicion]["referenciaProducto"] = $equivalencias[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["nombreLargoProducto"] = $equivalencias[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["error"] = 'La referencia ala cual se le desea adicionar la equivalencia no existe';
                    $bandera = false;
                    $posicion++;
                }


                if ($bandera == true) {
                    if ($equivalencias[$i2]["UnidadMedida_idUnidadMedida"] == 0) {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $equivalencias[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = 'La unidad de medida ' . $equivalencias[$i2]['codigoUnidadMedidaEquivalencia'] . ' no existe';
                        $erroresdetalle[$posicion]["error"] = 'Error';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {
                    if ($equivalencias[$i2]["cantidadContenidaProductoEquivalencia"] <= 0 || $equivalencias[$i2]["cantidadContenidaProductoEquivalencia"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $equivalencias[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $equivalencias[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["error"] = 'La cantidad del producto equivalencia debe ser mayor a cero';
                        $bandera = false;
                        $posicion++;
                    }
                }




                if ($bandera == true) {

                    $sql = "select * from ProductoEquivalencia where Producto_idProducto = " .
                            $equivalencias[$i2]["Producto_idProducto"] . "
                                                       and UnidadMedida_idUnidadMedida = " .
                            $equivalencias[$i2]["UnidadMedida_idUnidadMedida"];

                    $consulta = $bd->ConsultarVista($sql);

                    if (count($consulta) > 0) {

                        $sql = "delete from ProductoEquivalencia where Producto_idProducto = " .
                                $equivalencias[$i2]["Producto_idProducto"] . "
                                                       and UnidadMedida_idUnidadMedida = " .
                                $equivalencias[$i2]["UnidadMedida_idUnidadMedida"];

                        $bd->ejecutar($sql);
                    }


                    $producto->idProducto = $equivalencias[$i2]['Producto_idProducto'];
                    $producto->idProductoEquivalencia[0] = 0;
                    $producto->UnidadMedida_idUnidadMedidaProductoEquivalencia[0] = $equivalencias[$i2]['UnidadMedida_idUnidadMedida'];
                    $producto->cantidadContenidaProductoEquivalencia[0] = $equivalencias[$i2]["cantidadContenidaProductoEquivalencia"];
                    $producto->AdicionarProductoEquivalencia();
                }
            }


            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $erroresdetalle);
            $erroresdetalle = array();

            for ($i2 = 0; $i2 < count($seriales); $i2++) {
                $bandera = true;

                if ($seriales[$i2]["Producto_idProducto"] == 0) {
                    $erroresdetalle[$posicion]["referenciaProducto"] = $seriales[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["nombreLargoProducto"] = $seriales[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["error"] = 'La referencia a la cual se le desea adicionar el serial no existe';
                    $bandera = false;
                    $posicion++;
                }



                if (($seriales[$i2]["numeroProductoSerie"] == '' || $seriales[$i2]["fechaCrecionProductoSerie"] == '' || $seriales[$i2]["fechaCrecionProductoSerie"] == '') && $bandera == true) {
                    $erroresdetalle[$posicion]["referenciaProducto"] = $seriales[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["nombreLargoProducto"] = 'Campos vacios';
                    $erroresdetalle[$posicion]["error"] = 'debe completar todos los campos del serial';
                    $bandera = false;
                    $posicion++;
                }




                if ($bandera == true) {
                    for ($i3 = 0; $i3 < count($seriales); $i3++) {
                        if ($i2 != $i3) {
                            if ($seriales[$i2]["numeroProductoSerie"] == $seriales[$i3]["numeroProductoSerie"]) {
                                $erroresdetalle[$posicion]["referenciaProducto"] = $seriales[$i2]["referenciaProducto"];
                                $erroresdetalle[$posicion]["nombreLargoProducto"] = $seriales[$i2]["numeroProductoSerie"];
                                $erroresdetalle[$posicion]["error"] = 'El numero del producto serie se repite en las lineas ' . ($i2 + 1) . ' y ' . ($i3 + 1);
                                $bandera = false;
                                $posicion++;
                            }
                        }
                    }
                }


                if ($bandera == true) {

                    $sql = "select * from ProductoSerie where Producto_idProducto = " .
                            $seriales[$i2]["Producto_idProducto"] . " and numeroProductoSerie = '" .
                            $seriales[$i2]["numeroProductoSerie"] . "'";

                    $consulta = $bd->ConsultarVista($sql);

                    if (count($consulta) > 0) {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $seriales[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $seriales[$i2]["numeroProductoSerie"];
                        $erroresdetalle[$posicion]["error"] = 'El producto con el serial ' . $seriales[$i2]["numeroProductoSerie"] . ' ya se encuentra asociado a la referencia indicada';
                        $bandera = false;
                        $posicion++;
                    } else {
                        $sql = "select * from ProductoSerie where numeroProductoSerie = '" .
                                $seriales[$i2]["numeroProductoSerie"] . "'";

                        $consulta = $bd->ConsultarVista($sql);

                        if (count($consulta) > 0) {
                            $erroresdetalle[$posicion]["referenciaProducto"] = $seriales[$i2]["referenciaProducto"];
                            $erroresdetalle[$posicion]["nombreLargoProducto"] = $seriales[$i2]["numeroProductoSerie"];
                            $erroresdetalle[$posicion]["error"] = 'El el serial ' . $seriales[$i2]["numeroProductoSerie"] . ' ya se encuentra registrado';
                            $bandera = false;
                            $posicion++;
                        }
                    }
                }



                if ($bandera == true) {
                    $producto->idProducto = $seriales[$i2]["Producto_idProducto"];
                    $producto->idProductoSerie[0] = 0;
                    $producto->numeroProductoSerie[0] = $seriales[$i2]["numeroProductoSerie"];
                    $producto->fechaCreacionProductoSerie[0] = $seriales[$i2]["fechaCrecionProductoSerie"];
                    $producto->fechaVencimientoProductoSerie[0] = $seriales[$i2]["fechaVencimientoProductoSerie"];
                    $producto->observacionProductoSerie[0] = $seriales[$i2]["observacionProductoSerie"];
                    $producto->AdicionarProductoSerie();
                }
            }

            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $erroresdetalle);
            $erroresdetalle = array();

            for ($i2 = 0; $i2 < count($terceros); $i2++) {
                $bandera = true;

                $producto->idProducto = 0;
                $producto->ConsultarIdProducto("referenciaProducto = '" . $terceros[$i2]["referenciaProducto"] . "'");
                $terceros[$i2]["Producto_idProducto"] = $producto->idProducto;

                if ($terceros[$i2]["Producto_idProducto"] == 0) {
                    $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["referenciaProducto"];
                    $erroresdetalle[$posicion]["error"] = 'La referencia ala cual se le desea adicionar el producto tercero no existe';
                    $bandera = false;
                    $posicion++;
                }

                if ($bandera == true) {
                    if ($terceros[$i2]["Tercero_idTercero"] == 0) {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["codigoAlternoTerceroProducto"];
                        $erroresdetalle[$posicion]["error"] = 'El tercero con el codigo alterno ' . $terceros[$i2]["codigoAlternoTerceroProducto"] . ' no existe';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {
                    if ($terceros[$i2]["UnidadMedida_idUnidadMedidaCompra"] == 0) {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["codigoUnidadMedidaCompra"];
                        $erroresdetalle[$posicion]["error"] = 'La unidad de medida no existe';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {
                    if ($terceros[$i2]["UnidadMedida_idUnidadMedidaVenta"] == 0) {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["codigoUnidadMedidaVenta"];
                        $erroresdetalle[$posicion]["error"] = 'La unidad de medida no existe';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {
                    if ($terceros[$i2]["codigoAlternoTerceroProducto"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["nombreLargoProducto"];
                        $erroresdetalle[$posicion]["error"] = 'El codigo alterno del tercero no puede estar en blanco';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {
                    if ($terceros[$i2]["referenciaProducto"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["nombreLargoProducto"];
                        $erroresdetalle[$posicion]["error"] = 'La referencia padre del producto tercero no puede estar vacia';
                        $bandera = false;
                        $posicion++;
                    }
                }


                if ($bandera == true) {
                    if ($terceros[$i2]["referenciaProductoTercero"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["nombreLargoProducto"];
                        $erroresdetalle[$posicion]["error"] = 'La referencia del producto tercero no puede estar en blanco';
                        $bandera = false;
                        $posicion++;
                    }
                }


                if ($bandera == true) {
                    if ($terceros[$i2]["nombreProductoTercero"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["nombreLargoProducto"];
                        $erroresdetalle[$posicion]["error"] = 'El nombre del producto tercero no puede estar vacio';
                        $bandera = false;
                        $posicion++;
                    }
                }


                if ($bandera == true) {
                    if ($terceros[$i2]["precioVentaPublicoProductoTercero"] <= 0 || $terceros[$i2]["precioVentaPublicoProductoTercero"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["nombreLargoProducto"];
                        $erroresdetalle[$posicion]["error"] = 'El precio de venta de producto debe ser mayo a cero';
                        $bandera = false;
                        $posicion++;
                    }
                }


                if ($bandera == true) {
                    if ($terceros[$i2]["factorUnidadMedidaProductoTercero"] <= 0 || $terceros[$i2]["precioVentaPublicoProductoTercero"] == '') {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $terceros[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $terceros[$i2]["nombreLargoProducto"];
                        $erroresdetalle[$posicion]["error"] = 'EL factor de unidad de medida debe ser mayor o igual a cero';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {

                    $sql = "select * from ProductoTercero where Producto_idProducto = " .
                            $terceros[$i2]["Producto_idProducto"] . "
                                                       and Tercero_idTercero = " .
                            $terceros[$i2]["Tercero_idTercero"] . " and
                                                       referenciaProductoTercero = " . $terceros[$i2]["referenciaProductoTercero"];
                    //echo $sql;
                    $consulta = $bd->ConsultarVista($sql);

                    if (count($consulta) > 0) {
                        $sql = "delete from ProductoTercero where Producto_idProducto = " .
                                $terceros[$i2]["Producto_idProducto"] . "
                                                       and Tercero_idTercero = " .
                                $terceros[$i2]["Tercero_idTercero"] . " and
                                                       referenciaProductoTercero = " . $terceros[$i2]["referenciaProductoTercero"];
                        //                              echo $sql;
                        $bd->ejecutar($sql);
                    }


                    $producto->idProducto = $terceros[$i2]['Producto_idProducto'];
                    $producto->idProductoTercero[0] = 0;
                    $producto->Tercero_idTercero[0] = $terceros[$i2]['Tercero_idTercero'];
                    $producto->referenciaProductoTercero[0] = $terceros[$i2]['referenciaProductoTercero'];
                    $producto->codigoAlternoProductoTercero[0] = $terceros[$i2]["codigoAlternoProductoTercero"];
                    $producto->nombreProductoTercero[$i2] = $terceros[$i2]["nombreProductoTercero"];
                    $producto->UnidadMedida_idUnidadMedidaCompra[0] = $terceros[$i2]["UnidadMedida_idUnidadMedidaCompra"];
                    $producto->UnidadMedida_idUnidadMedidaVenta[0] = $terceros[$i2]["UnidadMedida_idUnidadMedidaVenta"];
                    $producto->factorUnidadMedidaProductoTercero[0] = $terceros[$i2]["factorUnidadMedidaProductoTercero"];
                    $producto->pluProductoTercero[0] = $terceros[$i2]["pluProductoTercero"];
                    $producto->codigoBarrasProductoTercero[0] = $terceros[$i2]["codigoBarrasProductoTercero"];
                    $producto->precioVentaPublicoProductoTercero[0] = $terceros[$i2]["precioVentaPublicoProductoTercero"];
                    $producto->AdicionarProductoTercero();
                }
            }

            $posicion = 0;
            $retorno = array_merge((array) $retorno, (array) $erroresdetalle);
            $erroresdetalle = array();

            for ($i2 = 0; $i2 < count($sustitutos); $i2++) {
                $bandera = true;
                if ($sustitutos[$i2]["Producto_idProducto"] == 0) {
                    $producto->idProducto = 0;
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $sustitutos[$i2]["referenciaProducto"] . "'");
                    $sustitutos[$i2]["Producto_idProducto"] = $producto->idProducto;
                    if ($sustitutos[$i2]["Producto_idProducto"] == 0) {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $sustitutos[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $sustitutos[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["error"] = 'La referencia ' . $sustitutos[$i2]["referenciaProducto"] . ' a la cual se le desea adicionar el sustituto no existe';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($sustitutos[$i2]["Producto_idProductoSustituto"] == 0) {
                    $producto->idProducto = 0;
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $sustitutos[$i2]["referenciaProductoSustituto"] . "'");
                    $sustitutos[$i2]["Producto_idProductoSustituto"] = $producto->idProducto;
                    if ($sustitutos[$i2]["Producto_idProductoSustituto"] == 0) {
                        $erroresdetalle[$posicion]["referenciaProducto"] = $sustitutos[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["nombreLargoProducto"] = $sustitutos[$i2]["referenciaProducto"];
                        $erroresdetalle[$posicion]["error"] = 'La referencia sustituta ' . $sustitutos[$i2]["referenciaProductoSustituto"] . ' no exite.';
                        $bandera = false;
                        $posicion++;
                    }
                }

                if ($bandera == true) {
                    $producto->idProducto = $sustitutos[$i2]['Producto_idProducto'];
                    $producto->idProductoSustituto[0] = 0;
                    $producto->Producto_idProductoSustituto[0] = $sustitutos[$i2]['Producto_idProductoSustituto'];
                    $producto->AdicionarProductoSustituto();
                }
            }

            $retorno = array_merge((array) $retorno, (array) $erroresdetalle);


            return $retorno;
        }

        function validarEncabezadoActivos($referenciaProducto, $x, $referencias) {
            $swerror = true;
            $errores = array();
            $linea = 0;
            require_once'db.class.php';
            require_once'conf.class.php';
            $bd = Db::getInstance();
            //            echo '<pre>';
            //            print_r($referencias);
            //            echo '<pre>';

            for ($i = 0; $i < count($referencias); $i++) {
                if ("'" . $referencias[$i]["referenciaProducto"] . "'" == "'" . $referenciaProducto . "'" and $i != $x) {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'La referencia ' . $referenciaProducto . ' esta repetida en el archivo, lineas ' . ($x + 4) . ' y ' . ($i + 4);
                    $swerror = false;
                    $linea++;
                }
            }

            $select = "select idTercero from Tercero where documentoTercero = " . $referencias[$x]['nombre1Proveedor'];
            $datoTerceroCompra = $bd->ConsultarVista($select);
            
            if($referencias[$x]['nombre1Proveedor'] != '' and !isset($datoTerceroCompra[0]))
            {    
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El proveedor '.$referencias[$x]['nombre1Proveedor'].' no existe';
                $swerror = false;
                $linea++;
            }    
            // validamos la referencia
            if ($referencias[$x]["referenciaProducto"] == '') {

                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La referencia esta en blanco';
                $swerror = false;
                $linea++;
            }

            // validamos la descripcion larga
            if ($referencias[$x]["nombreLargoProducto"] == '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La descripcion larga esta en blanco';
                $swerror = false;
                $linea++;
            }



            // validamos la clasificacion
            if ($referencias[$x]["clasificacionProducto"] == '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'Debe seleccionar a lo sumo una clasificacion';
                $swerror = false;
                $linea++;
            }

            if ($referencias[$x]["codigoAlternoCategoria"] == '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Categoria No puede Estar en Blanco ';
                $swerror = false;
                $linea++;
            }

            // validamos País de Origen
            if ($referencias[$x]["Pais_idPaisOrigen"] == 0 and $referencias[$x]["codigoAlternoPais"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El  País de Origen con el Código Alterno (' . $referencias[$x]["codigoAlternoPais"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Categoria tipo activo fijo
            if ($referencias[$x]["Categoria_idCategoria"] != 0 and $referencias[$x]["codigoAlternoCategoria"] != '') {
                $sqlcat = "select * from Categoria where idCategoria = " . $referencias[$x]["Categoria_idCategoria"];
                //                   echo $sqlcat;
                $datacat = $bd->ConsultarVista($sqlcat);

                if (($datacat[0]['tipoCategoria']) != 'A') {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["codigoAlternoCategoria"];
                    $errores[$linea]["error"] = 'La Categoria (' . $referencias[$x]["codigoAlternoCategoria"] . ') no es para activos fijos';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($referencias[$x]["Categoria_idCategoria"] == 0 and $referencias[$x]["codigoAlternoCategoria"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Categoria (' . $referencias[$x]["codigoAlternoCategoria"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos segmento operacion
            if ($referencias[$x]["SegmentoOperacion_idSegmentoOperacion"] == 0 and $referencias[$x]["CodigoAlternoSegmentoOperacion"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Segmento de Operacion (' . $referencias[$x]["codigoCategoria"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            if ($referencias[$x]["Marca_idMarca"] == 0 and $referencias[$x]["CodigoAlternoMarca"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La marca (' . $referencias[$x]["CodigoAlternoMarca"] . ') no existe';
                $swerror = false;
                $linea++;
            }


            // validamos la contabilizacion
            if ($referencias[$x]["opcionCompraProducto"] == 'Si'
                    and $referencias[$x]["valorOpcionCompraProducto"] == 0
                    and $referencias[$x]["metodoAdquisionNIIFProducto"] == 'Arrendamiento Operativo' ||
                    $referencias[$x]["metodoAdquisionNIIFProducto"] == 'Arrendamiento Financiero') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Valor de la Opción de Compra debe ser mayor a cero';
                $swerror = false;
                $linea++;
            }

            if ($referencias[$x]["SegmentoOperacion_idSegmentoOperacion"] == 0 and $referencias[$x]["CodigoAlternoSegmentoOperacion"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Segmento de Operacion (' . $referencias[$x]["codigoCategoria"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            if ($referencias[$x]["TipoProducto_idTipoProducto"] == 0 and $referencias[$x]["codigoAlternoTipoProducto"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Tipo de Producto (' . $referencias[$x]["codigoAlternoTipoProducto"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            if ($referencias[$x]["TipoNegocio_idTipoNegocio"] == 0 and $referencias[$x]["codigoAlternoTipoNegocio"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Tipo de Negocio (' . $referencias[$x]["codigoAlternoTipoProducto"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            //            echo $referencias[$x]["altoProducto"];
            //            if ($referencias[$x]["altoProducto"] < 0 || $referencias[$x]["altoProducto"] == '')
            //            {
            //                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
            //                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
            //                    $errores[$linea]["error"] = 'El Alto del Activo debe ser Mayor o Igual a Cero';
            //                    $swerror = false;
            //                    $linea++;
            //            }
            //
            //            if ($referencias[$x]["anchoProducto"] < 0 || $referencias[$x]["anchoProducto"] == '')
            //            {
            //                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
            //                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
            //                    $errores[$linea]["error"] = 'El Ancho del Activo debe ser Mayor o Igual a Cero';
            //                    $swerror = false;
            //                    $linea++;
            //            }
            //
            //            if ($referencias[$x]["profundidadProducto"] < 0 || $referencias[$x]["profundidadProducto"] == '')
            //            {
            //                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
            //                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
            //                    $errores[$linea]["error"] = 'La Profundidad del Activo debe ser Mayor o Igual a Cero';
            //                    $swerror = false;
            //                    $linea++;
            //            }
            //
            //            if ($referencias[$x]["pesoBrutoProducto"] < 0 || $referencias[$x]["pesoBrutoProducto"] == '')
            //            {
            //                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
            //                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
            //                    $errores[$linea]["error"] = 'El Peso Bruto del Activo debe ser Mayor o Igual a Cero';
            //                    $swerror = false;
            //                    $linea++;
            //            }
            //
            //            if ($referencias[$x]["pesoNetoProducto"] < 0 || $referencias[$x]["pesoNetoProducto"] == '')
            //            {
            //                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
            //                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
            //                    $errores[$linea]["error"] = 'El Peso Neto  del Activo debe ser Mayor o Igual a Cero';
            //                    $swerror = false;
            //                    $linea++;
            //            }
            //consultamos los datos de la compra teniendo en cuenta el numeor de la factura


            return $errores;
        }

        function validarProducto($referenciaProducto, $codigoBarrasProducto, $x, $referencias) {
            $swerror = true;
            $errores = array();
            $linea = 0;

            require_once('../clases/codigobarras.class.php');
            $codigobarras = new CodigoBarras();
            /* print_r($referencias);
              echo "<br>"; */

            //validamos que la referencia no este repetida en el mismo archivo de excel
            for ($i = 0; $i < count($referencias); $i++) {
                if ("'" . $referencias[$i]["referenciaProducto"] . "'" == "'" . $referenciaProducto . "'" and $i != $x) {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'La referencia ' . $referenciaProducto . ' esta repetida en el archivo, lineas ' . ($x + 4) . ' y ' . ($i + 4);
                    $swerror = false;
                    $linea++;
                }

                if ("'" . $referencias[$i]["codigoBarrasProducto"] . "'" != 0 and "'" . $referencias[$i]["codigoBarrasProducto"] . "'" == $codigoBarrasProducto and $i != $x) {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'El C&oacute;digo de Barras ' . $codigoBarrasProducto . ' esta repetido en el archivo, lineas ' . ($x + 4) . ' y ' . ($i + 4);
                    $swerror = false;
                    $linea++;
                }
            }


            // validamos si el codigo de barras ya existe en la base de datos con una referencia diferente (se valido en la importacion de productos)
            if (isset($referencias[$x]["errorbarras"]) and $referencias[$x]["errorbarras"] > 0) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El codigo de barras ' . $referencias[$x]["codigoBarrasProducto"] . ' ya existe en la base de datos con diferente referencia (id = ' . $referencias[$x]["errorbarras"] . ')';
                $swerror = false;
                $linea++;
            }

            //		require_once 'codigobarras.class.php';
            //		$codigobarras = new CodigoBarras();
            //		$codigoBarrasNuevo = $codigobarras->ValidarDigitoChequeo($codigoBarrasProducto);
            //		// validamos el digito de chequeo del codigo de barras
            //		if ($referencias[$x]["codigoBarrasProducto"] != '' and $codigoBarrasNuevo != $codigoBarrasProducto)
            //		{
            //			$errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
            //			$errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
            //			$errores[$linea]["error"] = 'El c&oacute;digo de barras ' . $codigoBarrasProducto . ' esta errado, el c&oacute;digo correcto es ' . $codigoBarrasNuevo;
            //			$swerror = false;
            //			$linea++;
            //		}
            // validamos la referencia
            if ($referencias[$x]["referenciaProducto"] == '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La referencia esta en blanco';
                $swerror = false;
                $linea++;
            }

            // validamos la descripcion larga
            if ($referencias[$x]["nombreLargoProducto"] == '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La descripcion larga esta en blanco';
                $swerror = false;
                $linea++;
            }

            // validamos la clasificacion
            if ($referencias[$x]["clasificacionProducto"] == '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'Debe seleccionar a lo sumo una clasificacion';
                $swerror = false;
                $linea++;
            }

            // validamos Talla
            if (isset($referencias[$x]["Talla_idTalla"]) and ( $referencias[$x]["Talla_idTalla"] == 0 and $referencias[$x]["codigoTalla"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Codigo Alterno de la Talla (' . $referencias[$x]["codigoTalla"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            if (isset($referencias[$x]["SegmentoOperacion_idSegmentoOperacion"]) and ( $referencias[$x]["SegmentoOperacion_idSegmentoOperacion"] == 0 and $referencias[$x]["codigoSegmentoOperacion"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Codigo Alterno del Segmento de Operacion (' . $referencias[$x]["codigoSegmentoOperacion"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            if (isset($referencias[$x]["Tono_idTono"]) and ( $referencias[$x]["Tono_idTono"] == 0 and $referencias[$x]["codigoTono"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Codigo Alterno del Tono (' . $referencias[$x]["codigoSegmentoOperacion"] . ') no existe';
                $swerror = false;
                $linea++;
            }
            if (isset($referencias[$x]["Pinta_idPinta"]) and ( $referencias[$x]["Pinta_idPinta"] == 0 and $referencias[$x]["codigoPinta"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Codigo Alterno de la Pinta (' . $referencias[$x]["codigoPinta"] . ') no existe';
                $swerror = false;
                $linea++;
            }
            if (isset($referencias[$x]["CalibreHilo_idCalibreHilo"]) and ( $referencias[$x]["CalibreHilo_idCalibreHilo"] == 0 and $referencias[$x]["codigoCalibreHilo"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Codigo Alterno del Calibre Hilo (' . $referencias[$x]["codigoCalibreHilo"] . ') no existe';
                $swerror = false;
                $linea++;
            }


            // validamos Color
            if (isset($referencias[$x]["Color_idColor"]) and ( $referencias[$x]["Color_idColor"] == 0 and $referencias[$x]["codigoColor"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Codigo Alterno del Color (' . $referencias[$x]["codigoColor"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos referencia ficha tecnica
            if (isset($referencias[$x]["FichaTecnica_idFichaTecnica"]) and ( $referencias[$x]["FichaTecnica_idFichaTecnica"] == 0 and $referencias[$x]["referenciaBaseFichaTecnica"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La referencia de Ficha Tecnica (' . $referencias[$x]["referenciaBaseFichaTecnica"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Marca
            if ($referencias[$x]["Marca_idMarca"] == 0 and $referencias[$x]["codigoMarca"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Marca (' . $referencias[$x]["codigoMarca"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Tipo de producto
            if ($referencias[$x]["TipoProducto_idTipoProducto"] == 0 and $referencias[$x]["codigoTipoProducto"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Tipo de Producto (' . $referencias[$x]["codigoTipoProducto"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Tipo de Negocio
            if ($referencias[$x]["TipoNegocio_idTipoNegocio"] == 0 and $referencias[$x]["codigoTipoNegocio"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Tipo de Negocio (' . $referencias[$x]["codigoTipoNegocio"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Temporada
            if (isset($referencias[$x]["Temporada_idTemporada"]) and ( $referencias[$x]["Temporada_idTemporada"] == 0 and $referencias[$x]["codigoTemporada"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Temporada (' . $referencias[$x]["codigoTemporada"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Estado de Conservacion
            if (isset($referencias[$x]["EstadoConservacion_idEstadoConservacion"]) and ( $referencias[$x]["EstadoConservacion_idEstadoConservacion"] == 0 and $referencias[$x]["codigoEstadoConservacion"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Estado de Conservacion (' . $referencias[$x]["codigoEstadoConservacion"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Composicion
            if (isset($referencias[$x]["Composicion_idComposicion"]) and ( $referencias[$x]["Composicion_idComposicion"] == 0 and $referencias[$x]["codigoComposicion"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Composicion (' . $referencias[$x]["codigoComposicion"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Posicoin Arancelaria
            if (isset($referencias[$x]["PosicionArancelaria_idPosicionArancelaria"]) and ( $referencias[$x]["PosicionArancelaria_idPosicionArancelaria"] == 0 and $referencias[$x]["codigoPosicionArancelaria"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Posicion Arancelaria (' . $referencias[$x]["codigoPosicionArancelaria"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Clima
            if (isset($referencias[$x]["Clima_idClima"]) and ( $referencias[$x]["Clima_idClima"] == 0 and $referencias[$x]["codigoClima"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Clima (' . $referencias[$x]["codigoClima"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Difusion
            if (isset($referencias[$x]["Difusion_idDifusion"]) and ( $referencias[$x]["Difusion_idDifusion"] == 0 and $referencias[$x]["codigoDifusion"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Difusion (' . $referencias[$x]["codigoDifusion"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Estrategia
            if (isset($referencias[$x]["Estrategia_idEstrategia"]) and ( $referencias[$x]["Estrategia_idEstrategia"] == 0 and $referencias[$x]["codigoEstrategia"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Estrategia (' . $referencias[$x]["codigoEstrategia"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Seccion
            if (isset($referencias[$x]["Seccion_idSeccion"]) and ( $referencias[$x]["Seccion_idSeccion"] == 0 and $referencias[$x]["codigoSeccion"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Seccion (' . $referencias[$x]["codigoSeccion"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            if (isset($referencias[$x]["Area_idArea"]) and ( $referencias[$x]["Area_idArea"] == 0 or $referencias[$x]["codigoArea"] == '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El &Aacute;rea (' . $referencias[$x]["codigoArea"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Evento
            if (isset($referencias[$x]["Evento_idEvento"]) and ( $referencias[$x]["Evento_idEvento"] == 0 and $referencias[$x]["codigoEvento"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Evento (' . $referencias[$x]["codigoEvento"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Cliente Objetivo
            if (isset($referencias[$x]["ClienteObjetivo_idClienteObjetivo"]) and ( $referencias[$x]["ClienteObjetivo_idClienteObjetivo"] == 0 and $referencias[$x]["codigoClienteObjetivo"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Cliente Objetivo (' . $referencias[$x]["codigoClienteObjetivo"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos el esquema de producto
            if (isset($referencias[$x]["EsquemaProducto_idEsquemaProducto"]) and ( $referencias[$x]["EsquemaProducto_idEsquemaProducto"] == 0 and $referencias[$x]["codigoEsquemaProducto"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Esquema de Producto(' . $referencias[$x]["codigoEsquemaProducto"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos País de Origen
            if ($referencias[$x]["Pais_idPaisOrigen"] == 0 and $referencias[$x]["codigoPais"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El País de Origen (' . $referencias[$x]["codigoPais"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Categoria
            if ($referencias[$x]["Categoria_idCategoria"] == 0 and $referencias[$x]["codigoCategoria"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La Categoria (' . $referencias[$x]["codigoCategoria"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Cliente
            if ($referencias[$x]["Tercero_idCliente"] == 0 and $referencias[$x]["codigoCliente"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Codigo Alterno o EAN del Cliente (' . $referencias[$x]["codigoCliente"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Proveedor
            if ($referencias[$x]["Tercero_idProveedor"] == 0 and $referencias[$x]["codigoProveedor"] != '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Codigo Alterno o EAN del Proveedor (' . $referencias[$x]["codigoProveedor"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Unidad de medida de compra
            if (isset($referencias[$x]["UnidadMedida_idCompra"]) and ( $referencias[$x]["UnidadMedida_idCompra"] == 0 and $referencias[$x]["codigoUnidadMedidaCompra"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La unidad de medida de compra (' . $referencias[$x]["codigoUnidadMedidaCompra"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos Unidad de medida de venta
            if (isset($referencias[$x]["UnidadMedida_idVenta"]) and ( $referencias[$x]["UnidadMedida_idVenta"] == 0 and $referencias[$x]["codigoUnidadMedidaVenta"] != '')) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'La unidad de medida de venta (' . $referencias[$x]["codigoUnidadMedidaVenta"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            if (isset($referencias[$x]["modeloProducto"]) and $referencias[$x]["modeloProducto"] == '') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'Debe digitar el modelo del Producto';
                $swerror = false;
                $linea++;
            }

            //            return $errores;

            if (isset($referencias[$x]["serialProducto"])) {
                if ($referencias[$x]["serialProducto"] != '') {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'Debe digitar la serie del producto';
                    $swerror = false;
                    $linea++;
                }
            }

            if (isset($referencias[$x]["tipoActivoProducto"])) {
                if ($referencias[$x]["tipoActivoProducto"] != '') {
                    if ($referencias[$x]["tipoActivoProducto"] != 'TANGIBLE' and $referencias[$x]["tipoActivoProducto"] != 'INTANGIBLE') {
                        $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                        $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                        $errores[$linea]["error"] = 'El tipo activo del Producto debe ser INTANGIBLE O TANGIBLE';
                        $swerror = false;
                        $linea++;
                    }
                }
            }

            //Validacion por tipo de Codigo de Barras
            //echo 'Tipo: '.$referencias[$x]["tipoCodigoBarrasProducto"];
            if ($referencias[$x]["tipoCodigoBarrasProducto"] != '' and $referencias[$x]["tipoCodigoBarrasProducto"] != 'EAN-8' and $referencias[$x]["tipoCodigoBarrasProducto"] != 'EAN-13' and $referencias[$x]["tipoCodigoBarrasProducto"] != 'UPC-8' and $referencias[$x]["tipoCodigoBarrasProducto"] != 'UPC-12' and $referencias[$x]["tipoCodigoBarrasProducto"] != 'ITF-14' and $referencias[$x]["tipoCodigoBarrasProducto"] != 'ESPECIFICO') {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                $errores[$linea]["error"] = 'El Tipo del C&oacute;digo de Barras ' . $referencias[$x]["tipoCodigoBarrasProducto"] . ' es incorrecto, debe ser EAN-8, EAN-13, UPC-8, UPC-12, ITF-14, ESPECIFICO o dejarlo vacio sino es necesario.';
                $swerror = false;
                $linea++;
            } else {
                if ($referencias[$x]["tipoCodigoBarrasProducto"] == 'EAN-8' or $referencias[$x]["tipoCodigoBarrasProducto"] == 'UPC-8' and strlen($codigoBarrasProducto) != 8) {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'La longitud del C&oacute;digo de Barras ' . $codigoBarrasProducto . ' es incorrecto, debe ser de 8 caracteres.';
                    $swerror = false;
                    $linea++;
                } else if ($referencias[$x]["tipoCodigoBarrasProducto"] == 'UPC-12' and strlen($codigoBarrasProducto) != 12) {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'La longitud del C&oacute;digo de Barras ' . $codigoBarrasProducto . ' es incorrecto, debe ser de 12 caracteres.';
                    $swerror = false;
                    $linea++;
                } else if ($referencias[$x]["tipoCodigoBarrasProducto"] == 'EAN-13' and strlen($codigoBarrasProducto) != 13) {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'La longitud del C&oacute;digo de Barras ' . $codigoBarrasProducto . ' es incorrecto, debe ser de 13 caracteres.';
                    $swerror = false;
                    $linea++;
                } else if ($referencias[$x]["tipoCodigoBarrasProducto"] == 'ITF-14' and strlen($codigoBarrasProducto) != 14) {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'La longitud del C&oacute;digo de Barras ' . $codigoBarrasProducto . ' es incorrecto, debe ser de 14 caracteres.';
                    $swerror = false;
                    $linea++;
                } else if ($referencias[$x]["tipoCodigoBarrasProducto"] == '' and strlen($codigoBarrasProducto) > 0) {
                    $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                    $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                    $errores[$linea]["error"] = 'Debe ingresar un Tipo de C&oacute;digo de Barras acorde a la longitud.';
                    $swerror = false;
                    $linea++;
                } else if ($referencias[$x]["tipoCodigoBarrasProducto"] != 'ESPECIFICO'){
                    $codigoEAN = $codigobarras->ValidarDigitoChequeo($codigoBarrasProducto);
                    if ($codigoEAN != $codigoBarrasProducto) {
                        $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                        $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
                        $errores[$linea]["error"] = 'El C&oacute;digo de Barras ' . $codigoBarrasProducto . ' digitado es incorrecto, debe ser ' . $codigoEAN . '.';
                        $swerror = false;
                        $linea++;
                    }
                }
            }

            return $errores;
        }

        function ImportarMaestroTercerosExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once('tercero.class.php');
            $tercero = new Tercero();
            require_once('tipoidentificacion.class.php');
            $tipoidentificacion = new TipoIdentificacion();
            require_once('ciudad.class.php');
            $ciudad = new Ciudad();
            require_once('listaprecio.class.php');
            $listaprecio = new ListaPrecio();
            require_once('clasificaciontercero.class.php');
            $clasificaciontercero = new ClasificacionTercero();
            require_once('formapago.class.php');
            $formapago = new FormaPago();
            require_once('centrocosto.class.php');
            $centrocosto = new CentroCosto();
            require_once('actividadeconomica.class.php');
            $actividadeconomica = new ActividadEconomica();
            require_once('zona.class.php');
            $zona = new Zona();
            require_once('macrocanal.class.php');
            $macrocanal = new MacroCanal();
            require_once 'clasificacionrenta.class.php';
            $clasificacionrenta = new ClasificacionRenta();
            require_once '../clases/naturalezajuridica.class.php';
            $naturalezajuridica = new NaturalezaJuridica();
            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }

            $objReader->setLoadSheetsOnly('tercero');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $terceros = array();
            $posTer = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != NULL) {
                // por cada numero de documento diferente, incrementamos el indice (empieza en cero)
                $posTer++;

                // para cada registro de terceros recorremos las columnas desde la 0 hasta la 73
                for ($columna = 0; $columna <= 114; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getDataType() == 'f')
                        $terceros[$posTer][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getCalculatedValue();
                    else
                        $terceros[$posTer][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }


                // cada que llenemos un tercero, hacemos las verificaciones de codigos necesarioos
                // verificamos cuales campos del tipo de tercero estan llenos para armar la codificacion de clasificacion
                $terceros[$posTer]["tipoTercero"] = '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["CLI"]) and $terceros[$posTer]["CLI"] != NULL) ? '*01*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["PRO"]) and $terceros[$posTer]["PRO"] != NULL) ? '*02*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["VEN"]) and $terceros[$posTer]["VEN"] != NULL) ? '*03*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["TRA"]) and $terceros[$posTer]["TRA"] != NULL) ? '*04*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["EMP"]) and $terceros[$posTer]["EMP"] != NULL) ? '*05*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["BAN"]) and $terceros[$posTer]["BAN"] != NULL) ? '*06*' : '';

                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["ASE"]) and $terceros[$posTer]["ASE"] != NULL) ? '*07*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["PROP"]) and $terceros[$posTer]["PROP"] != NULL) ? '*08*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["INQ"]) and $terceros[$posTer]["INQ"] != NULL) ? '*09*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["DEU"]) and $terceros[$posTer]["DEU"] != NULL) ? '*10*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["SAL"]) and $terceros[$posTer]["SAL"] != NULL) ? '*11*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["PEN"]) and $terceros[$posTer]["PEN"] != NULL) ? '*12*' : '';

                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["RIE"]) and $terceros[$posTer]["RIE"] != NULL) ? '*13*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["COM"]) and $terceros[$posTer]["COM"] != NULL) ? '*14*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["CES"]) and $terceros[$posTer]["CES"] != NULL) ? '*15*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["SOC"]) and $terceros[$posTer]["SOC"] != NULL) ? '*16*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["MIE"]) and $terceros[$posTer]["MIE"] != NULL) ? '*17*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["SUC"]) and $terceros[$posTer]["SUC"] != NULL) ? '*18*' : '';
                // este campo es auxiliar, nos sirve para saber si el el nit principal o es una sucursal

                $condicionSucursal = (!empty($terceros[$posTer]["SUC"]) and $terceros[$posTer]["SUC"] != NULL) ? "tipoTercero like '%*18*%'" : "tipoTercero not like '%*18*%'";

                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["SIA"]) and $terceros[$posTer]["SIA"] != NULL) ? '*19*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["POS"]) and $terceros[$posTer]["POS"] != NULL) ? '*20*' : '';

                // verificamos las columnas que se llenan con X, para cambiarlas por 1
                $terceros[$posTer]["esAgenteRetenedorTercero"] = (!empty($terceros[$posTer]["esAgenteRetenedorTercero"]) and $terceros[$posTer]["esAgenteRetenedorTercero"] != NULL ? 1 : 0);
                $terceros[$posTer]["esAutoretenedorCREETercero"] = (!empty($terceros[$posTer]["esAutoretenedorCREETercero"]) and $terceros[$posTer]["esAutoretenedorCREETercero"] != NULL ? 1 : 0);
                $terceros[$posTer]["esAutoretenedor"] = (!empty($terceros[$posTer]["esAutoretenedor"]) and $terceros[$posTer]["esAutoretenedor"] != NULL ? 1 : 0);
                $terceros[$posTer]["esGranContribuyente"] = (!empty($terceros[$posTer]["esGranContribuyente"]) and $terceros[$posTer]["esGranContribuyente"] != NULL ? 1 : 0);
                $terceros[$posTer]["esProveedorPermanenteTercero"] = (!empty($terceros[$posTer]["esProveedorPermanenteTercero"]) and $terceros[$posTer]["esProveedorPermanenteTercero"] != NULL ? 1 : 0);
                $terceros[$posTer]["esEntidadEstadoTercero"] = (!empty($terceros[$posTer]["esEntidadEstadoTercero"]) and $terceros[$posTer]["esEntidadEstadoTercero"] != NULL ? 1 : 0);



                $terceros[$posTer]["calcularRetencionFuenteProveedorSinBaseTercero"] = (!empty($terceros[$posTer]["calcularRetencionFuenteProveedorSinBaseTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionIvaProveedorSinBaseTercero"] = (!empty($terceros[$posTer]["calcularRetencionIvaProveedorSinBaseTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionIcaProveedorSinBaseTercero"] = (!empty($terceros[$posTer]["calcularRetencionIcaProveedorSinBaseTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionFuenteClienteSinBaseTercero"] = (!empty($terceros[$posTer]["calcularRetencionFuenteClienteSinBaseTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionIvaClienteSinBaseTercero"] = (!empty($terceros[$posTer]["calcularRetencionIvaClienteSinBaseTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionIcaClienteSinBaseTercero"] = (!empty($terceros[$posTer]["calcularRetencionIcaClienteSinBaseTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionCreeProveedorSinBaseTercero"] = (!empty($terceros[$posTer]["calcularRetencionCreeProveedorSinBaseTercero"]) ? 1 : 0);

                $terceros[$posTer]["calcularRetencionCreeClienteSinBaseTercero"] = (!empty($terceros[$posTer]["calcularRetencionCreeClienteSinBaseTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularIvaProveedorTercero"] = (!empty($terceros[$posTer]["calcularIvaProveedorTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularIvaClienteTercero"] = (!empty($terceros[$posTer]["calcularIvaClienteTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularImpoconsumoProveedorTercero"] = (!empty($terceros[$posTer]["calcularImpoconsumoProveedorTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularImpoconsumoClienteTercero"] = (!empty($terceros[$posTer]["calcularImpoconsumoClienteTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionFuenteClienteTercero"] = (!empty($terceros[$posTer]["calcularRetencionFuenteClienteTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionIvaClienteTercero"] = (!empty($terceros[$posTer]["calcularRetencionIvaClienteTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionIcaClienteTercero"] = (!empty($terceros[$posTer]["calcularRetencionIcaClienteTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionCreeClienteTercero"] = (!empty($terceros[$posTer]["calcularRetencionCreeClienteTercero"]) ? 1 : 0);
                $terceros[$posTer]["calcularRetencionCreeProveedorTercero"] = (!empty($terceros[$posTer]["calcularRetencionCreeProveedorTercero"]) ? 1 : 0);


                // consultamos el documento del tercero para obtener su id, en este caso como pueden existir varias sucursales con el mismo NIT,
                // verificamos que tambien coincida el campo TipoTercero con que tenga o no el identificador *18* (sucursal)
                //			echo $terceros[$posTer]["documentoTercero"].'hola0';
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["documentoTercero"]))
                    $tercero->ConsultarTercero("documentoTercero = '" . $terceros[$posTer]["documentoTercero"] . "' and codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlterno1Tercero"] . "'");
                $terceros[$posTer]["idTercero"] = $tercero->idTercero;

                //$terceros[$posTer]["digitoVerificacionTercero"] = $terceros[$posTer]["digitoVerificacionTercero"];
                //                         echo $terceros[$posTer]["idTercero"].'hola1';
                // Consultamos en la tabla de terceros, el codigo alterno o documento del vendedor asignado
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoVendedor"]))
                    $tercero->ConsultarTercero("(codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlternoVendedor"] . "' or documentoTercero = '" . $terceros[$posTer]["codigoAlternoVendedor"] . "') and tipoTercero like '%*03*%'");
                $terceros[$posTer]["Tercero_idVendedor"] = $tercero->idTercero;
                //			 echo 'hola2';
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoAsociado"]))
                    $tercero->ConsultarTercero("(codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlternoAsociado"] . "' or documentoTercero = '" . $terceros[$posTer]["codigoAlternoAsociado"] . "')");
                $terceros[$posTer]["Tercero_idAsociado"] = $tercero->idTercero;
                //                        echo $terceros[$posTer]["codigoIdentificacion"].'Hola';
                // consultamos el codigo de tipoidentificacion para obtener su id
                //                         echo 'hola3';
                $centrocosto->idCentroCosto = 0;
                if (!empty($terceros[$posTer]["codigoAlternoCentroCosto"]))
                //                            echo 'entra consultar';
                    $centrocosto->ConsultarCentroCosto("codigoAlternoCentroCosto =  '" . $terceros[$posTer]["codigoAlternoCentroCosto"] . "'");
                $terceros[$posTer]["CentroCosto_idCentroCosto"] = $centrocosto->idCentroCosto;


                //                        echo 'hola4';
                if (!empty($terceros[$posTer]["codigoAlternoMacroCanal"]))
                    $datos = $macrocanal->ConsultarVistaMacroCanal("codigoAlternoMacroCanal = '" . $terceros[$posTer]["codigoAlternoMacroCanal"] . "'");
                $terceros[$posTer]["MacroCanal_idMacroCanal"] = (isset($datos[0]["idMacroCanal"]) ? $datos[0]["idMacroCanal"] : 0);

                if (!empty($terceros[$posTer]["codigoAlternoZona"]))
                    $datos = $zona->ConsultarVistaZona("codigoAlternoZona = '" . $terceros[$posTer]["codigoAlternoZona"] . "'");
                $terceros[$posTer]["Zona_idZona"] = (isset($datos[0]["idZona"]) ? $datos[0]["idZona"] : 0);

                $tipoidentificacion->idIdentificacion = 0;
                if (!empty($terceros[$posTer]["codigoIdentificacion"]))
                    $tipoidentificacion->ConsultarIdentificacion("codigoIdentificacion = '" . $terceros[$posTer]["codigoIdentificacion"] . "'");


                $terceros[$posTer]["TipoIdentificacion_idIdentificacion"] = $tipoidentificacion->idIdentificacion;

                $naturalezajuridica->idNaturalezaJuridica = 0;
                if (!empty($terceros[$posTer]["codigoAlternoNaturalezaJuridica"]))
                    $naturalezajuridica->ConsultarNaturalezaJuridica("codigoAlternoNaturalezaJuridica = '" . $terceros[$posTer]["codigoAlternoNaturalezaJuridica"] . "'");

                $terceros[$posTer]["NaturalezaJuridica_idNaturalezaJuridica"] = $naturalezajuridica->idNaturalezaJuridica;
                //                         echo 'hola5';
                // consultamos la ciudad del tercero
                $ciudad->idCiudad = 0;
                if (!empty($terceros[$posTer]["codigoAlternoCiudad"]))
                    $ciudad->ConsultarCiudad("codigoAlternoCiudad = '" . $terceros[$posTer]["codigoAlternoCiudad"] . "'");
                $terceros[$posTer]["Ciudad_idCiudad"] = $ciudad->idCiudad;


                //                         echo 'hola6';
                // consultamos la ciudad del tercero
                /* $listaprecio->idListaPrecio = 0;
                  if (!empty($terceros[$posTer]["codigoAlternoListaPrecio"]))
                  $listaprecio->ConsultarListaPrecio("codigoAlternoListaPrecio = '" . $terceros[$posTer]["codigoAlternoListaPrecio"] . "'");
                  $terceros[$posTer]["ListaPrecio_idListaPrecio"] = $listaprecio->idListaPrecio;
                 */
                // consultamos la clasificacion del tercero

                if (!empty($terceros[$posTer]["codigoAlternoClasificacionRenta"]))
                    $datosRenta = $clasificacionrenta->ConsultarVistaClasificacionRenta("codigoAlternoClasificacionRenta = '" . $terceros[$posTer]["codigoAlternoClasificacionRenta"] . "'");
                $terceros[$posTer]["ClasificacionRenta_idClasificacionRenta"] = isset($datosRenta[0]['idClasificacionRenta']) ? $datosRenta[0]['idClasificacionRenta'] : 0;

                //                         echo 'hola7';
                $clasificaciontercero->idClasificacionTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoClasificacionTercero"]))
                    $clasificaciontercero->ConsultarClasificacionTercero("codigoAlternoClasificacionTercero = '" . $terceros[$posTer]["codigoAlternoClasificacionTercero"] . "'");
                $terceros[$posTer]["ClasificacionTercero_idClasificacionTercero"] = $clasificaciontercero->idClasificacionTercero;
                // echo 'hola8';
                // consultamos la forma de pago
                $formapago->idFormaPago = 0;
                if (!empty($terceros[$posTer]["codigoAlternoFormaPago"]))
                    $formapago->ConsultarFormaPago("codigoAlternoFormaPago = '" . $terceros[$posTer]["codigoAlternoFormaPago"] . "'");
                $terceros[$posTer]["FormaPago_idFormaPago"] = $formapago->idFormaPago;
                // echo 'hola9';
                // consultamos la forma de pago
                $actividadeconomica->idActividadEconomica = 0;
                if (!empty($terceros[$posTer]["codigoAlternoActividadEconomica"]))
                    $actividadeconomica->ConsultarActividadEconomica("codigoAlternoActividadEconomica = '" . $terceros[$posTer]["codigoAlternoActividadEconomica"] . "'");
                $terceros[$posTer]["ActividadEconomica_idActividadEconomica"] = $actividadeconomica->idActividadEconomica;
                // echo 'hola10';

                $fila++;
                //                        echo $fila;
                //                        echo 'hola 11';
                //print_r($terceros);
            }

            //
            //		print_r($terceros);
            //                return;
            // luego de que tenemos la matriz de terceros llena, las enviamos al proceso de importacion de terceros
            // para que los valide e importe al sistema
            $retorno = $this->llenarPropiedadesTercero($terceros);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($terceros);

            $this->eliminarArchivo($ruta);

            return $retorno;
        }

        function llenarPropiedadesTercero($terceros) {


            // instanciamos la clase producto y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'tercero.class.php';
            $tercero = new Tercero();

            $retorno = array();
            // contamos los registros del array de terceros
            $totalreg = (isset($terceros[0]["documentoTercero"]) ? count($terceros) : 0);
            for ($i = 0; $i < $totalreg; $i++) {
                $newerrors = $this->validarTipoDatoTercero($i, $terceros);
                if (!isset($newerrors[0]["error"])) {
                    $nuevoserrores = $this->validarTerceros($terceros[$i]["codigoAlterno1Tercero"], $i, $terceros);
                    $totalerr = count($nuevoserrores);

                    if (!isset($nuevoserrores[0]["error"])) {
                        // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrays
                        $tercero->Tercero();

                        // Identificacion
                        $tercero->idTercero = (isset($terceros[$i]["idTercero"]) ? $terceros[$i]["idTercero"] : 0);
                        $tercero->TipoIdentificacion_idIdentificacion = (isset($terceros[$i]["TipoIdentificacion_idIdentificacion"]) ? $terceros[$i]["TipoIdentificacion_idIdentificacion"] : 0);
                        $tercero->documentoTercero = (isset($terceros[$i]["documentoTercero"]) ? $terceros[$i]["documentoTercero"] : '');
                        $tercero->digitoVerificacionTercero = (isset($terceros[$i]["digitoVerificacionTercero"]) ? $terceros[$i]["digitoVerificacionTercero"] : '');
                        $tercero->codigoAlterno1Tercero = (isset($terceros[$i]["codigoAlterno1Tercero"]) ? $terceros[$i]["codigoAlterno1Tercero"] : '');
                        $tercero->codigoAlterno2Tercero = (isset($terceros[$i]["codigoAlterno2Tercero"]) ? $terceros[$i]["codigoAlterno2Tercero"] : '');
                        $tercero->codigoBarrasTercero = (isset($terceros[$i]["codigoBarrasTercero"]) ? $terceros[$i]["codigoBarrasTercero"] : '');
                        $tercero->codigoPostalTercero = (isset($terceros[$i]["codigoPostalTercero"]) ? $terceros[$i]["codigoPostalTercero"] : '');
                        $tercero->nombreATercero = (isset($terceros[$i]["nombreATercero"]) ? $terceros[$i]["nombreATercero"] : '');
                        $tercero->nombreBTercero = (isset($terceros[$i]["nombreBTercero"]) ? $terceros[$i]["nombreBTercero"] : '');
                        $tercero->apellidoATercero = (isset($terceros[$i]["apellidoATercero"]) ? $terceros[$i]["apellidoATercero"] : '');
                        $tercero->apellidoBTercero = (isset($terceros[$i]["apellidoBTercero"]) ? $terceros[$i]["apellidoBTercero"] : '');

                        $tercero->nombre1Tercero = (isset($terceros[$i]["nombre1Tercero"]) ? $terceros[$i]["nombre1Tercero"] : '');
                        $tercero->nombre2Tercero = (isset($terceros[$i]["nombre2Tercero"]) ? $terceros[$i]["nombre2Tercero"] : '');
                        $tercero->tipoTercero = (isset($terceros[$i]["tipoTercero"]) ? $terceros[$i]["tipoTercero"] : '*01*');

                        // Datos Generales
                        $tercero->imagenTercero = (isset($terceros[$i]["imagenTercero"]) ? $terceros[$i]["imagenTercero"] : '');
                        $tercero->sexoTercero = (isset($terceros[$i]["sexoTercero"]) ? $terceros[$i]["sexoTercero"] : '');
                        $tercero->direccionTercero = (isset($terceros[$i]["direccionTercero"]) ? $terceros[$i]["direccionTercero"] : '');
                        $tercero->tipoVia1Tercero = (isset($terceros[$i]["tipoVia1Tercero"]) ? $terceros[$i]["tipoVia1Tercero"] : '');
                        $tercero->numeroVia1Tercero = (isset($terceros[$i]["numeroVia1Tercero"]) ? $terceros[$i]["numeroVia1Tercero"] : '');
                        $tercero->apendice1Tercero = (isset($terceros[$i]["apendice1Tercero"]) ? $terceros[$i]["apendice1Tercero"] : '');
                        $tercero->cardinalidad1Tercero = (isset($terceros[$i]["cardinalidad1Tercero"]) ? $terceros[$i]["cardinalidad1Tercero"] : '');
                        $tercero->tipoVia2Tercero = (isset($terceros[$i]["tipoVia2Tercero"]) ? $terceros[$i]["tipoVia2Tercero"] : '');
                        $tercero->numeroVia2Tercero = (isset($terceros[$i]["numeroVia2Tercero"]) ? $terceros[$i]["numeroVia2Tercero"] : '');
                        $tercero->apendice2Tercero = (isset($terceros[$i]["apendice2Tercero"]) ? $terceros[$i]["apendice2Tercero"] : '');
                        $tercero->cardinalidad2Tercero = (isset($terceros[$i]["cardinalidad2Tercero"]) ? $terceros[$i]["cardinalidad2Tercero"] : '');
                        $tercero->numeroPlacaTercero = (isset($terceros[$i]["numeroPlacaTercero"]) ? $terceros[$i]["numeroPlacaTercero"] : '');
                        $tercero->complementoDireccionTercero = (isset($terceros[$i]["complementoDireccionTercero"]) ? $terceros[$i]["complementoDireccionTercero"] : '');

                        $tercero->direccionRutTercero = (isset($terceros[$i]["direccionRutTercero"]) ? $terceros[$i]["direccionRutTercero"] : '');

                        $tercero->tipoVia1RutTercero = (isset($terceros[$i]["tipoVia1RutTercero"]) ? $terceros[$i]["tipoVia1RutTercero"] : '');
                        $tercero->numeroVia1RutTercero = (isset($terceros[$i]["numeroVia1RutTercero"]) ? $terceros[$i]["numeroVia1RutTercero"] : '');
                        $tercero->apendice1RutTercero = (isset($terceros[$i]["apendice1RutTercero"]) ? $terceros[$i]["apendice1RutTercero"] : '');
                        $tercero->cardinalidad1RutTercero = (isset($terceros[$i]["cardinalidad1RutTercero"]) ? $terceros[$i]["cardinalidad1RutTercero"] : '');
                        $tercero->tipoVia2RutTercero = (isset($terceros[$i]["tipoVia2RutTercero"]) ? $terceros[$i]["tipoVia2RutTercero"] : '');
                        $tercero->numeroVia2RutTercero = (isset($terceros[$i]["numeroVia2RutTercero"]) ? $terceros[$i]["numeroVia2RutTercero"] : '');
                        $tercero->apendice2RutTercero = (isset($terceros[$i]["apendice2RutTercero"]) ? $terceros[$i]["apendice2RutTercero"] : '');
                        $tercero->cardinalidad2RutTercero = (isset($terceros[$i]["cardinalidad2RutTercero"]) ? $terceros[$i]["cardinalidad2RutTercero"] : '');
                        $tercero->numeroPlacaRutTercero = (isset($terceros[$i]["numeroPlacaRutTercero"]) ? $terceros[$i]["numeroPlacaRutTercero"] : '');
                        $tercero->complementoDireccionRutTercero = (isset($terceros[$i]["complementoDireccionRutTercero"]) ? $terceros[$i]["complementoDireccionRutTercero"] : '');


                        $tercero->Ciudad_idCiudad = (isset($terceros[$i]["Ciudad_idCiudad"]) ? $terceros[$i]["Ciudad_idCiudad"] : 0);
                        $tercero->telefono1Tercero = (isset($terceros[$i]["telefono1Tercero"]) ? $terceros[$i]["telefono1Tercero"] : '');
                        $tercero->telefono2Tercero = (isset($terceros[$i]["telefono2Tercero"]) ? $terceros[$i]["telefono2Tercero"] : '');
                        $tercero->movilTercero = (isset($terceros[$i]["movilTercero"]) ? $terceros[$i]["movilTercero"] : '');
                        $tercero->faxTercero = (isset($terceros[$i]["faxTercero"]) ? $terceros[$i]["faxTercero"] : '');
                        $tercero->correoElectronicoTercero = (isset($terceros[$i]["correoElectronicoTercero"]) ? $terceros[$i]["correoElectronicoTercero"] : '');
                        $tercero->paginaWebTercero = (isset($terceros[$i]["paginaWebTercero"]) ? $terceros[$i]["paginaWebTercero"] : '');
                        $tercero->fechaNacimientoTercero = (isset($terceros[$i]["fechaNacimientoTercero"]) ? $terceros[$i]["fechaNacimientoTercero"] : '');
                        $tercero->fechaCreacionTercero = (isset($terceros[$i]["fechaCreacionTercero"]) ? $terceros[$i]["fechaCreacionTercero"] : '');
                        $tercero->longitudSSCCTercero = (isset($terceros[$i]["longitudSSCCTercero"]) ? $terceros[$i]["longitudSSCCTercero"] : 0);
                        $tercero->estadoTercero = (isset($terceros[$i]["estadoTercero"]) ? $terceros[$i]["estadoTercero"] : 'ACTIVO');



                        //                                echo $terceros[$i]["Tercero_idVendedor"].'<br>';
                        // Informacion Comercial
                        //$tercero->ListaPrecio_idListaPrecio = (isset($terceros[$i]["ListaPrecio_idListaPrecio"]) ? $terceros[$i]["ListaPrecio_idListaPrecio"] : 0);
                        $tercero->Tercero_idVendedor = (isset($terceros[$i]["Tercero_idVendedor"]) ? $terceros[$i]["Tercero_idVendedor"] : '');
                        $tercero->Tercero_idAsociado = (isset($terceros[$i]["Tercero_idAsociado"]) ? $terceros[$i]["Tercero_idAsociado"] : '');
                        $tercero->CentroCosto_idCentroCosto = (isset($terceros[$i]["CentroCosto_idCentroCosto"]) ? $terceros[$i]["CentroCosto_idCentroCosto"] : '');
                        $tercero->ClasificacionTercero_idClasificacionTercero = (isset($terceros[$i]["ClasificacionTercero_idClasificacionTercero"]) ? $terceros[$i]["ClasificacionTercero_idClasificacionTercero"] : '');
                        $tercero->cupoTercero = (isset($terceros[$i]["cupoTercero"]) ? $terceros[$i]["cupoTercero"] : 0);
                        $tercero->presupuestoAnoTercero = (isset($terceros[$i]["presupuestoAnoTercero"]) ? $terceros[$i]["presupuestoAnoTercero"] : 0);
                        //$tercero->porcentajeDescuentoComercialTercero = (isset($terceros[$i]["porcentajeDescuentoComercialTercero"]) ? $terceros[$i]["porcentajeDescuentoComercialTercero"] : 0);
                        //$tercero->porcentajeDescuentoFinancieroTercero = (isset($terceros[$i]["porcentajeDescuentoFinancieroTercero"]) ? $terceros[$i]["porcentajeDescuentoFinancieroTercero"] : 0);
                        $tercero->FormaPago_idFormaPago = (isset($terceros[$i]["FormaPago_idFormaPago"]) ? $terceros[$i]["FormaPago_idFormaPago"] : 0);

                        // Informacion Tributaria
                        $tercero->esAgenteRetenedorTercero = (isset($terceros[$i]["esAgenteRetenedorTercero"]) ? $terceros[$i]["esAgenteRetenedorTercero"] : 0);
                        $tercero->esAutoretenedor = (isset($terceros[$i]["esAutoretenedor"]) ? $terceros[$i]["esAutoretenedor"] : 0);
                        $tercero->regimenVentasTercero = (isset($terceros[$i]["regimenVentasTercero"]) ? $terceros[$i]["regimenVentasTercero"] : 0);
                        $tercero->esGranContribuyente = (isset($terceros[$i]["esGranContribuyente"]) ? $terceros[$i]["esGranContribuyente"] : 0);
                        $tercero->ActividadEconomica_idActividadEconomica = (isset($terceros[$i]["ActividadEconomica_idActividadEconomica"]) ? $terceros[$i]["ActividadEconomica_idActividadEconomica"] : 0);
                        $tercero->esProveedorPermanenteTercero = (isset($terceros[$i]["esProveedorPermanenteTercero"]) ? $terceros[$i]["esProveedorPermanenteTercero"] : 0);
                        $tercero->esEntidadEstadoTercero = (isset($terceros[$i]["esEntidadEstadoTercero"]) ? $terceros[$i]["esEntidadEstadoTercero"] : 0);

                        // Informacion Laboral (importacion de empleados)
                        $tercero->GrupoNomina_idGrupoNomina = (isset($terceros[$i]["GrupoNomina_idGrupoNomina"]) ? $terceros[$i]["GrupoNomina_idGrupoNomina"] : 0);
                        $tercero->estadoCivilTercero = (isset($terceros[$i]["estadoCivilTercero"]) ? $terceros[$i]["estadoCivilTercero"] : 'Soltero');
                        $tercero->tipoSalarioTercero = (isset($terceros[$i]["tipoSalarioTercero"]) ? $terceros[$i]["tipoSalarioTercero"] : 'Fijo');
                        $tercero->salarioBasicoTercero = (isset($terceros[$i]["salarioBasicoTercero"]) ? $terceros[$i]["salarioBasicoTercero"] : 0);
                        $tercero->esSalarioMinimoTercero = (isset($terceros[$i]["esSalarioMinimoTercero"]) ? $terceros[$i]["esSalarioMinimoTercero"] : 0);
                        $tercero->registraTurnoTercero = (isset($terceros[$i]["registraTurnoTercero"]) ? $terceros[$i]["registraTurnoTercero"] : 0);
                        $tercero->Turno_idTurno = (isset($terceros[$i]["Turno_idTurno"]) ? $terceros[$i]["Turno_idTurno"] : 0);
                        $tercero->periodoLiquidacionTercero = (isset($terceros[$i]["periodoLiquidacionTercero"]) ? $terceros[$i]["periodoLiquidacionTercero"] : 'Quincenal');
                        $tercero->ingresoFamiliarTercero = (isset($terceros[$i]["ingresoFamiliarTercero"]) ? $terceros[$i]["ingresoFamiliarTercero"] : 0);
                        $tercero->sexoTercero = (isset($terceros[$i]["sexoTercero"]) ? $terceros[$i]["sexoTercero"] : 'Masculino');
                        $tercero->grupoSanguineoTercero = (isset($terceros[$i]["grupoSanguineoTercero"]) ? $terceros[$i]["grupoSanguineoTercero"] : '');
                        $tercero->factorRHTercero = (isset($terceros[$i]["factorRHTercero"]) ? $terceros[$i]["factorRHTercero"] : '');
                        $tercero->Cargo_idCargo = (isset($terceros[$i]["Cargo_idCargo"]) ? $terceros[$i]["Cargo_idCargo"] : 0);
                        $tercero->CentroCosto_idCentroCosto = (isset($terceros[$i]["CentroCosto_idCentroCosto"]) ? $terceros[$i]["CentroCosto_idCentroCosto"] : 0);
                        $tercero->CentroTrabajo_idCentroTrabajo = (isset($terceros[$i]["CentroTrabajo_idCentroTrabajo"]) ? $terceros[$i]["CentroTrabajo_idCentroTrabajo"] : 0);
                        $tercero->fechaNacimientoTercero = (isset($terceros[$i]["fechaNacimientoTercero"]) ? $terceros[$i]["fechaNacimientoTercero"] : '');
                        $tercero->Ciudad_idLugarNacimiento = (isset($terceros[$i]["Ciudad_idLugarNacimiento"]) ? $terceros[$i]["Ciudad_idLugarNacimiento"] : 0);
                        $tercero->Tercero_idSalud = (isset($terceros[$i]["Tercero_idSalud"]) ? $terceros[$i]["Tercero_idSalud"] : 0);
                        $tercero->periodicidadSaludTercero = (isset($terceros[$i]["periodicidadSaludTercero"]) ? $terceros[$i]["periodicidadSaludTercero"] : 'PERIODICO');
                        $tercero->valorSaludUPCTercero = (isset($terceros[$i]["valorSaludUPCTercero"]) ? $terceros[$i]["valorSaludUPCTercero"] : 0);
                        $tercero->Tercero_idPension = (isset($terceros[$i]["Tercero_idPension"]) ? $terceros[$i]["Tercero_idPension"] : 0);
                        $tercero->periodicidadPensionTercero = (isset($terceros[$i]["periodicidadPensionTercero"]) ? $terceros[$i]["periodicidadPensionTercero"] : 'PERIODICO');
                        $tercero->valorAporteVoluntarioPensionTercero = (isset($terceros[$i]["valorAporteVoluntarioPensionTercero"]) ? $terceros[$i]["valorAporteVoluntarioPensionTercero"] : 0);
                        $tercero->Tercero_idCesantias = (isset($terceros[$i]["Tercero_idCesantias"]) ? $terceros[$i]["Tercero_idCesantias"] : 0);
                        $tercero->leyCesantiasTercero = (isset($terceros[$i]["leyCesantiasTercero"]) ? $terceros[$i]["leyCesantiasTercero"] : 'ACTUAL');
                        $tercero->jornadaLaboralDiaTercero = (isset($terceros[$i]["jornadaLaboralDiaTercero"]) ? $terceros[$i]["jornadaLaboralDiaTercero"] : 0);
                        $tercero->Barrio_idBarrio = (isset($terceros[$i]["Barrio_idBarrio"]) ? $terceros[$i]["Barrio_idBarrio"] : 0);
                        $tercero->numeroHijosTercero = (isset($terceros[$i]["numeroHijosTercero"]) ? $terceros[$i]["numeroHijosTercero"] : 0);
                        $tercero->Tercero_idARP = (isset($terceros[$i]["Tercero_idARP"]) ? $terceros[$i]["Tercero_idARP"] : 0);
                        $tercero->Tercero_idCajaCompensacion = (isset($terceros[$i]["Tercero_idCajaCompensacion"]) ? $terceros[$i]["Tercero_idCajaCompensacion"] : 0);
                        $tercero->MacroCanal_idMacroCanal = (isset($terceros[$i]["MacroCanal_idMacroCanal"]) ? $terceros[$i]["MacroCanal_idMacroCanal"] : 0);
                        $tercero->Zona_idZona = (isset($terceros[$i]["Zona_idZona"]) ? $terceros[$i]["Zona_idZona"] : 0);
                        $tercero->Tercero_idSucursal = (isset($terceros[$i]["Tercero_idSucursal"]) ? $terceros[$i]["Tercero_idSucursal"] : 0);


                        $tercero->esAutoretenedorCREETercero = (isset($terceros[$i]["esAutoretenedorCREETercero"]) ? $terceros[$i]["esAutoretenedorCREETercero"] : 0);
                        $tercero->noResponsableIVATercero = (isset($terceros[$i]["noResponsableIVATercero"]) ? $terceros[$i]["noResponsableIVATercero"] : 0);
                        $tercero->fechaFinBeneficio1429Tercero = (isset($terceros[$i]["fechaFinBeneficio1429Tercero"]) ? $terceros[$i]["fechaFinBeneficio1429Tercero"] : 0);

                        $tercero->ClasificacionRenta_idClasificacionRenta = (isset($terceros[$i]["ClasificacionRenta_idClasificacionRenta"]) ? $terceros[$i]["ClasificacionRenta_idClasificacionRenta"] : 0);

                        $tercero->calcularRetencionFuenteProveedorSinBaseTercero = (isset($terceros[$i]["calcularRetencionFuenteProveedorSinBaseTercero"]) ? $terceros[$i]["calcularRetencionFuenteProveedorSinBaseTercero"] : 0);
                        $tercero->calcularRetencionIvaProveedorSinBaseTercero = (isset($terceros[$i]["calcularRetencionIvaProveedorSinBaseTercero"]) ? $terceros[$i]["calcularRetencionIvaProveedorSinBaseTercero"] : 0);
                        $tercero->calcularRetencionIcaProveedorSinBaseTercero = (isset($terceros[$i]["calcularRetencionIcaProveedorSinBaseTercero"]) ? $terceros[$i]["calcularRetencionIcaProveedorSinBaseTercero"] : 0);
                        $tercero->calcularRetencionFuenteClienteSinBaseTercero = (isset($terceros[$i]["calcularRetencionFuenteClienteSinBaseTercero"]) ? $terceros[$i]["calcularRetencionFuenteClienteSinBaseTercero"] : 0);
                        $tercero->calcularRetencionIvaClienteSinBaseTercero = (isset($terceros[$i]["calcularRetencionIvaClienteSinBaseTercero"]) ? $terceros[$i]["calcularRetencionIvaClienteSinBaseTercero"] : 0);
                        $tercero->calcularRetencionIcaClienteSinBaseTercero = (isset($terceros[$i]["calcularRetencionIcaClienteSinBaseTercero"]) ? $terceros[$i]["calcularRetencionIcaClienteSinBaseTercero"] : 0);
                        $tercero->calcularRetencionCreeProveedorSinBaseTercero = (isset($terceros[$i]["calcularRetencionCreeProveedorSinBaseTercero"]) ? $terceros[$i]["calcularRetencionCreeProveedorSinBaseTercero"] : 0);
                        $tercero->calcularRetencionCreeClienteSinBaseTercero = (isset($terceros[$i]["calcularRetencionCreeClienteSinBaseTercero"]) ? $terceros[$i]["calcularRetencionCreeClienteSinBaseTercero"] : 0);
                        $tercero->calcularIvaProveedorTercero = (isset($terceros[$i]["calcularIvaProveedorTercero"]) ? $terceros[$i]["calcularIvaProveedorTercero"] : 0);
                        $tercero->calcularIvaClienteTercero = (isset($terceros[$i]["calcularIvaClienteTercero"]) ? $terceros[$i]["calcularIvaClienteTercero"] : 0);
                        $tercero->calcularImpoconsumoProveedorTercero = (isset($terceros[$i]["calcularImpoconsumoProveedorTercero"]) ? $terceros[$i]["calcularImpoconsumoProveedorTercero"] : 0);
                        $tercero->calcularImpoconsumoClienteTercero = (isset($terceros[$i]["calcularImpoconsumoClienteTercero"]) ? $terceros[$i]["calcularImpoconsumoClienteTercero"] : 0);
                        $tercero->calcularRetencionFuenteClienteTercero = (isset($terceros[$i]["calcularRetencionFuenteClienteTercero"]) ? $terceros[$i]["calcularRetencionFuenteClienteTercero"] : 0);
                        $tercero->calcularRetencionIvaClienteTercero = (isset($terceros[$i]["calcularRetencionIvaClienteTercero"]) ? $terceros[$i]["calcularRetencionIvaClienteTercero"] : 0);
                        $tercero->calcularRetencionIcaClienteTercero = (isset($terceros[$i]["calcularRetencionIcaClienteTercero"]) ? $terceros[$i]["calcularRetencionIcaClienteTercero"] : 0);
                        $tercero->calcularRetencionCreeClienteTercero = (isset($terceros[$i]["calcularRetencionCreeClienteTercero"]) ? $terceros[$i]["calcularRetencionCreeClienteTercero"] : 0);

                        $tercero->calcularRetencionCreeProveedorTercero = (isset($terceros[$i]["calcularRetencionCreeProveedorTercero"]) ? $terceros[$i]["calcularRetencionCreeProveedorTercero"] : 0);
                        $tercero->resolucionDIANTercero = (isset($terceros[$i]["resolucionDIANTercero"]) ? $terceros[$i]["resolucionDIANTercero"] : 0);
                        $tercero->modalidadFacturacionTercero = (isset($terceros[$i]["modalidadFacturacionTercero"]) ? $terceros[$i]["modalidadFacturacionTercero"] : 0);
                        $tercero->tipoSolicitudFacturacionTercero = (isset($terceros[$i]["tipoSolicitudFacturacionTercero"]) ? $terceros[$i]["tipoSolicitudFacturacionTercero"] : 0);
                        $tercero->fechaResolucionTercero = (isset($terceros[$i]["fechaResolucionTercero"]) ? $terceros[$i]["fechaResolucionTercero"] : 0);
                        $tercero->prefijoResolucionTercero = (isset($terceros[$i]["prefijoResolucionTercero"]) ? $terceros[$i]["prefijoResolucionTercero"] : 0);
                        $tercero->sufijoResolucionTercero = (isset($terceros[$i]["sufijoResolucionTercero"]) ? $terceros[$i]["sufijoResolucionTercero"] : 0);
                        $tercero->numeroInicialResolucionTercero = (isset($terceros[$i]["numeroInicialResolucionTercero"]) ? $terceros[$i]["numeroInicialResolucionTercero"] : 0);
                        $tercero->numeroFinalResolucionTercero = (isset($terceros[$i]["numeroFinalResolucionTercero"]) ? $terceros[$i]["numeroFinalResolucionTercero"] : 0);

                        $tercero->puntosTercero = (isset($terceros[$i]["puntosTercero"]) ? $terceros[$i]["puntosTercero"] : 0);

                        $tercero->NaturalezaJuridica_idNaturalezaJuridica = (isset($terceros[$i]["NaturalezaJuridica_idNaturalezaJuridica"]) ? $terceros[$i]["NaturalezaJuridica_idNaturalezaJuridica"] : 0);

                        // cada que llenamos un producto, lo cargamos a la base de datos
                        // si el id esta lleno, lo actualizamos, si esta vacio lo insertamos
                        if ($terceros[$i]['idTercero'] == 0) {


                            $tercero->AdicionarTercero();
                        } else {

                            $tercero->ModificarExcelTercero();
                        }
                    } else {
                        $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                    }
                } else {
                    $retorno = array_merge((array) $retorno, (array) $newerrors);
                }
            }

            return $retorno;
        }

        function llenarPropiedadesEmpleados($terceros) {


            // instanciamos la clase producto y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'tercero.class.php';
            $tercero = new Tercero();

            $retorno = array();
            // contamos los registros del array de terceros
            $totalreg = (isset($terceros[0]["documentoTercero"]) ? count($terceros) : 0);
            for ($i = 0; $i < $totalreg; $i++) {

                $newerrors = $this->validarTipoDatoTercero($i, $terceros);
                if (!isset($newerrors[0]["error"])) {

                    $nuevoserrores = $this->validarTerceros($terceros[$i]["documentoTercero"], $i, $terceros);
                    $totalerr = count($nuevoserrores);

                    if (!isset($nuevoserrores[0]["error"])) {
                        // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrays
                        $tercero->Tercero();

                        // Identificacion
                        $tercero->idTercero = (isset($terceros[$i]["idTercero"]) ? $terceros[$i]["idTercero"] : 0);
                        $tercero->TipoIdentificacion_idIdentificacion = (isset($terceros[$i]["TipoIdentificacion_idIdentificacion"]) ? $terceros[$i]["TipoIdentificacion_idIdentificacion"] : 0);
                        $tercero->documentoTercero = (isset($terceros[$i]["documentoTercero"]) ? $terceros[$i]["documentoTercero"] : '');
                        $tercero->digitoVerificacionTercero = (isset($terceros[$i]["digitoVerificacionTercero"]) ? $terceros[$i]["digitoVerificacionTercero"] : '');
                        $tercero->codigoAlterno1Tercero = (isset($terceros[$i]["codigoAlterno1Tercero"]) ? $terceros[$i]["codigoAlterno1Tercero"] : '');
                        $tercero->codigoAlterno2Tercero = (isset($terceros[$i]["codigoAlterno2Tercero"]) ? $terceros[$i]["codigoAlterno2Tercero"] : '');
                        $tercero->codigoBarrasTercero = (isset($terceros[$i]["codigoBarrasTercero"]) ? $terceros[$i]["codigoBarrasTercero"] : '');
                        $tercero->codigoPostalTercero = (isset($terceros[$i]["codigoPostalTercero"]) ? $terceros[$i]["codigoPostalTercero"] : '');
                        $tercero->nombreATercero = (isset($terceros[$i]["nombreATercero"]) ? $terceros[$i]["nombreATercero"] : '');
                        $tercero->nombreBTercero = (isset($terceros[$i]["nombreBTercero"]) ? $terceros[$i]["nombreBTercero"] : '');
                        $tercero->apellidoATercero = (isset($terceros[$i]["apellidoATercero"]) ? $terceros[$i]["apellidoATercero"] : '');
                        $tercero->apellidoBTercero = (isset($terceros[$i]["apellidoBTercero"]) ? $terceros[$i]["apellidoBTercero"] : '');

                        $tercero->nombre1Tercero = (isset($terceros[$i]["nombre1Tercero"]) ? $terceros[$i]["nombre1Tercero"] : '');
                        $tercero->nombre2Tercero = (isset($terceros[$i]["nombre2Tercero"]) ? $terceros[$i]["nombre2Tercero"] : '');
                        $tercero->tipoTercero = (isset($terceros[$i]["tipoTercero"]) ? $terceros[$i]["tipoTercero"] : '*01*');

                        // Datos Generales
                        $tercero->imagenTercero = (isset($terceros[$i]["imagenTercero"]) ? $terceros[$i]["imagenTercero"] : '');
                        $tercero->sexoTercero = (isset($terceros[$i]["sexoTercero"]) ? $terceros[$i]["sexoTercero"] : '');
                        $tercero->direccionTercero = (isset($terceros[$i]["direccionTercero"]) ? $terceros[$i]["direccionTercero"] : '');
                        $tercero->tipoVia1Tercero = (isset($terceros[$i]["tipoVia1Tercero"]) ? $terceros[$i]["tipoVia1Tercero"] : '');
                        $tercero->numeroVia1Tercero = (isset($terceros[$i]["numeroVia1Tercero"]) ? $terceros[$i]["numeroVia1Tercero"] : '');
                        $tercero->apendice1Tercero = (isset($terceros[$i]["apendice1Tercero"]) ? $terceros[$i]["apendice1Tercero"] : '');
                        $tercero->cardinalidad1Tercero = (isset($terceros[$i]["cardinalidad1Tercero"]) ? $terceros[$i]["cardinalidad1Tercero"] : '');
                        $tercero->tipoVia2Tercero = (isset($terceros[$i]["tipoVia2Tercero"]) ? $terceros[$i]["tipoVia2Tercero"] : '');
                        $tercero->numeroVia2Tercero = (isset($terceros[$i]["numeroVia2Tercero"]) ? $terceros[$i]["numeroVia2Tercero"] : '');
                        $tercero->apendice2Tercero = (isset($terceros[$i]["apendice2Tercero"]) ? $terceros[$i]["apendice2Tercero"] : '');
                        $tercero->cardinalidad2Tercero = (isset($terceros[$i]["cardinalidad2Tercero"]) ? $terceros[$i]["cardinalidad2Tercero"] : '');
                        $tercero->numeroPlacaTercero = (isset($terceros[$i]["numeroPlacaTercero"]) ? $terceros[$i]["numeroPlacaTercero"] : '');
                        $tercero->complementoDireccionTercero = (isset($terceros[$i]["complementoDireccionTercero"]) ? $terceros[$i]["complementoDireccionTercero"] : '');

                        $tercero->direccionRutTercero = (isset($terceros[$i]["direccionRutTercero"]) ? $terceros[$i]["direccionRutTercero"] : '');

                        $tercero->tipoVia1RutTercero = (isset($terceros[$i]["tipoVia1RutTercero"]) ? $terceros[$i]["tipoVia1RutTercero"] : '');
                        $tercero->numeroVia1RutTercero = (isset($terceros[$i]["numeroVia1RutTercero"]) ? $terceros[$i]["numeroVia1RutTercero"] : '');
                        $tercero->apendice1RutTercero = (isset($terceros[$i]["apendice1RutTercero"]) ? $terceros[$i]["apendice1RutTercero"] : '');
                        $tercero->cardinalidad1RutTercero = (isset($terceros[$i]["cardinalidad1RutTercero"]) ? $terceros[$i]["cardinalidad1RutTercero"] : '');
                        $tercero->tipoVia2RutTercero = (isset($terceros[$i]["tipoVia2RutTercero"]) ? $terceros[$i]["tipoVia2RutTercero"] : '');
                        $tercero->numeroVia2RutTercero = (isset($terceros[$i]["numeroVia2RutTercero"]) ? $terceros[$i]["numeroVia2RutTercero"] : '');
                        $tercero->apendice2RutTercero = (isset($terceros[$i]["apendice2RutTercero"]) ? $terceros[$i]["apendice2RutTercero"] : '');
                        $tercero->cardinalidad2RutTercero = (isset($terceros[$i]["cardinalidad2RutTercero"]) ? $terceros[$i]["cardinalidad2RutTercero"] : '');
                        $tercero->numeroPlacaRutTercero = (isset($terceros[$i]["numeroPlacaRutTercero"]) ? $terceros[$i]["numeroPlacaRutTercero"] : '');
                        $tercero->complementoDireccionRutTercero = (isset($terceros[$i]["complementoDireccionRutTercero"]) ? $terceros[$i]["complementoDireccionRutTercero"] : '');


                        $tercero->Ciudad_idCiudad = (isset($terceros[$i]["Ciudad_idCiudad"]) ? $terceros[$i]["Ciudad_idCiudad"] : 0);
                        $tercero->telefono1Tercero = (isset($terceros[$i]["telefono1Tercero"]) ? $terceros[$i]["telefono1Tercero"] : '');
                        $tercero->telefono2Tercero = (isset($terceros[$i]["telefono2Tercero"]) ? $terceros[$i]["telefono2Tercero"] : '');
                        $tercero->movilTercero = (isset($terceros[$i]["movilTercero"]) ? $terceros[$i]["movilTercero"] : '');
                        $tercero->faxTercero = (isset($terceros[$i]["faxTercero"]) ? $terceros[$i]["faxTercero"] : '');
                        $tercero->correoElectronicoTercero = (isset($terceros[$i]["correoElectronicoTercero"]) ? $terceros[$i]["correoElectronicoTercero"] : '');
                        $tercero->paginaWebTercero = (isset($terceros[$i]["paginaWebTercero"]) ? $terceros[$i]["paginaWebTercero"] : '');
                        $tercero->fechaNacimientoTercero = (isset($terceros[$i]["fechaNacimientoTercero"]) ? $terceros[$i]["fechaNacimientoTercero"] : '');
                        $tercero->fechaCreacionTercero = (isset($terceros[$i]["fechaCreacionTercero"]) ? $terceros[$i]["fechaCreacionTercero"] : '');


                        $tercero->longitudSSCCTercero = (isset($terceros[$i]["longitudSSCCTercero"]) ? $terceros[$i]["longitudSSCCTercero"] : 0);
                        $tercero->estadoTercero = (isset($terceros[$i]["estadoTercero"]) ? $terceros[$i]["estadoTercero"] : 'ACTIVO');

                        // Informacion Comercial
                        $tercero->ListaPrecio_idListaPrecio = (isset($terceros[$i]["ListaPrecio_idListaPrecio"]) ? $terceros[$i]["ListaPrecio_idListaPrecio"] : 0);
                        $tercero->Tercero_idVendedor = (isset($terceros[$i]["Tercero_idVendedor"]) ? $terceros[$i]["Tercero_idVendedor"] : '');
                        $tercero->ClasificacionTercero_idClasificacionTercero = (isset($terceros[$i]["ClasificacionTercero_idClasificacionTercero"]) ? $terceros[$i]["ClasificacionTercero_idClasificacionTercero"] : '');
                        $tercero->cupoTercero = (isset($terceros[$i]["cupoTercero"]) ? $terceros[$i]["cupoTercero"] : 0);
                        $tercero->presupuestoAnoTercero = (isset($terceros[$i]["presupuestoAnoTercero"]) ? $terceros[$i]["presupuestoAnoTercero"] : 0);
                        $tercero->porcentajeDescuentoComercialTercero = (isset($terceros[$i]["porcentajeDescuentoComercialTercero"]) ? $terceros[$i]["porcentajeDescuentoComercialTercero"] : 0);
                        $tercero->porcentajeDescuentoFinancieroTercero = (isset($terceros[$i]["porcentajeDescuentoFinancieroTercero"]) ? $terceros[$i]["porcentajeDescuentoFinancieroTercero"] : 0);
                        $tercero->FormaPago_idFormaPago = (isset($terceros[$i]["FormaPago_idFormaPago"]) ? $terceros[$i]["FormaPago_idFormaPago"] : 0);

                        // Informacion Tributaria
                        $tercero->esAgenteRetenedorTercero = (isset($terceros[$i]["esAgenteRetenedorTercero"]) ? $terceros[$i]["esAgenteRetenedorTercero"] : 0);
                        $tercero->esAutoretenedor = (isset($terceros[$i]["esAutoretenedor"]) ? $terceros[$i]["esAutoretenedor"] : 0);
                        $tercero->regimenVentasTercero = (isset($terceros[$i]["regimenVentasTercero"]) ? $terceros[$i]["regimenVentasTercero"] : 0);
                        $tercero->esGranContribuyente = (isset($terceros[$i]["esGranContribuyente"]) ? $terceros[$i]["esGranContribuyente"] : 0);
                        $tercero->ActividadEconomica_idActividadEconomica = (isset($terceros[$i]["ActividadEconomica_idActividadEconomica"]) ? $terceros[$i]["ActividadEconomica_idActividadEconomica"] : 0);
                        $tercero->esProveedorPermanenteTercero = (isset($terceros[$i]["esProveedorPermanenteTercero"]) ? $terceros[$i]["esProveedorPermanenteTercero"] : 0);
                        $tercero->esEntidadEstadoTercero = (isset($terceros[$i]["esEntidadEstadoTercero"]) ? $terceros[$i]["esEntidadEstadoTercero"] : 0);

                        // Informacion Laboral (importacion de empleados)
                        $tercero->GrupoNomina_idGrupoNomina = (isset($terceros[$i]["GrupoNomina_idGrupoNomina"]) ? $terceros[$i]["GrupoNomina_idGrupoNomina"] : 0);
                        $tercero->estadoCivilTercero = (isset($terceros[$i]["estadoCivilTercero"]) ? $terceros[$i]["estadoCivilTercero"] : 'Soltero');
                        $tercero->tipoSalarioTercero = (isset($terceros[$i]["tipoSalarioTercero"]) ? $terceros[$i]["tipoSalarioTercero"] : 'Fijo');
                        $tercero->salarioBasicoTercero = (isset($terceros[$i]["salarioBasicoTercero"]) ? $terceros[$i]["salarioBasicoTercero"] : 0);
                        $tercero->esSalarioMinimoTercero = (isset($terceros[$i]["esSalarioMinimoTercero"]) ? $terceros[$i]["esSalarioMinimoTercero"] : 0);
                        $tercero->registraTurnoTercero = (isset($terceros[$i]["registraTurnoTercero"]) ? $terceros[$i]["registraTurnoTercero"] : 0);
                        $tercero->Turno_idTurno = (isset($terceros[$i]["Turno_idTurno"]) ? $terceros[$i]["Turno_idTurno"] : 0);
                        $tercero->periodoLiquidacionTercero = (isset($terceros[$i]["periodoLiquidacionTercero"]) ? $terceros[$i]["periodoLiquidacionTercero"] : 'Quincenal');
                        $tercero->ingresoFamiliarTercero = (isset($terceros[$i]["ingresoFamiliarTercero"]) ? $terceros[$i]["ingresoFamiliarTercero"] : 0);
                        $tercero->sexoTercero = (isset($terceros[$i]["sexoTercero"]) ? $terceros[$i]["sexoTercero"] : 'Masculino');
                        $tercero->grupoSanguineoTercero = (isset($terceros[$i]["grupoSanguineoTercero"]) ? $terceros[$i]["grupoSanguineoTercero"] : '');
                        $tercero->factorRHTercero = (isset($terceros[$i]["factorRHTercero"]) ? $terceros[$i]["factorRHTercero"] : '');
                        $tercero->Cargo_idCargo = (isset($terceros[$i]["Cargo_idCargo"]) ? $terceros[$i]["Cargo_idCargo"] : 0);
                        $tercero->CentroCosto_idCentroCosto = (isset($terceros[$i]["CentroCosto_idCentroCosto"]) ? $terceros[$i]["CentroCosto_idCentroCosto"] : 0);
                        $tercero->CentroTrabajo_idCentroTrabajo = (isset($terceros[$i]["CentroTrabajo_idCentroTrabajo"]) ? $terceros[$i]["CentroTrabajo_idCentroTrabajo"] : 0);
                        $tercero->fechaNacimientoTercero = (isset($terceros[$i]["fechaNacimientoTercero"]) ? $terceros[$i]["fechaNacimientoTercero"] : '');
                        $tercero->Ciudad_idLugarNacimiento = (isset($terceros[$i]["Ciudad_idLugarNacimiento"]) ? $terceros[$i]["Ciudad_idLugarNacimiento"] : 0);
                        $tercero->fechaExpedicionDocumentoTercero = (isset($terceros[$i]["fechaExpedicionDocumentoTercero"]) ? $terceros[$i]["fechaExpedicionDocumentoTercero"] : '');
                        $tercero->Ciudad_idLugarExpedicion = (isset($terceros[$i]["Ciudad_idLugarExpedicion"]) ? $terceros[$i]["Ciudad_idLugarExpedicion"] : 0);
                        $tercero->Tercero_idSalud = (isset($terceros[$i]["Tercero_idSalud"]) ? $terceros[$i]["Tercero_idSalud"] : 0);
                        $tercero->periodicidadSaludTercero = (isset($terceros[$i]["periodicidadSaludTercero"]) ? $terceros[$i]["periodicidadSaludTercero"] : 'PERIODICO');
                        $tercero->valorSaludUPCTercero = (isset($terceros[$i]["valorSaludUPCTercero"]) ? $terceros[$i]["valorSaludUPCTercero"] : 0);
                        $tercero->Tercero_idPension = (isset($terceros[$i]["Tercero_idPension"]) ? $terceros[$i]["Tercero_idPension"] : 0);
                        $tercero->periodicidadPensionTercero = (isset($terceros[$i]["periodicidadPensionTercero"]) ? $terceros[$i]["periodicidadPensionTercero"] : 'PERIODICO');
                        $tercero->valorAporteVoluntarioPensionTercero = (isset($terceros[$i]["valorAporteVoluntarioPensionTercero"]) ? $terceros[$i]["valorAporteVoluntarioPensionTercero"] : 0);
                        $tercero->Tercero_idCesantias = (isset($terceros[$i]["Tercero_idCesantias"]) ? $terceros[$i]["Tercero_idCesantias"] : 0);
                        $tercero->leyCesantiasTercero = (isset($terceros[$i]["leyCesantiasTercero"]) ? $terceros[$i]["leyCesantiasTercero"] : 'ACTUAL');
                        $tercero->jornadaLaboralDiaTercero = (isset($terceros[$i]["jornadaLaboralDiaTercero"]) ? $terceros[$i]["jornadaLaboralDiaTercero"] : 0);
                        $tercero->Barrio_idBarrio = (isset($terceros[$i]["Barrio_idBarrio"]) ? $terceros[$i]["Barrio_idBarrio"] : 0);
                        $tercero->numeroHijosTercero = (isset($terceros[$i]["numeroHijosTercero"]) ? $terceros[$i]["numeroHijosTercero"] : 0);
                        $tercero->Tercero_idARP = (isset($terceros[$i]["Tercero_idARP"]) ? $terceros[$i]["Tercero_idARP"] : 0);
                        $tercero->Tercero_idCajaCompensacion = (isset($terceros[$i]["Tercero_idCajaCompensacion"]) ? $terceros[$i]["Tercero_idCajaCompensacion"] : 0);
                        $tercero->MacroCanal_idMacroCanal = (isset($terceros[$i]["MacroCanal_idMacroCanal"]) ? $terceros[$i]["MacroCanal_idMacroCanal"] : 0);
                        $tercero->Zona_idZona = (isset($terceros[$i]["Zona_idZona"]) ? $terceros[$i]["Zona_idZona"] : 0);
                        $tercero->Tercero_idSucursal = (isset($terceros[$i]["Tercero_idSucursal"]) ? $terceros[$i]["Tercero_idSucursal"] : 0);


                        $tercero->esAutoretenedorCREETercero = (isset($terceros[$i]["esAutoretenedorCREETercero"]) ? $terceros[$i]["esAutoretenedorCREETercero"] : 0);
                        $tercero->noResponsableIVATercero = (isset($terceros[$i]["noResponsableIVATercero"]) ? $terceros[$i]["noResponsableIVATercero"] : 0);
                        $tercero->fechaFinBeneficio1429Tercero = (isset($terceros[$i]["fechaFinBeneficio1429Tercero"]) ? $terceros[$i]["fechaFinBeneficio1429Tercero"] : 0);
                        $tercero->ClasificacionRenta_idClasificacionRenta = (isset($terceros[$i]["ClasificacionRenta_idClasificacionRenta"]) ? $terceros[$i]["ClasificacionRenta_idClasificacionRenta"] : 0);
                        $tercero->calcularRetencionFuenteProveedorSinBaseTercero = (isset($terceros[$i]["calcularRetencionFuenteProveedorSinBaseTercero"]) ? $terceros[$i]["calcularRetencionFuenteProveedorSinBaseTercero"] : 0);
                        $tercero->calcularRetencionIvaProveedorSinBaseTercero = (isset($terceros[$i]["calcularRetencionIvaProveedorSinBaseTercero"]) ? $terceros[$i]["calcularRetencionIvaProveedorSinBaseTercero"] : 0);
                        $tercero->calcularRetencionIcaProveedorSinBaseTercero = (isset($terceros[$i]["calcularRetencionIcaProveedorSinBaseTercero"]) ? $terceros[$i]["calcularRetencionIcaProveedorSinBaseTercero"] : 0);
                        $tercero->calcularRetencionFuenteClienteSinBaseTercero = (isset($terceros[$i]["calcularRetencionFuenteClienteSinBaseTercero"]) ? $terceros[$i]["calcularRetencionFuenteClienteSinBaseTercero"] : 0);
                        $tercero->calcularRetencionIvaClienteSinBaseTercero = (isset($terceros[$i]["calcularRetencionIvaClienteSinBaseTercero"]) ? $terceros[$i]["calcularRetencionIvaClienteSinBaseTercero"] : 0);
                        $tercero->calcularRetencionIcaClienteSinBaseTercero = (isset($terceros[$i]["calcularRetencionIcaClienteSinBaseTercero"]) ? $terceros[$i]["calcularRetencionIcaClienteSinBaseTercero"] : 0);
                        $tercero->calcularRetencionCreeProveedorSinBaseTercero = (isset($terceros[$i]["calcularRetencionCreeProveedorSinBaseTercero"]) ? $terceros[$i]["calcularRetencionCreeProveedorSinBaseTercero"] : 0);
                        $tercero->calcularRetencionCreeClienteSinBaseTercero = (isset($terceros[$i]["calcularRetencionCreeClienteSinBaseTercero"]) ? $terceros[$i]["calcularRetencionCreeClienteSinBaseTercero"] : 0);
                        $tercero->calcularIvaProveedorTercero = (isset($terceros[$i]["calcularIvaProveedorTercero"]) ? $terceros[$i]["calcularIvaProveedorTercero"] : 0);
                        $tercero->calcularIvaClienteTercero = (isset($terceros[$i]["calcularIvaClienteTercero"]) ? $terceros[$i]["calcularIvaClienteTercero"] : 0);
                        $tercero->calcularImpoconsumoProveedorTercero = (isset($terceros[$i]["calcularImpoconsumoProveedorTercero"]) ? $terceros[$i]["calcularImpoconsumoProveedorTercero"] : 0);
                        $tercero->calcularImpoconsumoClienteTercero = (isset($terceros[$i]["calcularImpoconsumoClienteTercero"]) ? $terceros[$i]["calcularImpoconsumoClienteTercero"] : 0);
                        $tercero->calcularRetencionFuenteClienteTercero = (isset($terceros[$i]["calcularRetencionFuenteClienteTercero"]) ? $terceros[$i]["calcularRetencionFuenteClienteTercero"] : 0);
                        $tercero->calcularRetencionIvaClienteTercero = (isset($terceros[$i]["calcularRetencionIvaClienteTercero"]) ? $terceros[$i]["calcularRetencionIvaClienteTercero"] : 0);
                        $tercero->calcularRetencionIcaClienteTercero = (isset($terceros[$i]["calcularRetencionIcaClienteTercero"]) ? $terceros[$i]["calcularRetencionIcaClienteTercero"] : 0);
                        $tercero->calcularRetencionCreeClienteTercero = (isset($terceros[$i]["calcularRetencionCreeClienteTercero"]) ? $terceros[$i]["calcularRetencionCreeClienteTercero"] : 0);
                        $tercero->calcularRetencionCreeProveedorTercero = (isset($terceros[$i]["calcularRetencionCreeProveedorTercero"]) ? $terceros[$i]["calcularRetencionCreeProveedorTercero"] : 0);
                        $tercero->resolucionDIANTercero = (isset($terceros[$i]["resolucionDIANTercero"]) ? $terceros[$i]["resolucionDIANTercero"] : 0);
                        $tercero->modalidadFacturacionTercero = (isset($terceros[$i]["modalidadFacturacionTercero"]) ? $terceros[$i]["modalidadFacturacionTercero"] : 0);
                        $tercero->tipoSolicitudFacturacionTercero = (isset($terceros[$i]["tipoSolicitudFacturacionTercero"]) ? $terceros[$i]["tipoSolicitudFacturacionTercero"] : 0);
                        $tercero->fechaResolucionTercero = (isset($terceros[$i]["fechaResolucionTercero"]) ? $terceros[$i]["fechaResolucionTercero"] : 0);
                        $tercero->prefijoResolucionTercero = (isset($terceros[$i]["prefijoResolucionTercero"]) ? $terceros[$i]["prefijoResolucionTercero"] : 0);
                        $tercero->sufijoResolucionTercero = (isset($terceros[$i]["sufijoResolucionTercero"]) ? $terceros[$i]["sufijoResolucionTercero"] : 0);
                        $tercero->numeroInicialResolucionTercero = (isset($terceros[$i]["numeroInicialResolucionTercero"]) ? $terceros[$i]["numeroInicialResolucionTercero"] : 0);
                        $tercero->numeroFinalResolucionTercero = (isset($terceros[$i]["numeroFinalResolucionTercero"]) ? $terceros[$i]["numeroFinalResolucionTercero"] : 0);


                        // cada que llenamos un producto, lo cargamos a la base de datos
                        // si el id esta lleno, lo actualizamos, si esta vacio lo insertamos
                        if ($terceros[$i]['idTercero'] == 0) {


                            $tercero->AdicionarTercero();
                        } else {

                            $tercero->ModificarExcelEmpleado();
                        }
                    } else {
                        $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                    }
                } else {
                    $retorno = array_merge((array) $retorno, (array) $newerrors);
                }
            }

            return $retorno;
        }

        function validarTerceros($codigoAlterno1Tercero, $x, $terceros) {


            $swerror = true;
            $errores = array();
            $linea = 0;



            // validamos que la referencia no este repetida en el mismo archivo de excel
            for ($i = 0; $i < count($terceros); $i++) {
                if ($terceros[$i]["codigoAlterno1Tercero"] == $codigoAlterno1Tercero and $i != $x) {
                    $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                    $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                    $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                    $errores[$linea]["error"] = 'El c&oacute;digo alterno ' . $codigoAlterno1Tercero . ' esta repetida en el archivo, lineas ' . ($x + 4) . ' y ' . ($i + 4);
                    $swerror = false;
                    $linea++;
                }
            }

            // validamos tipo de identificacion
            if ($terceros[$x]["TipoIdentificacion_idIdentificacion"] == 0 and $terceros[$x]["codigoIdentificacion"] != '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'El Codigo del Tipo de Identificacion (' . $terceros[$x]["codigoIdentificacion"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            if ($terceros[$x]["codigoAlterno1Tercero"] == "") {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'El c&oacute;digo alterno 1 tercero (' . $terceros[$x]["codigoAlterno1Tercero"] . ') est&aacute; vac&iacute;o, debe cambiarlo.';
                $swerror = false;
                $linea++;
            }

            // validamos el documento
            if ($terceros[$x]["documentoTercero"] == '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'El Numero de Documento esta en blanco';
                $swerror = false;
                $linea++;
            }



            // validamos la razon social
            if ($terceros[$x]["nombre1Tercero"] == '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'La Razón Social o Nombre esta en blanco';
                $swerror = false;
                $linea++;
            }

            // validamos el nombre comercial
            /* if ($terceros[$x]["nombre2Tercero"] == '')
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El Nombre Comercial o Apellidos esta en blanco';
              $swerror = false;
              $linea++;
              } */

            // validamos la clasificacion
            if ($terceros[$x]["tipoTercero"] == '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'El tipo de Tercero esta en blanco, debe elegir al menos una opci&oacute;n';
                $swerror = false;
                $linea++;
            }

            // validamos la direccion
            if ($terceros[$x]["direccionTercero"] == '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'la dirección esta en blanco';
                $swerror = false;
                $linea++;
            }

            // validamos la ciudad
            if ($terceros[$x]["Ciudad_idCiudad"] == 0 and $terceros[$x]["Ciudad_idCiudad"] != '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'El Codigo del Ciudad (' . $terceros[$x]["codigoAlternoCiudad"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos el telefono 1
            if ($terceros[$x]["telefono1Tercero"] == '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'El telefono 1 esta en blanco';
                $swerror = false;
                $linea++;
            }

            // validamos la forma de pago
            if (isset($terceros[$x]["FormaPago_idFormaPago"]) and $terceros[$x]["FormaPago_idFormaPago"] == 0 and $terceros[$x]["FormaPago_idFormaPago"] != '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'El Codigo de la forma de pago (' . $terceros[$x]["codigoAlternoFormaPago"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            // validamos el regimen
            if (isset($terceros[$x]["regimenVentasTercero"]) and $terceros[$x]["regimenVentasTercero"] == '') {
                $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
                $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
                $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
                $errores[$linea]["error"] = 'El régimen de ventas esta en blanco, indique SIMPLIFICADO o COMUN';
                $swerror = false;
                $linea++;
            }

            return $errores;
        }

        function ImportarMaestroEmpleadosExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/tipoidentificacion.class.php');
            $tipoidentificacion = new TipoIdentificacion();
            require_once('../clases/ciudad.class.php');
            $ciudad = new Ciudad();
            require_once('../clases/barrio.class.php');
            $barrio = new Barrio();
            require_once('../clases/gruponomina.class.php');
            $gruponomina = new GrupoNomina();
            require_once('../clases/clasificaciontercero.class.php');
            $clasificaciontercero = new ClasificacionTercero();
            require_once('../clases/turno.class.php');
            $turno = new Turno();
            require_once('../clases/cargo.class.php');
            $cargo = new Cargo();
            require_once('../clases/centrocosto.class.php');
            $centrocosto = new CentroCosto();
            require_once('../clases/centrotrabajo.class.php');
            $centrotrabajo = new CentroTrabajo();
            require_once '../clases/macrocanal.class.php';
            $macrocanal = new MacroCanal();
            require_once '../clases/zona.class.php';
            $zona = new Zona();

            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }


            $objReader->setLoadSheetsOnly('tercero');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $terceros = array();
            $posTer = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != NULL) {

                // por cada numero de documento diferente, incrementamos el indice (empieza en cero)
                $posTer++;

                // para cada registro de terceros recorremos las columnas desde la 0 hasta la 86
                for ($columna = 0; $columna <= 92; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getDataType() == 'f')
                        $terceros[$posTer][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getCalculatedValue();
                    else
                        $terceros[$posTer][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // Concatenamos los nombres y apellidos como el nombre completo
                // convertimos la fecha de formato EXCEL a formato UNIX
                $fechaCreacion = $terceros[$posTer]["fechaCreacionTercero"];

                $terceros[$posTer]["fechaCreacionTercero"] = (gettype($fechaCreacion) == 'double' or gettype($fechaCreacion) == 'integer' and $fechaCreacion > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaCreacion))) : $terceros[$posTer]["fechaCreacionTercero"];

                $fechaNacimiento = $terceros[$posTer]["fechaNacimientoTercero"];

                $terceros[$posTer]["fechaNacimientoTercero"] = (gettype($fechaNacimiento) == 'double' or gettype($fechaNacimiento) == 'integer' and $fechaNacimiento > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaNacimiento))) : $terceros[$posTer]["fechaNacimientoTercero"];

                //$terceros[$posTer]["nombre1Tercero"] = $terceros[$posTer]["nombreATercero"] . ' ' .$terceros[$posTer]["nombreBTercero"] . ' ' .
                //										$terceros[$posTer]["apellidoATercero"] . ' ' .$terceros[$posTer]["apellidoBTercero"];
                // Adicionamos la ruta a la foto del empleado
                //$terceros[$posTer]["imagenTercero"] = ($terceros[$posTer]["imagenTercero"] != '' ? '../documentos_terceros/foto/'.$terceros[$posTer]["imagenTercero"] : '');
                // cada que llenemos un tercero, hacemos las verificaciones de codigos necesarioos
                // verificamos cuales campos del tipo de tercero estan llenos para armar la codificacion de clasificacion
                $terceros[$posTer]["tipoTercero"] = '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["CLI"]) and $terceros[$posTer]["CLI"] != NULL) ? '*01*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["PRO"]) and $terceros[$posTer]["PRO"] != NULL) ? '*02*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["VEN"]) and $terceros[$posTer]["VEN"] != NULL) ? '*03*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["TRA"]) and $terceros[$posTer]["TRA"] != NULL) ? '*04*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["EMP"]) and $terceros[$posTer]["EMP"] != NULL) ? '*05*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["BAN"]) and $terceros[$posTer]["BAN"] != NULL) ? '*06*' : '';

                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["ASE"]) and $terceros[$posTer]["ASE"] != NULL) ? '*07*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["PROP"]) and $terceros[$posTer]["PROP"] != NULL) ? '*08*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["INQ"]) and $terceros[$posTer]["INQ"] != NULL) ? '*09*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["DEU"]) and $terceros[$posTer]["DEU"] != NULL) ? '*10*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["SAL"]) and $terceros[$posTer]["SAL"] != NULL) ? '*11*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["PEN"]) and $terceros[$posTer]["PEN"] != NULL) ? '*12*' : '';

                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["RIE"]) and $terceros[$posTer]["RIE"] != NULL) ? '*13*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["COM"]) and $terceros[$posTer]["COM"] != NULL) ? '*14*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["CES"]) and $terceros[$posTer]["CES"] != NULL) ? '*15*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["SOC"]) and $terceros[$posTer]["SOC"] != NULL) ? '*16*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["MIE"]) and $terceros[$posTer]["MIE"] != NULL) ? '*17*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["SUC"]) and $terceros[$posTer]["SUC"] != NULL) ? '*18*' : '';

                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["SIA"]) and $terceros[$posTer]["SIA"] != NULL) ? '*19*' : '';
                $terceros[$posTer]["tipoTercero"] .= (!empty($terceros[$posTer]["POS"]) and $terceros[$posTer]["POS"] != NULL) ? '*20*' : '';

                // verificamos las columnas que se llenan con X, para cambiarlas por 1
                $terceros[$posTer]["registraTurnoTercero"] = (!empty($terceros[$posTer]["registraTurnoTercero"]) and $terceros[$posTer]["registraTurnoTercero"] != NULL ? 1 : 0);
                $terceros[$posTer]["esSalarioMinimoTercero"] = (!empty($terceros[$posTer]["esSalarioMinimoTercero"]) and $terceros[$posTer]["esSalarioMinimoTercero"] != NULL ? 1 : 0);


                // este campo es auxiliar, nos sirve para saber si el el nit principal o es una sucursal
                $condicionSucursal = (!empty($terceros[$posTer]["SUC"]) and $terceros[$posTer]["SUC"] != NULL) ? "tipoTercero like '%*18*%'" : "tipoTercero not like '%*18*%'";

                // consultamos el documento del tercero para obtener su id, en este caso como pueden existir varias sucursales con el mismo NIT,
                // verificamos que tambien coincida el campo TipoTercero con que tenga o no el identificador *18* (sucursal)
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["documentoTercero"]))
                    $tercero->ConsultarTercero("documentoTercero = '" . $terceros[$posTer]["documentoTercero"] . "' and $condicionSucursal");
                $terceros[$posTer]["idTercero"] = $tercero->idTercero;


                // consultamos el codigo de tipoidentificacion para obtener su id
                $tipoidentificacion->idIdentificacion = 0;
                if (!empty($terceros[$posTer]["codigoIdentificacion"]))
                    $tipoidentificacion->ConsultarIdentificacion("codigoIdentificacion = '" . $terceros[$posTer]["codigoIdentificacion"] . "'");
                $terceros[$posTer]["TipoIdentificacion_idIdentificacion"] = $tipoidentificacion->idIdentificacion;


                // consultamos el barrio de residencia del tercero
                $barrio->idBarrio = 0;
                if (!empty($terceros[$posTer]["codigoAlternoBarrio"]))
                    $barrio->ConsultarBarrio("codigoAlternoBarrio = '" . $terceros[$posTer]["codigoAlternoBarrio"] . "'");
                $terceros[$posTer]["Barrio_idBarrio"] = $barrio->idBarrio;


                // consultamos la ciudad de residencia del tercero
                $ciudad->idCiudad = 0;
                if (!empty($terceros[$posTer]["codigoAlternoCiudad"]))
                    $ciudad->ConsultarCiudad("codigoAlternoCiudad = '" . $terceros[$posTer]["codigoAlternoCiudad"] . "'");
                $terceros[$posTer]["Ciudad_idCiudad"] = $ciudad->idCiudad;


                // consultamos la ciudad de nacimiento del tercero
                $ciudad->idCiudad = 0;
                if (!empty($terceros[$posTer]["codigoAlternoLugarNacimiento"]))
                    $ciudad->ConsultarCiudad("codigoAlternoCiudad = '" . $terceros[$posTer]["codigoAlternoLugarNacimiento"] . "'");
                $terceros[$posTer]["Ciudad_idLugarNacimiento"] = $ciudad->idCiudad;


                //Consultamos la ciudad de expedición de del documento del tercero

                $ciudad->idCiudad = 0;
                if (!empty($terceros[$posTer]["codigoAlternoLugarExpedicion"]))
                    $ciudad->ConsultarCiudad("codigoAlternoCiudad = '" . $terceros[$posTer]["codigoAlternoLugarExpedicion"] . "'");
                $terceros[$posTer]["Ciudad_idLugarExpedicion"] = $ciudad->idCiudad;

                // consultamos la clasificacion del tercero
                $clasificaciontercero->idClasificacionTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoClasificacionTercero"]))
                    $clasificaciontercero->ConsultarClasificacionTercero("codigoAlternoClasificacionTercero = '" . $terceros[$posTer]["codigoAlternoClasificacionTercero"] . "'");
                $terceros[$posTer]["ClasificacionTercero_idClasificacionTercero"] = $clasificaciontercero->idClasificacionTercero;

                // consultamos el grupo de nomina
                if (!empty($terceros[$posTer]["codigoAlternoGrupoNomina"]))
                    $datos = $gruponomina->ConsultarVistaGrupoNomina("codigoAlternoGrupoNomina = '" . $terceros[$posTer]["codigoAlternoGrupoNomina"] . "'");
                $terceros[$posTer]["GrupoNomina_idGrupoNomina"] = (isset($datos[0]["idGrupoNomina"]) ? $datos[0]["idGrupoNomina"] : 0);


                // consultamos el Turno
                if (!empty($terceros[$posTer]["codigoAlternoTurno"]))
                    $datos = $turno->ConsultarVistaTurno("codigoAlternoTurno = '" . $terceros[$posTer]["codigoAlternoTurno"] . "'");
                $terceros[$posTer]["Turno_idTurno"] = (isset($datos[0]["idTurno"]) ? $datos[0]["idTurno"] : 0);

                // consultamos el Cargo
                if (!empty($terceros[$posTer]["codigoAlternoCargo"]))
                    $datos = $cargo->ConsultarVistaCargo("codigoAlternoCargo = '" . $terceros[$posTer]["codigoAlternoCargo"] . "'");
                $terceros[$posTer]["Cargo_idCargo"] = (isset($datos[0]["idCargo"]) ? $datos[0]["idCargo"] : 0);

                // consultamos el Centro de Costos
                if (!empty($terceros[$posTer]["codigoAlternoCentroCosto"]))
                    $datos = $centrocosto->ConsultarVistaCentroCosto("codigoAlternoCentroCosto = '" . $terceros[$posTer]["codigoAlternoCentroCosto"] . "'");
                $terceros[$posTer]["CentroCosto_idCentroCosto"] = (isset($datos[0]["idCentroCosto"]) ? $datos[0]["idCentroCosto"] : 0);

                //consultamos de Macro Canal
                if (!empty($terceros[$posTer]["codigoAlternoMacroCanal"]))
                    $datos = $macrocanal->ConsultarVistaMacroCanal("codigoAlternoMacroCanal = '" . $terceros[$posTer]["codigoAlternoMacroCanal"] . "'");
                $terceros[$posTer]["MacroCanal_idMacroCanal"] = (isset($datos[0]["idMacroCanal"]) ? $datos[0]["idMacroCanal"] : 0);

                //consultamos de Zona
                if (!empty($terceros[$posTer]["codigoAlternoZona"]))
                    $datos = $zona->ConsultarVistaZona("codigoAlternoZona = '" . $terceros[$posTer]["codigoAlternoZona"] . "'");
                $terceros[$posTer]["Zona_idZona"] = (isset($datos[0]["idZona"]) ? $datos[0]["idZona"] : 0);

                // consultamos el Centro de trabajo
                if (!empty($terceros[$posTer]["codigoAlternoCentroTrabajo"]))
                    $datos = $centrotrabajo->ConsultarVistaCentroTrabajo("codigoAlternoCentroTrabajo = '" . $terceros[$posTer]["codigoAlternoCentroTrabajo"] . "'");
                $terceros[$posTer]["CentroTrabajo_idCentroTrabajo"] = (isset($datos[0]["idCentroTrabajo"]) ? $datos[0]["idCentroTrabajo"] : 0);

                // Consultamos el id del fondo de salud
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoSalud"]))
                    $tercero->ConsultarTercero("(documentoTercero = '" . $terceros[$posTer]["codigoAlternoSalud"] . "' or codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlternoSalud"] . "')
                                                                                                            and tipoTercero like '%*11*%'");
                $terceros[$posTer]["Tercero_idSalud"] = $tercero->idTercero;

                // Consultamos el id del fondo de Pension
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoPension"]))
                    $tercero->ConsultarTercero("(documentoTercero = '" . $terceros[$posTer]["codigoAlternoPension"] . "' or codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlternoPension"] . "')
                                                                                                            and tipoTercero like '%*12*%'");
                $terceros[$posTer]["Tercero_idPension"] = $tercero->idTercero;

                // Consultamos el id del fondo de Cesantías
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoCesantias"]))
                    $tercero->ConsultarTercero("(documentoTercero = '" . $terceros[$posTer]["codigoAlternoCesantias"] . "' or codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlternoCesantias"] . "')
                                                                                                            and tipoTercero like '%*15*%'");
                $terceros[$posTer]["Tercero_idCesantias"] = $tercero->idTercero;

                // Consultamos el id de la ARP
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoARP"]))
                    $tercero->ConsultarTercero("(documentoTercero = '" . $terceros[$posTer]["codigoAlternoARP"] . "' or codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlternoARP"] . "')
                                                                                                            and tipoTercero like '%*13*%'");
                $terceros[$posTer]["Tercero_idARP"] = $tercero->idTercero;

                // Consultamos el id de la Caja de Compensacion Familiar
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoCajaCompensacion"]))
                    $tercero->ConsultarTercero("(documentoTercero = '" . $terceros[$posTer]["codigoAlternoCajaCompensacion"] . "' or codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlternoCajaCompensacion"] . "')
                                                                                                            and tipoTercero like '%*14*%'");
                $terceros[$posTer]["Tercero_idCajaCompensacion"] = $tercero->idTercero;

                // Consultamos el id de la Sucursal
                $tercero->idTercero = 0;
                if (!empty($terceros[$posTer]["codigoAlternoSucursal"]))
                    $tercero->ConsultarTercero("(documentoTercero = '" . $terceros[$posTer]["codigoAlternoSucursal"] . "' or codigoAlterno1Tercero = '" . $terceros[$posTer]["codigoAlternoSucursal"] . "')
                                                                                                            and tipoTercero like '%*18*%'");
                $terceros[$posTer]["Tercero_idSucursal"] = $tercero->idTercero;

                $fila++;
            }


            //		print_r($terceros);
            // luego de que tenemos la matriz de terceros llena, las enviamos al proceso de importacion de terceros
            // para que los valide e importe al sistema
            $retorno = $this->llenarPropiedadesEmpleados($terceros);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($terceros);

            $this->eliminarArchivo($ruta);
            //                return;

            return $retorno;
        }

        function ImportarContratosExcel($ruta) {

            //            ECHO 'ENTRA';


            set_time_limit(0);
            //		echo $ruta;
            require_once('../clases/contrato.class.php');
            $contrato = new Contrato();
            require_once('../clases/tipocontrato.class.php');
            $tipocontrato = new TipoContrato();
            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/producto.class.php');
            $producto = new Producto();
            require_once('../clases/turno.class.php');
            $turno = new Turno();
            require_once('../clases/mediopago.class.php');
            $mediopago = new MedioPago();
            require_once('../clases/gruponomina.class.php');
            $gruponomina = new GrupoNomina();
            require_once('../clases/gruponomina.class.php');
            $gruponomina = new GrupoNomina();
            require_once('../clases/tipocotizante.class.php');
            $tipocotizante = new TipoCotizante();
            require_once('../clases/subtipocotizante.class.php');
            $subtipocotizante = new SubtipoCotizante();

            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            //echo isset($objReader);

            $objReader->setLoadSheetsOnly('Contrato Laboral');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $contratos = array();
            $posCont = -1;


            $fila = 4;
            //echo 'primer dato'. $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();
            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                // por cada numero de documento diferente, incrementamos el indice (empieza en cero)
                $posCont++;

                // para cada registro de terceros recorremos las columnas desde la 0 hasta la 26
                for ($columna = 0; $columna <= 29; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getDataType() == 'f')
                        $contratos[$posCont][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getCalculatedValue();
                    else
                        $contratos[$posCont][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }


                $fechaReal1 = $contratos[$posCont]["fechaElaboracionContrato"];


                $contratos[$posCont]["fechaElaboracionContrato"] = (gettype($fechaReal1) == 'double' or gettype($fechaReal1) == 'integer' and $fechaReal1 > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal1))) : $contratos[$posCont]["fechaElaboracionContrato"];
                $fechaReal2 = $contratos[$posCont]["fechaInicioContrato"];

                $contratos[$posCont]["fechaInicioContrato"] = (gettype($fechaReal2) == 'double' or gettype($fechaReal2) == 'integer' and $fechaReal2 > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal2))) : $contratos[$posCont]["fechaInicioContrato"];
                $fechaReal3 = $contratos[$posCont]["fechaVencimientoContrato"];

                $contratos[$posCont]["fechaVencimientoContrato"] = (gettype($fechaReal3) == 'double' or gettype($fechaReal3) == 'integer' and $fechaReal3 > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal3))) : $contratos[$posCont]["fechaVencimientoContrato"];
                $fechaReal4 = $contratos[$posCont]["fechaTerminacionContrato"];

                $contratos[$posCont]["fechaTerminacionContrato"] = (gettype($fechaReal4) == 'double' or gettype($fechaReal4) == 'integer' and $fechaReal4 > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal4))) : $contratos[$posCont]["fechaTerminacionContrato"];



                $valores = array("0", "1", "2");

                if (!in_array($contratos[$posCont]["registraTurnoContrato"], $valores)) {
                    $contratos[$posCont]["registraTurnoContrato"] = 'ERROR';
                }


                $peri = array("Semanal", "Decadal", "Catorcenal", "Quincenal", "Mensual", "Bimestral", "Trimestral", "Semestral", "Anual");

                if (!in_array($contratos[$posCont]["periodicidadPagoContrato"], $peri)) {
                    $contratos[$posCont]["periodicidadPagoContrato"] = 'ERROR';
                } else {
                    switch ($contratos[$posCont]["periodicidadPagoContrato"]) {

                        case "Semanal":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 8;
                            break;
                        case "Decadal":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 10;
                            break;
                        case "Catorcenal":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 14;
                            break;
                        case "Quincenal":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 15;
                            break;
                        case "Mensual":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 30;
                            break;
                        case "Bimestral":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 60;
                            break;
                        case "Trimestral":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 90;
                            break;
                        case "Semestral":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 180;
                            break;
                        case "Anual":
                            $contratos[$posCont]["periodicidadPagoContrato"] = 360;
                            break;
                    }
                }



                if (($contratos[$posCont]["tipoSalarioContrato"]) != 'F' and ( $contratos[$posCont]["tipoSalarioContrato"]) != 'V' and ( $contratos[$posCont]["tipoSalarioContrato"]) != 'I') {
                    $contratos[$posCont]["tipoSalarioContrato"] = 'ERROR';
                } else {
                    if ($contratos[$posCont]["tipoSalarioContrato"] == 'F') {
                        $contratos[$posCont]["tipoSalarioContrato"] = 'Fijo';
                    } else if ($contratos[$posCont]["tipoSalarioContrato"] == 'V') {
                        $contratos[$posCont]["tipoSalarioContrato"] = 'Variable';
                    } else {
                        $contratos[$posCont]["tipoSalarioContrato"] = 'Integral';
                    }
                }





                if (trim($contratos[$posCont]["tipoPagoContrato"]) != 'A' and trim($contratos[$posCont]["tipoPagoContrato"]) != 'VE') {
                    $contratos[$posCont]["tipoPagoContrato"] = 'ERROR';
                } else {
                    if ($contratos[$posCont]["tipoPagoContrato"] == 'A') {
                        $contratos[$posCont]["tipoPagoContrato"] = 'ANTICIPADO';
                    } else {
                        $contratos[$posCont]["tipoPagoContrato"] = 'VENCIDO';
                    }
                }


                //                        if($contratos[$posCont]["periodicidadPagoContrato"] != 'Semanal' &&  $contratos[$posCont]["periodicidadPagoContrato"] != 'Decadal' && $contratos[$posCont]["periodicidadPagoContrato"] != 'Catorcenal')
                // verificamos las columnas que se llenan con X, para cambiarlas por 1
                //$contratos[$posCont]["registraTurnoContrato"] = (!empty($contratos[$posCont]["registraTurnoContrato"]) and $contratos[$posCont]["registraTurnoContrato"] != NULL ? 1 : 0);
                // consultamos el numero de contrato para obtener su ID (si existe)
                $contrato->idContrato = 0;
                if (!empty($contratos[$posCont]["codigoAlternoContrato"]))
                    $contrato->ConsultarContrato("codigoAlternoContrato = '" . $contratos[$posCont]["codigoAlternoContrato"] . "'");
                $contratos[$posCont]["idContrato"] = $contrato->idContrato;

                // consultamos el tipo de contrato para obtener su ID (si existe)
                $tipocontrato->idTipoContrato = 0;
                if (!empty($contratos[$posCont]["codigoAlternoTipoContrato"]))
                    $tipocontrato->ConsultarTipoContrato("codigoAlternoTipoContrato = '" . $contratos[$posCont]["codigoAlternoTipoContrato"] . "'");
                $contratos[$posCont]["TipoContrato_idTipoContrato"] = $tipocontrato->idTipoContrato;




                // consultamos el producto para obtener su ID (si existe)
                /* $producto->idProducto = 0;
                  if (!empty($contratos[$posCont]["codigoAlternoProducto"]))
                  $tercero->ConsultarProducto("codigoAlternoProducto = '" . $contratos[$posCont]["codigoAlternoProducto"] . "' or
                  referenciaProducto = '" . $contratos[$posCont]["referenciaProducto"] . "' or
                  codigoBarrasProducto = '" . $contratos[$posCont]["codigoBarrasProducto"] . "'");
                  $contratos[$posCont]["Producto_idProducto"] = $producto->idProducto;
                 */
                // consultamos los documentos de los terceros para obtener su id, en este caso deben ser terceros de tipo empleado, exepto los deudores 1 y 2
                $tercero->idTercero = 0;
                if (!empty($contratos[$posCont]["documentoAsesor"]))
                    $tercero->ConsultarTercero("documentoTercero = '" . $contratos[$posCont]["documentoAsesor"] . "' and tipoTercero like '%*05*%'");
                $contratos[$posCont]["Tercero_idAsesor"] = $tercero->idTercero;

                $tercero->idTercero = 0;
                if (!empty($contratos[$posCont]["documentoAuxiliar"]))
                    $tercero->ConsultarTercero("documentoTercero = '" . $contratos[$posCont]["documentoAuxiliar"] . "' and tipoTercero like '%*05*%'");
                $contratos[$posCont]["Tercero_idAuxiliar"] = $tercero->idTercero;

                $tercero->idTercero = 0;
                if (!empty($contratos[$posCont]["documentoCliente"]))
                    $tercero->ConsultarTercero("documentoTercero = '" . $contratos[$posCont]["documentoCliente"] . "' and tipoTercero like '%*05*%'");
                $contratos[$posCont]["Tercero_idCliente"] = $tercero->idTercero;

                $tercero->idTercero = 0;
                if (!empty($contratos[$posCont]["documentoDeudor1"]))
                    $tercero->ConsultarTercero("documentoTercero = '" . $contratos[$posCont]["documentoDeudor1"] . "'");
                $contratos[$posCont]["Tercero_idDeudor1"] = $tercero->idTercero;

                $tercero->idTercero = 0;
                if (!empty($contratos[$posCont]["documentoDeudor2"]))
                    $tercero->ConsultarTercero("documentoTercero = '" . $contratos[$posCont]["documentoDeudor2"] . "'");
                $contratos[$posCont]["Tercero_idDeudor2"] = $tercero->idTercero;

                $mediopago->idMedioPago = 0;
                if (!empty($contratos[$posCont]["codigoAlternoMedioPago"]))
                    $mediopago->ConsultarMedioPago("codigoAlternoMedioPago = '" . $contratos[$posCont]["codigoAlternoMedioPago"] . "'");
                $contratos[$posCont]["MedioPago_idMedioPago"] = $mediopago->idMedioPago;

                $producto->idProducto = 0;
                if (!empty($contratos[$posCont]["codigoAlternoProducto"]))
                    $producto->ConsultarIdProducto("codigoAlternoProducto = '" . $contratos[$posCont]["codigoAlternoProducto"] . "' or
                                                                                                                    referenciaProducto = '" . $contratos[$posCont]["codigoAlternoProducto"] . "' or
                                                                                                                    codigoBarrasProducto = '" . $contratos[$posCont]["codigoAlternoProducto"] . "'");
                $contratos[$posCont]["Producto_idProducto"] = $producto->idProducto;


                if (!empty($contratos[$posCont]["codigoAlternoTurno"]))
                    $datoturno = $turno->ConsultarVistaTurno("codigoAlternoTurno = '" . $contratos[$posCont]["codigoAlternoTurno"] . "'");
                $contratos[$posCont]["Turno_idTurno"] = isset($datoturno[0]["idTurno"]) ? $datoturno[0]["idTurno"] : 0;


                $gruponomina->idGrupoNomina = 0;
                if (!empty($contratos[$posCont]["codigoAlternoGrupoNomina"]))
                    $gruponomina->ConsultarIdGrupoNomina("codigoAlternoGrupoNomina = '" . $contratos[$posCont]["codigoAlternoGrupoNomina"] . "'");
                $contratos[$posCont]["GrupoNomina_idGrupoNomina"] = $gruponomina->idGrupoNomina;


                if (!empty($contratos[$posCont]["codigoAlternoTipoCotizante"]))
                    $datosTipo = $tipocotizante->ConsultarVistaTipoCotizante("codigoAlternoTipoCotizante = '" . $contratos[$posCont]["codigoAlternoTipoCotizante"] . "'");
                $contratos[$posCont]["TipoCotizante_idTipoCotizante"] = isset($datosTipo[0]['idTipoCotizante']) ? $datosTipo[0]['idTipoCotizante'] : 0;


                if (isset($contratos[$posCont]["codigoAlternoSubtipoCotizante"]))
                    $datosSubtipo = $subtipocotizante->ConsultarVistaSubtipoCotizante("codigoAlternoSubtipoCotizante = '" . $contratos[$posCont]["codigoAlternoSubtipoCotizante"] . "'");
                $contratos[$posCont]["SubtipoCotizante_idSubtipoCotizante"] = isset($datosSubtipo[0]['idSubtipoCotizante']) ? $datosSubtipo[0]['idSubtipoCotizante'] : 0;
                //formula duracion dias
                //Grupo de Nomina

                $fila++;
            }


            //print_r($contratos);
            // luego de que tenemos la matriz de terceros llena, las enviamos al proceso de importacion de terceros
            // para que los valide e importe al sistema
            $retorno = $this->llenarPropiedadesContrato($contratos);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($contratos);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));

            return $retorno;
        }

        function llenarPropiedadesContrato($contratos) {
            // instanciamos la clase producto y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'contrato.class.php';
            $contrato = new Contrato();

            $retorno = array();
            // contamos los registros del array de productos
            $totalreg = (isset($contratos[0]["codigoAlternoContrato"]) ? count($contratos) : 0);
            for ($i = 0; $i < $totalreg; $i++) {

                $nuevoserrores = $this->validarContrato($contratos[$i]["codigoAlternoContrato"], $i, $contratos);
                $totalerr = count($nuevoserrores);

                if (!isset($nuevoserrores[0]["error"])) {
                    // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
                    $contrato->Contrato();

                    $contrato->idContrato = (isset($contratos[$i]["idContrato"]) ? $contratos[$i]["idContrato"] : 0 );
                    $contrato->TipoContrato_idTipoContrato = (isset($contratos[$i]["TipoContrato_idTipoContrato"]) ? $contratos[$i]["TipoContrato_idTipoContrato"] : 0 );
                    $contrato->codigoAlternoContrato = (isset($contratos[$i]["codigoAlternoContrato"]) ? $contratos[$i]["codigoAlternoContrato"] : '');
                    $contrato->SolicitudProducto_idSolicitudProducto = (isset($contratos[$i]["SolicitudProducto_idSolicitudProducto"]) ? $contratos[$i]["SolicitudProducto_idSolicitudProducto"] : 0 );
                    $contrato->fechaElaboracionContrato = (isset($contratos[$i]["fechaElaboracionContrato"]) ? $contratos[$i]["fechaElaboracionContrato"] : '' );
                    $contrato->fechaIncrementoContrato = (isset($contratos[$i]["fechaIncrementoContrato"]) ? $contratos[$i]["fechaIncrementoContrato"] : '' );
                    $contrato->porcentajeIncrementoContrato = (isset($contratos[$i]["porcentajeIncrementoContrato"]) ? $contratos[$i]["porcentajeIncrementoContrato"] : 0 );
                    $contrato->Producto_idProducto = (isset($contratos[$i]["Producto_idProducto"]) ? $contratos[$i]["Producto_idProducto"] : 0 );
                    $contrato->Tercero_idAsesor = (isset($contratos[$i]["Tercero_idAsesor"]) ? $contratos[$i]["Tercero_idAsesor"] : 0 );
                    $contrato->Tercero_idAuxiliar = (isset($contratos[$i]["Tercero_idAuxiliar"]) ? $contratos[$i]["Tercero_idAuxiliar"] : 0 );
                    $contrato->Tercero_idCliente = (isset($contratos[$i]["Tercero_idCliente"]) ? $contratos[$i]["Tercero_idCliente"] : 0 );
                    $contrato->GrupoNomina_idGrupoNomina = (isset($contratos[$i]["GrupoNomina_idGrupoNomina"]) ? $contratos[$i]["GrupoNomina_idGrupoNomina"] : 0 );
                    $contrato->Tercero_idDeudor1 = (isset($contratos[$i]["Tercero_idDeudor1"]) ? $contratos[$i]["Tercero_idDeudor1"] : 0 );
                    $contrato->Tercero_idDeudor2 = (isset($contratos[$i]["Tercero_idDeudor2"]) ? $contratos[$i]["Tercero_idDeudor2"] : 0 );
                    $contrato->fechaInicioContrato = (isset($contratos[$i]["fechaInicioContrato"]) ? $contratos[$i]["fechaInicioContrato"] : '' );
                    $contrato->duracionContrato = (isset($contratos[$i]["duracionContrato"]) ? $contratos[$i]["duracionContrato"] : 0 );
                    $contrato->fechaVencimientoContrato = (isset($contratos[$i]["fechaVencimientoContrato"]) ? $contratos[$i]["fechaVencimientoContrato"] : '' );
                    $contrato->periodoPruebaContrato = (isset($contratos[$i]["periodoPruebaContrato"]) ? $contratos[$i]["periodoPruebaContrato"] : 0 );
                    $contrato->jornadaLaboralDiaContrato = (isset($contratos[$i]["jornadaLaboralDiaContrato"]) ? $contratos[$i]["jornadaLaboralDiaContrato"] : 0 );
                    $contrato->registraTurnoContrato = (isset($contratos[$i]["registraTurnoContrato"]) ? $contratos[$i]["registraTurnoContrato"] : 0 );
                    $contrato->Turno_idTurno = (isset($contratos[$i]["Turno_idTurno"]) ? $contratos[$i]["Turno_idTurno"] : 0 );
                    $contrato->TipoCotizante_idTipoCotizante = (isset($contratos[$i]["TipoCotizante_idTipoCotizante"]) ? $contratos[$i]["TipoCotizante_idTipoCotizante"] : 0 );
                    $contrato->SubtipoCotizante_idSubtipoCotizante = (isset($contratos[$i]["SubtipoCotizante_idSubtipoCotizante"]) ? $contratos[$i]["SubtipoCotizante_idSubtipoCotizante"] : 0 );
                    $contrato->tipoSalarioContrato = (isset($contratos[$i]["tipoSalarioContrato"]) ? $contratos[$i]["tipoSalarioContrato"] : '' );
                    $contrato->valorContrato = (isset($contratos[$i]["valorContrato"]) ? $contratos[$i]["valorContrato"] : 0 );
                    $contrato->fechaTerminacionContrato = (isset($contratos[$i]["fechaTerminacionContrato"]) ? $contratos[$i]["fechaTerminacionContrato"] : '' );
                    $contrato->CausaTerminacionContrato_idCausaTerminacionContrato = (isset($contratos[$i]["CausaTerminacionContrato_idCausaTerminacionContrato"]) ? $contratos[$i]["CausaTerminacionContrato_idCausaTerminacionContrato"] : 0 );
                    $contrato->periodicidadPagoContrato = (isset($contratos[$i]["periodicidadPagoContrato"]) ? $contratos[$i]["periodicidadPagoContrato"] : '' );
                    $contrato->diaPagoContrato = (isset($contratos[$i]["diaPagoContrato"]) ? $contratos[$i]["diaPagoContrato"] : 0 );
                    $contrato->tipoPagoContrato = (isset($contratos[$i]["tipoPagoContrato"]) ? $contratos[$i]["tipoPagoContrato"] : '' );
                    $contrato->MedioPago_idMedioPago = (isset($contratos[$i]["MedioPago_idMedioPago"]) ? $contratos[$i]["MedioPago_idMedioPago"] : 0 );
                    $contrato->textoContrato = (isset($contratos[$i]["textoContrato"]) ? $contratos[$i]["textoContrato"] : '' );
                    $contrato->observacionContrato = (isset($contratos[$i]["observacionContrato"]) ? $contratos[$i]["observacionContrato"] : '' );
                    $contrato->estadoContrato = (isset($contratos[$i]["estadoContrato"]) ? $contratos[$i]["estadoContrato"] : 'ACTIVO' );

                    // si el array de contratos tiene fecha de vencimiento de prorroga, debemos llenar un array con los datos apra que la clase lo inserte
                    // consultamos la fecha de prorroga para ver si es superior a la ultima prorroga del contrato, para insertarla
                    $contrato->idContratoProrroga = array();
                    $contrato->numeroContratoProrroga = array();
                    $contrato->fechaVencimientoContratoProrroga = array();

                    // consultamos todas las prorrogas para pasarlas al array, asi no se pierden al grabar
                    $prorrogas = $contrato->ConsultarVistaContratoProrroga("Contrato_idContrato = $contrato->idContrato");
                    for ($pro = 0; $pro < count($prorrogas); $pro++) {
                        $contrato->idContratoProrroga[$pro] = $prorrogas[$pro]["idContratoProrroga"];
                        $contrato->numeroContratoProrroga[$pro] = $prorrogas[$pro]["numeroContratoProrroga"];
                        $contrato->fechaVencimientoContratoProrroga[$pro] = $prorrogas[$pro]["fechaVencimientoContratoProrroga"];
                    }


                    if (!empty($contratos[$i]["fechaVencimientoContratoProrroga"])) {
                        $datopro = $contrato->ConsultarVistaContratoProrroga("Contrato_idContrato = $contrato->idContrato", "", "MAX(fechaVencimientoContratoProrroga) as fechaVencimientoContratoProrroga");
                        //print_r($datopro);
                    }

                    if (isset($datopro[0]["fechaVencimientoContratoProrroga"]) and ( $datopro[0]["fechaVencimientoContratoProrroga"] == '' or $datopro[0]["fechaVencimientoContratoProrroga"] < $contratos[$i]["fechaVencimientoContratoProrroga"])) {

                        $contrato->idContratoProrroga[$pro] = 0;
                        $contrato->numeroContratoProrroga[$pro] = 0;
                        $contrato->fechaVencimientoContratoProrroga[$pro] = $contratos[$i]["fechaVencimientoContratoProrroga"];
                    } else {

                        if ($contratos[$i]["fechaVencimientoContratoProrroga"] != '') {
                            $contrato->idContratoProrroga[$pro] = 0;
                            $contrato->numeroContratoProrroga[$pro] = 0;
                            $contrato->fechaVencimientoContratoProrroga[$pro] = $contratos[$i]["fechaVencimientoContratoProrroga"];
                        }
                    }

                    // cada que llenamos un contrato, lo cargamos a la base de datos
                    // si el id esta lleno, lo actualizamos, si esta vacio lo insertamos
                    if ($contratos[$i]['idContrato'] == 0) {
                        $contrato->AdicionarContrato();
                    } else {
                        $contrato->ModificarContrato();
                    }
                } else {
                    $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                }
            }

            return $retorno;
        }

        function validarContrato($codigoAlternoContrato, $x, $contratos) {


            require_once 'parametrosnomina.class.php';
            $param = new ParametrosNomina();
            $datN = $param->ConsultarVistaParametrosNomina();

            $swerror = true;
            $errores = array();
            $linea = 0;
            $campo = "^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$";  // /i case-insensitivo
            // validamos que el numero de contrato no este repetido en el mismo archivo de excel
            for ($i = 0; $i < count($contratos); $i++) {
                if ($contratos[$i]["codigoAlternoContrato"] == $codigoAlternoContrato and $i != $x) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El Numero de Contrato ' . $codigoAlternoContrato . ' esta repetido en el archivo, lineas ' . ($x + 4) . ' y ' . ($i + 4);
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["codigoAlternoTipoContrato"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoTipoContrato"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El tipo de contrato debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["codigoAlternoContrato"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoContrato"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El numero del contrato debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["codigoAlternoProducto"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoProducto"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El codigo alterno del producto debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }


            if ($contratos[$x]["codigoAlternoProducto"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoProducto"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El codigo alterno del producto debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            ///ACA VA LA VALIDACION DE LA FECHA

            if ($contratos[$x]["documentoAsesor"] != '') {
                if (ereg($campo, $contratos[$x]["documentoAsesor"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El documento del asesor debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["documentoAuxiliar"] != '') {
                if (ereg($campo, $contratos[$x]["documentoAuxiliar"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'EL documento del auxiliar debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["tipoSalarioContrato"] == 'ERROR') {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'El tipo de pago debe estar entre (A = ANTICIPADO,VE = VENCIDO)';
                $swerror = false;
                $linea++;
            }

            if ($contratos[$x]["tipoSalarioContrato"] == 'ERROR') {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'El tipo de salario debe estar entre (F = Fijo, V =  Variable, I = Integral)';
                $swerror = false;
                $linea++;
            }

            if ($contratos[$x]["periodicidadPagoContrato"] == 'ERROR') {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'La periodicidad de pago debe estar entre (Semanal, Decadal, Catorcenal, Quincenal, Mensual, Bimestral, Trimestral, Semestral, Anual)';
                $swerror = false;
                $linea++;
            }

            if ($contratos[$x]["registraTurnoContrato"] == 'ERROR') {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'El registro de horario debe estar entre (0 = Jornada Laboral, 1 = Control de Ingreso, 2 = Bitacora)';
                $swerror = false;
                $linea++;
            }

            if ($contratos[$x]["documentoCliente"] != '') {
                if (ereg($campo, $contratos[$x]["documentoCliente"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'EL documento del cliente debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["codigoAlternoGrupoNomina"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoGrupoNomina"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'EL codigo alterno del grupo de nomina debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["documentoDeudor1"] != '') {
                if (ereg($campo, $contratos[$x]["documentoDeudor1"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'EL documento del deudor 1 debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["documentoDeudor2"] != '') {
                if (ereg($campo, $contratos[$x]["documentoDeudor2"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'EL documento del deudor 2 debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }


            if ($contratos[$x]["periodoPruebaContrato"] != '') {
                if (!is_numeric($contratos[$x]["periodoPruebaContrato"])) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El periodo de prueba debe ser un valor numerico';
                    $swerror = false;
                    $linea++;
                } else {

                }
            }

            //// fecha inicio del contrato


            if ($contratos[$x]["duracionContrato"] != '') {
                if (!is_numeric($contratos[$x]["duracionContrato"])) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'La duracion del contrato debe ser un valor numerico (MESES)';
                    $swerror = false;
                    $linea++;
                }
            }


            //fecha vencimiento

            if ($contratos[$x]["valorContrato"] != '') {
                if (!is_numeric($contratos[$x]["valorContrato"]) or ! is_double($contratos[$x]["valorContrato"])) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El salario digitado es invalido ' . $contratos[$x]["valorContrato"];
                    $swerror = false;
                    $linea++;
                }
            } else {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'El salario no puede estar en blanco';
                $swerror = false;
                $linea++;
            }



            if ($contratos[$x]["jornadaLaboralDiaContrato"] != '') {
                if (!is_numeric($contratos[$x]["jornadaLaboralDiaContrato"]) or ! is_double($contratos[$x]["jornadaLaboralDiaContrato"]) or $contratos[$x]["jornadaLaboralDiaContrato"] <= 0 or $contratos[$x]["jornadaLaboralDiaContrato"] > $datN[0]['jornadaOrdinariaDiaParametrosNomina']) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'Jornada laboral invalida';
                    $swerror = false;
                    $linea++;
                }
            }



            if ($contratos[$x]["codigoAlternoTurno"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoTurno"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El codigo alterno del turno debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["codigoAlternoTipoCotizante"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoTipoCotizante"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El codigo alterno del tipo cotizante debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["codigoAlternoSubtipoCotizante"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoSubtipoCotizante"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El codigo alterno del sub cotizante debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }




            if ($contratos[$x]["codigoAlternoMedioPago"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoMedioPago"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El codigo alterno del medio de pago debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["codigoAlternoCausaTerminacionContrato"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoCausaTerminacionContrato"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El codigo de la causacion de terminacion debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }



            if ($contratos[$x]["codigoAlternoPlantillaContrato"] != '') {
                if (ereg($campo, $contratos[$x]["codigoAlternoPlantillaContrato"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'El codigo de la planilla del contrato  debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }

            if ($contratos[$x]["observacionContrato"] != '') {
                if (ereg($campo, $contratos[$x]["observacionContrato"]) === false) {
                    $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                    $errores[$linea]["error"] = 'La observacion del contrato debe ser un dato alfanumerico';
                    $swerror = false;
                    $linea++;
                }
            }


            // Verificamos que el tipo de contrato exista
            if (!isset($contratos[$x]["TipoContrato_idTipoContrato"]) or ( isset($contratos[$x]["TipoContrato_idTipoContrato"]) and $contratos[$x]["TipoContrato_idTipoContrato"] == 0)) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                if ($contratos[$x]["codigoAlternoTipoContrato"] == '')
                    $errores[$linea]["error"] = 'El C&oacute;digo de Tipo de Contrato esta vac&iacute;o';
                else
                    $errores[$linea]["error"] = 'El C&oacute;digo de Tipo de Contrato ' . $contratos[$x]["codigoAlternoTipoContrato"] . ' no existe';
                $swerror = false;
                $linea++;
            }


            if ((trim($contratos[$x]["codigoAlternoProducto"])) != '' and $contratos[$x]["Producto_idProducto"] == 0) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'El C&oacute;digo del Producto ' . $contratos[$x]["codigoAlternoProducto"] . ' no existe';
                $swerror = false;
                $linea++;
            }

            // Verificamos que el asesor exista
            if (!isset($contratos[$x]["Tercero_idAsesor"]) or ( isset($contratos[$x]["Tercero_idAsesor"]) and $contratos[$x]["Tercero_idAsesor"] == 0)) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                if ($contratos[$x]["documentoAsesor"] == '')
                    $errores[$linea]["error"] = 'El documento del Asesor esta vac&iacute;o';
                else
                    $errores[$linea]["error"] = 'El documento del Asesor  ' . $contratos[$x]["documentoAsesor"] . ' no existe';
                $swerror = false;
                $linea++;
            }

            // Verificamos que el Cliente/Empleado exista
            if (!isset($contratos[$x]["Tercero_idCliente"]) or ( isset($contratos[$x]["Tercero_idCliente"]) and $contratos[$x]["Tercero_idCliente"] == 0)) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                if ($contratos[$x]["documentoCliente"] == '')
                    $errores[$linea]["error"] = 'El documento del Cliente/Empleado esta vac&iacute;o';
                else
                    $errores[$linea]["error"] = 'El documento del Cliente/Empleado  ' . $contratos[$x]["documentoCliente"] . ' no existe';
                $swerror = false;
                $linea++;
            }

            // Verificamos que el Auxiliar exista
            if (!isset($contratos[$x]["Tercero_idAuxiliar"]) or ( isset($contratos[$x]["Tercero_idAuxiliar"]) and $contratos[$x]["Tercero_idAuxiliar"] == 0 and $contratos[$x]["documentoAuxiliar"] != '')) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'El documento  del Auxiliar  ' . $contratos[$x]["documentoAuxiliar"] . ' no existe';
                $swerror = false;
                $linea++;
            }

            // Verificamos que el Deudor 1 exista
            if (!isset($contratos[$x]["Tercero_idDeudor1"]) or ( isset($contratos[$x]["Tercero_idDeudor1"]) and $contratos[$x]["Tercero_idDeudor1"] == 0 and $contratos[$x]["documentoDeudor1"] != '')) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'El documento del Deudor 1  ' . $contratos[$x]["documentoDeudor1"] . ' no existe';
                $swerror = false;
                $linea++;
            }

            // Verificamos que el Deudor 2 exista
            if (!isset($contratos[$x]["Tercero_idDeudor2"]) or ( isset($contratos[$x]["Tercero_idDeudor2"]) and $contratos[$x]["Tercero_idDeudor2"] == 0 and $contratos[$x]["documentoDeudor2"] != '')) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                $errores[$linea]["error"] = 'El documento  del Deudor 2  ' . $contratos[$x]["documentoDeudor2"] . ' no existe';
                $swerror = false;
                $linea++;
            }

            // Verificamos que el Grupo de Nomina exista
            if (!isset($contratos[$x]["GrupoNomina_idGrupoNomina"]) or ( isset($contratos[$x]["GrupoNomina_idGrupoNomina"]) and $contratos[$x]["GrupoNomina_idGrupoNomina"] == 0)) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                if ($contratos[$x]["codigoAlternoGrupoNomina"] == '')
                    $errores[$linea]["error"] = 'El grupo de nomina esta vac&iacute;o';
                else
                    $errores[$linea]["error"] = 'El grupo de nomina ' . $contratos[$x]["codigoAlternoGrupoNomina"] . ' no existe';
                $swerror = false;
                $linea++;
            }

            // Verificamos que el Turno exista
            /* if (!isset($contratos[$x]["Turno_idTurno"]) or (isset($contratos[$x]["Turno_idTurno"]) and $contratos[$x]["Turno_idTurno"] == 0))
              {
              $errores[$linea]["codigoAlternoTurno"] = $contratos[$x]["codigoAlternoTurno"];
              if($contratos[$x]["codigoAlternoTurno"] == '')
              $errores[$linea]["error"] = 'El Turno esta vac&iacute;o';
              else
              $errores[$linea]["error"] = 'El Turno  ' . $contratos[$x]["codigoAlternoTurno"] . ' no existe';
              $swerror = false;
              $linea++;
              } */

            // Verificamos que el Cliente/Empleado exista
            if (!isset($contratos[$x]["MedioPago_idMedioPago"]) or ( isset($contratos[$x]["MedioPago_idMedioPago"]) and $contratos[$x]["MedioPago_idMedioPago"] == 0)) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                if ($contratos[$x]["codigoAlternoMedioPago"] == '')
                    $errores[$linea]["error"] = 'El Medio de Pago  esta vac&iacute;o';
                else
                    $errores[$linea]["error"] = 'El Medio de Pago  ' . $contratos[$x]["codigoAlternoMedioPago"] . ' no existe';
                $swerror = false;
                $linea++;
            }

        if (!isset($contratos[$x]["Turno_idTurno"]) or ( isset($contratos[$x]["Turno_idTurno"]) and $contratos[$x]["Turno_idTurno"] == 0)) {
                $errores[$linea]["codigoAlternoContrato"] = $contratos[$x]["codigoAlternoContrato"];
                if ($contratos[$x]["codigoAlternoTurno"] == '')
                    $errores[$linea]["error"] = 'El codigo del turno esta vac&iacute;o';
                else
                    $errores[$linea]["error"] = 'El turno con codigo ' . $contratos[$x]["codigoAlternoTurno"] . ' no existe';
                $swerror = false;
                $linea++;
            }

            return $errores;
        }

        function ImportarMovimientoComercialExcel($ruta) {
            set_time_limit(0);

            //echo $ruta;
            require_once('../clases/documentocomercial.class.php');
            if (!isset($documentocomercial))
                $documentocomercial = new Documento();
            require_once('../clases/documentoconcepto.class.php');
            $documentoconcepto = new DocumentoConcepto();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();

            require_once('../clases/moneda.class.php');
            $moneda = new Moneda();

            require_once('../clases/formapago.class.php');
            $formapago = new FormaPago();

            require_once('../clases/mediopago.class.php');
            $mediodepago = new MedioPago();

            require_once('../clases/incoterm.class.php');
            $incoterm = new Incoterm();

            require_once('../clases/producto.class.php');
            $producto = new Producto();

            require_once('../clases/periodo.class.php');
            $periodo = new Periodo();

            require_once('../clases/bodega.class.php');
            $bodega = new Bodega();

            require_once('../clases/centrocosto.class.php');

            $centrocosto = new CentroCosto();

            require_once('../clases/listaprecio.class.php');
            $listaprecio = new ListaPrecio();



            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            // importamos las pestaña del detalle
            $objReader->setLoadSheetsOnly('Movimiento Comercial');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);

            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != NULL) {
                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue();
                $documentoAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();
                // por cada numero de documento diferente, llenamos el encabezado
                $posEnc++;

                // para cada registro del encabezado recorremos las columnas desde la 0 hasta la 23
                for ($columna = 0; $columna <= 28; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // luego recorremos las columnas desde la 34 hasta la 37 para obetener los datos de pie de pagina
                for ($columna = 39; $columna <= 42; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }


                // convertimos la fecha de formato EXCEL a formato UNIX
                $fechaReal = $encabezado[$posEnc]["fechaElaboracionMovimiento"];

                $encabezado[$posEnc]["fechaElaboracionMovimiento"] = (gettype($fechaReal) == 'double' or gettype($fechaReal) == 'integer' and $fechaReal > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal))) : $encabezado[$posEnc]["fechaElaboracionMovimiento"];

                $fechaReal1 = $encabezado[$posEnc]["fechaMinimaMovimiento"];

                $encabezado[$posEnc]["fechaMinimaMovimiento"] = (gettype($fechaReal1) == 'double' or gettype($fechaReal1) == 'integer' and $fechaReal1 > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal1))) : $encabezado[$posEnc]["fechaMinimaMovimiento"];
                $fechaReal2 = $encabezado[$posEnc]["fechaMaximaMovimiento"];

                $encabezado[$posEnc]["fechaMaximaMovimiento"] = (gettype($fechaReal2) == 'double' or gettype($fechaReal2) == 'integer' and $fechaReal2 > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal2))) : $encabezado[$posEnc]["fechaMaximaMovimiento"];

                //echo $encabezado[$posEnc]["fechaElaboracionMovimiento"];
                // cada que llenemos un encabezado, hacemos las verificaciones de codigos necesarioos
                // validamos el documento
                if (!empty($encabezado[$posEnc]["codigoDocumento"]))
                    $datos = $documentocomercial->ConsultarVistaDocumento("codigoAlternoDocumento =  '" . $encabezado[$posEnc]["codigoDocumento"] . "'");
                $encabezado[$posEnc]["Documento_idDocumento"] = isset($datos[0]["idDocumento"]) ? $datos[0]["idDocumento"] : 0;
                $encabezado[$posEnc]["estadoWMSMovimiento"] = isset($datos[0]["idDocumento"]) ? $datos[0]["estadoWMSDocumento"] : 'ABIERTO';
                //			$encabezado[$posEnc]["ModeloContable_idModeloContable"] = isset($datos[0]["ModeloContable_idModeloContable"]) ? $datos[0]["ModeloContable_idModeloContable"] : 0;
                // validamos el concepto de documento
                $documentoconcepto->idDocumentoConcepto = 0;
                if (!empty($encabezado[$posEnc]["codigoConceptoDocumento"]))
                    $documentoconcepto->ConsultarDocumentoConcepto("codigoAlternoDocumentoConcepto =  '" . $encabezado[$posEnc]["codigoConceptoDocumento"] . "'");
                $encabezado[$posEnc]["DocumentoConcepto_idDocumentoConcepto"] = $documentoconcepto->idDocumentoConcepto;

                $centrocosto->idCentroCosto = 0;
                if (!empty($encabezado[$posEnc]["codigoCentroCosto"]))
                    $centrocosto->ConsultarCentroCosto("codigoAlternoCentroCosto =  '" . $encabezado[$posEnc]["codigoCentroCosto"] . "'");
                $encabezado[$posEnc]["CentroCosto_idCentroCosto"] = $centrocosto->idCentroCosto;

                // validamos el periodo
                $periodo->idPeriodo = 0;
                if (!empty($encabezado[$posEnc]["fechaElaboracionMovimiento"]))
                    $periodo->ConsultarPeriodo("fechaInicialPeriodo <=  '" . $encabezado[$posEnc]["fechaElaboracionMovimiento"] .
                            "' and fechaFinalPeriodo >=  '" . $encabezado[$posEnc]["fechaElaboracionMovimiento"] .
                            "'  and estadoPeriodo = 'ACTIVO' and estadoComercialPeriodo = 'ACTIVO'");
                $encabezado[$posEnc]["Periodo_idPeriodo"] = $periodo->idPeriodo;

                // consultamos el EAN del Cliente en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($encabezado[$posEnc]["eanTercero"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $encabezado[$posEnc]["eanTercero"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["eanTercero"] . "'");
                $encabezado[$posEnc]["Tercero_idTercero"] = $tercero->idTercero;

                $tercero->idTercero = 0;
                $tercero->ConsultarIdTercero("documentoTercero = '" . $encabezado[$posEnc]["eanTercero"] . "' and tipoTercero not like '%*18*%'");
                $encabezado[$posEnc]["Tercero_idPrincipal"] = $tercero->idTercero;

                // consultamos el EAN del Sitio de entrega en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($encabezado[$posEnc]["eanEntrega"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $encabezado[$posEnc]["eanEntrega"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["eanEntrega"] . "'");
                $encabezado[$posEnc]["Tercero_idEntrega"] = $tercero->idTercero;

                // consultamos el EAN del Transportador en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($encabezado[$posEnc]["eanTransportador"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $encabezado[$posEnc]["eanTransportador"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["eanTransportador"] . "'");
                $encabezado[$posEnc]["Tercero_idTransportador"] = $tercero->idTercero;

                // consultamos el EAN del Vendedor en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($encabezado[$posEnc]["eanVendedor"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $encabezado[$posEnc]["eanVendedor"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["eanVendedor"] . "'");
                if ($tercero->idTercero == 0) {
                    require_once('db.class.php');
                    require_once('conf.class.php');
                    $sql = "Select Tercero_idVendedor from Tercero where idTercero = " . $encabezado[$posEnc]["Tercero_idPrincipal"];
                    $bd = Db::getInstance();
                    $idVendedor = $bd->ConsultarVista($sql);
                    $encabezado[$posEnc]["Tercero_idVendedor"] = $idVendedor[0]['Tercero_idVendedor'];
                } else {
                    $encabezado[$posEnc]["Tercero_idVendedor"] = $tercero->idTercero;
                }

                // consultamos la moneda  en la tabla de monedas para obtener el ID
                $moneda->idMoneda = 0;
                if (!empty($encabezado[$posEnc]["codigoMoneda"]))
                    $moneda->ConsultarMoneda("codigoAlternoMoneda = '" . $encabezado[$posEnc]["codigoMoneda"] . "'");
                $encabezado[$posEnc]["Moneda_idMoneda"] = $moneda->idMoneda;

                if ($encabezado[$posEnc]['tasaCambioMovimiento'] == 0) {
                    $sql = "Select pideTasaCambioMoneda,fechaMonedaTasaCambio,tasaMonedaTasaCambio
                                            from Moneda mon
                                            left join MonedaTasaCambio tasa
                                            on mon.idMoneda = tasa.Moneda_idMoneda
                                            where mon.idMoneda = " . $encabezado[$posEnc]["Moneda_idMoneda"] . " and fechaMonedaTasaCambio = '" . $encabezado[$posEnc]["fechaElaboracionMovimiento"] . "'";
                    $bd = Db::getInstance();
                    $tasacambio = $bd->ConsultarVista($sql);
                    if (isset($tasacambio[0]['tasaMonedaTasaCambio'])) {
                        $encabezado[$posEnc]["tasaCambioMovimiento"] = $tasacambio[0]['tasaMonedaTasaCambio'];
                    }
                }
                // consultamos la forma de pago  en la tabla de formapago para obtener el ID
                $formapago->idFormaPago = 0;
                if (!empty($encabezado[$posEnc]["codigoFormaPago"]))
                    $formapago->ConsultarFormaPago("codigoAlternoFormaPago = '" . $encabezado[$posEnc]["codigoFormaPago"] . "'");
                $encabezado[$posEnc]["FormaPago_idFormaPago"] = $formapago->idFormaPago;

                // consultamos el Incoterm  en la tabla de incoterms para obtener el ID
                $incoterm->idIncoterm = 0;
                if (!empty($encabezado[$posEnc]["codigoIncoterm"]))
                    $incoterm->ConsultarIncoterm("codigoAlternoIncoterm = '" . $encabezado[$posEnc]["codigoIncoterm"] . "'");
                $encabezado[$posEnc]["Incoterm_idIncoterm"] = $incoterm->idIncoterm;

                $listaprecio->idListaPrecio = 0;
                if (!empty($encabezado[$posEnc]["codigoListaPrecio"]))
                    $listaprecio->ConsultarListaPrecio("codigoAlternoListaPrecio = '" . $encabezado[$posEnc]["codigoListaPrecio"] . "'");

                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != NULL and
                $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() and
                $documentoAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue()) {
                    // por cada numero de documento diferente, llenamos el detelle
                    $posDet++;


                    // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                    $detalle[$posDet]["numeroMovimiento"] = $numeroAnt;
                    $detalle[$posDet]["Documento_idDocumento"] = $encabezado[$posEnc]["Documento_idDocumento"];
                    // para cada registro del detalle recorremos las columnas desde la 24 hasta la 33
                    for ($columna = 29; $columna <= 38; $columna++) {
                        // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    }

                    // consultamos el Id de la Bodega Origen
                    $bodega->idBodega = 0;
                    if (!empty($detalle[$posDet]["codigoBodegaOrigen"]))
                        $bodega->ConsultarBodega("codigoAlternoBodega = '" . $detalle[$posDet]["codigoBodegaOrigen"] . "' ");
                    $detalle[$posDet]["Bodega_idBodegaOrigen"] = $bodega->idBodega;

                    // consultamos el Id de la Bodega Destino
                    $bodega->idBodega = 0;
                    if (!empty($detalle[$posDet]["codigoBodegaDestino"]))
                        $bodega->ConsultarBodega("codigoAlternoBodega = '" . $detalle[$posDet]["codigoBodegaDestino"] . "' ");
                    $detalle[$posDet]["Bodega_idBodegaDestino"] = $bodega->idBodega;


                    // consultamos el Id del producto
                    $producto->idProducto = 0;
                    if (!empty($detalle[$posDet]["eanProducto"]))
                        $producto->ConsultarProducto("codigoBarrasProducto = '" . $detalle[$posDet]["eanProducto"] . "' or referenciaProducto = '" . $detalle[$posDet]["eanProducto"] . "'");
                    $detalle[$posDet]["Producto_idProducto"] = $producto->idProducto;

                    // si no encontramos el producto, lo buscamos en sus homologos por tercero
                    if ($detalle[$posDet]["Producto_idProducto"] == 0) {
                        $datos = $producto->ConsultarVistaProductoTercero("codigoBarrasProductoTercero = '" . $detalle[$posDet]["eanProducto"] . "' or referenciaProductoTercero = '" . $detalle[$posDet]["eanProducto"] . "' or pluProductoTercero = '" . $detalle[$posDet]["eanProducto"] . "'", "", "Producto_idProducto", "");
                        if (isset($datos[0]["Producto_idProducto"]))
                            $detalle[$posDet]["Producto_idProducto"] = $datos[0]["Producto_idProducto"];
                    }

                    // consultamos el id del producto serie que se le da en el detalle
                    //$producto->idProductoSerie = 0;
                    $datoserie = $producto->ConsultarVistaProductoSerie("numeroProductoSerie = " . $detalle[$posDet]['numeroSerie']);
                    $detalle[$posDet]["ProductoSerie_idProductoSerie"] = isset($datoserie[0]['idProductoSerie']) != '' ? $datoserie[0]['idProductoSerie'] : 0;


                    // consultamos el EAN del Almacen de predistribucion en la tabla de terceros para obtener el ID
                    $tercero->idTercero = 0;
                    if (!empty($detalle[$posDet]["eanAlmacen"]) and $encabezado[$posEnc]["tipoMovimiento"] == 'PREDISTRIBUIDA')
                        $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $detalle[$posDet]["eanAlmacen"] . "' or codigoAlterno1Tercero = '" . $detalle[$posDet]["eanAlmacen"] . "'");
                    $detalle[$posDet]["Tercero_idAlmacen"] = $tercero->idTercero;

                    // llenamos el precio de lista con el valor Bruto
                    $detalle[$posDet]["precioListaMovimientoDetalle"] = $detalle[$posDet]["valorBrutoMovimientoDetalle"];

                    if ($encabezado[$posDet]['tipoReferenciaInternoMovimiento'] != '' and $encabezado[$posDet]['numeroReferenciaInternoMovimiento'] != '') {
                        $sql = "Select idMovimiento
                                                from Movimiento mov
                                                left join Documento doc
                                                on mov.Documento_idDocumento = doc.idDocumento
                                                left join MovimientoDetalle det
                                                on mov.idMovimiento = det.Movimiento_idMovimiento
                                                where doc.codigoAlternoDocumento = '" . $encabezado[$posDet]['tipoReferenciaInternoMovimiento'] . "' and mov.numeroMovimiento = '" . $encabezado[$posDet]['numeroReferenciaInternoMovimiento'] . "' and det.Producto_idProducto = " . $detalle[$posDet]["Producto_idProducto"];
                        $bd = Db::getInstance();
                        $interno = $bd->ConsultarVista($sql);
                        if (isset($interno[0]['idMovimiento'])) {
                            $detalle[$posDet]["Movimiento_idDocumentoRef"] = $interno[0]['idMovimiento'];
                        }
                    }

                    // agregamos al array de detalle los valores de impuestos y retenciones y el valor total del producto
                    // pasamos a la siguiente fila
                    $fila++;
                }
            }

            $mediopago = array();
            //if($objPHPExcel->setLoadSheetsOnly('Medio Pago') != NULL)
            //{
            // importamos las pestaña de los kits
            $objReader->setLoadSheetsOnly('Medio Pago');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);


            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $posMed = -1;

            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != NULL) {

                $posMed++;

                // para cada registro de medios de pago recorremos las columnas desde la 0 hasta la 6
                for ($columna = 0; $columna <= 6; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $mediopago[$posMed][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }


                $mediodepago->idMedioPago = 0;
                if (!empty($mediopago[$posMed]["codigoAlternoMedioPago"]))
                    $mediodepago->ConsultarIdMedioPago("codigoAlternoMedioPago = '" . $mediopago[$posMed]["codigoAlternoMedioPago"] . "'");
                $mediopago[$posMed]["MedioPago_idMedioPago"] = $mediodepago->idMedioPago;
                $fila++;
            }
            //}
            //
                                        /*
              print_r($encabezado);
              print_r($detalle);
              print_r($mediopago);
              return;
             */
            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos comerciales
            // para que las valide e importe al sistema, para esto recorremos cada orden de compra importada para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array

            $retorno = $this->llenarPropiedadesMovimiento($encabezado, $detalle, $origen = 'interface', $listaprecio = '', $listapreciotercero = '', $mediopago);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($encabezado);
            unset($detalle);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
            return $retorno;
        }

        function ImportarInventarioProductoProcesoExcel($ruta) {
            set_time_limit(0);

            /**
             * ESPACIO PARA LA INSTANCIACION DE ENTIDADES
             */
            require_once('tercero.class.php');
            require_once('centroproduccion.class.php');
            require_once('producto.class.php');
            require_once('ordenproduccion.class.php');

            $tercero = new Tercero();
            $centroproduccion = new CentroProduccion();
            $producto = new Producto();
            $ordenProduccion = new OrdenProduccion();

            /**
             * Se hace la inclucion de la clase PHPExcel
             */
            include('PHPExcel/Classes/PHPExcel.php');

            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }

            // importamos las pestaña del detalle
            $objReader->setLoadSheetsOnly('Hoja1');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);

            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezadoOP = array();
            $encabezadoRem = array();
            $encabezadoRec = array();
            $posEnc = -1;
            $posEncRem = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalleOP = array();
            $centroproduccionOP = array();
            $detalleRem = array();
            $detalleRec = array();
            $posDet = -1;
            $posRuta = -1;
            $posDetRem = -1;

            $fila = 3;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {
                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();
                $primeraReferencia = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue();

                // por cada numero de documento diferente, llenamos el encabezado
                $posEnc++;

                // para cada centro de produccion diferente que pueda tener la OP, tenemos un contador para que cambie el numero de la remision
                $contCP = 0;

                // para cada registro del encabezadode la OP recorremos las columnas desde la 0 hasta la 2
                for ($columna = 0; $columna <= 2; $columna++) {
                    // en la fila 1 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 1)->getValue();
                    $encabezadoOP[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // convertimos la fecha de formato EXCEL a formato UNIX
                $fechaReal = $encabezadoOP[$posEnc]["fechaElaboracionOrdenProduccion"];
                $encabezadoOP[$posEnc]["fechaElaboracionOrdenProduccion"] = (gettype($fechaReal) == 'double' or gettype($fechaReal) == 'integer' and $fechaReal > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal))) : $encabezadoOP[$posEnc]["fechaElaboracionOrdenProduccion"];


                //                    echo 'ID DE LA ORDEN'.$encabezadoOP[$posEnc]["idOrdenProduccion"];
                // llenamos los campos fijos del encabezado
                $encabezadoOP[$posEnc]["conceptoOrdenProduccion"] = 'OP';
                $encabezadoOP[$posEnc]["prefijoOrdenProduccion"] = '';
                $encabezadoOP[$posEnc]["sufijoOrdenProduccion"] = '';
                $encabezadoOP[$posEnc]["nombreOrdenProduccion"] = '';
                $encabezadoOP[$posEnc]["fechaEstimadaEntregaOrdenProduccion"] = $encabezadoOP[$posEnc]["fechaElaboracionOrdenProduccion"];
                $encabezadoOP[$posEnc]["fechaRealEntregaOrdenProduccion"] = $encabezadoOP[$posEnc]["fechaElaboracionOrdenProduccion"];
                $encabezadoOP[$posEnc]["tipoOrdenProduccion"] = 'STOCK';
                $encabezadoOP[$posEnc]["responsableOrdenProduccion"] = 'Inventario Inicial PP';
                $encabezadoOP[$posEnc]["prioridadOrdenProduccion"] = 'ALTA';
                $encabezadoOP[$posEnc]["metodoProgramacionOrdenProduccion"] = 'MANUAL';
                $encabezadoOP[$posEnc]["observacionOrdenProduccion"] = '';
                $encabezadoOP[$posEnc]["totalUnidadesOrdenProduccion"] = 0;
                $encabezadoOP[$posEnc]["estadoOrdenProduccion"] = 'PROCESO';
                $encabezadoOP[$posEnc]["numeroLiquidacionCorteOrdenProduccion"] = '';

                // con la primera referencia de la OP, buscamos la referencia base de la ficha tecnica y la ruta de procesos de la misma
                $sql = "select idFichaTecnica, referenciaBaseFichaTecnica
                                            From Producto P
                                            Left join FichaTecnica F
                                            on P.FichaTecnica_idFichaTecnica = F.idFichaTecnica
                                            Where P.referenciaProducto = '$primeraReferencia'";
                $bd = Db::getInstance();
                $datoFicha = $bd->ConsultarVista($sql);

                //               echo $sql;
                // si existe la ficha, llenamos el campo de descripcionde la OP con la referencia base y luego consultamos la ruta de procesos
                if (isset($datoFicha[0]["idFichaTecnica"])) {
                    $encabezadoOP[$posEnc]["nombreOrdenProduccion"] = $datoFicha[0]["referenciaBaseFichaTecnica"];

                    // COnsultamos la ruta de procesos
                    $sql = "select ordenFichaTecnicaCentroProduccion, CentroProduccion_idCentroProduccion, observacionFichaTecnicaCentroProduccion
                                                From FichaTecnicaCentroProduccion
                                                Where FichaTecnica_idFichaTecnica = " . $datoFicha[0]["idFichaTecnica"];
                    $bd = Db::getInstance();
                    $datoFicha = $bd->ConsultarVista($sql);

                    //    echo $sql;
                    //                    return;
                    //
                    // Si existen los datos de la ruta de procesos, las pasamso al array de ruta de procesos ($centroproduccionOP)
                    if (isset($datoFicha[0]["ordenFichaTecnicaCentroProduccion"])) {
                        // recorremos cada centro de produccionde la ruta
                        for ($ruta = 0; $ruta < count($datoFicha); $ruta++) {
                            $posRuta++;
                            $centroproduccionOP[$posRuta]["numeroOrdenProduccion"] = $encabezadoOP[$posEnc]["numeroOrdenProduccion"];
                            $centroproduccionOP[$posRuta]["ordenOrdenProduccionCentroProduccion"] = $datoFicha[$ruta]["ordenFichaTecnicaCentroProduccion"];
                            $centroproduccionOP[$posRuta]["CentroProduccion_idCentroProduccion"] = $datoFicha[$ruta]["CentroProduccion_idCentroProduccion"];
                            $centroproduccionOP[$posRuta]["observacionOrdenProduccionCentroProduccion"] = $datoFicha[$ruta]["observacionFichaTecnicaCentroProduccion"];
                        }
                    }
                }

                // cada que llenemos un encabezado, hacemos las verificaciones de codigos necesarioos
                // Buscamos el id de Cliente
                $tercero->idTercero = 0;
                if (!empty($encabezadoOP[$posEnc]["documentoTercero"]))
                    $datos = $tercero->ConsultarIdTercero("documentoTercero =  '" . $encabezadoOP[$posEnc]["documentoTercero"] . "'");
                $encabezadoOP[$posEnc]["Tercero_idTercero"] = $tercero->idTercero;

                //                echo  $encabezadoRem[$posEnc]["codigoAltenoCentroProduccion"].'<br>';
                //                echo  $encabezadoRem[$posEnc]["CentroProduccion_idCentroProduccion"].'<br>';


                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL and
                $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue()) {

                    $cpAnterior = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, $fila)->getValue();

                    $contCP++;
                    $numeroRemision = $encabezadoOP[$posEnc]["numeroOrdenProduccion"] . "-$contCP";

                    $posEncRem++;
                    // el mismo numero de OP, lo tomamos como numero de remision
                    $encabezadoRem[$posEncRem]["numeroProduccionEntrega"] = $numeroRemision;
                    $encabezadoRem[$posEncRem]["numeroOrdenProduccion"] = $encabezadoOP[$posEnc]["numeroOrdenProduccion"];
                    $encabezadoRem[$posEncRem]["fechaElaboracionProduccionEntrega"] = $encabezadoOP[$posEnc]["fechaElaboracionOrdenProduccion"];

                    // para cada registro del encabezado de la remision recorremos las columnas desde la 5 hasta la 7
                    for ($columna = 5; $columna <= 7; $columna++) {
                        // en la fila 1 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 1)->getValue();
                        $encabezadoRem[$posEncRem][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    }


                    // Si el registro tiene cantidad recibida, entonces llenamos encabezado de RECIBO
                    $encabezadoRec[$posEncRem]["numeroProduccionRecibo"] = $encabezadoOP[$posEnc]["numeroOrdenProduccion"];
                    $encabezadoRec[$posEncRem]["fechaElaboracionProduccionRecibo"] = date("Y-m-d", strtotime($encabezadoOP[$posEnc]["fechaElaboracionOrdenProduccion"]));
                    $encabezadoRec[$posEncRem]["estadoLoteProduccionRecibo"] = 'PARCIAL';
                    $encabezadoRec[$posEncRem]["tipoDocumentoProduccionRecibo"] = '01';

                    // Buscamos el id de proveedor
                    $tercero->idTercero = 0;
                    if (!empty($encabezadoRem[$posEncRem]["documentoProveedor"]))
                        $datos = $tercero->ConsultarIdTercero("documentoTercero =  '" . $encabezadoRem[$posEncRem]["documentoProveedor"] . "'");
                    $encabezadoRem[$posEncRem]["Tercero_idTercero"] = $tercero->idTercero;

                    // Buscamos el id del centro de produccion
                    $tercero->idCentroProduccion = 0;
                    if (!empty($encabezadoRem[$posEncRem]["codigoAltenoCentroProduccion"]))
                        $datos = $centroproduccion->ConsultarIdCentroProduccion("codigoAlternoCentroProduccion =  '" . $encabezadoRem[$posEncRem]["codigoAltenoCentroProduccion"] . "'");
                    $encabezadoRem[$posEncRem]["CentroProduccion_idCentroProduccion"] = $centroproduccion->idCentroProduccion;


                    while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL and
                    $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() and
                    $cpAnterior == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, $fila)->getValue()) {

                        $referencia = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue();
                        if (!empty($referencia))
                            $datos = $producto->ConsultarIdProducto("referenciaProducto =  '" . $referencia . "'");


                        //*******************************
                        // DETALLE DE LA OP
                        //*******************************
                        // con la referencia y el nunmero de la OP, buscamos en el array de detalleOP, si ya existe, para sumar la cantidad, sino, lo adicionamos
                        $totres = count($detalleOP);

                        $pos = 0;
                        $sw = false;

                        for ($res = 0; $res < $totres; $res++) {
                            if ($detalleOP[$res]["numeroOrdenProduccion"] == $numeroAnt and
                                    $detalleOP[$res]["referenciaProducto"] == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue()) {
                                $pos = $res;
                                $sw = true;
                            }
                        }

                        // si no encontro la cuenta, inserta el registro, si la encuentra, acumula los valores
                        if ($sw == false) {
                            $posDet++;
                            $detalleOP[$posDet]["numeroOrdenProduccion"] = $numeroAnt;
                            $detalleOP[$posDet]["referenciaProducto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue();
                            $detalleOP[$posDet]["Producto_idProducto"] = $producto->idProducto;
                            $detalleOP[$posDet]["cantidadOrdenProduccionDetalle"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $fila)->getValue();
                        } else {
                            $detalleOP[$pos]["cantidadOrdenProduccionDetalle"] += $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $fila)->getValue();
                        }


                        //*******************************
                        // DETALLE DE LA REMISION
                        //*******************************
                        // con la referencia, el centro de produccion y el numero de la OP, buscamos en el array de detalleRem, si ya existe, para sumar la cantidad, sino, lo adicionamos
                        $totres = count($detalleRem);

                        $pos = 0;
                        $sw = false;

                        for ($res = 0; $res < $totres; $res++) {
                            if ($detalleRem[$res]["numeroProduccionEntrega"] == $numeroRemision and
                                    $detalleRem[$res]["codigoAltenoCentroProduccion"] == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, $fila)->getValue() and
                                    $detalleRem[$res]["referenciaProducto"] == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue()) {
                                $pos = $res;
                                $sw = true;
                            }
                        }

                        // si no encontro la cuenta, inserta el registro, si la encuentra, acumula los valores
                        if ($sw == false) {
                            $posDetRem++;
                            $detalleRem[$posDetRem]["numeroProduccionEntrega"] = $numeroRemision;
                            $detalleRem[$posDetRem]["codigoAltenoCentroProduccion"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, $fila)->getValue();
                            $detalleRem[$posDetRem]["referenciaProducto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue();
                            $detalleRem[$posDetRem]["Producto_idProducto"] = $producto->idProducto;
                            $detalleRem[$posDetRem]["cantidadProduccionEntregaProducto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(7, $fila)->getValue();
                        } else {
                            $detalleRem[$pos]["cantidadProduccionEntregaProducto"] += $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(7, $fila)->getValue();
                        }


                        //                    //*******************************
                        //                    // DETALLE DE LA RECIBO
                        //                    //*******************************
                        //                    // con la referencia, el centro de produccion y el numero de la OP, buscamos en el array de detalleRem, si ya existe, para sumar la cantidad, sino, lo adicionamos
                        //                    $totres = count($detalleRec);
                        //
                    //                    $pos = 0;
                        //                    $sw = false;
                        //
                    //                    for ($res = 0; $res < $totres; $res++)
                        //                    {
                        //                        if ($detalleRec[$res]["numeroProduccionRecibo"] == $numeroAnt and
                        //                            $detalleRec[$res]["codigoAltenoCentroProduccion"] == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, $fila)->getValue() and
                        //                            $detalleRec[$res]["referenciaProducto"] == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue())
                        //                        {
                        //                            $pos = $res;
                        //                            $sw = true;
                        //                        }
                        //                    }
                        //
                    //                    // si no encontro la cuenta, inserta el registro, si la encuentra, acumula los valores
                        //                    if ($sw == false)
                        //                    {
                        //                        $posDetRem++;
                        //                        $detalleRec[$posDetRem]["numeroProduccionRecibo"] = $numeroAnt;
                        //                        $detalleRec[$posDetRem]["codigoAltenoCentroProduccion"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(5, $fila)->getValue();
                        //                        $detalleRec[$posDetRem]["referenciaProducto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue();
                        //                        $detalleRec[$posDetRem]["Producto_idProducto"] = $producto->idProducto;
                        //                        $detalleRec[$posDetRem]["cantidadProduccionReciboProducto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(8, $fila)->getValue();
                        //                    } else
                        //                    {
                        //                        $detalleRec[$pos]["cantidadProduccionReciboProducto"] += $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(8, $fila)->getValue();
                        //                    }



                        $fila++;
                    }
                }
            }


            $this->llenarPropiedadesInventarioProductoProceso($encabezadoOP, $detalleOP, $centroproduccionOP, $encabezadoRem, $detalleRem, $encabezadoRec, $detalleRec);

            //        print_r($encabezadoRem);
            //        print_r($detalleRem);


            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($encabezadoOP);
            unset($detalleOP);

            return $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));

            //return ;
        }

        function llenarPropiedadesInventarioProductoProceso($encabezadoOP, $detalleOP, $centroproduccionOP, $encabezadoRem, $detalleRem, $encabezadoRec, $detalleRec) {

            // instanciamos la clase movimiento y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'ordenproduccion.class.php';
            $ordenproduccion = new OrdenProduccion();

            require_once 'remisionproduccion.class.php';
            $produccionentrega = new ProduccionEntrega();

            require_once 'producto.class.php';
            $producto = new Producto();

            require_once 'periodo.class.php';
            $periodo = new Periodo();

            require_once 'reciboproduccion.class.php';
            $recibo = new ProduccionRecibo();

            $retorno = array();
            // contamos los registros del encabezado
            $totalreg = (isset($encabezadoOP[0]["numeroOrdenProduccion"]) ? count($encabezadoOP) : 0);

            //  print_r($encabezadoOP);
            //echo '<br>';
            //                 print_r($encabezadoOP);
            //print_r($detalleOP);
            //                 exit();
            //echo '<br>';
            // $nuevoserrores = $this->validarInventarioProductoProceso($encabezadoOP, $detalleOP);
            //                print_r($nuevoserrores);
            //                exit();
            ////
            if (!isset($nuevoserrores[0]["error"]) or $nuevoserrores[0]["error"] == '') {
                //                    echo "<br>entra1<br>";
                //                    return;
                for ($i = 0; $i < $totalreg; $i++) {
                    //                    echo "<br> entra for encabezado<br>";
                    //echo " entra if isset ";
                    // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
                    $ordenproduccion->OrdenProduccion();
                    //echo 'registros de detalle '.count($ordenproduccion->idMovimientoDetalle)."<br><br>";
                    $ordenproduccion->idOrdenProduccion = (isset($encabezadoOP[$i]["idOrdenProduccion"]) ? $encabezadoOP[$i]["idOrdenProduccion"] : 0);

                    // echo $encabezadoOP[$i]["numeroOrdenProduccion"];

                    $ordenproduccion->conceptoOrdenProduccion = (isset($encabezadoOP[$i]["conceptoOrdenProduccion"]) ? $encabezadoOP[$i]["conceptoOrdenProduccion"] : 'OP');
                    $ordenproduccion->prefijoOrdenProduccion = (isset($encabezadoOP[$i]["prefijoOrdenProduccion"]) ? $encabezadoOP[$i]["prefijoOrdenProduccion"] : '');
                    $ordenproduccion->numeroOrdenProduccion = (isset($encabezadoOP[$i]["numeroOrdenProduccion"]) ? $encabezadoOP[$i]["numeroOrdenProduccion"] : '');
                    $ordenproduccion->sufijoOrdenProduccion = (isset($encabezadoOP[$i]["sufijoOrdenProduccion"]) ? $encabezadoOP[$i]["sufijoOrdenProduccion"] : '');
                    $ordenproduccion->fechaElaboracionOrdenProduccion = (isset($encabezadoOP[$i]["fechaElaboracionOrdenProduccion"]) ? date("Y-m-d", strtotime($encabezadoOP[$i]["fechaElaboracionOrdenProduccion"])) : date("Y-m-d"));

                    //                        $ordenproduccion->Periodo_idPeriodo = (isset($encabezadoOP[$i]["fechaElaboracionOrdenProduccion"]) ? date("Y-m-d",strtotime($encabezadoOP[$i]["fechaElaboracionOrdenProduccion"])) : date("Y-m-d"));

                    $ordenproduccion->nombreOrdenProduccion = (isset($encabezadoOP[$i]["nombreOrdenProduccion"]) ? $encabezadoOP[$i]["nombreOrdenProduccion"] : '');
                    $ordenproduccion->fechaEstimadaEntregaOrdenProduccion = (isset($encabezadoOP[$i]["fechaEstimadaEntregaOrdenProduccion"]) ? $encabezadoOP[$i]["fechaEstimadaEntregaOrdenProduccion"] : $ordenproduccion->fechaElaboracionOrdenProduccion);
                    $ordenproduccion->fechaRealEntregaOrdenProduccion = (isset($encabezadoOP[$i]["fechaRealEntregaOrdenProduccion"]) ? $encabezadoOP[$i]["fechaRealEntregaOrdenProduccion"] : $ordenproduccion->fechaElaboracionOrdenProduccion);
                    $ordenproduccion->Tercero_idTercero = (isset($encabezadoOP[$i]["Tercero_idTercero"]) ? $encabezadoOP[$i]["Tercero_idTercero"] : 0);
                    $ordenproduccion->tipoOrdenProduccion = (isset($encabezadoOP[$i]["tipoOrdenProduccion"]) ? $encabezadoOP[$i]["tipoOrdenProduccion"] : 'STOCK');
                    $ordenproduccion->documentoReferenciaOrdenProduccion = (isset($encabezadoOP[$i]["documentoReferenciaOrdenProduccion"]) ? $encabezadoOP[$i]["documentoReferenciaOrdenProduccion"] : '');
                    $ordenproduccion->responsableOrdenProduccion = (isset($encabezadoOP[$i]["responsableOrdenProduccion"]) ? $encabezadoOP[$i]["responsableOrdenProduccion"] : '');
                    $ordenproduccion->prioridadOrdenProduccion = (isset($encabezadoOP[$i]["prioridadOrdenProduccion"]) ? $encabezadoOP[$i]["prioridadOrdenProduccion"] : 'ALTA');
                    $ordenproduccion->metodoProgramacionOrdenProduccion = (isset($encabezadoOP[$i]["metodoProgramacionOrdenProduccion"]) ? $encabezadoOP[$i]["metodoProgramacionOrdenProduccion"] : 'MANUAL');
                    $ordenproduccion->observacionOrdenProduccion = (isset($encabezadoOP[$i]["observacionOrdenProduccion"]) ? $encabezadoOP[$i]["observacionOrdenProduccion"] : '');
                    $ordenproduccion->totalUnidadesOrdenProduccion = 0;
                    $ordenproduccion->estadoOrdenProduccion = (isset($encabezadoOP[$i]["estadoOrdenProduccion"]) ? $encabezadoOP[$i]["estadoOrdenProduccion"] : 'PROGRAMADA');
                    $ordenproduccion->numeroLiquidacionCorteOrdenProduccion = (isset($encabezadoOP[$i]["numeroLiquidacionCorteOrdenProduccion"]) ? $encabezadoOP[$i]["numeroLiquidacionCorteOrdenProduccion"] : '');


                    // por cada registro del encabezado, recorremos el detalle para obtener solo los datos del mismo numero de orden de produccion del encabezado, con estos
                    // llenamos arrays por cada campo
                    $totaldet = (isset($detalleOP[0]["numeroOrdenProduccion"]) ? count($detalleOP) : 0);

                    // llevamos un contador de registros por cada producto del detalle
                    $registroact = 0;

                    //var_dump($detalleOP);

                    for ($j = 0; $j < $totaldet; $j++) {
                        //                            echo "<br> entra for detalle <br>";
                        if (isset($encabezadoOP[$i]["numeroOrdenProduccion"]) and
                                isset($detalleOP[$j]["numeroOrdenProduccion"]) and
                                $encabezadoOP[$i]["numeroOrdenProduccion"] == $detalleOP[$j]["numeroOrdenProduccion"]) {

                            //                                    echo '<br>'.'ENTRA DETALLE'.'<br>';
                            //                                    echo '<br>'.$detalleOP[$j]["Producto_idProducto"].'<br>';
                            //echo "id lista: ".$nuevoserrores[$j]["ListaPrecio_idListaPrecioDetalle"]." precio de lista: ".$nuevoserrores[$j]["precioListaMovimientoDetalle"]." valor bruto: ".$nuevoserrores[$j]["valorBrutoMovimientoDetalle"]."<br>";

                            $ordenproduccion->idOrdenProduccionProducto[$registroact] = 0;
                            $ordenproduccion->Producto_idProducto[$registroact] = (isset($detalleOP[$j]["Producto_idProducto"]) ? $detalleOP[$j]["Producto_idProducto"] : 0);
                            $ordenproduccion->cantidadOrdenProduccionProducto[$registroact] = (isset($detalleOP[$j]["cantidadOrdenProduccionDetalle"]) ? $detalleOP[$j]["cantidadOrdenProduccionDetalle"] : 0);
                            $ordenproduccion->Movimiento_idDocumentoRefProd[$registroact] = (isset($detalleOP[$j]["Movimiento_idDocumentoRefProd"]) ? $detalleOP[$j]["Movimiento_idDocumentoRefProd"] : 0);
                            $ordenproduccion->totalUnidadesOrdenProduccion += $ordenproduccion->cantidadOrdenProduccionProducto[$registroact];

                            $registroact++;
                        }
                    }

                    //                        echo 'total orden'.$ordenproduccion->totalUnidadesOrdenProduccion.'<br>';
                    //                        var_dump($ordenproduccion->Producto_idProducto);
                    // por cada registro del encabezado, recorremos el detalle de ruta de procesos para obtener solo los datos del mismo numero de orden de produccion del encabezado, con estos
                    // llenamos arrays por cada campo
                    $totaldet = (isset($centroproduccionOP[0]["numeroOrdenProduccion"]) ? count($centroproduccionOP) : 0);

                    // llevamos un contador de registros por cada producto del detalle
                    $registroact = 0;


                    //                        var_dump($centroproduccionOP);


                    for ($j = 0; $j < $totaldet; $j++) {
                        //                            echo "<br> entra for detalle <br>";
                        if (isset($encabezadoOP[$i]["numeroOrdenProduccion"]) and
                                isset($centroproduccionOP[$j]["numeroOrdenProduccion"]) and
                                $encabezadoOP[$i]["numeroOrdenProduccion"] == $centroproduccionOP[$j]["numeroOrdenProduccion"]) {
                            //echo "id lista: ".$nuevoserrores[$j]["ListaPrecio_idListaPrecioDetalle"]." precio de lista: ".$nuevoserrores[$j]["precioListaMovimientoDetalle"]." valor bruto: ".$nuevoserrores[$j]["valorBrutoMovimientoDetalle"]."<br>";

                            $ordenproduccion->idOrdenProduccionCentroProduccion[$registroact] = 0;
                            $ordenproduccion->ordenOrdenProduccionCentroProduccion[$registroact] = (isset($centroproduccionOP[$j]["ordenOrdenProduccionCentroProduccion"]) ? $centroproduccionOP[$j]["ordenOrdenProduccionCentroProduccion"] : 0);
                            $ordenproduccion->CentroProduccion_idCentroProduccion_2[$registroact] = (isset($centroproduccionOP[$j]["CentroProduccion_idCentroProduccion"]) ? $centroproduccionOP[$j]["CentroProduccion_idCentroProduccion"] : 0);
                            $ordenproduccion->observacionOrdenProduccionCentroProduccion[$registroact] = (isset($centroproduccionOP[$j]["observacionOrdenProduccionCentroProduccion"]) ? $centroproduccionOP[$j]["observacionOrdenProduccionCentroProduccion"] : '');

                            $registroact++;
                        }
                    }

                    $ordenproduccion->ConsultarIdOrdenProduccion("numeroOrdenProduccion = '" . $encabezadoOP[$i]["numeroOrdenProduccion"] . "'");

                    //                        echo 'ENTRAAAAAAAAAAAAAAAAAAAAAAA hasta fin ';
                    //                        print_r($encabezadoOP);
                    //                        print_r($detalleOP);
                    //

                    if ($ordenproduccion->idOrdenProduccion == 0) {
                        //                            echo 'entra1';
                        $ordenproduccion->AdicionarOrdenProduccion('si');

                        //                                 echo '<br>'.$ordenproduccion->idOrdenProduccion.'<br>';
                    } else {
                        //                            echo 'entra2';
                        $ordenproduccion->ModificarOrdenProduccion();
                    }


                    // **********************************
                    //        R E M I S I O N E S
                    // **********************************

                    $totalrem = (isset($encabezadoRem[0]["numeroProduccionEntrega"]) ? count($encabezadoRem) : 0);

                    // llevamos un contador de registros por cada producto del detalle
                    //                        var_dump($detalleOP);

                    for ($r = 0; $r < $totalrem; $r++) {
                        //                            echo "<br> entra for detalle <br>";
                        if (isset($encabezadoRem[$r]["numeroProduccionEntrega"]) and
                                $encabezadoOP[$i]["numeroOrdenProduccion"] == $encabezadoRem[$r]["numeroOrdenProduccion"]) {


                            // Luego de insertar las ordenes de produccion, procedemos a insertar las remisiones y recibos que dependen de ella
                            $produccionentrega->ProduccionEntrega();
                            //echo 'registros de detalle '.count($ordenproduccion->idMovimientoDetalle)."<br><br>";
                            $produccionentrega->idProduccionEntrega = (isset($encabezadoRem[$r]["idProduccionEntrega"]) ? $encabezadoRem[$r]["idProduccionEntrega"] : 0);

                            $produccionentrega->prefijoProduccionEntrega = (isset($encabezadoRem[$r]["prefijoProduccionEntrega"]) ? $encabezadoRem[$r]["prefijoProduccionEntrega"] : '');
                            $produccionentrega->numeroProduccionEntrega = (isset($encabezadoRem[$r]["numeroProduccionEntrega"]) ? $encabezadoRem[$r]["numeroProduccionEntrega"] : '');
                            $produccionentrega->sufijoProduccionEntrega = (isset($encabezadoRem[$r]["sufijoProduccionEntrega"]) ? $encabezadoRem[$r]["sufijoProduccionEntrega"] : '');
                            $produccionentrega->fechaElaboracionProduccionEntrega = (isset($encabezadoRem[$r]["fechaElaboracionProduccionEntrega"]) ? date("Y-m-d", strtotime($encabezadoRem[$r]["fechaElaboracionProduccionEntrega"])) : date("Y-m-d"));
                            $produccionentrega->fechaEstimadaInicioProduccionEntrega = (isset($encabezadoRem[$r]["fechaEstimadaInicioProduccionEntrega"]) ? $encabezadoRem[$r]["fechaEstimadaInicioProduccionEntrega"] : $produccionentrega->fechaElaboracionProduccionEntrega);
                            $produccionentrega->fechaEstimadaFinProduccionEntrega = (isset($encabezadoRem[$r]["fechaEstimadaFinProduccionEntrega"]) ? $encabezadoRem[$r]["fechaEstimadaFinProduccionEntrega"] : $produccionentrega->fechaElaboracionProduccionEntrega);
                            $produccionentrega->fechaEstimadaReciboProduccionEntrega = (isset($encabezadoRem[$r]["fechaEstimadaReciboProduccionEntrega"]) ? $encabezadoRem[$r]["fechaEstimadaReciboProduccionEntrega"] : $produccionentrega->fechaElaboracionProduccionEntrega);
                            $produccionentrega->Tercero_idTercero = (isset($encabezadoRem[$r]["Tercero_idTercero"]) ? $encabezadoRem[$r]["Tercero_idTercero"] : 0);
                            $produccionentrega->CentroProduccion_idCentroProduccion = (isset($encabezadoRem[$r]["CentroProduccion_idCentroProduccion"]) ? $encabezadoRem[$r]["CentroProduccion_idCentroProduccion"] : 0);
                            $produccionentrega->OrdenProduccion_idOrdenProduccion = $ordenproduccion->idOrdenProduccion;
                            $produccionentrega->prioridadProduccionEntrega = (isset($encabezadoRem[$r]["prioridadProduccionEntrega"]) ? $encabezadoRem[$r]["prioridadProduccionEntrega"] : 'ALTA');
                            $produccionentrega->totalUnidadesProduccionEntrega = (isset($encabezadoRem[$r]["totalUnidadesProduccionEntrega"]) ? $encabezadoRem[$r]["totalUnidadesProduccionEntrega"] : 0);
                            $produccionentrega->observacionProduccionEntrega = (isset($encabezadoRem[$r]["observacionProduccionEntrega"]) ? $encabezadoRem[$r]["observacionProduccionEntrega"] : '');
                            $produccionentrega->reprocesoProduccionEntrega = (isset($encabezadoRem[$r]["reprocesoProduccionEntrega"]) ? $encabezadoRem[$r]["reprocesoProduccionEntrega"] : 0);
                            $produccionentrega->cobrarProduccionEntrega = (isset($encabezadoRem[$r]["cobrarProduccionEntrega"]) ? $encabezadoRem[$r]["cobrarProduccionEntrega"] : 0);
                            $produccionentrega->estadoProduccionEntrega = (isset($encabezadoRem[$r]["estadoProduccionEntrega"]) ? $encabezadoRem[$r]["estadoProduccionEntrega"] : 'ACTIVO');
                            $produccionentrega->Movimiento_idMovimientoMaterial = (isset($encabezadoRem[$r]["Movimiento_idMovimientoMaterial"]) ? $encabezadoRem[$r]["Movimiento_idMovimientoMaterial"] : 0);
                            $produccionentrega->TipoReproceso_idTipoReproceso = (isset($encabezadoRem[$r]["TipoReproceso_idTipoReproceso"]) ? $encabezadoRem[$r]["TipoReproceso_idTipoReproceso"] : 0);

                            //                        echo $encabezadoRem[$r]["CentroProduccion_idCentroProduccion"].'<br>';
                            //                        return;
                            $totaldet = (isset($detalleRem[0]["numeroProduccionEntrega"]) ? count($detalleRem) : 0);

                            //                                echo '<pre>';
                            //                                echo '<pre>'.print_r($detalleRem).'<pre>';
                            //                                echo '</pre>';
                            // llevamos un contador de registros por cada producto del detalle
                            //                        var_dump($detalleOP);
                            $registroact = 0;
                            for ($j = 0; $j < $totaldet; $j++) {
                                //                                    echo "<br> entra for detalle REMISION ".$encabezadoRem[$r]["numeroProduccionEntrega"]." cantidad ".$detalleRem[$j]["cantidadProduccionEntregaProducto"]." <br>";
                                if (isset($encabezadoRem[$r]["numeroProduccionEntrega"]) and
                                        isset($detalleRem[$j]["numeroProduccionEntrega"]) and
                                        $encabezadoRem[$r]["numeroProduccionEntrega"] == $detalleRem[$j]["numeroProduccionEntrega"]) {

                                    //                                            echo '<br>'.$detalleRem[$j]["cantidadProduccionEntregaProducto"].'<br>';

                                    $produccionentrega->idProduccionEntregaProducto[$registroact] = 0;
                                    //                                    $produccionentrega->ProduccionEntrega_idProduccionEntrega[$registroact] = (isset($detalleRem[$j]["ordenOrdenProduccionCentroProduccion"]) ? $detalleRem[$j]["ordenOrdenProduccionCentroProduccion"] : 0);
                                    $produccionentrega->Producto_idProducto[$registroact] = (isset($detalleRem[$j]["Producto_idProducto"]) ? $detalleRem[$j]["Producto_idProducto"] : 0);
                                    $produccionentrega->cantidadProduccionEntregaProducto[$registroact] = (isset($detalleRem[$j]["cantidadProduccionEntregaProducto"]) ? $detalleRem[$j]["cantidadProduccionEntregaProducto"] : 0);
                                    $produccionentrega->costoUnitarioProduccionEntregaProducto[$registroact] = (isset($detalleRem[$j]["costoUnitarioProduccionEntregaProducto"]) ? $detalleRem[$j]["costoUnitarioProduccionEntregaProducto"] : 0);
                                    $produccionentrega->costoTotalProduccionEntregaProducto[$registroact] = (isset($detalleRem[$j]["costoTotalProduccionEntregaProducto"]) ? $detalleRem[$j]["costoTotalProduccionEntregaProducto"] : 0);
                                    $produccionentrega->observacionProduccionEntregaProducto[$registroact] = (isset($detalleRem[$j]["observacionProduccionEntregaProducto"]) ? $detalleRem[$j]["observacionProduccionEntregaProducto"] : '');
                                    $produccionentrega->totalUnidadesProduccionEntrega += $produccionentrega->cantidadProduccionEntregaProducto[$registroact];
                                    $registroact++;
                                }
                            }

                            //                        echo 'TOTASL REM'.$produccionentrega->totalUnidadesProduccionEntrega.'<br>';
                            //                        echo '<pre>';
                            //                        echo '<pre>'.print_r($detalleRem).'</pre>';
                            //                        echo '</pre>';
                            //
                            //
                            //
                            //                        var_dump($produccionentrega->idProduccionEntregaProducto[$registroact]);
                            //
                            //                        return;

                            $produccionentrega->ConsultarIdProduccionEntrega("numeroProduccionEntrega = '" . $encabezadoRem[$r]["numeroProduccionEntrega"] . "'");


                            if ($produccionentrega->idProduccionEntrega == 0) {
                                //                            echo 'entra1';
                                $produccionentrega->AdicionarProduccionEntrega();
                            } else {
                                //                            echo 'entra2';
                                $produccionentrega->ModificarProduccionEntrega();
                            }


                            $produccionentrega->ConsultarIdProduccionEntrega("numeroProduccionEntrega = '" . $encabezadoRem[$r]["numeroProduccionEntrega"] . "'");
                        }
                    }
                }
            }
            //                            echo " entra else error ";
            $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
            //print_r($retorno);
            return $retorno;
        }

        function ImportarProductoComercialPrecioExcel($ruta) {
            set_time_limit(0);

            require_once('../clases/producto.class.php');
            $producto = new Producto();
            require_once('../clases/talla.class.php');
            $talla = new Talla();
            require_once('../clases/color.class.php');
            $color = new GrupoColor();
            require_once('../clases/fichatecnica.class.php');
            $fichatecnica = new FichaTecnica();
            require_once('../clases/marca.class.php');
            $marca = new Marca();
            require_once('../clases/tipoproducto.class.php');
            $tipoproducto = new TipoProducto();
            require_once('../clases/tiponegocio.class.php');
            $tiponegocio = new TipoNegocio();
            require_once('../clases/temporada.class.php');
            $temporada = new Temporada();
            require_once('../clases/estadoconservacion.class.php');
            $estadoconservacion = new EstadoConservacion();
            require_once('../clases/composicion.class.php');
            $composicion = new Composicion();
            require_once('../clases/posicionarancelaria.class.php');
            $posicionarancelaria = new PosicionArancelaria();
            require_once('../clases/pais.class.php');
            $pais = new Pais();
            require_once('../clases/categoria.class.php');
            $categoria = new Categoria();
            require_once('../clases/clima.class.php');
            $clima = new Clima();
            require_once('../clases/estrategia.class.php');
            $estrategia = new Estrategia();
            require_once('../clases/difusion.class.php');
            $difusion = new Difusion();
            require_once('../clases/seccion.class.php');
            $seccion = new Seccion();
            require_once('../clases/evento.class.php');
            $evento = new Evento();
            require_once('../clases/clienteobjetivo.class.php');
            $clienteobjetivo = new ClienteObjetivo();
            require_once('../clases/esquemaproducto.class.php');
            $esquemaproducto = new EsquemaProducto();
            require_once('../clases/codigobarras.class.php');
            $codigobarras = new CodigoBarras();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/bodega.class.php');
            $bodega = new Bodega();
            require_once('../clases/unidadmedida.class.php');
            $unidadmedida = new UnidadMedida();

            require_once '../clases/segmentooperacion.class.php';
            $segmento = new SegmentoOperacion();

            require_once '../clases/tono.class.php';
            $tono = new Tono();

            require_once '../clases/pinta.class.php';
            $pinta = new Pinta();

            require_once '../clases/calibreHilo.class.php';
            $calibre = new CalibreHilo();

            require_once('../clases/documentocomercial.class.php');
            if (!isset($documentocomercial))
                $documentocomercial = new Documento();
            require_once('../clases/documentoconcepto.class.php');
            $documentoconcepto = new DocumentoConcepto();

            require_once('../clases/moneda.class.php');
            $moneda = new Moneda();
            require_once('../clases/formapago.class.php');
            $formapago = new FormaPago();
            require_once('../clases/incoterm.class.php');
            $incoterm = new Incoterm();
            require_once('../clases/periodo.class.php');
            $periodo = new Periodo();
            require_once('../clases/componentecosto.class.php');
            $componentecosto = new ComponenteCosto();


            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            //echo $extension;
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            $objReader->setLoadSheetsOnly('datos');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $referencias = array();
            $posRef = -1;
            $inconsistencias = array();


            $fila = 15;
            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != NULL) {

                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                //--------------------------------------------------------
                //
                                            //  P  R  O  D  U  C  T  O  S
                //
                                            //--------------------------------------------------------
                // para cada registro del referencias recorremos las columnas desde la 0 hasta la 39
                for ($columna = 0; $columna <= 44; $columna++) {
                    // en la fila 12 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getDataType() == 'f')
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 13)->getCalculatedValue();
                    else
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 13)->getValue();

                    $referencias[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // llenamos le codigo del proveedor vacio
                $referencias[$posRef]["codigoProveedor"] = '';


                // tomamos las imagenes del producto y le adicionamos la ruta donde deben estar esta imagenes
                if ($referencias[$posRef]["imagen1Producto"] != '')
                    $referencias[$posRef]["imagen1Producto"] = "../fotosficha/catalogo/" . $referencias[$posRef]["imagen1Producto"];

                if ($referencias[$posRef]["imagen2Producto"] != '')
                    $referencias[$posRef]["imagen2Producto"] = "../fotosficha/catalogo/" . $referencias[$posRef]["imagen2Producto"];

                // cada que llenemos un referencias, hacemos las verificaciones de codigos necesarioos
                // verificamos cuales campos de la clasificacion del prodcuto estan llenos para armar la codificacion de clasificacion
                $referencias[$posRef]["clasificacionProducto"] = '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["PT"]) and $referencias[$posRef]["PT"] != NULL) ? '*01*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["IN"]) and $referencias[$posRef]["IN"] != NULL) ? '*04*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["MP"]) and $referencias[$posRef]["MP"] != NULL) ? '*02*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["SE"]) and $referencias[$posRef]["SE"] != NULL) ? '*03*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["CO"]) and $referencias[$posRef]["CO"] != NULL) ? '*06*' : '';
                $referencias[$posRef]["clasificacionProducto"] .= (!empty($referencias[$posRef]["KI"]) and $referencias[$posRef]["KI"] != NULL) ? '*07*' : '';

                // si el iva incluido esta lleno se cambia por un 1
                $referencias[$posRef]["ivaIncluidoProducto"] = (!empty($referencias[$posRef]["ivaIncluidoProducto"]) and $referencias[$posRef]["ivaIncluidoProducto"] != NULL ? 1 : 0);


                // consultamos el EAN del producto en la tabla de productos para obtener el ID
                $producto->idProducto = 0;
                if (!empty($referencias[$posRef]["referenciaProducto"]))
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $referencias[$posRef]["referenciaProducto"] . "'");
                $referencias[$posRef]["idProducto"] = $producto->idProducto;



                // buscamos si el codigo de barras ya existe con otro numero de referencia
                $referencias[$posRef]["errorbarras"] = 0;
                $producto->idProducto = 0;
                if (!empty($referencias[$posRef]["codigoBarrasProducto"])) {
                    $producto->ConsultarIdProducto("codigoBarrasProducto = '" . $referencias[$posRef]["codigoBarrasProducto"] . "' and referenciaProducto != '" . $referencias[$posRef]["referenciaProducto"] . "'");

                    if ($producto->idProducto > 0)
                        $referencias[$posRef]["errorbarras"] = $producto->idProducto;
                }


                // validamos la talla
                $talla->idTalla = 0;
                if (!empty($referencias[$posRef]["codigoTalla"]))
                    $talla->ConsultarTalla("codigoAlternoTalla =  '" . $referencias[$posRef]["codigoTalla"] . "'");
                $referencias[$posRef]["Talla_idTalla"] = $talla->idTalla;

                // validamos el Color
                if (!empty($referencias[$posRef]["codigoColor"]))
                    $datos = $color->ConsultarVistaColor("codigoAlternoColor =  '" . $referencias[$posRef]["codigoColor"] . "'");
                $referencias[$posRef]["Color_idColor"] = (isset($datos[0]["idColor"]) ? $datos[0]["idColor"] : 0);


                // validamos la referencia de la ficha tecnica
                $fichatecnica->idFichaTecnica = 0;
                if (!empty($referencias[$posRef]["referenciaBaseFichaTecnica"]))
                    $fichatecnica->ConsultarIdFichaTecnica("referenciaBaseFichaTecnica =  '" . $referencias[$posRef]["referenciaBaseFichaTecnica"] . "'");
                $referencias[$posRef]["FichaTecnica_idFichaTecnica"] = $fichatecnica->idFichaTecnica;

                // validamos la Marca
                $marca->idMarca = 0;
                if (!empty($referencias[$posRef]["codigoMarca"]))
                    $marca->ConsultarMarca("codigoAlternoMarca =  '" . $referencias[$posRef]["codigoMarca"] . "'");
                $referencias[$posRef]["Marca_idMarca"] = $marca->idMarca;

                // validamos el tipo de producto
                $tipoproducto->idTipoProducto = 0;
                if (!empty($referencias[$posRef]["codigoTipoProducto"]))
                    $tipoproducto->ConsultarTipoProducto("codigoAlternoTipoProducto =  '" . $referencias[$posRef]["codigoTipoProducto"] . "'");
                $referencias[$posRef]["TipoProducto_idTipoProducto"] = $tipoproducto->idTipoProducto;

                // validamos el tipo de negocio
                if (!empty($referencias[$posRef]["codigoTipoNegocio"]))
                    $datos = $tiponegocio->ConsultarVistaTipoNegocio("codigoAlternoTipoNegocio =  '" . $referencias[$posRef]["codigoTipoNegocio"] . "'");
                $referencias[$posRef]["TipoNegocio_idTipoNegocio"] = (isset($datos[0]["idTipoNegocio"]) ? $datos[0]["idTipoNegocio"] : 0);


                // validamos la Temporada
                $temporada->idTemporada = 0;
                if (!empty($referencias[$posRef]["codigoTemporada"]))
                    $temporada->ConsultarTemporada("codigoAlternoTemporada =  '" . $referencias[$posRef]["codigoTemporada"] . "'");
                $referencias[$posRef]["Temporada_idTemporada"] = $temporada->idTemporada;


                // validamos el estado de conservacion
                $estadoconservacion->idEstadoConservacion = 0;
                if (!empty($referencias[$posRef]["codigoEstadoConservacion"]))
                    $estadoconservacion->ConsultarEstadoConservacion("codigoAlternoEstadoConservacion =  '" . $referencias[$posRef]["codigoEstadoConservacion"] . "'");
                $referencias[$posRef]["EstadoConservacion_idEstadoConservacion"] = $estadoconservacion->idEstadoConservacion;

                // validamos la composicion
                $composicion->idComposicion = 0;
                if (!empty($referencias[$posRef]["codigoComposicion"]))
                    $composicion->ConsultarComposicion("codigoAlternoComposicion =  '" . $referencias[$posRef]["codigoComposicion"] . "'");
                $referencias[$posRef]["Composicion_idComposicion"] = $composicion->idComposicion;

                // validamos la Posicion Arancelaria
                $posicionarancelaria->idPosicionArancelaria = 0;
                if (!empty($referencias[$posRef]["codigoPosicionArancelaria"]))
                    $posicionarancelaria->ConsultarPosicionArancelaria("codigoAlternoPosicionArancelaria =  '" . $referencias[$posRef]["codigoPosicionArancelaria"] . "'");
                $referencias[$posRef]["PosicionArancelaria_idPosicionArancelaria"] = $posicionarancelaria->idPosicionArancelaria;

                // validamos el Pais de Origen
                $pais->idPais = 0;
                if (!empty($referencias[$posRef]["codigoPais"]))
                    $pais->ConsultarPais("codigoAlternoPais =  '" . $referencias[$posRef]["codigoPais"] . "'");
                $referencias[$posRef]["Pais_idPaisOrigen"] = $pais->idPais;

                // validamos la Categoria
                $categoria->idCategoria = 0;
                if (!empty($referencias[$posRef]["codigoCategoria"]))
                    $categoria->ConsultarCategoria("codigoAlterno1Categoria =  '" . $referencias[$posRef]["codigoCategoria"] . "'");
                $referencias[$posRef]["Categoria_idCategoria"] = $categoria->idCategoria;


                // validamos el Clima
                $clima->idClima = 0;
                if (!empty($referencias[$posRef]["codigoClima"]))
                    $clima->ConsultarClima("codigoAlternoClima =  '" . $referencias[$posRef]["codigoClima"] . "'");
                $referencias[$posRef]["Clima_idClima"] = $clima->idClima;

                // validamos el Difusion
                $difusion->idDifusion = 0;
                if (!empty($referencias[$posRef]["codigoDifusion"]))
                    $difusion->ConsultarDifusion("codigoAlternoDifusion =  '" . $referencias[$posRef]["codigoDifusion"] . "'");
                $referencias[$posRef]["Difusion_idDifusion"] = $difusion->idDifusion;

                // validamos el Estrategia
                $estrategia->idEstrategia = 0;
                if (!empty($referencias[$posRef]["codigoEstrategia"]))
                    $estrategia->ConsultarEstrategia("codigoAlternoEstrategia =  '" . $referencias[$posRef]["codigoEstrategia"] . "'");
                $referencias[$posRef]["Estrategia_idEstrategia"] = $estrategia->idEstrategia;

                // validamos el Seccion
                $seccion->idSeccion = 0;
                if (!empty($referencias[$posRef]["codigoSeccion"]))
                    $seccion->ConsultarSeccion("codigoAlternoSeccion =  '" . $referencias[$posRef]["codigoSeccion"] . "'");
                $referencias[$posRef]["Seccion_idSeccion"] = $seccion->idSeccion;

                // validamos el Evento
                $evento->idEvento = 0;
                if (!empty($referencias[$posRef]["codigoEvento"]))
                    $evento->ConsultarEvento("codigoAlternoEvento =  '" . $referencias[$posRef]["codigoEvento"] . "'");
                $referencias[$posRef]["Evento_idEvento"] = $evento->idEvento;

                // validamos el ClienteObjetivo
                $clienteobjetivo->idClienteObjetivo = 0;
                if (!empty($referencias[$posRef]["codigoClienteObjetivo"]))
                    $clienteobjetivo->ConsultarClienteObjetivo("codigoAlternoClienteObjetivo =  '" . $referencias[$posRef]["codigoClienteObjetivo"] . "'");
                $referencias[$posRef]["ClienteObjetivo_idClienteObjetivo"] = $clienteobjetivo->idClienteObjetivo;

                // validamos el esquema de producto
                $esquemaproducto->idEsquemaProducto = 0;
                if (!empty($referencias[$posRef]["codigoEsquemaProducto"]))
                    $esquemaproducto->ConsultarEsquemaProducto("codigoAlternoEsquemaProducto =  '" . $referencias[$posRef]["codigoEsquemaProducto"] . "'");
                $referencias[$posRef]["EsquemaProducto_idEsquemaProducto"] = $esquemaproducto->idEsquemaProducto;
                $referencias[$posRef]["generaCodigoBarrasEsquemaProducto"] = $esquemaproducto->generaCodigoBarrasEsquemaProducto;

                // consultamos el EAN del Cliente en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($referencias[$posRef]["codigoCliente"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $referencias[$posRef]["codigoCliente"] . "' or codigoAlterno1Tercero = '" . $referencias[$posRef]["codigoCliente"] . "'");
                $referencias[$posRef]["Tercero_idCliente"] = $tercero->idTercero;

                // consultamos el EAN del proveedor en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($referencias[$posRef]["codigoProveedor"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $referencias[$posRef]["codigoProveedor"] . "' or codigoAlterno1Tercero = '" . $referencias[$posRef]["codigoProveedor"] . "'");
                $referencias[$posRef]["Tercero_idProveedor"] = $tercero->idTercero;


                /* // validamos la ubicacion en bodega
                  if(!empty($referencias[$posRef]["codigoBodegaUbicacion"]))
                  $datos = $bodega->ConsultarVistaBodega("codigoBodegaUbicacion =  '".$referencias[$posRef]["codigoBodegaUbicacion"]."'");
                  $referencias[$posRef]["BodegaUbicacion_idBodegaUbicacion"] = $datos[0]["idBodegaUbicacion"];
                 */

                // validamos la unidad de medida de compra
                $unidadmedida->idUnidadMedida = 0;
                if (!empty($referencias[$posRef]["codigoUnidadMedidaCompra"]))
                    $unidadmedida->ConsultarUnidadMedida("codigoAlternoUnidadMedida = '" . $referencias[$posRef]["codigoUnidadMedidaCompra"] . "' ");
                $referencias[$posRef]["UnidadMedida_idCompra"] = $unidadmedida->idUnidadMedida;


                // validamos la unidad de medida de venta
                $unidadmedida->idUnidadMedida = 0;
                if (!empty($referencias[$posRef]["codigoUnidadMedidaVenta"]))
                    $unidadmedida->ConsultarUnidadMedida("codigoAlternoUnidadMedida = '" . $referencias[$posRef]["codigoUnidadMedidaVenta"] . "' ");
                $referencias[$posRef]["UnidadMedida_idVenta"] = $unidadmedida->idUnidadMedida;


                $segmento->idSegmentoOperacion = 0;
                if (!empty($referencias[$posRef]["codigoSegmentoOperacion"]))
                    $segmento->ConsultarIdSegmetoOperacion("codigoAlternoSegmentoOperacion = '" . $referencias[$posRef]["codigoSegmentoOperacion"] . "' ");
                $referencias[$posRef]["SegmentoOperacion_idSegmentoOperacion"] = $segmento->idSegmentoOperacion;

                $tono->idTono = 0;
                if (!empty($referencias[$posRef]["codigoTono"]))
                    $tono->idTono("codigoAlternoTono = '" . $referencias[$posRef]["codigoTono"] . "' ");
                $referencias[$posRef]["Tono_idTono"] = $tono->idTono;

                $pinta->idPinta = 0;
                if (!empty($referencias[$posRef]["codigoPinta"]))
                    $pinta->ConsultarIdSegmetoOperacion("codigoAlternoPinta = '" . $referencias[$posRef]["codigoPinta"] . "' ");
                $referencias[$posRef]["Pinta_idPinta"] = $pinta->idPinta;

                $calibre->idCalibreHilo = 0;
                if (!empty($referencias[$posRef]["codigoCalibreHilo"]))
                    $calibre->ConsultarIdSegmetoOperacion("codigoAlternoCalibreHilo = '" . $referencias[$posRef]["codigoCalibreHilo"] . "' ");
                $referencias[$posRef]["CalibreHilo_idCalibreHilo"] = $calibre->idCalibreHilo;

                $fila++;
            }

            $erroresproductos = $this->llenarPropiedadesProducto($referencias);

            if (count($erroresproductos) > 0) {
                $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
                return $erroresproductos;
            }
            //$inconsistencias = array_merge((array)$inconsistencias, (array)$retorno);
            //--------------------------------------------------------
            //  E  N  C  A  B  E  Z  A  D  O
            //  L I S T A   D E   P R E C I O S
            //--------------------------------------------------------
            $precio = array();
            $preciodetalle = array();
            $preciotercero = array();
            $componentedetalle = array();
            $posPreEnc = 0;
            $posPreDet = -1;
            $posPreTerDet = -1;
            //----------------------------------------
            $precio[$posPreEnc]["codigoAlternoListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 2)->getValue();
            $precio[$posPreEnc]["nombreListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 3)->getValue();
            $precio[$posPreEnc]["redondeoListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 4)->getValue();
            $precio[$posPreEnc]["fechaInicialListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 5)->getValue();
            $precio[$posPreEnc]["horaInicialListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(55, 5)->getValue();
            $precio[$posPreEnc]["fechaFinalListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 6)->getValue();
            $precio[$posPreEnc]["horaFinalListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(55, 6)->getValue();
            $precio[$posPreEnc]["codigoAlternoMoneda"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 7)->getValue();
            $precio[$posPreEnc]["codigoAlternoComponenteCosto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 8)->getValue();
            $precio[$posPreEnc]["modificarPrecioProductoListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 9)->getValue();
            $precio[$posPreEnc]["ivaIncluidoProductoListaPrecio"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 10)->getValue();
            $precio[$posPreEnc]["actualizar"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 11)->getValue();
            $precio[$posPreEnc]["estadoListaPrecioDetalle"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(54, 12)->getValue();
            //----------------------------------------
            //
                                    // convertimos la fecha de formato EXCEL a formato UNIX
            $fechaReal = $precio[$posPreEnc]["fechaInicialListaPrecio"];
            $precio[$posPreEnc]["fechaInicialListaPrecio"] = (gettype($fechaReal) == 'double' or gettype($fechaReal) == 'integer' and $fechaReal > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal))) : $precio[$posPreEnc]["fechaInicialListaPrecio"];

            $fechaReal = $precio[$posPreEnc]["fechaFinalListaPrecio"];
            $precio[$posPreEnc]["fechaFinalListaPrecio"] = (gettype($fechaReal) == 'double' or gettype($fechaReal) == 'integer' and $fechaReal > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal))) : $precio[$posPreEnc]["fechaFinalListaPrecio"];

            // consultamos le ID del componente de costo
            $componentecosto->idComponenteCosto = 0;
            if (!empty($precio[$posPreEnc]["codigoAlternoComponenteCosto"]))
                $componentecosto->ConsultarIdComponenteCosto("codigoAlternoComponenteCosto = '" . $precio[$posPreEnc]["codigoAlternoComponenteCosto"] . "' ");
            $precio[$posPreEnc]["ComponenteCosto_idComponenteCosto"] = $componentecosto->idComponenteCosto;

            // consultamos le ID de la Moneda
            $moneda->idMoneda = 0;
            if (!empty($precio[$posPreEnc]["codigoAlternoMoneda"]))
                $moneda->ConsultarIdMoneda("codigoAlternoMoneda = '" . $precio[$posPreEnc]["codigoAlternoMoneda"] . "' ");
            $precio[$posPreEnc]["Moneda_idMoneda"] = $moneda->idMoneda;


            echo '<br><br>';
            $fila = 15;
            //---------------------
            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(53, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(53, $fila)->getValue() != NULL) {
                //------------------
                //--------------------------------------------------------
                //
                                            //  L  I  S  T  A     D  E     P  R  E  C  I  O  S
                //
                                            //--------------------------------------------------------
                // por cada numero de documento diferente, llenamos el detelle
                $posPreDet++;


                // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                $preciodetalle[$posPreDet]["codigoAlternoListaPrecio"] = $precio[$posPreEnc]["codigoAlternoListaPrecio"];


                // le cambie la posicion
                $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(53, 13)->getValue();
                $preciodetalle[$posPreDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(53, $fila)->getCalculatedValue();

                //echo "campo: ".$campo."<br>";
                // echo "<br>".$preciodetalle[$posPreDet]["Bodega_idBodega"]."<br>";
                // consultamos el EAN del producto en la tabla de productos para obtener el ID
                $producto->idProducto = 0;
                if (!empty($preciodetalle[$posPreDet]["referenciaProductoLP"]))
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $preciodetalle[$posPreDet]["referenciaProductoLP"] . "' or
                                                                                    codigoBarrasProducto = '" . $preciodetalle[$posPreDet]["referenciaProductoLP"] . "'");
                $preciodetalle[$posPreDet]["Producto_idProducto"] = $producto->idProducto;
                //---------------
                $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(52, 13)->getValue();
                $preciodetalle[$posPreDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(52, $fila)->getCalculatedValue();
                //------------------
                // consultamos el Id de la Bodega del detalle de la lista de precio
                $bodega->idBodega = 0;
                if (!empty($preciodetalle[$posPreDet]["codigoAlternoBodegaLP"]))
                    $bodega->ConsultarBodega("codigoAlternoBodega = '" . $preciodetalle[$posPreDet]["codigoAlternoBodegaLP"] . "' ");
                $preciodetalle[$posPreDet]["Bodega_idBodega"] = $bodega->idBodega;

                // para cada registro del detalle recorremos las columnas desde la 24 hasta la 33
                //--
                $columna = 52;
                //--
                $comp = 0;
                while ($campo != 'precioListaPrecioDetalle') {
                    // en la fila 12 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    // si el nombre de campo esta vacio lo tomamos de la linea 13 como el codigo alterno del componente de costo digitado por el usuario
                    $tipoDato = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 13)->getValue() == '' ? 14 : 13;

                    if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getDataType() == 'f')
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $tipoDato)->getCalculatedValue();
                    else
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $tipoDato)->getValue();

                    if ($campo != '' and substr($campo, 0, 7) != 'Columna') {
                        //print_r($campo);
                        if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getDataType() == 'f') {
                            // si es un campo fijo de la lista de precios (12) lo llevamos al array preciodetalle
                            // sino lo llevamos al array componentedetalle
                            if ($tipoDato == 13)
                                $preciodetalle[$posPreDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getCalculatedValue();
                            else {
                                $componentedetalle[$posPreDet]["codigoAlternoComponenteCosto"][$comp] = $campo;
                                $componentedetalle[$posPreDet]["valorListaPrecioComponenteCosto"][$comp] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getCalculatedValue();
                            }
                        } else {
                            if ($tipoDato == 13)
                                $preciodetalle[$posPreDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                            else {
                                $componentedetalle[$posPreDet]["codigoAlternoComponenteCosto"][$comp] = $campo;
                                $componentedetalle[$posPreDet]["valorListaPrecioComponenteCosto"][$comp] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                            }
                        }

                        if ($tipoDato == 14 and substr($campo, 0, 7) != 'Columna') {
                            // consultamos le ID del componente de costo detalle
                            $componentecosto->idComponenteCostoDetalle = 0;
                            if (!empty($componentedetalle[$posPreDet]["codigoAlternoComponenteCosto"][$comp]))
                                $componentecosto->ConsultarIdComponenteCostoDetalle("ComponenteCosto_idComponenteCosto = " . $precio[$posPreEnc]["ComponenteCosto_idComponenteCosto"] . " and
                                                                                                                    codigoAlternoComponenteCostoDetalle = '" . $componentedetalle[$posPreDet]["codigoAlternoComponenteCosto"][$comp] . "' ");
                            $componentedetalle[$posPreDet]["ComponenteCostoDetalle_idComponenteCostoDetalle"][$comp] = $componentecosto->idComponenteCostoDetalle;
                            $comp++;
                        }
                    }
                    $columna++;
                }

                $fila++;
            }
            $fila = 15;
            $columna = $columna + 1;
            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue() != '' and $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue() != NULL) {

                //--------------------------------------------------------------------------
                //
                                //  L  I  S  T  A     D  E     P  R  E  C  I  O  S    T  E  R  C  E  R  O  S
                //
                                //---------------------------------------------------------------------------
                // por cada numero de documento diferente, llenamos el detelle
                $posPreTerDet++;

                $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 13)->getValue();
                $preciotercero[$posPreTerDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getCalculatedValue();

                // consultamos el Id del Tercero para la lista de precio
                $tercero->idTercero = 0;
                if (!empty($preciotercero[$posPreTerDet]["EANTerceroLP"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $preciotercero[$posPreTerDet]["EANTerceroLP"] . "' or codigoAlterno1Tercero = '" . $preciotercero[$posPreTerDet]["EANTerceroLP"] . "'");
                $preciotercero[$posPreTerDet]["Tercero_idTercero"] = $tercero->idTercero;
                $fila++;
            }

            $erroreslistaprecio = $this->llenarPropiedadesListaPrecio($precio, $preciodetalle, $componentedetalle, $preciotercero);

            if (count($erroreslistaprecio) > 0) {
                $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
                return $erroreslistaprecio;
            }

            //--------------------------------------------------------
            //  E  N  C  A  B  E  Z  A  D  O
            //  M O V M I E N T O   C O M E R C I A L
            //--------------------------------------------------------
            $encabezado = array();
            $detalle = array();
            $posEnc = 0;
            $posDet = -1;

            $encabezado[$posEnc]["codigoDocumento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 3)->getValue();
            $encabezado[$posEnc]["codigoConceptoDocumento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 3)->getValue();
            $encabezado[$posEnc]["numeroMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 4)->getValue();
            $encabezado[$posEnc]["fechaElaboracionMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(50, 4)->getValue();
            $encabezado[$posEnc]["eanTercero"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 5)->getValue();
            $encabezado[$posEnc]["factorMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(50, 5)->getValue();
            $encabezado[$posEnc]["nombreTipoDocumento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 6)->getValue();
            $encabezado[$posEnc]["numeroReferenciaExternoMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 6)->getValue();
            $encabezado[$posEnc]["codigoMoneda"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 7)->getValue();
            $encabezado[$posEnc]["tasaCambioMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(50, 7)->getValue();
            $encabezado[$posEnc]["codigoIncoterm"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 8)->getValue();


            // convertimos la fecha de formato EXCEL a formato UNIX
            $fechaReal = $encabezado[$posEnc]["fechaElaboracionMovimiento"];

            $encabezado[$posEnc]["fechaElaboracionMovimiento"] = (gettype($fechaReal) == 'double' or gettype($fechaReal) == 'integer' and $fechaReal > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal))) : $encabezado[$posEnc]["fechaElaboracionMovimiento"];
            $fechaReal1 = $encabezado[$posEnc]["fechaMinimaMovimiento"];

            $encabezado[$posEnc]["fechaMinimaMovimiento"] = (gettype($fechaReal1) == 'double' or gettype($fechaReal1) == 'integer' and $fechaReal1 > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal1))) : $encabezado[$posEnc]["fechaMinimaMovimiento"];
            $fechaReal2 = $encabezado[$posEnc]["fechaMaximaMovimiento"];

            $encabezado[$posEnc]["fechaMaximaMovimiento"] = (gettype($fechaReal2) == 'double' or gettype($fechaReal2) == 'integer' and $fechaReal2 > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal2))) : $encabezado[$posEnc]["fechaMaximaMovimiento"];

            //echo $encabezado[$posEnc]["fechaElaboracionMovimiento"];
            // cada que llenemos un encabezado, hacemos las verificaciones de codigos necesarioos
            // validamos el documento
            if (!empty($encabezado[$posEnc]["codigoDocumento"]))
                $datos = $documentocomercial->ConsultarVistaDocumento("codigoAlternoDocumento =  '" . $encabezado[$posEnc]["codigoDocumento"] . "'");
            $encabezado[$posEnc]["Documento_idDocumento"] = isset($datos[0]["idDocumento"]) ? $datos[0]["idDocumento"] : 0;
            $encabezado[$posEnc]["estadoWMSMovimiento"] = isset($datos[0]["idDocumento"]) ? $datos[0]["estadoWMSDocumento"] : 'ABIERTO';

            // validamos el concepto de documento
            $documentoconcepto->idDocumentoConcepto = 0;
            if (!empty($encabezado[$posEnc]["codigoConceptoDocumento"]))
                $documentoconcepto->ConsultarDocumentoConcepto("codigoAlternoDocumentoConcepto =  '" . $encabezado[$posEnc]["codigoConceptoDocumento"] . "'");
            $encabezado[$posEnc]["DocumentoConcepto_idDocumentoConcepto"] = $documentoconcepto->idDocumentoConcepto;

            // validamos el periodo
            $periodo->idPeriodo = 0;
            if (!empty($encabezado[$posEnc]["fechaElaboracionMovimiento"]))
                $periodo->ConsultarPeriodo("fechaInicialPeriodo <=  '" . $encabezado[$posEnc]["fechaElaboracionMovimiento"] .
                        "' and fechaFinalPeriodo >=  '" . $encabezado[$posEnc]["fechaElaboracionMovimiento"] .
                        "'  and estadoPeriodo = 'ACTIVO' and estadoComercialPeriodo = 'ACTIVO'");
            $encabezado[$posEnc]["Periodo_idPeriodo"] = $periodo->idPeriodo;

            // consultamos el EAN del Cliente en la tabla de terceros para obtener el ID
            $tercero->idTercero = 0;
            if (!empty($encabezado[$posEnc]["eanTercero"]))
                $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $encabezado[$posEnc]["eanTercero"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["eanTercero"] . "'");
            $encabezado[$posEnc]["Tercero_idTercero"] = $tercero->idTercero;

            $tercero->idTercero = 0;
            $tercero->ConsultarIdTercero("documentoTercero = '" . $encabezado[$posEnc]["eanTercero"] . "' and tipoTercero not like '%*18*%'");
            $encabezado[$posEnc]["Tercero_idPrincipal"] = $tercero->idTercero;

            // consultamos la moneda  en la tabla de monedas para obtener el ID
            $moneda->idMoneda = 0;
            if (!empty($encabezado[$posEnc]["codigoMoneda"]))
                $moneda->ConsultarMoneda("codigoAlternoMoneda = '" . $encabezado[$posEnc]["codigoMoneda"] . "'");
            $encabezado[$posEnc]["Moneda_idMoneda"] = $moneda->idMoneda;
            if ($encabezado[$posEnc]['tasaCambioMovimiento'] == 0) {
                $sql = "Select pideTasaCambioMoneda,fechaMonedaTasaCambio,tasaMonedaTasaCambio
                                        from Moneda mon
                                        left join MonedaTasaCambio tasa
                                        on mon.idMoneda = tasa.Moneda_idMoneda
                                        where mon.idMoneda = " . $encabezado[$posEnc]["Moneda_idMoneda"] . " and fechaMonedaTasaCambio = '" . $encabezado[$posEnc]["fechaElaboracionMovimiento"] . "'";
                $bd = Db::getInstance();
                $tasacambio = $bd->ConsultarVista($sql);
                if (isset($tasacambio[0]['tasaMonedaTasaCambio'])) {
                    $encabezado[$posEnc]["tasaCambioMovimiento"] = $tasacambio[0]['tasaMonedaTasaCambio'];
                }
            }
            // consultamos la forma de pago  en la tabla de formapago para obtener el ID
            /* $formapago->idFormaPago = 0;
              if (!empty($encabezado[$posEnc]["codigoFormaPago"]))
              $formapago->ConsultarFormaPago("codigoAlternoFormaPago = '" . $encabezado[$posEnc]["codigoFormaPago"] . "'");
              $encabezado[$posEnc]["FormaPago_idFormaPago"] = $formapago->idFormaPago; */


            // consultamos el Incoterm  en la tabla de incoterms para obtener el ID
            $incoterm->idIncoterm = 0;
            if (!empty($encabezado[$posEnc]["codigoIncoterm"]))
                $incoterm->ConsultarIncoterm("codigoAlternoIncoterm = '" . $encabezado[$posEnc]["codigoIncoterm"] . "'");
            $encabezado[$posEnc]["Incoterm_idIncoterm"] = $incoterm->idIncoterm;

            $fila = 15;


            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, $fila)->getValue() != NULL) {

                //--------------------------------------------------------
                //
                                            //  M O V M I E N T O   C O M E R C I A L
                //
                                            //--------------------------------------------------------
                // por cada numero de documento diferente, llenamos el detelle
                $posDet++;


                // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                $detalle[$posDet]["numeroMovimiento"] = $encabezado[$posEnc]["numeroMovimiento"];
                $detalle[$posDet]["Documento_idDocumento"] = $encabezado[$posEnc]["Documento_idDocumento"];

                // para cada registro del detalle recorremos las columnas desde la 24 hasta la 33
                for ($columna = 46; $columna <= 50; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    // si el nombre de campo esta vacio lo saltamos

                    if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getDataType() == 'f')
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 13)->getCalculatedValue();
                    else
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 13)->getValue();
                    if ($campo != '')
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // consultamos el Id de la Bodega Origen

                $bodega->idBodega = 0;
                if (!empty($detalle[$posDet]["codigoBodegaOrigen"]))
                    $bodega->ConsultarBodega("codigoAlternoBodega = '" . $detalle[$posDet]["codigoBodegaOrigen"] . "' ");
                $detalle[$posDet]["Bodega_idBodegaOrigen"] = $bodega->idBodega;


                $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, 13)->getValue();
                $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(47, $fila)->getCalculatedValue();



                // consultamos el EAN del producto en la tabla de productos para obtener el ID
                $producto->idProducto = 0;
                if (!empty($detalle[$posDet]["referenciaProductoM"]))
                    $producto->ConsultarIdProducto("referenciaProducto = '" . $detalle[$posDet]["referenciaProductoM"] . "'");
                $detalle[$posDet]["Producto_idProducto"] = $producto->idProducto;


                //echo $posRef."<br>";
                //print_r($detalle);
                $detalle[$posDet]["eanProducto"] = $posRef == -1 ? 0 : $referencias[$posRef]["codigoBarrasProducto"];
                // si no encontramos el producto, lo buscamos en sus homologos por tercero
                if ($detalle[$posDet]["Producto_idProducto"] == 0) {
                    $datos = $producto->ConsultarVistaProductoTercero("codigoBarrasProductoTercero = '" . $referencias[$posRef]["referenciaProducto"] . "' or referenciaProductoTercero = '" . $referencias[$posRef]["referenciaProducto"] . "' or pluProductoTercero = '" . $referencias[$posRef]["referenciaProducto"] . "'", "", "Producto_idProducto", "");
                    if (isset($datos[0]["Producto_idProducto"]))
                        $detalle[$posDet]["Producto_idProducto"] = $datos[0]["Producto_idProducto"];
                }

                $detalle[$posDet]["Tercero_idAlmacen"] = 0;

                // llenamos el precio de lista con el valor Bruto
                $detalle[$posDet]["precioListaMovimientoDetalle"] = $detalle[$posDet]["valorBrutoMovimientoDetalle"];

                $fila++;
            }
            if (!empty($detalle)) {
                $erroresmovimiento = $this->llenarPropiedadesMovimiento($encabezado, $detalle, 'interface', $preciodetalle, $preciotercero);
            }

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);



            unset($precio);
            unset($preciodetalle);
            unset($componentedetalle);
            unset($encabezado);
            unset($detalle);
            unset($referencias);

            if (count($erroresmovimiento) > 0) {
                $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
                return $erroresmovimiento;
            }

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
        }

        function llenarPropiedadesListaPrecio($encabezado, $detalle, $componente, $tercerolista) {
            // instanciamos la clase movimiento y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'listaprecio.class.php';
            $listaprecio = new ListaPrecio();
            // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
            $listaprecio->ListaPrecio();
            /* $idListaPrecio = $listaprecio->ConsultarIdListaPrecio("codigoAlternoListaPrecio = '" . $encabezado[0]["codigoAlternoListaPrecio"] . "'
              and nombreListaPrecio ='" . $encabezado[0]["nombreListaPrecio"] . "'"); */
            sort($tercerolista);
            //print_r($detalle);
            $retorno = array();
            $nuevoserrores = array();
            // contamos los registros del encabezado
            //print_r($encabezado);
            $totalreg = (isset($encabezado[0]["codigoAlternoListaPrecio"]) ? count($encabezado) : 0);
            $totalter = (isset($tercerolista[0]["EANTerceroLP"]) ? count($tercerolista) : 0);
            $nuevoserrores = $this->validarListaPrecio($encabezado, $detalle, $tercerolista);
            for ($i = 0; $i < $totalreg; $i++) {
                $totaldet = (isset($detalle[0]["codigoAlternoListaPrecio"]) ? count($detalle) : 0);
                //'ERRORES '.isset($nuevoserrores[0]["error"])."<br>";
                if (!isset($nuevoserrores[0]["error"])) {
                    $listaprecio->codigoAlternoListaPrecio = (isset($encabezado[$i]["codigoAlternoListaPrecio"]) ? $encabezado[$i]["codigoAlternoListaPrecio"] : '');
                    $listaprecio->nombreListaPrecio = (isset($encabezado[$i]["nombreListaPrecio"]) ? $encabezado[$i]["nombreListaPrecio"] : '');
                    $listaprecio->redondeoListaPrecio = (isset($encabezado[$i]["redondeoListaPrecio"]) ? $encabezado[$i]["redondeoListaPrecio"] : 0);
                    $listaprecio->fechaInicialListaPrecio = (isset($encabezado[$i]["fechaInicialListaPrecio"]) ? $encabezado[$i]["fechaInicialListaPrecio"] : '');
                    $listaprecio->horaInicialListaPrecio = (isset($encabezado[$i]["horaInicialListaPrecio"]) ? $encabezado[$i]["horaInicialListaPrecio"] : '');
                    $listaprecio->fechaFinalListaPrecio = (isset($encabezado[$i]["fechaFinalListaPrecio"]) ? $encabezado[$i]["fechaFinalListaPrecio"] : '');
                    $listaprecio->horaFinalListaPrecio = (isset($encabezado[$i]["horaFinalListaPrecio"]) ? $encabezado[$i]["horaFinalListaPrecio"] : '');
                    $listaprecio->Moneda_idMoneda = (isset($encabezado[$i]["Moneda_idMoneda"]) ? $encabezado[$i]["Moneda_idMoneda"] : 0);
                    $listaprecio->ListaPrecio_idBasadoenListaPrecio = (isset($encabezado[$i]["ListaPrecio_idBasadoenListaPrecio"]) ? $encabezado[$i]["ListaPrecio_idBasadoenListaPrecio"] : 0);
                    $listaprecio->redondeoListaPrecio = (isset($encabezado[$i]["redondeoListaPrecio"]) ? $encabezado[$i]["redondeoListaPrecio"] : '');
                    $listaprecio->ComponenteCosto_idComponenteCosto = (isset($encabezado[$i]["ComponenteCosto_idComponenteCosto"]) ? $encabezado[$i]["ComponenteCosto_idComponenteCosto"] : 0);
                    $listaprecio->modificarPrecioProductoListaPrecio = ((isset($encabezado[$i]["modificarPrecioProductoListaPrecio"]) && $encabezado[$i]["modificarPrecioProductoListaPrecio"] == 'X') ? 1 : 0);
                    $listaprecio->ivaIncluidoProductoListaPrecio = ((isset($encabezado[$i]["ivaIncluidoProductoListaPrecio"]) && $encabezado[$i]["ivaIncluidoProductoListaPrecio"] == 'X') ? 1 : 0);

                    // por cada registro del encabezado, recorremos el detalle para obtener solo los datos del mismo numero de movimiento del encabezado, con estos
                    // llenamos arrays por cada campo
                    $ter = 0;
                    // llevamos un contador de registros por cada producto del detalle
                    $registroact = 0;
                    $registroter = 0;
                    $registroprod = 0;

                    for ($j = 0; $j < $totaldet; $j++) {
                        $totalcompo = (isset($componente[$j]["codigoAlternoComponenteCosto"]) ? count($componente[$j]["codigoAlternoComponenteCosto"]) : 0);
                        if ($encabezado[$i]["ComponenteCosto_idComponenteCosto"] > 0) {
                            require_once '../clases/componentecosto.class.php';
                            $componentecosto = new ComponenteCosto();
                            $formula = $componentecosto->ConsultarVistaComponenteCosto("idComponenteCosto = " . $encabezado[$i]["ComponenteCosto_idComponenteCosto"], "", "formulaComponenteCosto");
                            //print_r($formula);
                            $formulaComponente = $formula[0]["formulaComponenteCosto"];
                            $formulaComponente = str_replace("Base", ($detalle[$j]["valorBaseListaPrecioDetalle"] != 0 ? $detalle[$j]["valorBaseListaPrecioDetalle"] : 0), $formulaComponente);
                            $formulaComponente = str_replace("Margen", ($detalle[$j]["margenListaPrecioDetalle"] != 0 ? $detalle[$j]["margenListaPrecioDetalle"] : 0), $formulaComponente);
                            $formulaComponente = str_replace("Descuento", ($detalle[$j]["descuentoListaPrecioDetalle"] != 0 ? $detalle[$j]["descuentoListaPrecioDetalle"] : 0), $formulaComponente);
                            for ($k = 1; $k < $totalcompo; $k++) {
                                $formulaComponente = str_replace($componente[$j]["codigoAlternoComponenteCosto"][$k], ($componente[$j]["valorListaPrecioComponenteCosto"][$k] != 0 ? $componente[$j]["valorListaPrecioComponenteCosto"][$k] : 0), $formulaComponente);
                            }
                            $valor = '$resultadoprecioLista = ' . $formulaComponente . ';';

                            eval($valor);
                        } else if ($detalle[$j]["precioListaPrecioDetalle"] == '') {
                            $preci = ($detalle[$j]["valorBaseListaPrecioDetalle"] * (1 + ((isset($detalle[$j]["margenListaPrecioDetalle"]) ? $detalle[$j]["margenListaPrecioDetalle"] : 0)) / 100));
                            $des = $preci * ((isset($detalle[$j]["descuentoListaPrecioDetalle"]) ? $detalle[$j]["descuentoListaPrecioDetalle"] : 0)) / 100;
                            $precio = $preci - $des;
                        } else {
                            $precio = $detalle[$j]["precioListaPrecioDetalle"];
                        }
                        if (isset($encabezado[$i]["codigoAlternoListaPrecio"]) and isset($detalle[$j]["codigoAlternoListaPrecio"]) and $encabezado[$i]["codigoAlternoListaPrecio"] == $detalle[$j]["codigoAlternoListaPrecio"]) {
                            $listaprecio->idListaPrecioDetalle[$registroprod] = 0;
                            $listaprecio->Producto_idProducto[$registroprod] = (isset($detalle[$j]["Producto_idProducto"]) ? $detalle[$j]["Producto_idProducto"] : 0);
                            $listaprecio->valorBaseListaPrecioDetalle[$registroprod] = (isset($detalle[$j]["valorBaseListaPrecioDetalle"]) ? number_format($detalle[$j]["valorBaseListaPrecioDetalle"], $listaprecio->redondeoListaPrecio, ".", "") : 0);
                            $listaprecio->margenListaPrecioDetalle[$registroprod] = (isset($detalle[$j]["margenListaPrecioDetalle"]) ? number_format($detalle[$j]["margenListaPrecioDetalle"], $listaprecio->redondeoListaPrecio, ".", "") : 0);
                            $listaprecio->precioListaPrecioDetalle[$registroprod] = ((isset($resultadoprecioLista) && $resultadoprecioLista != '') ? number_format($resultadoprecioLista, $listaprecio->redondeoListaPrecio, ".", "") : (isset($precio) ? number_format($precio, $listaprecio->redondeoListaPrecio, ".", "") : 0));
                            $listaprecio->descuentoListaPrecioDetalle[$registroprod] = (isset($detalle[$j]["descuentoListaPrecioDetalle"]) ? number_format($detalle[$j]["descuentoListaPrecioDetalle"], $listaprecio->redondeoListaPrecio, ".", "") : 0);
                            $listaprecio->descuentoMaxListaPrecioDetalle[$registroprod] = (isset($detalle[$j]["descuentoMaxListaPrecioDetalle"]) ? number_format($detalle[$j]["descuentoMaxListaPrecioDetalle"], $listaprecio->redondeoListaPrecio, ".", "") : 0);
                            $listaprecio->valorDescuentoMaxListaPrecioDetalle[$registroprod] = (isset($detalle[$j]["valorDescuentoMaxListaPrecioDetalle"]) ? number_format($detalle[$j]["valorDescuentoMaxListaPrecioDetalle"], $listaprecio->redondeoListaPrecio, ".", "") : 0);
                            $listaprecio->dineroListaPrecioDetalle[$registroprod] = (isset($detalle[$j]["dineroListaPrecioDetalle"]) ? $detalle[$j]["dineroListaPrecioDetalle"] : 0);
                            $listaprecio->puntosListaPrecioDetalle[$registroprod] = (isset($detalle[$j]["puntosListaPrecioDetalle"]) ? $detalle[$j]["puntosListaPrecioDetalle"] : 0);
                            $listaprecio->Bodega_idBodega[$registroprod] = (isset($detalle[$j]["Bodega_idBodega"]) ? $detalle[$j]["Bodega_idBodega"] : 0);
                            $listaprecio->estadoListaPrecioDetalle[$registroprod] = (isset($encabezado[$i]["estadoListaPrecioDetalle"]) ? $encabezado[$i]["estadoListaPrecioDetalle"] : '');

                            for ($k = 0; $k < $totalcompo; $k++) {
                                // campos dinamicos de componentes del costo
                                $listaprecio->idListaPrecioComponenteCosto[$registroact][$k] = (isset($componente[$j]["idListaPrecioComponenteCosto"][$k]) ? $componente[$j]["idListaPrecioComponenteCosto"][$k] : 0);
                                $listaprecio->ComponenteCostoDetalle_idComponenteCostoDetalle[$registroact][$k] = (isset($componente[$j]["ComponenteCostoDetalle_idComponenteCostoDetalle"][$k]) ? $componente[$j]["ComponenteCostoDetalle_idComponenteCostoDetalle"][$k] : 0);
                                $listaprecio->valorListaPrecioComponenteCosto[$registroact][$k] = (isset($componente[$j]["valorListaPrecioComponenteCosto"][$k]) ? $componente[$j]["valorListaPrecioComponenteCosto"][$k] : 0);
                            }
                            for ($t = 0; $t < $totalter; $t++) {
                                $listaprecio->idListaPrecioTercero[$registroter] = 0;
                                $listaprecio->Tercero_idTercero[$registroter] = (isset($tercerolista[$t]["Tercero_idTercero"]) ? $tercerolista[$t]["Tercero_idTercero"] : 0);
                            }
                            $registroact++;
                            $registroprod++;
                        }
                    }
                    //print_r($encabezado);
                    // buscamos si ya existe la lista de precios con el mismo numero y la reemplazamos con la nueva
                    $listaprecio->ConsultarIdListaPrecio("codigoAlternoListaPrecio = '" . $encabezado[0]["codigoAlternoListaPrecio"] . "' and nombreListaPrecio ='" . $encabezado[0]["nombreListaPrecio"] . "'");
                    if ($listaprecio->idListaPrecio != 0) {
                        if (strtoupper($encabezado[0]["actualizar"]) == "REEMPLAZAR") {
                            $listaprecio->ModificarListaPrecio('1');
                        } else if (strtoupper($encabezado[0]["actualizar"]) == "ACTUALIZAR"){
                            $listaprecio->ActualizarListaPrecioDetalle($detalle);
                        } else if (strtoupper($encabezado[0]["actualizar"]) == "MODIFICAR"){
                            $listaprecio->ModificarListaPrecioDetalle($detalle);
                        }
                    } else
                        $listaprecio->AdicionarListaPrecio();
                }
                else {
                    $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                }
            }
            //print_r($retorno);
            return $retorno;
        }

        function validarListaPrecio($encabezado, $detalle, $tercerolista) {
            //echo " entra function validar ";
            require_once 'tercero.class.php';
            $tercero = new Tercero();

            require_once 'listaprecio.class.php';
            $lista = new ListaPrecio();

            require_once 'producto.class.php';
            $producto = new Producto();

            $swerror = true;
            $errores = array();
            $linea = 0;

            $totaldet = (isset($detalle[0]["codigoAlternoListaPrecio"]) ? count($detalle) : 0);
            $totalter = (isset($tercerolista[0]["EANTerceroLP"]) ? count($tercerolista) : 0);
            /* print_r($tercerolista);
              echo "<br>"; */
            //echo $totaldet;
            for ($y = 0; $y < $totaldet; $y++) {
                if (isset($detalle[$y]["codigoAlternoListaPrecio"])) {
                    // Verificamos que el Producto exista
                    if (isset($detalle[$y]["Producto_idProducto"]) and ( $detalle[$y]["Producto_idProducto"] == 0 or $detalle[$y]["Producto_idProducto"] == '')) {
                        $errores[$linea]["codigoAlternoListaPrecio"] = $detalle[$y]["codigoAlternoListaPrecio"];
                        $errores[$linea]["error"] = 'La Referencia del Producto (' . $detalle[$y]["referenciaProductoLP"] . ') no existe';
                        $swerror = false;
                        $linea++;
                    }

                    // verificamos que la el valor base no sea cero
                    if (isset($detalle[$y]["valorBaseListaPrecioDetalle"]) and ( $detalle[$y]["valorBaseListaPrecioDetalle"] == 0 or $detalle[$y]["valorBaseListaPrecioDetalle"] == '')) {
                        $errores[$linea]["codigoAlternoListaPrecio"] = $detalle[$y]["codigoAlternoListaPrecio"];
                        $errores[$linea]["error"] = 'El valor base del Producto con ' . $detalle[$y]["referenciaProductoLP"] . ' es cero';
                        $swerror = false;
                        $linea++;
                    }
                    // verificamos que la el valor del precio no sea cero
                    if (isset($detalle[$y]["precioListaPrecioDetalle"]) and ( $detalle[$y]["precioListaPrecioDetalle"] == 0 or $detalle[$y]["precioListaPrecioDetalle"] == '')) {
                        $errores[$linea]["codigoAlternoListaPrecio"] = $detalle[$y]["codigoAlternoListaPrecio"];
                        $errores[$linea]["error"] = 'El Precio del Producto con ' . $detalle[$y]["referenciaProductoLP"] . ' es cero';
                        $swerror = false;
                        $linea++;
                    }
                    for ($z = 0; $z < $totaldet; $z++) {
                        if ("'" . $detalle[$z]["referenciaProductoLP"] . "'" == "'" . $detalle[$y]["referenciaProductoLP"] . "'" and $y != $z) {
                            //echo '<br>'.$detalle[$z]["referenciaProductoLP"]." == ".$detalle[$y]["referenciaProductoLP"].' and '.$y.' != '.$z.'<br>';
                            $errores[$linea]["codigoAlternoListaPrecio"] = $detalle[$y]["codigoAlternoListaPrecio"];
                            $errores[$linea]["error"] = 'La referencia ' . $detalle[$y]["referenciaProductoLP"] . ' esta repetida en el archivo, lineas ' . ($z + 14) . ' y ' . ($y + 14);
                            $swerror = false;
                            $linea++;
                        }
                    }
                    //echo "bodega ;".$detalle[$y]["Bodega_idBodega"];
                    // verificamos que la bodega no este vacia
                    /* if (isset($detalle[$y]["Bodega_idBodega"]) and ($detalle[$y]["Bodega_idBodega"] == 0 or $detalle[$y]["Bodega_idBodega"] == ''))
                      {
                      $errores[$linea]["codigoAlternoListaPrecio"] = $detalle[$y]["codigoAlternoListaPrecio"];
                      $errores[$linea]["error"] = 'La Bodega del Producto con ' . $detalle[$y]["referenciaProductoLP"] . ' es cero';
                      $swerror = false;
                      $linea++;
                      } */

                    // verificamos que el tercero y el producto no esten asociados en otra lista de precio vigente
                    /* $lista->ConsultarIdListaPrecio("codigoAlternoListaPrecio = '" . $encabezado[0]["codigoAlternoListaPrecio"] . "' and nombreListaPrecio ='" . $encabezado[0]["nombreListaPrecio"] . "'");
                      $encabezado[$y]["idListaPrecio"] = $lista->idListaPrecio;
                      //echo "<br>id del tercero en el excel: ".$tercerolista[$y]["Tercero_idTercero"]."<br>";
                      $hoy = date("Y-m-d");
                      for($t = 0; $t < $totalter; $t++)
                      {
                      $datosLista = $lista->ConsultarVistaListaPrecioTerceroDetalle("Producto_idProducto = ".$detalle[$y]["Producto_idProducto"]." and idTercero = ".$tercerolista[$t]["Tercero_idTercero"]." and fechaInicialListaPrecio <= '".$hoy."' and fechaFinalListaPrecio >= '".$hoy."' and idListaPrecio < ".$encabezado[$y]["idListaPrecio"],"","idListaPrecio, precioListaPrecioDetalle, Producto_idProducto, idListaPrecioDetalle");

                      if(isset($datosLista[0]["idListaPrecio"]))
                      {
                      if($datosLista[0]["idListaPrecio"] != $encabezado[$y]["idListaPrecio"])
                      {
                      $lista->ActualizaEstadoListaPrecioDetalle("idListaPrecioDetalle = ".$datosLista[0]["idListaPrecioDetalle"]);
                      }
                      }
                      } */
                }
            }
            return $errores;
        }

        function ImportarPedidoTaniaExcel($ruta) {
            set_time_limit(0);

            require_once('../clases/documentocomercial.class.php');
            $documentocomercial = new Documento();
            require_once('../clases/documentoconcepto.class.php');
            $documentoconcepto = new DocumentoConcepto();
            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/moneda.class.php');
            $moneda = new Moneda();
            require_once('../clases/formapago.class.php');
            $formapago = new FormaPago();
            require_once('../clases/incoterm.class.php');
            $incoterm = new Incoterm();
            require_once('../clases/producto.class.php');
            $producto = new Producto();
            require_once('../clases/periodo.class.php');
            $periodo = new Periodo();
            require_once('../clases/bodega.class.php');
            $bodega = new Bodega();


            //Se llama la clase PHPExcel
            //include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }

            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = 0;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;

            // Capturamos todos los datos del encabezado
            $encabezado[$posEnc]["Documento_idDocumento"] = 14;
            // validamos el documento
            if (!empty($encabezado[$posEnc]["Documento_idDocumento"]))
                $datos = $documentocomercial->ConsultarVistaDocumento("idDocumento =  '" . $encabezado[$posEnc]["Documento_idDocumento"] . "'");
            $encabezado[$posEnc]["estadoWMSMovimiento"] = isset($datos[0]["idDocumento"]) ? $datos[0]["estadoWMSDocumento"] : 'ABIERTO';



            $encabezado[$posEnc]["DocumentoConcepto_idDocumentoConcepto"] = 39;
            $encabezado[$posEnc]["numeroMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(8, 2)->getValue();
            //$encabezado[$posEnc]["fechaElaboracionMovimiento"] = date("Y-m-d",$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(9, 3)->getValue());
            $encabezado[$posEnc]["fechaElaboracionMovimiento"] = date("Y-m-d");
            //$encabezado[$posEnc]["fechaMinimaMovimiento"] = $this->fecha_dmyAymd($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, 6)->getValue());
            //$encabezado[$posEnc]["fechaMaximaMovimiento"] = date("Y-m-d",$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, 6)->getValue());
            $encabezado[$posEnc]["eanEntrega"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, 4)->getValue();
            $encabezado[$posEnc]["eanTercero"] = substr($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, 2)->getValue(), 4, 9);
            $encabezado[$posEnc]["eanAlmacen"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(9, 4)->getValue();
            $encabezado[$posEnc]["numeroMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(8, 2)->getValue();
            $encabezado[$posEnc]["tipoMovimiento"] = 'PREDISTRIBUIDA';

            $fila = 11;

            // cada que llenemos un encabezado, hacemos las verificaciones de codigos necesarioos
            // validamos el periodo
            $periodo->idPeriodo = 0;
            if (!empty($encabezado[$posEnc]["fechaElaboracionMovimiento"]))
                $periodo->ConsultarPeriodo("fechaInicialPeriodo <=  '" . $encabezado[$posEnc]["fechaElaboracionMovimiento"] .
                        "' and fechaFinalPeriodo >=  '" . $encabezado[$posEnc]["fechaElaboracionMovimiento"] .
                        "'  and estadoPeriodo = 'ACTIVO'");
            $encabezado[$posEnc]["Periodo_idPeriodo"] = $periodo->idPeriodo;

            // consultamos el EAN del Sitio de entrega en la tabla de terceros para obtener el ID
            $tercero->idTercero = 0;
            if (!empty($encabezado[$posEnc]["eanEntrega"]))
                $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $encabezado[$posEnc]["eanEntrega"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["eanEntrega"] . "'");
            $encabezado[$posEnc]["Tercero_idEntrega"] = $tercero->idTercero;

            // con el documentoTercero buscamos el id del tercero principal

            $tercero->idTercero = 0;
            $tercero->ConsultarIdTercero("documentoTercero = '" . $encabezado[$posEnc]["eanTercero"] . "' and tipoTercero not like '%*18*%'");
            $encabezado[$posEnc]["Tercero_idPrincipal"] = $tercero->idTercero;

            // consultamos el EAN del Almacen de predistribucion en la tabla de terceros para obtener el ID
            $tercero->idTercero = 0;
            if (!empty($encabezado[$posEnc]["eanAlmacen"]) and $encabezado[$posEnc]["tipoMovimiento"] == 'PREDISTRIBUIDA')
                $datos = $tercero->ConsultarVistaTercero("codigoBarrasTercero = '" . $encabezado[$posEnc]["eanAlmacen"] .
                        "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["eanAlmacen"] . "'");

            $Tercero_idAlmacen = (count($datos) > 0) ? $datos[0]["idTercero"] : 0;
            $encabezado[$posEnc]["Tercero_idTercero"] = (count($datos) > 0) ? $datos[0]["idTercero"] : 0;
            $encabezado[$posEnc]["porcentajeDescuentoMovimiento"] = (count($datos) > 0) ? $datos[0]["porcentajeDescuentoComercialTercero"] : 0;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != NULL) {

                // por cada documento, llenamos el detelle
                $posDet++;


                // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                $detalle[$posDet]["numeroMovimiento"] = $encabezado[$posEnc]["numeroMovimiento"];
                $detalle[$posDet]["Documento_idDocumento"] = $encabezado[$posEnc]["Documento_idDocumento"];

                // para cada registro del detalle tomamos las columnas necesarias
                $detalle[$posDet]["Tercero_idAlmacen"] = $Tercero_idAlmacen;
                $detalle[$posDet]["eanAlmacen"] = $encabezado[$posEnc]["eanAlmacen"];
                $detalle[$posDet]["codigoBodegaOrigen"] = 0;
                $detalle[$posDet]["codigoBodegaDestino"] = 0;
                $detalle[$posDet]["eanProducto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue();
                $detalle[$posDet]["cantidadMovimientoDetalle"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(7, $fila)->getValue();
                $detalle[$posDet]["valorBrutoMovimientoDetalle"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(8, $fila)->getValue();
                $detalle[$posDet]["porcentajeDescuentoMovimientoDetalle"] = $encabezado[$posEnc]["porcentajeDescuentoMovimiento"];

                // consultamos el Id de la Bodega Origen
                $bodega->idBodega = 0;
                if (!empty($detalle[$posDet]["codigoBodegaOrigen"]))
                    $bodega->ConsultarBodega("codigoAlternoBodega = '" . $detalle[$posDet]["codigoBodegaOrigen"] . "' ");
                $detalle[$posDet]["Bodega_idBodegaOrigen"] = $bodega->idBodega;

                // consultamos el Id de la Bodega Destino
                $bodega->idBodega = 0;
                if (!empty($detalle[$posDet]["codigoBodegaDestino"]))
                    $bodega->ConsultarBodega("codigoAlternoBodega = '" . $detalle[$posDet]["codigoBodegaDestino"] . "' ");
                $detalle[$posDet]["Bodega_idBodegaDestino"] = $bodega->idBodega;


                // consultamos el Id del producto
                $producto->idProducto = 0;
                if (!empty($detalle[$posDet]["eanProducto"]))
                    $datos = $producto->ConsultarVistaProducto("codigoBarrasProducto = '" . $detalle[$posDet]["eanProducto"] . "' or referenciaProducto = '" . $detalle[$posDet]["eanProducto"] . "'");

                $detalle[$posDet]["Producto_idProducto"] = (count($datos) > 0) ? $datos[0]["idProducto"] : 0;
                $detalle[$posDet]["valorBrutoMovimientoDetalle"] = (count($datos) > 0) ? $datos[0]["precioProducto"] : 0;

                // llenamos el precio de lista con el valor Bruto
                $detalle[$posDet]["precioListaMovimientoDetalle"] = $detalle[$posDet]["valorBrutoMovimientoDetalle"];

                // agregamos al array de detalle los valores de impuestos y retenciones y el valor total del producto
                // pasamos a la siguiente fila
                $fila++;
            }




            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos comerciales
            // para que las valide e importe al sistema, para esto recorremos cada orden de compra importada para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array
            $retorno = $this->llenarPropiedadesMovimiento($encabezado, $detalle);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($encabezado);
            unset($detalle);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
            return $retorno;
        }

        /*
          // Consulta los impuestos de cada uno de los productos del detalle
          function consultarimpuestos($idTercero, $idDocumento, $idDocumentoConcepto, $array_ids, $array_cants, $array_precios, $descuento)
          {


          require_once('../clases/documentocomercial.class.php');
          $documento = new Documento();
          $doc = $documento->ConsultarVistaDocumento("idDocumento = " . $idDocumento, '');

          require_once('../clases/documentoconcepto.class.php');
          $documentoconcepto = new DocumentoConcepto();
          $doccon = $documentoconcepto->ConsultarVistaDocumentoConcepto("idDocumentoConcepto = " . $idDocumentoConcepto, '');

          require_once('../clases/tercero.class.php');
          $tercero = new Tercero();
          $ter = $tercero->ConsultarVistaTercero("idTercero = $idTercero");

          require_once('../clases/producto.class.php');
          $producto = new Producto();



          // si el documento es del modelo compras u orden de compra, consultamos como proveedor al tercero del documento
          // y como cliente al tercero que ingreso al sistema (Compania del formulario de acceso)
          if ($doc[0]["ModeloContable_idModeloContable"] == 1 or $doc[0]["ModeloContable_idModeloContable"] == 13)
          {
          $prov = $tercero->ConsultarVistaTercero("idTercero = $idTercero");
          $clie = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");
          } else
          {
          // si el documento es del modelo ventas o pedido, consultamos como proveedor al tercero que ingreso al sistema (Compania del formulario de acceso)
          // y como cliente al tercero del documento
          if ($doc[0]["ModeloContable_idModeloContable"] == 6 or $doc[0]["ModeloContable_idModeloContable"] == 12)
          {
          $clie = $tercero->ConsultarVistaTercero("idTercero = $idTercero");
          $prov = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");
          } else
          {
          $clie = $tercero->ConsultarVistaTercero("idTercero = $idTercero");
          $prov = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");
          }
          }

          // debemos calcular una base de impuestos antes de calcular los impuestos, esto es por si el documento no
          // calcula los impuestos por cualquiera de las condiciones, para que pueda tener una base para el calculo de las retenciones
          $totalreg = count($array_ids);
          $totalBaseImp = 0;
          for ($regid = 0;
          $regid < $totalreg;
          $regid++)
          {
          $valorBase = $array_precios[$regid] * $array_cants[$regid];
          // luego de que tenemos la base, le aplicamos el descuento para obtener el impuesto
          $valorDescuento = $valorBase * ($descuento / 100);

          // con el valor base y el descuento aplicado, obetenmos el valor Gravado que es la base de impuestos
          $valorGravado = $valorBase - $valorDescuento;

          $totalBaseImp += $valorGravado;
          }

          $impuestos = array();


          for ($regid = 0;
          $regid < $totalreg;
          $regid++)
          {

          //////////////////////////////////////////////////////
          //	EL CALCULO DE LOS IMPUESTOS DEPENDE DE QUE EL
          //	CONCEPTO DEL DOCUMENTO ESTE CONFIGURADO PARA
          // 	MANEJO DE IMPUESTOS
          //////////////////////////////////////////////////////

          $valorBase = $array_precios[$regid];
          // creamos una variable que sume el valor unitarios de los impuestos de cada referencia
          $impuestoReferencia = 0;

          $totalImp = 0;
          $valorDescuento = $valorBase * ($descuento / 100);
          $valorGravado = $valorBase - $valorDescuento;

          if (isset($doccon[0]["idDocumentoConcepto"]) and $doccon[0]["impuestoDocumentoConcepto"] == 1 and $prov[0]["regimenVentasTercero"] != 'SIMPLIFICADO')
          {

          $totalBaseImp = 0;
          // consultamos la explosion de impuestos de uno de los IDS del array
          $imp = $producto->ConsultarVistaProductoImpuesto("Producto_idProducto = " . $array_ids[$regid], 'tipoValorImpuesto DESC');




          // recorremos el array consultado para acumular los datos en el array de impuestos
          $totalregimp = count($imp);
          for ($regimp = 0;
          $regimp < $totalregimp;
          $regimp++)
          {
          // para cada uno de los registros del array IMP, lo adicionamos al array de Impuestos
          // Calculamos el valor Base, dependiendo de si el impuesto esta incluido en el precio del producto o no
          if ($imp[$regimp]["ivaIncluidoProducto"] == 1)
          {
          // cuando el impuesto esta incluido, hay que recorreo el array de impuestos del producto
          // para  restar los impuestos por valor y por ultimo sacar la base con el de porcentaje
          $valorBase = $array_precios[$regid];
          //$respuesta->alert('Base inicial '.$valorBase);
          for ($reg2 = 0;
          $reg2 < $totalregimp;
          $reg2++)
          {
          if ($imp[$reg2]["tipoValorImpuesto"] == 'P')
          {
          $valorBase -= ($valorBase - ($valorBase / (1 + ($imp[$reg2]["valorImpuesto"] / 100))));
          } else
          {
          $valorBase -= $imp[$reg2]["valorImpuesto"];
          }
          //$respuesta->alert('Base con impuesto '.$imp[$reg2]["valorImpuesto"]. ' = ' .$valorBase);
          }
          } else
          {
          // si el impuesto no esta incluido, entonces el precio sera la base de los impuestos
          $valorBase = $array_precios[$regid];
          //$respuesta->alert('Base Neta '.$valorBase);
          }


          // luego de que tenemos la base, le aplicamos el descuento para obtener el impuesto
          $valorDescuento = $valorBase * ($descuento / 100);

          // con el valor base y el descuento aplicado, obetenmos el valor Gravado que es la base de impuestos
          $valorGravado = $valorBase - $valorDescuento;

          $valorImpUnit = (($imp[$regimp]["tipoValorImpuesto"] == 'P') ? ($valorGravado * ($imp[$regimp]["valorImpuesto"] / 100)) : $imp[$regimp]["valorImpuesto"] );

          // acumulamos los impuestos unitarios
          $impuestoReferencia += $valorImpUnit;

          $impuestos[] = array("registro" => $regid,
          "Producto_idProducto" => $imp[$regimp]["Producto_idProducto"],
          "tipoImpuesto" => $imp[$regimp]["tipoImpuesto"],
          "referenciaProducto" => $imp[$regimp]["referenciaProducto"],
          "nombreLargoProducto" => $imp[$regimp]["nombreLargoProducto"],
          "cantidadMovimientoDetalle" => $array_cants[$regid],
          "Impuesto_idImpuesto" => $imp[$regimp]["Impuesto_idImpuesto"],
          "nombreImpuesto" => $imp[$regimp]["nombreImpuesto"],
          "valorBaseMovimientoImpuesto" => $valorBase,
          "tipoValorImpuesto" => $imp[$regimp]["tipoValorImpuesto"],
          "valorImpuesto" => $imp[$regimp]["valorImpuesto"],
          "valorUnitarioMovimientoImpuesto" => $valorImpUnit,
          "valorTotalMovimientoImpuesto" => $valorImpUnit * $array_cants[$regid]);
          }
          }
          }
          //print_r($impuestos);

          return $impuestos;
          }

          // Consulta las retenciones de cada uno de los productos del detalle y los calcula en la segunda pestaña
          // Generamos la function para ver si existe;
          function consultarretenciones($idTercero, $idDocumento, $idDocumentoConcepto, $array_ids, $array_cants, $array_precios, $totalBaseImp, $totalImp)
          {

          require_once('../clases/documentocomercial.class.php');
          $documento = new Documento();
          $doc = $documento->ConsultarVistaDocumento("idDocumento = " . $idDocumento, '');

          require_once('../clases/documentoconcepto.class.php');
          $documentoconcepto = new DocumentoConcepto();
          $doccon = $documentoconcepto->ConsultarVistaDocumentoConcepto("idDocumentoConcepto = " . $idDocumentoConcepto, '');

          require_once('../clases/tercero.class.php');
          $tercero = new Tercero();
          $ter = $tercero->ConsultarVistaTercero("idTercero = $idTercero");

          require_once('../clases/producto.class.php');
          $producto = new Producto();

          require_once('../clases/retencion.class.php');
          $retencion = new Retencion();


          // si el documento es del modelo compras u orden de compra, consultamos como proveedor al tercero del documento
          // y como cliente al tercero que ingreso al sistema (Compania del formulario de acceso)
          if ($doc[0]["ModeloContable_idModeloContable"] == 1 or $doc[0]["ModeloContable_idModeloContable"] == 13)
          {
          $prov = $tercero->ConsultarVistaTercero("idTercero = $idTercero");
          $clie = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");
          } else
          {
          // si el documento es del modelo ventas o pedido, consultamos como proveedor al tercero que ingreso al sistema (Compania del formulario de acceso)
          // y como cliente al tercero del documento
          if ($doc[0]["ModeloContable_idModeloContable"] == 6 or $doc[0]["ModeloContable_idModeloContable"] == 12)
          {
          $clie = $tercero->ConsultarVistaTercero("idTercero = $idTercero");
          $prov = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");
          } else
          {
          $clie = $tercero->ConsultarVistaTercero("idTercero = $idTercero");
          $prov = $tercero->ConsultarVistaTercero("tipoTercero LIKE '%*17*%'");
          }
          }



          $retenciones = array();

          $totalreg = count($array_ids);
          for ($regid = 0;
          $regid < $totalreg;
          $regid++)
          {

          $valorBase = $array_precios[$regid];

          // creamos una variable que sume el valor unitario de las retenciones de cada referencia
          $retencionReferencia = 0;


          // con la base de impuestos calculada, procedemos a calcular las retenciones del producto
          // consultamos las retenciones de uno de los IDS del array
          $ret = $producto->ConsultarVistaProductoRetencion("idProducto = " . $array_ids[$regid], '');


          // recorremos el array consultado para acumular los datos en el array de impuestos
          $totalregret = count($ret);


          for ($regret = 0;
          $regret < $totalregret;
          $regret++)
          {

          $aplicarRet = false;
          // Dependiendo del tipo de retencion, hay condiciones diferentes
          // si es retencion en la fuente
          if ($ret[$regret]["tipoRetencion"] == 'valorReteFuenteMovimientoDetalle')
          {
          // si estamos haciendo una compra, verificamos que el proveedor no sea autoretenedor ni entidad estatal
          // si estamos haciendo una venta, verificamos que el Cliente no sea autoretenedor ni entidad estatal
          //$respuesta->alert($doc[0]["ModeloContable_idModeloContable"]);
          if (isset($doccon[0]["idDocumentoConcepto"]) and $doccon[0]["retencionDocumentoConcepto"] == 1 and
          (($doc[0]["ModeloContable_idModeloContable"] == 1 or $doc[0]["ModeloContable_idModeloContable"] == 13) and
          $prov[0]["esAutoretenedor"] == 0 and
          $prov[0]["esEntidadEstadoTercero"] == 0)
          or
          (($doc[0]["ModeloContable_idModeloContable"] == 6 or $doc[0]["ModeloContable_idModeloContable"] == 12) and
          $clie[0]["esAutoretenedor"] == 0 and
          $clie[0]["esEntidadEstadoTercero"] == 0 ))
          {

          $aplicarRet = true;
          }
          }

          //$respuesta->alert($aplicarRet);
          // si la condicion segun el tipo de retencion devuelve verdadero, hacemos el calculo
          if ($aplicarRet == true)
          {

          // la base para retencion es la misma base de impuestos totalizada por la cantidad
          $baseRetencion = $totalBaseImp;
          $baseCalculo = ($ret[$regret]["baseCalculoRetencion"] == 'valorBaseMovimientoDetalle' ? ($array_precios[$regid] * $array_cants[$regid]) : $totalImp);

          // Si el valor base de impuestos, es mayor o igual a la Base de la retencion, calculamos la retencion
          if ($baseRetencion >= $ret[$regret]["valorBaseRetencion"])
          {
          $valorRetUnit = ((float) $baseCalculo *
          ((float) $ret[$regret]["porcentajeJuridicoRetencion"] / 100));
          } else
          {
          // sino se debe aplicar la retencion la ponemos en cero
          $valorRetUnit = 0;
          }



          $retenciones[] = array("registro" => $regid,
          "Producto_idProducto" => $ret[$regret]["idProducto"],
          "tipoRetencion" => $ret[$regret]["tipoRetencion"],
          "referenciaProducto" => $ret[$regret]["referenciaProducto"],
          "nombreLargoProducto" => $ret[$regret]["nombreLargoProducto"],
          "cantidadMovimientoDetalle" => $array_cants[$regid],
          "Retencion_idRetencion" => $ret[$regret]["Retencion_idRetencion"],
          "nombreRetencion" => $ret[$regret]["nombreRetencion"],
          "valorBaseMovimientoRetencion" => $baseRetencion,
          "valorBaseRetencion" => $ret[$regret]["valorBaseRetencion"],
          "porcentajeJuridicoRetencion" => $ret[$regret]["porcentajeJuridicoRetencion"],
          "porcentajeNaturalRetencion" => $ret[$regret]["porcentajeNaturalRetencion"],
          "valorUnitarioMovimientoRetencion" => $valorRetUnit / ($array_cants[$regid] == 0 ? 1 : $array_cants[$regid]),
          "valorTotalMovimientoRetencion" => $valorRetUnit);


          }
          }

          ////////////////////////////////////////////////////
          // CALCULAMOS LA RETENCION DE IMPESTO A LAS       //
          // VENTAS (RETEIVA), ESTA NO ESTA ASOCIADA A LOS  //
          // PRODUCTOS, SINO QUE SE CONSULTA DE LA          //
          // TABLA DE RETENCIONES Y SE LE APLICA AL         //
          // PRODUCTO SEGUN SE CUMPLA LA CONDICION          //
          ////////////////////////////////////////////////////
          // hay una retencion qu eno se le aplica a cada producto en el maestro de productos, esta es el
          // rete iva y se le calcula a todos los productos en el movimiento, siempre y cuando se haya calculado retefuente
          // o el producto tuviera retencion y la base daba para calcularla pero por las condiciones del proveedor no se hizo
          // comparamos si tenia retefuente y la base de impuestos era suficuente para calcularle la retefuente
          //	if($retencionReferencia > 0)
          //$respuesta->alert($totalBaseImp .'>='. $ret[0]["valorBaseRetencion"]);
          if (isset($ret[0]["valorBaseRetencion"]) and $totalBaseImp >= $ret[0]["valorBaseRetencion"])
          {
          $retiva = $retencion->ConsultarVistaRetencion("tipoRetencion = 'valorReteIvaMovimientoDetalle'", '');
          $prod = $producto->ConsultarVistaProducto("idProducto = " . $array_ids[$regid], '');
          //print_r($retiva);
          // recorremos el array consultado para acumular los datos en el array de impuestos
          $totalregret = count($retiva);


          for ($regret = 0;
          $regret < $totalregret;
          $regret++)
          {

          $aplicarRet = false;
          // Dependiendo del tipo de retencion, hay condiciones diferentes
          // si es retencion al impuesto a las ventas (rete IVA)
          if ($retiva[$regret]["tipoRetencion"] == 'valorReteIvaMovimientoDetalle')
          {
          // si el cliente es gran contribuyente o es entidad estatal y el proveedor no es ninguna de las anteriores O
          // si el cliente es regimen COMUN y el proveedor no es gran contribuyente ni entidad estatal
          if (($clie[0]["esGranContribuyente"] == 1 or $clie[0]["esEntidadEstadoTercero"] == 1) and ($prov[0]["esGranContribuyente"] == 0 and
          $prov[0]["esEntidadEstadoTercero"] == 0) or
          ($clie[0]["regimenVentasTercero"] == 'COMUN' ) and ($prov[0]["esGranContribuyente"] == 0 and $prov[0]["esEntidadEstadoTercero"] == 0 and
          $prov[0]["regimenVentasTercero"] != 'COMUN' ))
          {
          $aplicarRet = true;
          }
          }

          //$respuesta->alert($aplicarRet);
          // si la condicion segun el tipo de retencion devuelve verdadero, hacemos el calculo
          if ($aplicarRet == true)
          {
          // la base para retencion es la misma base de impuestos totalizada por la cantidad
          $baseRetencion = $totalBaseImp;
          $baseCalculo = ($retiva[$regret]["baseCalculoRetencion"] == 'valorBaseMovimientoDetalle' ? ($array_precios[$regid] * $array_cants[$regid]) : $totalImp);

          // Si el valor base de impuestos, es mayor o igual a la Base de la retencion, calculamos la retencion
          if ($baseRetencion >= $retiva[$regret]["valorBaseRetencion"])
          {
          $valorRetUnit = ((float) $baseCalculo *
          ((float) $retiva[$regret]["porcentajeJuridicoRetencion"] / 100));
          } else
          {
          // sino se debe aplicar la retencion la ponemos en cero
          $valorRetUnit = 0;
          }


          $retenciones[] = array("registro" => $regid,
          "Producto_idProducto" => $prod[$regret]["idProducto"],
          "tipoRetencion" => $retiva[$regret]["tipoRetencion"],
          "referenciaProducto" => $prod[$regret]["referenciaProducto"],
          "nombreLargoProducto" => $prod[$regret]["nombreLargoProducto"],
          "cantidadMovimientoDetalle" => $array_cants[$regid],
          "Retencion_idRetencion" => $retiva[$regret]["idRetencion"],
          "nombreRetencion" => $retiva[$regret]["nombreRetencion"],
          "valorBaseMovimientoRetencion" => $baseCalculo,
          "valorBaseRetencion" => $retiva[$regret]["valorBaseRetencion"],
          "porcentajeJuridicoRetencion" => $retiva[$regret]["porcentajeJuridicoRetencion"],
          "porcentajeNaturalRetencion" => $retiva[$regret]["porcentajeNaturalRetencion"],
          "valorUnitarioMovimientoRetencion" => $valorRetUnit / ($array_cants[$regid] == 0 ? 1 : $array_cants[$regid]),
          "valorTotalMovimientoRetencion" => $valorRetUnit);
          }
          }
          }


          ////////////////////////////////////////////////
          // LUEGO DE CALCULAR LAS RETENCIONES BASICAS  //
          // PASAMOS A CALCULAR LA RETENCION DE         //
          // INDUSTRIA Y COMERCIO QUE DEPENDE DE LA     //
          // ACTIVIDAD ECONOMICA Y DE LA CIUDAD         //
          ////////////////////////////////////////////////
          //$respuesta->alert($ret[$regret]["referenciaProducto"]);
          // primero verificamos que el proveedor tenga asociada una actividad economica y que sea de la misma ciudad del cliente
          if ((($doc[0]["ModeloContable_idModeloContable"] == 1 or $doc[0]["ModeloContable_idModeloContable"] == 13) and
          $prov[0]["ActividadEconomica_idActividadEconomica"] > 0 and $prov[0]["Ciudad_idCiudad"] == $clie[0]["Ciudad_idCiudad"]) or
          (($doc[0]["ModeloContable_idModeloContable"] == 6 or $doc[0]["ModeloContable_idModeloContable"] == 12) and
          $clie[0]["ActividadEconomica_idActividadEconomica"] > 0 and $prov[0]["Ciudad_idCiudad"] == $clie[0]["Ciudad_idCiudad"]))
          {
          // consultamos la actividad economica para saber la tarifa a aplicar
          require_once '../clases/actividadeconomica.class.php';
          $actividadeconomica = new ActividadEconomica();
          $codigo = ($doc[0]["ModeloContable_idModeloContable"] == 1 or $doc[0]["ModeloContable_idModeloContable"] == 13) ? $prov[0]["ActividadEconomica_idActividadEconomica"] : $clie[0]["ActividadEconomica_idActividadEconomica"];
          $ica = $actividadeconomica->ConsultarVistaActividadEconomica("idActividadEconomica = " . $codigo);

          $valorReteIca = 0;
          if (isset($ica[0]["tipoTarifaReteICAActividadEconomica"]))
          {
          // calculamos el valor a retener sobre la base de impuestos del producto
          $valorReteIca = (($array_precios[$regid] * $array_cants[$regid]) / (float) $ica[0]["tipoTarifaReteICAActividadEconomica"]) *
          (float) $ica[0]["tarifaReteICAActividadEconomica"];
          }
          }
          }
          //print_r($retenciones);

          return $retenciones;
          } */

        function llenarPropiedadesMovimiento($encabezado, $detalle, $origen = 'interface', $listaprecio = '', $listapreciotercero = '', $mediopago = array()) {

            $ruta = dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR;

            // instanciamos la clase movimiento y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once $ruta.'movimiento.class.php';
            $movimiento = new Movimiento();

            require_once $ruta.'producto.class.php';
            $producto = new Producto();

            require_once $ruta.'periodo.class.php';
            $periodo = new Periodo();

            require_once $ruta.'formapago.class.php';
            $formapago = new FormaPago();

            require_once($ruta.'documentocomercial.class.php');
            if (!isset($documento))
            {
                $documento = new Documento();
            }

            $retorno = array();
            // contamos los registros del encabezado
            $totalreg = (isset($encabezado[0]["numeroMovimiento"]) ? count($encabezado) : 0);

            //print_r($encabezado);
            //echo '<br>';
            //                 print_r($encabezado);
            //print_r($detalle);
            //                 exit();
            //echo '<br>';
            $nuevoserrores = $this->validarMovimiento($encabezado, $detalle, $listaprecio, $listapreciotercero, $origen);
            // print_r($nuevoserrores);
            //exit();
            ////
            if (!isset($nuevoserrores[0]["error"]) or $nuevoserrores[0]["error"] == '') {
                //                    echo "<br>entra1<br>";
                //                    return;
                for ($i = 0; $i < $totalreg; $i++) {
                    //                    echo "<br> entra for encabezado<br>";
                    //echo " entra if isset ";
                    // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys

                    $movimiento->Movimiento();
                    //echo 'registros de detalle '.count($movimiento->idMovimientoDetalle)."<br><br>";
                    $movimiento->idMovimiento = (isset($encabezado[$i]["idMovimiento"]) ? $encabezado[$i]["idMovimiento"] : 0);

                    $movimiento->Documento_idDocumento = (isset($encabezado[$i]["Documento_idDocumento"]) ? $encabezado[$i]["Documento_idDocumento"] : 0);
                    $movimiento->DocumentoConcepto_idDocumentoConcepto = (isset($encabezado[$i]["DocumentoConcepto_idDocumentoConcepto"]) ? $encabezado[$i]["DocumentoConcepto_idDocumentoConcepto"] : 0);

                    $movimiento->prefijoMovimiento = (isset($encabezado[$i]["prefijoMovimiento"]) ? $encabezado[$i]["prefijoMovimiento"] : '');
                    $movimiento->sufijoMovimiento = (isset($encabezado[$i]["sufijoMovimiento"]) ? $encabezado[$i]["sufijoMovimiento"] : '');
                    $movimiento->fechaElaboracionMovimiento = (isset($encabezado[$i]["fechaElaboracionMovimiento"]) ? $encabezado[$i]["fechaElaboracionMovimiento"] : date("Y-m-d"));
                    $movimiento->horaElaboracionMovimiento = (isset($encabezado[$i]["horaElaboracionMovimiento"]) ? $encabezado[$i]["horaElaboracionMovimiento"] : date("H:i:s"));

                    // obtenemos el período contable segun la fecha de elaboracion del documento
                    $datoper = $periodo->ConsultarVistaPeriodo("fechaInicialPeriodo <= '" . $movimiento->fechaElaboracionMovimiento .
                            "' and fechaFinalPeriodo >= '" . $movimiento->fechaElaboracionMovimiento . "'");
                    $movimiento->Periodo_idPeriodo = (isset($datoper[0]["idPeriodo"]) ? $datoper[0]["idPeriodo"] : 0);
                    $datosDoc = $documento->ConsultarVistaDocumento("idDocumento = $movimiento->Documento_idDocumento");

                    $sqlTerc = "Select  FormaPago_idFormaPago, FormaPago_idFormaPagoCompra
                                                        From    Tercero
                                                        Where   idTercero = " . $encabezado[$i]["Tercero_idTercero"];
                    $bd = Db::getInstance();
                    $datoTerc = $bd->ConsultarVista($sqlTerc);

                    if ($datosDoc[0]['ModeloContable_idModeloContable'] == 1 OR $datosDoc[0]['ModeloContable_idModeloContable'] == 2 OR $datosDoc[0]['ModeloContable_idModeloContable'] == 13 OR $datosDoc[0]['ModeloContable_idModeloContable'] == 20)
                        $idFormaPago = $datoTerc[0]["FormaPago_idFormaPagoCompra"];
                    elseif ($datosDoc[0]['ModeloContable_idModeloContable'] == 6 OR $datosDoc[0]['ModeloContable_idModeloContable'] == 12 OR $datosDoc[0]['ModeloContable_idModeloContable'] == 7)
                        $idFormaPago = $datoTerc[0]["FormaPago_idFormaPago"];
                    // consultamos el tercero para conocer la forma de pago, si la forma de pago del array de encabezado esta vacia, tomamos por defecto la del tercero

                    $movimiento->FormaPago_idFormaPago = (isset($encabezado[$i]["FormaPago_idFormaPago"]) ? $encabezado[$i]["FormaPago_idFormaPago"] : (isset($idFormaPago) ? $idFormaPago : 0 ));
                    // con el id de la forma de pago, buscamos cuantos días de pago tiene
                    $datopago = $formapago->ConsultarVistaFormaPago("idFormaPago = '" . $movimiento->FormaPago_idFormaPago . "'");
                    $dias = (isset($datopago[0]["diasFormaPago"]) ? $datopago[0]["diasFormaPago"] : 0);

                    // calculamos la fecha de vencimiento según la fecha de elaboracion y la forma de pago del documento
                    $movimiento->fechaVencimientoMovimiento = $this->calcularvencimiento($movimiento->fechaElaboracionMovimiento, $dias);
                    $movimiento->fechaMinimaMovimiento = (isset($encabezado[$i]["fechaMinimaMovimiento"]) ? $encabezado[$i]["fechaMinimaMovimiento"] : '');
                    $movimiento->fechaMaximaMovimiento = (isset($encabezado[$i]["fechaMaximaMovimiento"]) ? $encabezado[$i]["fechaMaximaMovimiento"] : '');
                    $movimiento->fechaSolicitudMovimiento = (isset($encabezado[$i]["fechaSolicitudMovimiento"]) ? $encabezado[$i]["fechaSolicitudMovimiento"] : '');

                    $movimiento->numeroMovimiento = (isset($encabezado[$i]["numeroMovimiento"]) ? $encabezado[$i]["numeroMovimiento"] : '');
                    $movimiento->Tercero_idTercero = (isset($encabezado[$i]["Tercero_idTercero"]) ? $encabezado[$i]["Tercero_idTercero"] : 0);
                    $movimiento->Tercero_idPrincipal = (isset($encabezado[$i]["Tercero_idPrincipal"]) ? $encabezado[$i]["Tercero_idPrincipal"] : 0);
                    $movimiento->Tercero_idVendedor = (isset($encabezado[$i]["Tercero_idVendedor"]) ? $encabezado[$i]["Tercero_idVendedor"] : 0);
                    $movimiento->CentroCosto_idCentroCosto = (isset($encabezado[$i]["CentroCosto_idCentroCosto"]) ? $encabezado[$i]["CentroCosto_idCentroCosto"] : 0);
                    //echo 'llenar Movimiento id tercero '.$movimiento->Tercero_idTercero.' id principal '. $movimiento->Tercero_idPrincipal;

                    $movimiento->Tercero_idEntrega = (isset($encabezado[$i]["Tercero_idEntrega"]) ? $encabezado[$i]["Tercero_idEntrega"] : 0);
                    $movimiento->tipoMovimiento = (isset($encabezado[$i]["tipoMovimiento"]) ? $encabezado[$i]["tipoMovimiento"] : 'NORMAL');
                    $movimiento->Incapacidad_idIncapacidad = (isset($encabezado[$i]["Incapacidad_idIncapacidad"]) ? $encabezado[$i]["Incapacidad_idIncapacidad"] : 0);

                    $movimiento->tipoReferenciaInternoMovimiento = (isset($encabezado[$i]["tipoReferenciaInternoMovimiento"]) ? $encabezado[$i]["tipoReferenciaInternoMovimiento"] : 0);
                    $movimiento->numeroReferenciaInternoMovimiento = (isset($encabezado[$i]["numeroReferenciaInternoMovimiento"]) ? $encabezado[$i]["numeroReferenciaInternoMovimiento"] : '');
                    $movimiento->tipoReferenciaExternoMovimiento = (isset($encabezado[$i]["tipoReferenciaExternoMovimiento"]) ? $encabezado[$i]["tipoReferenciaExternoMovimiento"] : 0);
                    $movimiento->numeroReferenciaExternoMovimiento = (isset($encabezado[$i]["numeroReferenciaExternoMovimiento"]) ? $encabezado[$i]["numeroReferenciaExternoMovimiento"] : '');
                    $movimiento->Importacion_idImportacion = (isset($encabezado[$i]["Importacion_idImportacion"]) ? $encabezado[$i]["Importacion_idImportacion"] : 0);
                    $movimiento->Embarque_idEmbarque = (isset($encabezado[$i]["Embarque_idEmbarque"]) ? $encabezado[$i]["Embarque_idEmbarque"] : 0);


                    $movimiento->Moneda_idMoneda = (isset($encabezado[$i]["Moneda_idMoneda"]) ? $encabezado[$i]["Moneda_idMoneda"] : 0);
                    $movimiento->tasaCambioMovimiento = (!empty($encabezado[$i]["tasaCambioMovimiento"]) ? $encabezado[$i]["tasaCambioMovimiento"] : 0);
                    $movimiento->factorMovimiento = (!empty($encabezado[$i]["factorMovimiento"]) ? $encabezado[$i]["factorMovimiento"] : 0);

                    $movimiento->Incoterm_idIncoterm = (isset($encabezado[$i]["Incoterm_idIncoterm"]) ? $encabezado[$i]["Incoterm_idIncoterm"] : 0);
                    $movimiento->observacionMovimiento = (isset($encabezado[$i]["observacionMovimiento"]) ? $encabezado[$i]["observacionMovimiento"] : '');

                    $movimiento->totalUnidadesMovimiento = 0;
                    $movimiento->valorFleteMovimiento = (!empty($encabezado[$i]["valorFleteMovimiento"]) ? $encabezado[$i]["valorFleteMovimiento"] : 0);
                    $movimiento->valorSeguroMovimiento = (!empty($encabezado[$i]["valorSeguroMovimiento"]) ? $encabezado[$i]["valorSeguroMovimiento"] : 0);
                    $movimiento->valorAcarreoMovimiento = (!empty($encabezado[$i]["valorAcarreoMovimiento"]) ? $encabezado[$i]["valorAcarreoMovimiento"] : 0);

                    $movimiento->estadoMovimiento = 'ACTIVO';


                    $movimiento->SegLogin_idUsuarioCrea = (isset($encabezado[$i]["SegLogin_idUsuarioCrea"]) ? $encabezado[$i]["SegLogin_idUsuarioCrea"] : '');
                    $movimiento->impresoMovimiento = (isset($encabezado[$i]["impresoMovimiento"]) ? $encabezado[$i]["impresoMovimiento"] : '');
                    $movimiento->SegLogin_idUsuarioAnula = (isset($encabezado[$i]["SegLogin_idUsuarioAnula"]) ? $encabezado[$i]["SegLogin_idUsuarioAnula"] : '');
                    $movimiento->fechaAnuladoMovimiento = (isset($encabezado[$i]["fechaAnuladoMovimiento"]) ? $encabezado[$i]["fechaAnuladoMovimiento"] : '');
                    $movimiento->LiquidacionNomina_idLiquidacionNomina = (isset($encabezado[$i]["LiquidacionNomina_idLiquidacionNomina"]) ? $encabezado[$i]["LiquidacionNomina_idLiquidacionNomina"] : 0);
                    $movimiento->Embarque_idTransito = (isset($encabezado[$i]["Embarque_idTransito"]) ? $encabezado[$i]["Embarque_idTransito"] : 0);
                    $movimiento->MercanciaExtranjera_idMercanciaExtranjera = (isset($encabezado[$i]["MercanciaExtranjera_idMercanciaExtranjera"]) ? $encabezado[$i]["MercanciaExtranjera_idMercanciaExtranjera"] : 0);
                    $movimiento->Nacionalizacion_idNacionalizacion = (isset($encabezado[$i]["Nacionalizacion_idNacionalizacion"]) ? $encabezado[$i]["Nacionalizacion_idNacionalizacion"] : 0);

                    $movimiento->tipoDescuentoMovimiento = (isset($encabezado[$i]["tipoDescuentoMovimiento"]) ? $encabezado[$i]["tipoDescuentoMovimiento"] : 'Porcentaje');
                    $movimiento->nivelDescuentoMovimiento = (isset($encabezado[$i]["nivelDescuentoMovimiento"]) ? $encabezado[$i]["nivelDescuentoMovimiento"] : 'Detalle');
                    $movimiento->CentroProduccion_idCentroProduccion = (isset($encabezado[$i]["CentroProduccion_idCentroProduccion"]) ? $encabezado[$i]["CentroProduccion_idCentroProduccion"] : 0);
                    $movimiento->OrdenProduccion_idOrdenProduccion = (isset($encabezado[$i]["OrdenProduccion_idOrdenProduccion"]) ? $encabezado[$i]["OrdenProduccion_idOrdenProduccion"] : 0);
                    $movimiento->ListaPrecio_idListaPrecio = (isset($encabezado[$i]["ListaPrecio_idListaPrecio"]) ? $encabezado[$i]["ListaPrecio_idListaPrecio"] : (isset($nuevoserrores[0]["ListaPrecio_idListaPrecioDetalle"]) ? $nuevoserrores[0]["ListaPrecio_idListaPrecioDetalle"] : 0));
                    // consultamos el estado del WMS por defecto
                    //if($datosDoc[0]["afectaWMSDocumento"])
                    $movimiento->estadoWMSMovimiento = ($datosDoc[0]["afectaWMSDocumento"] == 'SI' ? $datosDoc[0]["estadoWMSDocumento"] : 'CERRADO');

                    $subtotal = 0;
                    $descuento = 0;
                    $base = 0;
                    $impuesto = 0;
                    $retencion = 0;
                    $reteiva = 0;
                    $totalUnidades = 0;

                    // por cada registro del encabezado, recorremos el detalle para obtener solo los datos del mismo numero de movimiento del encabezado, con estos
                    // llenamos arrays por cada campo
                    $totaldet = (isset($detalle[0]["numeroMovimiento"]) ? count($detalle) : 0);

                    $ids = '';
                    $precios = '';
                    $descuentos = '';
                    $cants = '';
                    $regs = '';
                    $ivas = '';

                    $totalBaseImp = 0;
                    $totalImp = 0;

                    $totalImpoc = 0;
                    $totalIva = 0;
                    $totalImpDep = 0;
                    // llevamos un contador de registros por cada producto del detalle
                    $registroact = 0;

                    for ($j = 0; $j < $totaldet; $j++) {
                        if (isset($encabezado[$i]["Documento_idDocumento"]) and
                                isset($detalle[$j]["Documento_idDocumento"]) and
                                $encabezado[$i]["Documento_idDocumento"] == $detalle[$j]["Documento_idDocumento"]) {
                            //                            echo "<br> entra for detalle <br>";
                            if (isset($encabezado[$i]["numeroMovimiento"]) and
                                    isset($detalle[$j]["numeroMovimiento"]) and
                                    $encabezado[$i]["numeroMovimiento"] == $detalle[$j]["numeroMovimiento"]) {
                                //echo "id lista: ".$nuevoserrores[$j]["ListaPrecio_idListaPrecioDetalle"]." precio de lista: ".$nuevoserrores[$j]["precioListaMovimientoDetalle"]." valor bruto: ".$nuevoserrores[$j]["valorBrutoMovimientoDetalle"]."<br>";
                                // consultamos los id del iva y la retencion de cada producto
                                $imp = $producto->ConsultarVistaProductoImpuesto("idProducto IN (" . $detalle[$j]["Producto_idProducto"] . ")", "idProducto", "Impuesto_idImpuesto");
                                $ret = $producto->ConsultarVistaProductoRetencion("idProducto IN (" . $detalle[$j]["Producto_idProducto"] . ")", "idProducto", "Retencion_idRetencion");
                                $idImpuesto = isset($imp[0]['Impuesto_idImpuesto']) ? $imp[0]['Impuesto_idImpuesto'] : 0;
                                $idRetencion = isset($ret[0]['Retencion_idRetencion']) ? $ret[0]['Retencion_idRetencion'] : 0;

                                $movimiento->idMovimientoDetalle[$registroact] = 0;
                                $movimiento->Bodega_idBodegaOrigen[$registroact] = (isset($detalle[$j]["Bodega_idBodegaOrigen"]) ? $detalle[$j]["Bodega_idBodegaOrigen"] : 0);
                                $movimiento->Bodega_idBodegaDestino[$registroact] = (isset($detalle[$j]["Bodega_idBodegaDestino"]) ? $detalle[$j]["Bodega_idBodegaDestino"] : 0);
                                $movimiento->ProductoSerie_idProductoSerie[$registroact] = (isset($detalle[$j]["ProductoSerie_idProductoSerie"]) ? $detalle[$j]["ProductoSerie_idProductoSerie"] : 0);
                                $movimiento->numeroProductoSerie[$registroact] = (isset($detalle[$j]["numeroProductoSerie"]) ? $detalle[$j]["numeroProductoSerie"] : 0);
                                $movimiento->numeroLoteMovimientoDetalle[$registroact] = (isset($detalle[$j]["numeroLoteMovimientoDetalle"]) ? $detalle[$j]["numeroLoteMovimientoDetalle"] : '');
                                $movimiento->Movimiento_idDocumentoRef[$registroact] = (isset($detalle[$j]["Movimiento_idDocumentoRef"]) ? $detalle[$j]["Movimiento_idDocumentoRef"] : 0);
                                $movimiento->Poliza_idPoliza[$registroact] = (isset($detalle[$j]["Poliza_idPoliza"]) ? $detalle[$j]["Poliza_idPoliza"] : 0);
                                $movimiento->Producto_idProducto[$registroact] = (isset($detalle[$j]["Producto_idProducto"]) ? $detalle[$j]["Producto_idProducto"] : 0);
                                $movimiento->Producto_idSustitutoPrincipal[$registroact] = (isset($detalle[$j]["Producto_idProducto"]) ? $detalle[$j]["Producto_idProducto"] : 0);
                                $movimiento->Tercero_idAlmacen[$registroact] = (isset($detalle[$j]["Tercero_idAlmacen"]) ? $detalle[$j]["Tercero_idAlmacen"] : 0);
                                $movimiento->cantidadMovimientoDetalle[$registroact] = (isset($detalle[$j]["cantidadMovimientoDetalle"]) ? $detalle[$j]["cantidadMovimientoDetalle"] : 0);
                                $movimiento->ListaPrecio_idListaPrecioDetalle[$registroact] = (isset($encabezado[$i]["ListaPrecio_idListaPrecio"]) ? $encabezado[$i]["ListaPrecio_idListaPrecio"] : (isset($nuevoserrores[$j]["ListaPrecio_idListaPrecioDetalle"]) ? $nuevoserrores[$j]["ListaPrecio_idListaPrecioDetalle"] : 0));
                                $movimiento->precioListaMovimientoDetalle[$registroact] = (isset($nuevoserrores[$j]["precioListaMovimientoDetalle"]) ? $nuevoserrores[$j]["precioListaMovimientoDetalle"] : (isset($detalle[$j]["precioListaMovimientoDetalle"]) ? $detalle[$j]["precioListaMovimientoDetalle"] : 0));
                                $movimiento->valorBrutoMovimientoDetalle[$registroact] = (isset($nuevoserrores[$j]["valorBrutoMovimientoDetalle"]) ? $nuevoserrores[$j]["valorBrutoMovimientoDetalle"] : (isset($detalle[$j]["valorBrutoMovimientoDetalle"]) ? $detalle[$j]["valorBrutoMovimientoDetalle"] : 0));
                                $movimiento->BodegaUbicacion_idBodegaUbicacionOrigen = (isset($detalle[$j]["BodegaUbicacion_idBodegaUbicacionOrigen"]) ? $detalle[$j]["BodegaUbicacion_idBodegaUbicacionOrigen"] : 0);
                                $movimiento->BodegaUbicacion_idBodegaUbicacionDestino[$registroact] = (isset($detalle[$j]["BodegaUbicacion_idBodegaUbicacionDestino"]) ? $detalle[$j]["BodegaUbicacion_idBodegaUbicacionDestino"] : 0);
                                $movimiento->Embalaje_idEmbalaje[$registroact] = (isset($detalle[$j]["Embalaje_idEmbalaje"]) ? $detalle[$j]["Embalaje_idEmbalaje"] : 0);
                                $movimiento->CentroCosto_idCentroCostoDetalle[$registroact] = (isset($detalle[$j]["CentroCosto_idCentroCostoDetalle"]) ? $detalle[$j]["CentroCosto_idCentroCostoDetalle"] : 0);

                                // descuento comercial
                                $movimiento->porcentajeDescuentoMovimientoDetalle[$registroact] = (isset($detalle[$j]["porcentajeDescuentoMovimientoDetalle"]) ? $detalle[$j]["porcentajeDescuentoMovimientoDetalle"] : 0);
                                $movimiento->valorDescuentoMovimientoDetalle[$registroact] = (isset($nuevoserrores[$j]["valorBrutoMovimientoDetalle"]) ? $nuevoserrores[$j]["valorBrutoMovimientoDetalle"] : (isset($detalle[$j]["valorBrutoMovimientoDetalle"]) ? $detalle[$j]["valorBrutoMovimientoDetalle"] : 0)) *
                                        (isset($detalle[$j]["porcentajeDescuentoMovimientoDetalle"]) ? $detalle[$j]["porcentajeDescuentoMovimientoDetalle"] : 0) / 100;
                                $movimiento->valorBaseMovimientoDetalle[$registroact] = (isset($nuevoserrores[$j]["valorBrutoMovimientoDetalle"]) ? $nuevoserrores[$j]["valorBrutoMovimientoDetalle"] : (isset($detalle[$j]["valorBrutoMovimientoDetalle"]) ? $detalle[$j]["valorBrutoMovimientoDetalle"] : 0)) -
                                        (isset($detalle[$j]["valorDescuentoMovimientoDetalle"]) ? $detalle[$j]["valorDescuentoMovimientoDetalle"] : 0);

                                // campos de descuento financiero para las NIIF
                                $movimiento->porcentajeDescuentoFinancieroMovimientoDetalle[$registroact] = (isset($detalle[$j]["porcentajeDescuentoFinancieroMovimientoDetalle"]) ? $detalle[$j]["porcentajeDescuentoFinancieroMovimientoDetalle"] : 0);
                                $movimiento->valorDescuentoFinancieroMovimientoDetalle[$registroact] = (isset($detalle[$j]["valorDescuentoFinancieroMovimientoDetalle"]) ? $detalle[$j]["valorDescuentoFinancieroMovimientoDetalle"] : 0);
                                $movimiento->valorBaseNIIFMovimientoDetalle[$registroact] = $movimiento->valorBrutoMovimientoDetalle[$registroact] -
                                        $movimiento->valorDescuentoMovimientoDetalle[$registroact] -
                                        $movimiento->valorDescuentoFinancieroMovimientoDetalle[$registroact];

                                // llenamos los id de iva y retencion antes consultados
                                $movimiento->Impuesto_idIva[$registroact] = (isset($detalle[$j]["Impuesto_idIva"]) ? $detalle[$j]["Impuesto_idIva"] : 0);
                                $movimiento->Impuesto_idReteFuente[$registroact] = (isset($detalle[$j]["Impuesto_idReteFuente"]) ? $detalle[$j]["Impuesto_idReteFuente"] : 0);

                                $movimiento->Impuesto_idReteCree[$registroact] = 0;

                                $movimiento->volumenTotalMovimientoDetalle[$registroact] = (isset($detalle[$j]["volumenTotalMovimientoDetalle"]) ? $detalle[$j]["volumenTotalMovimientoDetalle"] : 0);
                                $movimiento->pesoTotalMovimientoDetalle[$registroact] = (isset($detalle[$j]["pesoTotalMovimientoDetalle"]) ? $detalle[$j]["pesoTotalMovimientoDetalle"] : 0);
                                $movimiento->numeroCajasMovimientoDetalle[$registroact] = (isset($detalle[$j]["numeroCajasMovimientoDetalle"]) ? $detalle[$j]["numeroCajasMovimientoDetalle"] : 0);

                                $movimiento->precioVentaPublicoMovimientoDetalle[$registroact] = (isset($detalle[$j]["precioVentaPublicoMovimientoDetalle"]) ? $detalle[$j]["precioVentaPublicoMovimientoDetalle"] : 0);
                                $movimiento->margenUtilidadMovimientoDetalle[$registroact] = (isset($detalle[$j]["margenUtilidadMovimientoDetalle"]) ? $detalle[$j]["margenUtilidadMovimientoDetalle"] : 0);

                                // datos de marcacion de productos
                                $movimiento->EtiquetaProducto_idEtiquetaProducto[$registroact] = (isset($detalle[$j]["EtiquetaProducto_idEtiquetaProducto"]) ? $detalle[$j]["EtiquetaProducto_idEtiquetaProducto"] : 0);
                                $movimiento->etiquetaSeccionMovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaSeccionMovimientoDetalle"]) ? $detalle[$j]["etiquetaSeccionMovimientoDetalle"] : '');
                                $movimiento->etiquetaClasificacionMovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaClasificacionMovimientoDetalle"]) ? $detalle[$j]["etiquetaClasificacionMovimientoDetalle"] : '');
                                $movimiento->etiquetaFechaMovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaFechaMovimientoDetalle"]) ? $detalle[$j]["etiquetaFechaMovimientoDetalle"] : '');
                                $movimiento->etiquetaPrecioVentaNormalMovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaPrecioVentaNormalMovimientoDetalle"]) ? $detalle[$j]["etiquetaPrecioVentaNormalMovimientoDetalle"] : '');
                                $movimiento->etiquetaPrecioVentaOfertaMovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaPrecioVentaOfertaMovimientoDetalle"]) ? $detalle[$j]["etiquetaPrecioVentaOfertaMovimientoDetalle"] : '');
                                $movimiento->etiquetaLugarExhibicionMovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaLugarExhibicionMovimientoDetalle"]) ? $detalle[$j]["etiquetaLugarExhibicionMovimientoDetalle"] : '');
                                $movimiento->etiquetaDescripcion1MovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaDescripcion1MovimientoDetalle"]) ? $detalle[$j]["etiquetaDescripcion1MovimientoDetalle"] : '');
                                $movimiento->etiquetaDescripcion2MovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaDescripcion2MovimientoDetalle"]) ? $detalle[$j]["etiquetaDescripcion2MovimientoDetalle"] : '');
                                $movimiento->etiquetaDescripcion3MovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaDescripcion3MovimientoDetalle"]) ? $detalle[$j]["etiquetaDescripcion3MovimientoDetalle"] : '');
                                $movimiento->etiquetaReferenciaClienteMovimientoDetalle[$registroact] = (isset($detalle[$j]["etiquetaReferenciaClienteMovimientoDetalle"]) ? $detalle[$j]["etiquetaReferenciaClienteMovimientoDetalle"] : '');
                                $movimiento->Lote_idLote[$registroact] = (isset($detalle[$j]["Lote_idLote"]) ? $detalle[$j]["Lote_idLote"] : 0);

                                // inicializamos los impuestos en cero
                                $movimiento->valorIvaMovimientoDetalle[$registroact] = 0;
                                $movimiento->valorImpoconsumoMovimientoDetalle[$registroact] = 0;
                                $movimiento->valorImpDeporteMovimientoDetalle[$registroact] = 0;
                                $movimiento->valorReteCreeMovimientoDetalle[$registroact] = 0;

                                // inicializamos las retenciones en cero
                                $movimiento->valorReteIcaMovimientoDetalle[$registroact] = 0;
                                $movimiento->valorReteFuenteMovimientoDetalle[$registroact] = 0;
                                $movimiento->valorReteIvaMovimientoDetalle[$registroact] = 0;
                                //$movimiento->valorReteOtrosMovimientoDetalle[$registroact] = 0;


                                $movimiento->valorNetoMovimientoDetalle[$registroact] = $movimiento->valorBaseMovimientoDetalle[$registroact];
                                $movimiento->valorTotalMovimientoDetalle[$registroact] = $movimiento->valorBaseMovimientoDetalle[$registroact] *
                                        $movimiento->cantidadMovimientoDetalle[$registroact];
                                $movimiento->observacionMovimientoDetalle[$registroact] = (isset($detalle[$j]["observacionMovimientoDetalle"]) ? $detalle[$j]["observacionMovimientoDetalle"] : '');

                                // luego de tener llenas las matrices, consultamos los impuestos y retenciones
                                //
                                $impuestos = $movimiento->consultarimpuestos($encabezado[$i]["Tercero_idTercero"], $encabezado[$i]["Documento_idDocumento"], $encabezado[$i]["DocumentoConcepto_idDocumentoConcepto"], $movimiento->Producto_idProducto[$registroact], $movimiento->cantidadMovimientoDetalle[$registroact], $movimiento->precioListaMovimientoDetalle[$registroact], $registroact, $movimiento->porcentajeDescuentoMovimientoDetalle[$registroact], $movimiento->fechaElaboracionMovimiento);

                                //print_r($impuestos);
                                // sumamos los impuestos para enviar al calculo de las retenciones la base de impuestos
                                // para esto recorremos el array de impuestos y aplicamos una suma
                                $totalregimp = (isset($impuestos[0]["Producto_idProducto"]) ? count($impuestos) : 0 );

                                if (isset($impuestos[0]["Producto_idProducto"])) {
                                    //echo " entra if isset 3 ";
                                    $totalBaseImp += $impuestos[0]["valorBaseMovimientoImpuesto"] * $impuestos[0]["cantidadMovimientoDetalle"];
                                    $totalImp += $impuestos[0]["valorUnitarioMovimientoImpuesto"] * $impuestos[0]["cantidadMovimientoDetalle"];

                                    // cada impuesto que recorremos, lo vamos acumulando en el campo correspondiente (segun el tipoImpuesto) y en el producto correspondiente
                                    // (segun el registro del array de impuestos)
                                    switch ($impuestos[0]["tipoImpuesto"]) {
                                        case 'valorImpoconsumoMovimientoDetalle' :
                                            $movimiento->valorImpoconsumoMovimientoDetalle[$registroact] += $impuestos[0]["valorUnitarioMovimientoImpuesto"];
                                            $totalImpoc += $impuestos[0]["valorUnitarioMovimientoImpuesto"] * $impuestos[0]["cantidadMovimientoDetalle"];
                                            break;
                                        case 'valorIvaMovimientoDetalle' :
                                            $movimiento->valorIvaMovimientoDetalle[$registroact] += $impuestos[0]["valorUnitarioMovimientoImpuesto"];
                                            $movimiento->valorBrutoMovimientoDetalle[$registroact] = $impuestos[0]["valorBrutoMovimientoImpuesto"];
                                            $movimiento->valorBaseMovimientoDetalle[$registroact] = $impuestos[0]["valorBaseMovimientoImpuesto"];
                                            $movimiento->Impuesto_idIva[$registroact] += $impuestos[0]["Impuesto_idImpuesto"];
                                            $totalIva += $impuestos[0]["valorUnitarioMovimientoImpuesto"] * $impuestos[0]["cantidadMovimientoDetalle"];
                                            break;
                                        case 'valorImpDeporteMovimientoDetalle' :
                                            $movimiento->valorImpDeporteMovimientoDetalle[$registroact] += $impuestos[0]["valorUnitarioMovimientoImpuesto"];
                                            $totalImpDep += $impuestos[0]["valorUnitarioMovimientoImpuesto"] * $impuestos[0]["cantidadMovimientoDetalle"];
                                            break;
                                    }

                                    $ids .= $movimiento->Producto_idProducto[$registroact] . ',';
                                    $precios .= $movimiento->valorBrutoMovimientoDetalle[$registroact] . ',';
                                    $descuentos .= $movimiento->porcentajeDescuentoMovimientoDetalle[$registroact] . ',';
                                    $cants .= $movimiento->cantidadMovimientoDetalle[$registroact] . ',';
                                    $regs .= $registroact . ',';

                                    $ivas .= ($movimiento->valorIvaMovimientoDetalle[$registroact] * $movimiento->cantidadMovimientoDetalle[$registroact]) . ',';


                                    $movimiento->valorBaseMovimientoDetalle[$registroact] = $movimiento->valorBrutoMovimientoDetalle[$registroact] -
                                            $movimiento->valorDescuentoMovimientoDetalle[$registroact];

                                    $movimiento->valorNetoMovimientoDetalle[$registroact] = $movimiento->valorBaseMovimientoDetalle[$registroact] +
                                            $movimiento->valorIvaMovimientoDetalle[$registroact] +
                                            $movimiento->valorImpoconsumoMovimientoDetalle[$registroact] +
                                            $movimiento->valorImpDeporteMovimientoDetalle[$registroact] -
                                            $movimiento->valorReteFuenteMovimientoDetalle[$registroact] -
                                            $movimiento->valorReteIvaMovimientoDetalle[$registroact] -
                                            $movimiento->valorReteCreeMovimientoDetalle[$registroact] -
                                            $movimiento->valorReteIcaMovimientoDetalle[$registroact];

                                    $movimiento->valorTotalMovimientoDetalle[$registroact] = $movimiento->valorNetoMovimientoDetalle[$registroact] *
                                            $movimiento->cantidadMovimientoDetalle[$registroact];

                                    $movimiento->margenUtilidadMovimientoDetalle[$registroact] = $movimiento->precioVentaPublicoMovimientoDetalle[$registroact] /
                                            ($movimiento->valorNetoMovimientoDetalle[$registroact] == 0 ? 1 : (($movimiento->valorNetoMovimientoDetalle[$registroact]) * 100));



                                    //echo $impuestos[0]["tipoImpuesto"].' = '.  $impuestos[0]["valorUnitarioMovimientoImpuesto"]."<br>";
                                    //$movimiento->valorNetoMovimientoDetalle[$registroact] += $impuestos[0]["valorUnitarioMovimientoImpuesto"];
                                    //$movimiento->valorTotalMovimientoDetalle[$registroact] += ($impuestos[0]["valorUnitarioMovimientoImpuesto"] *
                                    //       $impuestos[0]["cantidadMovimientoDetalle"]);
                                }

                                $subtotal += (isset($nuevoserrores[$j]["valorBrutoMovimientoDetalle"]) ? $nuevoserrores[$j]["valorBrutoMovimientoDetalle"] : (isset($movimiento->valorBrutoMovimientoDetalle[$registroact]) ? $movimiento->valorBrutoMovimientoDetalle[$registroact] : 0)) * $detalle[$j]["cantidadMovimientoDetalle"];
                                $descuento += $movimiento->valorDescuentoMovimientoDetalle[$registroact] *
                                        $detalle[$j]["cantidadMovimientoDetalle"];
                                $totalUnidades += $detalle[$j]["cantidadMovimientoDetalle"];

                                // Ancho y numero de Rollo de Telas
                         

                                $registroact++;
                            }
                        }
                    }
                    /* if($encabezado[$i]["porcentajeDescuentoMovimiento"] != 0)
                      {
                      $descuento = ($descuento != 0 ? $descuento : $subtotal * ($encabezado[$i]["porcentajeDescuentoMovimiento"] != 0 ? $encabezado[$i]["porcentajeDescuentoMovimiento"] : 1) / 100);
                      }
                      else
                      {
                      $descuento = 0;
                      } */

                    $base = $subtotal - $descuento;
                    // luego de calculados los impuestos, calculamos las retenciones ya que estas dependen de la base de impuestos de documento
                    $retenciones = $movimiento->consultarretenciones($encabezado[$i]["Tercero_idTercero"], $encabezado[$i]["Documento_idDocumento"], $encabezado[$i]["DocumentoConcepto_idDocumentoConcepto"], substr($ids, 0, strlen($ids) - 1), substr($cants, 0, strlen($cants) - 1), substr($precios, 0, strlen($precios) - 1), substr($ivas, 0, strlen($ivas) - 1), substr($regs, 0, strlen($regs) - 1), substr($descuentos, 0, strlen($descuentos) - 1), $totalBaseImp, $totalImp, ($movimiento->tasaCambioMovimiento == 0 ? 1 : $movimiento->tasaCambioMovimiento), $movimiento->fechaElaboracionMovimiento);

                    // sumamos las retenciones en los campos corres
                    // pondientes
                    //
                    //                print_r($retenciones);
                    $totalregret = (isset($retenciones[0]["Producto_idProducto"]) ? count($retenciones) : 0 );

                    $totalReteFte = 0;
                    $totalReteIva = 0;
                    $totalReteIca = 0;
                    $totalReteOtr = 0;
                    $totalReteCree = 0;


                    for ($ret = 0; $ret < $totalregret; $ret++) {
                        //                            echo " entra for 3 ";
                        // cada retencion que recorremos, la vamos acumulando en el campo correspondiente (segun el tipoRetencion) y en el producto correspondiente
                        // (segun el registro del array de retenciones)
                        switch ($retenciones[$ret]["tipoRetencion"]) {
                            case 'valorReteFuenteMovimientoDetalle' :
                                $movimiento->valorReteFuenteMovimientoDetalle[(int) $retenciones[$ret]["registro"]] += $retenciones[$ret]["valorUnitarioMovimientoRetencion"];
                                $totalReteFte += $retenciones[$ret]["valorUnitarioMovimientoRetencion"] * $retenciones[$ret]["cantidadMovimientoDetalle"];
                                break;
                            case 'valorReteIcaMovimientoDetalle' :
                                $movimiento->valorReteIcaMovimientoDetalle[(int) $retenciones[$ret]["registro"]] += $retenciones[$ret]["valorUnitarioMovimientoRetencion"];
                                $totalReteIca += $retenciones[$ret]["valorUnitarioMovimientoRetencion"] * $retenciones[$ret]["cantidadMovimientoDetalle"];
                                break;
                            case 'valorReteIvaMovimientoDetalle' :
                                $movimiento->valorReteIvaMovimientoDetalle[(int) $retenciones[$ret]["registro"]] += $retenciones[$ret]["valorUnitarioMovimientoRetencion"];
                                $totalReteIva += $retenciones[$ret]["valorUnitarioMovimientoRetencion"] * $retenciones[$ret]["cantidadMovimientoDetalle"];
                                $afecReteIva = $retenciones[$ret]["ReteIvaAfectable"];
                                break;
                            case 'valorReteCreeMovimientoDetalle' :
                                $movimiento->valorReteCreeMovimientoDetalle[(int) $retenciones[$ret]["registro"]] += $retenciones[$ret]["valorUnitarioMovimientoRetencion"];
                                $totalReteCree += $retenciones[$ret]["valorUnitarioMovimientoRetencion"] * $retenciones[$ret]["cantidadMovimientoDetalle"];
                                break;
                        }
                        //echo $retenciones[$ret]["tipoRetencion"].' = '.  $retenciones[$ret]["valorUnitarioMovimientoRetencion"]."<br>";
                    }

                    $movimiento->totalUnidadesMovimiento = $totalUnidades;
                    $movimiento->subtotalMovimiento = $subtotal;
                    $movimiento->porcentajeDescuentoMovimiento = (isset($encabezado[$i]["porcentajeDescuentoMovimiento"]) ? $encabezado[$i]["porcentajeDescuentoMovimiento"] : 0);
                    $movimiento->valorDescuentoMovimiento = $descuento;
                    $movimiento->valorBaseMovimiento = $base;
                    $movimiento->valorIvaMovimiento = $totalImp;

                    // Pendiente llenar estos datos automaticamente en la importacion (son para las NIIF)
                    $movimiento->porcentajeDescuentoFinancieroMovimiento = 0;
                    $movimiento->valorDescuentoFinancieroMovimiento = 0;
                    //$movimiento->valorBaseNIIFMovimiento = 0;

                    $movimiento->valorIvaMovimiento = $totalImp;
                    $movimiento->valorRetencionMovimiento = $totalReteFte;
                    $movimiento->valorReteIvaMovimiento = $totalReteIva;
                    $movimiento->valorReteIcaMovimiento = $totalReteIca;


                    $movimiento->valorTotalMovimiento = number_format(($base + $totalImp - $totalReteFte - (($afecReteIva == 'NO') ? 0 : $totalReteIva) - $totalReteIca + $movimiento->valorFleteMovimiento + $movimiento->valorSeguroMovimiento + $movimiento->valorAcarreoMovimiento), $datosDoc[0]['redondeoTotalDocumento'],'.','');



                    // Importar los medios de pago
                    $totalmed = (isset($mediopago[0]["numeroMovimiento"]) ? count($mediopago) : 0);
                    $registroact = 0;
                    $valorRecibido = 0;
                    for ($m = 0; $m < $totalmed; $m++) {
                        //                            echo "<br> entra for Mediopago <br>";
                        if (isset($encabezado[$i]["numeroMovimiento"]) and
                                isset($mediopago[$m]["numeroMovimiento"]) and
                                $encabezado[$i]["numeroMovimiento"] == $mediopago[$m]["numeroMovimiento"]) {

                            $movimiento->idMovimientoMedioPago[$registroact] = 0;
                            $movimiento->MedioPago_idMedioPago[$registroact] = (isset($mediopago[$m]["MedioPago_idMedioPago"]) ? $mediopago[$m]["MedioPago_idMedioPago"] : 0);
                            $movimiento->puntosMovimientoMedioPago[$registroact] = (isset($mediopago[$m]["puntosMovimientoMedioPago"]) ? $mediopago[$m]["puntosMovimientoMedioPago"] : 0);
                            $movimiento->valorMovimientoMedioPago[$registroact] = (isset($mediopago[$m]["valorMovimientoMedioPago"]) ? $mediopago[$m]["valorMovimientoMedioPago"] : 0);
                            $movimiento->Movimiento_idDocumento[$registroact] = (isset($mediopago[$m]["Movimiento_idDocumento"]) ? $mediopago[$m]["Movimiento_idDocumento"] : 0);
                            $movimiento->numeroComprobanteMovimientoMedioPago[$registroact] = (isset($mediopago[$m]["numeroComprobanteMovimientoMedioPago"]) ? $mediopago[$m]["numeroComprobanteMovimientoMedioPago"] : 0);
                            $movimiento->Tercero_idBanco[$registroact] = (isset($mediopago[$m]["Tercero_idBanco"]) ? $mediopago[$m]["Tercero_idBanco"] : 0);

                            $valorRecibido += $movimiento->valorMovimientoMedioPago[$registroact];
                            $registroact++;
                        }
                    }
                    $movimiento->valorRecibidoMovimiento = $valorRecibido;
                    //                       echo 'entre hasta aca pase fors';
                    //                       exit();
                    // cada que llenamos un documento, lo cargamos a la base de datos
                    // pero antes de adicionarlo, consultamos que exista del mismo tipo de documento y con el mismo numero para obtener el id
                    // la variable Origen, es para identificar si viene de Excel, EDI, o es de una liquidacion de importacion
                    switch ($origen) {
                        case 'interface':
                            $movimiento->ConsultarMovimiento("Documento_idDocumento = " . $movimiento->Documento_idDocumento . " and numeroMovimiento = '" . $movimiento->numeroMovimiento . "'");
                            break;
                        case 'importacion':
                            $movimiento->ConsultarMovimiento("Documento_idDocumento = " . $movimiento->Documento_idDocumento . " and Importacion_idImportacion  = " . $movimiento->Importacion_idImportacion . " and numeroMovimiento = '" . $movimiento->numeroMovimiento . "'");
                            break;
                        case 'produccion':
                            $movimiento->ConsultarMovimiento("Documento_idDocumento = " . $movimiento->Documento_idDocumento . " and numeroReferenciaInternoMovimiento = '" . $movimiento->numeroReferenciaInternoMovimiento . "'");
                            break;
                        case 'conectividad':
                            //						$movimiento->ConsultarMovimiento("Documento_idDocumento = " . $movimiento->Documento_idDocumento . " and numeroReferenciaInternoMovimiento = '" . $movimiento->numeroReferenciaInternoMovimiento . "' and DocumentoConcepto_idDocumentoConcepto = ".$movimiento->DocumentoConcepto_idDocumentoConcepto);
                            $movimiento->ConsultarMovimiento("Documento_idDocumento = " . $movimiento->Documento_idDocumento . " and numeroReferenciaExternoMovimiento = '" . $movimiento->numeroReferenciaExternoMovimiento . "'");
                            break;
                    }

                    //                       echo 'ENTRAAAAAAAAAAAAAAAAAAAAAAA hasta fin ';
                    //                        print_r($encabezado);
                    //                        print_r($detalle);
                    //
                    if ($movimiento->idMovimiento == 0) {
                        //                           echo 'entra1';
                        $movimiento->AdicionarMovimiento();
                    } else {
                        //                            echo 'entra2';
                        $movimiento->ModificarMovimiento();
                    }


                }
            }

            //
            //                var_dump($retorno);
            //                var_dump($nuevoserrores);
            //                            echo " entra else error ";
            $returnuevoserrores = isset($nuevoserrores[0]["error"]) ? $nuevoserrores : array();
            $retorno = array_merge((array) $retorno, (array) $returnuevoserrores);



            return $retorno;
        }

        function validarMovimiento($encabezado, $detalle, $listaprecio, $listapreciotercero, $tipo = '') {


            $ruta = dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR;

            require_once $ruta.'tercero.class.php';
            $tercero = new Tercero();

            require_once $ruta.'listaprecio.class.php';
            $lista = new ListaPrecio();

            require_once $ruta.'producto.class.php';
            $producto = new Producto();

            $swerror = true;
            $errores = array();
            $linea = 0;
            $totalreg = (isset($encabezado[0]["numeroMovimiento"]) ? count($encabezado) : 0);

            //print_r($detalle);
            for ($x = 0; $x < $totalreg; $x++) {
                //echo " entra for validar ";
                // validamos si el tercero no es cero
                if ($encabezado[$x]["Documento_idDocumento"] == 0) {
                    $errores[$linea]["numeroMovimiento"] = $encabezado[$x]["numeroMovimiento"];
                    $errores[$linea]["error"] = 'El Documento (' . (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '' ) . ') no existe';
                    $errores[$linea]["segmento"] = 'BGM';
                    $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                    $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                    $swerror = false;
                    $linea++;
                }
                //                        else if($encabezado[$x]["Documento_idDocumento"] != 0)
                //                        {
                //
                //                        }
                //                        if($encabezado[$x]["Tercero_idTercero"] == 0 || $encabezado[$x]["Tercero_idTercero"] == '')
                //                        {
                //
                //
                //                            echo 'NO EXISTE';
                //                            echo '<br>';
                //                            echo '<br>';
                //                        }
                //


                if (!isset($encabezado[$x]["Tercero_idTercero"]) or $encabezado[$x]["Tercero_idTercero"] == 0 or $encabezado[$x]["Tercero_idTercero"] == '') {
                    $errores[$linea]["numeroMovimiento"] = $encabezado[$x]["numeroMovimiento"];
                    $errores[$linea]["error"] = 'El EAN del Cliente (' . (isset($encabezado[$x]["eanTercero"]) ? $encabezado[$x]["eanTercero"] : '' ) . ') no existe';
                    $errores[$linea]["segmento"] = 'NAD+BY';
                    $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                    $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                    $swerror = false;
                    $linea++;
                }
                // validamos si el sitio de entrega esta lleno pero no existe
                if ((isset($encabezado[$x]["eanEntrega"]) and $encabezado[$x]["eanEntrega"] != '') and ( $encabezado[$x]["Tercero_idEntrega"] == 0 or $encabezado[$x]["Tercero_idEntrega"] == '')) {
                    $errores[$linea]["numeroMovimiento"] = $encabezado[$x]["numeroMovimiento"];
                    $errores[$linea]["error"] = 'El EAN del Sitio de Entrega (' . $encabezado[$x]["eanEntrega"] . ') no existe';
                    $errores[$linea]["segmento"] = 'NAD+DP';
                    $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                    $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                    $swerror = false;
                    $linea++;
                }
                // Verificamos que el periodo exista
                if (isset($encabezado[$x]["Periodo_idPeriodo"]) and ( $encabezado[$x]["Periodo_idPeriodo"] == 0 or $encabezado[$x]["Periodo_idPeriodo"] == '')) {
                    $errores[$linea]["numeroMovimiento"] = $encabezado[$x]["numeroMovimiento"];
                    $errores[$linea]["error"] = 'La Fecha de elaboracion (' . $encabezado[$x]["fechaElaboracionMovimiento"] .
                            ') no pertenece a un periodo ACTIVO o el periodo no se ha creado';
                    $errores[$linea]["segmento"] = 'DTM+137';
                    $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                    $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                    $swerror = false;
                    $linea++;
                }


                $totaldet = (isset($detalle[0]["numeroMovimiento"]) ? count($detalle) : 0);
                for ($y = 0; $y < $totaldet; $y++) {
                    if (isset($encabezado[$x]["numeroMovimiento"]) and isset($detalle[$y]["numeroMovimiento"]) and $encabezado[$x]["numeroMovimiento"] == $detalle[$y]["numeroMovimiento"]) {
                        // Verificamos que el Producto exista
                        if (isset($detalle[$y]["Producto_idProducto"]) and ( $detalle[$y]["Producto_idProducto"] == 0 or $detalle[$y]["Producto_idProducto"] == '')) {
                            $errores[$linea]["numeroMovimiento"] = $detalle[$y]["numeroMovimiento"];

                            $errores[$linea]["error"] = 'El EAN del Producto (' . $detalle[$y]["eanProducto"] . ') no existe';
                            $errores[$linea]["segmento"] = 'LIN';
                            $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                            $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                            $swerror = false;
                            $linea++;
                        }
                        // Verificamos que si tiene localizaciones de predistribucion, existan
                        if (isset($encabezado[$x]["tipoMovimiento"]) and $encabezado[$x]["tipoMovimiento"] == 'PREDISTRIBUIDA'
                                and isset($detalle[$y]["Tercero_idAlmacen"]) and ( $detalle[$y]["Tercero_idAlmacen"] == 0 or $detalle[$y]["Tercero_idAlmacen"] == '')) {
                            $errores[$linea]["numeroMovimiento"] = $detalle[$y]["numeroMovimiento"];
                            $errores[$linea]["error"] = 'El Codigo/EAN del Tercero/Almacen del Detalle (' . $detalle[$y]["eanAlmacen"] .
                                    ') no existe';
                            $errores[$linea]["segmento"] = 'LOC+7';
                            $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                            $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                            $swerror = false;
                            $linea++;
                        }
                        // verificamos que la cantidad no sea cero
                        if (isset($detalle[$y]["cantidadMovimientoDetalle"]) and ( $detalle[$y]["cantidadMovimientoDetalle"] == 0 or $detalle[$y]["cantidadMovimientoDetalle"] == '')) {
                            $errores[$linea]["numeroMovimiento"] = $detalle[$y]["numeroMovimiento"];
                            $errores[$linea]["error"] = 'La cantidad del Producto con EAN (' . $detalle[$y]["eanProducto"] . ') es cero';
                            $errores[$linea]["segmento"] = 'QTY';
                            $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                            $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                            $swerror = false;
                            $linea++;
                        }


                        /*

                          //echo 'precios '.$detalle[$y]["valorBrutoMovimientoDetalle"].' '.$precio;
                          $tercero->ConsultarVistaTercero("idTercero = ".$encabezado[$x]["Tercero_idTercero"], "","idTercero, ListaPrecio_idListaPrecio");
                          $datosProducto = $producto->ConsultarVistaProducto("idProducto = ".$detalle[$y]["Producto_idProducto"], "", "precioProducto","idProducto");

                          if(!empty($tercero->ListaPrecio_idListaPrecio))
                          {
                          $lista->ConsultarPrecio("ListaPrecio_idListaPrecio = ".$tercero->ListaPrecio_idListaPrecio." and idProducto = ".$detalle[$y]["Producto_idProducto"]);

                          if($lista->precioListaPrecioDetalle > 0)
                          {
                          $precio = $lista->precioListaPrecioDetalle;
                          }
                          else
                          {
                          $precio = $datosProducto[0]['precioProducto'];
                          }
                          }
                          else
                          {

                          $precio = $datosProducto[0]['precioProducto'];
                          }

                          if ((!isset($encabezado[$x]["LiquidacionNomina_idLiquidacionNomina"]) or (isset($encabezado[$x]["LiquidacionNomina_idLiquidacionNomina"]) and $encabezado[$x]["LiquidacionNomina_idLiquidacionNomina"] == 0) )  and
                          isset($detalle[$y]["valorBrutoMovimientoDetalle"]) and ($detalle[$y]["valorBrutoMovimientoDetalle"] == 0 or $detalle[$y]["valorBrutoMovimientoDetalle"] == '' or $detalle[$y]["valorBrutoMovimientoDetalle"] != $precio))
                          {
                          $errores[$linea]["numeroMovimiento"] = $detalle[$y]["numeroMovimiento"];
                          $errores[$linea]["error"] = 'REF: '.$detalle[$y]["eanProducto"].', El precio del documento (' . $detalle[$y]["valorBrutoMovimientoDetalle"] . ') no es igual al del producto ('.$precio.')';
                          $errores[$linea]["segmento"] = 'PRI';
                          $swerror = false;
                          $linea++;
                          } */
                        $hoy = date("Y-m-d");
                        if ($listaprecio != '') {
                            $lista->ConsultarIdListaPrecio("codigoAlternoListaPrecio = '" . $listaprecio[$y]["codigoAlternoListaPrecio"] . "' and fechaInicialListaPrecio <= '" . $hoy . "' and fechaFinalListaPrecio >= '" . $hoy . "'");
                            $idListaPrecio = $lista->idListaPrecio;
                        } else {
                            $idListaPrecio = 0;
                        }
                        require_once($ruta.'documentocomercial.class.php');
                        if (!isset($documentocomercial))
                        {
                            $documentocomercial = new Documento();
                        }

                        $datosdocumento = $documentocomercial->ConsultarVistaDocumento("idDocumento = " . $encabezado[$x]["Documento_idDocumento"]);
                        // -----------------------------
                        // Si el documento comercial esta configurado para que no valide precios,
                        // nos devolvemos al inicio del ciclo
                        // -----------------------------
                        if ($datosdocumento[0]["existeDiferenciaPrecioDocumento"] == "NoValidar" and isset($datosdocumento[0]["existeDiferenciaPrecioDocumento"])) {
                            continue;
                        }


                        require_once($ruta.'producto.class.php');
                        $producto = new Producto();
                        $precioproducto = $producto->ConsultarVistaProducto('idProducto = ' . $detalle[$y]["Producto_idProducto"], '', 'precioProducto');
                        //print_r($listapreciotercero);
                        /* $datosLista = $lista->ConsultarVistaListaPrecioTerceroDetalle("Producto_idProducto = " . $detalle[$y]["Producto_idProducto"] . " and idTercero = " . $encabezado[$x]["Tercero_idTercero"] . " and fechaInicialListaPrecio <= '" . $hoy . "' and fechaFinalListaPrecio >= '" . $hoy . "'", "", "idListaPrecio, precioListaPrecioDetalle, Producto_idProducto"); */
                        if (!empty($listaprecio) && !empty($listapreciotercero)) {
                            $datosLista = $lista->BuscarValoresListaPrecio($listaprecio[$y]["Producto_idProducto"], $listapreciotercero[$x]["Tercero_idTercero"], $hoy, '', $idListaPrecio);
                        } else {
                            if ($encabezado[$x]['ListaPrecio_idListaPrecio'] != 0) {
                                if ($datosdocumento[0]['validarTerceroListaPrecioDocumento'] == 0) {
                                    $encabezado[$x]["Tercero_idTercero"] = 0;
                                }
                                $datosLista = $lista->BuscarValoresListaPrecio($detalle[$y]["Producto_idProducto"], $encabezado[$x]["Tercero_idTercero"], $hoy, '', $encabezado[$x]['ListaPrecio_idListaPrecio']);
                            } else {
                                $datoslista = 0;
                            }
                        }
                        /* print_r($datosLista);
                          echo "<br>"; */
                        if ($tipo == 'interface') {
                            if ($datosdocumento[0]["existeDiferenciaPrecioDocumento"] == "ReemplazarPrecio" && ($listaprecio != '' || $encabezado[$x]['ListaPrecio_idListaPrecio'] != 0)) {
                                if (isset($detalle[$y]["Producto_idProducto"])) {
                                    $errores[$linea]["numeroMovimiento"] = "";
                                    $errores[$linea]["error"] = '';
                                    $errores[$linea]["ListaPrecio_idListaPrecioDetalle"] = $datosLista[0]['idListaPrecio'];
                                    $errores[$linea]["precioListaMovimientoDetalle"] = $datosLista[0]['precioListaPrecioDetalle'];
                                    $errores[$linea]["valorBrutoMovimientoDetalle"] = $datosLista[0]['precioListaPrecioDetalle'];
                                    $linea++;
                                } else {
                                    $errores[$linea]["precioListaPrecioDetalle"] = $detalle[$y]["precioListaPrecioDetalle"];
                                    $errores[$linea]["error"] = 'REF: ' . $detalle[$y]["eanProducto"] . ', No existe en la Lista de Precio';
                                    $errores[$linea]["segmento"] = 'PRI';
                                    $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                                    $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                                    $swerror = false;
                                    $linea++;
                                }
                            } else {
                                if ($datosdocumento[0]["existeDiferenciaPrecioDocumento"] == "GenerarError") {
                                    if ($detalle[$y]["valorBrutoMovimientoDetalle"] <> $datosLista[0]['precioListaPrecioDetalle']) {
                                        $errores[$linea]["numeroMovimiento"] = $detalle[$y]["numeroMovimiento"];
                                        $errores[$linea]["error"] = 'REF: ' . $detalle[$y]["eanProducto"] . ', El precio del documento (' . $detalle[$y]["valorBrutoMovimientoDetalle"] . ') no es igual al del producto (' . $datosLista[0]['precioListaPrecioDetalle'] . ')';
                                        $errores[$linea]["segmento"] = 'PRI';
                                        $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                                        $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                                        $swerror = false;
                                        $linea++;
                                    }
                                } else {
                                    if ($detalle[$y]["valorBrutoMovimientoDetalle"] == '' || $detalle[$y]["valorBrutoMovimientoDetalle"] == 0) {
                                        $errores[$linea]["numeroMovimiento"] = "";
                                        $errores[$linea]["error"] = '';
                                        $errores[$linea]["ListaPrecio_idListaPrecioDetalle"] = 0;
                                        $errores[$linea]["precioListaMovimientoDetalle"] = $precioproducto[0]['precioProducto'];
                                        $errores[$linea]["valorBrutoMovimientoDetalle"] = $precioproducto[0]['precioProducto'];
                                        $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                                        $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                                        $linea++;
                                    }
                                }

                                $valserie = true;

                                for ($w = 0; $w < $totaldet; $w++) {
                                    if ($w != $y) {
                                        if (isset($detalle[$w]['numeroSerie']) && isset($detalle[$y]['numeroSerie'])) {
                                            if (($detalle[$w]['numeroSerie'] == $detalle[$y]['numeroSerie']) && ($detalle[$w]['numeroSerie'] != '')) {
                                                $errores[$linea]["numeroMovimiento"] = $detalle[$y]["numeroMovimiento"];
                                                $errores[$linea]["error"] = 'REF: ' . $detalle[$y]["eanProducto"] . ' El numero de producto serie se repite en la lineas ' . ($w + 1) . ' y ' . ($y + 1);
                                                $errores[$linea]["segmento"] = 'PRI';
                                                $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                                                $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                                                $swerror = false;
                                                $linea++;
                                                $valserie = false;
                                            }
                                        }
                                    }
                                }

                                if ($valserie == true) {
                                    if (isset($datosdocumento[0]["ModeloContable_idModeloContable"]) && ($datosdocumento[0]['ModeloContable_idModeloContable'] == 6 || $datosdocumento[0]['ModeloContable_idModeloContable'] == 2 || $datosdocumento[0]['ModeloContable_idModeloContable'] == 5 || $datosdocumento[0]['ModeloContable_idModeloContable'] == 7)) {

                                    } else if (isset($datosdocumento[0]["ModeloContable_idModeloContable"]) && ($datosdocumento[0]['ModeloContable_idModeloContable'] == 1 || $datosdocumento[0]['ModeloContable_idModeloContable'] == 12 )) {

                                    }
                                }
                            }
                        } else {
                            if ($datosdocumento[0]["existeDiferenciaPrecioDocumento"] == "ReemplazarPrecio") {
                                if (isset($detalle[$y]["Producto_idProducto"])) {
                                    $errores[$linea]["numeroMovimiento"] = "";
                                    $errores[$linea]["error"] = '';
                                    $errores[$linea]["ListaPrecio_idListaPrecioDetalle"] = 0;
                                    $errores[$linea]["precioListaMovimientoDetalle"] = 0;
                                    $errores[$linea]["valorBrutoMovimientoDetalle"] = $detalle[$y]["valorBrutoMovimientoDetalle"];
                                    $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                                    $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                                    $linea++;
                                }
                            } else {
                                if ($datosdocumento[0]["existeDiferenciaPrecioDocumento"] == "GenerarError") {
                                    if ($detalle[$y]["valorBrutoMovimientoDetalle"] <> $detalle[$y]["valorBrutoMovimientoDetalle"]) {
                                        $errores[$linea]["numeroMovimiento"] = $detalle[$y]["numeroMovimiento"];
                                        $errores[$linea]["error"] = 'REF: ' . $detalle[$y]["eanProducto"] . ', El precio del documento (' . $detalle[$y]["valorBrutoMovimientoDetalle"] . ') no es igual al del producto (' . $precioproducto[0]['precioProducto'] . ')';
                                        $errores[$linea]["segmento"] = 'PRI';
                                        $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                                        $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                                        $swerror = false;
                                        $linea++;
                                    }
                                } else {
                                    if ($detalle[$y]["valorBrutoMovimientoDetalle"] == '' || $detalle[$y]["valorBrutoMovimientoDetalle"] == 0) {
                                        $errores[$linea]["numeroMovimiento"] = "";
                                        $errores[$linea]["error"] = '';
                                        $errores[$linea]["ListaPrecio_idListaPrecioDetalle"] = 0;
                                        $errores[$linea]["precioListaMovimientoDetalle"] = $precioproducto[0]['precioProducto'];
                                        $errores[$linea]["valorBrutoMovimientoDetalle"] = $precioproducto[0]['precioProducto'];
                                        $errores[$linea]["documento"] = (isset($encabezado[$x]["codigoDocumento"]) ? $encabezado[$x]["codigoDocumento"] : '');
                                        $errores[$linea]["concepto"] = (isset($encabezado[$x]["codigoConceptoDocumento"]) ? $encabezado[$x]["codigoConceptoDocumento"] : '');
                                        $linea++;
                                    }
                                }
                            }
                        }

                        /* se le debe agregar al formato de excel en el encabezado de lista de precio una opcion para reemplazar el precio del producto, y en el detalle agregar una columna de tercero el cual se le insertara a todos los productos
                         */


                        // esta validacion es para mirar que el numero de serial de un producto no se repita en el excel
                        // vamos a validar si el documento pide numero de serie y dependiendo del modelo contable
                        // miramos si hay que adicionar o mirar si el serial existe.
                        //                            var_dump($errores);
                    }
                }
            }
            //print_r($errores);
            return $errores;
        }

        function ImportarUtrade($ruta) {


            set_time_limit(0);


            require_once('../clases/movimiento.class.php');
            $movimiento = new Movimiento();

            require_once '../clases/movimientocalidad.class.php';
            $calidad = new MovimientoCalidad();

            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $referencias = array();
            $posRef = -1;

            $retorno = array();
            $fila = 2;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {
                if ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(12, $fila)->getValue() != '' and $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(12, $fila)->getValue() != NULL and
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $fila)->getValue() != '' and $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $fila)->getValue() != NULL) {



                    $refAnterior = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $fila)->getValue();
                    // por cada numero de documento diferente, llenamos el referencias

                    $posRef++;

                    $referencias[$posRef]["estadoReferenciaMovimientoDetalle"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue();

                    //echo 'estados'.$referencias[$posRef]["estadoReferenciaMovimientoDetalle"].'<br>';
                    $referencias[$posRef]["referenciaProducto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $fila)->getValue();

                    $referencias[$posRef]["numeroMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(12, $fila)->getValue();


                    $fechaReal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(25, $fila)->getValue();
                    if ($fechaReal != '') {
                        $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaReal);
                        //$timestamp = strtotime("+1 day",$timestamp);
                        $referencias[$posRef]["fechaRealInspeccionMovimientoCalidad"] = date("Y-m-d", $timestamp);
                    } else {
                        $referencias[$posRef]["fechaRealInspeccionMovimientoCalidad"] = '';
                    }

                    $referencias[$posRef]["resultadoDefinitivoMovimientoCalidad"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(26, $fila)->getValue();



                    $fechaBooking1 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(40, $fila)->getValue();
                    if ($fechaBooking1 != '') {
                        $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaBooking1);
                        //$timestamp = strtotime("+1 day",$timestamp);
                        $referencias[$posRef]["fechaReservaEmbarque1MovimientoDetalle"] = date("Y-m-d", $timestamp);
                    } else {
                        $referencias[$posRef]["fechaReservaEmbarque1MovimientoDetalle"] = '';
                    }

                    $fechaBooking2 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(41, $fila)->getValue();
                    if ($fechaBooking2 != '') {
                        $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaBooking2);
                        //$timestamp = strtotime("+1 day",$timestamp);
                        $referencias[$posRef]["fechaReservaEmbarque2MovimientoDetalle"] = date("Y-m-d", $timestamp);
                    } else {
                        $referencias[$posRef]["fechaReservaEmbarque2MovimientoDetalle"] = '';
                    }

                    // consultamos el idMovimiento y Producto_idProducto con el numeroMovimiento y referenciaProducto
                    $datos = array();
                    if (!empty($referencias[$posRef]["numeroMovimiento"]) and ! empty($referencias[$posRef]["referenciaProducto"])) {
                        $datos = $movimiento->ConsultarVistaMovimientoComercialConectividad("numeroMovimiento = '" . $referencias[$posRef]["numeroMovimiento"] . "' and
                                                                                                                    codigoAlternoProducto = '" . $referencias[$posRef]["referenciaProducto"] . "'", "", "", "idMovimiento, numeroMovimiento, Producto_idProducto, referenciaProducto, codigoAlternoProducto, idMovimientoDetalle", "");
                    }


                    if (isset($datos[0]['idMovimiento'])) {
                        // para el registro cero ($i = 0), llenamos los campos de ids, y para los demas registros, duplicamos el registro y llenamos los campos de ids
                        /* $referencias[$posRef]["idMovimiento"] = $datos[0]['idMovimiento'];
                          $referencias[$posRef]["Producto_idProducto"] = $datos[0]['Producto_idProducto'];
                          $referencias[$posRef]["idMovimientoDetalle"] = $datos[0]['idMovimientoDetalle'];
                          $campos = $calidad->ConsultarVistaMovimientoCalidad('Movimiento_idMovimiento = ' . $referencias[$posRef]["idMovimiento"] . ' and Producto_idProducto = ' . $referencias[$posRef]["Producto_idProducto"]);
                          $referencias[$posRef]['idMovimientoCalidad'] = (isset($campos[0]['idMovimientoCalidad']) ? $campos[0]['idMovimientoCalidad'] : 0);
                         */
                        $regtotal = count($datos);
                        for ($i = 0; $i < $regtotal; $i++) {
                            $posRef++;
                            $referencias[$posRef] = $referencias[$posRef - 1];
                            $referencias[$posRef]["idMovimiento"] = $datos[$i]['idMovimiento'];
                            $referencias[$posRef]["Producto_idProducto"] = $datos[$i]['Producto_idProducto'];
                            $referencias[$posRef]["idMovimientoDetalle"] = $datos[$i]['idMovimientoDetalle'];
                            $campos = $calidad->ConsultarVistaMovimientoCalidad('Movimiento_idMovimiento = ' . $referencias[$posRef]["idMovimiento"] . ' and Producto_idProducto = ' . $referencias[$posRef]["Producto_idProducto"]);
                            $referencias[$posRef]['idMovimientoCalidad'] = (isset($campos[0]['idMovimientoCalidad']) ? $campos[0]['idMovimientoCalidad'] : 0);
                        }
                    } else {
                        $referencias[$posRef]["idMovimiento"] = 0;
                        $referencias[$posRef]["Producto_idProducto"] = 0;
                        $referencias[$posRef]["idMovimientoDetalle"] = 0;
                        $referencias[$posRef]['idMovimientoCalidad'] = 0;
                    }

                    while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
                    $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL
                    and $refAnterior == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(4, $fila)->getValue()) {
                        $fila++;
                    }
                } else {
                    $fila++;
                }
            }

            //                echo 'recorrio datos';
            //                return;
            // luego de que tenemos la matriz de referencias llena, las enviamos al proceso de importacion de productos
            // para que los valide e importe al sistema
            $retorno = $this->llenarUtrade($referencias);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($referencias);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));

            return $retorno;
        }

        function llenarUtrade($referencias) {
            // instanciamos la clase producto y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'movimientocalidad.class.php';
            $calidad = new MovimientoCalidad();

            require_once 'movimiento.class.php';
            $movimiento = new Movimiento();

            $retorno = array();
            $movimiento->Movimiento();


            $regtotal = count($referencias);
            for ($i = 0; $i < $regtotal; $i++) {


                $nuevoserrores = array();
                $nuevoserrores = $this->validarUtrade($referencias[$i]);

                $totalerr = count($nuevoserrores);


                if ($referencias[$i]["idMovimiento"] != 0 and $referencias[$i]["Producto_idProducto"] != 0) {

                    // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
                    $calidad->MovimientoCalidad();

                    $calidad->idMovimientoCalidad = (isset($referencias[$i]["idMovimientoCalidad"]) ? $referencias[$i]["idMovimientoCalidad"] : 0);
                    $calidad->Movimiento_idMovimiento = (isset($referencias[$i]["idMovimiento"]) ? $referencias[$i]["idMovimiento"] : 0);
                    $calidad->Producto_idProducto = (isset($referencias[$i]["Producto_idProducto"]) ? $referencias[$i]["Producto_idProducto"] : 0);
                    $calidad->fechaRealInspeccionMovimientoCalidad = (isset($referencias[$i]['fechaRealInspeccionMovimientoCalidad']) ? $referencias[$i]['fechaRealInspeccionMovimientoCalidad'] : '');


                    $calidad->resultadoDefinitivoMovimientoCalidad = (!isset($referencias[$i]['resultadoDefinitivoMovimientoCalidad']) ? '' : (strtolower($referencias[$i]['resultadoDefinitivoMovimientoCalidad']) == 'passed' ? 'aprobada' : ''));



                    $movimiento->Movimiento_idMovimiento[0] = (isset($referencias[$i]["idMovimiento"]) ? $referencias[$i]["idMovimiento"] : 0);
                    $movimiento->idMovimientoDetalle[0] = (isset($referencias[$i]["idMovimientoDetalle"]) ? $referencias[$i]["idMovimientoDetalle"] : 0);

                    $movimiento->fechaReservaEmbarque1MovimientoDetalle[0] = (isset($referencias[$i]['fechaReservaEmbarque1MovimientoDetalle']) ? $referencias[$i]['fechaReservaEmbarque1MovimientoDetalle'] : '');
                    $movimiento->fechaReservaEmbarque2MovimientoDetalle[0] = (isset($referencias[$i]['fechaReservaEmbarque2MovimientoDetalle']) ? $referencias[$i]['fechaReservaEmbarque2MovimientoDetalle'] : '');
                    $movimiento->estadoReferenciaMovimientoDetalle[0] = (isset($referencias[$i]['estadoReferenciaMovimientoDetalle']) ? $referencias[$i]['estadoReferenciaMovimientoDetalle'] : '');

                    // cada que llenamos un producto, lo cargamos a la base de datos
                    // si el id esta lleno, lo actualizamos, si esta vacio lo insertamos
                    if ($referencias[$i]["idMovimientoCalidad"] == 0) {
                        $calidad->AdicionarMovimientoCalidadUtrade();
                    } else {

                        $calidad->ModificarMovimientoCalidadUtrade();
                    }

                    if (isset($referencias[$i]["idMovimientoDetalle"]) and $referencias[$i]["idMovimientoDetalle"] != 0)
                        $movimiento->ModificarMovimientoDetalleUtrade();
                }
                else {
                    $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                }
            }

            return $retorno;
        }

        function validarUtrade($referencias) {
            require_once 'movimientocalidad.class.php';
            $calidad = new MovimientoCalidad();

            require_once 'movimiento.class.php';
            $movimiento = new Movimiento();


            $swerror = true;
            $errores = array();
            $linea = 0;
            //print_r($referencias);
            //echo $idMovimiento;
            //echo $idProducto;
            //echo $idMovimientoDetalle;
            //echo $idMovimientoCalidad;
            //and ($referencias["idMovimiento"] == 0 and $referencias["numeroMovimiento"] != '')
            if ((isset($referencias["idMovimientoDetalle"]) and $referencias["idMovimientoDetalle"] == 0)) {
                $errores[$linea]['tipo'] = 'Error';
                $errores[$linea]["referenciaProducto"] = $referencias["referenciaProducto"];
                $errores[$linea]["error"] = 'El numero de la PI (' . $referencias["numeroMovimiento"] . ') con la  REF (' . $referencias["referenciaProducto"] . ') no existen';
                $swerror = false;

                $linea++;
            } else {
                // si existe el id de calidad, verificamos que cambios se realizaran en los datos para mostrarlos como una alerta
                if (isset($referencias["idMovimientoCalidad"]) and $referencias["idMovimientoCalidad"] != 0) {
                    $calidadUtrade = $calidad->ConsultarVistaMovimientoCalidad("idMovimientoCalidad = " . $referencias["idMovimientoCalidad"]);

                    // cambiamos el estado de calidad a español
                    $referencias['resultadoDefinitivoMovimientoCalidad'] = (strtolower($referencias['resultadoDefinitivoMovimientoCalidad']) == 'passed' ? 'aprobada' : '');

                    if (isset($calidadUtrade[0]['idMovimientoCalidad'])) {
                        $mensaje = '';
                        if ($calidadUtrade[0]['fechaRealInspeccionMovimientoCalidad'] != '0000-00-00' and $calidadUtrade[0]['fechaRealInspeccionMovimientoCalidad'] != $referencias['fechaRealInspeccionMovimientoCalidad'])
                            $mensaje .= 'La fecha Real (' . $calidadUtrade[0]['fechaRealInspeccionMovimientoCalidad'] . ') se cambi&oacute; por (' . $referencias['fechaRealInspeccionMovimientoCalidad'] . ').';


                        if ($calidadUtrade[0]['resultadoDefinitivoMovimientoCalidad'] != '' and $calidadUtrade[0]['resultadoDefinitivoMovimientoCalidad'] != $referencias['resultadoDefinitivoMovimientoCalidad'])
                            $mensaje .= 'El resultado Definitivo (' . $calidadUtrade[0]['resultadoDefinitivoMovimientoCalidad'] . ') se cambi&oacute; por (' . $referencias['resultadoDefinitivoMovimientoCalidad'] . ')';

                        if ($mensaje != '') {
                            $errores[$linea]['tipo'] = 'Alerta';
                            $errores[$linea]['referenciaProducto'] = $referencias["referenciaProducto"];
                            $errores[$linea]["error"] = $mensaje;
                            $swerror = false;

                            $linea++;
                        }
                    }
                }

                if (isset($referencias["idMovimientoDetalle"]) and $referencias["idMovimientoDetalle"] != 0) {
                    $movimientoUtrade = array();
                    $sql = "Select fechaReservaEmbarque1MovimientoDetalle, fechaReservaEmbarque2MovimientoDetalle, estadoReferenciaMovimientoDetalle
                                                            from MovimientoDetalle
                                                            where idMovimientoDetalle = " . $referencias["idMovimientoDetalle"];
                    $bd = Db::getInstance();
                    $movimientoUtrade = $bd->ConsultarVista($sql);
                    //print_r($movimientoUtrade);


                    if (isset($movimientoUtrade[0]['fechaReservaEmbarque1MovimientoDetalle'])) {
                        $mensaje = '';
                        if ($movimientoUtrade[0]['fechaReservaEmbarque1MovimientoDetalle'] != '0000-00-00' and $movimientoUtrade[0]['fechaReservaEmbarque1MovimientoDetalle'] != $referencias['fechaReservaEmbarque1MovimientoDetalle'])
                            $mensaje .= 'El Booking 1 (' . $movimientoUtrade[0]['fechaReservaEmbarque1MovimientoDetalle'] . ') se cambi&oacute; por (' . $referencias['fechaReservaEmbarque1MovimientoDetalle'] . ') ';

                        if ($movimientoUtrade[0]['fechaReservaEmbarque2MovimientoDetalle'] != '0000-00-00' and $movimientoUtrade[0]['fechaReservaEmbarque2MovimientoDetalle'] != $referencias['fechaReservaEmbarque2MovimientoDetalle'])
                            $mensaje .= 'El Booking 2 (' . $movimientoUtrade[0]['fechaReservaEmbarque2MovimientoDetalle'] . ') se cambi&oacute; por (' . $referencias['fechaReservaEmbarque2MovimientoDetalle'] . ') ';

                        if ($movimientoUtrade[0]['estadoReferenciaMovimientoDetalle'] != '' and $movimientoUtrade[0]['estadoReferenciaMovimientoDetalle'] != $referencias['estadoReferenciaMovimientoDetalle'])
                            $mensaje .= 'El Estado Referencia (' . $movimientoUtrade[0]['estadoReferenciaMovimientoDetalle'] . ') se cambi&oacute; por (' . $referencias['estadoReferenciaMovimientoDetalle'] . ')';


                        if ($mensaje != '') {
                            $errores[$linea]['tipo'] = 'Alerta';
                            $errores[$linea]['referenciaProducto'] = $referencias["referenciaProducto"];
                            $errores[$linea]["error"] = $mensaje;
                            $swerror = false;

                            $linea++;
                        }
                    }
                }
            }



            return $errores;
        }

        function ImportarMovimientoCalidad($ruta) {
            set_time_limit(0);
            //echo $ruta;

            require_once('../clases/movimiento.class.php');
            $movimiento = new Movimiento();

            require_once '../clases/movimientocalidad.class.php';
            $calidad = new MovimientoCalidad();

            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $referencias = array();
            $posRef = -1;


            $fila = 2;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $refAnterior = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();

                // por cada numero de documento diferente, llenamos el referencias
                $posRef++;

                $referencias[$posRef]["referenciaProducto"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();

                $referencias[$posRef]["numeroMovimiento"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue();
                $fechaMaxima = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(30, $fila)->getValue();
                if ($fechaMaxima != '') {
                    $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaMaxima);
                    $timestamp = strtotime("-7 day", $timestamp);
                    $referencias[$posRef]["fechaMaximaInspeccionMovimientoCalidad"] = date("Y-m-d", $timestamp);
                } else {
                    $referencias[$posRef]["fechaMaximaInspeccionMovimientoCalidad"] = '';
                }
                $fechaCita = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(33, $fila)->getValue();
                if ($fechaCita != '') {
                    $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaCita);
                    $timestamp = strtotime("+1 day", $timestamp);
                    $referencias[$posRef]["fechaCitaInspeccionMovimientoCalidad"] = date("Y-m-d", $timestamp);
                } else {
                    $referencias[$posRef]["fechaCitaInspeccionMovimientoCalidad"] = '';
                }
                $fechaReal = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(34, $fila)->getValue();
                if ($fechaReal != '') {
                    $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaReal);
                    $timestamp = strtotime("+1 day", $timestamp);
                    $referencias[$posRef]["fechaRealInspeccionMovimientoCalidad"] = date("Y-m-d", $timestamp);
                } else {
                    $referencias[$posRef]["fechaRealInspeccionMovimientoCalidad"] = '';
                }
                $fechaReporte = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(35, $fila)->getValue();
                if ($fechaReporte != '') {
                    $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaReporte);
                    $timestamp = strtotime("+1 day", $timestamp);
                    $referencias[$posRef]["fechaReporteInspeccionMovimientoCalidad"] = date("Y-m-d", $timestamp);
                } else {
                    $referencias[$posRef]["fechaReporteInspeccionMovimientoCalidad"] = '';
                }
                $referencias[$posRef]["resultadoInspeccionMovimientoCalidad"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(38, $fila)->getValue();

                $fechaAprobacion = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(39, $fila)->getValue();

                if ($fechaAprobacion != '') {
                    $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaAprobacion);
                    $timestamp = strtotime("+1 day", $timestamp);
                    $referencias[$posRef]["fechaAprobacionInspeccionMovimientoCalidad"] = date("Y-m-d", $timestamp);
                } else {
                    $referencias[$posRef]["fechaAprobacionInspeccionMovimientoCalidad"] = '';
                }

                $referencias[$posRef]["observacionInspeccionMovimientoCalidad"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(40, $fila)->getValue();
                $referencias[$posRef]["observacionDetalleInspeccionMovimientoCalidad"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(41, $fila)->getValue();

                $fechaMax = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(43, $fila)->getValue();

                if ($fechaMax != '') {
                    $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaMax);
                    $timestamp = strtotime("+1 day", $timestamp);
                    $referencias[$posRef]["fechaMaximaReinspeccionMovimientoCalidad"] = date("Y-m-d", $timestamp);
                } else {
                    $referencias[$posRef]["fechaMaximaReinspeccionMovimientoCalidad"] = '';
                }



                $fechaReinspeccion = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(44, $fila)->getValue();

                if ($fechaReinspeccion != '') {
                    $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fechaReinspeccion);
                    $timestamp = strtotime("+1 day", $timestamp);
                    $referencias[$posRef]["fechaReinspeccionMovimientoCalidad"] = date("Y-m-d", $timestamp);
                } else {
                    $referencias[$posRef]["fechaReinspeccionMovimientoCalidad"] = '';
                }



                $referencias[$posRef]["resultadoReinspeccionMovimientoCalidad"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(45, $fila)->getValue();

                $referencias[$posRef]["observacionReinspeccionMovimientoCalidad"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(46, $fila)->getValue();
                $referencias[$posRef]["resultadoDefinitivoMovimientoCalidad"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(48, $fila)->getValue();

                // consultamos el idMovimiento y Producto_idProducto con el numeroMovimiento y referenciaProducto
                /*
                  if (!empty($referencias[$posRef]["numeroMovimiento"]) and !empty($referencias[$posRef]["referenciaProducto"]))
                  {
                  $datos = $movimiento->ConsultarVistaMovimiento("numeroMovimiento = '" . $referencias[$posRef]["numeroMovimiento"] . "' and
                  codigoAlternoProducto = '" . $referencias[$posRef]["referenciaProducto"] . "'", "", "idMovimiento, numeroMovimiento, Producto_idProducto, referenciaProducto, codigoAlternoProducto ");

                  //print_r($datos);
                  }

                  if (isset($datos[0]['idMovimiento']))
                  {
                  $referencias[$posRef]["idMovimiento"] = $datos[0]['idMovimiento'];
                  $referencias[$posRef]["Producto_idProducto"] = $datos[0]['Producto_idProducto'];
                  }
                  else
                  {
                  $referencias[$posRef]["idMovimiento"] = 0;
                  $referencias[$posRef]["Producto_idProducto"] = 0;
                  }


                  $campos = $calidad->ConsultarVistaMovimientoCalidad('Movimiento_idMovimiento = ' . $referencias[$posRef]["idMovimiento"] . ' and Producto_idProducto = ' . $referencias[$posRef]["Producto_idProducto"]);
                  $referencias[$posRef]['idMovimientoCalidad'] = (isset($campos[0]['idMovimientoCalidad']) ? $campos[0]['idMovimientoCalidad'] : 0);
                  $fila++; */

                // consultamos el idMovimiento y Producto_idProducto con el numeroMovimiento y referenciaProducto
                $datos = array();
                if (!empty($referencias[$posRef]["numeroMovimiento"]) and ! empty($referencias[$posRef]["referenciaProducto"])) {
                    $datos = $movimiento->ConsultarVistaMovimientoComercialConectividad("numeroMovimiento = '" . $referencias[$posRef]["numeroMovimiento"] . "' and
                                                                                                                                codigoAlternoProducto = '" . $referencias[$posRef]["referenciaProducto"] . "'", "", "", "idMovimiento, numeroMovimiento, Producto_idProducto, referenciaProducto, codigoAlternoProducto, idMovimientoDetalle", "");
                }


                if (isset($datos[0]['idMovimiento'])) {
                    // para el registro cero ($i = 0), llenamos los campos de ids, y para los demas registros, duplicamos el registro y llenamos los campos de ids
                    /* $referencias[$posRef]["idMovimiento"] = $datos[0]['idMovimiento'];
                      $referencias[$posRef]["Producto_idProducto"] = $datos[0]['Producto_idProducto'];

                      //$campos = $calidad->ConsultarVistaMovimientoCalidad('Movimiento_idMovimiento = ' . $referencias[$posRef]["idMovimiento"] . ' and Producto_idProducto = ' . $referencias[$posRef]["Producto_idProducto"]);
                      //$referencias[$posRef]['idMovimientoCalidad'] = (isset($campos[0]['idMovimientoCalidad']) ? $campos[0]['idMovimientoCalidad'] : 0);
                     */
                    $regtotal = count($datos);
                    for ($i = 0; $i < $regtotal; $i++) {
                        $posRef++;
                        $referencias[$posRef] = $referencias[$posRef - 1];
                        $referencias[$posRef]["idMovimiento"] = $datos[$i]['idMovimiento'];
                        $referencias[$posRef]["Producto_idProducto"] = $datos[$i]['Producto_idProducto'];

                        $campos = $calidad->ConsultarVistaMovimientoCalidad('Movimiento_idMovimiento = ' . $referencias[$posRef]["idMovimiento"] . ' and Producto_idProducto = ' . $referencias[$posRef]["Producto_idProducto"]);
                        $referencias[$posRef]['idMovimientoCalidad'] = (isset($campos[0]['idMovimientoCalidad']) ? $campos[0]['idMovimientoCalidad'] : 0);
                    }
                } else {
                    $referencias[$posRef]["idMovimiento"] = 0;
                    $referencias[$posRef]["Producto_idProducto"] = 0;

                    $referencias[$posRef]['idMovimientoCalidad'] = 0;
                }

                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL
                and $refAnterior == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue()) {
                    $fila++;
                }
            }
            return;
            //	print_r($referencias);
            // luego de que tenemos la matriz de referencias llena, las enviamos al proceso de importacion de productos
            // para que los valide e importe al sistema
            $retorno = $this->llenarMovimientoCalidad($referencias, $campos);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($referencias);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));

            return $retorno;
        }

        function llenarMovimientoCalidad($referencias) {
            // instanciamos la clase producto y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'movimientocalidad.class.php';
            $calidad = new MovimientoCalidad();

            $retorno = array();
            // contamos los registros del array de productos



            $totalreg = ((isset($referencias[0]["idMovimiento"]) and isset($referencias[0]["Producto_idProducto"])) ? count($referencias) : 0);
            for ($i = 0; $i < $totalreg; $i++) {

                $nuevoserrores = $this->validarMovimientoCalidad($i, $referencias);
                //print_r($nuevoserrores);
                $totalerr = count($nuevoserrores);

                if (!isset($nuevoserrores[0]["error"])) {

                    // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
                    $calidad->MovimientoCalidad();

                    $calidad->idMovimientoCalidad = (isset($referencias[$i]['idMovimientoCalidad']) ? $referencias[$i]['idMovimientoCalidad'] : 0);
                    $calidad->Movimiento_idMovimiento = (isset($referencias[$i]['idMovimiento']) ? $referencias[$i]['idMovimiento'] : 0);
                    $calidad->Producto_idProducto = (isset($referencias[$i]['Producto_idProducto']) ? $referencias[$i]['Producto_idProducto'] : 0);
                    $calidad->fechaMaximaInspeccionMovimientoCalidad = (isset($referencias[$i]['fechaMaximaInspeccionMovimientoCalidad']) ? $referencias[$i]['fechaMaximaInspeccionMovimientoCalidad'] : '');

                    $calidad->tieneCitaInspeccionMovimientoCalidad = ($referencias[$i]['fechaCitaInspeccionMovimientoCalidad'] == '' ? 'NO' : 'SI');


                    $calidad->fechaCitaInspeccionMovimientoCalidad = (isset($referencias[$i]['fechaCitaInspeccionMovimientoCalidad']) ? $referencias[$i]['fechaCitaInspeccionMovimientoCalidad'] : '');
                    $calidad->fechaRealInspeccionMovimientoCalidad = (isset($referencias[$i]['fechaRealInspeccionMovimientoCalidad']) ? $referencias[$i]['fechaRealInspeccionMovimientoCalidad'] : '');
                    $calidad->fechaReporteInspeccionMovimientoCalidad = (isset($referencias[$i]['fechaReporteInspeccionMovimientoCalidad']) ? $referencias[$i]['fechaReporteInspeccionMovimientoCalidad'] : '');
                    $calidad->resultadoInspeccionMovimientoCalidad = (isset($referencias[$i]['resultadoInspeccionMovimientoCalidad']) ? $referencias[$i]['resultadoInspeccionMovimientoCalidad'] : '');

                    switch (strtolower($referencias[$i]['resultadoInspeccionMovimientoCalidad'])) {
                        case 'aprobada':
                            $calidad->tieneInspeccionMovimientoCalidad = 'SI';
                            break;
                        case 'rechazada':
                            $calidad->tieneInspeccionMovimientoCalidad = 'SI';
                            break;
                        case 'embarcada sin inspección';
                            $calidad->tieneInspeccionMovimientoCalidad = 'SI';
                            break;
                        case 'sin inspección':
                            $calidad->tieneInspeccionMovimientoCalidad = 'NO';
                            break;
                        case 'programada':
                            $calidad->tieneInspeccionMovimientoCalidad = 'NO';
                            break;
                        default :
                            $calidad->tieneInspeccionMovimientoCalidad = 'VALIDAR DATOS';
                            break;
                    }



                    $calidad->fechaAprobacionInspeccionMovimientoCalidad = (isset($referencias[$i]['fechaAprobacionInspeccionMovimientoCalidad']) ? $referencias[$i]['fechaAprobacionInspeccionMovimientoCalidad'] : '');


                    $calidad->observacionInspeccionMovimientoCalidad = (isset($referencias[$i]['observacionInspeccionMovimientoCalidad']) ? $referencias[$i]['observacionInspeccionMovimientoCalidad'] : '');
                    $calidad->observacionDetalleInspeccionMovimientoCalidad = (isset($referencias[$i]['observacionDetalleInspeccionMovimientoCalidad']) ? $referencias[$i]['observacionDetalleInspeccionMovimientoCalidad'] : '');

                    $calidad->requiereReinspeccionMovimientoCalidad = (strtolower($referencias[$i]['resultadoInspeccionMovimientoCalidad']) == 'rechazada' ? 'SI' : 'NO');

                    $calidad->fechaMaximaReinspeccionMovimientoCalidad = (isset($referencias[$i]['fechaMaximaReinspeccionMovimientoCalidad']) ? $referencias[$i]['fechaMaximaReinspeccionMovimientoCalidad'] : '');
                    $calidad->fechaReinspeccionMovimientoCalidad = (isset($referencias[$i]['fechaReinspeccionMovimientoCalidad']) ? $referencias[$i]['fechaReinspeccionMovimientoCalidad'] : '');
                    $calidad->resultadoReinspeccionMovimientoCalidad = (isset($referencias[$i]['resultadoReinspeccionMovimientoCalidad']) ? $referencias[$i]['resultadoReinspeccionMovimientoCalidad'] : '');

                    $calidad->observacionReinspeccionMovimientoCalidad = (isset($referencias[$i]['observacionReinspeccionMovimientoCalidad']) ? $referencias[$i]['observacionReinspeccionMovimientoCalidad'] : '');



                    $calidad->resultadoDefinitivoMovimientoCalidad = (strtolower($referencias[$i]['resultadoInspeccionMovimientoCalidad']) == 'aprobada' ? 'aprobada' :
                                    (strtolower($referencias[$i]['resultadoReinspeccionMovimientoCalidad']) == 'aprobada' ? 'aprobada' :
                                            (strtolower($referencias[$i]['resultadoInspeccionMovimientoCalidad']) == 'cancelada' ? 'cancelada' :
                                                    (strtolower($referencias[$i]['resultadoInspeccionMovimientoCalidad']) == 'programada' ? 'pendiente' :
                                                            (strtolower($referencias[$i]['resultadoInspeccionMovimientoCalidad']) == 'stock excento' ? 'stock excento' : 'rechazada')))));


                    // cada que llenamos un producto, lo cargamos a la base de datos
                    // si el id esta lleno, lo actualizamos, si esta vacio lo insertamos
                    if ($referencias[$i]['idMovimientoCalidad'] == 0) {
                        $calidad->AdicionarMovimientoCalidad();
                    } else {
                        $calidad->ModificarMovimientoCalidad();
                    }
                } else {
                    $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                }
            }

            return $retorno;
        }

        function validarMovimientoCalidad($x, $referencias) {

            $swerror = true;
            $errores = array();
            $linea = 0;


            //and ($referencias[$x]["idMovimiento"] == 0 and $referencias[$x]["numeroMovimiento"] != '')
            if (isset($referencias[$x]["idMovimiento"]) and ( $referencias[$x]["idMovimiento"] == 0)) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["error"] = 'El numero de la PI (' . $referencias[$x]["numeroMovimiento"] . ') no existe';
                $swerror = false;
                $linea++;
            }

            //and ($referencias[$x]["Producto_idProducto"] == 0 and $referencias[$x]["referenciaProducto"] != '')
            if (isset($referencias[$x]["Producto_idProducto"]) and ( $referencias[$x]["Producto_idProducto"] == 0)) {
                $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
                $errores[$linea]["error"] = 'El numero de REF (' . $referencias[$x]["referenciaProducto"] . ') no existe';
                $swerror = false;
                $linea++;
            }


            return $errores;
        }

        function ImportarNovedadNovidadExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once '../clases/gruponomina.class.php';
            $grupo = new GrupoNomina();
            require_once '../clases/tercero.class.php';
            $tercero = new Tercero();
            require_once '../clases/conceptonomina.class.php';
            $concepto = new ConceptoNomina();
            require_once '../clases/periodo.class.php';
            $periodo = new Periodo();


            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {
                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();
                $grupoAnterior = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue();

                // por cada numero de documento diferente, llenamos el encabezado
                $posEnc++;

                // para cada registro del encabezado recorremos las columnas desde la 0 hasta la 3
                for ($columna = 0; $columna <= 3; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // validamos el periodo
                $periodo->idPeriodo = 0;
                if (!empty($encabezado[$posEnc]["fechaElaboracionNovedadNomina"]))
                    $periodo->ConsultarPeriodo("fechaInicialPeriodo <=  '" . $encabezado[$posEnc]["fechaElaboracionNovedadNomina"] .
                            "' and fechaFinalPeriodo >=  '" . $encabezado[$posEnc]["fechaElaboracionNovedadNomina"] .
                            "'  and estadoPeriodo = 'ACTIVO'");
                $encabezado[$posEnc]["Periodo_idPeriodo"] = $periodo->idPeriodo;

                // validamos el grupo de nomina
                $grupo->idGrupoNomina = 0;
                if (!empty($encabezado[$posEnc]["codigoAlternoGrupoNomina"]))
                    $datos = $grupo->ConsultarVistaGrupoNomina("codigoAlternoGrupoNomina = '" . $encabezado[$posEnc]["codigoAlternoGrupoNomina"] . "'");
                $encabezado[$posEnc]["GrupoNomina_idGrupoNomina"] = isset($datos[0]['idGrupoNomina']) ? $datos[0]['idGrupoNomina'] : 0;



                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL and
                $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() and
                $grupoAnterior == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue()) {

                    // por cada numero de documento, llenamos el detelle
                    $posDet++;

                    // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                    $detalle[$posDet]["fechaElaboracionNovedadNomina"] = $numeroAnt;
                    $detalle[$posDet]["GrupoNomina_idGrupoNomina"] = $encabezado[$posEnc]["GrupoNomina_idGrupoNomina"];

                    // para cada registro del detalle recorremos las columnas desde la 7 hasta la 12
                    for ($columna = 4; $columna <= 8; $columna++) {
                        // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    }


                    // consultamos el NIT del Cliente como tipo Tercero Principal en la tabla de terceros para obtener el ID
                    $tercero->idTercero = 0;
                    if (!empty($detalle[$posDet]["documentoTercero"]))
                        $tercero->ConsultarIdTercero("documentoTercero = '" . $detalle[$posDet]["documentoTercero"] . "' or codigoAlterno1Tercero = '" . $detalle[$posDet]["documentoTercero"] . "'");
                    $detalle[$posDet]["Tercero_idEmpleado"] = $tercero->idTercero;

                    //consultamos el concepto
                    $concepto->idConceptoNomina = 0;
                    if (!empty($detalle[$posDet]['codigoAlternoConceptoNomina']))
                        $concepto->ConsultarIdConceptoNomina("codigoAlternoConceptoNomina = '" . $detalle[$posDet]['codigoAlternoConceptoNomina'] . "'");
                    $detalle[$posDet]['ConceptoNomina_idConceptoNomina'] = $concepto->idConceptoNomina;


                    // pasamos a la siguiente fila
                    $fila++;
                }
            }


            //print_r($encabezado);
            //print_r($detalle);
            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos contables
            // para que las valide e importe al sistema, para esto recorremos cada documento importado para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array
            $retorno = $this->llenarPropiedadesNovedadNomina($encabezado, $detalle);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($encabezado);
            unset($detalle);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
            return $retorno;
        }

        function llenarPropiedadesNovedadNomina($encabezado, $detalle) {


            // instanciamos la clase conceptonomina y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once '../clases/novedadnomina.class.php';
            $novedad = new NovedadNomina();




            $retorno = array();
            // contamos los registros del encabezado
            $totalreg = (isset($encabezado[0]["fechaElaboracionNovedadNomina"]) ? count($encabezado) : 0);
            for ($i = 0; $i < $totalreg; $i++) {
                //echo 'primer for<br>';
                $nuevoserrores = $this->validarNovedadNomina($encabezado[$i]["fechaElaboracionNovedadNomina"], $encabezado, $detalle);
                $totalerr = count($nuevoserrores);
                //'ERRORES '.isset($nuevoserrores[0]["error"])."<br>";
                if (!isset($nuevoserrores[0]["error"])) {

                    // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
                    $novedad = new NovedadNomina();

                    $novedad->fechaElaboracionNovedadNomina = (isset($encabezado[$i]['fechaElaboracionNovedadNomina']) ? $encabezado[$i]['fechaElaboracionNovedadNomina'] : '');
                    $novedad->GrupoNomina_idGrupoNomina = (isset($encabezado[$i]['GrupoNomina_idGrupoNomina']) ? $encabezado[$i]['GrupoNomina_idGrupoNomina'] : 0);
                    $novedad->periodoNovedadNomina = (isset($encabezado[$i]['periodoNovedadNomina']) ? $encabezado[$i]['periodoNovedadNomina'] : 0);
                    $novedad->observacionNovedadNomina = (isset($encabezado[$i]['observacionNovedadNomina']) ? $encabezado[$i]['observacionNovedadNomina'] : '');
                    $novedad->Periodo_idPeriodo = (isset($encabezado[$i]['Periodo_idPeriodo']) ? $encabezado[$i]['Periodo_idPeriodo'] : '');
                    // por cada registro del encabezado, recorremos el detalle para obtener solo los datos del mismo numero de movimiento del encabezado, con estos
                    // llenamos arrays por cada campo
                    $totaldet = (isset($detalle[0]["fechaElaboracionNovedadNomina"]) ? count($detalle) : 0);



                    // llevamos un contador de registros por cada producto del detalle
                    $registroact = 0;
                    for ($j = 0; $j < $totaldet; $j++) {
                        //echo 'segundo for<br>';
                        if (isset($encabezado[$i]["fechaElaboracionNovedadNomina"]) and
                                isset($detalle[$j]["fechaElaboracionNovedadNomina"]) and
                                $encabezado[$i]["fechaElaboracionNovedadNomina"] == $detalle[$j]["fechaElaboracionNovedadNomina"] and
                                $encabezado[$i]["GrupoNomina_idGrupoNomina"] == $detalle[$j]["GrupoNomina_idGrupoNomina"]) {

                            $novedad->idNovedadNominaDetalle[$registroact] = 0;
                            $novedad->Tercero_idEmpleado[$registroact] = (isset($detalle[$j]['Tercero_idEmpleado']) ? $detalle[$j]['Tercero_idEmpleado'] : 0);
                            $novedad->ConceptoNomina_idConceptoNomina[$registroact] = (isset($detalle[$j]['ConceptoNomina_idConceptoNomina']) ? $detalle[$j]['ConceptoNomina_idConceptoNomina'] : 0);
                            $novedad->horasNovedadNominaDetalle[$registroact] = (isset($detalle[$j]['horasNovedadNominaDetalle']) ? $detalle[$j]['horasNovedadNominaDetalle'] : 0);
                            $novedad->valorNovedadNominaDetalle[$registroact] = (isset($detalle[$j]['valorNovedadNominaDetalle']) ? $detalle[$j]['valorNovedadNominaDetalle'] : 0);
                            $novedad->observacionNovedadNominaDetalle[$registroact] = (isset($detalle[$j]['observacionNovedadNominaDetalle']) ? $detalle[$j]['observacionNovedadNominaDetalle'] : '');

                            $registroact++;
                        }
                    }
                    //echo 'numero' . $registroact;
                    // cada que llenamos un documento, lo cargamos a la base de datos
                    $novedad->AdicionarNovedadNomina();
                } else {
                    $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                }
            }
            //print_r($retorno);
            return $retorno;
        }

        function validarNovedadNomina($fechaElaboracionNovedadNomina, $encabezado, $detalle) {
            require_once('../clases/cuentacontable.class.php');
            $cuentacontable = new CuentaContable();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();

            $swerror = true;
            $errores = array();
            $linea = 0;
            $totalreg = (isset($encabezado[0]["fechaElaboracionNovedadNomina"]) ? count($encabezado) : 0);
            for ($x = 0; $x < $totalreg; $x++) {


                // Verificamos que el periodo exista
                if (isset($encabezado[$x]["Periodo_idPeriodo"]) and ( ($encabezado[$x]["Periodo_idPeriodo"] == 0 and $encabezado[$x]["fechaElaboracionNovedadNomina"] != '') or
                        $encabezado[$x]["fechaElaboracionNovedadNomina"] == '')) {
                    $errores[$linea]["fechaElaboracionNovedadNomina"] = $encabezado[$x]["fechaElaboracionNovedadNomina"];
                    $errores[$linea]["error"] = 'El periodo contable para la fecha (' . $encabezado[$x]["fechaElaboracionNovedadNomina"] .
                            ') no existe en la base de datos o es un periodo cerrado';
                    $swerror = false;
                    $linea++;
                }

                // Verificamos que el grupo de nomina exista
                if (isset($encabezado[$x]["GrupoNomina_idGrupoNomina"]) and ( ($encabezado[$x]["GrupoNomina_idGrupoNomina"] == 0 and $encabezado[$x]["codigoAlternoGrupoNomina"] != '') or
                        $encabezado[$x]["codigoAlternoGrupoNomina"] == '')) {
                    $errores[$linea]["fechaElaboracionNovedadNomina"] = $encabezado[$x]["fechaElaboracionNovedadNomina"];
                    $errores[$linea]["error"] = 'El grupo de nomina (' . $encabezado[$x]["codigoAlternoGrupoNomina"] .
                            ') no existe en la base de datos';
                    $swerror = false;
                    $linea++;
                }

                // Verificamos que el grupo de nomina exista
                if (isset($encabezado[$x]["periodoNovedadNomina"]) and
                        $encabezado[$x]["periodoNovedadNomina"] == '') {
                    $errores[$linea]["fechaElaboracionNovedadNomina"] = $encabezado[$x]["fechaElaboracionNovedadNomina"];
                    $errores[$linea]["error"] = 'El periodo de nomina (' . $encabezado[$x]["periodoNovedadNomina"] .
                            ') esta en blanco';
                    $swerror = false;
                    $linea++;
                }

                $totaldet = (isset($detalle[0]["fechaElaboracionNovedadNomina"]) ? count($detalle) : 0);
                for ($y = 0; $y < $totaldet; $y++) {
                    if (isset($encabezado[$x]["fechaElaboracionNovedadNomina"]) and isset($detalle[$y]["fechaElaboracionNovedadNomina"]) and $encabezado[$x]["fechaElaboracionNovedadNomina"] == $detalle[$y]["fechaElaboracionNovedadNomina"]) {
                        // Verificamos que el grupo de nomina exista
                        if (isset($detalle[$y]["Tercero_idEmpleado"]) and
                                $detalle[$y]["Tercero_idEmpleado"] == 0 and ( $detalle[$y]["documentoTercero"] != '' or $detalle[$y]["documentoTercero"] == '')) {

                            $errores[$linea]["fechaElaboracionNovedadNomina"] = $detalle[$y]["fechaElaboracionNovedadNomina"];
                            $errores[$linea]["error"] = 'El documento del Tercero (' . $detalle[$y]["documentoTercero"] .
                                    ') no existe en la base de datos';
                            $swerror = false;
                            $linea++;
                        }

                        //					if (isset($detalle[$y]["ConceptoNomina_idConceptoNomina"]) and
                        //						(($detalle[$y]["ConceptoNomina_idConceptoNomina"] == 0 and $detalle[$y]["codigoAlternoConceptoNomina"] != '') or
                        //						$detalle[$y]["codigoAlternoConceptoNomina"] == ''))
                        //					{
                        //						$errores[$linea]["fechaElaboracionNovedadNomina"] = $detalle[$y]["fechaElaboracionNovedadNomina"];
                        //						$errores[$linea]["error"] = 'El concepto nomina (' . $detalle[$y]["codigoAlternoConceptoNomina"] .
                        //							') no existe en la base de datos';
                        //						$swerror = false;
                        //						$linea++;
                        //					}
                        // Verificamos que el grupo de nomina exista
                        if (($detalle[$y]["horasNovedadNominaDetalle"] == '' or $detalle[$y]["horasNovedadNominaDetalle"] == 0) and ( $detalle[$y]["valorNovedadNominaDetalle"] == '' or $detalle[$y]["valorNovedadNominaDetalle"] == 0)) {
                            $errores[$linea]["fechaElaboracionNovedadNomina"] = $detalle[$y]["fechaElaboracionNovedadNomina"];
                            $errores[$linea]["error"] = 'Las horas de la novedad de nomina (' . $detalle[$y]["horasNovedadNominaDetalle"] .
                                    ') o el valor de la novedad (' . $detalle[$y]["valorNovedadNominaDetalle"] .
                                    ') estan en cero o vacía';
                            $swerror = false;
                            $linea++;
                        }
                    }
                }
            }
            return $errores;
        }

        function ImportarNovedadNovidadMatrizExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once '../clases/gruponomina.class.php';
            $grupo = new GrupoNomina();
            require_once '../clases/tercero.class.php';
            $tercero = new Tercero();
            require_once '../clases/conceptonomina.class.php';
            $concepto = new ConceptoNomina();
            require_once '../clases/periodo.class.php';
            $periodo = new Periodo();


            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;

            $encabezado[0]["codigoAlternoGrupoNomina"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, 2)->getValue();
            ;

            $grupo->idGrupoNomina = 0;
            if (!empty($encabezado[0]["codigoAlternoGrupoNomina"]))
                $datos = $grupo->ConsultarVistaGrupoNomina("codigoAlternoGrupoNomina = '" . $encabezado[0]["codigoAlternoGrupoNomina"] . "'");
            $encabezado[0]["GrupoNomina_idGrupoNomina"] = isset($datos[0]['idGrupoNomina']) ? $datos[0]['idGrupoNomina'] : 0;


            // tomamos el periodo de nomina y la fecha
            $encabezado[0]["periodoNovedadNomina"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, 3)->getValue();
            $encabezado[0]["fechaElaboracionNovedadNomina"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, 4)->getValue();

            $fechaReal = $encabezado[0]["fechaElaboracionNovedadNomina"];
            $encabezado[0]["fechaElaboracionNovedadNomina"] = (gettype($fechaReal) == 'double' or gettype($fechaReal) == 'integer' and $fechaReal > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal))) : $encabezado[0]["fechaElaboracionNovedadNomina"];

            $periodo->idPeriodo = 0;

            $periodo->ConsultarPeriodo("fechaInicialPeriodo <=  '" . $encabezado[0]["fechaElaboracionNovedadNomina"] .
                    "' and fechaFinalPeriodo >=  '" . $encabezado[0]["fechaElaboracionNovedadNomina"] .
                    "'  and estadoPeriodo = 'ACTIVO'");

            $encabezado[0]["Periodo_idPeriodo"] = $periodo->idPeriodo;

            $fila = 9;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $columna = 3;
                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 6)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 6)->getValue() != NULL) {
                    // por cada numero de documento diferente, llenamos el encabezado
                    $posDet++;

                    // para cada registro del encabezado recorremos las columnas desde la 0 hasta la 3
                    for ($col = 0; $col <= 2; $col++) {
                        // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, 6)->getValue();
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, $fila)->getValue();
                    }

                    $detalle[$posDet]["codigoAlternoConceptoNomina"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 6)->getValue();
                    $detalle[$posDet]["horasNovedadNominaDetalle"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    $detalle[$posDet]["valorNovedadNominaDetalle"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna + 1, $fila)->getValue();


                    $detalle[$posDet]["GrupoNomina_idGrupoNomina"] = $encabezado[0]["GrupoNomina_idGrupoNomina"];
                    $detalle[$posDet]["fechaElaboracionNovedadNomina"] = $encabezado[0]["fechaElaboracionNovedadNomina"];

                    $concepto->idConceptoNomina = 0;
                    if (!empty($detalle[$posDet]["codigoAlternoConceptoNomina"]))
                        $concepto->ConsultarIdConceptoNomina("codigoAlternoConceptoNomina = '" . $detalle[$posDet]["codigoAlternoConceptoNomina"] . "'");
                    $detalle[$posDet]['ConceptoNomina_idConceptoNomina'] = $concepto->idConceptoNomina;


                    $tercero->idTercero = 0;
                    if (!empty($detalle[$posDet]["documentoTercero"]))
                        $tercero->ConsultarIdTercero("documentoTercero = '" . $detalle[$posDet]["documentoTercero"] . "' or codigoAlterno1Tercero = '" . $detalle[$posDet]["documentoTercero"] . "'");
                    $detalle[$posDet]["Tercero_idEmpleado"] = $tercero->idTercero;


                    $columna+=2;
                }

                // pasamos a la siguiente fila
                $fila++;
            }

            /* echo '<br><br>';
              print_r($encabezado);
              echo '<br><br>';
              print_r($detalle);
              echo '<br><br>'; */
            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos contables
            // para que las valide e importe al sistema, para esto recorremos cada documento importado para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array
            $retorno = $this->llenarPropiedadesNovedadNomina($encabezado, $detalle);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($encabezado);
            unset($detalle);

            //$this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
            return $retorno;
        }

        function ImportarConceptoNominaEmpleadoExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/periodo.class.php');
            $periodo = new Periodo();
            require_once('../clases/cuentacontable.class.php');
            $cuentacontable = new CuentaContable();
            require_once('../clases/centrocosto.class.php');
            $centrocosto = new CentroCosto();
            require_once('../clases/conceptonomina.class.php');
            $concepto = new ConceptoNomina();



            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;


            $fila = 4;

            // mientras no este en blanco el numero de la cuenta contable
            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {
                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue();

                // por cada numero de cuenta, llenamos el encabezado
                $posEnc++;

                // para cada registro recorremos las columnas desde la 0 hasta la 5
                for ($columna = 0; $columna <= 7; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // consultamos el NIT del Cliente como tipo Tercero Principal en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($encabezado[$posEnc]["documentoTercero"]))
                    $tercero->ConsultarIdTercero("documentoTercero = '" . $encabezado[$posEnc]["documentoTercero"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["documentoTercero"] . "'");
                $encabezado[$posEnc]["Tercero_idEmpleado"] = $tercero->idTercero;

                //consultamos el concepto
                $concepto->idConceptoNomina = 0;
                if (!empty($encabezado[$posEnc]['codigoAlternoConceptoNomina']))
                    $concepto->ConsultarIdConceptoNomina("codigoAlternoConceptoNomina = '" . $encabezado[$posEnc]['codigoAlternoConceptoNomina'] . "'");
                $encabezado[$posEnc]['ConceptoNomina_idConceptoNomina'] = $concepto->idConceptoNomina;



                // pasamos a la siguiente fila
                $fila++;
            }

            //print_r($encabezado);
            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos contables
            // para que las valide e importe al sistema, para esto recorremos cada documento importado para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array
            $retorno = $this->llenarPropiedadesConceptoNominaEmpleado($encabezado);


            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
            return $retorno;
        }

        function llenarPropiedadesConceptoNominaEmpleado($encabezado) {
            // instanciamos la clase conceptonomina y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once '../clases/conceptonominaempleado.class.php';
            $novedad = new ConceptoNominaEmpleado();

            $retorno = array();

            // contamos los registros del encabezado
            $totalreg = (isset($encabezado[0]["Tercero_idEmpleado"]) ? count($encabezado) : 0);


            $nuevoserrores = $this->validarConceptoNominaEmpleado($encabezado);
            $totalerr = count($nuevoserrores);
            //'ERRORES '.isset($nuevoserrores[0]["error"])."<br>";
            if (!isset($nuevoserrores[0]["error"])) {
                $i = 0;
                while ($i < $totalreg) {
                    $novedad->Tercero_idEmpleado = $encabezado[$i]['Tercero_idEmpleado'];

                    $novedad->idConceptoNominaEmpleado = array();
                    $novedad->ConceptoNomina_idConceptoNomina = array();
                    $novedad->documentoConceptoNominaEmpleado = array();
                    $novedad->horasConceptoNominaEmpleado = array();
                    $novedad->valorConceptoNominaEmpleado = array();
                    $novedad->fechaInicialConceptoNominaEmpleado = array();
                    $novedad->numeroPeriodosConceptoNominaEmpleado = array();
                    $novedad->observacionConceptoNominaEmpleado = array();

                    $registroact = 0;

                    while ($i < $totalreg and
                    $novedad->Tercero_idEmpleado == $encabezado[$i]['Tercero_idEmpleado']) {

                        $novedad->idConceptoNominaEmpleado[$registroact] = 0;
                        $novedad->ConceptoNomina_idConceptoNomina[$registroact] = (isset($encabezado[$i]['ConceptoNomina_idConceptoNomina']) ? $encabezado[$i]['ConceptoNomina_idConceptoNomina'] : 0);
                        $novedad->documentoConceptoNominaEmpleado[$registroact] = (isset($encabezado[$i]['documentoConceptoNominaEmpleado']) ? $encabezado[$i]['documentoConceptoNominaEmpleado'] : '');
                        $novedad->horasConceptoNominaEmpleado[$registroact] = (isset($encabezado[$i]['horasConceptoNominaEmpleado']) ? $encabezado[$i]['horasConceptoNominaEmpleado'] : 0);
                        $novedad->valorConceptoNominaEmpleado[$registroact] = (isset($encabezado[$i]['valorConceptoNominaEmpleado']) ? $encabezado[$i]['valorConceptoNominaEmpleado'] : 0);
                        $novedad->fechaInicialConceptoNominaEmpleado[$registroact] = (isset($encabezado[$i]['fechaInicialConceptoNominaEmpleado']) ? $encabezado[$i]['fechaInicialConceptoNominaEmpleado'] : '');
                        $novedad->numeroPeriodosConceptoNominaEmpleado[$registroact] = (isset($encabezado[$i]['numeroPeriodosConceptoNominaEmpleado']) ? $encabezado[$i]['numeroPeriodosConceptoNominaEmpleado'] : 0);
                        $novedad->observacionConceptoNominaEmpleado[$registroact] = (isset($encabezado[$i]['observacionConceptoNominaEmpleado']) ? $encabezado[$i]['observacionConceptoNominaEmpleado'] : '');

                        $i++;
                        $registroact++;
                    }
                    $novedad->AdicionarConceptoNominaEmpleado();
                }
            } else {
                $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
            }
            // cada que llenamos un documento, lo cargamos a la base de datos
            //print_r($retorno);
            return $retorno;
        }

        function validarConceptoNominaEmpleado($encabezado) {
            require_once('../clases/cuentacontable.class.php');
            $cuentacontable = new CuentaContable();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();

            $swerror = true;
            $errores = array();
            $linea = 0;
            $totalreg = (isset($encabezado[0]["Tercero_idEmpleado"]) ? count($encabezado) : 0);
            for ($x = 0; $x < $totalreg; $x++) {


                // Verificamos que el periodo exista
                if (isset($encabezado[$x]["Tercero_idEmpleado"]) and ( ($encabezado[$x]["Tercero_idEmpleado"] == 0 and $encabezado[$x]["documentoTercero"] != '') or
                        $encabezado[$x]["documentoTercero"] == '')) {
                    $errores[$linea]["documentoTercero"] = $encabezado[$x]["documentoTercero"];
                    $errores[$linea]["error"] = 'El Empleado (' . $encabezado[$x]["documentoTercero"] .
                            ') no existe en la base de datos';
                    $swerror = false;
                    $linea++;
                }

                // Verificamos que el grupo de nomina exista
                if (isset($encabezado[$x]["ConceptoNomina_idConceptoNomina"]) and ( ($encabezado[$x]["ConceptoNomina_idConceptoNomina"] == 0 and $encabezado[$x]["codigoAlternoConceptoNomina"] != '') or
                        $encabezado[$x]["codigoAlternoConceptoNomina"] == '')) {
                    $errores[$linea]["documentoTercero"] = $encabezado[$x]["documentoTercero"];
                    $errores[$linea]["error"] = 'El Concepto de nomina (' . $encabezado[$x]["codigoAlternoConceptoNomina"] .
                            ') no existe en la base de datos';
                    $swerror = false;
                    $linea++;
                }

                // Verificamos que este digitado el valor o las horas (uno de los 2 o los 2)
                if (($encabezado[$x]["horasConceptoNominaEmpleado"] == '' or $encabezado[$x]["horasConceptoNominaEmpleado"] == 0) and ( $encabezado[$x]["valorConceptoNominaEmpleado"] == '' or $encabezado[$x]["valorConceptoNominaEmpleado"] == 0)) {
                    $errores[$linea]["documentoTercero"] = $encabezado[$x]["documentoTercero"];
                    $errores[$linea]["error"] = 'Las horas del concepto de nomina (' . $encabezado[$x]["horasConceptoNominaEmpleado"] .
                            ') o el valor (' . $encabezado[$x]["valorConceptoNominaEmpleado"] .
                            ') estan en cero o vacía';
                    $swerror = false;
                    $linea++;
                }
            }
            return $errores;
        }

        function ImportarMovimientoContableExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once('../clases/documentocomercial.class.php');
            $documentocomercial = new Documento();
            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/moneda.class.php');
            $moneda = new Moneda();
            require_once('../clases/periodo.class.php');
            $periodo = new Periodo();
            require_once('../clases/cuentacontable.class.php');
            $cuentacontable = new CuentaContable();
            require_once('../clases/centrocosto.class.php');
            $centrocosto = new CentroCosto();

            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }

            $objReader->setLoadSheetsOnly('Movimiento Contable');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != NULL) {

                //echo 'entra';


                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue();

                // por cada numero de documento diferente, llenamos el encabezado
                $posEnc++;

                // para cada registro del encabezado recorremos las columnas desde la 0 hasta la 6
                for ($columna = 0; $columna <= 7; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // luego recorremos las columnas desde la 13 hasta la 13 para obetener los datos de pie de pagina
                for ($columna = 18; $columna <= 18; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // cada que llenemos un encabezado, hacemos las verificaciones de codigos necesarios
                // validamos el documento

                $fechaReal = $encabezado[$posEnc]["fechaElaboracionMovimientoContable"];
                $encabezado[$posEnc]["fechaElaboracionMovimientoContable"] = (gettype($fechaReal) == 'double' or gettype($fechaReal) == 'integer' and $fechaReal > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal))) : $encabezado[$posEnc]["fechaElaboracionMovimientoContable"];

                $documentocomercial->idDocumento = 0;
                if (!empty($encabezado[$posEnc]["codigoDocumento"]))
                    $documentocomercial->ConsultarDocumento("codigoAlternoDocumento =  '" . $encabezado[$posEnc]["codigoDocumento"] . "'");
                $encabezado[$posEnc]["Documento_idDocumento"] = $documentocomercial->idDocumento;

                $tercero->idTercero = 0;
                if (!empty($encabezado[$posEnc]["documentoTerceroPrincipal"]))
                    $tercero->ConsultarIdTercero("documentoTercero = '" . $encabezado[$posEnc]["documentoTerceroPrincipal"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["documentoTerceroPrincipal"] . "'");
                $encabezado[$posEnc]["Tercero_idTerceroPrincipal"] = $tercero->idTercero;




                // validamos el periodo
                $periodo->idPeriodo = 0;
                if (!empty($encabezado[$posEnc]["fechaElaboracionMovimientoContable"]))
                    $periodo->ConsultarPeriodo("fechaInicialPeriodo <=  '" . $encabezado[$posEnc]["fechaElaboracionMovimientoContable"] .
                            "' and fechaFinalPeriodo >=  '" . $encabezado[$posEnc]["fechaElaboracionMovimientoContable"] .
                            "'  and estadoPeriodo = 'ACTIVO'");
                $encabezado[$posEnc]["Periodo_idPeriodo"] = $periodo->idPeriodo;


                // consultamos la moneda  en la tabla de monedas para obtener el ID
                $moneda->idMoneda = 0;
                if (!empty($encabezado[$posEnc]["codigoMoneda"]))
                    $moneda->ConsultarMoneda("codigoAlternoMoneda = '" . $encabezado[$posEnc]["codigoMoneda"] . "'");
                $encabezado[$posEnc]["Moneda_idMoneda"] = $moneda->idMoneda;


                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != NULL and
                $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue()) {
                    // por cada numero de documento, llenamos el detelle
                    $posDet++;


                    // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                    $detalle[$posDet]["numeroMovimientoContable"] = $numeroAnt;

                    // para cada registro del detalle recorremos las columnas desde la 7 hasta la 12
                    for ($columna = 8; $columna <= 17; $columna++) {
                        // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    }


                    // consultamos el NIT del Cliente como tipo Tercero Principal en la tabla de terceros para obtener el ID
                    $tercero->idTercero = 0;
                    if (!empty($detalle[$posDet]["documentoTercero"]))
                        $tercero->ConsultarIdTercero("((tipoTercero like '%*01*%' or tipoTercero like '%*02*%') and tipoTercero not like '%*18*%') and (documentoTercero = '" . $detalle[$posDet]["documentoTercero"] . "' or codigoAlterno1Tercero = '" . $detalle[$posDet]["documentoTercero"] . "')");
                    $detalle[$posDet]["Tercero_idTercero"] = $tercero->idTercero;

                    // consultamos el Id de la Cuenta Contable
                    $cuentacontable->idCuentaContable = 0;
                    if (!empty($detalle[$posDet]["numeroCuentaContable"]))
                        $cuentacontable->ConsultarCuentaContable("numeroCuentaContable = '" . $detalle[$posDet]["numeroCuentaContable"] . "' ");
                    $detalle[$posDet]["CuentaContable_idCuentaContable"] = $cuentacontable->idCuentaContable;

                    // consultamos el Id del centro de costos
                    $centrocosto->idCentroCosto = 0;
                    if (!empty($detalle[$posDet]["codigoCentroCosto"]))
                        $centrocosto->ConsultarCentroCosto("trim(codigoAlternoCentroCosto) = '" . trim($detalle[$posDet]["codigoCentroCosto"]) . "' ");
                    $detalle[$posDet]["CentroCosto_idCentroCosto"] = $centrocosto->idCentroCosto;


                    // pasamos a la siguiente fila
                    $fila++;
                }
            }


            //return;
            //
                    //		print_r($encabezado);
            //                echo '<br>';
            //                echo '<br>';
            //                echo '<br>';
            //		print_r($detalle);
            //                return;
            //
            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos contables
            // para que las valide e importe al sistema, para esto recorremos cada documento importado para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array
            $retorno = $this->llenarPropiedadesMovimientoContable($encabezado, $detalle);
            //$retorno = '';

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($encabezado);
            unset($detalle);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
            return $retorno;
        }

        function llenarPropiedadesMovimientoContable($encabezado, $detalle) {
            // instanciamos la clase movimiento y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'movimientocontable.class.php';
            $movimiento = new MovimientoContable();

            require_once 'periodo.class.php';
            $periodo = new Periodo();

            $retorno = array();
            // contamos los registros del encabezado
            $totalreg = (isset($encabezado[0]["numeroMovimientoContable"]) ? count($encabezado) : 0);

            $nuevoserrores = $this->validarMovimientoContable($encabezado, $detalle);
            $totalerr = count($nuevoserrores);

            //

            if ($totalerr == 0) {

                for ($i = 0; $i < $totalreg; $i++) {


                    //'ERRORES '.isset($nuevoserrores[0]["error"])."<br>";
                    // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
                    $movimiento->MovimientoContable();
                    //echo 'registros de detalle '.count($movimiento->idMovimientoDetalle)."<br><br>";
                    $movimiento->Documento_idDocumento = (isset($encabezado[$i]["Documento_idDocumento"]) ? $encabezado[$i]["Documento_idDocumento"] : 0);

                    $movimiento->fechaElaboracionMovimientoContable = (isset($encabezado[$i]["fechaElaboracionMovimientoContable"]) ? $encabezado[$i]["fechaElaboracionMovimientoContable"] : date("Y-m-d"));

                    // obtenemos el período contable segun la fecha de elaboracion del documento
                    $datoper = $periodo->ConsultarVistaPeriodo("fechaInicialPeriodo <= '" . $movimiento->fechaElaboracionMovimientoContable .
                            "' and fechaFinalPeriodo >= '" . $movimiento->fechaElaboracionMovimientoContable . "'");
                    $movimiento->Periodo_idPeriodo = (isset($datoper[0]["idPeriodo"]) ? $datoper[0]["idPeriodo"] : 0);

                    $movimiento->prefijoMovimientoContable = (isset($encabezado[$i]["prefijoMovimientoContable"]) ? $encabezado[$i]["prefijoMovimientoContable"] : '');
                    $movimiento->numeroMovimientoContable = (isset($encabezado[$i]["numeroMovimientoContable"]) ? $encabezado[$i]["numeroMovimientoContable"] : '');
                    $movimiento->sufijoMovimientoContable = (isset($encabezado[$i]["sufijoMovimientoContable"]) ? $encabezado[$i]["sufijoMovimientoContable"] : '');
                    $movimiento->Moneda_idMoneda = (isset($encabezado[$i]["Moneda_idMoneda"]) ? $encabezado[$i]["Moneda_idMoneda"] : 0);
                    $movimiento->tasaCambioMovimientoContable = (isset($encabezado[$i]["tasaCambioMovimientoContable"]) ? $encabezado[$i]["tasaCambioMovimientoContable"] : 1);

                    $movimiento->Tercero_idTerceroPrincipal = (isset($encabezado[$i]["Tercero_idTerceroPrincipal"]) ? $encabezado[$i]["Tercero_idTerceroPrincipal"] : 0);
                    $movimiento->Movimiento_idMovimiento = (isset($encabezado[$i]["Movimiento_idMovimiento"]) ? $encabezado[$i]["Movimiento_idMovimiento"] : 0);
                    $movimiento->MovimientoAdministrativo_idMovimientoAdministrativo = (isset($encabezado[$i]["MovimientoAdministrativo_idMovimientoAdministrativo"]) ? $encabezado[$i]["MovimientoAdministrativo_idMovimientoAdministrativo"] : 0);
                    $movimiento->bloqueoMovimientoContable = (isset($encabezado[$i]["bloqueoMovimientoContable"]) ? $encabezado[$i]["bloqueoMovimientoContable"] : 1);
                    $movimiento->tipoReferenciaExternoMovimientoContable = (isset($encabezado[$i]["tipoReferenciaExternoMovimientoContable"]) ? $encabezado[$i]["tipoReferenciaExternoMovimientoContable"] : '');
                    $movimiento->numeroReferenciaExternoMovimientoContable = (isset($encabezado[$i]["numeroReferenciaExternoMovimientoContable"]) ? $encabezado[$i]["numeroReferenciaExternoMovimientoContable"] : '');


                    $movimiento->observacionMovimientoContable = (isset($encabezado[$i]["observacionMovimientoContable"]) ? $encabezado[$i]["observacionMovimientoContable"] : '');

                    $movimiento->totalDebitosMovimientoContable = 0;
                    $movimiento->totalCreditosMovimientoContable = 0;

                    $movimiento->totalDebitosNIIFMovimientoContable = 0;
                    $movimiento->totalCreditosNIIFMovimientoContable = 0;


                    $movimiento->estadoMovimientoContable = 'ACTIVO';


                    // por cada registro del encabezado, recorremos el detalle para obtener solo los datos del mismo numero de movimiento del encabezado, con estos
                    // llenamos arrays por cada campo
                    $totaldet = (isset($detalle[0]["numeroMovimientoContable"]) ? count($detalle) : 0);


                    // llevamos un contador de registros por cada producto del detalle
                    $registroact = 0;
                    for ($j = 0; $j < $totaldet; $j++) {
                        if (isset($encabezado[$i]["numeroMovimientoContable"]) and isset($detalle[$j]["numeroMovimientoContable"]) and $encabezado[$i]["numeroMovimientoContable"] == $detalle[$j]["numeroMovimientoContable"]) {


                            $movimiento->idMovimientoContableDetalle[$registroact] = 0;
                            $movimiento->CuentaContable_idCuentaContable[$registroact] = (isset($detalle[$j]["CuentaContable_idCuentaContable"]) ? $detalle[$j]["CuentaContable_idCuentaContable"] : 0);
                            $movimiento->Tercero_idTercero[$registroact] = (isset($detalle[$j]["Tercero_idTercero"]) ? $detalle[$j]["Tercero_idTercero"] : 0);
                            $movimiento->CentroCosto_idCentroCosto[$registroact] = (isset($detalle[$j]["CentroCosto_idCentroCosto"]) ? $detalle[$j]["CentroCosto_idCentroCosto"] : 0);
                            $movimiento->Producto_idProducto[$registroact] = (isset($detalle[$j]["Producto_idProducto"]) ? $detalle[$j]["Producto_idProducto"] : 0);
                            $movimiento->SegmentoOperacion_idSegmentoOperacion[$registroact] = (isset($detalle[$j]["SegmentoOperacion_idSegmentoOperacion"]) ? $detalle[$j]["SegmentoOperacion_idSegmentoOperacion"] : 0);

                            $movimiento->baseMovimientoContableDetalle[$registroact] = (isset($detalle[$j]["baseMovimientoContableDetalle"]) ? $detalle[$j]["baseMovimientoContableDetalle"] : 0);
                            $movimiento->debitosMovimientoContableDetalle[$registroact] = (isset($detalle[$j]["debitosMovimientoContableDetalle"]) ? $detalle[$j]["debitosMovimientoContableDetalle"] : 0);
                            $movimiento->creditosMovimientoContableDetalle[$registroact] = (isset($detalle[$j]["creditosMovimientoContableDetalle"]) ? $detalle[$j]["creditosMovimientoContableDetalle"] : 0);

                            $movimiento->baseNIIFMovimientoContableDetalle[$registroact] = (isset($detalle[$j]["baseNIIFMovimientoContableDetalle"]) ? $detalle[$j]["baseNIIFMovimientoContableDetalle"] : 0);
                            $movimiento->debitosNIIFMovimientoContableDetalle[$registroact] = (isset($detalle[$j]["debitosNIIFMovimientoContableDetalle"]) ? $detalle[$j]["debitosNIIFMovimientoContableDetalle"] : 0);
                            $movimiento->creditosNIIFMovimientoContableDetalle[$registroact] = (isset($detalle[$j]["creditosNIIFMovimientoContableDetalle"]) ? $detalle[$j]["creditosNIIFMovimientoContableDetalle"] : 0);

                            $movimiento->observacionMovimientoContableDetalle[$registroact] = (isset($detalle[$j]["observacionMovimientoContableDetalle"]) ? $detalle[$j]["observacionMovimientoContableDetalle"] : '');


                            $movimiento->totalDebitosMovimientoContable += (isset($detalle[$j]["debitosMovimientoContableDetalle"]) ? $detalle[$j]["debitosMovimientoContableDetalle"] : 0);
                            $movimiento->totalCreditosMovimientoContable += (isset($detalle[$j]["creditosMovimientoContableDetalle"]) ? $detalle[$j]["creditosMovimientoContableDetalle"] : 0);

                            $movimiento->totalDebitosNIIFMovimientoContable += (isset($detalle[$j]["debitosNIIFMovimientoContableDetalle"]) ? $detalle[$j]["debitosNIIFMovimientoContableDetalle"] : 0);
                            $movimiento->totalCreditosNIIFMovimientoContable += (isset($detalle[$j]["creditosNIIFMovimientoContableDetalle"]) ? $detalle[$j]["creditosNIIFMovimientoContableDetalle"] : 0);

                            $registroact++;
                        }
                    }

                    // cada que llenamos un documento, lo cargamos a la base de datos
                    $movimiento->AdicionarMovimientoContable();
                }
            } else {
                $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
            }

            return $retorno;
        }

        function validarMovimientoContable($encabezado, $detalle) {
            require_once('../clases/cuentacontable.class.php');
            $cuentacontable = new CuentaContable();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();

            $swerror = true;
            $errores = array();
            $linea = 0;
            $totalreg = (isset($encabezado[0]["numeroMovimientoContable"]) ? count($encabezado) : 0);
            for ($x = 0; $x < $totalreg; $x++) {


                // Verificamos que el periodo exista
                if (isset($encabezado[$x]["Periodo_idPeriodo"]) and ( $encabezado[$x]["Periodo_idPeriodo"] == 0 or $encabezado[$x]["Periodo_idPeriodo"] == '')) {
                    $errores[$linea]["numeroMovimientoContable"] = $encabezado[$x]["numeroMovimientoContable"];
                    $errores[$linea]["error"] = 'La Fecha de elaboracion (' . $encabezado[$x]["fechaElaboracionMovimientoContable"] .
                            ') no pertenece a un periodo ACTIVO o el periodo no se ha creado';
                    $swerror = false;
                    $linea++;
                }

                if (isset($encabezado[$x]["documentoTerceroPrincipal"]) and ( $encabezado[$x]["documentoTerceroPrincipal"] == '' or $encabezado[$x]["Tercero_idTerceroPrincipal"] == 0)) {
                    $errores[$linea]["numeroMovimientoContable"] = $encabezado[$x]["numeroMovimientoContable"];
                    $errores[$linea]["error"] = 'El Tercero del encabezado no existe o esta vacio';
                    $swerror = false;
                    $linea++;
                }



                $totaldet = (isset($detalle[0]["numeroMovimientoContable"]) ? count($detalle) : 0);
                for ($y = 0; $y < $totaldet; $y++) {

                    if (isset($encabezado[$x]["numeroMovimientoContable"]) and isset($detalle[$y]["numeroMovimientoContable"]) and $encabezado[$x]["numeroMovimientoContable"] == $detalle[$y]["numeroMovimientoContable"]) {

                        //verificamos que exista el centro de costos
                        if (isset($detalle[$y]["codigoCentroCosto"]) && $detalle[$y]["codigoCentroCosto"] != '' && $detalle[$y]["CentroCosto_idCentroCosto"] == 0) {
                            $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                            $errores[$linea]["error"] = 'El centro de costos con el codigo alterno (' . $detalle[$y]["codigoCentroCosto"] . ') no existe';
                            $swerror = false;
                            $linea++;
                        }

                        // Verificamos que la cuenta contable exista
                        if (isset($detalle[$y]["CuentaContable_idCuentaContable"]) and ( $detalle[$y]["CuentaContable_idCuentaContable"] == 0 or $detalle[$y]["CuentaContable_idCuentaContable"] == '')) {
                            $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                            $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $detalle[$y]["numeroCuentaContable"] . ') no existe';
                            $swerror = false;
                            $linea++;
                        } else {
                            // consultamos el ID de la cuenta contable
                            $cuenta = $cuentacontable->ConsultarVistaCuentaContable("idCuentaContable = " . $detalle[$y]["CuentaContable_idCuentaContable"]);


                            if (!isset($cuenta[0]["idCuentaContable"])) {
                                $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $detalle[$y]["numeroCuentaContable"] . ') no se encontro en la Base de datos';
                                $swerror = false;
                                $linea++;
                            } else {
                                // verificamos que la cuenta contable SI sea afectable
                                if ($cuenta[0]["esAfectableCuentaContable"] != 1) {
                                    $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                    $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $detalle[$y]["numeroCuentaContable"] . ') no es afectable';
                                    $swerror = false;
                                    $linea++;
                                }

                                // si los debiditos y creditos son mayores a 0 y no aplican para contabilidad local
                                if ((isset($detalle[$y]["debitosMovimientoContableDetalle"]) and ( $detalle[$y]["debitosMovimientoContableDetalle"] > 0)) or ( isset($detalle[$y]["creditosMovimientoContableDetalle"]) and ( $detalle[$y]["creditosMovimientoContableDetalle"] > 0))) {


                                    if ($cuenta[0]["aplicaContabilidadCuentaContable"] == 0) {


                                        $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                        $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $detalle[$y]["numeroCuentaContable"] . ') no aplica para contabilidad local';
                                        $swerror = false;
                                        $linea++;
                                    }
                                }

                                // si los debiditos y creditos son mayores a 0 y no aplican para contabilidad NIIF
                                if ((isset($detalle[$y]["debitosNIIFMovimientoContableDetalle"]) and ( $detalle[$y]["debitosNIIFMovimientoContableDetalle"] > 0)) or ( isset($detalle[$y]["creditosNIIFMovimientoContableDetalle"]) and ( $detalle[$y]["creditosNIIFMovimientoContableDetalle"] > 0))) {



                                    if ($cuenta[0]["aplicaNIIFCuentaContable"] == 0) {

                                        $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                        $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $detalle[$y]["numeroCuentaContable"] . ') no aplica para contabilidad NIIF';
                                        $swerror = false;
                                        $linea++;
                                    }
                                }

                                // si el tercero no esta en blanco, verificamos que la cuenta si maneje tercero
                                if (isset($detalle[$y]["Tercero_idTercero"]) and ( $detalle[$y]["Tercero_idTercero"] != 0 and $detalle[$y]["Tercero_idTercero"] != '') and
                                        $cuenta[0]["manejaTerceroCuentaContable"] == 0) {
                                    $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                    $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $detalle[$y]["numeroCuentaContable"] . ') no maneja tercero';
                                    $swerror = false;
                                    $linea++;
                                }


                                // si esta lleno el id del tercero lo validamos
                                if (isset($detalle[$y]["Tercero_idTercero"]) and ( $detalle[$y]["Tercero_idTercero"] != 0 and $detalle[$y]["Tercero_idTercero"] != '')) {
                                    // buscamos los datos del tercero del documento
                                    $datos = $tercero->ConsultarVistaTercero("idTercero = " . $detalle[$y]["Tercero_idTercero"]);
                                    // contamos los registros obtenidos
                                    $totalTerceros = isset($datos[0]["idTercero"]) ? count($datos) : 0;
                                    $documento = '';

                                    // si no encontramos le tercero, mostramos el error
                                    if ($totalTerceros == 0) {

                                        $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                        $errores[$linea]["error"] = "El Tercero (" . $detalle[$y]["documentoTercero"] . ") de la cuenta contable (" . $detalle[$y]["numeroCuentaContable"] . ")  No existe";
                                        $swerror = false;
                                        $linea++;
                                    } else {
                                        $documento = $datos[0]["documentoTercero"];
                                    }

                                    // con el numero de documento del tercero del documento, buscamos en la tabla de terceros
                                    // el id del tercero del NIT PRINCIPAL ya que el tercero elegido en el documento actual puede ser una sucursal
                                    // y ese id no se debe llevar a la contabilidad, debe ser el id del tercero principal
                                    $datos2 = $tercero->ConsultarVistaTercero("documentoTercero = " . $documento .
                                            " and ((tipoTercero like '%*01*%' or tipoTercero like '%*02*%' or tipoTercero like '%*05*%') and tipoTercero not like '%*18*%')");


                                    // contamos los registros obtenidos
                                    $totalTerceros2 = isset($datos2[0]["idTercero"]) ? count($datos2) : 0;
                                    // si encontramos que hay varios terceros con el mismo nit y que no son de tipo sucursal (o sea que son el principal
                                    // advertimos al usuario para que corrija el maestreo de terceros antes de hacer el documento actual
                                    if ($totalTerceros2 > 1) {
                                        $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                        $errores[$linea]["error"] = "El Tercero (" . $detalle[$y]["documentoTercero"] . ") de la cuenta contable (" . $detalle[$y]["numeroCuentaContable"] . ") esta asociado a varios Nit Principales, por favor corrija esto en el Archivo de Terceros antes de elaborar el documento";
                                        $swerror = false;
                                        $linea++;
                                    } else {
                                        // si no se encontro el nit principal, mostramos alerta
                                        if ($totalTerceros2 == 0) {

                                            //echo $detalle[$y]["Tercero_idTercero"] . 'ID DEL TERCERO';

                                            $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                            $errores[$linea]["error"] = "El Tercero (" . $detalle[$y]["documentoTercero"] . ") de la cuenta contable (" . $detalle[$y]["numeroCuentaContable"] . ") No esta asociado a ningun Nit Principal, por favor corrija esto en el Archivo de Terceros antes de elaborar el documento";
                                            $swerror = false;
                                            $linea++;
                                        } else {

                                            // si existe solo un tercero con nit principal, lo llenamos en el dato del tercero inicial del documento
                                            if ($totalTerceros2 == 1) {
                                                $detalle[$y]["Tercero_idTercero"] = $datos2[0]["idTercero"];
                                            }
                                        }
                                    }
                                } else {
                                    // el tercero no existe y la cuenta maneja tercero
                                    if ($cuenta[0]["manejaTerceroCuentaContable"] == 1) {


                                        $errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                        $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $detalle[$y]["numeroCuentaContable"] . ') maneja tercero y este no existe, nit(' . $detalle[$y]["documentoTercero"] . ') ';
                                        $swerror = false;
                                        $linea++;
                                    }
                                }

                                // si el centro de costos  no esta en blanco, verificamos que la cuenta si maneje  centro de costos
                                //							if (isset($detalle[$y]["CentroCosto_idCentroCosto"]) and
                                //								($detalle[$y]["CentroCosto_idCentroCosto"] != 0 or $detalle[$y]["CentroCosto_idCentroCosto"] != '') and
                                //								$cuenta[0]["manejaCentroCostoCuentaContable"] == 0)
                                //							{
                                //								$errores[$linea]["numeroMovimientoContable"] = $detalle[$y]["numeroMovimientoContable"];
                                //								$errores[$linea]["error"] = 'El número de Cuenta Contable (' . $detalle[$y]["numeroCuentaContable"] . ') no maneja Centro de Costos';
                                //								$swerror = false;
                                //								$linea++;
                                //							}
                            }
                        }


                        //                                        echo $detalle[$y]["debitosMovimientoContableDetalle"].'<br>';
                        //                                        echo $detalle[$y]["creditosMovimientoContableDetalle"].'<br>';
                        // verificamos que los debitos no sean cero
                        //					7853
                    }
                }
            }
            return $errores;
        }

        function ImportarSaldoInicialContableExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();
            require_once('../clases/periodo.class.php');
            $periodo = new Periodo();
            require_once('../clases/cuentacontable.class.php');
            $cuentacontable = new CuentaContable();
            require_once('../clases/centrocosto.class.php');
            $centrocosto = new CentroCosto();



            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }


            $objReader->setLoadSheetsOnly('Movimiento Contable');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;


            $fila = 4;

            // mientras no este en blanco el numero de la cuenta contable

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {


                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();

                // por cada numero de cuenta, llenamos el encabezado
                $posEnc++;

                // para cada registro recorremos las columnas desde la 0 hasta la 5
                for ($columna = 0; $columna <= 10; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }


                // cada que llenemos un registro, hacemos las verificaciones de codigos necesarios
                // validamos el periodo
                $periodo->idPeriodo = 0;
                if (!empty($encabezado[$posEnc]["codigoAlternoPeriodo"]))
                    $periodo->ConsultarPeriodo("codigoAlternoPeriodo =  '" . $encabezado[$posEnc]["codigoAlternoPeriodo"] .
                            "'  and estadoPeriodo = 'ACTIVO'");
                $encabezado[$posEnc]["Periodo_idPeriodo"] = $periodo->idPeriodo;

                // consultamos el NIT del Tercero como tipo Tercero Principal en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($encabezado[$posEnc]["documentoTercero"]))
                    $tercero->ConsultarIdTercero("(documentoTercero = '" . $encabezado[$posEnc]["documentoTercero"] .
                            "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["documentoTercero"] . "') and " .
                            "tipoTercero not like '%18%'");
                $encabezado[$posEnc]["Tercero_idTercero"] = $tercero->idTercero;

                // consultamos el Id de la Cuenta Contable


                $cuentacontable->idCuentaContable = 0;
                if (!empty($encabezado[$posEnc]["numeroCuentaContable"]))
                    $cuentacontable->ConsultarCuentaContable("numeroCuentaContable = '" . $encabezado[$posEnc]["numeroCuentaContable"] . "' ");
                $encabezado[$posEnc]["CuentaContable_idCuentaContable"] = $cuentacontable->idCuentaContable;


                $centrocosto->idCentroCosto = 0;
                if (!empty($encabezado[$posEnc]["codigoAlternoCentroCosto"]))
                    $centrocosto->ConsultarCentroCosto("codigoAlternoCentroCosto = '" . $encabezado[$posEnc]["codigoAlternoCentroCosto"] . "' ");
                $encabezado[$posEnc]["CentroCosto_idCentroCosto"] = $centrocosto->idCentroCosto;


                // pasamos a la siguiente fila
                $fila++;
            }

            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos contables
            // para que las valide e importe al sistema, para esto recorremos cada documento importado para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array
            $retorno = $this->llenarPropiedadesSaldoInicialContable($encabezado, $detalle);


            //$this->moverArchivo($ruta,str_replace('nuevos','procesados', $ruta));
            return $retorno;
        }

        function llenarPropiedadesSaldoinicialContable($encabezado) {
            // instanciamos la clase movimiento y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'contabilidad.class.php';
            $contabilidad = new Contabilidad();

            // adicionamos la clase cuenta contable nivel
            require_once 'cuentacontablenivel.class.php';
            $cuentacontablenivel = new CuentaContableNivel();

            // adicionamos la clase cuenta contable
            require_once 'cuentacontable.class.php';
            $cuentacontable = new CuentaContable();

            $retorno = array();

            $nuevoserrores = $this->validarSaldoInicialContable($encabezado);
            $totalerr = count($nuevoserrores);
            //print_r($encabezado);

            if (!isset($nuevoserrores[0]["error"])) {
                // contamos los registros del encabezado
                $totalreg = (isset($encabezado[0]["numeroCuentaContable"]) ? count($encabezado) : 0);


                for ($i = 0; $i < $totalreg; $i++) {

                    // PARA CADA CUENTA QUE TENGAMOS QUE ACTUALIZAR EN EL SALDO CONTABLE,
                    // CONSULTAMOS EN LA TABLA DE NIVELES CONTABLES LOS QUE TENGAN
                    // MENOS O IGUAL CANTIDAD DE DIGITOS QUE LA CUENTA A BONAR,
                    // PORQUE A CADA UNA DE ESAS CUENTAS QUE SE GENERAN EXTRALLENDO
                    // DIGITOS HAY QUE ACTUALIZARLES EL MISMO VALOR

                    $nivel = $cuentacontablenivel->ConsultarVistaCuentaContableNivel("longitudCuentaContableNivel <= " . strlen(trim($encabezado[$i]["numeroCuentaContable"])));

                    //print_r($nivel);
                    $totnivel = isset($nivel[0]["idCuentaContableNivel"]) ? count($nivel) : 0;
                    for ($niv = 0; $niv < $totnivel; $niv++) {
                        // extraemos la cantidad de digitos desde el primero hasta la longitud que indica el nivel
                        $nuevacuenta = substr(trim($encabezado[$i]["numeroCuentaContable"]), 0, (int) $nivel[$niv]["longitudCuentaContableNivel"]);

                        // consultamos el numero de cuenta resultante en el PUC
                        $cuenta = $cuentacontable->ConsultarVistaCuentaContable("numeroCuentaContable = '$nuevacuenta'");

                        // si existe la cuenta contable en el puc, la insertamos en el saldo contable
                        if (isset($cuenta[0]["idCuentaContable"])) {
                            //echo 'cuenta '.$cuenta[0]["numeroCuentaContable"]."<br>";
                            // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrays
                            $contabilidad->Contabilidad();

                            $contabilidad->Periodo_idPeriodo = (isset($encabezado[$i]["Periodo_idPeriodo"]) ? $encabezado[$i]["Periodo_idPeriodo"] : 0);
                            $contabilidad->CuentaContable_idCuentaContable = $cuenta[0]["idCuentaContable"];
                            $contabilidad->debitosContabilidad = (isset($encabezado[$i]["debitosContabilidad"]) ? $encabezado[$i]["debitosContabilidad"] : 0);
                            $contabilidad->creditosContabilidad = (isset($encabezado[$i]["creditosContabilidad"]) ? $encabezado[$i]["creditosContabilidad"] : 0);

                            // verificamos si la cuenta contable maneja centro de costos para saber si le llenamos el campo
                            if ($cuenta[0]["manejaCentroCostoCuentaContable"] == 1)
                                $contabilidad->CentroCosto_idCentroCosto = (isset($encabezado[$i]["CentroCosto_idCentroCosto"]) ? $encabezado[$i]["CentroCosto_idCentroCosto"] : 0);
                            else
                                $contabilidad->CentroCosto_idCentroCosto = 0;

                            //luego verificamos si maneja Tercero para llenar el campo
                            if ($cuenta[0]["manejaTerceroCuentaContable"] == 1)
                                $contabilidad->Tercero_idTercero = (isset($encabezado[$i]["Tercero_idTercero"]) ? $encabezado[$i]["Tercero_idTercero"] : 0);
                            else
                                $contabilidad->Tercero_idTercero = 0;

                            // cada que llenamos un documento, lo cargamos a la base de datos
                            $contabilidad->AdicionarContabilidad();
                            $contabilidad->RecalcularContabilidadPeriodos($contabilidad->Periodo_idPeriodo, $contabilidad->CentroCosto_idCentroCosto, $contabilidad->CuentaContable_idCuentaContable, $contabilidad->Tercero_idTercero, 0);
                        }
                    }
                }
            }
            else {
                $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
            }
            //print_r($retorno);
            return $retorno;
        }

        function validarSaldoInicialContable($encabezado) {
            require_once('../clases/cuentacontable.class.php');
            $cuentacontable = new CuentaContable();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();

            $swerror = true;
            $errores = array();
            $linea = 0;
            $totalreg = (isset($encabezado[0]["numeroCuentaContable"]) ? count($encabezado) : 0);
            for ($x = 0; $x < $totalreg; $x++) {


                // Verificamos que el periodo exista
                if (isset($encabezado[$x]["Periodo_idPeriodo"]) and ( $encabezado[$x]["Periodo_idPeriodo"] == 0 or $encabezado[$x]["Periodo_idPeriodo"] == '')) {
                    $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                    $errores[$linea]["error"] = 'La Fecha de elaboracion (' . $encabezado[$x]["fechaElaboracionMovimientoContable"] .
                            ') no pertenece a un periodo ACTIVO o el periodo no se ha creado';
                    $swerror = false;
                    $linea++;
                }

                // Verificamos que la cuenta contable exista
                if (isset($encabezado[$x]["CuentaContable_idCuentaContable"]) and ( $encabezado[$x]["CuentaContable_idCuentaContable"] == 0 or $encabezado[$x]["CuentaContable_idCuentaContable"] == '')) {
                    $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                    $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $encabezado[$x]["numeroCuentaContable"] . ') no existe';
                    $swerror = false;
                    $linea++;
                } else {
                    // consultamos el ID de la cuenta contable
                    $cuenta = $cuentacontable->ConsultarVistaCuentaContable("idCuentaContable = " . $encabezado[$x]["CuentaContable_idCuentaContable"]);

                    if (!isset($cuenta[0]["idCuentaContable"])) {
                        $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                        $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $encabezado[$x]["numeroCuentaContable"] . ') no se encontro en la Base de datos';
                        $swerror = false;
                        $linea++;
                    } else {

                        // si el tercero no esta en blanco, verificamos que la cuenta si maneje tercero
                        if (isset($encabezado[$x]["Tercero_idTercero"]) and ( $encabezado[$x]["Tercero_idTercero"] != 0 or $encabezado[$x]["Tercero_idTercero"] != '') and
                                $cuenta[0]["manejaTerceroCuentaContable"] == 0) {
                            $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                            $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $encabezado[$x]["numeroCuentaContable"] . ') no maneja tercero';
                            $swerror = false;
                            $linea++;
                        } else {
                            if ((!isset($encabezado[$x]["Tercero_idTercero"]) or ( $encabezado[$x]["Tercero_idTercero"] == 0 or $encabezado[$x]["Tercero_idTercero"] == '')) and
                                    $cuenta[0]["manejaTerceroCuentaContable"] == 1) {
                                $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                                $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $encabezado[$x]["numeroCuentaContable"] . ') maneja tercero, y este no existe (' . $encabezado[$x]["documentoTercero"] . ')';
                                $swerror = false;
                                $linea++;
                            }
                        }

                        // si esta lleno el id del tercero lo validamos
                        if (isset($encabezado[$x]["Tercero_idTercero"]) and ( $encabezado[$x]["Tercero_idTercero"] != 0 or $encabezado[$x]["Tercero_idTercero"] != '')) {
                            // buscamos los datos del tercero del documento
                            $datos = $tercero->ConsultarVistaTercero("idTercero = " . $encabezado[$x]["Tercero_idTercero"]);
                            // contamos los registros obtenidos
                            $totalTerceros = isset($datos[0]["idTercero"]) ? count($datos) : 0;
                            $documento = '';

                            // si no encontramos el tercero, mostramos el error
                            if ($totalTerceros == 0) {

                                $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                                $errores[$linea]["error"] = "El Tercero (" . $encabezado[$x]["documentoTercero"] . ") de la cuenta contable (" . $encabezado[$x]["numeroCuentaContable"] . ")  No existe";
                                $swerror = false;
                                $linea++;
                            } else {
                                $documento = $datos[0]["documentoTercero"];
                            }

                            // con el numero de documento del tercero del documento, buscamos en la tabla de terceros
                            // el id del tercero del NIT PRINCIPAL ya que el tercero elegido en el documento actual puede ser una sucursal
                            // y ese id no se debe llevar a la contabilidad, debe ser el id del tercero principal
                            $datos2 = $tercero->ConsultarVistaTercero("documentoTercero = " . $documento .
                                    " and ((tipoTercero like '%*01*%' or tipoTercero like '%*02*%') and tipoTercero not like '%*18*%')");


                            // contamos los registros obtenidos
                            $totalTerceros2 = isset($datos2[0]["idTercero"]) ? count($datos2) : 0;
                            // si encontramos que hay varios terceros con el mismo nit y que no son de tipo sucursal (o sea que son el principal
                            // advertimos al usuario para que corrija el maestreo de terceros antes de hacer el documento actual
                            if ($totalTerceros2 > 1) {
                                $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                                $errores[$linea]["error"] = "El Tercero (" . $encabezado[$x]["documentoTercero"] . ") de la cuenta contable (" . $encabezado[$x]["numeroCuentaContable"] . ") esta asociado a varios Nit Principales, por favor corrija esto en el Archivo de Terceros antes de elaborar el documento";
                                $swerror = false;
                                $linea++;
                            } else {
                                // si no se encontro el nit principal, mostramos alerta
                                if ($totalTerceros2 == 0) {
                                    $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                                    $errores[$linea]["error"] = "El Tercero (" . $encabezado[$x]["documentoTercero"] . ") de la cuenta contable (" . $encabezado[$x]["numeroCuentaContable"] . ") No esta asociado a ningun Nit Principal, por favor corrija esto en el Archivo de Terceros antes de elaborar el documento";
                                    $swerror = false;
                                    $linea++;
                                }
                            }
                        }


                        // si el centro de costos  no esta en blanco, verificamos que la cuenta si maneje  centro de costos
                        if (isset($encabezado[$x]["CentroCosto_idCentroCosto"]) and ( $encabezado[$x]["CentroCosto_idCentroCosto"] != 0 or $encabezado[$x]["CentroCosto_idCentroCosto"] != '') and
                                $cuenta[0]["manejaCentroCostoCuentaContable"] == 0) {
                            $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                            $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $encabezado[$x]["numeroCuentaContable"] . ') no maneja Centro de Costos';
                            $swerror = false;
                            $linea++;
                        } else {
                            if ((!isset($encabezado[$x]["CentroCosto_idCentroCosto"]) or ( $encabezado[$x]["CentroCosto_idCentroCosto"] == 0 or $encabezado[$x]["CentroCosto_idCentroCosto"] == '')) and
                                    $cuenta[0]["manejaCentroCostoCuentaContable"] == 1) {
                                $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                                $errores[$linea]["error"] = 'El número de Cuenta Contable (' . $encabezado[$x]["numeroCuentaContable"] . ') maneja Centro de Costos, y este no existe';
                                $swerror = false;
                                $linea++;
                            }
                        }
                    }
                }



                // verificamos que los debitos no sean cero
                if (isset($encabezado[$x]["debitosMovimientoContableDetalle"]) and ( $encabezado[$x]["debitosMovimientoContableDetalle"] == 0 or $encabezado[$x]["debitosMovimientoContableDetalle"] == '') or
                        isset($encabezado[$x]["creditosMovimientoContableDetalle"]) and ( $encabezado[$x]["creditosMovimientoContableDetalle"] == 0 or $encabezado[$x]["creditosMovimientoContableDetalle"] == '')) {
                    $errores[$linea]["numeroCuentaContable"] = $encabezado[$x]["numeroCuentaContable"];
                    $errores[$linea]["error"] = 'La cuenta contable  (' . $encabezado[$x]["numeroCuentaContable"] . ' no puede tener debitos y creditos en cero ';
                    $swerror = false;
                    $linea++;
                }
            }
            return $errores;
        }

        function ImportarMovimientoAdministrativoExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once('documentocomercial.class.php');
            $documentocomercial = new Documento();
            require_once('documentoconcepto.class.php');
            $documentoconcepto = new DocumentoConcepto();
            require_once('movimiento.class.php');
            $movimiento = new Movimiento();
            require_once('tercero.class.php');
            $tercero = new Tercero();
            require_once('moneda.class.php');
            $moneda = new Moneda();
            require_once('periodo.class.php');
            $periodo = new Periodo();
            require_once('producto.class.php');
            $producto = new Producto();
            require_once('cartera.class.php');
            $cartera = new Cartera();

            require_once('mediopago.class.php');
            $mediodepago = new MedioPago();

            //                require_once 'movimientoadministrativo.class.php';
            //		$movimientoadm = new MovimientoAdministrativo();
            //Se llama la clase PHPExcel
            include('PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            //                var_dump($documentocomercial->ConsultarVistaDocumento());
            //                return;
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }

            $objReader->setLoadSheetsOnly('Movimiento Administrativo');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del encabezado, estos se incrementan cada que se encuentra un BGM
            $encabezado = array();
            $posEnc = -1;

            // creamos un array para almacenar los campos del detalle, estos se incrementan cada que se encuentra un LIN
            $detalle = array();
            $posDet = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != NULL) {
                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue();

                // por cada numero de documento diferente, llenamos el encabezado
                $posEnc++;

                // para cada registro del encabezado recorremos las columnas desde la 0 hasta la 5
                for ($columna = 0; $columna <= 6; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // luego recorremos las columnas desde la 23 hasta la 23 para obetener los datos de pie de pagina
                for ($columna = 25; $columna <= 25; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posEnc][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                // cada que llenemos un encabezado, hacemos las verificaciones de codigos necesarios
                // validamos el documento
                //                        echo '<pre>';
                //                        print_r($encabezado);
                //                        echo '</pre>';
                //                        return;
                //
                //                        echo "codigoAlternoDocumento =  '" . $encabezado[$posEnc]["codigoDocumento"] . "'";



                $documentocomercial->idDocumento = 0;
                if (!empty($encabezado[$posEnc]["codigoDocumento"]))
                    $documentocomercial->ConsultarDocumento("codigoAlternoDocumento =  '" . $encabezado[$posEnc]["codigoDocumento"] . "'");
                $encabezado[$posEnc]["Documento_idDocumento"] = $documentocomercial->idDocumento;


                // validamos el periodo
                $periodo->idPeriodo = 0;
                if (!empty($encabezado[$posEnc]["fechaElaboracionMovimientoAdministrativo"]))
                    $periodo->ConsultarPeriodo("fechaInicialPeriodo <=  '" . $encabezado[$posEnc]["fechaElaboracionMovimientoAdministrativo"] .
                            "' and fechaFinalPeriodo >=  '" . $encabezado[$posEnc]["fechaElaboracionMovimientoAdministrativo"] .
                            "'  and estadoPeriodo = 'ACTIVO' and estadoCarteraPeriodo = 'ACTIVO'");
                $encabezado[$posEnc]["Periodo_idPeriodo"] = $periodo->idPeriodo;



                // consultamos el EAN del Cliente en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($encabezado[$posEnc]["eanTercero"]))
                    $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $encabezado[$posEnc]["eanTercero"] . "' or codigoAlterno1Tercero = '" . $encabezado[$posEnc]["eanTercero"] . "'");
                $encabezado[$posEnc]["Tercero_idTercero"] = $tercero->idTercero;



                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue() != NULL and
                $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(3, $fila)->getValue()) {
                    // por cada numero de documento, llenamos el detelle
                    $posDet++;


                    // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                    $detalle[$posDet]["numeroMovimientoAdministrativo"] = $numeroAnt;

                    // para cada registro del detalle recorremos las columnas desde la 6 hasta la 22
                    for ($columna = 7; $columna <= 24; $columna++) {
                        // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    }

                    // validamos el documento del mvoimiento a afectar
                    $documentocomercial->idDocumento = 0;
                    if (!empty($detalle[$posDet]["codigoDocumentoAfectado"]))
                        $documentocomercial->ConsultarDocumento("codigoAlternoDocumento =  '" . $detalle[$posDet]["codigoDocumentoAfectado"] . "'");
                    $detalle[$posDet]["Documento_idDocumento"] = $documentocomercial->idDocumento;


                    // llenamos el id del documento comercial a afectar, este se busca con el Documento + concepto + numero
                    if (!empty($detalle[$posDet]["codigoDocumentoAfectado"]) and ! empty($detalle[$posDet]["codigoDocumentoConceptoAfectado"]) and ! empty($detalle[$posDet]["numeroMovimiento"]))
                        $datos = $movimiento->ConsultarVistaMovimientoEncabezado("codigoAlternoDocumento =  '" . $detalle[$posDet]["codigoDocumentoAfectado"] .
                                "' and codigoAlternoDocumentoConcepto =  '" . $detalle[$posDet]["codigoDocumentoConceptoAfectado"] .
                                "' and numeroMovimiento =  '" . $detalle[$posDet]["numeroMovimiento"] . "'");
                    // llenamos los datos del documento comercial
                    $detalle[$posDet]["Movimiento_idMovimiento"] = isset($datos[0]["idMovimiento"]) ? $datos[0]["idMovimiento"] : 0;

                    // con el id del movimiento, lo consultamos en la cartera para traer el saldo actual
                    if (!empty($detalle[$posDet]["Movimiento_idMovimiento"]))
                        $datos = $cartera->ConsultarVistaCartera("Movimiento_idMovimiento = " . $detalle[$posDet]["Movimiento_idMovimiento"]);

                    // llenamos los datos del documento comercial
                    $detalle[$posDet]["valorSaldoMovimientoAdministrativoDetalle"] = isset($datos[0]["saldoCartera"]) ? $datos[0]["saldoCartera"] : 0;


                    $detalle[$posDet]["tasaCambioOrigenMovimientoAdministrativoDetalle"] = isset($datos[0]["idMovimiento"]) ? $datos[0]["tasaCambioMovimiento"] : 0;
                    $detalle[$posDet]["valorDocumentoMovimientoAdministrativoDetalle"] = isset($datos[0]["idMovimiento"]) ? $datos[0]["valorTotalMovimiento"] : 0;
                    $detalle[$posDet]["valorRetencionMovimiento"] = isset($datos[0]["idMovimiento"]) ? $datos[0]["valorRetencionMovimiento"] : 0;
                    $detalle[$posDet]["valorReteIvaMovimiento"] = isset($datos[0]["idMovimiento"]) ? $datos[0]["valorReteIvaMovimiento"] : 0;
                    $detalle[$posDet]["valorReteIcaMovimiento"] = isset($datos[0]["idMovimiento"]) ? $datos[0]["valorReteIcaMovimiento"] : 0;

                    // si los datos de retenciones del archivo de excel vienen en ceros, los llenamos con los datos originales de la factura
                    // para que no se generen diferencias en retenciones
                    $detalle[$posDet]["valorReteFuenteMovimientoAdministrativoDetalle"] = ($detalle[$posDet]["valorReteFuenteMovimientoAdministrativoDetalle"] == 0 and isset($datos[0]["idMovimiento"])) ? $datos[0]["valorRetencionMovimiento"] : $detalle[$posDet]["valorReteFuenteMovimientoAdministrativoDetalle"];
                    $detalle[$posDet]["valorReteIvaMovimientoAdministrativoDetalle"] = ($detalle[$posDet]["valorReteIvaMovimientoAdministrativoDetalle"] == 0 and isset($datos[0]["idMovimiento"])) ? $datos[0]["valorReteIvaMovimiento"] : $detalle[$posDet]["valorReteIvaMovimientoAdministrativoDetalle"];
                    $detalle[$posDet]["valorReteIcaMovimientoAdministrativoDetalle"] = ($detalle[$posDet]["valorReteIcaMovimientoAdministrativoDetalle"] == 0 and isset($datos[0]["idMovimiento"])) ? $datos[0]["valorReteIcaMovimiento"] : $detalle[$posDet]["valorReteIcaMovimientoAdministrativoDetalle"];

                    // calculamos las casillas de diferencia en retenciones
                    $detalle[$posDet]["diferenciaReteFuenteMovimientoAdministrativoDetalle"] = $detalle[$posDet]["valorReteFuenteMovimientoAdministrativoDetalle"] - $detalle[$posDet]["valorRetencionMovimiento"];
                    $detalle[$posDet]["diferenciaReteIvaMovimientoAdministrativoDetalle"] = $detalle[$posDet]["valorReteIvaMovimientoAdministrativoDetalle"] - $detalle[$posDet]["valorReteIvaMovimiento"];
                    $detalle[$posDet]["diferenciaReteIcaMovimientoAdministrativoDetalle"] = $detalle[$posDet]["valorReteIcaMovimientoAdministrativoDetalle"] - $detalle[$posDet]["valorReteIcaMovimiento"];


                    // validamos el codigo de concepto (producto)
                    $producto->idProducto = 0;
                    if (!empty($detalle[$posDet]["referenciaProducto"]))
                        $producto->ConsultarProducto("codigoBarrasProducto = '" . $detalle[$posDet]["referenciaProducto"] . "' or referenciaProducto = '" . $detalle[$posDet]["referenciaProducto"] . "'");
                    $detalle[$posDet]["Producto_idProducto"] = $producto->idProducto;


                    // consultamos el NIT del Banco como tipo Tercero Principal en la tabla de terceros para obtener el ID
                    $tercero->idTercero = 0;
                    if (!empty($detalle[$posDet]["codigoBanco"]))
                        $tercero->ConsultarIdTercero("codigoBarrasTercero = '" . $detalle[$posDet]["codigoBanco"] . "' or codigoAlterno1Tercero = '" . $detalle[$posDet]["codigoBanco"] . "'");
                    $detalle[$posDet]["Tercero_idBanco"] = $tercero->idTercero;

                    // consultamos la cuenta bancaria
                    if (!empty($detalle[$posDet]["codigoBanco"]) and ! empty($detalle[$posDet]["NumeroCuentaBanco"]))
                        $datos = $tercero->ConsultarVistaTerceroBanco("Tercero_idBanco =  '" . $detalle[$posDet]["Tercero_idBanco"] .
                                "' and numeroCuentaTerceroBanco =  '" . $detalle[$posDet]["NumeroCuentaBanco"] . "'");
                    // llenamos los datos del documento comercial
                    $detalle[$posDet]["TerceroBanco_idTerceroBanco"] = isset($datos[0]["Tercero_idBanco"]) ? $datos[0]["idTerceroBanco"] : 0;


                    // validamos el codigo de concepto de ajuste (producto)
                    $producto->idProducto = 0;
                    if (!empty($detalle[$posDet]["referenciaConceptoAjuste"]))
                        $producto->ConsultarProducto("codigoBarrasProducto = '" . $detalle[$posDet]["referenciaConceptoAjuste"] . "' or referenciaProducto = '" . $detalle[$posDet]["referenciaConceptoAjuste"] . "'");
                    $detalle[$posDet]["Producto_idConceptoAjuste"] = $producto->idProducto;

                    // pasamos a la siguiente fila
                    $fila++;
                }
            }

            $mediopago = array();
            $objReader->setLoadSheetsOnly('Medio Pago');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);


            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.

            $posMed = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue() != NULL) {

                $posMed++;

                // para cada registro de medios de pago recorremos las columnas desde la 0 hasta la 6
                for ($columna = 0; $columna <= 6; $columna++) {
                    // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $mediopago[$posMed][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }


                $mediodepago->idMedioPago = 0;
                if (!empty($mediopago[$posMed]["codigoAlternoMedioPago"]))
                    $mediodepago->ConsultarIdMedioPago("codigoAlternoMedioPago = '" . $mediopago[$posMed]["codigoAlternoMedioPago"] . "'");
                $mediopago[$posMed]["MedioPago_idMedioPago"] = $mediodepago->idMedioPago;

                $tercero->idTercero = 0;
                if (!empty($mediopago[$posMed]["codigoAlterno1Banco"]))
                    $tercero->ConsultarIdTercero("codigoAlterno1Banco = '" . $mediopago[$posMed]["codigoAlterno1Banco"] . "'");
                $mediopago[$posMed]["Tercero_idBanco"] = $tercero->idTercero;
                $fila++;
            }


            //		print_r($encabezado);
            //		print_r($detalle);
            //		print_r($mediopago);
            //
            //                return;
            //echo "<br><br><br><br>";
            //print_r($detalle);
            // luego de que tenemos la matriz de encabezado y detalle lenos, las enviamos al proceso de importacion de movimientos contables
            // para que las valide e importe al sistema, para esto recorremos cada documento importado para llenar el encabezado en variables
            // normales y el detalle correspondiente en un array
            $retorno = $this->llenarPropiedadesMovimientoAdministrativo($encabezado, $detalle, $mediopago);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($encabezado);
            unset($detalle);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));
            return $retorno;
        }

        function llenarPropiedadesMovimientoAdministrativo($encabezado, $detalle, $mediopago) {
            // instanciamos la clase movimiento y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'movimientoadministrativo.class.php';
            $movimiento = new MovimientoAdministrativo();
            //
            require_once 'periodo.class.php';
            $periodo = new Periodo();
            //print_r($detalle);
            $retorno = array();
            // contamos los registros del encabezado
            $totalreg = (isset($encabezado[0]["numeroMovimientoAdministrativo"]) ? count($encabezado) : 0);

            //                echo '<pre>';
            //                echo '<pre>'.var_dump($encabezado).'</pre>';
            //                echo '</pre>';
            //                var_dump($detalle);

            for ($i = 0; $i < $totalreg; $i++) {
                //                    echo 'ENTRA FOR ENCABEZADO';

                $nuevoserrores = $this->validarMovimientoAdministrativo($encabezado[$i]["numeroMovimientoAdministrativo"], $encabezado, $detalle, $mediopago);

                if (!isset($nuevoserrores[0]["error"])) {

                    //                            echo 'ENTRA SIN ERRORES';
                    // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
                    $movimiento->MovimientoAdministrativo();
                    //                                $movimiento->idMovimientoAdministrativo = 0;
                    $movimiento->Documento_idDocumento = (isset($encabezado[$i]["Documento_idDocumento"]) ? $encabezado[$i]["Documento_idDocumento"] : 0);

                    $movimiento->fechaElaboracionMovimientoAdministrativo = (isset($encabezado[$i]["fechaElaboracionMovimientoAdministrativo"]) ? $encabezado[$i]["fechaElaboracionMovimientoAdministrativo"] : date("Y-m-d"));

                    // obtenemos el período contable segun la fecha de elaboracion del documento
                    $datoper = $periodo->ConsultarVistaPeriodo("fechaInicialPeriodo <= '" . $movimiento->fechaElaboracionMovimientoAdministrativo .
                            "' and fechaFinalPeriodo >= '" . $movimiento->fechaElaboracionMovimientoAdministrativo . "'");
                    $movimiento->Periodo_idPeriodo = (isset($datoper[0]["idPeriodo"]) ? $datoper[0]["idPeriodo"] : 0);

                    $movimiento->prefijoMovimientoAdministrativo = (isset($encabezado[$i]["prefijoMovimientoAdministrativo"]) ? $encabezado[$i]["prefijoMovimientoAdministrativo"] : '');
                    $movimiento->numeroMovimientoAdministrativo = (isset($encabezado[$i]["numeroMovimientoAdministrativo"]) ? $encabezado[$i]["numeroMovimientoAdministrativo"] : '');
                    $movimiento->sufijoMovimientoAdministrativo = (isset($encabezado[$i]["sufijoMovimientoAdministrativo"]) ? $encabezado[$i]["sufijoMovimientoAdministrativo"] : '');
                    $movimiento->Tercero_idTercero = (isset($encabezado[$i]["Tercero_idTercero"]) ? $encabezado[$i]["Tercero_idTercero"] : '');
                    $movimiento->observacionMovimientoAdministrativo = (isset($encabezado[$i]["observacionMovimientoAdministrativo"]) ? $encabezado[$i]["observacionMovimientoAdministrativo"] : '');

                    $movimiento->totalDebitosMovimientoAdministrativo = 0;
                    $movimiento->totalCreditosMovimientoAdministrativo = 0;

                    $movimiento->subtotalMovimientoAdministrativo = 0;
                    $movimiento->valorDescuentoMovimientoAdministrativo = 0;
                    $movimiento->valorRetencionMovimientoAdministrativo = 0;
                    $movimiento->valorReteIvaMovimientoAdministrativo = 0;
                    $movimiento->valorReteOtrosMovimientoAdministrativo = 0;
                    $movimiento->valorTotalMovimientoAdministrativo = 0;

                    $movimiento->estadoMovimientoAdministrativo = 'ACTIVO';


                    // por cada registro del encabezado, recorremos el detalle para obtener solo los datos del mismo numero de movimiento del encabezado, con estos
                    // llenamos arrays por cada campo
                    $totaldet = (isset($detalle[0]["numeroMovimientoAdministrativo"]) ? count($detalle) : 0);

                    //                                echo 'DETALLE EXCEL '.$totaldet;
                    // llevamos un contador de registros por cada producto del detalle
                    $registroact = 0;
                    for ($j = 0; $j < $totaldet; $j++) {

                        //                                    echo 'ENTRA DETALLE';

                        if (isset($encabezado[$i]["numeroMovimientoAdministrativo"]) and isset($detalle[$j]["numeroMovimientoAdministrativo"]) and $encabezado[$i]["numeroMovimientoAdministrativo"] == $detalle[$j]["numeroMovimientoAdministrativo"]) {

                            //                                            echo 'ENTRA CONDICION';


                            $movimiento->idMovimientoAdministrativoDetalle[$registroact] = 0;
                            $movimiento->Movimiento_idMovimiento[$registroact] = (isset($detalle[$j]["Movimiento_idMovimiento"]) ? $detalle[$j]["Movimiento_idMovimiento"] : 0);
                            $movimiento->Producto_idProducto[$registroact] = (isset($detalle[$j]["Producto_idProducto"]) ? $detalle[$j]["Producto_idProducto"] : 0);
                            $movimiento->Tercero_idBanco[$registroact] = (isset($detalle[$j]["Tercero_idBanco"]) ? $detalle[$j]["Tercero_idBanco"] : 0);
                            $movimiento->TerceroBanco_idTerceroBanco[$registroact] = (isset($detalle[$j]["TerceroBanco_idTerceroBanco"]) ? $detalle[$j]["TerceroBanco_idTerceroBanco"] : 0);
                            $movimiento->tasaCambioOrigenMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["tasaCambioOrigenMovimientoAdministrativoDetalle"]) ? $detalle[$j]["tasaCambioOrigenMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorDocumentoMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorDocumentoMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorDocumentoMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorSaldoMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorSaldoMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorSaldoMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorAplicadoMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorAplicadoMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorAplicadoMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->porcentajeDescuentoMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["porcentajeDescuentoMovimientoAdministrativoDetalle"]) ? $detalle[$j]["porcentajeDescuentoMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorDescuentoMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorDescuentoMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorDescuentoMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorDescuentoLey33MovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorDescuentoLey33MovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorDescuentoLey33MovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorBaseMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorBaseMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorBaseMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorReteFuenteMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorReteFuenteMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorReteFuenteMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorReteIvaMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorReteIvaMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorReteIvaMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorReteIcaMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorReteIcaMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorReteIcaMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorReteOtrosMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorReteOtrosMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorReteOtrosMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorTotalMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorTotalMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorTotalMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->observacionMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["observacionMovimientoAdministrativoDetalle"]) ? $detalle[$j]["observacionMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->tasaCambioPagoMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["tasaCambioPagoMovimientoAdministrativoDetalle"]) ? $detalle[$j]["tasaCambioPagoMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorAjusteMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorAjusteMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorAjusteMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->Producto_idConceptoAjuste[$registroact] = (isset($detalle[$j]["Producto_idConceptoAjuste"]) ? $detalle[$j]["Producto_idConceptoAjuste"] : 0);
                            $movimiento->CentroCosto_idCentroCostoDetalle[$registroact] = 0;
                            //Campos faltantes
                            $movimiento->valorReteCreeMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorReteCreeMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorReteCreeMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->diferenciaReteCreeMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["diferenciaReteCreeMovimientoAdministrativoDetalle"]) ? $detalle[$j]["diferenciaReteCreeMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->porcentajeReteCreeMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["porcentajeReteCreeMovimientoAdministrativoDetalle"]) ? $detalle[$j]["porcentajeReteCreeMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->porcentajeReteIcaMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["porcentajeReteIcaMovimientoAdministrativoDetalle"]) ? $detalle[$j]["porcentajeReteIcaMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->porcentajeReteIvaMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["porcentajeReteIvaMovimientoAdministrativoDetalle"]) ? $detalle[$j]["porcentajeReteIvaMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->porcentajeReteFuenteMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["porcentajeReteFuenteMovimientoAdministrativoDetalle"]) ? $detalle[$j]["porcentajeReteFuenteMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->porcentajeDescuentoLey33MovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["porcentajeDescuentoLey33MovimientoAdministrativoDetalle"]) ? $detalle[$j]["porcentajeDescuentoLey33MovimientoAdministrativoDetalle"] : 0);
                            $movimiento->valorDescuentoMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["valorDescuentoMovimientoAdministrativoDetalle"]) ? $detalle[$j]["valorDescuentoMovimientoAdministrativoDetalle"] : 0);
                            $movimiento->diferenciaDescuentoMovimientoAdministrativoDetalle[$registroact] = (isset($detalle[$j]["diferenciaDescuentoMovimientoAdministrativoDetalle"]) ? $detalle[$j]["diferenciaDescuentoMovimientoAdministrativoDetalle"] : 0);


                            // calculamos las diferencias entre las retenciones causadas en la factura y las pagadas en la cartera
                            $movimiento->diferenciaReteFuenteMovimientoAdministrativoDetalle[$registroact] = $movimiento->valorReteFuenteMovimientoAdministrativoDetalle[$registroact] - $detalle[$j]["valorRetencionMovimiento"];
                            $movimiento->diferenciaReteIvaMovimientoAdministrativoDetalle[$registroact] = $movimiento->valorReteIvaMovimientoAdministrativoDetalle[$registroact] - $detalle[$j]["valorReteIvaMovimiento"];
                            $movimiento->diferenciaReteIcaMovimientoAdministrativoDetalle[$registroact] = $movimiento->valorReteIcaMovimientoAdministrativoDetalle[$registroact] - $detalle[$j]["valorReteIcaMovimiento"];

                            // luego de calcular las diferencias, calculamos el descuento, solo si el % es mayor a cero para respetar cuando se ponga descuento en valor
                            if ($movimiento->porcentajeDescuentoMovimientoAdministrativoDetalle[$registroact] > 0) {
                                $movimiento->valorDescuentoMovimientoAdministrativoDetalle[$registroact] = $movimiento->valorSaldoMovimientoAdministrativoDetalle[$registroact] *
                                        ($movimiento->porcentajeDescuentoMovimientoAdministrativoDetalle[$registroact] / 100);
                            }

                            // con las retenciones y el descuento calculamos los totales del pago
                            $movimiento->valorTotalMovimientoAdministrativoDetalle[$registroact] = $movimiento->valorAplicadoMovimientoAdministrativoDetalle[$registroact] -
                                    $movimiento->valorDescuentoMovimientoAdministrativoDetalle[$registroact] -
                                    $movimiento->valorDescuentoLey33MovimientoAdministrativoDetalle[$registroact] -
                                    abs($movimiento->diferenciaReteFuenteMovimientoAdministrativoDetalle[$registroact]) -
                                    abs($movimiento->diferenciaReteIvaMovimientoAdministrativoDetalle[$registroact]) -
                                    abs($movimiento->diferenciaReteIcaMovimientoAdministrativoDetalle[$registroact]) -
                                    $movimiento->valorReteOtrosMovimientoAdministrativoDetalle[$registroact] +
                                    $movimiento->valorAjusteMovimientoAdministrativoDetalle[$registroact];

                            // calculamos la diferencia en cambio cuando se paga en otra moneda
                            $movimiento->diferenciaCambioMovimientoAdministrativoDetalle[$registroact] = $movimiento->tasaCambioOrigenMovimientoAdministrativoDetalle[$registroact] -
                                    $movimiento->tasaCambioPagoMovimientoAdministrativoDetalle[$registroact];


                            // totalizamos los campos del detalle
                            $movimiento->subtotalMovimientoAdministrativo += $movimiento->valorAplicadoMovimientoAdministrativoDetalle[$registroact];
                            $movimiento->valorDescuentoMovimientoAdministrativo += $movimiento->valorDescuentoMovimientoAdministrativoDetalle[$registroact];
                            $movimiento->valorRetencionMovimientoAdministrativo += $movimiento->valorReteFuenteMovimientoAdministrativoDetalle[$registroact];
                            $movimiento->valorReteIvaMovimientoAdministrativo += $movimiento->valorReteIvaMovimientoAdministrativoDetalle[$registroact];
                            $movimiento->valorReteOtrosMovimientoAdministrativo += $movimiento->valorReteOtrosMovimientoAdministrativoDetalle[$registroact];
                            $movimiento->valorTotalMovimientoAdministrativo += $movimiento->valorTotalMovimientoAdministrativoDetalle[$registroact];

                            $registroact++;
                        }



                        //                                        echo 'REGISTRO '.$registroact;
                        //                                        print_r($detalle);

                        $totalmed = (isset($mediopago[0]["numeroMovimientoAdministrativo"]) ? count($mediopago) : 0);
                        $regmediopago = 0;
                        $valorRecibido = 0;

                        //                                        var_dump($mediopago);

                        for ($m = 0; $m < $totalmed; $m++) {
                            //                            echo "<br> entra for Mediopago <br>";
                            if (isset($encabezado[$j]["numeroMovimientoAdministrativo"]) and
                                    isset($mediopago[$m]["numeroMovimientoAdministrativo"]) and
                                    $encabezado[$j]["numeroMovimientoAdministrativo"] == $mediopago[$m]["numeroMovimientoAdministrativo"]) {

                                $movimiento->idMovimientoAdministrativoMedioPago[$regmediopago] = 0;
                                $movimiento->MedioPago_idMedioPago[$regmediopago] = (isset($mediopago[$m]["MedioPago_idMedioPago"]) ? $mediopago[$m]["MedioPago_idMedioPago"] : 0);
                                $movimiento->valorMovimientoAdministrativoMedioPago[$regmediopago] = (isset($mediopago[$m]["valorMovimientoAdministrativoMedioPago"]) ? $mediopago[$m]["valorMovimientoAdministrativoMedioPago"] : 0);
                                $movimiento->MovimientoAdministrativo_idMovimientoAdministrativo[$regmediopago] = (isset($mediopago[$m]["MovimientoAdministrativo_idMovimientoAdministrativo"]) ? $mediopago[$m]["MovimientoAdministrativo_idMovimientoAdministrativo"] : 0);
                                $movimiento->Tercero_idBancoMedioPago[$regmediopago] = (isset($mediopago[$m]["Tercero_idBanco"]) ? $mediopago[$m]["Tercero_idBanco"] : 0);
                                $movimiento->numeroComprobanteMovimientoAdministrativoMedioPago[$regmediopago] = (isset($mediopago[$m]["numeroComprobanteMovimientoMedioPago"]) ? $mediopago[$m]["numeroComprobanteMovimientoMedioPago"] : 0);
                                $valorRecibido += $movimiento->valorMovimientoAdministrativoMedioPago[$regmediopago];
                                $regmediopago++;
                            }
                        }

                        $movimiento->valorRecibidoMovimientoAdministrativo = $valorRecibido;
                    }
                    //cada que llenamos un documento, lo cargamos a la base de datos
                    $movimiento->AdicionarMovimientoAdministrativo();
                } else {
                    $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                }
            }


            //print_r($retorno);
            return $retorno;
        }

        function validarMovimientoAdministrativo($numeroMovimientoAdministrativo = '', $encabezado, $detalle, $mediopago) {
            require_once('../clases/cuentacontable.class.php');
            $cuentacontable = new CuentaContable();

            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();

            $swerror = true;
            $errores = array();
            $linea = 0;
            $totalreg = (isset($encabezado[0]["numeroMovimientoAdministrativo"]) ? count($encabezado) : 0);

            //                var_dump($encabezado);
            //                var_dump($detalle);
            //                var_dump($mediopago);
            //                return;

            for ($x = 0; $x < $totalreg; $x++) {
                //                    echo 'entra';
                // validamos si el tipo de documento no es cero
                if (!isset($encabezado[$x]["Documento_idDocumento"]) or $encabezado[$x]["Documento_idDocumento"] == 0 or $encabezado[$x]["Documento_idDocumento"] == '') {
                    $errores[$linea]["numeroMovimientoAdministrativo"] = $encabezado[$x]["numeroMovimientoAdministrativo"];
                    $errores[$linea]["error"] = 'El Tipo de Documento Administrativo  (' . $encabezado[$x]["codigoDocumento"] . ') no existe';
                    $swerror = false;
                    $linea++;
                }


                // Verificamos que el periodo exista
                if (isset($encabezado[$x]["Periodo_idPeriodo"]) and ( $encabezado[$x]["Periodo_idPeriodo"] == 0 or $encabezado[$x]["Periodo_idPeriodo"] == '')) {
                    $errores[$linea]["numeroMovimientoAdministrativo"] = $encabezado[$x]["numeroMovimientoAdministrativo"];
                    $errores[$linea]["error"] = 'La Fecha de elaboracion (' . $encabezado[$x]["fechaElaboracionMovimientoAdministrativo"] .
                            ') no pertenece a un periodo ACTIVO o el periodo no se ha creado';
                    $swerror = false;
                    $linea++;
                }

                // validamos si el tercero no es cero
                if (!isset($encabezado[$x]["Tercero_idTercero"]) or $encabezado[$x]["Tercero_idTercero"] == 0 or $encabezado[$x]["Tercero_idTercero"] == '') {
                    $errores[$linea]["numeroMovimientoAdministrativo"] = $encabezado[$x]["numeroMovimientoAdministrativo"];
                    $errores[$linea]["error"] = 'El Codigo o EAN del Cliente (' . $encabezado[$x]["eanTercero"] . ') no existe';
                    $swerror = false;
                    $linea++;
                }

                $totaldet = (isset($detalle[0]["numeroMovimientoAdministrativo"]) ? count($detalle) : 0);
                for ($y = 0; $y < $totaldet; $y++) {

                    if (isset($encabezado[$x]["numeroMovimientoAdministrativo"]) and isset($detalle[$y]["numeroMovimientoAdministrativo"]) and $encabezado[$x]["numeroMovimientoAdministrativo"] == $detalle[$y]["numeroMovimientoAdministrativo"]) {

                        // en el detalle debe estar lleno el campo referenciaConcepto (cuando es documento por conceptos) o el numero de movimiento
                        // (cuando es documento para abono de facturas), no pueden estar vacios los 2
                        if ((isset($detalle[$y]["Producto_idProducto"]) and ( $detalle[$y]["Producto_idProducto"] == 0 or $detalle[$y]["Producto_idProducto"] == '')) and ( (isset($detalle[$y]["Documento_idDocumento"]) and ( $detalle[$y]["Documento_idDocumento"] == 0 or $detalle[$y]["Documento_idDocumento"] == '')) or ( isset($detalle[$y]["DocumentoConcepto_idDocumentoConcepto"]) and ( $detalle[$y]["DocumentoConcepto_idDocumentoConcepto"] == 0 or $detalle[$y]["DocumentoConcepto_idDocumentoConcepto"] == '')) or ( isset($detalle[$y]["Movimiento_idMovimiento"]) and ( $detalle[$y]["Movimiento_idMovimiento"] == 0 or $detalle[$y]["Movimiento_idMovimiento"] == '')))) {
                            $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                            $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' debe contener el numero de Documento Afectado o la Referencia del Concepto';
                            $swerror = false;
                            $linea++;
                        } else {
                            // si la referencia del concepto esta en blanco, verificamos si se lleno el documento
                            if (isset($detalle[$y]["Producto_idProducto"]) and ( $detalle[$y]["Producto_idProducto"] == 0 or $detalle[$y]["Producto_idProducto"] == '')) {
                                // si esta en blanco el documento
                                if (isset($detalle[$y]["Documento_idDocumento"]) and ( $detalle[$y]["Documento_idDocumento"] == 0 or $detalle[$y]["Documento_idDocumento"] == '')) {
                                    $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                                    $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' contiene un Documento Comercial(' . $detalle[$y]["codigoDocumentoAfectado"] . ') que no existe';
                                    $swerror = false;
                                    $linea++;
                                }

                                // si esta en blanco el Concepto
                                if (isset($detalle[$y]["DocumentoConcepto_idDocumentoConcepto"]) and ( $detalle[$y]["DocumentoConcepto_idDocumentoConcepto"] == 0 or $detalle[$y]["DocumentoConcepto_idDocumentoConcepto"] == '')) {
                                    $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                                    $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' contiene un Concepto Comercial (' . $detalle[$y]["codigoDocumentoConceptoAfectado"] . ') que no existe';
                                    $swerror = false;
                                    $linea++;
                                }

                                // si esta en blanco el Movimiento comercial
                                if (isset($detalle[$y]["Movimiento_idMovimiento"]) and ( $detalle[$y]["Movimiento_idMovimiento"] == 0 or $detalle[$y]["Movimiento_idMovimiento"] == '')) {
                                    $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                                    $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' contiene un Numero de Documento Comercial (' . $detalle[$y]["numeroMovimiento"] . ') que no existe';
                                    $swerror = false;
                                    $linea++;
                                }
                            }
                        }

                        // verificamos si el documento si tiene saldo en la cartera
                        if (isset($detalle[$y]["valorSaldoMovimientoAdministrativoDetalle"]) and $detalle[$y]["valorSaldoMovimientoAdministrativoDetalle"] == 0) {
                            $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                            $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' contiene un Numero de Documento Comercial (' . $detalle[$y]["numeroMovimiento"] . ') que no tiene Saldo en la Cartera (' . $detalle[$y]["valorSaldoMovimientoAdministrativoDetalle"] . ') ';
                            $swerror = false;
                            $linea++;
                        }

                        // Verificamos que la cuenta bancaria exista
                        if ((count($mediopago) == 0)) {

                            if ((isset($detalle[$y]["Tercero_idBanco"]) and ( $detalle[$y]["Tercero_idBanco"] == 0 or $detalle[$y]["Tercero_idBanco"] == '')) or ( isset($detalle[$y]["TerceroBanco_idTerceroBanco"]) and ( $detalle[$y]["TerceroBanco_idTerceroBanco"] == 0 or $detalle[$y]["TerceroBanco_idTerceroBanco"] == ''))) {
                                $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                                $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' no contiene un Banco (' . $detalle[$y]["codigoBanco"] . '-' . $detalle[$y]["Tercero_idBanco"] . ') y/o Cuenta (' . $detalle[$y]["NumeroCuentaBanco"] . '-' . $detalle[$y]["TerceroBanco_idTerceroBanco"] . ') correctos';
                                $swerror = false;
                                $linea++;
                            }
                        }


                        // Verificamos que si el valor aplicado es mayor a cero
                        if (isset($detalle[$y]["valorAplicadoMovimientoAdministrativoDetalle"]) and ( $detalle[$y]["valorAplicadoMovimientoAdministrativoDetalle"] == 0 or $detalle[$y]["valorAplicadoMovimientoAdministrativoDetalle"] == '')) {
                            $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                            $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' contiene un valor aplicado (' . $detalle[$y]["valorAplicadoMovimientoAdministrativoDetalle"] . ') igual a cero';
                            $swerror = false;
                            $linea++;
                        }

                        // si el valor de ajuste es mayor que cero, entonces el concpeto de ajuste debe existir
                        if ((isset($detalle[$y]["valorAjusteMovimientoAdministrativoDetalle"]) and $detalle[$y]["valorAjusteMovimientoAdministrativoDetalle"] > 0) and ( !isset($detalle[$y]["Producto_idConceptoAjuste"]) or $detalle[$y]["Producto_idConceptoAjuste"] == 0 or $detalle[$y]["Producto_idConceptoAjuste"] == '')) {

                            $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                            $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' contiene un valor de ajuste (' . $detalle[$y]["valorAjusteMovimientoAdministrativoDetalle"] . ') y el concepto del ajuste (' . $detalle[$y]["referenciaConceptoAjuste"] . ') no existe';
                            $swerror = false;
                            $linea++;
                        }

                        // si el valor de ajuste es cero, y el concpeto de ajuste es valido
                        if ((isset($detalle[$y]["valorAjusteMovimientoAdministrativoDetalle"]) and ( $detalle[$y]["valorAjusteMovimientoAdministrativoDetalle"] == 0 or $detalle[$y]["valorAjusteMovimientoAdministrativoDetalle"] == '')) and ( isset($detalle[$y]["Producto_idConceptoAjuste"]) and $detalle[$y]["Producto_idConceptoAjuste"] != 0 )) {

                            $errores[$linea]["numeroMovimientoAdministrativo"] = $detalle[$y]["numeroMovimientoAdministrativo"];
                            $errores[$linea]["error"] = 'El Registro No. ' . ($y + 4) . ' contiene un Concepto de ajuste (' . $detalle[$y]["referenciaConceptoAjuste"] . ') y el Valor del ajuste (' . $detalle[$y]["valorAjusteMovimientoAdministrativoDetalle"] . ') esta en cero';
                            $swerror = false;
                            $linea++;
                        }
                    }
                }
            }

            return $errores;
        }

        function ImportarControlIngresoBiostarExcel($ruta) {
            set_time_limit(0);
            //echo $ruta;
            require_once('../clases/tercero.class.php');
            $tercero = new Tercero();

            //Se llama la clase PHPExcel
            include('../clases/PHPExcel/Classes/PHPExcel.php');
            //$objPHPExcel = new PHPExcel();
            // dependiendo de la extension del archivo, lo leemos como excel 5.0/95 o como excel 97 o 2010
            $rutacompleta = explode(".", $ruta);
            $extension = array_pop($rutacompleta);
            if (!isset($objReader)) {
                if ($extension == 'xlsx')
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                else
                    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            $objReader->setLoadSheetsOnly('Hoja1');
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($ruta);
            $objWorksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g.
            // creamos un array para almacenar los campos del archivo
            $ingresos = array();
            $posIng = -1;


            $fila = 1;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                // por cada numero de documento diferente, llenamos el referencias
                $posIng++;

                $ingresos[$posIng]["fecha"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();
                $ingresos[$posIng]["hora"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(1, $fila)->getValue();
                $ingresos[$posIng]["cedula"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(7, $fila)->getValue();
                $ingresos[$posIng]["nombre"] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(8, $fila)->getValue() . ' ' .
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(9, $fila)->getValue() . ' ' .
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(10, $fila)->getValue() . ' ' .
                        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(11, $fila)->getValue();


                // convertimos la fecha de formato EXCEL a formato UNIX
                $fechaReal = $ingresos[$posIng]["fecha"];
                $ingresos[$posIng]["fecha"] = (gettype($fechaReal) == 'double' or gettype($fechaReal) == 'integer' and $fechaReal > 0) ? date("Y-m-d", strtotime("+1 days", PHPExcel_Shared_Date::ExcelToPHP($fechaReal))) : $ingresos[$posIng]["fecha"];

                // convertimos la hora de formato EXCEL a formato UNIX
                $horaReal = $ingresos[$posIng]["hora"];
                $ingresos[$posIng]["hora"] = (gettype($horaReal) == 'double' or gettype($horaReal) == 'integer' and $horaReal > 0) ? date("G:i", strtotime("+5 hours", PHPExcel_Shared_Date::ExcelToPHP($horaReal))) : $ingresos[$posIng]["hora"];


                // consultamos el EAN del proveedor en la tabla de terceros para obtener el ID
                $tercero->idTercero = 0;
                if (!empty($ingresos[$posIng]["cedula"]))
                    $tercero->ConsultarIdTercero("documentoTercero = '" . $ingresos[$posIng]["cedula"] . "' and tipoTercero like '%*05*%'");
                $ingresos[$posIng]["Tercero_idTercero"] = $tercero->idTercero;




                $fila++;
            }
            //echo $fila.' fila</br>';
            //print_r($ingresos);
            // luego de que tenemos la matriz de referencias llena, las enviamos al proceso de importacion de productos
            // para que los valide e importe al sistema
            $retorno = $this->llenarPropiedadesControlIngreso($ingresos);

            unset($objReader);
            unset($objPHPExcel);
            unset($objWorksheet);
            unset($ingresos);

            $this->moverArchivo($ruta, str_replace('nuevos', 'procesados', $ruta));

            return $retorno;
        }

        function llenarPropiedadesControlIngreso($ingresos) {
            // instanciamos la clase producto y llenamos sus propiedades para que ella se encargue de importar los datos
            require_once 'controlporteria.class.php';
            $controlporteria = new ControlPorteria();

            $retorno = array();
            // contamos los registros del array de productos
            $totalreg = (isset($ingresos[0]["cedula"]) ? count($ingresos) : 0);
            $i = 0;
            while ($i < $totalreg) {
                $cedulaAnterior = $ingresos[$i]["cedula"];
                /* $control = 'INGRESO';
                  $fechaIngreso = $ingresos[$i]["fecha"];
                  $horaIngreso = $ingresos[$i]["hora"]; */

                while ($i < $totalreg and $cedulaAnterior == $ingresos[$i]["cedula"]) {

                    $fechaAnterior = $ingresos[$i]["fecha"];
                    $control = 'INGRESO';
                    $fechaIngreso = $ingresos[$i]["fecha"];
                    $horaIngreso = $ingresos[$i]["hora"];

                    while ($i < $totalreg and $cedulaAnterior == $ingresos[$i]["cedula"] and $fechaAnterior == $ingresos[$i]["fecha"]) {

                        /* $horaAnterior = $ingresos[$i]["hora"];
                          $control = 'INGRESO';
                          $fechaIngreso = $ingresos[$i]["fecha"];
                          $horaIngreso = $ingresos[$i]["hora"]; */

                        //while($i < $totalreg and $cedulaAnterior == $ingresos[$i]["cedula"] and $fechaAnterior == $ingresos[$i]["fecha"] and $horaAnterior == $ingresos[$i]['hora'])
                        //{
                        //echo $fechaAnterior.' fecha Anterior</br>';
                        //echo $horaIngreso .' hora de Ingreso <br/>';

                        $nuevoserrores = $this->validarControlIngreso($i, $ingresos);

                        if (!isset($nuevoserrores[0]["error"])) {

                            // para cada registro, ejecutamos el constructor de la clase para que inicialice todas las variables y arrys
                            $controlporteria->ControlPorteria();

                            $datos = $controlporteria->ConsultarVistaControlPorteria("Tercero_idTercero = " . $ingresos[$i]['Tercero_idTercero'] . " and
                                                                                                fechaIngresoControlPorteria = '$fechaIngreso' and
                                                                                                horaIngresoControlPorteria = '$horaIngreso'");
                            if ($control == 'INGRESO') {
                                $controlporteria->Visita_idVisita = 0;
                                $controlporteria->Tercero_idTercero = $ingresos[$i]['Tercero_idTercero'];
                                $controlporteria->descripcionControlPorteria = 'Importado desde Excel';

                                $controlporteria->fechaIngresoControlPorteria = $ingresos[$i]['fecha'];
                                $controlporteria->horaIngresoControlPorteria = $ingresos[$i]['hora'];
                                //echo $i. ' -> '. $control . ' '. $ingresos[$i]['fecha']. ' ' .$ingresos[$i]['hora'].'<br>';
                                // adicionamos el registro
                                // si existe el registro con fecha de ingreso y hora de ingreso, no lo adicionamos de nuevo
                                if (!isset($datos[0]["Tercero_idTercero"]) or ( $datos[0]["Tercero_idTercero"] == $ingresos[$i]['Tercero_idTercero'] and $datos[0]["horaIngresoControlPorteria"] != $ingresos[$i]['hora']))
                                    $controlporteria->AdicionarControlPorteria();

                                $control = 'SALIDA';
                            }
                            else {

                                //echo $datos[0]["Tercero_idTercero"] .' == '. $ingresos[$i]['Tercero_idTercero'] .' and '. $datos[0]["horaIngresoControlPorteria"] .' != '. $ingresos[($i-1)]['hora'].' if<br/>';

                                if ($datos[0]["Tercero_idTercero"] == $ingresos[$i]['Tercero_idTercero'] and $datos[0]["horaIngresoControlPorteria"] != $ingresos[($i - 1)]['hora']) {
                                    $consulta = $controlporteria->ConsultarVistaControlPorteria("Tercero_idTercero = " . $ingresos[$i]['Tercero_idTercero'] . " and
                                                                                                fechaIngresoControlPorteria = '$fechaIngreso' and
                                                                                                horaIngresoControlPorteria = '" . $ingresos[($i - 1)]['hora'] . "'");

                                    if (isset($consulta[0]["idControlPorteria"]))
                                        $idControl = $consulta[0]["idControlPorteria"];
                                    else
                                        $idControl = 0;
                                }
                                else {
                                    $idControl = 0;
                                }
                                //echo $idControl .' id consulta </br>';
                                // como la consulta trae los datos llenos, solo reemplazamos los siguientes datos
                                $controlporteria->idControlPorteria = ($idControl != 0 ? $consulta[0]["idControlPorteria"] : ((isset($datos[0]["idControlPorteria"]) ? $datos[0]["idControlPorteria"] : 0)));
                                $controlporteria->descripcionControlPorteria = 'Importado desde Excel';
                                $controlporteria->fechaSalidaControlPorteria = $ingresos[$i]['fecha'];
                                $controlporteria->horaSalidaControlPorteria = $ingresos[$i]['hora'];
                                //echo $i. ' -> id = '.$controlporteria->idControlPorteria.' -> '. $control . ' '. $ingresos[$i]['fecha']. ' ' .$ingresos[$i]['hora'].'<br>';
                                // modificamos el registro
                                $controlporteria->ModificarSalidaControlPorteria();
                                $control = 'INGRESO';
                            }
                        } else {
                            $retorno = array_merge((array) $retorno, (array) $nuevoserrores);
                        }
                        $i++;

                        //}
                    }
                }
            }

            return $retorno;
        }

        function validarControlIngreso($x, $ingresos) {

            $swerror = true;
            $errores = array();
            $linea = 0;


            // validamos la clasificacion
            if ($ingresos[$x]["cedula"] == '' or $ingresos[$x]["Tercero_idTercero"] == '' or $ingresos[$x]["Tercero_idTercero"] == 0) {
                $errores[$linea]["linea"] = $x + 2;
                $errores[$linea]["nombre"] = $ingresos[$x]["nombre"];
                $errores[$linea]["error"] = 'La cedula esta en blanco (' . $ingresos[$x]["cedula"] . ') o no es un empleado valido';
                $swerror = false;
                $linea++;
            }

            return $errores;
        }

        function validarTipoDatoProducto($x, $referencias) {
            $swerror = true;
            $errores = array();
            $linea = 0;
            /* foreach($referencias as $primer=>$segunda)
              {
              $title = array_keys($segunda);
              $totaltitle = count($title);
              for($j=0; $j<$totaltitle; $j++)
              {
              if($title[$j] == 'codigoAlternoProducto')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["codigoAlternoProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo C&oacute;digo Alterno debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'referenciaProducto')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["referenciaProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo Referencia debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'nombreLargoProducto')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["nombreLargoProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo Descripci&oacute;n Larga debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'nombreCortoProducto')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["nombreCortoProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo Descripci&oacute;n Corta debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'codigoBarrasProducto')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["codigoBarrasProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo C&oacute;digo de Barras debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'referenciaClienteProducto')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["referenciaClienteProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo Referencia Cliente debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'codigoProveedor')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["codigoProveedor"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo C&oacute;digo Proveedor debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'referenciaProveedorProducto')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["referenciaProveedorProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo Referencia Proveedor debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'nombreFabricanteProducto')
              {
              $patron = "/^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$/";
              if(!preg_match($patron, $referencias[$x]["referenciaProveedorProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo Nombre Fabricante debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'precioProducto')
              {
              $patron = "/^[\0-9.]+$/";
              if(!preg_match($patron, $referencias[$x]["precioProducto"]))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo Precio de Venta debe ser numérico indicando los decimales con punto(.).';
              $swerror = false;
              $linea++;
              }
              }
              }
              }

              //print_r($referencias);
              /*switch($tipo)
              {
              //Validación Númerica /^[[:digit:]]+$/
              case 'N':
              $patron = "[0-9]";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe de ser numérico.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación de Texto
              case 'T':
              //^([A-Za-z\s])*$/  /[^A-Za-z\s ]/ /\w+
              $patron = "[A-Za-z]";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe de ser texto.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación de Correo
              case 'C':
              $patron = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre. ' debe ser de tipo correo.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación de Página Web
              case 'W':
              $patron = "^(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)( [a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_]*)?$";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe ser de tipo página web.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación Alfanumérico
              case 'A':
              //[^a-zA-Z0-9]/
              $patron = "[A-Za-z0-9]";
              if(!preg_match($patron, $valor))
              {
              echo 'Entrar.';
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación de Fechas
              case 'F':
              $patron = "^\d{4,4}\/\d{1,2}\/\d{4,4}$";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe ser con formato aa-mm-dd.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validacion de Horas
              case 'H':
              $patron = "/^(0[1-9]|1\d|2[0-3]):([0-5]\d):([0-5]\d)$/";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe ser con formato hh:mm:ss.';
              $swerror = false;
              $linea++;
              }
              break;
              } */
            return $errores;
        }

        function validarTipoDatoTercero($x, $terceros) {
            $swerror = true;
            $errores = array();
            $linea = 0;
            /* foreach($terceros as $primer=>$segunda)
              {
              $title = array_keys($segunda);
              $totaltitle = count($title);
              for($j=0; $j<$totaltitle; $j++)
              {
              if($title[$j] == 'documentoTercero')
              {
              $patron = "/^[\0-9]+$/";
              if(!preg_match($patron, $terceros[$x]["documentoTercero"]))
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El campo N&uacute;mero Documento debe ser numérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'codigoAlterno1Tercero')
              {
              $patron = "/^[\A-Za-z0-9*-:.() ]+$/";
              if(!preg_match($patron, $terceros[$x]["codigoAlterno1Tercero"]))
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El campo C&oacute;digo Alterno 1 debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'nombre1Tercero')
              {
              $patron = "/^[\A-Za-z0-9*-:.() ]+$/";
              if(!preg_match($patron, $terceros[$x]["nombre1Tercero"]))
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El campo Razon Social o Nombres debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'direccionTercero')
              {
              $patron = "/^[\A-Za-z0-9*-:.() ]+$/";
              if(!preg_match($patron, $terceros[$x]["direccionTercero"]))
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El campo Direcci&oacute;n debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'direccioRutTercero')
              {
              $patron = "/^[\A-Za-z0-9*-:.() ]+$/";
              if(!preg_match($patron, $terceros[$x]["direccioRutTercero"]))
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El campo Direcci&oacute;n debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'telefono1Tercero')
              {
              $patron = "/^[\A-Za-z0-9*-:.() ]+$/";
              if(!preg_match($patron, $terceros[$x]["telefono1Tercero"]))
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El campo Telefono 1 debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'correoElectronicoTercero')
              {
              $patron = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-])+$/";
              if(!preg_match($patron, $terceros[$x]["correoElectronicoTercero"]))
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El campo Correo(s) Electr&oacute;nico(s) debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              if($title[$j] == 'paginaWebTercero')
              {
              $patron = "/^[\A-Za-z0-9*-:.() ]+$/";
              if(!preg_match($patron, $terceros[$x]["paginaWebTercero"]))
              {
              $errores[$linea]["documentoTercero"] = $terceros[$x]["documentoTercero"];
              $errores[$linea]["nombre1Tercero"] = $terceros[$x]["nombre1Tercero"];
              $errores[$linea]["nombre2Tercero"] = $terceros[$x]["nombre2Tercero"];
              $errores[$linea]["error"] = 'El campo P&aacute;gina WEB debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              }
              }
              }

              //print_r($terceros);
              /*switch($tipo)
              {
              //Validación Númerica /^[[:digit:]]+$/
              case 'N':
              $patron = "[0-9]";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe de ser numérico.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación de Texto
              case 'T':
              //^([A-Za-z\s])*$/  /[^A-Za-z\s ]/ /\w+
              $patron = "[A-Za-z]";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe de ser texto.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación de Correo
              case 'C':
              $patron = "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre. ' debe ser de tipo correo.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación de Página Web
              case 'W':
              $patron = "^(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)( [a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_]*)?$";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe ser de tipo página web.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación Alfanumérico
              case 'A':
              //[^a-zA-Z0-9]/
              $patron = "[A-Za-z0-9]";
              if(!preg_match($patron, $valor))
              {
              echo 'Entrar.';
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe ser alfanumérico.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validación de Fechas
              case 'F':
              $patron = "^\d{4,4}\/\d{1,2}\/\d{4,4}$";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe ser con formato aa-mm-dd.';
              $swerror = false;
              $linea++;
              }
              break;
              //Validacion de Horas
              case 'H':
              $patron = "/^(0[1-9]|1\d|2[0-3]):([0-5]\d):([0-5]\d)$/";
              if(!preg_match($patron, $valor))
              {
              $errores[$linea]["referenciaProducto"] = $referencias[$x]["referenciaProducto"];
              $errores[$linea]["nombreLargoProducto"] = $referencias[$x]["nombreLargoProducto"];
              $errores[$linea]["error"] = 'El campo '.$nombre.' debe ser con formato hh:mm:ss.';
              $swerror = false;
              $linea++;
              }
              break;
              } */
            return $errores;
        }

        function ImportarInventarioFisico($ruta) {
            set_time_limit(0);


            include('../clases/PHPExcel/Classes/PHPExcel.php');

            /* Incluimos el fichero de la clase Db */
            require_once'db.class.php';
            /* Incluimos el fichero de la clase Conf */
            require_once'conf.class.php';




            $bd = Db::getInstance();

            $rutacompleta = explode(".", $ruta); //echo '3';
            $extension = array_pop($rutacompleta); //echo '4';
            if (!isset($objReader)) {
                if ($extension == 'xlsx') {
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007'); /* echo 'xlsx'; */
                } else {
                    $objReader = PHPExcel_IOFactory::createReader('Excel5'); /* echo 'xls'; */
                }
            }
            //echo '5';
            $objReader->setLoadSheetsOnly('datos'); //echo '6';
            $objReader->setReadDataOnly(true); //echo '7'.$ruta;
            $objPHPExcel = $objReader->load($ruta); //echo '8';

            $objWorksheet = $objPHPExcel->getActiveSheet(); //echo '9';
            $highestRow = $objWorksheet->getHighestRow(); //echo '10';// e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); //echo '11';// e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //echo '12';// e.g.

            $encabezado = array();
            $detalle = array();
            $posRef = -1;
            $posDet = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();



                $posRef++;


                for ($columna = 0; $columna <= 8; $columna++) {

                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }


                $sql = "select idBodega from Bodega where codigoAlternoBodega = '" . $encabezado[$posRef]['codigoAlternoBodega'] . "'";
                $bodega = $bd->ConsultarVista($sql);


                if (isset($bodega[0]['idBodega'])) {
                    $encabezado[$posRef]['Bodega_idBodega'] = $bodega[0]['idBodega'];
                } else {
                    $encabezado[$posRef]['Bodega_idBodega'] = 0;
                }

                $sql = "select idInventarioFisico,estadoInventarioFisico from InventarioFisico where numeroInventarioFisico = '" . $encabezado[$posRef]['numeroInventarioFisico'] . "'";
                $inv_fisico = $bd->ConsultarVista($sql);

                if (isset($inv_fisico[0]['idInventarioFisico'])) {
                    $encabezado[$posRef]['idInventarioFisico'] = $inv_fisico[0]['idInventarioFisico'];
                    $encabezado[$posRef]['estadoInventarioFisico'] = $inv_fisico[0]['estadoInventarioFisico'];
                } else {
                    $encabezado[$posRef]['idInventarioFisico'] = 0;
                    $encabezado[$posRef]['estadoInventarioFisico'] = '';
                }





                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL and
                $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue()) {
                    $posDet++;


                    // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                    $detalle[$posDet]["numeroInventarioFisico"] = $numeroAnt;


                    for ($columna = 9; $columna <= 15; $columna++) {
                        // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    }


                    $sql = "select idProducto from Producto where referenciaProducto = '" . $detalle[$posDet]['referenciaProducto'] . "' or codigoBarrasProducto = '" . $detalle[$posDet]['referenciaProducto'] . "'";
                    $producto = $bd->ConsultarVista($sql);


                    if (isset($producto[0]['idProducto'])) {
                        $detalle[$posDet]['Producto_idProducto'] = $producto[0]['idProducto'];
                    } else {
                        $detalle[$posDet]['Producto_idProducto'] = 0;
                    }

                    $fila++;
                }
            }

            $objReader->setLoadSheetsOnly('lotes'); //echo '6';
            $objReader->setReadDataOnly(true); //echo '7'.$ruta;
            $objPHPExcel = $objReader->load($ruta); //echo '8';

            $objWorksheet = $objPHPExcel->getActiveSheet(); //echo '9';
            $highestRow = $objWorksheet->getHighestRow(); //echo '10';// e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); //echo '11';// e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //echo '12';// e.g.

            $lotes = array();
            $posLote = -1;
            $fila = 4;


            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $posLote++;
                for ($columna = 0; $columna <= 6; $columna++) {
                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $lotes[$posLote][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                $sql = "select idInventarioFisico from InventarioFisico where numeroInventarioFisico = '" . $lotes[$posLote]['numeroInventarioFisico'] . "'";
                //                echo $sql;

                $inv = $bd->ConsultarVista($sql);


                $sql = "select idProducto from Producto where referenciaProducto = '" . $lotes[$posLote]['referenciaProducto'] . "'";
                $referencia = $bd->ConsultarVista($sql);
                //                echo $sql;

                $sql = "select idLote from Lote where numeroLote = '" . $lotes[$posLote]['numeroLote'] . "'";
                $lote = $bd->ConsultarVista($sql);

                $sql = "select idProdctoSerie from Lote where numeroProductoSerie = '" . $lotes[$posLote]['numeroSerie'] . "'";
                $serie = $bd->ConsultarVista($sql);

                $lotes[$posLote]['InventarioFisico_idInventarioFisico'] = isset($inv[0]['idInventarioFisico']) ? $inv[0]['idInventarioFisico'] : 0;
                $lotes[$posLote]['Producto_idProducto'] = isset($referencia[0]['idProducto']) ? $referencia[0]['idProducto'] : 0;
                $lotes[$posLote]['Lote_idLote'] = isset($lote[0]['idLote']) ? $lote[0]['idLote'] : 0;
                $lotes[$posLote]['ProductoSerie_idProductoSerie'] = isset($serie[0]['ProductoSerie_idProductoSerie']) ? $serie[0]['ProductoSerie_idProductoSerie'] : 0;

                $fila++;
            }

            //            echo '<pre>';
            //            echo '<pre>'.var_dump($lotes).'</pre>';
            //            echo '</pre>';
            //
            //
            //
            unlink($ruta);
            $retorno = $this->llenarPropiedadesInventarioFisico($encabezado, $detalle, $lotes);
            return $retorno;
        }

        function llenarPropiedadesInventarioFisico($encabezado, $detalle, $lotes) {
            set_time_limit(0);
            require_once('../clases/inventariofisico.class.php');
            $inv_fisico = new InventarioFisico();

            $errores = $this->validarInventarioFisico($encabezado, $detalle, $lotes);
            $totalEnc = count($encabezado);
            $totalDet = count($detalle);

            /* Incluimos el fichero de la clase Db */
            require_once'db.class.php';
            /* Incluimos el fichero de la clase Conf */
            require_once'conf.class.php';

            $bd = Db::getInstance();


            require_once('periodo.class.php');
            $periodo = new Periodo();
            $posicion = 0;


            if (count($errores) == 0) {
                for ($i = 0; $i < $totalEnc; $i++) {

                    $datoPer = $periodo->ConsultarVistaPeriodo("fechaInicialPeriodo <= '" . $encabezado[$i]['fechaElaboracionInventarioFisico'] . "' and fechaFinalPeriodo >= '" . $encabezado[$i]['fechaElaboracionInventarioFisico'] . "' ");


                    if (isset($datoPer[0]['idPeriodo']) != '') {
                        $idPeriodo = $datoPer[0]['idPeriodo'];
                    } else {
                        $idPeriodo = 0;
                    }
                    //

                    $inv_fisico->idInventarioFisico = $encabezado[$i]['idInventarioFisico'];
                    $inv_fisico->numeroInventarioFisico = $encabezado[$i]['numeroInventarioFisico'];
                    $inv_fisico->fechaElaboracionInventarioFisico = $encabezado[$i]['fechaElaboracionInventarioFisico'];
                    $inv_fisico->Periodo_idPeriodoAjuste = 0;
                    $inv_fisico->Bodega_idBodegaPorDefecto = $encabezado[$i]['Bodega_idBodega'];
                    $inv_fisico->ConteoInventarioFisico = $encabezado[$i]['conteoInventarioFisico'];
                    $inv_fisico->nombreInventarioFisico = $encabezado[$i]['nombreInventarioFisico'];
                    $inv_fisico->observacionInventarioFisico = $encabezado[$i]['observacionInventarioFisico'];
                    $inv_fisico->campo1DiferenciaInventarioFisico = '';
                    $inv_fisico->campo2DiferenciaInventarioFisico = '';
                    $inv_fisico->estadoInventarioFisico = 'CONTEO';
                    $inv_fisico->tipoConteoInventarioFisico = $encabezado[$i]['tipoInventarioFisico'];
                    $inv_fisico->metodoInventarioFisico = $encabezado[$i]['metodoInventarioFisico'];
                    $inv_fisico->corteInventarioFisico = $encabezado[$i]['corteInventarioFisico'];
                    $inv_fisico->tipoStockInventarioFisico = 3;

                    if ($encabezado[$i]['idInventarioFisico'] == 0) {
                        $inv_fisico->AdicionarInventario();
                        $sql = "select idInventarioFisico from InventarioFisico where numeroInventarioFisico = '" . $encabezado[$i]['numeroInventarioFisico'] . "'";
                        $dat_fisico = $bd->ConsultarVista($sql);
                        $inv_fisico->idInventarioFisico = $dat_fisico[0]['idInventarioFisico'];
                    }else{

                        $inv_fisico->idInventarioFisico = $encabezado[$i]['idInventarioFisico'];
                    }



                    for ($j = 0; $j < $totalDet; $j++) {
                        if (isset($encabezado[$i]["numeroInventarioFisico"]) and
                                isset($detalle[$j]["numeroInventarioFisico"]) and
                                $encabezado[$i]["numeroInventarioFisico"] == $detalle[$j]["numeroInventarioFisico"]) {


                            $sql = "select GROUP_CONCAT(idInventarioFisicoDetalle) as idInventarioFisicoDetalle from InventarioFisicoDetalle where Producto_idProducto = " . $detalle[$j]["Producto_idProducto"] . " and InventarioFisico_idInventarioFisico = " . $inv_fisico->idInventarioFisico;




                            $dato = $bd->ConsultarVista($sql);

                            if (isset($dato[0]['idInventarioFisicoDetalle'])) {
                                $sql = "DELETE FROM InventarioFisicoDetalle WHERE idInventarioFisicoDetalle in (" . $dato[0]['idInventarioFisicoDetalle'].')';
                                $bd->ejecutar($sql);
                            }

                            $inv_fisico->idInventarioFisicoDetalle[$posicion] = 0;
                            $inv_fisico->BodegaUbicacion_idBodegaUbicacion[$posicion] = 0;
                            $inv_fisico->Embalaje_idEmbalaje[$posicion] = 0;
                            $inv_fisico->Producto_idProducto[$posicion] = $detalle[$j]["Producto_idProducto"];
                            $inv_fisico->ProductoSerie_idProductoSerie[$posicion] = 0;
                            $inv_fisico->tipoRegistroInventarioFisicoDetalle[$posicion] = 0;
                            $inv_fisico->conteo1InventarioFisicoDetalle[$posicion] = number_format($detalle[$j]["conteo1InventarioFisico"], 6, '.', '');
                            $inv_fisico->conteo2InventarioFisicoDetalle[$posicion] = number_format($detalle[$j]["conteo2InventarioFisico"], 6, '.', '');
                            $inv_fisico->conteo3InventarioFisicoDetalle[$posicion] = number_format($detalle[$j]["conteo3InventarioFisico"], 6, '.', '');
                            $inv_fisico->conteo4InventarioFisicoDetalle[$posicion] = number_format($detalle[$j]["conteo4InventarioFisico"], 6, '.', '');
                            $inv_fisico->conteo5InventarioFisicoDetalle[$posicion] = number_format($detalle[$j]["conteo5InventarioFisico"], 6, '.', '');
                            $inv_fisico->existenciaInventarioFisicoDetalle[$posicion] = 0;
                            $inv_fisico->diferenciaCalculaInventarioFisicoDetalle[$posicion] = 0;
                            $inv_fisico->diferenciaAjustadaInventarioFisicoDetalle[$posicion] = 0;

                            if (!is_double($detalle[$j]["costoInventarioFisico"]) or $detalle[$j]["costoInventarioFisico"] == 0 or $detalle[$j]["costoInventarioFisico"] == '') {
                                $inv_fisico->costoInventarioFisicoDetalle[$posicion] = $inv_fisico->consultarCosto($detalle[$j]["Producto_idProducto"], $idPeriodo, $encabezado[$i]['Bodega_idBodega'], 0, 0);
                            } else {
                                $inv_fisico->costoInventarioFisicoDetalle[$posicion] = number_format($detalle[$j]["costoInventarioFisico"], 6, '.', '');
                            }

                            $posicion++;



                        }
                    }

                    $inv_fisico->AdicionarInventarioFisicoDetalle($inv_fisico->idInventarioFisico);
                    $inv_fisico->InventarioFisico();
                    $posicion = 0;
                }


                $inv_fisico->AdicionarLoteExcel($lotes, $idPeriodo, $encabezado[$i]['Bodega_idBodega']);
            } else {
                return $errores;
            }
        }

        function validarInventarioFisico($encabezado = array(), $detalle = array(), $lotes = array()) {
            $errores = array();


            $con = 0;

            /* Incluimos el fichero de la clase Db */
            require_once'db.class.php';
            /* Incluimos el fichero de la clase Conf */
            require_once'conf.class.php';

            $bd = Db::getInstance();



            for ($i = 0; $i < count($encabezado); $i++) {

                $date_format = 'Y-m-d';
                $input = $encabezado[$i]['fechaElaboracionInventarioFisico'];

                $input = trim($input);
                $time = strtotime($input);

                $is_valid = date($date_format, $time) == $input;

                if (!$is_valid) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha  ' . $encabezado[$i]['fechaElaboracionInventarioFisico'] . ' es invalida';
                    $con++;
                }


                if ($encabezado[$i]['Bodega_idBodega'] == 0) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La Bodega Con Codigo ' . $encabezado[$i]['codigoAlternoBodega'] . ' no existe';
                    $con++;
                }

                if ($encabezado[$i]['estadoInventarioFisico'] == 'ANULADA' || $encabezado[$i]['estadoInventarioFisico'] == 'AJUSTADA') {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La planilla numero ' . $encabezado[$i]['numeroInventarioFisico'] . ' se encuentra '.$encabezado[$i]['estadoInventarioFisico'] .', no se realizan modificaciones';
                    $con++;
                }

                if ($encabezado[$i]['tipoInventarioFisico'] != 'CICLICO'
                        and $encabezado[$i]['tipoInventarioFisico'] != 'COMPLETO'
                        and $encabezado[$i]['tipoInventarioFisico'] != 'ALEATORIO') {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El Tipo de Inventario Fisico debe estar entre el valor (CICLICO, COMPLETO, ALEATORIO)';
                    $con++;
                }


                if ($encabezado[$i]['corteInventarioFisico'] != 1 and $encabezado[$i]['corteInventarioFisico'] != 2) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El corte del invenario fisico debe estar entre (1: Dia actual, 2: Periodo Completo)';
                    $con++;
                }

                if ($encabezado[$i]['metodoInventarioFisico'] != 1 and $encabezado[$i]['metodoInventarioFisico'] != 2) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El Metodo del invenario fisico debe estar entre (1:Completas, 2: Solo Planilla)';
                    $con++;
                }

                if ($encabezado[$i]['conteoInventarioFisico'] != 1 and $encabezado[$i]['conteoInventarioFisico'] != 2 and $encabezado[$i]['conteoInventarioFisico'] != 3 and $encabezado[$i]['conteoInventarioFisico'] != 4 and $encabezado[$i]['conteoInventarioFisico'] != 5) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El Numero de Conteo debe Estar entre (1  , 5)';
                    $con++;
                }
            }

            for ($i = 0; $i < count($detalle); $i++) {


                for ($o = 0; $o < count($detalle); $o++) {

                    if ($o != $i and $detalle[$o]['referenciaProducto'] == $detalle[$i]['referenciaProducto']) {
                        $errores[$con]["linea"] = (4 + $i);
                        $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                        $errores[$con]["Descripcion"] = 'Descripcion: La Producto Con Referencia y/o Codigo ' . $detalle[$i]['referenciaProducto'] . ' se repite en las lineas (' . ($i + 4) . ') y (' . ($o + 4) . ')';
                        $con++;
                    }
                }

                if ($detalle[$i]['Producto_idProducto'] == 0) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'Descripcion: La Producto Con Referencia y/o Codigo ' . $detalle[$i]['referenciaProducto'] . ' no existe';
                    $con++;
                }



                for ($b = 1; $b < 6; $b++) {

                    if ($detalle[$i]['conteo' . $b . 'InventarioFisico'] < 0 or ! is_double($detalle[$i]['conteo' . $b . 'InventarioFisico'])) {

                        $errores[$con]["linea"] = (4 + $i);
                        $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                        $errores[$con]["Descripcion"] = 'La Cantidad de Conteo ' . $b . ' para el Producto Con Referencia y/o Codigo ' . $detalle[$i]['referenciaProducto'] . ' debe ser mayor o igual a cero';
                        $con++;
                    }
                }

                if ($detalle[$i]['costoInventarioFisico'] < 0 or ! is_double($detalle[$i]['costoInventarioFisico'])) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La Cantidad del Costo  para el Producto Con Referencia y/o Codigo ' . $detalle[$i]['costoInventarioFisico'] . ' debe ser mayor o igual a cero';
                    $con++;
                }
            }

            for ($i = 0; $i < count($lotes); $i++) {
                if ($lotes[$i]['InventarioFisico_idInventarioFisico'] == 0) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El numero del Inventario Fisico  ' . $lotes[$i]['numeroInventarioFisico'] . ' para el lote ' . $lotes[$i]['numeroLote'] . ' con referencia ' . $lotes[$i]['referenciaProducto'] . ' no existe';
                    $con++;
                }

                if ($lotes[$i]['Producto_idProducto'] == 0) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La referencia ' . $lotes[$i]['referenciaProducto'] . ' para el lote ' . $lotes[$i]['numeroLote'] . ' no existe';
                    $con++;
                }

                if ($lotes[$i]['Lote_idLote'] == 0 and $lotes[$i]['numeroLote'] != '') {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El lote con numero ' . $lotes[$i]['numeroLote'] . ' no existe';
                    $con++;
                }

                if ($lotes[$i]['ProductoSerie_idProductoSerie'] == 0 and $lotes[$i]['ProductoSerie_idProductoSerie'] != '') {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El Producto con Serie ' . $lotes[$i]['numeroSerie'] . ' no existe';
                    $con++;
                }

                if ($lotes[$i]['numeroSerie'] == '' and $lotes[$i]['numeroLote'] != '') {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'Debe llenar el lote o la serie en la fila indicada ';
                    $con++;
                }


                if (!is_float($lotes[$i]['conteo1InventarioFisicoDetalleLote']) || !is_float($lotes[$i]['conteo2InventarioFisicoDetalleLote'])) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El Conteo 1 y Conteo 2  para el lote ' . $lotes[$i]['numeroLote'] . ' debe ser datos formato numero igual o mayores a cero';
                    $con++;
                }

                if (!is_float($lotes[$i]['costoInventarioFisicoDetalleLote'])) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El costo para el lote ' . $lotes[$i]['numeroLote'] . ' debe ser datos formato numero igual o mayores a cero';
                    $con++;
                }

                if (($lotes[$i]['conteo1InventarioFisicoDetalleLote']) == 0 and ( $lotes[$i]['conteo2InventarioFisicoDetalleLote']) == 0) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El Conteo 1 o Conteo 2  para el lote ' . $lotes[$i]['numeroLote'] . ' debe ser mayor a cero';
                    $con++;
                }

                if ($lotes[$i]['InventarioFisico_idInventarioFisico'] != 0 and $lotes[$i]['Producto_idProducto'] != 0) {
                    $sql = "select Producto_idProducto from InventarioFisicoDetalle where Producto_idProducto = " . $lotes[$i]['Producto_idProducto'] . ' and InventarioFisico_idInventarioFisico = ' . $lotes[$i]['InventarioFisico_idInventarioFisico'];
                    $datos = $bd->ConsultarVista($sql);

                    if (!isset($datos[0]['Producto_idProducto'])) {
                        $errores[$con]["linea"] = (4 + $i);
                        $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                        $errores[$con]["Descripcion"] = 'La referencia ' . $lotes[$i]['referenciaProducto'] . ' no existe en el en el inventario fisico numero ' . $lotes[$i]['numeroInventarioFisico'];
                        $con++;
                    }
                }
            }

            return $errores;
        }

        function importarVacacionesEmpleado($ruta) {
            set_time_limit(0);


            include('../clases/PHPExcel/Classes/PHPExcel.php');

            /* Incluimos el fichero de la clase Db */
            require_once'db.class.php';
            /* Incluimos el fichero de la clase Conf */
            require_once'conf.class.php';

            require_once('../clases/contrato.class.php');
            $contrato = new Contrato();


            $bd = Db::getInstance();

            $rutacompleta = explode(".", $ruta); //echo '3';
            $extension = array_pop($rutacompleta); //echo '4';
            if (!isset($objReader)) {
                if ($extension == 'xlsx') {
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007'); /* echo 'xlsx'; */
                } else {
                    $objReader = PHPExcel_IOFactory::createReader('Excel5'); /* echo 'xls'; */
                }
            }
            //echo '5';
            $objReader->setLoadSheetsOnly('datos'); //echo '6';
            $objReader->setReadDataOnly(true); //echo '7'.$ruta;
            $objPHPExcel = $objReader->load($ruta); //echo '8';

            $objWorksheet = $objPHPExcel->getActiveSheet(); //echo '9';
            $highestRow = $objWorksheet->getHighestRow(); //echo '10';// e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); //echo '11';// e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //echo '12';// e.g.

            $encabezado = array();
            $detalle = array();
            $posRef = -1;
            $posDet = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();



                $posRef++;


                for ($columna = 0; $columna <= 3; $columna++) {

                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                //Consultamos el grupo de nomina
                $encabezado[$posRef]['GrupoNomina_idGrupoNomina'] = 0;
                $encabezado[$posRef]['Periodo_idPeriodo'] = 0;
                $encabezado[$posRef]['idVacaciones'] = 0;


                $sql = "select idGrupoNomina from GrupoNomina where codigoAlternoGrupoNomina = '" . $encabezado[$posRef]['codigoGrupoNomina'] . "'";
                $dato = $bd->ConsultarVista($sql);

                if (isset($dato[0]['idGrupoNomina']) and $dato[0]['idGrupoNomina'] > 0) {
                    $encabezado[$posRef]['GrupoNomina_idGrupoNomina'] = $dato[0]['idGrupoNomina'];
                }

                $sql = "select idPeriodo from Periodo where fechaInicialPeriodo <= '" . $encabezado[$posRef]['periodoContable'] . "' and fechaFinalPeriodo >= '" . $encabezado[$posRef]['periodoContable'] . "'";
                $dato = $bd->ConsultarVista($sql);


                if (isset($dato[0]['idPeriodo']) and $dato[0]['idPeriodo'] > 0) {
                    $encabezado[$posRef]['Periodo_idPeriodo'] = $dato[0]['idPeriodo'];
                }

                $sql = "select idVacaciones from Vacaciones where numeroVacaciones = '" . $encabezado[$posRef]['numeroVacaciones'] . "'";
                $dato = $bd->ConsultarVista($sql);



                if (isset($dato[0]['idVacaciones']) and $dato[0]['idVacaciones'] > 0) {
                    $encabezado[$posRef]['idVacaciones'] = $dato[0]['idVacaciones'];
                }

                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL and
                $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue()) {
                    $posDet++;


                    // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                    $detalle[$posDet]["numeroVacaciones"] = $numeroAnt;


                    for ($columna = 4; $columna <= 16; $columna++) {
                        // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    }

                    $detalle[$posDet]['Contrato_idContrato'] = 0;
                    $detalle[$posDet]['Tercero_idTercero'] = 0;


                    $sql = "select idTercero from Tercero where documentoTercero = '" . $detalle[$posDet]['documentoEmpleado'] . "' and estadoTercero = 'ACTIVO'";
                    $dato = $bd->ConsultarVista($sql);


                    if (isset($dato[0]['idTercero']) and $dato[0]['idTercero'] > 0) {
                        $detalle[$posDet]['Tercero_idTercero'] = $dato[0]['idTercero'];
                    }


                    $conempl = $contrato->ConsultarVistaContrato(
                            "Tercero_idCliente IN (" . $detalle[$posDet]['Tercero_idTercero'] . ") and (fechaTerminacionContrato = '0000-00-00' or (fechaTerminacionContrato >= '" . $detalle[$posDet]['fechaInicio'] . "' ))", "Tercero_idCliente", "idContrato,fechaElaboracionContrato, valorContrato, fechaVencimientoContrato,
                                                      fechaInicioContrato, Tercero_idCliente, nombre1Empleado, nombre2Empleado, fechaTerminacionContrato,
                                                      periodicidadPagoContrato, registraTurnoContrato, jornadaLaboralDiaContrato, registraTurnoContrato, basePagoGrupoNomina,
                                                      tipoSalarioContrato, tipoPagoContrato, estadoContrato, MedioPago_idMedioPago, TipoContrato_idTipoContrato,
                                                      CausaTerminacionContrato_idCausaTerminacionContrato, Turno_idTurno, GrupoNomina_idGrupoNomina", "");


                    if (isset($conempl[0]['idContrato']) and $conempl[0]['idContrato'] > 0) {
                        $detalle[$posDet]['Contrato_idContrato'] = $conempl[0]['idContrato'];
//                        $detalle[$posDet]['contrato'] = $conempl[0];
                    }




                    $fila++;
                }
            }

    //                    echo '<pre>';
    //                            print_r($encabezado);
    //                            echo '</pre>';
    //                            echo '<br>';

            unlink($ruta);
            $retorno = $this->llenarPropiedadesVacaciones($encabezado, $detalle);



            return $retorno;
        }

        function llenarPropiedadesVacaciones($encabezado, $detalle) {


            $errores = $this->validarVacaciones($encabezado, $detalle);

            if (count($errores) == 0) {

                require_once 'vacaciones.class.php';
                $vacaciones = new Vacaciones();

                require_once('../clases/nomina.class.php');
                $nomina = new Nomina();

                /* Incluimos el fichero de la clase Db */
                require_once'db.class.php';
                /* Incluimos el fichero de la clase Conf */
                require_once'conf.class.php';

                require_once('../clases/contrato.class.php');
                $contrato = new Contrato();

                $bd = Db::getInstance();


                for ($i = 0; $i < count($encabezado); $i++) {

                    if ($encabezado[$i]['idVacaciones'] == 0) {
                        $vacaciones->idVacaciones = 0;
                        $vacaciones->numeroVacaciones = $encabezado[$i]['numeroVacaciones'];
                        $vacaciones->fechaElaboracionVacaciones = $encabezado[$i]['fechaElaboracionVacaciones'] != '' ? $encabezado[$i]['fechaElaboracionVacaciones'] : date('Y-m-d');
                        $vacaciones->Periodo_idPeriodo = $encabezado[$i]['Periodo_idPeriodo'];
                        $vacaciones->GrupoNomina_idGrupoNomina = $encabezado[$i]['GrupoNomina_idGrupoNomina'];
                        $vacaciones->periodoVacaciones = 0;
                        $vacaciones->observacionVacaciones = '';
                        $id = $vacaciones->AdicionarVacaciones();
                    } else {
                        $vacaciones->idVacaciones = $encabezado[$i]['idVacaciones'];
                    }

                    $sm = 0;


                    for ($j = 0; $j < count($detalle); $j++) {
                        if (isset($encabezado[$i]["numeroVacaciones"]) and
                                isset($detalle[$j]["numeroVacaciones"]) and
                                $encabezado[$i]["numeroVacaciones"] == $detalle[$j]["numeroVacaciones"]) {


                            $sql = "select idVacacionesDetalle from VacacionesDetalle where Vacaciones_idVacaciones = ".$vacaciones->idVacaciones." and Tercero_idEmpleado = " . $detalle[$j]["Tercero_idTercero"];
//                            echo $sql.'<br>';
                            $info = $bd->ConsultarVista($sql);



                            if (count($info) > 0) {
                                $sql = "delete from VacacionesDetalle where Vacaciones_idVacaciones = ".$vacaciones->idVacaciones." and Tercero_idEmpleado = " . $detalle[$j]["Tercero_idTercero"];
//                                echo $sql.'<br>';
                                $bd->ejecutar($sql);
                            }

                            $conempl = $contrato->ConsultarVistaContrato(
                            "Tercero_idCliente IN (" . $detalle[$j]['Tercero_idTercero'] . ") and (fechaTerminacionContrato = '0000-00-00' or (fechaTerminacionContrato >= '" . $detalle[$j]['fechaInicio'] . "' ))", "Tercero_idCliente", "idContrato,fechaElaboracionContrato, valorContrato, fechaVencimientoContrato,
                                                      fechaInicioContrato, Tercero_idCliente, nombre1Empleado, nombre2Empleado, fechaTerminacionContrato,
                                                      periodicidadPagoContrato, registraTurnoContrato, jornadaLaboralDiaContrato, registraTurnoContrato, basePagoGrupoNomina,
                                                      tipoSalarioContrato, tipoPagoContrato, estadoContrato, MedioPago_idMedioPago, TipoContrato_idTipoContrato,
                                                      CausaTerminacionContrato_idCausaTerminacionContrato, Turno_idTurno, GrupoNomina_idGrupoNomina", "");


//                                    echo '<pre>';
//                                    print_r($conempl);
//                                    echo '</pre>';



                            $datos = $nomina->CalcularVacaciones($conempl[0], strtotime($detalle[$j]["fechaInicio"]), strtotime("+15 day", strtotime($detalle[$j]["fechaInicio"])));
                            $valordia = round($datos['salarioBase'] / $datos['diasBase'], 2);

//                            var_dump($datos);

//                                 echo '<pre>';
//                                    print_r($datos);
//                                    echo '</pre>';
//


                            //$vacDet = $this->calcularVacacionesDias($detalle[$j]["fechaInicio"],$datos['diasPago'],$detalle[$j]["diasTiempo"],$detalle[$j]["diasDinero"],($datos["salarioEmpleado"]/30),$detalle[$j]["anticipadasVacaciones"]);


                            $vacaciones->idVacacionesDetalle[$sm] = 0;
                            $vacaciones->Contrato_idContrato[$sm] = $conempl[0]['idContrato'];
                            $vacaciones->Tercero_idEmpleado[$sm] = $detalle[$j]["Tercero_idTercero"];
                            $vacaciones->fechaIngresoVacacionesDetalle[$sm] = $conempl[0]["fechaInicioContrato"];
                            $vacaciones->anosServicioVacacionesDetalle[$sm] = round($datos['anosServicio'], 2);
                            $vacaciones->valorBaseVacacionesDetalle[$sm] = $datos["salarioBase"];
                            $vacaciones->salarioDiaVacacionesDetalle[$sm] = ($datos["salarioEmpleado"]/30);
                            $vacaciones->diasPendientesVacacionesDetalle[$sm] = $datos['diasPago'];
                            $vacaciones->fechaInicioVacacionesDetalle[$sm] = $detalle[$j]["fechaInicio"];
                            $vacaciones->diasDisfrutadosTiempoVacacionesDetalle[$sm] = $detalle[$j]["diasTiempo"];
                            $vacaciones->diasDisfrutadosValorVacacionesDetalle[$sm] = $detalle[$j]["diasTiempo"] * $valordia;
                            $vacaciones->diasDomingoFestivoVacacionesDetalle[$sm] = 0;
                            $vacaciones->totalDiasVacacionesDetalle[$sm] = 0;
                            $vacaciones->fechaFinVacacionesDetalle[$sm] = 0;
                            $vacaciones->valorTotalDiasVacacionesDetalle[$sm] = 0;
                            $vacaciones->anticipadasVacacionesDetalle[$sm] = $detalle[$j]["anticipadasVacaciones"];
                            $sm++;

                            unset($datos);
                            unset($conempl);

                        }
                    }

                    $vacaciones->AdicionarVacacionesDetalle();
                }
            } else {

                return $errores;
            }
        }

        function validarVacaciones($encabezado, $detalle) {

            $errores = array();
            $con = 0;


            $campo = "^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$";

            for ($i = 0; $i < count($encabezado); $i++) {

                $time = strtotime(trim($encabezado[$i]['fechaElaboracionVacaciones']));

                $is_valid = date('Y-m-d', $time) == trim($encabezado[$i]['fechaElaboracionVacaciones']);

                if (!$is_valid) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha  ' . $encabezado[$i]['fechaElaboracionVacaciones'] . ' es invalida';
                    $con++;
                }


                $time = strtotime(trim($encabezado[$i]['periodoContable']));
                $is_valid = date('Y-m-d', $time) == trim($encabezado[$i]['periodoContable']);

                if (!$is_valid) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha  ' . $encabezado[$i]['periodoContable'] . ' es invalida';
                    $con++;
                }



                if ($encabezado[$i]['numeroVacaciones'] != '') {
                    if (ereg($campo, $encabezado[$i]['numeroVacaciones']) === false) {
                        $errores[$con]["linea"] = (4 + $i);
                        $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                        $errores[$con]["Descripcion"] = 'El numero de las vacaciones debe ser un dato alfanumerico';
                        $con++;
                    }
                }

                if (trim($encabezado[$i]['codigoGrupoNomina']) == '') {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El grupo de nomina no puede estar vacio';
                    $con++;
                }

                if (trim($encabezado[$i]['periodoContable']) == '') {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El Periodo contable no puede estar vacio';
                    $con++;
                }

                if ($encabezado[$i]['codigoGrupoNomina'] != '') {
                    if (ereg($campo, $encabezado[$i]['codigoGrupoNomina']) === false) {
                        $errores[$con]["linea"] = (4 + $i);
                        $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                        $errores[$con]["Descripcion"] = 'El grupo de nomina debe ser un dato alfanumerico';
                        $con++;
                    }
                }

                if ($encabezado[$i]['Periodo_idPeriodo'] == 0 and $encabezado[$i]['periodoContable'] != '') {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El periodo con la fecha de corte ' . $encabezado[$i]['periodoContable'] . ' no existe';
                    $con++;
                }

                if ($encabezado[$i]['GrupoNomina_idGrupoNomina'] == 0 and $encabezado[$i]['codigoGrupoNomina'] != '') {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El grupo de nomina con el codigo ' . $encabezado[$i]['codigoGrupoNomina'] . ' no existe';
                    $con++;
                }
            }

            for ($i = 0; $i < count($detalle); $i++) {

                if ($detalle[$i]['anticipadasVacaciones'] != 0 and $detalle[$i]['anticipadasVacaciones'] != 1) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'Para las vacaciones anticipadas el valor debe ser (0 o 1)';
                    $con++;
                }



                if ($detalle[$i]['documentoEmpleado'] == '') {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El documento del empleado no puede estar vacio';
                    $con++;
                }

                if ($detalle[$i]['Contrato_idContrato'] == 0) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'No existe un contrato vigente para el empleado con documento ' . $detalle[$i]['documentoEmpleado'];
                    $con++;
                }

                if ($detalle[$i]['Tercero_idTercero'] == 0 and $detalle[$i]['documentoEmpleado'] != '') {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El empleado con el documento ' . $detalle[$i]['documentoEmpleado'] . ' no existe o esta inactivo';
                    $con++;
                }

                $time = strtotime(trim($detalle[$i]['fechaInicio']));

                $is_valid = date('Y-m-d', $time) == trim($detalle[$i]['fechaInicio']);

                if (!$is_valid) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha  ' . $detalle[$i]['fechaInicio'] . ' es invalida';
                    $con++;
                }

                if ($detalle[$i]['diasTiempo'] < 0 or ! is_double($detalle[$i]['diasTiempo'])) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La Cantidad los dias en tiempo para el empleado con el documento ' . $detalle[$i]['documentoEmpleado'] . ' debe ser mayor o igual a cero';
                    $con++;
                }

                if ($detalle[$i]['diasDinero'] < 0 or ! is_double($detalle[$i]['diasDinero'])) {

                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La Cantidad los dias en dinero para el empleado con el documento ' . $detalle[$i]['diasDinero'] . ' debe ser mayor o igual a cero';
                    $con++;
                }

                for ($s = 0; $s < count($detalle); $s++) {
                    if ($s != $i) {
                        if (($detalle[$i]['documentoEmpleado'] == $detalle[$s]['documentoEmpleado']) and ( $detalle[$i]['numeroVacaciones'] == $detalle[$s]['numeroVacaciones'])) {
                            $errores[$con]["linea"] = (4 + $i);
                            $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                            $errores[$con]["Descripcion"] = 'El empleado con documento  ' . $detalle[$i]['documentoEmpleado'] . ' se repite en las lineas (' . ($i + 4) . ') y (' . ($s + 4) . ')';
                            $con++;
                        }
                    }
                }
            }

            return $errores;
        }


        function importarProgramacionTurno($ruta) {
            set_time_limit(0);


            include('../clases/PHPExcel/Classes/PHPExcel.php');

            /* Incluimos el fichero de la clase Db */
            require_once'db.class.php';
            /* Incluimos el fichero de la clase Conf */
            require_once'conf.class.php';




            $bd = Db::getInstance();

            $rutacompleta = explode(".", $ruta); //echo '3';
            $extension = array_pop($rutacompleta); //echo '4';
            if (!isset($objReader)) {
                if ($extension == 'xlsx') {
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007'); /* echo 'xlsx'; */
                } else {
                    $objReader = PHPExcel_IOFactory::createReader('Excel5'); /* echo 'xls'; */
                }
            }
            //echo '5';
            $objReader->setLoadSheetsOnly('datos'); //echo '6';
            $objReader->setReadDataOnly(true); //echo '7'.$ruta;
            $objPHPExcel = $objReader->load($ruta); //echo '8';

            $objWorksheet = $objPHPExcel->getActiveSheet(); //echo '9';
            $highestRow = $objWorksheet->getHighestRow(); //echo '10';// e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); //echo '11';// e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //echo '12';// e.g.

            $encabezado = array();
            $detalle = array();
            $posRef = -1;
            $posDet = -1;


            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $numeroAnt = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue();



                $posRef++;


                for ($columna = 0; $columna <= 5; $columna++) {

                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }


                //consultamso el id de la programcion
                $sql = "select idProgramacionTurno from ProgramacionTurno where numeroProgramacionTurno = '".$encabezado[$posRef]['numeroProgramacionTurno']."'";
                $datos = $bd->ConsultarVista($sql);

                $encabezado[$posRef]['idProgramacionTurno'] = 0;

                if (isset($datos[0]['idProgramacionTurno']) and $datos[0]['idProgramacionTurno'] > 0) {
                        $encabezado[$posRef]['idProgramacionTurno'] = $datos[0]['idProgramacionTurno'];
                }

               // echo $sql.'<br>';



                while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
                $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL and
                $numeroAnt == $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue()) {
                    $posDet++;


                    // llenamos la columna del numero de movimiento que es la que se encarga de enlazar el encabezado con su detalle correspondiente
                    $detalle[$posDet]["numeroProgramacionTurno"] = $numeroAnt;


                    for ($columna = 6; $columna <= 8; $columna++) {
                        // en la fila 2 del archivo de excel (oculta) estan los nombres de los campos de la tabla
                        $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                        $detalle[$posDet][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                    }

                    $detalle[$posDet]['Tercero_idTercero'] = 0;
                    $detalle[$posDet]['Turno_idTurno'] = 0;


                    $sql = "select idTurno from Turno where codigoAlternoTurno = '".$detalle[$posDet]['codigoTurno']."'";
                    $datos = $bd->ConsultarVista($sql);

                    //echo $sql.'<br>';

                    if (isset($datos[0]['idTurno']) and $datos[0]['idTurno'] > 0) {
                        $detalle[$posDet]['Turno_idTurno'] = $datos[0]['idTurno'];
                    }

                    $sql = "select idTercero from Tercero where documentoTercero = '".$detalle[$posDet]['documentoEmpleado']."'";
                    $datos = $bd->ConsultarVista($sql);

                    //echo $sql.'<br>';

                    if (isset($datos[0]['idTercero']) and $datos[0]['idTercero'] > 0) {
                        $detalle[$posDet]['Tercero_idTercero'] = $datos[0]['idTercero'];
                    }

                    $fila++;
                }
            }

            unlink($ruta);
            $retorno = $this->llenarPropiedadesProgramacionTurno($encabezado, $detalle);
            return $retorno;
        }

        function llenarPropiedadesProgramacionTurno($encabezado, $detalle) {

            /* Incluimos el fichero de la clase Db */
            require_once'db.class.php';
            /* Incluimos el fichero de la clase Conf */
            require_once'conf.class.php';
            $bd = Db::getInstance();


            $errores = $this->validarProgramacionTurno($encabezado, $detalle);

            if (count($errores) == 0) {

                require_once('../clases/programacionturno.class.php');
                $programacion = new ProgramacionTurno();



                for ($i = 0; $i < count($encabezado); $i++) {

                    if ($encabezado[$i]['idProgramacionTurno'] == 0) {

                        $programacion->idProgramacionTurno = 0;
                        $programacion->numeroProgramacionTurno = $encabezado[$i]['numeroProgramacionTurno'];
                        $programacion->nombreProgramacionTurno = $encabezado[$i]['nombreProgramacionTurno'];
                        $programacion->fechaElaboracionProgramacionTurno = $encabezado[$i]['fechaElaboracionProgramacionTurno'] != '' ? $encabezado[$i]['fechaElaboracionProgramacionTurno'] : date('Y-m-d');
                        $programacion->fechaInicioProgramacionTurno = $encabezado[$i]['fechaInicioProgramacionTurno'] != '' ? $encabezado[$i]['fechaInicioProgramacionTurno'] : date('Y-m-d');
                        $programacion->fechaFinProgramacionTurno = $encabezado[$i]['fechaFinProgramacionTurno'] != '' ? $encabezado[$i]['fechaFinProgramacionTurno'] : date('Y-m-d');
                        $programacion->observacionProgramacionTurno = $encabezado[$i]['observacionProgramacionTurno'];

                        $programacion->idProgramacionTurno = $programacion->AdicionarProgramacionTurno();

                    } else {

                        $programacion->idProgramacionTurno = $encabezado[$i]['idProgramacionTurno'];
                    }

                    $sm = 0;


                    for ($j = 0; $j < count($detalle); $j++) {

                        if (isset($encabezado[$i]["numeroProgramacionTurno"]) and
                                isset($detalle[$j]["numeroProgramacionTurno"]) and
                                $encabezado[$i]["numeroProgramacionTurno"] == $detalle[$j]["numeroProgramacionTurno"]) {

                           $sql = "delete from ProgramacionTurnoDetalle where Tercero_idEmpleado = ".$detalle[$j]["Tercero_idTercero"]."
                                   and ProgramacionTurno_idProgramacionTurno = ".$programacion->idProgramacionTurno."";

                           $bd->ejecutar($sql);


                        $sql = "insert into ProgramacionTurnoDetalle(
                                                idProgramacionTurnoDetalle,
                                                ProgramacionTurno_idProgramacionTurno,
                                                Tercero_idEmpleado,
                                                Turno_idTurno,
                                                esExtraProgramacionTurnoDetalle)
                                        values
                                        (0,".$programacion->idProgramacionTurno.",".$detalle[$j]["Tercero_idTercero"].",".$detalle[$j]["Turno_idTurno"].",".$detalle[$j]["esExtraProgramacionTurnoDetalle"].")";


                        $bd->ejecutar($sql);


                        }
                    }
                }


            }

            return $errores;


        }

         function validarProgramacionTurno($encabezado, $detalle) {

            $errores = array();
            $con = 0;


            $campo = "^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$";

            for ($i = 0; $i < count($encabezado); $i++) {

                $time = strtotime(trim($encabezado[$i]['fechaElaboracionProgramacionTurno']));

                $is_valid = date('Y-m-d', $time) == trim($encabezado[$i]['fechaElaboracionProgramacionTurno']);

                if (!$is_valid) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha  de elaboracion ' . $encabezado[$i]['fechaElaboracionProgramacionTurno'] . ' es incorrecta';
                    $con++;
                }


                $time = strtotime(trim($encabezado[$i]['fechaInicioProgramacionTurno']));
                $is_valid = date('Y-m-d', $time) == trim($encabezado[$i]['fechaInicioProgramacionTurno']);

                if (!$is_valid) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha de inicio ' . $encabezado[$i]['fechaInicioProgramacionTurno'] . ' es incorrecta';
                    $con++;
                }


                $time = strtotime(trim($encabezado[$i]['fechaFinProgramacionTurno']));
                $is_valid = date('Y-m-d', $time) == trim($encabezado[$i]['fechaFinProgramacionTurno']);

                if (!$is_valid) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha  fin ' . $encabezado[$i]['fechaFinProgramacionTurno'] . ' es incorrecta';
                    $con++;
                }



                if ($encabezado[$i]['numeroProgramacionTurno'] != '') {
                    if (ereg($campo, $encabezado[$i]['numeroProgramacionTurno']) === false) {
                        $errores[$con]["linea"] = (4 + $i);
                        $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                        $errores[$con]["Descripcion"] = 'El numero de la programacion de turno debe ser un dato alfanumerico';
                        $con++;
                    }
                }



                if ($encabezado[$i]['nombreProgramacionTurno'] != '') {
                    if (ereg($campo, $encabezado[$i]['nombreProgramacionTurno']) === false) {
                        $errores[$con]["linea"] = (4 + $i);
                        $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                        $errores[$con]["Descripcion"] = 'El nombre de la programacion de turno debe ser un dato alfanumerico';
                        $con++;
                    }
                }
            }

            for ($i = 0; $i < count($detalle); $i++) {


                if ($detalle[$i]['documentoEmpleado'] == '') {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El documento del empleado no puede estar vacio';
                    $con++;
                }


                if ($detalle[$i]['Tercero_idTercero'] == 0 and $detalle[$i]['documentoEmpleado'] != '') {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El empleado con el documento ' . $detalle[$i]['documentoEmpleado'] . ' no existe o esta inactivo';
                    $con++;
                }

                if ($detalle[$i]['Turno_idTurno'] == 0 and $detalle[$i]['codigoTurno'] != '') {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El turno con el codigo ' . $detalle[$i]['codigoTurno'] . ' no existe';
                    $con++;
                }

                if ($detalle[$i]['esExtraProgramacionTurnoDetalle'] != 0 and $detalle[$i]['esExtraProgramacionTurnoDetalle'] != 1) {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'Para indicar si es programcion extra o no el valor debe ser (0: No, 1:Si)';
                    $con++;
                }


                for ($s = 0; $s < count($detalle); $s++) {
                    if ($s != $i) {
                        if (($detalle[$i]['documentoEmpleado'] == $detalle[$s]['documentoEmpleado']) and ( $detalle[$i]['numeroProgramacionTurno'] == $detalle[$s]['numeroProgramacionTurno'])) {
                            $errores[$con]["linea"] = (4 + $i);
                            $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                            $errores[$con]["Descripcion"] = 'El empleado con documento  ' . $detalle[$i]['documentoEmpleado'] . ' se repite en las lineas (' . ($i + 4) . ') y (' . ($s + 4) . ')';
                            $con++;
                        }
                    }
                }
            }

            return $errores;
        }


        function importarControlPorteria($ruta) {

            set_time_limit(0);


            include('../clases/PHPExcel/Classes/PHPExcel.php');

            /* Incluimos el fichero de la clase Db */
            require_once'db.class.php';
            /* Incluimos el fichero de la clase Conf */
            require_once'conf.class.php';

            $bd = Db::getInstance();

            $rutacompleta = explode(".", $ruta); //echo '3';
            $extension = array_pop($rutacompleta); //echo '4';
            if (!isset($objReader)) {
                if ($extension == 'xlsx') {
                    $objReader = PHPExcel_IOFactory::createReader('Excel2007'); /* echo 'xlsx'; */
                } else {
                    $objReader = PHPExcel_IOFactory::createReader('Excel5'); /* echo 'xls'; */
                }
            }
            //echo '5';
            $objReader->setLoadSheetsOnly('datos'); //echo '6';
            $objReader->setReadDataOnly(true); //echo '7'.$ruta;
            $objPHPExcel = $objReader->load($ruta); //echo '8';

            $objWorksheet = $objPHPExcel->getActiveSheet(); //echo '9';
            $highestRow = $objWorksheet->getHighestRow(); //echo '10';// e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); //echo '11';// e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); //echo '12';// e.g.

            $encabezado = array();
            $posRef = -1;
            $fila = 4;

            while ($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != '' and
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, $fila)->getValue() != NULL) {

                $posRef++;


                for ($columna = 0; $columna <= 5; $columna++) {

                    $campo = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, 2)->getValue();
                    $encabezado[$posRef][$campo] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($columna, $fila)->getValue();
                }

                $sql = "select idTercero from Tercero where documentoTercero = '".$encabezado[$posRef]['documentoEmpleado']."'";
                $dato = $bd->ConsultarVista($sql);

                $encabezado[$posRef]['idTercero'] = (isset($dato[0]['idTercero']) ? $dato[0]['idTercero'] : 0);


                //verificamos si el empleado ya ingreso
                $sql = "select idControlPorteria from ControlPorteria
                               where fechaIngresoControlPorteria = '".$encabezado[$posRef]['fechaIngreso']."'
                               and horaIngresoControlPorteria = '".$encabezado[$posRef]['horaIngreso']."'
                               and Tercero_idTercero = ".$encabezado[$posRef]['idTercero'];

                $dato = $bd->ConsultarVista($sql);

                $encabezado[$posRef]['idControlPorteria'] = (isset($dato[0]['idControlPorteria']) ? $dato[0]['idControlPorteria'] : 0);
                $fila++;
            }


            unlink($ruta);
            $retorno = $this->llenarPropiedadesControlPorteria($encabezado);
            return $retorno;
        }


        function llenarPropiedadesControlPorteria($encabezado) {

            $errores = $this->validarControlPorteria($encabezado);

            require_once'db.class.php';
            require_once'conf.class.php';
            $bd = Db::getInstance();

            if(count($errores) == 0)
            {
                require_once 'controlporteria.class.php';
                $control = new ControlPorteria();

                for ($i=0; $i < count($encabezado); $i++)
                {

                    $sql = "INSERT INTO ControlPorteria
                            (
                             idControlPorteria,
                             Visita_idVisita,
                             Compania_idCompania,
                             Tercero_idTercero,
                             fechaIngresoControlPorteria,
                             horaIngresoControlPorteria,
                             fechaSalidaControlPorteria,
                             horaSalidaControlPorteria,
                             descripcionControlPorteria,
                             puntoIngresoControlPorteria)VALUES(

                    0,0,0,
                    '".$encabezado[$i]['idTercero']."',
                    '".$encabezado[$i]['fechaIngreso']."',
                    '".$encabezado[$i]['horaIngreso']."',
                    '".$encabezado[$i]['fechaSalida']."',
                    '".$encabezado[$i]['horaSalida']."',
                    '".$encabezado[$i]['Descripcion ']."',0)";



                    $bd->ejecutar($sql);
                }
            }



            return $errores;

        }

        function validarControlPorteria($encabezado)
        {

            $errores = array();
            $con = 0;
            $campo = "^[A-Za-záéíóúüñÁÉÍÓÚÜÑ0-9%()+-/*$@#.?;:_ ]+$";
            $pattern="/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])[\:]([0-5][0-9])$/";

            for ($i = 0; $i < count($encabezado); $i++) {




                if($encabezado[$i]['documentoEmpleado'] != '' and $encabezado[$i]['idTercero'] == 0)
                {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'El tercero con el documento '.$encabezado[$i]['documentoEmpleado'].' no existe';
                    $con++;

                }

                if($encabezado[$i]['fechaIngreso'] == '')
                {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha  ingreso esta vacia';
                    $con++;

                }

                if($encabezado[$i]['fechaSalida'] == '')
                {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha salida esta vacia';
                    $con++;

                }

                if($encabezado[$i]['horaIngreso'] == '')
                {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La hora  ingreso esta vacia';
                    $con++;

                }

                if($encabezado[$i]['idControlPorteria'] > 0 and $encabezado[$i]['idTercero'] > 0)
                {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'Ya existe un registro con la hora y fecha espeficada para el empleado';
                    $con++;

                }

                if($encabezado[$i]['horaSalida'] == '')
                {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La hora salida esta vacia';
                    $con++;

                }

                $time = strtotime(trim($encabezado[$i]['fechaIngreso']));
                $is_valid = date('Y-m-d', $time) == trim($encabezado[$i]['fechaIngreso']);
                if (!$is_valid and $encabezado[$i]['fechaIngreso'] != '') {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha  ingreso ' . $encabezado[$i]['fechaIngreso'] . ' es incorrecta';
                    $con++;
                }


                $time = strtotime(trim($encabezado[$i]['fechaSalida']));
                $is_valid = date('Y-m-d', $time) == trim($encabezado[$i]['fechaSalida']);
                if (!$is_valid and $encabezado[$i]['fechaSalida'] != '') {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La fecha salida ' . $encabezado[$i]['fechaSalida'] . ' es incorrecta';
                    $con++;
                }


                 if(!preg_match($pattern,$encabezado[$i]['horaIngreso']) and $encabezado[$i]['horaIngreso'] != '')
                 {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La hora de ingreso ' . $encabezado[$i]['horaIngreso'] . ' es incorrecta';
                    $con++;
                 }

                if(!preg_match($pattern,$encabezado[$i]['horaSalida']) and $encabezado[$i]['horaSalida'] != '')
                 {
                    $errores[$con]["linea"] = (4 + $i);
                    $errores[$con]["numeroError"] = '' . ($con + 1) . '';
                    $errores[$con]["Descripcion"] = 'La hora de salida ' . $encabezado[$i]['horaSalida'] . ' es incorrecta';
                    $con++;
                 }



            }


            return $errores;

        }



//018000945555 3
     //   numeral 263

    }

?>
