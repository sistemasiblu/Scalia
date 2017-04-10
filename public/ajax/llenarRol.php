<?php
// Realizo una consulta trayendo el idRol por post para poder mediante un ajax llenar el campo 
$idRol = $_POST['idRol'];

$consulta = DB::table('rol')
->select(DB::raw('nombreRol'))
->where ('idRol', "=", $idRol)
->get();

//Convierto un array en string
$nombrerol = get_object_vars($consulta[0]); 
echo json_encode($nombrerol['nombreRol']); //envio el nombre del campo mediante JSON
?>