<?php

//Recibo por post el valor del campo clave, los campos, la tabla a la cual debo consultar, la condicion y por ultimo el id del documento
$valor = $_POST['value'];
$campos = $_POST['campos'];
$tablaDocumento = $_POST['tablaDocumento'];
$condicion = $_POST['condicion'];
$idDocumento = $_POST['idDocumento'];

// $idbd = $_POST['idbd'];



$bd = DB::Select('SELECT SistemaInformacion_idSistemaInformacion from documento where idDocumento = '.$idDocumento);

$idbd = get_object_vars($bd[0]);

// Como los campos a los que no se les asignó un campo de la tabla conectada quedan 'null' se les hace un replace para que no interfieran en la consulta
$campos = str_replace(', null', '', $campos);
$campos = str_replace('null,', '', $campos);

// print_r($consulta);
// return;

$datos = DB::table('sistemainformacion')
->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
->where('idSistemaInformacion', "=", $idbd)
->get();


$datos = get_object_vars($datos[0]);
// print_r($datos);
// return;   
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
        'prefix'     =>  '',
        'strict'    => false,
        'options'   => [    
        \PDO::ATTR_EMULATE_PREPARES => true
        ]
    )); 

//Ejecuto la consulta guardada en el campo hidden de armar metadatos y en array($valor) le asigno el value al where

    $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();

    $tablas = DB::connection($datos['bdSistemaInformacion'])->select('SHOW FULL TABLES FROM '. $datos['bdSistemaInformacion']);

    // $consultaMetadatos = DB::connection($datos['bdSistemaInformacion'])->select(DB::raw($consulta), $valor); 

    $consultaMetadatos = DB::connection($datos['bdSistemaInformacion'])->select(
        'SELECT '.$campos.' FROM '.$tablaDocumento.' WHERE '.$condicion.' "'.$valor.'"');

    // echo 'SELECT '.$campos.' FROM '.$tablaDocumento.' WHERE '.$condicion.' "'.$valor.'"';

    // return;
$consultaM = array();
$valores = array();
if (count($consultaMetadatos) > 0) 
{
    $consultaM = get_object_vars($consultaMetadatos[0]);

	//Consulto a documentopropiedad para saber cual es el id y el campo y no selecciono los que tengan como valor null que son los que no se conectan a la tabla o vista del sistema de informacion en el momento de crear las propiedades(metadatos) del documento.
	$consultaPropiedades = DB::Select('SELECT idDocumentoPropiedad, campoDocumentoPropiedad from documentopropiedad where campoDocumentoPropiedad != "null" and Documento_idDocumento = '.$idDocumento.'');

    // print_r($consultaPropiedades);
    // return;


	for ($i=0; $i < count($consultaPropiedades); $i++) 
	{ 
		$consultaDP = get_object_vars($consultaPropiedades[$i]);

		$valores[$i]["campo"] = $consultaDP["idDocumentoPropiedad"]; //A "campo" le asigno el iddocumentopropieda
		$valores[$i]["valor"] = $consultaM[$consultaDP["campoDocumentoPropiedad"]]; //a "valor" le asigno el valor de la consulta (consultaMetadatos)
	}

	
}

	
echo json_encode($valores);
?>