<?php

    $eventocrm = DB::table('eventocrm')
            ->select(DB::raw('idEventoCRM, codigoEventoCRM, nombreEventoCRM'))
            ->get();

    $row = array();

    foreach ($eventocrm as $key => $value) 
    {  
        $row[$key][] = '<a href="eventocrm/'.$value->idEventoCRM.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil"></span>'.
                        '</a>&nbsp;'.
                        '<a href="eventocrm/'.$value->idEventoCRM.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash"></span>'.
                        '</a>';
        $row[$key][] = $value->idEventoCRM;
        $row[$key][] = $value->codigoEventoCRM;
        $row[$key][] = $value->nombreEventoCRM; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>