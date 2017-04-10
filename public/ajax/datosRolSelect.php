<?php


    $rol = \App\Rol::All();
    // print_r($rol);
    // exit;
    $row = array();

    foreach ($rol as $key => $value) 
    {  

        $row[$key][] = $value['idRol'];
        $row[$key][] = $value['codigoRol'];
        $row[$key][] = $value['nombreRol'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>