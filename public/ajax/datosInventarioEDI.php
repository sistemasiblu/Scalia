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

    $inventarioedi = DB::table('inventarioedi')
            ->select(DB::raw('idInventarioEDI, numeroInventarioEDI, nombreClienteInventarioEDI, fechaInicialInventarioEDI, fechaFinalInventarioEDI'))
            ->get();

    $row = array();

    foreach ($inventarioedi as $key => $value) 
    {  
        $row[$key][] = '';
        $row[$key][] = $value->idInventarioEDI;
        $row[$key][] = $value->numeroInventarioEDI;
        $row[$key][] = $value->nombreClienteInventarioEDI;
        $row[$key][] = $value->fechaInicialInventarioEDI;
        $row[$key][] = $value->fechaFinalInventarioEDI;  
 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>