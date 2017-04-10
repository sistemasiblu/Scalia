<?php

$datos = ['host' => $_POST['host'], 'puerto' => $_POST['puerto'], 'usuario' => $_POST['usuario'], 'clave' => $_POST['clave'], 'bd' => $_POST['bd'], 'motorbd' => $_POST['motorbd']];
   
   Config::set( 'database.connections.'.$datos['bd'], array 
    ( 
        'driver'     =>  $datos['motorbd'], 
        'host'       =>  $datos['host'], 
        'port'       =>  $datos['puerto'], 
        'database'   =>  $datos['bd'], 
        'username'   =>  $datos['usuario'], 
        'password'   =>  $datos['clave'], 
        'charset'    =>  'utf8', 
        'collation'  =>  'utf8_unicode_ci', 
        'prefix'     =>  ''
    )); 

    // try 
    // {
        $datos = DB::connection($datos['bd'])->getDatabaseName();
        echo  json_encode("Conectado correctamente a la base de datos: ".$datos.".");
    // } 
    // catch (\Exception $e) 
    // {
    //     echo '<script type="text/javascript">alert("No se ha podido conectar");</script>';
    // }      

?>