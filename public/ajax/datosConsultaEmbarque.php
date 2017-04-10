<?php

    $idDocumento = $_GET['idDocumento'];

    $modificar = $_GET['modificar'];

    $visibleM = '';
    $visibleE = '';
    if ($modificar == 1) 
        $visibleM = 'inline-block;';
    else
        $visibleM = 'none;';
    
    $consultaembarque = DB::Select('
        SELECT 
            numeroEmbarque,
            puertoCargaEmbarque,
            agenteCargaEmbarque,
            fechaRealEmbarque,
            nombreProveedorCompra,
            numeroCompra,
            facturaEmbarqueDetalle,
            numeroContenedorEmbarqueDetalle,
            blEmbarqueDetalle,
            fileEmbarqueDetalle,
            idEmbarque
        FROM
            embarque e
                LEFT JOIN
            embarquedetalle ed ON e.idEmbarque = ed.Embarque_idEmbarque
                LEFT JOIN
            compra c ON c.idCompra = ed.Compra_idCompra
        WHERE
            e.DocumentoImportacion_idDocumentoImportacion = '.$idDocumento);

    $row = array();

    foreach ($consultaembarque as $key => $value) 
    {  
        $datosembarque = get_object_vars($value);
        $row[$key][] = '<a href="embarque/'.$datosembarque['idEmbarque'].'/edit?idDocumento='.$idDocumento.'&accion=modificar">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>';

        foreach ($value as $pos => $campo) 
        {
            $row[$key][] = $campo;
        }                        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>