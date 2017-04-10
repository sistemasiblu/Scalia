<?php 

$idCargo = $_POST['idCargo'];

$consulta = DB::Select('
	SELECT nombrePerfilCargo, idPerfilCargo,porcentajeCargoHabilidad
	FROM cargo c
	LEFT JOIN  cargohabilidad ch
	ON c.idCargo = ch.Cargo_idCargo
	LEFT JOIN perfilcargo pc
	ON ch.PerfilCargo_idPerfilCargo = pc.idPerfilCargo
	WHERE idCargo = '.$idCargo);

//print_r($consulta);

echo json_encode($consulta);
?>


