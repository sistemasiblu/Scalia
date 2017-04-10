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

    $opcion = DB::table('opcion')
            ->leftJoin('paquete', 'Paquete_idPaquete', '=', 'idPaquete')
            ->select(DB::raw('idOpcion, ordenOpcion, nombreOpcion, rutaOpcion, nombrePaquete'))
            ->get();

 //   print_r($opcion);
 // exit;
    $row = array();

    foreach ($opcion as $key => $value) 
    {  
        $row[$key][] = '<a href="opcion/'.$value->idOpcion.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="opcion/'.$value->idOpcion.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idOpcion;
        $row[$key][] = $value->ordenOpcion;
        $row[$key][] = $value->nombreOpcion; 
        $row[$key][] = $value->rutaOpcion;    
        $row[$key][] = $value->nombrePaquete;
    }
    //  print_r($row);
    // exit;
    $output['aaData'] = $row;
    echo json_encode($output);
?>