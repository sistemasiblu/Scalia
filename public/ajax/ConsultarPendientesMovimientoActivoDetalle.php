
<?php
@$id=$_GET['idMovimientoActivo'];


//echo $id;

    //$activo = \App\Activo::All();
    $compania=\Session::get('nombreCompania');
    
    $row = array();
    $movimientoactivod = DB::select(
     "SELECT 
    padredet.idMovimientoActivoDetalle,
    padredet.MovimientoActivo_idMovimientoActivo,
    padredet.Localizacion_idOrigen AS nombreLocalizacionO,
    padredet.Localizacion_idDestino AS nombreLocalizacionD,
    padredet.Activo_idActivo,
    activo.codigoActivo,activo.nombreActivo,activo.serieActivo,
    padredet.cantidadMovimientoActivoDetalle,
    padredet.observacionMovimientoActivoDetalle,
    hijodet.MovimientoActivo_idMovimientoActivo as idInterno,
    hijodet.Activo_idActivo as ActivoInterno
FROM
    movimientoactivo padre
    LEFT JOIN movimientoactivodetalle padredet
    ON padre.idMovimientoActivo = padredet.MovimientoActivo_idMovimientoActivo
    LEFT JOIN movimientoactivodetalle hijodet 
    ON padre.idMovimientoActivo = hijodet.MovimientoActivo_idDocumentoInterno
    AND padredet.Activo_idActivo = hijodet.Activo_idActivo
    LEFT JOIN movimientoactivo hijo 
    ON hijodet.MovimientoActivo_idMovimientoActivo = hijo.idMovimientoActivo
    INNER JOIN activo 
    ON padredet.Activo_idActivo = activo.idActivo
WHERE
    padre.idMovimientoActivo IN ($id) AND hijodet.MovimientoActivo_idMovimientoActivo IS NULL;
    "
    );
   

    for ($i=0 ; $i < count( $movimientoactivod); $i++) 
    {  
        $movimientoactivodetalle[] = get_object_vars($movimientoactivod[$i]);

    }
    

    echo json_encode($movimientoactivodetalle);
    //print_r($movimientoactivodetalle);

?>