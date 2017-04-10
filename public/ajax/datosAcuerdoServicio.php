<?php

    $acuerdoservicio = DB::table('acuerdoservicio')
            ->select(DB::raw('idAcuerdoServicio, codigoAcuerdoServicio, nombreAcuerdoServicio, tiempoAcuerdoServicio, unidadTiempoAcuerdoServicio'))
            ->get();

    $row = array();

    foreach ($acuerdoservicio as $key => $value) 
    {  
        $row[$key][] = '<a href="acuerdoservicio/'.$value->idAcuerdoServicio.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil"></span>'.
                        '</a>&nbsp;'.
                        '<a href="acuerdoservicio/'.$value->idAcuerdoServicio.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash"></span>'.
                        '</a>';
        $row[$key][] = $value->idAcuerdoServicio;
        $row[$key][] = $value->codigoAcuerdoServicio;
        $row[$key][] = $value->nombreAcuerdoServicio; 
        $row[$key][] = $value->tiempoAcuerdoServicio; 
        $row[$key][] = $value->unidadTiempoAcuerdoServicio; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>