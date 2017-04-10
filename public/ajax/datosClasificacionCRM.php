<?php

$clasificacioncrm = DB::select("
    select 
    clasificacioncrm.idClasificacionCRM,clasificacioncrm.codigoClasificacionCRM,clasificacioncrm.nombreClasificacionCRM,nombreGrupoEstado from clasificacioncrm 
    inner join grupoestado
    on  clasificacioncrm.GrupoEstado_idGrupoEstado=grupoestado.idGrupoEstado");

$row = array();

foreach ($clasificacioncrm as $key => $value) 
{  
    $valores = get_object_vars($value);
    $row[$key][] = '<a href="clasificacioncrm/'.$valores['idClasificacionCRM'].'/edit" title="Editar" style="cursor:pointer;">'.
    '<span class="glyphicon glyphicon-pencil" ></span>'.
    '</a>&nbsp;'.
    '<a href="clasificacioncrm/'.$valores['idClasificacionCRM'].'/edit?accion=eliminar" title="Eliminar" style="cursor:pointer;">'.
    '<span class="glyphicon glyphicon-trash" ></span>'.
    '</a>';
    $row[$key][] = $valores['idClasificacionCRM'];
    $row[$key][] = $valores['codigoClasificacionCRM'];
    $row[$key][] = $valores['nombreClasificacionCRM']; 
    $row[$key][] = $valores['nombreGrupoEstado'];
 
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>