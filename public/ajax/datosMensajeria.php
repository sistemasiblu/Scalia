<?php

    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];
    $imprimir = $_GET['imprimir'];
    $tipo = $_GET['tipo'];

    $visibleM = '';
    $visibleE = '';
    $visibleI = '';

    $rol = DB::Select('
        SELECT 
            idRol
        FROM
            rol r
                LEFT JOIN
            users u ON r.idRol = u.Rol_idRol
        WHERE
            id = '.\Session::get('idUsuario'));

    $idRol = get_object_vars($rol[0]);

    if ($idRol['idRol'] == 10 or $idRol['idRol'] == 1) 
    {
        $mensajeria = DB::Select('
        SELECT 
            idMensajeria,
            tipoCorrespondenciaMensajeria,
            tipoEnvioMensajeria,
            prioridadMensajeria,
            fechaLimiteMensajeria,
            transportadorMensajeria,
            destinatarioMensajeria,
            estadoEntregaMensajeria,
            fechaEntregaMensajeria,
            u.name as nombreUsuario
        FROM
            mensajeria m
                LEFT JOIN
            users u ON u.id = m.Users_idCrea
        WHERE idMensajeria IS NOT NULL
            AND tipoEnvioMensajeria = "'.$tipo.'" 
        GROUP BY idMensajeria');
    }
    else
    {
        $mensajeria = DB::Select('
        SELECT 
            idMensajeria,
            tipoCorrespondenciaMensajeria,
            tipoEnvioMensajeria,
            prioridadMensajeria,
            fechaLimiteMensajeria,
            transportadorMensajeria,
            destinatarioMensajeria,
            estadoEntregaMensajeria,
            fechaEntregaMensajeria,
            u.name as nombreUsuario
        FROM
            mensajeria m
                LEFT JOIN
            users u ON u.id = m.Users_idCrea
        WHERE idMensajeria IS NOT NULL
        AND id = '.\Session::get('idUsuario').'
        GROUP BY idMensajeria');
    }


    $row = array();

    foreach ($mensajeria as $key => $value) 
    {  
        $valor = get_object_vars($value);

        if ($idRol['idRol'] == 10 or $idRol['idRol'] == 1) 
        {
            if ($modificar == 1) 
            $visibleM = 'inline-block;';
            else
                $visibleM = 'none;';
        }
        else
        {
            if ($modificar == 1  and $valor['estadoEntregaMensajeria'] != 'Recibida') 
            $visibleM = 'inline-block;';
            else
                $visibleM = 'none;';
        }

        if ($idRol['idRol'] == 10 or $idRol['idRol'] == 1) 
        {
            if ($eliminar == 1) 
            $visibleE = 'inline-block;';
            else
                $visibleE = 'none;';
        }
        else
        {
            if ($eliminar == 1 and $valor['estadoEntregaMensajeria'] != 'Recibida') 
            $visibleE = 'inline-block;';
            else
                $visibleE = 'none;';
        }

        if ($imprimir == 1) 
            $visibleI = 'inline-block;';
        else
            $visibleI = 'none;';

        $row[$key][] = '<a href="mensajeria/'.$valor['idMensajeria'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="mensajeria/'.$valor['idMensajeria'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>&nbsp;'.
                        '<a title="Detalles del registro" onclick="imprimirMensajeria('.$valor['idMensajeria'].')">'.
                            '<span class="glyphicon glyphicon-print" style="cursor: pointer; display: '.$visibleI.'"></span>'.
                        '</a>&nbsp;'.
                        '<a title="Sitcker de mensajeria" onclick="imprimirStickerMensajeria('.$valor['idMensajeria'].')">'.
                            '<span class="glyphicon glyphicon-barcode" style="cursor: pointer; display: '.$visibleI.'"></span>'.
                        '</a>';

        $row[$key][] = $valor['idMensajeria'];
        $row[$key][] = $valor['tipoEnvioMensajeria'];
        $row[$key][] = $valor['tipoCorrespondenciaMensajeria'];
        $row[$key][] = $valor['prioridadMensajeria'];
        $row[$key][] = $valor['fechaLimiteMensajeria'];
        $row[$key][] = $valor['transportadorMensajeria'];
        $row[$key][] = $valor['destinatarioMensajeria'];
        $row[$key][] = str_replace("_", " ", $valor['estadoEntregaMensajeria']);
        $row[$key][] = $valor['fechaEntregaMensajeria'] == "0000-00-00 00:00:00" ? "" : $valor['fechaEntregaMensajeria'];
        $row[$key][] = $valor['nombreUsuario'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>