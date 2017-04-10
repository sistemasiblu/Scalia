<?php

$idSistemaInformacion = $_POST['idSistema'];

$datos = DB::table('sistemainformacion')
->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
->where('idSistemaInformacion', "=", $idSistemaInformacion)
->get();

$datos = get_object_vars($datos[0]);
// print_r($datos);
   
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
        'prefix'     =>  ''
    )); 

    $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();
        // echo  json_encode("Conectado correctamente a la base de datos: ".$conexion.".");

    $tablas = DB::connection($datos['bdSistemaInformacion'])->select('SHOW FULL TABLES FROM '. $datos['bdSistemaInformacion']);

        // $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();

        $respuesta = [$tablas,$datos['bdSistemaInformacion']];
        // print_r($respuesta);
        echo json_encode($respuesta);

?>