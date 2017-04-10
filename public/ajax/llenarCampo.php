<?php
// Realizo una consulta trayendo el idCampoCRM por post para poder mediante un ajax llenar el campo 
$idCampoCRM = $_POST['idCampoCRM'];

$campo = DB::table('campocrm')
->select(DB::raw('descripcionCampoCRM'))
->where ('idCampoCRM', "=", $idCampoCRM)
->get();

//Convierto un array en string
$nombrecampo = get_object_vars($campo[0]); 
echo json_encode($nombrecampo['descripcionCampoCRM']); //envio el nombre del campo mediante JSON
?>