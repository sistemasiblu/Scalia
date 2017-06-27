
<?php
@$id=$_GET['idMovimientoActivo'];


//echo $id;

    //$activo = \App\Activo::All();
    $compania=\Session::get('nombreCompania');
    
    $row = array();
    

    $movimientoactivod = DB::select(
     " SELECT 
    
    movimientoactivodetalle.MovimientoActivo_idMovimientoActivo,movimientoactivodetalle.Activo_idActivo,codigoActivo,
    serieActivo, nombreActivo, idLocalizacion,nombreLocalizacion
FROM
    movimientoactivodetalle
    inner join activo 
    on movimientoactivodetalle.Activo_idActivo=activo.idActivo
    inner join localizacion
    on movimientoactivodetalle.Localizacion_idDestino=localizacion.idLocalizacion
    inner join movimientoactivo
    on movimientoactivodetalle.MovimientoActivo_idMovimientoActivo=movimientoactivo.idMovimientoActivo
     inner join asignacionactivodetalle
    on asignacionactivodetalle.MovimientoActivo_idMovimientoActivo=movimientoactivo.idMovimientoActivo

WHERE movimientoactivodetalle.Activo_idActivo NOT IN 
(SELECT asignacionactivodetalle.Activo_idActivo FROM asignacionactivodetalle) group by Activo_idActivo"

    );
   

    /*for ($i=0 ; $i < count( $movimientoactivod); $i++) 
    {  
        $movimientoactivodetalle[] = get_object_vars($movimientoactivod[$i]);

    }*/
    

    $row = array();
    
    foreach ($movimientoactivod as $key => $value) 
    {  
        $valores = get_object_vars($value);
        $row[$key][] = $valores['MovimientoActivo_idMovimientoActivo'];
        $row[$key][] = $valores['Activo_idActivo'];
        $row[$key][] = $valores['codigoActivo'];   
        $row[$key][] = $valores['serieActivo'];  
        $row[$key][] = $valores['nombreActivo'];  
        $row[$key][] = $valores['idLocalizacion'];  
        $row[$key][] = $valores['nombreLocalizacion'];  

       
    }

    $output['aaData'] = $row;
    echo json_encode($output);

    //echo json_encode($movimientoactivodetalle);
    //print_r($movimientoactivodetalle);

?>