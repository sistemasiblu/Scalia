<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idInforme = (isset($_POST['idInforme']) ? $_POST['idInforme'] : 0);

$datos = DB::table('informe')
			->select(DB::raw('idInforme, nombreInforme, descripcionInforme'))
			->where('idInforme', '=', $idInforme)
			->get();

$informe = array();
for($i = 0; $i < count($datos); $i++) 
{
    $informe[] = get_object_vars($datos[$i]);
}

echo json_encode($informe);

?>