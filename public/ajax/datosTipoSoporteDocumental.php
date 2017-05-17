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

    $tiposoportedocumental = \App\TipoSoporteDocumental::All();
    // print_r($tiposoportedocumental);
    // exit;
    $row = array();

    foreach ($tiposoportedocumental as $key => $value) 
    {  
        $row[$key][] = '<a href="tiposoportedocumental/'.$value['idTipoSoporteDocumental'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="tiposoportedocumental/'.$value['idTipoSoporteDocumental'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idTipoSoporteDocumental'];
        $row[$key][] = $value['codigoTipoSoporteDocumental'];
        $row[$key][] = $value['nombreTipoSoporteDocumental'];

    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>