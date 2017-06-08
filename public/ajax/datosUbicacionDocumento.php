<?php

    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];

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

    $ubicaciondocumento = DB::Select('
        SELECT
            idUbicacionDocumento,
            tipoUbicacionDocumento,
            posicionUbicacionDocumento,
            numeroLegajoUbicacionDocumento,
            nombreTipoSoporteDocumental,
            nombreDependencia,
            nombreCompania,
            observacionUbicacionDocumento,
            estadoUbicacionDocumento
        FROM
            ubicaciondocumento ud
                LEFT JOIN
            tiposoportedocumental tsd ON ud.TipoSoporteDocumental_idTipoSoporteDocumental = tsd.idTipoSoporteDocumental
                LEFT JOIN
            dependencia d ON ud.Dependencia_idProductora = d.idDependencia
                LEFT JOIN
            compania c ON ud.Compania_idCompania = c.idCompania');

    $row = array();

    foreach ($ubicaciondocumento as $key => $value) 
    {  
        $ubicacion = get_object_vars($value);

        $row[$key][] = '<a href="etiqueta/'.$ubicacion['idUbicacionDocumento'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="etiqueta/'.$ubicacion['idUbicacionDocumento'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $ubicacion['idUbicacionDocumento'];
        $row[$key][] = $ubicacion['tipoUbicacionDocumento'];
        $row[$key][] = $ubicacion['posicionUbicacionDocumento'];
        $row[$key][] = $ubicacion['numeroLegajoUbicacionDocumento'];
        $row[$key][] = $ubicacion['nombreTipoSoporteDocumental'];
        $row[$key][] = $ubicacion['nombreDependencia'];
        $row[$key][] = $ubicacion['nombreCompania'];
        $row[$key][] = $ubicacion['observacionUbicacionDocumento'];
        $row[$key][] = $ubicacion['estadoUbicacionDocumento'];

    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>