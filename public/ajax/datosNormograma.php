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

    $normograma = \App\Normograma::All();
    // print_r($normograma);
    // exit;
    $row = array();

    foreach ($normograma as $key => $value) 
    {  
        $row[$key][] = '<a href="normograma/'.$value['idNormograma'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="normograma/'.$value['idNormograma'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idNormograma'];
        $row[$key][] = $value['nombreNormograma'];
        $row[$key][] = $value['descripcionNormograma'];   
        $row[$key][] = $value['derogada_vigenteNormograma'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>