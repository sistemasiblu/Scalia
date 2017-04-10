<?php


$idCompra = isset($_POST['idCompra']) ? $_POST['idCompra'] : '';

if ($idCompra != '') 
{
	$datosCompra = DB::Select('SELECT nombreTemporadaCompra, nombreProveedorCompra, numeroCompra, eventoCompra, fechaDeliveryCompra from compra where idCompra = '.$idCompra);

	$compra = get_object_vars($datosCompra[0]);
}
else
{
	$compra = array();
}

echo json_encode($compra);
?>