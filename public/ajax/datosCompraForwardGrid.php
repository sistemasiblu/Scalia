<?php

    $tipo = $_GET['tipo'];

    if ($tipo == 'forward') 
    {
        $compraforward = DB::Select('
        SELECT 
            idForward,
            numeroForward,
            descripcionForward,
            fechaVencimientoForward,
            valorDolarForward,
            numeroCompra,
            GROUP_CONCAT(facturaEmbarqueDetalle) AS facturaEmbarqueDetalle,
            GROUP_CONCAT(numeroDocumentoFinanciero) AS numeroDocumentoFinanciero,
            GROUP_CONCAT(IFNULL(fechaProrrogaDocumentoFinancieroProrroga,
                    fechaNegociacionDocumentoFinanciero)) as fechaDocumentoFinanciero,
            nombreProveedorCompra,
            IFNULL(valorRealForwardDetalle, valorCompra) AS valorCompra,
            IFNULL(nombreTemporadaCompra, nombreTemporada) AS nombreTemporadaCompra,
            idCompra
        FROM
            forward f
                LEFT JOIN
            forwarddetalle fd ON f.idForward = fd.Forward_idForward
                LEFT JOIN
            compra c ON c.idCompra = fd.Compra_idCompra
                LEFT JOIN
            embarquedetalle ed ON c.idCompra = ed.Compra_idCompra
                LEFT JOIN
            Iblu.Temporada t ON fd.Temporada_idTemporada = t.idTemporada
                LEFT JOIN
            documentofinancierodetalle dfd ON c.idCompra = dfd.Compra_idCompra
                LEFT JOIN
            documentofinanciero df ON dfd.DocumentoFinanciero_idDocumentoFinanciero = df.idDocumentoFinanciero
                LEFT JOIN
            (SELECT 
                MAX(fechaProrrogaDocumentoFinancieroProrroga) AS fechaProrrogaDocumentoFinancieroProrroga, DocumentoFinanciero_idDocumentoFinanciero
            FROM
                documentofinancieroprorroga
            GROUP BY DocumentoFinanciero_idDocumentoFinanciero) AS dfp ON df.idDocumentoFinanciero = dfp.DocumentoFinanciero_idDocumentoFinanciero
        GROUP BY idForward , idCompra
        ORDER BY numeroForward');
    }
    else if ($tipo == 'compra')
    {
        $compraforward = DB::Select('
        SELECT 
            idForward,
            numeroForward,
            descripcionForward,
            fechaVencimientoForward,
            valorDolarForward,
            idCompra,
            numeroCompra,
            CONCAT(facturaEmbarqueDetalle,",") as facturaEmbarqueDetalle,
            GROUP_CONCAT(numeroDocumentoFinanciero) AS numeroDocumentoFinanciero,
            GROUP_CONCAT(IFNULL(fechaProrrogaDocumentoFinancieroProrroga,
                    fechaNegociacionDocumentoFinanciero)) as fechaDocumentoFinanciero,
            nombreProveedorCompra,
            nombreProveedorCompra,
            IFNULL(valorRealForwardDetalle, valorCompra) AS valorCompra,
            nombreTemporadaCompra
        FROM
            (SELECT 
                idCompra,
                    numeroCompra,
                    nombreProveedorCompra,
                    valorCompra,
                    nombreTemporadaCompra
            FROM
                (SELECT 
                idCompra,
                    numeroCompra,
                    nombreProveedorCompra,
                    valorCompra,
                    nombreTemporadaCompra
            FROM
                compra c
            GROUP BY numeroCompra , numeroVersionCompra
            ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
            GROUP BY numeroCompra) AS comp
                LEFT JOIN
            forwarddetalle fd ON comp.idCompra = fd.Compra_idCompra
                LEFT JOIN
            forward f ON fd.Forward_idForward = f.idForward
                LEFT JOIN
            embarquedetalle ed on comp.idCompra = ed.Compra_idCompra
                LEFT JOIN
            documentofinancierodetalle dfd ON comp.idCompra = dfd.Compra_idCompra
                LEFT JOIN
            documentofinanciero df ON dfd.DocumentoFinanciero_idDocumentoFinanciero = df.idDocumentoFinanciero
                LEFT JOIN
            (SELECT 
                MAX(fechaProrrogaDocumentoFinancieroProrroga) AS fechaProrrogaDocumentoFinancieroProrroga,
                    DocumentoFinanciero_idDocumentoFinanciero
            FROM
                documentofinancieroprorroga
            GROUP BY DocumentoFinanciero_idDocumentoFinanciero) AS dfp ON df.idDocumentoFinanciero = dfp.DocumentoFinanciero_idDocumentoFinanciero
        ORDER BY numeroCompra, fechaDocumentoFinanciero DESC');
    }


    $row = array();

    foreach ($compraforward as $key => $value) 
    {  
        $valor = get_object_vars($value);

        // $row[$key][] = '';

        $row[$key][] = $valor['numeroForward'];
        $row[$key][] = $valor['descripcionForward'];
        $row[$key][] = $valor['fechaVencimientoForward'];
        $row[$key][] = $valor['valorDolarForward'];
        $row[$key][] = $valor['numeroCompra'];
        $row[$key][] = $valor['facturaEmbarqueDetalle'];
        $row[$key][] = $valor['numeroDocumentoFinanciero'];
        $row[$key][] = $valor['fechaDocumentoFinanciero'];
        $row[$key][] = $valor['nombreTemporadaCompra'];
        $row[$key][] = $valor['nombreProveedorCompra'];
        $row[$key][] = $valor['valorCompra'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>