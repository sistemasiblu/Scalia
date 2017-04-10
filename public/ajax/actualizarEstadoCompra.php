<?php 

$compra = $_POST['compra'];
$observacion = $_POST['observacion'];

$mensaje = '';

if ($observacion != '') 
{
	$estado = DB::Select('SELECT estadoCompra from compra where idCompra = '.$compra);

	$est = get_object_vars($estado[0]);


	if ($est['estadoCompra'] == "Abierto") 
	{
		$sql = DB::Select('UPDATE compra SET estadoCompra = "Cerrado"
		where idCompra = '.$compra);	
	}

	elseif($est['estadoCompra'] == "Cerrado")
	{
		$sql = DB::Select('UPDATE compra SET estadoCompra = "Abierto"
		where idCompra = '.$compra);
	}

	$obs = DB::Select('UPDATE compra SET observacionCompra = "'.$observacion.'"  
		where idCompra = '.$compra);

	$mensaje = 'Estado actualizado correctamente.';

}

else
{
	$mensaje = 'Debe escribir una observación.';
}


echo json_encode($mensaje);
?>