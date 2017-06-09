<?php

$transaccionactivo = DB::select("select 
    idTransaccionActivo, codigoTransaccionActivo, nombreTransaccionActivo, nombreTransaccionGrupo 
    from transaccionactivo
    inner join transacciongrupo
    on transaccionactivo.TransaccionGrupo_idTransaccionGrupo=transacciongrupo.idTransaccionGrupo
    ");

$row = array();

foreach ($transaccionactivo as $key => $value) 
{  
    $valores = get_object_vars($value);
    $row[$key][] = '<a href="transaccionactivo/'.$valores['idTransaccionActivo'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="transaccionactivo/'.$valores['idTransaccionActivo'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idTransaccionActivo'];
    $row[$key][] = $valores['codigoTransaccionActivo'];
    $row[$key][] = $valores['nombreTransaccionActivo']; 
    $row[$key][] = $valores['nombreTransaccionGrupo'];
      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>