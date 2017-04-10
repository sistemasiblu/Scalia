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
            embarquedetalle ed on c.idCompra = ed.Compra_idCompra
                LEFT JOIN
            Iblu.Temporada t ON fd.Temporada_idTemporada = t.idTemporada
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
        ORDER BY numeroCompra');
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
        $row[$key][] = $valor['nombreTemporadaCompra'];
        $row[$key][] = $valor['nombreProveedorCompra'];
        $row[$key][] = $valor['valorCompra'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>