<?php

 $activo = DB::select("select idActivo,nombreActivo 
    from activo
    where activo.clasificacionActivo='Parte'");

$row = array();

    foreach ($activo as $key => $value) 
    {  
        $valores = get_object_vars($value);
        $row[$key][] = $valores['idActivo'];
        $row[$key][] = $valores['nombreActivo']; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>