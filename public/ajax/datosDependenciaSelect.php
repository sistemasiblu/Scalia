<?php


    $dependencia = \App\Dependencia::All();
    // print_r($dependencia);
    // exit;
    $row = array();

    foreach ($dependencia as $key => $value) 
    {  
                        
        $row[$key][] = $value['idDependencia'];
        $row[$key][] = $value['codigoDependencia'];
        $row[$key][] = $value['nombreDependencia'];
        $row[$key][] = $value['abreviaturaDependencia'];    
    }
    $output['aaData'] = $row;
    echo json_encode($output);
?>