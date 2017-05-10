<?php

$asignacionactivo = DB::select("select idAsignacionActivo, numeroAsignacionActivo, fechaHoraAsignacionActivo, TransaccionActivo_idTransaccionActivo, documentoInternoAsignacionActivo, Users_idCrea
 from asignacionactivo");



$row = array();

foreach ($asignacionactivo as $key => $value) 
{  
    $valores = get_object_vars($value);
    $row[$key][] = '<a href="asignacionactivo/'.$valores['idAsignacionActivo'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="asignacionactivo/'.$valores['idAsignacionActivo'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idAsignacionActivo'];
    $row[$key][] = $valores['numeroAsignacionActivo'];
    $row[$key][] = $valores['fechaHoraAsignacionActivo']; 
    $row[$key][] = $valores['TransaccionActivo_idTransaccionActivo'];
    $row[$key][] = $valores['documentoInternoAsignacionActivo'];
    $row[$key][] = $valores['Users_idCrea'];

      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>