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

    $listafinanciacion = \App\ListaFinanciacion::All();
    // print_r($listafinanciacion);
    // exit;
    $row = array();

    foreach ($listafinanciacion as $key => $value) 
    {  
        $row[$key][] = '<a href="listafinanciacion/'.$value['idListaFinanciacion'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="listafinanciacion/'.$value['idListaFinanciacion'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idListaFinanciacion'];
        $row[$key][] = $value['nombreListaFinanciacion'];
        $row[$key][] = $value['codigoListaFinanciacion'];   
        $row[$key][] = $value['codigoSayaListaFinanciacion'];
        $row[$key][] = $value['tipoListaFinanciacion'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>