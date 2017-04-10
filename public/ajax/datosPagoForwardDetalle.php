<?php

$idForward = $_GET['idForward'];
 
$row = array();

    $sql=DB::Select("
        SELECT 
            nombrePeriodo,
            nombreTemporada,
            idTemporada,
            '' AS numeroCompra,
            NULL AS idCompra,
            valorRealForwardDetalle,
            valorForwardDetalle,
            Forward_idForward,
            NULL AS idDocumentoFinanciero,
            '' AS numeroDocumentoFinanciero,
            SUM(IF(saldoInicialCarteraForward IS NULL,
                0,
                saldoInicialCarteraForward)) AS saldoInicialCarteraForward,
            SUM(IF(abonoCarteraForward IS NULL,
                0,
                abonoCarteraForward)) AS abonoCarteraForward,
            SUM(saldoInicialCarteraForward - abonoCarteraForward) AS saldoFinalCarteraForward
        FROM
            scalia.forwarddetalle fd
                LEFT JOIN
            Iblu.Temporada AS temp ON fd.Temporada_idTemporada = temp.idTemporada
                LEFT JOIN
            scalia.compra AS comp ON temp.idTemporada = comp.Temporada_idTemporada
                LEFT JOIN
            scalia.carteraforward AS cartf ON comp.idCompra = cartf.Compra_idCompra
                AND cartf.Periodo_idPeriodo = (SELECT 
                    idPeriodo
                FROM
                    Iblu.Periodo
                WHERE
                    fechaInicialPeriodo <= CURDATE()
                        AND fechaFinalPeriodo >= CURDATE())
                LEFT JOIN
            Iblu.Periodo P ON cartf.Periodo_idPeriodo = P.idPeriodo
        WHERE
            cartf.Compra_idCompra IS NOT NULL
                AND Forward_idForward = $idForward
                AND fd.Temporada_idTemporada IS NOT NULL
        GROUP BY comp.Temporada_idTemporada
        HAVING saldoFinalCarteraForward > 0 
        UNION SELECT 
            nombrePeriodo,
            nombreTemporada,
            idTemporada,
            numeroCompra,
            idCompra,
            valorRealForwardDetalle,
            valorForwardDetalle,
            Forward_idForward,
            NULL AS idDocumentoFinanciero,
            '' AS numeroDocumentoFinanciero,
            SUM(IF(saldoInicialCarteraForward IS NULL,
                0,
                saldoInicialCarteraForward)) AS saldoInicialCarteraForward,
            SUM(IF(abonoCarteraForward IS NULL,
                0,
                abonoCarteraForward)) AS abonoCarteraForward,
            SUM(saldoInicialCarteraForward - abonoCarteraForward) AS saldoFinalCarteraForward
        FROM
            scalia.forwarddetalle fd
                LEFT JOIN
            scalia.compra AS comp ON fd.Compra_idCompra = comp.idCompra
                LEFT JOIN
            Iblu.Temporada AS temp ON temp.idTemporada = comp.Temporada_idTemporada
                LEFT JOIN
            scalia.carteraforward AS cartf ON comp.idCompra = cartf.Compra_idCompra
                AND cartf.Periodo_idPeriodo = (SELECT 
                    idPeriodo
                FROM
                    Iblu.Periodo
                WHERE
                    fechaInicialPeriodo <= CURDATE()
                        AND fechaFinalPeriodo >= CURDATE())
                LEFT JOIN
            Iblu.Periodo P ON cartf.Periodo_idPeriodo = P.idPeriodo
        WHERE
            cartf.Compra_idCompra IS NOT NULL
                AND Forward_idForward = $idForward
                AND fd.Compra_idCompra IS NOT NULL
        GROUP BY comp.idCompra
        HAVING saldoFinalCarteraForward > 0 
        UNION SELECT 
            nombrePeriodo,
            '' AS nombreTemporada,
            NULL AS idTemporada,
            '' AS numeroCompra,
            NULL AS idCompra,
            valorPagoDocumentoFinancieroDetalle as valorRealForwardDetalle,
            valorFobDocumentoFinancieroDetalle as valorForwardDetalle,
            NULL AS Forward_idForward,
            idDocumentoFinanciero,
            numeroDocumentoFinanciero,
            SUM(IF(saldoInicialCarteraForward IS NULL,
                0,
                saldoInicialCarteraForward)) AS saldoInicialCarteraForward,
            SUM(IF(abonoCarteraForward IS NULL,
                0,
                abonoCarteraForward)) AS abonoCarteraForward,
            SUM(saldoInicialCarteraForward - abonoCarteraForward) AS saldoFinalCarteraForward
        FROM
            documentofinancierodetalle dfd
                LEFT JOIN
            documentofinanciero df ON dfd.DocumentoFinanciero_idDocumentoFinanciero = df.idDocumentoFinanciero
                LEFT JOIN
            carteraforward cartf ON df.idDocumentoFinanciero = cartf.DocumentoFinanciero_idDocumentoFinanciero
                AND cartf.Periodo_idPeriodo = (SELECT 
                    idPeriodo
                FROM
                    Iblu.Periodo
                WHERE
                    fechaInicialPeriodo <= CURDATE()
                        AND fechaFinalPeriodo >= CURDATE())
                LEFT JOIN
            Iblu.Periodo P ON cartf.Periodo_idPeriodo = P.idPeriodo
        WHERE
            cartf.DocumentoFinanciero_idDocumentoFinanciero IS NOT NULL
        GROUP BY df.idDocumentoFinanciero
        HAVING saldoFinalCarteraForward > 0");

    foreach ($sql as $key => $value) 
    { 
        $row[$key][] = $value->nombreTemporada; 
        $row[$key][] = '<a style="cursor:pointer;" onclick="mostrarDetalleCompras('.$value->idCompra.')">'.$value->numeroCompra.'</a>'; 
        $row[$key][] = $value->numeroDocumentoFinanciero;
        $row[$key][] = $value->valorForwardDetalle;  
        $row[$key][] = $value->saldoFinalCarteraForward;  
        $row[$key][] = $value->idTemporada; 
        $row[$key][] = $value->idCompra;
        $row[$key][] = $value->idDocumentoFinanciero;  
        $row[$key][] = $value->Forward_idForward; 
        $row[$key][] = $value->numeroCompra;
    }

    $output['aaData'] = $row;
    echo json_encode($output);

?>  