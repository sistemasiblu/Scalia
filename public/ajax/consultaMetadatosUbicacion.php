<?php

//Recibo por post el valor del campo clave, los campos, la tabla a la cual debo consultar, la condicion y por ultimo el id del documento
$valor = $_POST['value'];

$consultaMetadatos = DB::Select('
    SELECT 
        idTercero,
        nombre1Tercero 
    FROM 
        '.\Session::get('baseDatosCompania').'.Tercero 
    WHERE documentoTercero = '.$valor);

    
echo json_encode($consultaMetadatos);
?>