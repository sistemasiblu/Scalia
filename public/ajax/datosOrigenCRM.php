<?php

    $origencrm = DB::table('origencrm')
            ->select(DB::raw('idOrigenCRM, codigoOrigenCRM, nombreOrigenCRM'))
            ->get();

    $row = array();

    foreach ($origencrm as $key => $value) 
    {  
        $row[$key][] = '<a href="origencrm/'.$value->idOrigenCRM.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil"></span>'.
                        '</a>&nbsp;'.
                        '<a href="origencrm/'.$value->idOrigenCRM.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash"></span>'.
                        '</a>';
        $row[$key][] = $value->idOrigenCRM;
        $row[$key][] = $value->codigoOrigenCRM;
        $row[$key][] = $value->nombreOrigenCRM; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>