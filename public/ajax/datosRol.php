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

    $rol = \App\Rol::All();
    // print_r($rol);
    // exit;
    $row = array();

    foreach ($rol as $key => $value) 
    {  
        $row[$key][] = '<a href="rol/'.$value['idRol'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="rol/'.$value['idRol'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idRol'];
        $row[$key][] = $value['codigoRol'];
        $row[$key][] = $value['nombreRol'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>