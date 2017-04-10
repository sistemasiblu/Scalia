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
    
    $dispositivo = \App\Dispositivo::All();
 
    $row = array();

    foreach ($dispositivo as $key => $value) 
    {  
        $row[$key][] = '<a href="dispositivo/'.$value['idDispositivo'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="dispositivo/'.$value['idDispositivo'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idDispositivo'];
        $row[$key][] = $value['codigoDispositivo'];
        $row[$key][] = $value['nombreDispositivo'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>