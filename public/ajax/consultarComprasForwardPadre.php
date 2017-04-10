<?php 

$idForwardPadre = $_POST['idForwardPadre'];

$comprasForward = DB::Select('
	SELECT 
	    fd.Compra_idCompra,
	    fd.numeroCompraForwardDetalle,
	    fd.nombreTemporadaForwardDetalle,
	    valorCompra,
	    nombreProveedorCompra,
	    numeroVersionCompra,
	    IF(valorRealForwardDetalle IS NULL,
	        0,
	        valorRealForwardDetalle) AS valorRealForwardDetalle,
	    SUM(IF(saldoInicialCarteraForward IS NULL,
	        0,
	        saldoInicialCarteraForward)) AS saldoInicialCarteraForward,
	    SUM(IF(abonoCarteraForward IS NULL,
	        0,
	        abonoCarteraForward)) AS abonoCarteraForward,
	    SUM(IF(saldoInicialCarteraForward IS NULL,
	        0,
	        saldoInicialCarteraForward) - IF(abonoCarteraForward IS NULL,
	        0,
	        abonoCarteraForward)) AS saldoFinalCarteraForward,
	        fd.Temporada_idTemporada,
            fd.nombreTemporadaForwardDetalle,
	        fechaCompra
	FROM
	    (SELECT 
	        numeroCompra, nombreTemporadaCompra, valorCompra, idCompra, nombreProveedorCompra, numeroVersionCompra, Temporada_idTemporada, fechaCompra
	    FROM
	        compra c
	    GROUP BY numeroCompra , numeroVersionCompra
	    ORDER BY numeroCompra , numeroVersionCompra DESC) AS comp
	        LEFT JOIN
	    forwarddetalle fd ON comp.idCompra = fd.Compra_idCompra
	        LEFT JOIN
	    scalia.carteraforward AS cartf ON comp.idCompra = cartf.Compra_idCompra
		    AND cartf.Periodo_idPeriodo = (SELECT 
	                idPeriodo
	            FROM
	                Iblu.Periodo
	            WHERE
	                fechaInicialPeriodo <= CURDATE()
	                    AND fechaFinalPeriodo >= CURDATE())
	    /*WHERE
				fechaCompra <= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)*/
		WHERE Forward_idForward = '.$idForwardPadre.'
	GROUP BY numeroCompra
	HAVING saldoFinalCarteraForward > 0');

echo json_encode($comprasForward);

?>