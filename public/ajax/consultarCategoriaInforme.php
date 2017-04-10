<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$valor = (isset($_POST['idCategoriaInforme']) ? $_POST['idCategoriaInforme'] : 0);
$operador = (isset($_POST['idCategoriaInforme']) ? '=' : '>=');

$datos = DB::table('categoriainforme')
->select(DB::raw('idCategoriaInforme, nombreCategoriaInforme, observacionCategoriaInforme'))
->where('idCategoriaInforme', $operador, $valor)
->orderby('nombreCategoriaInforme')
->get();
// echo $operador. $valor;
$categoria = array();
for($i = 0; $i < count($datos); $i++) 
{
    $categoria[] = get_object_vars($datos[$i]);
}

echo json_encode($categoria);
?>