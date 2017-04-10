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

    $sistemainformacion = \App\SistemaInformacion::All();
 
    $row = array();

    foreach ($sistemainformacion as $key => $value) 
    {  
        $row[$key][] = '<a href="sistemainformacion/'.$value['idSistemaInformacion'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="sistemainformacion/'.$value['idSistemaInformacion'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idSistemaInformacion'];
        $row[$key][] = $value['codigoSistemaInformacion'];
        $row[$key][] = $value['nombreSistemaInformacion'];   
        $row[$key][] = $value['webSistemaInformacion'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>