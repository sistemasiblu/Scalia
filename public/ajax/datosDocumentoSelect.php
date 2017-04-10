<?php


    $documento = \App\Documento::All();
    // print_r($documento);
    // exit;
    $row = array();

    foreach ($documento as $key => $value) 
    {  

        $row[$key][] = $value['idDocumento'];
        $row[$key][] = $value['codigoDocumento'];
        $row[$key][] = $value['nombreDocumento'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>