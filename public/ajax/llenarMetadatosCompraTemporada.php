<?php 

$idTemporada = (isset($_POST['idTemporada']) ? $_POST['idTemporada'] : '');

$temp = '';
if ($idTemporada != '')
{
	$temp = DB::Select('
		SELECT 
		    Tercero_idCliente,
		    nombreClienteCompra,
		    formaPagoClienteCompra,
		    eventoCompra,
		    diaPagoClienteCompra
		FROM
		    compra
		WHERE
		    Temporada_idTemporada = '.$idTemporada);
}

echo json_encode($temp);

?>