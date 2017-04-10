<?php

    $idDocumento = $_GET['idDocumento'];

    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];
    $actualizar = $_GET['actualizar'];

    $visibleM = '';
    $visibleE = '';
    if ($modificar == 1) 
        $visibleM = 'inline-block;';
    else
        $visibleM = 'none;';

    if ($eliminar == 1) 
        $visibleE = 'inline-block;';
    else
        $visibleE = 'none;'; 

    if ($actualizar == 1) 
        $visibleA = 'inline-block;';
    else
        $visibleA = 'none;'; 
    
    $campos = 'numeroVersionCompra, nombreTemporadaCompra, nombreProveedorCompra, nombreClienteCompra, numeroCompra, valorCompra, cantidadCompra, cantidadEmbarcada, Faltante, estadoCompra, name, idCompra, Temporada_idTemporada';

    $compra = DB::Select('SELECT '.
        $campos. '
    FROM
        (SELECT 
            numeroVersionCompra, nombreTemporadaCompra, nombreProveedorCompra, nombreClienteCompra, numeroCompra, valorCompra, cantidadCompra, idCompra, SUM(COALESCE(case when unidadFacturaEmbarqueDetalle = 0 then null else unidadFacturaEmbarqueDetalle end,unidadEmbarqueDetalle,0)) AS cantidadEmbarcada,
            cantidadCompra - SUM(COALESCE(case when unidadFacturaEmbarqueDetalle = 0 then null else unidadFacturaEmbarqueDetalle end,unidadEmbarqueDetalle,0)) AS Faltante, estadoCompra, Usuario_idUsuario, Temporada_idTemporada
        FROM
        compra c
    LEFT JOIN embarquedetalle ed ON ed.Compra_idCompra = c.idCompra
    LEFT JOIN embarque e ON ed.Embarque_idEmbarque = e.idEmbarque
    WHERE
        c.DocumentoImportacion_idDocumentoImportacion = '.$idDocumento.' 
    GROUP BY numeroCompra , numeroVersionCompra
    ORDER BY numeroCompra , numeroVersionCompra DESC) AS comp
    LEFT JOIN users u ON comp.Usuario_idUsuario = u.id
    GROUP BY numeroCompra');

    $row = array();

    foreach ($compra as $key => $value) 
    {  
        $datoscompra = get_object_vars($value);
        $row[$key][] = '<a href="compra/'.$datoscompra["idCompra"].'/edit?idDocumento='.$idDocumento.'&accion=editar">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.

                        '<a href="compra/'.$datoscompra["idCompra"].'/edit?accion=eliminar&idDocumento='.$idDocumento.'">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>&nbsp;'.

                        '<a onclick="actualizarEstado('.$datoscompra["idCompra"].');">'.
                            '<span class="glyphicon glyphicon-lock" style="cursor:pointer; display: '.$visibleA.'"></span>'.
                        '</a>&nbsp;'.

                        '<a onclick="imprimirFormatoCompra('.$datoscompra["idCompra"].',\''.$datoscompra["numeroCompra"].'\','.$idDocumento.',\'compra\');">'.
                            '<span class="glyphicon glyphicon-print" style="cursor:pointer; display: '.$visibleA.'"></span>'.
                        '</a>&nbsp;';
                        $row[$key][] = $datoscompra['numeroVersionCompra'];
                        $row[$key][] = '<a title="Ver detalles de la temporada" style="cursor:pointer;" onclick="mostrarDetalleTemporada('.$datoscompra["Temporada_idTemporada"].')">'.$datoscompra["nombreTemporadaCompra"].'</a>'; 
                        $row[$key][] = $datoscompra['nombreProveedorCompra'];
                        $row[$key][] = $datoscompra['nombreClienteCompra'];
                        $row[$key][] = $datoscompra['numeroCompra'];
                        $row[$key][] = $datoscompra['valorCompra'];
                        $row[$key][] = $datoscompra['cantidadCompra'];
                        $row[$key][] = $datoscompra['cantidadEmbarcada'];
                        $row[$key][] = $datoscompra['Faltante'];
                        $row[$key][] = $datoscompra['estadoCompra'];
                        $row[$key][] = $datoscompra['name'];

        // foreach ($value as $pos => $campo) 
        // {
        //     $row[$key][] = $campo;
        // }                        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>