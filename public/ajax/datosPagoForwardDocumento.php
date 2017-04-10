<?php 

// $idSistemaInformacion = 1;
$lista = $_GET['lista'];
$registro = $_GET['registro'];

// $datos = DB::table('sistemainformacion')
// ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
// ->where('idSistemaInformacion', "=", $idSistemaInformacion)
// ->get();


// $datos = get_object_vars($datos[0]);
// // print_r($datos);
   
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

    $codigoDoc = DB::Select('SELECT codigoSayaListaFinanciacion from listafinanciacion where idListaFinanciacion = '.$lista);

    $codigoAlternoDoc = get_object_vars($codigoDoc[0]);
    
    //Consulto el nombre del documento en saya filtrando por el codigoAlterno que tengo en el maestro de listas de financiacion que está en scalia
    $consulta = DB::Select('
        SELECT numeroMovimiento, fechaElaboracionMovimiento, nombre1Tercero, valorTotalMovimiento
        from Iblu.Movimiento m 
        left join Iblu.Documento d on m.Documento_idDocumento = d.idDocumento 
        left join Iblu.Tercero t on m.Tercero_idTercero = t.idTercero where codigoAlternoDocumento = "'.$codigoAlternoDoc["codigoSayaListaFinanciacion"].'"');


    $row = array();

    foreach ($consulta as $key => $value) 
    {  
        $value = get_object_vars($consulta[$key]); 

        $row[$key][] = $value['numeroMovimiento']; 
        $row[$key][] = $value['fechaElaboracionMovimiento']; 
        $row[$key][] = $value['nombre1Tercero'];   
        $row[$key][] = $value['valorTotalMovimiento']; 
        $row[$key][] = $registro;
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>