<?php


    //$activo = \App\Activo::All();
    
    $row = array();$activo = DB::select("select idActivo,codigoActivo,codigobarraActivo,nombreActivo, marcaActivo,modeloActivo,serieActivo,tipoactivo.nombreTipoActivo, estadoActivo from activo inner join tipoactivo on activo.TipoActivo_idTipoActivo=tipoactivo.idTipoActivo where activo.TipoActivo_idTipoActivo='1'");

    foreach ($activo as $key => $value) 
    {  
        $valores = get_object_vars($value);
        $row[$key][] = $valores['idActivo'];
        $row[$key][] = $valores['codigoActivo'];
        $row[$key][] = $valores['codigobarraActivo'];   
        $row[$key][] = $valores['nombreActivo'];  
        $row[$key][] = $valores['marcaActivo'];  
        $row[$key][] = $valores['modeloActivo'];  
        $row[$key][] = $valores['serieActivo'];  
        $row[$key][] = $valores['nombreTipoActivo'];  
        $row[$key][] = $valores['estadoActivo'];  
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>