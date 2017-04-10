<?php 

$idCompra = $_POST['idCompra'];
$idForward = $_POST['idForward'];

$im = DB::Select ("
	SELECT 
        if(c.Temporada_idTemporada = '1',null,c.Temporada_idTemporada) as idTemporada,
        '' as nombreTemporadaCompra,
        idCompra,
        numeroCompra,
        facturaEmbarqueDetalle,
        fechaRealEmbarqueDetalle,
        valorFacturaEmbarqueDetalle,
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
            abonoCarteraForward) AS saldoFinalCarteraForward
    FROM
        embarquedetalle ed
            LEFT JOIN
        compra c ON ed.Compra_idCompra = c.idCompra
            LEFT JOIN
        carteraforward AS cartf ON c.idCompra = cartf.Compra_idCompra
            LEFT JOIN
        forwarddetalle fd on c.idCompra = fd.Compra_idCompra
    WHERE
        ed.Compra_idCompra = $idCompra
    GROUP BY facturaEmbarqueDetalle
    HAVING saldoFinalCarteraForward > 0");

echo json_encode($im);

?>