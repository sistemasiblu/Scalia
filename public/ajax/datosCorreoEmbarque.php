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

    $correoembarque = \App\CorreoEmbarque::All();
    // print_r($correoembarque);
    // exit;
    $row = array();

    foreach ($correoembarque as $key => $value) 
    {  
        $row[$key][] = '<a href="correoembarque/'.$value['idCorreoEmbarque'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="correoembarque/'.$value['idCorreoEmbarque'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idCorreoEmbarque'];
        $row[$key][] = $value['tipoCorreoEmbarque'];
        $row[$key][] = $value['asuntoCorreoEmbarque'];   
        $row[$key][] = $value['destinatarioCorreoEmbarque'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>