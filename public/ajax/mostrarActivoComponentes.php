<?php

$activo = DB::select("select idActivo, nombreActivo 
    from activo
    where activo.clasificacionActivo='Componente'");

$row = array();

foreach ($activo as $key => $value) 
{  
    $valores = get_object_vars($value);
    /*$valores = $value;*/
   $vacio="";
    $row[$key][] = $valores['idActivo'];
    $row[$key][] = $valores['nombreActivo']; 
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>