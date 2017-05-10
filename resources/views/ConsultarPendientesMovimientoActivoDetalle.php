
<?php
@$id=$_GET['idMovimientoActivo'];
//echo $id;

    //$activo = \App\Activo::All();
    $compania=\Session::get('nombreCompania');
    
    $row = array();
     "select idMovimientoActivoDetalle, MovimientoActivo_idMovimientoActivo, Localizacion_idOrigen as nombreLocalizacionO, Localizacion_idDestino as nombreLocalizacionD, Activo_idActivo, cantidadMovimientoActivoDetalle, observacionMovimientoActivoDetalle, codigoActivo, nombreActivo, serieActivo, MovimientoActivo_idDocumentoInterno 
    from movimientoactivodetalle M 
    inner join activo
    on M.Activo_idActivo=activo.idActivo where MovimientoActivo_idMovimientoActivo IN($id)
    ");

    foreach ($movimientoactivod as $key => $value) 
    {  
        $valores = get_object_vars($value);
        $row[$key][] = $valores['idMovimientoActivoDetalle'];
        $row[$key][] = $valores['MovimientoActivo_idMovimientoActivo'];
        $row[$key][] = $valores['nombreLocalizacionO'];   
        $row[$key][] = $valores['nombreLocalizacionD'];  
        $row[$key][] = $valores['Activo_idActivo'];  
        $row[$key][] = $valores['codigoActivo'];  
        $row[$key][] = $valores['serieActivo'];  
        $row[$key][] = $valores['nombreActivo']; 
        $row[$key][] = $valores['cantidadMovimientoActivoDetalle'];  
        $row[$key][] = $valores['observacionMovimientoActivoDetalle'];  
        $row[$key][] = $valores['MovimientoActivo_idDocumentoInterno']; 
 

    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>