<?php 

//Recibo por post el valor del campo clave, la consulta guardada en el campo hidden y el id del documento
$condicion = $_POST['condicion'];
$consulta = 'select idTercero, nombre1Tercero, direccionTercero, documentoTercero, Ciudad_idCiudad from Tercero where idTercero = 32';
$idbd = $_POST['idSistema'];


$datos = DB::table('sistemainformacion')
->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
->where('idSistemaInformacion', "=", $idbd)
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

//Ejecuto la consulta guardada en el campo hidden de armar metadatos y en array($valor) le asigno el value al where
$consultaMetadatos = DB::connection($datos['bdSistemaInformacion'])->select(DB::raw($consulta));	
print_r($consultaMetadatos);
	
echo json_encode($valores);
?>
