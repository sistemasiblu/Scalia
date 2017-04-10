<?php 

$codigo = $_POST['codigo'];
$temporada = $_POST['temporada'];
$fechaIni = $_POST['fechaIni'];
$fechaFin = $_POST['fechaFin'];
$tolerancia = $_POST['tolerancia'];

DB::Select('INSERT INTO Iblu.Temporada VALUES(0, "'.$codigo.'", "'.$temporada.'", "'.$fechaIni.'", "'.$fechaFin.'", '.$tolerancia.')');

echo json_encode("Guardado correctamente.")

?>