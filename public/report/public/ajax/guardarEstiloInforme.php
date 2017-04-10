<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idEstiloInforme = (isset($_POST['idEstiloInforme']) ? $_POST['idEstiloInforme'] : 0);
$valores = (isset($_POST['valores']) ? $_POST['valores'] : array());

switch ($accion) {
	case 'insertar':
		
		$datos = DB::select(
				"INSERT INTO estiloinforme (nombreEstiloInforme, colorFondoEstiloInforme, colorBordeEstiloInforme, colorTextoEstiloInforme, fuenteTextoEstiloInforme, tamañoTextoEstiloInforme, negrillaEstiloInforme, italicaEstiloInforme, subrayadoEstiloInforme)
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
				"UPDATE  estiloinforme 
				SET nombreEstiloInforme = '".$valores[1]."',
					colorFondoEstiloInforme = '".$valores[2]."', 
					colorBordeEstiloInforme = '".$valores[3]."', 
					colorTextoEstiloInforme = '".$valores[4]."',
					fuenteTextoEstiloInforme = '".$valores[5]."', 
					tamañoTextoEstiloInforme = '".$valores[6]."', 
					negrillaEstiloInforme = '".$valores[7]."', 
					italicaEstiloInforme = '".$valores[8]."', 
					subrayadoEstiloInforme = '".$valores[9]."'
				WHERE idEstiloInforme = ".$idEstiloInforme.";");

		
		echo json_encode($datos);
		break;

	case 'eliminar':
		$datos = DB::select(
				"DELETE FROM  estiloinforme 
				WHERE idEstiloInforme = ".$idEstiloInforme.";");

		echo json_encode($datos);
		break;

	default:
		# code...
		break;
}
?>