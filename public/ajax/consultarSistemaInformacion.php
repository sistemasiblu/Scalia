<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$valor = (isset($_POST['idSistemaInformacion']) ? $_POST['idSistemaInformacion'] : 0);
$operador = (isset($_POST['idSistemaInformacion']) ? '=' : '>=');
$esWeb = (isset($_POST['esWeb']) ? [1] : [0,1]);

$datos = DB::table('sistemainformacion')
->select(DB::raw('idSistemaInformacion, codigoSistemaInformacion, nombreSistemaInformacion, webSistemaInformacion, ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
->where('idSistemaInformacion', $operador, $valor)
->whereIn('webSistemaInformacion', $esWeb)
->get();
// echo $operador. $valor;
$sistema = array();
for($i = 0; $i < count($datos); $i++) 
{
    $sistema[] = get_object_vars($datos[$i]);
}

echo json_encode($sistema);
?>