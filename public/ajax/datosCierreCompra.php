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

    $cierrecompra = \App\CierreCompra::All();
    $row = array();

    foreach ($cierrecompra as $key => $value) 
    {  
        $row[$key][] = '<a href="cierrecompra/'.$value['idCierreCompra'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="cierrecompra/'.$value['idCierreCompra'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idCierreCompra'];
        $row[$key][] = $value['numeroCierreCompra'];
        $row[$key][] = $value['fechaCierreCompra'];
        $row[$key][] = $value['descripcionCierreCompra'];    
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>