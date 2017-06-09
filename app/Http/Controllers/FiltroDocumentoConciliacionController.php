<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FiltroDocumentoConciliacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documento = DB::Select(
            "SELECT idDocumento AS id, nombreDocumento AS nombre
            FROM documentoconciliacion DC 
            LEFT JOIN ".\Session::get("baseDatosCompania").".Documento D 
            ON DC.Documento_idDocumento = D.idDocumento
            WHERE Compania_idCompania = ".\Session::get("idCompania")."
            ORDER BY codigoAlternoDocumento, nombreDocumento");

        $documento = $this->convertirArray($documento);


        $valorconciliacion = DB::Select(
            "SELECT nombreValorConciliacion as nombre, idValorConciliacion as id
            FROM valorconciliacion
            WHERE moduloValorConciliacion = 'comercial'
            ORDER BY nombreValorConciliacion");
        $valorconciliacion = $this->convertirArray($valorconciliacion);


        return view('filtrodocumentoconciliacion', 
            compact( 'documento', 'valorconciliacion'));    
    }    

    function convertirArray($dato)
    {
        $nuevo = array();
        //$nuevo[0] = 'Todos';
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

    function consultarInformacion()
    {
        $where = (isset($_GET["condicionGeneral"]) and $_GET["condicionGeneral"] != '') ? ' WHERE '.$_GET["condicionGeneral"] : '';
        $whereValores = (isset($_GET["condicionValorConciliacion"]) and $_GET["condicionValorConciliacion"] != '') ? ' AND '.$_GET["condicionValorConciliacion"] : '';

        $consultaValores = DB::Select("SELECT Documento_idDocumento, idValorConciliacion, campoValorConciliacion, cuentasLocalDocumentoConciliacionComercial
                                        FROM documentoconciliacion 
                                        LEFT JOIN documentoconciliacioncomercial 
                                        ON documentoconciliacion.idDocumentoConciliacion = documentoconciliacioncomercial.DocumentoConciliacion_idDocumentoConciliacion 
                                        LEFT JOIN valorconciliacion 
                                        ON documentoconciliacioncomercial.ValorConciliacion_idValorConciliacion = valorconciliacion.idValorConciliacion 
                                        WHERE Compania_idCompania = ".\Session::get("idCompania")." $whereValores 
                                        GROUP BY idDocumentoConciliacion,idValorConciliacion");

        $datosValores = array();

        foreach ($consultaValores as $key => $value) 
        {  
            foreach ($value as $datoscampo => $campo) 
            {
                $datosValores[$datoscampo][] = $campo;
            }                        
        }

        $sqlCampos = '';
        $whereCuentas = "";

        for($cont = 0; $cont < count($datosValores['campoValorConciliacion']); $cont++)
        {
            $sqlCampos .= "SUM(Movimiento.".$datosValores['campoValorConciliacion'][$cont]." * IF(Movimiento.tasaCambioMovimiento = 0, 1, Movimiento.tasaCambioMovimiento)) AS ".$datosValores['campoValorConciliacion'][$cont].",";
            
            $cuentas = $datosValores['cuentasLocalDocumentoConciliacionComercial'][$cont];
            $cuentas = explode(",", $cuentas);
            
            $estructuraWhere = "";
            for ($i=0; $i < count($cuentas); $i++) 
            { 
                // echo '---------- array cuentas por coma ---------<br>';
                // echo $cuentas[$i].'<br>';
                // echo '----------  ---------<br><br>';
                $cuentas2[$cont] = explode("-", $cuentas[$i]);

                if(count($cuentas2[$cont]) > 1)
                {
                    $estructuraWhere .= " (numeroCuentaContable BETWEEN ".$cuentas2[$cont][0]." AND ".$cuentas2[$cont][1].") OR ";
                }
                else
                {
                    $estructuraWhere .= " (numeroCuentaContable IN(".$cuentas2[$cont][0].")) OR ";
                }

            }
            
            $estructuraWhere = substr($estructuraWhere, 0, -3);

            // $whereCuentas[$cont] = " (idDocumento = ".$datosValores['Documento_idDocumento'][$cont]." AND idValorConciliacion = ".$datosValores['idValorConciliacion'][$cont]." AND (".$estructuraWhere.")) OR ";
            $whereCuentas .= " (idDocumento = ".$datosValores['Documento_idDocumento'][$cont]." AND idValorConciliacion = ".$datosValores['idValorConciliacion'][$cont]." AND (".$estructuraWhere.")) OR ";
        }

            $whereCuentas = substr($whereCuentas, 0, -3);

                // echo '---------- array cuentas por guion ---------<br>';
        echo '<pre>';
        echo($whereCuentas).'<br>';
        echo($sqlCampos).'<br>';
        // print_r($whereCuentas);
        echo '</pre>';
                // echo '----------  ---------<br><br>';
        // print_r($datosValores['campoValorConciliacion']);
        // echo $sqlCampos;
        

        // $datosValores = array();
        // // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
        // for ($i = 0, $c = count($consultaValores); $i < $c; ++$i) 
        // {
        //     $datosValores[$i] = (array) $consultaValores[$i];
        // }

        $sql = "SELECT Movimiento.DocumentoConcepto_idDocumentoConcepto, 
                    idMovimiento, 
                    idMovimientoContable, 
                    idMovimientoContableDetalle, 
                    idDocumento, 
                    idCuentaContable, 
                    idCuentaContableModelo, 
                    Documento.ModeloContable_idModeloContable, 
                    CuentaContableModelo.ModeloContable_idModeloContable, 
                    CuentaContableModelo.ConceptoContable_idConceptoContable, 
                    Movimiento.subtotalMovimiento, 
                    Movimiento.tasaCambioMovimiento,$sqlCampos 
                    valorTotalMovimiento, 
                    SUM(
                        MovimientoContableDetalle.debitosMovimientoContableDetalle
                    ), 
                    SUM(
                        MovimientoContableDetalle.creditosMovimientoContableDetalle
                    ),
                    valorconciliacion.campoValorConciliacion, cuentasLocalDocumentoConciliacionComercial, 
                                    cuentasNiifDocumentoConciliacionComercial
                FROM 
                    compania 
                    LEFT JOIN documentoconciliacion ON compania.idCompania = documentoconciliacion.Compania_idCompania 
                    LEFT JOIN documentoconciliacioncomercial ON documentoconciliacion.idDocumentoConciliacion = documentoconciliacioncomercial.DocumentoConciliacion_idDocumentoConciliacion 
                    LEFT JOIN valorconciliacion ON documentoconciliacioncomercial.ValorConciliacion_idValorConciliacion = valorconciliacion.idValorConciliacion 
                    LEFT JOIN Iblu.Documento ON documentoconciliacion.Documento_idDocumento = Documento.idDocumento 
                    LEFT JOIN Iblu.Movimiento ON Documento.idDocumento = Movimiento.Documento_idDocumento 
                    LEFT JOIN Iblu.DocumentoConcepto ON Movimiento.DocumentoConcepto_idDocumentoConcepto = DocumentoConcepto.idDocumentoConcepto 
                    LEFT JOIN Iblu.MovimientoContable ON Movimiento.idMovimiento = MovimientoContable.Movimiento_idMovimiento 
                    LEFT JOIN Iblu.CuentaContableModelo ON Documento.ModeloContable_idModeloContable = CuentaContableModelo.ModeloContable_idModeloContable 
                    AND Documento.idDocumento = CuentaContableModelo.Documento_idDocumento 
                    AND DocumentoConcepto.idDocumentoConcepto = CuentaContableModelo.DocumentoConcepto_idDocumentoConcepto 
                    LEFT JOIN Iblu.CuentaContable ON CuentaContableModelo.CuentaContable_idCuentaContable = CuentaContable.idCuentaContable AND $whereCuentas
                    LEFT JOIN Iblu.MovimientoContableDetalle ON MovimientoContable.idMovimientoContable = MovimientoContableDetalle.MovimientoContable_idMovimientoContable 
                    AND CuentaContable.idCuentaContable = MovimientoContableDetalle.CuentaContable_idCuentaContable 
                WHERE 
                    idCompania = ".\Session::get("idCompania")." 
                    AND Movimiento.Documento_idDocumento IN(19) 
                    AND Movimiento.fechaElaboracionMovimiento >= '2017-04-01' 
                    AND Movimiento.fechaElaboracionMovimiento <= '2017-04-30' 
                    AND Movimiento.estadoMovimiento = 'ACTIVO' 
                    AND CuentaContableModelo.ModeloContable_idModeloContable = 6 
                    AND CuentaContableModelo.ConceptoContable_idConceptoContable = 24 
                    AND CuentaContableModelo.Ano_idAno = 7 
                GROUP BY 
                    idDocumento, 
                    idValorConciliacion";

        echo ($sql);
        exit();


        $consulta = DB::Select("SELECT 
                                    Movimiento.DocumentoConcepto_idDocumentoConcepto, 
                                    idMovimiento, 
                                    idMovimientoContable, 
                                    idMovimientoContableDetalle, 
                                    idDocumento, 
                                    idCuentaContable, 
                                    idCuentaContableModelo, 
                                    Documento.ModeloContable_idModeloContable, 
                                    CuentaContableModelo.ModeloContable_idModeloContable, 
                                    CuentaContableModelo.ConceptoContable_idConceptoContable, 
                                    Movimiento.subtotalMovimiento, 
                                    Movimiento.tasaCambioMovimiento, 
                                    SUM(
                                        Movimiento.subtotalMovimiento * IF(
                                            Movimiento.tasaCambioMovimiento = 0, 
                                            1, Movimiento.tasaCambioMovimiento
                                        )
                                    ) AS subtotalMovimiento, 
                                    SUM(
                                        Movimiento.valorDescuentoMovimiento * IF(
                                            Movimiento.tasaCambioMovimiento = 0, 
                                            1, Movimiento.tasaCambioMovimiento
                                        )
                                    ) AS valorDescuentoMovimiento, 
                                    SUM(
                                        Movimiento.valorBaseMovimiento * IF(
                                            Movimiento.tasaCambioMovimiento = 0, 
                                            1, Movimiento.tasaCambioMovimiento
                                        )
                                    ) AS valorBaseMovimiento, 
                                    SUM(
                                        Movimiento.valorIvaMovimiento * IF(
                                            Movimiento.tasaCambioMovimiento = 0, 
                                            1, Movimiento.tasaCambioMovimiento
                                        )
                                    ) AS valorIvaMovimiento, 
                                    SUM(
                                        Movimiento.valorRetencionMovimiento * IF(
                                            Movimiento.tasaCambioMovimiento = 0, 
                                            1, Movimiento.tasaCambioMovimiento
                                        )
                                    ) AS valorRetencionMovimiento, 
                                    SUM(
                                        Movimiento.valorReteIvaMovimiento * IF(
                                            Movimiento.tasaCambioMovimiento = 0, 
                                            1, Movimiento.tasaCambioMovimiento
                                        )
                                    ) AS valorReteIvaMovimiento, 
                                    SUM(
                                        Movimiento.valorReteIcaMovimiento * IF(
                                            Movimiento.tasaCambioMovimiento = 0, 
                                            1, Movimiento.tasaCambioMovimiento
                                        )
                                    ) AS valorReteIcaMovimiento, 
                                    valorTotalMovimiento, 
                                    SUM(
                                        MovimientoContableDetalle.debitosMovimientoContableDetalle
                                    ), 
                                    SUM(
                                        MovimientoContableDetalle.creditosMovimientoContableDetalle
                                    ),
                                    valorconciliacion.campoValorConciliacion, cuentasLocalDocumentoConciliacionComercial, 
                                    cuentasNiifDocumentoConciliacionComercial
                                FROM 
                                    compania 
                                    LEFT JOIN documentoconciliacion ON compania.idCompania = documentoconciliacion.Compania_idCompania 
                                    LEFT JOIN documentoconciliacioncomercial ON documentoconciliacion.idDocumentoConciliacion = documentoconciliacioncomercial.DocumentoConciliacion_idDocumentoConciliacion 
                                    LEFT JOIN valorconciliacion ON documentoconciliacioncomercial.ValorConciliacion_idValorConciliacion = valorconciliacion.idValorConciliacion 
                                    LEFT JOIN Iblu.Documento ON documentoconciliacion.Documento_idDocumento = Documento.idDocumento 
                                    LEFT JOIN Iblu.Movimiento ON Documento.idDocumento = Movimiento.Documento_idDocumento 
                                    LEFT JOIN Iblu.DocumentoConcepto ON Movimiento.DocumentoConcepto_idDocumentoConcepto = DocumentoConcepto.idDocumentoConcepto 
                                    LEFT JOIN Iblu.MovimientoContable ON Movimiento.idMovimiento = MovimientoContable.Movimiento_idMovimiento 
                                    LEFT JOIN Iblu.CuentaContableModelo ON Documento.ModeloContable_idModeloContable = CuentaContableModelo.ModeloContable_idModeloContable 
                                    AND Documento.idDocumento = CuentaContableModelo.Documento_idDocumento 
                                    AND DocumentoConcepto.idDocumentoConcepto = CuentaContableModelo.DocumentoConcepto_idDocumentoConcepto 
                                    LEFT JOIN Iblu.CuentaContable ON CuentaContableModelo.CuentaContable_idCuentaContable = CuentaContable.idCuentaContable 
                                    LEFT JOIN Iblu.MovimientoContableDetalle ON MovimientoContable.idMovimientoContable = MovimientoContableDetalle.MovimientoContable_idMovimientoContable 
                                    AND CuentaContable.idCuentaContable = MovimientoContableDetalle.CuentaContable_idCuentaContable 
                                WHERE 
                                    idCompania = 2 
                                    AND Movimiento.Documento_idDocumento = 4 
                                    AND Movimiento.fechaElaboracionMovimiento >= '2017-04-01' 
                                    AND Movimiento.fechaElaboracionMovimiento <= '2017-04-30' 
                                    AND Movimiento.estadoMovimiento = 'ACTIVO' 
                                    AND CuentaContableModelo.ModeloContable_idModeloContable = 6 
                                    AND CuentaContableModelo.ConceptoContable_idConceptoContable = 24 
                                    AND CuentaContableModelo.Ano_idAno = 7 
                                GROUP BY 
                                    idDocumento, 
                                    idValorConciliacion");
        
        return view('formatos.impresionDocumentoConciliacion',compact('consulta'));
    }
}
