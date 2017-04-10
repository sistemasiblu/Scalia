<?php 

$numeroOP = $_POST['op'];

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
    
    // Consulto la observación de la orden de producción
    $observacion = DB::Select('SELECT observacionOrdenProduccion FROM Iblu.OrdenProduccion
        where numeroOrdenProduccion = '. $numeroOP); 

    $observacionOP = get_object_vars($observacion[0]);

    $textarea = '';

    $textarea .= '<textarea id="contObservacion" name="contObservacion" style="width:570px; height:200px;">'.$observacionOP["observacionOrdenProduccion"].'</textarea>';


    $textarea .= '<input type="hidden" id="numeroOP" value ="'.$numeroOP.'">';

     
    echo json_encode($textarea);
?>