<?php 

    $consulta = DB::Select("
        SELECT idCompra, numeroCompra, valorCompra, facturaEmbarqueDetalle
        FROM
            (SELECT 
                idCompra, numeroCompra, valorCompra
            FROM
                compra c
            GROUP BY numeroCompra , numeroVersionCompra
            ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
        LEFT JOIN carteraforward cf ON c.idCompra = cf.Compra_idCompra
        LEFT JOIN embarquedetalle ed ON c.idCompra = ed.Compra_idCompra
        WHERE saldoInicialCarteraForward > 0
        GROUP BY numeroCompra");

    $row = array();

    foreach ($consulta as $key => $value) 
    {  
        $value = get_object_vars($consulta[$key]); 

        $row[$key][] = $value['numeroCompra']; 
        $row[$key][] = $value['facturaEmbarqueDetalle'];   
        $row[$key][] = $value['valorCompra']; 
        $row[$key][] = $value['valorCompra']; 
        $row[$key][] = $value['idCompra']; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>