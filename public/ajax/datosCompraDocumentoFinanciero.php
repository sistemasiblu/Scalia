<?php 

    $consulta = DB::Select("
        SELECT idCompra, numeroCompra, nombreProveedorCompra, valorCompra, facturaEmbarqueDetalle, saldoFinalCarteraForward
        FROM
            (SELECT 
                idCompra, numeroCompra, nombreProveedorCompra, valorCompra
            FROM
                compra c
            GROUP BY numeroCompra , numeroVersionCompra
            ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
        LEFT JOIN
        scalia.carteraforward AS cartf ON c.idCompra = cartf.Compra_idCompra
            AND cartf.Periodo_idPeriodo = (SELECT 
                idPeriodo
            FROM
                Iblu.Periodo
            WHERE
                fechaInicialPeriodo <= CURDATE()
                    AND fechaFinalPeriodo >= CURDATE())
            LEFT JOIN
        Iblu.Periodo P ON cartf.Periodo_idPeriodo = P.idPeriodo
        LEFT JOIN embarquedetalle ed ON c.idCompra = ed.Compra_idCompra
        WHERE saldoInicialCarteraForward > 0
        GROUP BY numeroCompra");

    $row = array();

    foreach ($consulta as $key => $value) 
    {  
        $value = get_object_vars($consulta[$key]); 

        $row[$key][] = $value['numeroCompra']; 
        $row[$key][] = $value['nombreProveedorCompra']; 
        $row[$key][] = $value['facturaEmbarqueDetalle'];   
        $row[$key][] = $value['valorCompra']; 
        $row[$key][] = $value['saldoFinalCarteraForward']; 
        $row[$key][] = $value['idCompra']; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>