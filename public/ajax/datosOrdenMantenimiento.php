<?php

 $ordenmantenimiento = \App\OrdenMantenimiento::All();
 /*$ordenmantenimiento = DB::select("select idOrdenMantenimiento, nombreProtocoloMantenimiento, nombreTipoActivo, nombreTipoAccion
  from ordenmantenimiento 
  inner join tipoactivo 
  on ordenmantenimiento.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo
  inner join tipoaccion
  on ordenmantenimiento.TipoAccion_idTipoAccion=tipoaccion.idTipoAccion


  ");
 $row = array();*/

foreach ($ordenmantenimiento as $key => $value) 
{  
    //$valores = get_object_vars($value);
    $valores = $value;
    $row[$key][] = '<a href="ordenmantenimiento/'.$valores['idOrdenMantenimiento'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="ordenmantenimiento/'.$valores['idOrdenMantenimiento'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idOrdenMantenimiento'];
    $row[$key][] = $valores['asuntoOrdenMantenimiento'];
    $row[$key][] = $valores['fechaElaboracionOrdenMantenimiento']; 
    $row[$key][] = $valores['ProtocoloMantenimiento_idProtocoloMantenimiento']; 
    //$row[$key][] = $valores['nombreTipoActivoCaracteristica'];    
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>