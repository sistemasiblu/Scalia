<?php 

$numeroDocumento = $_POST['numeroDocumento'];

$consulta = DB::Select('
	SELECT  *
	FROM controlingreso
	WHERE numeroDocumentoVisitanteControlIngreso = "'.$numeroDocumento.'" and fechaSalidaControlIngreso = "0000-00-00 00:00:00"');	

// $idControl = DB::Select('
// 	SELECT *, MAX(idControlIngreso) AS idControlIngreso
// 	FROM controlingreso
// 	WHERE numeroDocumentoVisitanteControlIngreso = '.$numeroDocumento);
// $maxID = get_object_vars($idControl[0]);

$datos = '';
// Si el usuario va a salir
if (count($consulta) > 0)
{
	$datos = get_object_vars($consulta[0]);

	$detalle = DB::Select('
	SELECT idControlIngresoDetalle, ControlIngreso_idControlIngreso, Dispositivo_idDispositivo, Marca_idMarca, referenciaDispositivoControlIngresoDetalle, observacionControlIngresoDetalle
	FROM controlingresodetalle cid
	LEFT JOIN controlingreso ci 
	ON cid.ControlIngreso_idControlIngreso = ci.idControlIngreso
	WHERE numeroDocumentoVisitanteControlIngreso = '.$numeroDocumento.
	' AND ControlIngreso_idControlIngreso = '.$datos["idControlIngreso"]);

	$respuesta = array("encabezado"=>$datos,"detalle"=>(count($detalle) > 0 ? $detalle : ""));

	echo json_encode($respuesta);
}
// El usaurio apenas va a ingresar
else
{
	// buscamos la cedula en SAYA para traer los nombres 
	$tercero = DB::SELECT('
	SELECT CONCAT(nombreATercero, " ", nombreBTercero) as nombreTercero, CONCAT(apellidoATercero, " ", apellidoBTercero) as apellidoTercero, TipoIdentificacion_idIdentificacion
	FROM Iblu.Tercero 
	WHERE documentoTercero = '.$numeroDocumento);

	// si encuentra los datos en SAYA
	if (count($tercero) > 0) 
	{
		$datos = get_object_vars($tercero[0]);

		$respuesta = array("encabezado"=>$datos,"detalle"=>'');

		echo json_encode($respuesta);		
	}
	// si no esta en SAYA lo buscamos en la tabla de control de ingreso de SCALIA
	else
	{
		$tercero = DB::Select('
		SELECT  nombreVisitanteControlIngreso as nombreTercero, 
				apellidoVisitanteControlIngreso as apellidoTercero, 
				TipoIdentificacion_idTipoIdentificacion as TipoIdentificacion_idIdentificacion
		FROM controlingreso
		WHERE numeroDocumentoVisitanteControlIngreso = "'.$numeroDocumento.'" 
		LIMIT 0,1');

		

		if(count($tercero) > 0)
			$datos = get_object_vars($tercero[0]);
		else
			$datos = array('nombreTercero' => '', 'apellidoTercero' => '', 'TipoIdentificacion_idIdentificacion' => '');

		$respuesta = array("encabezado"=>$datos,"detalle"=>'');

		echo json_encode($respuesta);
	}
}

?>