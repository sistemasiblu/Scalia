<?php

$detalleMovimientoactivo = DB::select("select idMovimientoActivoDetalle, MovimientoActivo_idMovimientoActivo, Localizacion_idOrigen, Localizacion_idDestino, Activo_idActivo, cantidadMovimientoActivoDetalle, observacionMovimientoActivoDetalle, MovimientoActivo_idDocumentoInterno, estadoMovimientoActivoDetalle, RechazoActivo_idRechazoActivo
 from movimientoactivodetalle");



$row = array();

foreach ($detalleMovimientoactivo as $key => $value) 
{  
    $valores = get_object_vars($value);
    
    $row[$key][] = $valores['idMovimientoActivoDetalle'];
    $row[$key][] = $valores['MovimientoActivo_idMovimientoActivo'];
    $row[$key][] = $valores['Localizacion_idOrigen']; 
    $row[$key][] = $valores['Localizacion_idDestino'];
    $row[$key][] = $valores['Activo_idActivo'];
    $row[$key][] = $valores['cantidadMovimientoActivoDetalle'];

      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>