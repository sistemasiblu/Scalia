<?php 

$valor = $_POST['valor'];
$campo = $_POST['campo'];
$tabla = $_POST['tabla'];

$codigo = DB::Select('
	SELECT '.$campo.' from Iblu.'.$tabla.' where '.$campo.' = "'.$valor.'"');

	if (count($codigo) > 0) 
		echo json_encode("Ya existe.");
	else
		echo json_encode("");

?>