<?php
// Realizo una consulta trayendo el idTercero por post para poder mediante un ajax llenar el campo cargo
$idTercero = $_POST['idTercero'];

$cargo = DB::table('tercero')
->leftjoin('cargo', 'tercero.Cargo_idCargo', "=", 'cargo.idCargo')
->select(DB::raw('idCargo, nombreCargo'))
->where ('idTercero', "=", $idTercero)
->get();

//Convierto un array en string
$nombrecargo = get_object_vars($cargo[0]); 
echo json_encode($nombrecargo['nombreCargo']); //envio el nombre del cargo mediante JSON
?>