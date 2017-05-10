<?php

$inventarioactivo = DB::select("select idInventarioActivo, Periodo_idPeriodo, nombreActivo, nombreLocalizacion, saldoInicialInventarioActivo, entradasInventarioActivo, salidasFinalInventarioActivo, saldoFinalInventarioActivo 
    from inventarioactivo 
    inner join activo
    on activo.idActivo=inventarioactivo.Activo_idActivo
    inner join movimientoactivodetalle
    on movimientoactivodetalle.Activo_idActivo=activo.idActivo
    inner join localizacion
    on movimientoactivodetalle.Localizacion_idDestino=localizacion.idLocalizacion
    ");



$row = array();

foreach ($inventarioactivo as $key => $value) 
{  
    $valores = get_object_vars($value);
    $row[$key][] = $valores['idInventarioActivo'];
    $row[$key][] = $valores['Periodo_idPeriodo'];
    $row[$key][] = $valores['nombreActivo']; 
    $row[$key][] = $valores['nombreLocalizacion'];
    $row[$key][] = $valores['saldoFinalInventarioActivo'];
      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>