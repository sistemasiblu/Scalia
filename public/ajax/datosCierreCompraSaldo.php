<?php

$AND = (isset($_GET["idProveedor"]) and $_GET["idProveedor"] != '') ? ' AND Tercero_idProveedor = '.$_GET["idProveedor"] : '';
 
$row = array();

    $sql=DB::Select("
        SELECT 
            idCompra,
            numeroCompra,
            valorCompra,
            nombreTemporadaCompra,
            estadoCompra,
            saldoFinalCarteraForward,
            Tercero_idProveedor,
            numeroForward,
            idForward
        FROM
            (SELECT 
                idCompra,
                    numeroCompra,
                    valorCompra,
                    nombreTemporadaCompra,
                    estadoCompra,
                    Tercero_idProveedor
            FROM
                (SELECT 
                idCompra,
                    numeroCompra,
                    valorCompra,
                    nombreTemporadaCompra,
                    estadoCompra,
                    Tercero_idProveedor
            FROM
                compra c
            GROUP BY numeroCompra , numeroVersionCompra
            ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
            GROUP BY numeroCompra) AS comp
                LEFT JOIN
            forwarddetalle fd ON comp.idCompra = fd.Compra_idCompra
                LEFT JOIN
            forward f on fd.Forward_idForward = f.idForward
                LEFT JOIN
            carteraforward cf ON comp.idCompra = cf.Compra_idCompra
                LEFT JOIN
            embarquedetalle ed ON comp.idCompra = ed.Compra_idCompra
        WHERE
            cf.Compra_idCompra IS NOT NULL
                AND estadoCompra = 'Cerrado'
                $AND
        GROUP BY numeroCompra, Forward_idForward
        HAVING saldoFinalCarteraForward > 0");

    foreach ($sql as $key => $value) 
    { 
        $row[$key][] = $value->numeroCompra; 
        $row[$key][] = $value->nombreTemporadaCompra;
        $row[$key][] = $value->saldoFinalCarteraForward;
        $row[$key][] = $value->numeroForward;
        $row[$key][] = $value->idCompra; 
        $row[$key][] = $value->idForward; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);

?>  
