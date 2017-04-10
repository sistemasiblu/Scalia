<?php 
	
	$reg = $_GET['reg'];
	
	$compra = DB::Select('
		SELECT 
		    idCompra,
		    numeroCompra,
		    nombreTemporadaCompra,
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
		        comp.Temporada_idTemporada,
		        fechaCompra
		FROM
		    (SELECT 
		        numeroCompra,
		            nombreTemporadaCompra,
		            valorCompra,
		            idCompra,
		            nombreProveedorCompra,
		            numeroVersionCompra,
		            Temporada_idTemporada,
		            fechaCompra
		    FROM
		        (SELECT 
		        numeroCompra,
		            nombreTemporadaCompra,
		            valorCompra,
		            idCompra,
		            nombreProveedorCompra,
		            numeroVersionCompra,
		            Temporada_idTemporada,
		            fechaCompra
		    FROM
		        compra c
		    LEFT JOIN documentoimportacion di ON di.idDocumentoImportacion = c.DocumentoImportacion_idDocumentoImportacion
		    GROUP BY numeroCompra , numeroVersionCompra
		    ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
		    GROUP BY numeroCompra) AS comp
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
		    -- WHERE
   			-- 	fechaCompra <= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
		GROUP BY numeroCompra
		HAVING saldoFinalCarteraForward > 0');

    $row = array();

    foreach ($compra as $key => $value) 
    { 
    	$value = get_object_vars($compra[$key]);

    	$row[$key][] = $value['nombreTemporadaCompra']; 
        $row[$key][] = $value['numeroCompra']; 
        $row[$key][] = $value['nombreProveedorCompra'];   
        $row[$key][] = $value['valorCompra']; 
        $row[$key][] = $value['abonoCarteraForward']; 
        $row[$key][] = $value['saldoFinalCarteraForward']; 
        $row[$key][] = $value['idCompra']; 
        $row[$key][] = $value['numeroVersionCompra']; 
        $row[$key][] = $reg;
        $row[$key][] = $value['Temporada_idTemporada']; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);

?>