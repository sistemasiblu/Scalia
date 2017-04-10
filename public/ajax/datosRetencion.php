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
    
    $retencion = \App\Retencion::where('Compania_idCompania', "=", \Session::get("idCompania"))->get();
    // print_r($retencion);
    // exit;
    $row = array();

    foreach ($retencion as $key => $value) 
    {  
        $row[$key][] = '<a href="retencion/'.$value['idRetencion'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="retencion/'.$value['idRetencion'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idRetencion'];
        $row[$key][] = $value['anioRetencion'];  
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>