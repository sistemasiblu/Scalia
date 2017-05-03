<?php 

$compra = $_POST['compra'];
$observacion = $_POST['observacion'];

$mensaje = '';

if ($observacion != '') 
{
	$estado = DB::Select('SELECT numeroCompra, estadoCompra from compra where idCompra = '.$compra);

	$est = get_object_vars($estado[0]);


	if ($est['estadoCompra'] == "Abierto") 
	{
		$sql = DB::Select('UPDATE compra SET estadoCompra = "Cerrado"
		where idCompra = '.$compra);

		$destinatario = 'comercio1@ciiblu.com; comercio4@ciiblu.com';

        $mail['destinatarioCorreo'] = explode(';', $destinatario);
        $mail['asuntoCorreo'] = 'Cierre de compra';
        $mail['mensaje'] = 'Se ha cerrado la compra número <b>'.$est["numeroCompra"].'</b>, realizada por el usuario '.\Session::get("nombreUsuario").'.';

        Mail::send('emails.contact',$mail,function($msj) use ($mail)
        {
            $msj->to($mail['destinatarioCorreo']);
            $msj->subject($mail['asuntoCorreo']);
        });

	}

	elseif($est['estadoCompra'] == "Cerrado")
	{
		$sql = DB::Select('UPDATE compra SET estadoCompra = "Abierto"
		where idCompra = '.$compra);

		$destinatario = 'comercio1@ciiblu.com; comercio4@ciiblu.com';

        $mail['destinatarioCorreo'] = explode(';', $destinatario);
        $mail['asuntoCorreo'] = 'Re apertura de compra';
        $mail['mensaje'] = 'Se ha re abierto la compra número <b>'.$est["numeroCompra"].'</b>, realizada por el usuario '.\Session::get("nombreUsuario").'.';

        Mail::send('emails.contact',$mail,function($msj) use ($mail)
        {
            $msj->to($mail['destinatarioCorreo']);
            $msj->subject($mail['asuntoCorreo']);
        });
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