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

    $metadato = \App\Metadato::All();
    // print_r($metadato);
    // exit;
    $row = array();

    foreach ($metadato as $key => $value) 
    {  
        $row[$key][] = '<a href="metadato/'.$value['idMetadato'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="metadato/'.$value['idMetadato'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idMetadato'];
        $row[$key][] = $value['tituloMetadato'];
        $row[$key][] = $value['tipoMetadato'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>