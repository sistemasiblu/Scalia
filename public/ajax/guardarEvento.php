<?php 

$tercero = $_POST['tercero'];
$codigo = $_POST['codigo'];
$evento = $_POST['evento'];
$fechaIni = $_POST['fechaIni'];
$fechaFin = $_POST['fechaFin'];
$dias = $_POST['dias'];

DB::Select('INSERT INTO Iblu.Evento VALUES(0, '.$tercero.', "'.$codigo.'", "'.$evento.'", "'.$fechaIni.'", "'.$fechaFin.'", '.$dias.')');

echo json_encode("Guardado correctamente.")

?>