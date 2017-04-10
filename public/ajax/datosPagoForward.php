<?php

    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];
    $imprimir = $_GET['imprimir'];

    $visibleM = '';
    $visibleE = '';
    $visibleI = '';
    if ($modificar == 1) 
        $visibleM = 'inline-block;';
    else
        $visibleM = 'none;';

    if ($eliminar == 1) 
        $visibleE = 'inline-block;';
    else
        $visibleE = 'none;';

    if ($imprimir == 1) 
        $visibleI = 'inline-block;';
    else
        $visibleI = 'none;';
    
    $pagoforward = DB::Select('
        SELECT 
            idPagoForward,
            numeroForward,
            fechaNegociacionForward,
            fechaVencimientoForward,
            modalidadForward,
            valorDolarForward,
            bancoForward,
            estadoForward,
            DATEDIFF(fechaVencimientoForward,
                IFNULL(fechaVencimientoDocumentoFinanciero,
                    SUM(fechaRealEmbarqueDetalle + diasFormaPago))) AS diasRestantesVencimientoForward
        FROM
            pagoforward pf
                left join
            forward f on pf.Forward_idForward = f.idForward
                LEFT JOIN
            forwarddetalle fd ON f.idForward = fd.Forward_idForward
                LEFT JOIN
            compra c ON fd.Compra_idCompra = c.idCompra
                LEFT JOIN
            embarquedetalle ed ON c.idCompra = ed.Compra_idCompra
                LEFT JOIN
            documentofinancierodetalle dfd ON c.idCompra = dfd.Compra_idCompra
                LEFT JOIN
            documentofinanciero df ON dfd.DocumentoFinanciero_idDocumentoFinanciero = df.idDocumentoFinanciero
                LEFT JOIN
            Iblu.FormaPago fp ON c.FormaPago_idFormaPago = fp.idFormaPago
        GROUP BY idPagoForward');

    $row = array();

    foreach ($pagoforward as $key => $value) 
    {  
        $datosForward = get_object_vars($value);
        $row[$key][] = '<a href="pagoforward/'.$datosForward['idPagoForward'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="pagoforward/'.$datosForward['idPagoForward'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>&nbsp;'.
                        '<a onclick="imprimirFormatoCumplimientoForward('.$datosForward['idPagoForward'].')">'.
                            '<span class="glyphicon glyphicon-print" style="display: '.$visibleI.' cursor:pointer;"></span>'.
                        '</a>';

        foreach ($value as $pos => $campo) 
        {
            $row[$key][] = $campo;
        }  
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>