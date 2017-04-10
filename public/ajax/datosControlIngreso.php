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

    $controlingreso = DB::Select('
        SELECT idControlIngreso, nombreVisitanteControlIngreso, apellidoVisitanteControlIngreso, numeroDocumentoVisitanteControlIngreso, fechaIngresoControlIngreso, fechaSalidaControlIngreso, nombre1Tercero, dependenciaControlIngreso
        FROM controlingreso ci
        LEFT JOIN Iblu.Tercero t
        ON ci.Tercero_idResponsable = t.idTercero');
    $row = array();

    foreach ($controlingreso as $key => $value) 
    {  
        $value = get_object_vars($controlingreso[$key]);

        $row[$key][] = $value['idControlIngreso'];
        $row[$key][] = $value['nombreVisitanteControlIngreso'].' '.$value['apellidoVisitanteControlIngreso'];
        $row[$key][] = $value['numeroDocumentoVisitanteControlIngreso'];
        $row[$key][] = $value['fechaIngresoControlIngreso'];    
        $row[$key][] = ($value['fechaSalidaControlIngreso'] == "0000-00-00 00:00:00" ? "" : $value['fechaSalidaControlIngreso']);    
        $row[$key][] = $value['nombre1Tercero'];    
        $row[$key][] = $value['dependenciaControlIngreso'];    

    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>