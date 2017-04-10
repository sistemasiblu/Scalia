<?php 

// $id = DB::Select('SELECT idSistemaInformacion from sistemainformacion where bdSistemaInformacion = "Iblu"');
// $idSistemaInformacion = get_object_vars($id[0]);

 $unidadM = $_POST['unidadMedida'];
 

    // $datos = DB::table('sistemainformacion')
    // ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
    // ->where('idSistemaInformacion', "=", $idSistemaInformacion["idSistemaInformacion"])
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

    // $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();

    // $tablas = DB::connection($datos['bdSistemaInformacion'])->select('SHOW FULL TABLES FROM '. $datos['bdSistemaInformacion']);

    $unidMedida = DB::Select('
                    SELECT * from Iblu.UnidadMedida');

    $select = '';
    for ($i=0; $i <count($unidMedida) ; $i++) 
    { 
    	$medida = get_object_vars($unidMedida[$i]);
    	$select .= '<option id="uMedida" value="'.$medida["idUnidadMedida"].'" '.($unidadM == $medida["idUnidadMedida"] ? 'selected = "selected"' : '').' >'.$medida["nombreUnidadMedida"].'</option>';
    }

    echo json_encode($select);

?>