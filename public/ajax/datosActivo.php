<?php

$activo = DB::select("select activo.idActivo,activo.codigoActivo,activo.nombreActivo, tipoactivo.nombreTipoActivo from activo inner join tipoactivo on activo.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo");

$row = array();

foreach ($activo as $key => $value) 
{  
    $valores = get_object_vars($value);
    $row[$key][] = '<a href="activo/'.$valores['idActivo'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="activo/'.$valores['idActivo'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idActivo'];
    $row[$key][] = $valores['codigoActivo'];
    $row[$key][] = $valores['nombreActivo']; 
    $row[$key][] = $valores['nombreTipoActivo'];
      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>