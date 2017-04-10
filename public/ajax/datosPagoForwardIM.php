<?php 

// $idSistemaInformacion = 1;

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
    
    //Consulto el nombre del documento en saya las ims que tengan saldo pendiente en cartera
    $consulta = DB::Select("
        SELECT 
            numeroMovimiento,
            fechaElaboracionMovimiento,
            nombre1Tercero,
            valorTotalMovimiento
        FROM
            Iblu.Cartera c
                LEFT JOIN
            Iblu.Movimiento m ON c.Movimiento_idMovimiento = m.idMovimiento
                LEFT JOIN
            Iblu.Documento d ON m.Documento_idDocumento = d.idDocumento
                LEFT JOIN
            Iblu.Tercero t ON m.Tercero_idTercero = t.idTercero
                LEFT JOIN
            Iblu.Periodo p ON c.Periodo_idPeriodo = p.idPeriodo
        WHERE
            idDocumento = 20
                AND estadoMovimiento = 'ACTIVO'
                AND saldoCartera != 0
                AND codigoAlternoPeriodo = '1610'");

    $row = array();

    foreach ($consulta as $key => $value) 
    {  
        $value = get_object_vars($consulta[$key]); 

        $row[$key][] = $value['numeroMovimiento']; 
        $row[$key][] = $value['fechaElaboracionMovimiento']; 
        $row[$key][] = $value['nombre1Tercero'];   
        $row[$key][] = $value['valorTotalMovimiento']; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>