
<?php
@$id=$_GET['id'];
//echo $id;

    //$activo = \App\Activo::All();
    $compania=\Session::get('nombreCompania');
    
    $row = array();
    $movimientoactivod = DB::select(
    "select idMovimientoActivo, numeroMovimientoActivo, fechaElaboracionMovimientoActivo, nombre1Tercero, nombreTransaccionActivo, totalArticulosMovimientoActivo, users.name, nombreCompania
    from movimientoactivo
    inner join ".$compania.".tercero
    on tercero.idTercero=movimientoactivo.Tercero_idTercero
    inner join transaccionactivo 
    on transaccionactivo.idTransaccionActivo=movimientoactivo.TransaccionActivo_idTransaccionActivo
    inner join users
    on users.id=movimientoactivo.Users_idCrea
    inner join compania
    on compania.idCompania=movimientoactivo.Compania_idCompania
    where TransaccionActivo_idTransaccionActivo=".$id);



    foreach ($movimientoactivod as $key => $value) 
    {  
        $valores = get_object_vars($value);
        $row[$key][] = $valores['idMovimientoActivo'];
        $row[$key][] = $valores['numeroMovimientoActivo'];
        $row[$key][] = $valores['fechaElaboracionMovimientoActivo'];   
        $row[$key][] = $valores['nombre1Tercero'];  
        $row[$key][] = $valores['nombreTransaccionActivo'];  
        $row[$key][] = $valores['totalArticulosMovimientoActivo'];  
        $row[$key][] = $valores['name'];  
        $row[$key][] = $valores['nombreCompania']; 
        /*$row[$key][] = $valores['idMovimientoActivoDetalle'];
        $row[$key][] = $valores['MovimientoActivo_idMovimientoActivo'];
        $row[$key][] = $valores['nombreLocalizacionO'];   
        $row[$key][] = $valores['nombreLocalizacionD'];  
        $row[$key][] = $valores['Activo_idActivo'];  
        $row[$key][] = $valores['codigoActivo'];  
        $row[$key][] = $valores['serieActivo'];  
        $row[$key][] = $valores['nombreActivo']; 
        $row[$key][] = $valores['cantidadMovimientoActivoDetalle'];  
        $row[$key][] = $valores['observacionMovimientoActivoDetalle'];*/
       
 

    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>