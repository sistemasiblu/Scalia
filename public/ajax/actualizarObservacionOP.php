<?php

$numeroOP = $_POST['op'];
$conetidoObservacion = $_POST['contenido'];

// $idSistemaInformacion = 1;

// $datos = DB::table('sistemainformacion')
// ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
// ->where('idSistemaInformacion', "=", $idSistemaInformacion)
// ->get();


// $datos = get_object_vars($datos[0]);
   
//    Config::set( 'database.connections.'.$datos['bdSistemaInformacion'], array 
//     ( 
//         'driver'     =>  $datos['motorbdSistemaInformacion'], 
//         'host'       =>  $datos['ipSistemaInformacion'], 
//         'port'       =>  $datos['puertoSistemaInformacion'], 
//         'database'   =>  $datos['bdSistemaInformacion'], 
//         'username'   =>  $datos['usuarioSistemaInformacion'], 
//         'password'   =>  $datos['claveSistemaInformacion'], 
//         'charset'    =>  'utf8', 
//         'collation'  =>  'utf8_unicode_ci', 
//         'prefix'     =>  ''
//     )); 

//     $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();

//     $tablas = DB::connection($datos['bdSistemaInformacion'])->select('SHOW FULL TABLES FROM '. $datos['bdSistemaInformacion']);


    //Actualizo la observación
    $observacion = DB::Select('UPDATE Iblu.OrdenProduccion
	Set observacionOrdenProduccion = "'.$conetidoObservacion.'"
	where numeroOrdenProduccion = '.$numeroOP); 


	echo json_encode('Observación actualizada correctamente.');

?>