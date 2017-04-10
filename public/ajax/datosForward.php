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

    $forward = \App\Forward::All();
    $row = array();

    foreach ($forward as $key => $value) 
    {  
        $row[$key][] = '<a href="forward/'.$value['idForward'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="forward/'.$value['idForward'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>&nbsp;'.
                        '<a onclick="imprimirFormatoForward('.$value['idForward'].')">'.
                            '<span class="glyphicon glyphicon-print" style="display: '.$visibleE.' cursor:pointer;"></span>'.
                        '</a>';
        $row[$key][] = $value['idForward'];
        $row[$key][] = $value['numeroForward'];
        $row[$key][] = $value['fechaNegociacionForward'];
        $row[$key][] = $value['fechaVencimientoForward'];
        $row[$key][] = str_replace("_", " ", $value['modalidadForward']);
        $row[$key][] = $value['valorDolarForward'];
        $row[$key][] = $value['bancoForward'];
        $row[$key][] = $value['estadoForward'];    
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>