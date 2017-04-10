<?php

    $zona = DB::table('zona')
            ->select(DB::raw('idZona, codigoZona, nombreZona'))
            ->where('Compania_idCompania','=',\Session::get("idCompania"))
            ->get();

    $row = array();

    foreach ($zona as $key => $value) 
    {  
        $row[$key][] = '<a href="zona/'.$value->idZona.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil"></span>'.
                        '</a>&nbsp;'.
                        '<a href="zona/'.$value->idZona.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash"></span>'.
                        '</a>';
        $row[$key][] = $value->idZona;
        $row[$key][] = $value->codigoZona;
        $row[$key][] = $value->nombreZona; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>