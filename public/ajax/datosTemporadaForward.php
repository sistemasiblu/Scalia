<?php 
	
	$reg = $_GET['reg'];
	
	$temporada = DB::Select('
		SELECT 
    idTemporada,
    idCompra,
    nombreTemporada,
    numeroCompra,
    SUM(valorCompra) AS valorCompra,
    SUM(IF(saldoInicialCarteraForward IS NULL,
        0,
        saldoInicialCarteraForward)) AS saldoInicialCarteraForward,
    SUM(IFNULL(abonoCarteraForward, 0)) AS abonoCarteraForward,
    SUM(IF(saldoInicialCarteraForward IS NULL,
        0,
        saldoInicialCarteraForward) - IF(abonoCarteraForward IS NULL,
        0,
        abonoCarteraForward)) AS saldoFinalCarteraForward
FROM
    Iblu.Temporada temp
        INNER JOIN
    (SELECT 
        *
    FROM
        (SELECT 
        Temporada_idTemporada,
            numeroCompra,
            valorCompra,
            idCompra,
            numeroVersionCompra
    FROM
        compra c
    GROUP BY numeroCompra , numeroVersionCompra
    ORDER BY numeroCompra , numeroVersionCompra DESC) AS temp
    GROUP BY numeroCompra) AS comp ON temp.idTemporada = comp.Temporada_idTemporada
        LEFT JOIN
    scalia.carteraforward AS cartf ON comp.idCompra = cartf.Compra_idCompra
        AND cartf.Periodo_idPeriodo = (SELECT 
            idPeriodo
        FROM
            Iblu.Periodo
        WHERE
            fechaInicialPeriodo <= CURDATE()
                AND fechaFinalPeriodo >= CURDATE())
	GROUP BY idTemporada');

    $row = array();

    foreach ($temporada as $key => $value) 
    { 
    	$value = get_object_vars($temporada[$key]);

        $row[$key][] = $value['nombreTemporada']; 
        $row[$key][] = $value['valorCompra']; 
        $row[$key][] = $value['abonoCarteraForward']; 
        $row[$key][] = $value['saldoFinalCarteraForward']; 
        $row[$key][] = $value['idTemporada']; 
        $row[$key][] = $reg;
    }

    $output['aaData'] = $row;
    echo json_encode($output);

?>