<?php 
$idCategoriaInforme = (isset($_POST['idCategoriaInforme']) ? $_POST['idCategoriaInforme'] : 0);

$datos = DB::table('informe')
			->select(DB::raw('idInforme, nombreInforme, descripcionInforme'))
			->where('CategoriaInforme_idCategoriaInforme', '=', $idCategoriaInforme)
			->get();

$informe = array();
for($i = 0; $i < count($datos); $i++) 
{
    $informe[] = get_object_vars($datos[$i]);
}

echo json_encode($informe);

?>