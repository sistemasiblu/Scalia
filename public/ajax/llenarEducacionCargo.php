<?php 

$idCargo = $_POST['idCargo'];

$consulta = DB::Select('
	SELECT nombrePerfilCargo, idPerfilCargo,porcentajeCargoEducacion, aniosExperienciaCargo
	FROM cargo c
	LEFT JOIN  cargoeducacion ce
	ON c.idCargo = ce.Cargo_idCargo
	LEFT JOIN perfilcargo pc
	ON ce.PerfilCargo_idPerfilCargo = pc.idPerfilCargo
	WHERE idCargo = '.$idCargo);

//print_r($consulta);

echo json_encode($consulta);
?>


