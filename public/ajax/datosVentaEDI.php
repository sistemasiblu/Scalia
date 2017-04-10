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

    $ventaedi = DB::table('ventaedi')
            ->select(DB::raw('idVentaEDI, numeroVentaEDI, nombreClienteVentaEDI, fechaInicialVentaEDI, fechaFinalVentaEDI'))
            ->get();

    $row = array();

    foreach ($ventaedi as $key => $value) 
    {  
        $row[$key][] = '';
        $row[$key][] = $value->idVentaEDI;
        $row[$key][] = $value->numeroVentaEDI;
        $row[$key][] = $value->nombreClienteVentaEDI;
        $row[$key][] = $value->fechaInicialVentaEDI;
        $row[$key][] = $value->fechaFinalVentaEDI;  
 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>