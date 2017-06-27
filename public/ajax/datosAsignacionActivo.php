<?php

$asignacionactivo = DB::select("select idAsignacionActivo, numeroAsignacionActivo, fechaHoraAsignacionActivo, nombreTransaccionActivo, documentoInternoAsignacionActivo,name as UserCrea
 from asignacionactivo
 inner join asignacionactivodetalle
 on asignacionactivodetalle.AsignacionActivo_idAsignacionActivo=asignacionactivo.idAsignacionActivo
 inner join movimientoactivo
 on asignacionactivodetalle.MovimientoActivo_idMovimientoActivo=movimientoactivo.idMovimientoActivo
  inner join transaccionactivo
 on movimientoactivo.TransaccionActivo_idTransaccionActivo=transaccionactivo.idTransaccionActivo
 inner join users
 on asignacionactivo.Users_idCrea=users.id group by idAsignacionActivo

 ");



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
    $row[$key][] = $valores['nombreTransaccionActivo'];
    $row[$key][] = $valores['documentoInternoAsignacionActivo'];
    $row[$key][] = $valores['UserCrea'];

      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>