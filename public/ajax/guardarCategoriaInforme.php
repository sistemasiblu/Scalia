<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idCategoriaInforme = (isset($_POST['idCategoriaInforme']) ? $_POST['idCategoriaInforme'] : 0);
$valores = (isset($_POST['valores']) ? $_POST['valores'] : array());

switch ($accion) {
	case 'insertar':
		
		$datos = DB::insert(
				"INSERT INTO categoriainforme (nombreCategoriaInforme, observacionCategoriaInforme)
				VALUES ('".$valores[1]."', 
						'".$valores[2]."');");

		
		echo json_encode($datos);
		break;

	case 'modificar':
		$datos = DB::update(
				"UPDATE  categoriainforme 
				SET nombreCategoriaInforme = '".$valores[1]."', 
					observacionCategoriaInforme = '".$valores[2]."'
				WHERE idCategoriaInforme = ".$idCategoriaInforme.";");

		
		echo json_encode($datos);
		break;

	case 'eliminar':
		$datos = DB::delete(
				"DELETE FROM  categoriainforme 
				WHERE idCategoriaInforme = ".$idCategoriaInforme.";");

		echo json_encode($datos);
		break;

	default:
		# code...
		break;
}
?>