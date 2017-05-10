<?php

//Recibo por post el valor del campo clave, la consulta guardada en el campo hidden y el id del documento
$valor = $_POST['objeto'];


$datos = DB::table('sistemainformacion')
->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
->where('idSistemaInformacion', "=", 1)
->get();


$datos = get_object_vars($datos[0]);

   Config::set( 'database.connections.'.$datos['bdSistemaInformacion'], array 
    ( 
        'driver'     =>  $datos['motorbdSistemaInformacion'], 
        'host'       =>  $datos['ipSistemaInformacion'], 
        'port'       =>  $datos['puertoSistemaInformacion'], 
        'database'   =>  $datos['bdSistemaInformacion'], 
        'username'   =>  $datos['usuarioSistemaInformacion'], 
        'password'   =>  $datos['claveSistemaInformacion'], 
        'charset'    =>  'utf8', 
        'collation'  =>  'utf8_unicode_ci', 
        'prefix'     =>  '',
        'strict'    => false,
        'options'   => [    
        \PDO::ATTR_EMULATE_PREPARES => true
        ]
    )); 

$consulta = ('SELECT idTercero, nombre1Tercero, fechaElaboracionMovimiento,numeroMovimiento, idMovimiento, valorTotalMovimiento, nombreFormaPago, FormaPago_idFormaPago, totalUnidadesMovimiento, fechaMaximaMovimiento, diasFormaPago, nombre1Vendedor, Tercero_idVendedor from viewMovimientoEncabezado where Documento_idDocumento IN (28,35) and numeroMovimiento = "'.$valor.'"');

//Ejecuto la consulta conectado a la base de datos
$consultaMetadatos = DB::connection($datos['bdSistemaInformacion'])->Select(DB::raw($consulta));    

$valores = array();

$consultaDP = get_object_vars($consultaMetadatos[0]);

echo json_encode($consultaDP);
?>