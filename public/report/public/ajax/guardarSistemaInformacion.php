<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idSistemaInformacion = (isset($_POST['idSistemaInformacion']) ? $_POST['idSistemaInformacion'] : 0);
$valores = (isset($_POST['valores']) ? $_POST['valores'] : array());

switch ($accion) {
	case 'insertar':
		
		$datos = DB::select(
				"INSERT INTO sistemainformacion (codigoSistemaInformacion, nombreSistemaInformacion, webSistemaInformacion, ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion)
				VALUES ('".$valores[1]."', 
						'".$valores[2]."', 
						'".$valores[3]."', 
						'".$valores[4]."', 
						'".$valores[5]."', 
						'".$valores[6]."', 
						'".$valores[7]."', 
						'".$valores[8]."', 
						'".$valores[9]."');");

		
		echo json_encode($datos);
		break;

	case 'modificar':
		$datos = DB::select(
				"UPDATE  sistemainformacion 
				SET codigoSistemaInformacion = '".$valores[1]."',
					nombreSistemaInformacion = '".$valores[2]."', 
					webSistemaInformacion = '".$valores[3]."', 
					ipSistemaInformacion = '".$valores[4]."',
					puertoSistemaInformacion = '".$valores[5]."', 
					usuarioSistemaInformacion = '".$valores[6]."', 
					claveSistemaInformacion = '".$valores[7]."', 
					bdSistemaInformacion = '".$valores[8]."', 
					motorbdSistemaInformacion = '".$valores[9]."'
				WHERE idSistemaInformacion = ".$idSistemaInformacion.";");

		
		echo json_encode($datos);
		break;

	case 'eliminar':
		$datos = DB::select(
				"DELETE FROM  sistemainformacion 
				WHERE idSistemaInformacion = ".$idSistemaInformacion.";");

		echo json_encode($datos);
		break;

	default:
		# code...
		break;
}
?>