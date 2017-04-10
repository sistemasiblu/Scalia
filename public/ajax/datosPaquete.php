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

    $paquete = \App\Paquete::All();
    // print_r($paquete);
    // exit;
    $row = array();

    foreach ($paquete as $key => $value) 
    {  
        $row[$key][] = '<a href="paquete/'.$value['idPaquete'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="paquete/'.$value['idPaquete'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idPaquete'];
        $row[$key][] = $value['ordenPaquete'];
        $row[$key][] = $value['nombrePaquete'];   
        $row[$key][] = '<img src="imagenes/'.$value['iconoPaquete'].'">';
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>