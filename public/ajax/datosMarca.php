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
    
    $marca = \App\Marca::All();
 
    $row = array();

    foreach ($marca as $key => $value) 
    {  
        $row[$key][] = '<a href="marca/'.$value['idMarca'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="marca/'.$value['idMarca'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idMarca'];
        $row[$key][] = $value['codigoMarca'];
        $row[$key][] = $value['nombreMarca'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>