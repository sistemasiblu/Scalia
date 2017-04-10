<?php 
$idInforme = (isset($_POST['idInforme']) ? $_POST['idInforme'] : 0);

$datos = DB::delete('delete from informe where idInforme = '. $idInforme);


echo json_encode($datos);

?>