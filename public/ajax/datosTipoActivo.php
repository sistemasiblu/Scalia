<?php

 $tipoactivo = \App\TipoActivo::All();
 // $tipoactivo = DB::select("select tipoactivo.idTipoActivo,tipoactivo.codigoTipoActivo,tipoactivo.nombreTipoActivo, tipoactivocaracteristica.nombreTipoActivoCaracteristica from tipoactivo inner join tipoactivocaracteristica on tipoactivocaracteristica.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo");
 $row = array();

foreach ($tipoactivo as $key => $value) 
{  
    //$valores = get_object_vars($value);
    $valores = $value;
    $row[$key][] = '<a href="tipoactivo/'.$valores['idTipoActivo'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="tipoactivo/'.$valores['idTipoActivo'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idTipoActivo'];
    $row[$key][] = $valores['codigoTipoActivo'];
    $row[$key][] = $valores['nombreTipoActivo']; 
    //$row[$key][] = $valores['nombreTipoActivoCaracteristica'];    
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>