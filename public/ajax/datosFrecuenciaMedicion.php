<?php

 $frecuenciamedicion = \App\FrecuenciaMedicion::All();
 // $tipoactivo = DB::select("select tipoactivo.idTipoActivo,tipoactivo.codigoTipoActivo,tipoactivo.nombreTipoActivo, tipoactivocaracteristica.nombreTipoActivoCaracteristica from tipoactivo inner join tipoactivocaracteristica on tipoactivocaracteristica.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo");
 $row = array();

foreach ($frecuenciamedicion as $key => $value) 
{  
    //$valores = get_object_vars($value);
    $valores = $value;
    $row[$key][] = '<a href="frecuenciamedicion/'.$valores['idFrecuenciaMedicion'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="frecuenciamedicion/'.$valores['idFrecuenciaMedicion'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['ididFrecuenciaMedicion'];
    $row[$key][] = $valores['codigoFrecuenciaMedicion'];
    $row[$key][] = $valores['nombreFrecuenciaMedicion']; 
    $row[$key][] = $valores['valorFrecuenciaMedicion']; 
    $row[$key][] = $valores['unidadFrecuenciaMedicion']; 

    //$row[$key][] = $valores['nombreTipoActivoCaracteristica'];    
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>