<?php

$idSistemaInformacion = $_POST['idSistema'];
$nombreTabla = $_POST['nombreTabla'];

$datos = DB::table('sistemainformacion')
->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
->where('idSistemaInformacion', "=", $idSistemaInformacion)
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
        'prefix'     =>  ''
    )); 
// $campos = DB::select('SELECT COLUMN_NAME From information_schema.COLUMNS 
//                      where TABLE_NAME = "'.$nombreTabla.'" and TABLE_SCHEMA = "' .$datos['bdSistemaInformacion'].'"');


        $conexion = DB::connection($datos['bdSistemaInformacion'])->select('SELECT COLUMN_NAME as Campo, COLUMN_COMMENT as Comentario From information_schema.COLUMNS 
                     where TABLE_NAME = "'.$nombreTabla.'" and TABLE_SCHEMA = "' .$datos['bdSistemaInformacion'].'"');

        echo json_encode($conexion);

?>