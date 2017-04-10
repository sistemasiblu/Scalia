<?php


    $dependencia = \App\Dependencia::All();
    // print_r($dependencia);
    // exit;
    $row = array();

    foreach ($dependencia as $key => $value) 
    {  
        $row[$key][] = '<a href="dependencia/'.$value['idFuncion'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil"></span>'.
                        '</a>&nbsp;'.
                        '<a href="dependencia/'.$value['idFuncion'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash"></span>'.
                        '</a>';
        $row[$key][] = $value['idFuncion'];
        $row[$key][] = $value['numeroFuncion'];
        $row[$key][] = $value['descripcionFuncion'];
        $row[$key][] = $value['Dependencia_idDependencia'];    
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>