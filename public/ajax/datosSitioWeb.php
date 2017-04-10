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

    $sitioweb = \App\SitioWeb::All();
    $row = array();

    foreach ($sitioweb as $key => $value) 
    {  
        $row[$key][] = '<a href="sitioweb/'.$value['idSitioWeb'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="sitioweb/'.$value['idSitioWeb'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idSitioWeb'];
        $row[$key][] = $value['descripcionSitioWeb'];
        $row[$key][] = $value['urlSitioWeb'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>