<?php

$numeroEmbarque = $_POST['numeroEmbarque'];

 // $idSistemaInformacion = 1;

 //    $datos = DB::table('sistemainformacion')
 //    ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
 //    ->where('idSistemaInformacion', "=", $idSistemaInformacion)
 //    ->get();


 //    $datos = get_object_vars($datos[0]);
       
 //       Config::set( 'database.connections.'.$datos['bdSistemaInformacion'], array 
 //        ( 
 //            'driver'     =>  $datos['motorbdSistemaInformacion'], 
 //            'host'       =>  $datos['ipSistemaInformacion'], 
 //            'port'       =>  $datos['puertoSistemaInformacion'], 
 //            'database'   =>  $datos['bdSistemaInformacion'], 
 //            'username'   =>  $datos['usuarioSistemaInformacion'], 
 //            'password'   =>  $datos['claveSistemaInformacion'], 
 //            'charset'    =>  'utf8', 
 //            'collation'  =>  'utf8_unicode_ci', 
 //            'prefix'     =>  ''
 //        )); 

 //    $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();

 //    $tablas = DB::connection($datos['bdSistemaInformacion'])->select('SHOW FULL TABLES FROM '. $datos['bdSistemaInformacion']);

    $numEmbarque = DB::Select('
                    SELECT numeroEmbarque from Iblu.Embarque where numeroEmbarque = "'.$numeroEmbarque.'"');


    $respuesta = '';
    if ($numEmbarque != null) 
    {
    	$respuesta .= 'El numero de embarque ya existe';
    }
    else
    {
    	$respuesta .= 'El numero de embarque no existe';
    }

    echo json_encode($respuesta);

?>
