<?php 

include public_path().'/ajax/actualizarCartera.php';

    $fecha = $_POST['fecha'];

    #Borro los datos del periodo que consulté
    DB::Select('Delete from carteraforward
        WHERE Periodo_idPeriodo IN 
            (SELECT
                idPeriodo
            FROM
                Iblu.Periodo
            WHERE
                fechaInicialPeriodo <= "'.$fecha.'" AND fechaFinalPeriodo >= "'.$fecha.'")');

    #Consulto en el mes anterior el id de la compra y el saldo final e inserto en el mes actual el id de la compra, el saldo inicial y el saldo final
    #Le resto un mes a la fecha
    // $fechaAnt = date("Y-m-d",strtotime("$fecha - 1 months"));
    $fechaAnt = date("Y-m-d", strtotime("-1 MONTH", strtotime($fecha)));

    DB::Select('INSERT INTO carteraforward (Compra_idCompra, Periodo_idPeriodo, DocumentoFinanciero_idDocumentoFinanciero, saldoInicialCarteraForward, abonoCarteraForward, saldoFinalCarteraForward)
        (SELECT Compra_idCompra, 
        (SELECT idPeriodo FROM Iblu.Periodo WHERE fechaInicialPeriodo <= "'.$fecha.'" AND fechaFinalPeriodo >= "'.$fecha.'") as Periodo_idPeriodo,
        DocumentoFinanciero_idDocumentoFinanciero, 
        saldoFinalCarteraForward, 
        0, 
        saldoFinalCarteraForward 
        FROM carteraforward 
        WHERE saldoFinalCarteraForward > 0 and Periodo_idPeriodo = (SELECT idPeriodo FROM Iblu.Periodo WHERE fechaInicialPeriodo <= "'.$fechaAnt.'" AND fechaFinalPeriodo >= "'.$fechaAnt.'"))');

    #Consulto las compras del mes actual
    $compras = DB::Select(
        'SELECT 
            idCompra, numeroCompra, fechaCompra, valorCompra
        FROM
            (SELECT 
                numeroCompra, idCompra, fechaCompra, valorCompra
            FROM
                compra c
            GROUP BY idCompra , numeroVersionCompra
            ORDER BY numeroCompra , numeroVersionCompra DESC) AS comp
            where DATE_FORMAT(fechaCompra,"%m-%Y") = DATE_FORMAT("'.$fecha.'","%m-%Y")
        GROUP BY numeroCompra');

    #Como la consulta devuelvo varios registros, debo calcular registro por registro
    for ($i=0; $i < count($compras); $i++) 
    { 
        $recalculoC = get_object_vars($compras[$i]);

        #Calculo las compras del mes actual
        actualizarCartera('carga', 'compra', $recalculoC['idCompra'], '', $recalculoC['fechaCompra'], $recalculoC['valorCompra']);
    }

    #Consulto los documentos financieros del mes actual
    $documentos = DB::Select(
        'SELECT
    idDocumentoFinanciero, fechaNegociacionDocumentoFinanciero, IFNULL(SUM(valorPagoDocumentoFinancieroDetalle) , 0) AS valorTotalDocumentoFinanciero
        FROM
            documentofinanciero df
        LEFT JOIN documentofinancierodetalle dfd ON df.idDocumentoFinanciero = dfd.DocumentoFinanciero_idDocumentoFinanciero
        WHERE DATE_FORMAT(fechaNegociacionDocumentoFinanciero,"%m-%Y") = DATE_FORMAT("'.$fecha.'","%m-%Y")
        GROUP BY idDocumentoFinanciero');

    

    for ($i=0; $i < count($documentos); $i++) 
    { 
        $recalculoD = get_object_vars($documentos[$i]);

        actualizarCartera('carga', 'documentofinanciero', '', $recalculoD['idDocumentoFinanciero'], $recalculoD['fechaNegociacionDocumentoFinanciero'], $recalculoD['valorTotalDocumentoFinanciero']);
    }
    
    #Consulto los pagos del mes actual 
    $pagos = DB::Select(
        'SELECT Compra_idCompra, valorPagadoPagoForwardDetalle, fechaPagoForward, DocumentoFinanciero_idDocumentoFinanciero 
        FROM pagoforwarddetalle pfd 
        LEFT JOIN pagoforward pf on pf.idPagoForward = pfd.PagoForward_idPagoForward 
        where DATE_FORMAT(fechaPagoForward,"%m-%Y") = DATE_FORMAT("'.$fecha.'","%m-%Y")');
    
    #Como la consulta devuelvo varios registros, debo calcular registro por registro
    for ($i=0; $i < count($pagos); $i++) 
    { 
        $recalculoP = get_object_vars($pagos[$i]);

        #Calculo los datos del mes actual
        actualizarCartera('carga', 'pago', $recalculoP['Compra_idCompra'], $recalculoP['DocumentoFinanciero_idDocumentoFinanciero'], $recalculoP['fechaPagoForward'], $recalculoP['valorPagadoPagoForwardDetalle']);
    }

echo json_encode('Cartera recalculada extisosamente');
