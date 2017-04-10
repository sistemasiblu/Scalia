<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$valor = (isset($_POST['idEstiloInforme']) ? $_POST['idEstiloInforme'] : 0);
$operador = (isset($_POST['idEstiloInforme']) ? '=' : '>=');

$datos = DB::table('estiloinforme')
			->select(DB::raw('idEstiloInforme, nombreEstiloInforme, colorFondoEstiloInforme, colorBordeEstiloInforme, colorTextoEstiloInforme, fuenteTextoEstiloInforme, tamañoTextoEstiloInforme, negrillaEstiloInforme, italicaEstiloInforme, subrayadoEstiloInforme'))
			->where('idEstiloInforme', $operador, $valor)
			->get();

$estilo = array();
for($i = 0; $i < count($datos); $i++) 
{
    $estilo[] = get_object_vars($datos[$i]);
}

echo json_encode($estilo);

?>