<?php

    $categoriacrm = DB::table('categoriacrm')
            ->select(DB::raw('idCategoriaCRM, codigoCategoriaCRM, nombreCategoriaCRM'))
            ->get();

    $row = array();

    foreach ($categoriacrm as $key => $value) 
    {  
        $row[$key][] = '<a href="categoriacrm/'.$value->idCategoriaCRM.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil"></span>'.
                        '</a>&nbsp;'.
                        '<a href="categoriacrm/'.$value->idCategoriaCRM.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash"></span>'.
                        '</a>';
        $row[$key][] = $value->idCategoriaCRM;
        $row[$key][] = $value->codigoCategoriaCRM;
        $row[$key][] = $value->nombreCategoriaCRM; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>