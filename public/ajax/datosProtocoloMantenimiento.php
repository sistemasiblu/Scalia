<?php

 //$protocolomantenimiento = \App\ProtocoloMantenimiento::All();
 $protocolomantenimiento = DB::select("select idProtocoloMantenimiento, nombreProtocoloMantenimiento, nombreTipoActivo, nombreTipoAccion
  from protocolomantenimiento 
  inner join tipoactivo 
  on protocolomantenimiento.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo
  inner join tipoaccion
  on protocolomantenimiento.TipoAccion_idTipoAccion=tipoaccion.idTipoAccion


  ");
 $row = array();

foreach ($protocolomantenimiento as $key => $value) 
{  
    $valores = get_object_vars($value);
    //$valores = $value;
    $row[$key][] = '<a href="protocolomantenimiento/'.$valores['idProtocoloMantenimiento'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="protocolomantenimiento/'.$valores['idProtocoloMantenimiento'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idProtocoloMantenimiento'];
    $row[$key][] = $valores['nombreProtocoloMantenimiento'];
    $row[$key][] = $valores['nombreTipoActivo']; 
    $row[$key][] = $valores['nombreTipoAccion']; 
    //$row[$key][] = $valores['nombreTipoActivoCaracteristica'];    
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>