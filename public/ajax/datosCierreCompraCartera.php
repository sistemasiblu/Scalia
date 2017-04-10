<?php

$AND = (isset($_GET["idProveedor"]) and $_GET["idProveedor"] != '') ? ' AND Tercero_idTercero = '.$_GET["idProveedor"] : '';
 
$row = array();

    $sql=DB::Select("
        SELECT 
            nombreDocumento,
            numeroMovimiento,
            numeroReferenciaExternoMovimiento,
            REPLACE(saldoCartera, '-', '') AS saldoCartera,
            idDocumento,
            idMovimiento,
            numeroCompra
        FROM
            Iblu.Cartera c
                LEFT JOIN
            Iblu.Movimiento m ON c.Movimiento_idMovimiento = m.idMovimiento
                LEFT JOIN
            cierrecompracartera ccc ON ccc.Movimiento_idMovimiento = m.idMovimiento
                LEFT JOIN
            Iblu.Documento d ON m.Documento_idDocumento = d.idDocumento
                LEFT JOIN
            embarquedetalle ed ON m.numeroReferenciaExternoMovimiento = ed.facturaEmbarqueDetalle
                LEFT JOIN
            compra c ON ed.Compra_idCompra = c.idCompra
        WHERE
            m.Documento_idDocumento IN (38 , 42)
                AND saldoCartera < 0
                $AND
                AND c.Periodo_idPeriodo = (SELECT 
                    idPeriodo
                FROM
                    Iblu.Periodo
                WHERE
                    CURDATE() >= fechaInicialPeriodo
                        AND CURDATE() <= fechaFinalPeriodo)");

    foreach ($sql as $key => $value) 
    { 
        $row[$key][] = $value->nombreDocumento; 
        $row[$key][] = $value->numeroMovimiento;
        $row[$key][] = $value->numeroReferenciaExternoMovimiento;  
        $row[$key][] = $value->saldoCartera;  
        $row[$key][] = $value->idDocumento; 
        $row[$key][] = $value->idMovimiento; 
        $row[$key][] = $value->numeroCompra; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);

?>  
