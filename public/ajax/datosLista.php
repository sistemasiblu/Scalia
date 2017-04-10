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

    $lista = \App\Lista::All();
    // print_r($lista);
    // exit;
    $row = array();

    foreach ($lista as $key => $value) 
    {  
        $row[$key][] = '<a href="lista/'.$value['idLista'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="lista/'.$value['idLista'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idLista'];
        $row[$key][] = $value['codigoLista'];
        $row[$key][] = $value['nombreLista'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>