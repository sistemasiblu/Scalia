<?php


    $compania = \App\Compania::All();
    // print_r($compania);
    // exit;
    $row = array();

    foreach ($compania as $key => $value) 
    {  

        $row[$key][] = $value['idCompania'];
        $row[$key][] = $value['codigoCompania'];
        $row[$key][] = $value['nombreCompania'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>