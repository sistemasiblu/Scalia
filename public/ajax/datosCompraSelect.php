<?php

    $idDocumento = $_GET['idDocumento'];

    // $compra = \App\Compra::where('DocumentoImportacion_idDocumentoImportacion', "=", $idDocumento)->get();
 
    
    $campos = 'nombreTemporadaCompra, nombreProveedorCompra, numeroCompra, volumenCompra, valorCompra, cantidadCompra, cantidadEmbarcada, Faltante, pesoCompra, bultoCompra, fechaDeliveryCompra, idCompra, numeroVersionCompra, compradorVendedorCompra, estadoCompra, eventoCompra';

    $compra = DB::Select('SELECT '.
        $campos. '
    FROM
        (SELECT 
            nombreTemporadaCompra, nombreProveedorCompra, numeroCompra, volumenCompra, valorCompra, cantidadCompra, pesoCompra, bultoCompra, fechaDeliveryCompra, idCompra, numeroVersionCompra, compradorVendedorCompra, SUM(COALESCE(case when unidadFacturaEmbarqueDetalle = 0 then null else unidadFacturaEmbarqueDetalle end,unidadEmbarqueDetalle,0)) AS cantidadEmbarcada,
            cantidadCompra - SUM(COALESCE(case when unidadFacturaEmbarqueDetalle = 0 then null else unidadFacturaEmbarqueDetalle end,unidadEmbarqueDetalle,0)) AS Faltante, estadoCompra, eventoCompra
        FROM
        compra c
    LEFT JOIN embarquedetalle ed ON ed.Compra_idCompra = c.idCompra
    LEFT JOIN embarque e ON ed.Embarque_idEmbarque = e.idEmbarque
    WHERE
        c.DocumentoImportacion_idDocumentoImportacion = '.$idDocumento.' 
    GROUP BY numeroCompra , numeroVersionCompra
    ORDER BY numeroCompra , numeroVersionCompra DESC) AS comp
    GROUP BY numeroCompra
    HAVING estadoCompra = "Abierto"  and Faltante > 0');

    $row = array();

    foreach ($compra as $key => $value) 
    {  
        $datoscompra = get_object_vars($value);
        
        foreach ($value as $datoscompra => $campo) 
        {
            $row[$key][] = $campo;
        }                        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>