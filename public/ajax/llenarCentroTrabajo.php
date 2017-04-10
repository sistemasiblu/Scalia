<?php 

$idTercero = $_POST['idTercero'];

$centrotrabajo = DB::Select('
	SELECT nombreCentroTrabajo 
	FROM Iblu.Tercero t left join Iblu.CentroTrabajo ct on t.CentroTrabajo_idCentroTrabajo = ct.idCentroTrabajo
	WHERE idTercero = '.$idTercero);

$centro = get_object_vars($centrotrabajo[0]);

echo json_encode($centro);

?>