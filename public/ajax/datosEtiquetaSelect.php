<?php


    $etiqueta = \App\Etiqueta::All();
    // print_r($etiqueta);
    // exit;
    $row = array();

    foreach ($etiqueta as $key => $value) 
    {  
                        
        $row[$key][] = $value['idEtiqueta'];
        $row[$key][] = $value['nombreEtiqueta']; 
    }
    $output['aaData'] = $row;
    echo json_encode($output);
?>