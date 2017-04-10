<?php

  
    $compraforward = DB::Select('
    SELECT
        idCompra,
        nombreTemporadaCompra,
        nombreProveedorCompra,
        nombreClienteCompra,
        numeroCompra,
        valorCompra,
        SUM(valorFacturaEmbarqueDetalle) AS valorFaltante,
        cantidadCompra,
        SUM(unidadFacturaEmbarqueDetalle) AS cantidadFaltante,
        nombreCiudadCompra,
        volumenCompra,
        fechaDeliveryCompra,
        fechaForwardCompra,
        tiempoBodegaCompra,
        diaPagoClienteCompra,
        fechaReservaEmbarqueDetalle,
        fechaRealEmbarque,
        fechaArriboPuertoEstimadaEmbarqueDetalle,
        diasCiudadTipoTransporte,
        fechaLlegadaZonaFrancaEmbarqueDetalle,
        (((fechaForwardCompra - INTERVAL diasCiudadTipoTransporte DAY) - INTERVAL tiempoBodegaCompra DAY) - INTERVAL IFNULL(diasFormaPago, 0) DAY) AS fechaMaximaCliente,
        (((fechaRealEmbarque - INTERVAL tiempoBodegaCompra DAY) - INTERVAL diaPagoClienteCompra DAY) - INTERVAL diasCiudadTipoTransporte DAY) AS fechaMaximaEmbarqueCumplirForward,
        Temporada_idTemporada,
        Tercero_idCliente,
        Tercero_idProveedor,
        Ciudad_idPuerto,
        fechaCompra,
        nombreDocumentoImportacion
    FROM
        (SELECT 
            idCompra,
                nombreTemporadaCompra,
                nombreProveedorCompra,
                nombreClienteCompra,
                numeroCompra,
                valorCompra,
                cantidadCompra,
                nombreCiudadCompra,
                volumenCompra,
                fechaDeliveryCompra,
                fechaForwardCompra,
                tiempoBodegaCompra,
                diaPagoClienteCompra,
                Temporada_idTemporada,
                Tercero_idCliente,
                Tercero_idProveedor,
                Ciudad_idPuerto,
                fechaCompra,
                nombreDocumentoImportacion,
                formaPagoClienteCompra
        FROM
            (SELECT 
            numeroVersionCompra,
                idCompra,
                nombreTemporadaCompra,
                nombreProveedorCompra,
                nombreClienteCompra,
                numeroCompra,
                valorCompra,
                cantidadCompra,
                nombreCiudadCompra,
                volumenCompra,
                fechaDeliveryCompra,
                fechaForwardCompra,
                tiempoBodegaCompra,
                diaPagoClienteCompra,
                Temporada_idTemporada,
                Tercero_idCliente,
                Tercero_idProveedor,
                Ciudad_idPuerto,
                fechaCompra,
                nombreDocumentoImportacion,
                formaPagoClienteCompra
        FROM
            compra c
        LEFT JOIN documentoimportacion di ON di.idDocumentoImportacion = c.DocumentoImportacion_idDocumentoImportacion
        GROUP BY numeroCompra , numeroVersionCompra
        ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
        GROUP BY numeroCompra) AS comp
            LEFT JOIN
        embarquedetalle ed ON comp.idCompra = ed.Compra_idCompra
            LEFT JOIN
        embarque e ON e.idEmbarque = ed.Embarque_idEmbarque
            LEFT JOIN
        Iblu.CiudadTipoTransporte ctt ON ctt.Ciudad_idCiudad = e.Ciudad_idPuerto_Carga
            LEFT JOIN
        Iblu.Movimiento m ON ed.facturaEmbarqueDetalle = m.numeroReferenciaExternoMovimiento
            AND m.Documento_idDocumento = 20
            LEFT JOIN
        (SELECT 
            Movimiento_idMovimiento, idMercanciaExtranjeraDetalle
        FROM
            Iblu.MercanciaExtranjeraDetalle
        GROUP BY Movimiento_idMovimiento) med ON m.idMovimiento = med.Movimiento_idMovimiento
            LEFT JOIN
        Iblu.FormaPago fp on comp.formaPagoClienteCompra = fp.nombreFormaPago
    GROUP BY numeroCompra , numeroEmbarque
    ORDER BY nombreDocumentoImportacion , nombreClienteCompra ASC');

    $row = array();

    foreach ($compraforward as $key => $value) 
    {  
        $valor = get_object_vars($value);

        $row[$key][] = $valor['nombreClienteCompra'];
        $row[$key][] = $valor['numeroCompra'];
        $row[$key][] = $valor['nombreProveedorCompra'];
        $row[$key][] = $valor['valorCompra'];
        $row[$key][] = $valor['valorFaltante'];
        $row[$key][] = $valor['cantidadCompra'];
        $row[$key][] = $valor['cantidadFaltante'];
        $row[$key][] = $valor['nombreCiudadCompra'];
        $row[$key][] = $valor['volumenCompra'];
        $row[$key][] = $valor['fechaDeliveryCompra'];
        $row[$key][] = $valor['fechaForwardCompra'];
        $row[$key][] = $valor['tiempoBodegaCompra'];
        $row[$key][] = $valor['diaPagoClienteCompra'];
        $row[$key][] = $valor['fechaReservaEmbarqueDetalle'];
        $row[$key][] = $valor['fechaRealEmbarque'];
        $row[$key][] = $valor['fechaArriboPuertoEstimadaEmbarqueDetalle'];
        $row[$key][] = $valor['diasCiudadTipoTransporte'];
        $row[$key][] = $valor['fechaMaximaCliente'];
        $row[$key][] = $valor['fechaMaximaEmbarqueCumplirForward'];
        $row[$key][] = $valor['fechaLlegadaZonaFrancaEmbarqueDetalle'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>