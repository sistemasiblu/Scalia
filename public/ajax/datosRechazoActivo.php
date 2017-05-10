<?php

$rechazoactivo = DB::select("select idRechazoActivo, codigoRechazoActivo, nombreRechazoActivo, observacionRechazoActivo from rechazoactivo");



$row = array();

foreach ($rechazoactivo as $key => $value) 
{  
    $valores = get_object_vars($value);
    $row[$key][] = '<a href="rechazoactivo/'.$valores['idRechazoActivo'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="rechazoactivo/'.$valores['idRechazoActivo'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idRechazoActivo'];
    $row[$key][] = $valores['codigoRechazoActivo'];
    $row[$key][] = $valores['nombreRechazoActivo']; 
    $row[$key][] = $valores['observacionRechazoActivo'];
      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>