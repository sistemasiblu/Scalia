<?php

 $protocolomantenimiento = \App\ProtocoloMantenimiento::All();
 // $tipoactivo = DB::select("select tipoactivo.idTipoActivo,tipoactivo.codigoTipoActivo,tipoactivo.nombreTipoActivo, tipoactivocaracteristica.nombreTipoActivoCaracteristica from tipoactivo inner join tipoactivocaracteristica on tipoactivocaracteristica.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo");
 $row = array();

foreach ($protocolomantenimiento as $key => $value) 
{  
    //$valores = get_object_vars($value);
    $valores = $value;
    $row[$key][] = '<a href="protocolomantenimiento/'.$valores['idProtocoloMantenimiento'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="protocolomantenimiento/'.$valores['idProtocoloMantenimiento'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idProtocoloMantenimiento'];
    $row[$key][] = $valores['nombreProtocoloMantenimiento'];
    $row[$key][] = $valores['TipoActivo_idTipoActivo']; 
    $row[$key][] = $valores['TipoAccion_idTipoAccion']; 
    //$row[$key][] = $valores['nombreTipoActivoCaracteristica'];    
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>