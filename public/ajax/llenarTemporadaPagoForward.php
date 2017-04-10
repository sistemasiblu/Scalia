<?php 

$idTemporada = $_POST['idTemporada'];
$idForward = $_POST['idForward'];

$compras = DB::Select ('
	SELECT 
        idTemporada,
        nombreTemporada,
        idCompra,
        numeroCompra,
        '.$idForward.' as idForward,
        valorCompra,
        IF(valorRealForwardDetalle IS NULL,
            0,
            valorRealForwardDetalle) AS valorRealForwardDetalle,
        IF(saldoInicialCarteraForward IS NULL,
            0,
            saldoInicialCarteraForward) AS saldoInicialCarteraForward,
        IF(abonoCarteraForward IS NULL,
            0,
            abonoCarteraForward) AS abonoCarteraForward,
        IF(saldoInicialCarteraForward IS NULL,
            0,
            saldoInicialCarteraForward) - IF(abonoCarteraForward IS NULL,
            0,
            abonoCarteraForward) AS saldoFinalCarteraForward,
        facturaEmbarqueDetalle,
        valorFacturaEmbarqueDetalle,
        fechaRealEmbarqueDetalle
	FROM
	    Iblu.Temporada temp
	        INNER JOIN
	    (SELECT 
	        Temporada_idTemporada, numeroCompra, valorCompra, idCompra
	    FROM
	        compra c
	    GROUP BY numeroCompra , numeroVersionCompra
	    ORDER BY numeroCompra , numeroVersionCompra DESC) AS comp ON temp.idTemporada = comp.Temporada_idTemporada
	        LEFT JOIN
	    forwarddetalle fd ON temp.idTemporada = fd.Temporada_idTemporada
	        LEFT JOIN
	    scalia.carteraforward AS cartf ON comp.idCompra = cartf.Compra_idCompra
            LEFT JOIN
        embarquedetalle ed ON comp.idCompra = ed.Compra_idCompra
	    where idTemporada = '.$idTemporada.'
	GROUP BY numeroCompra, facturaEmbarqueDetalle
	HAVING saldoFinalCarteraForward > 0');

echo json_encode($compras);

?>