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

    $etiqueta = \App\Etiqueta::All();
    // print_r($etiqueta);
    // exit;
    $row = array();

    foreach ($etiqueta as $key => $value) 
    {  
        $row[$key][] = '<a href="etiqueta/'.$value['idEtiqueta'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="etiqueta/'.$value['idEtiqueta'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idEtiqueta'];
        $row[$key][] = $value['nombreEtiqueta'];

    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>