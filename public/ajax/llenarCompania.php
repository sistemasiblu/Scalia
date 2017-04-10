<?php
// Realizo una consulta trayendo el idCompania por post para poder mediante un ajax llenar el campo 
$idCompania = $_POST['idCompania'];

$consulta = DB::table('compania')
->select(DB::raw('nombreCompania'))
->where ('idCompania', "=", $idCompania)
->get();

//Convierto un array en string
$nombrecompania = get_object_vars($consulta[0]); 
echo json_encode($nombrecompania['nombreCompania']); //envio el nombre del campo mediante JSON
?>