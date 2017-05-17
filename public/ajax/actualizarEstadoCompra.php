<?php 

$compra = $_POST['compra'];
$observacion = $_POST['observacion'];

$mensaje = '';

if ($observacion != '') 
{
	$estado = DB::Select('SELECT numeroCompra, estadoCompra, nombreTemporadaCompra, nombreProveedorCompra, nombreClienteCompra, valorCompra from compra where idCompra = '.$compra);

	$est = get_object_vars($estado[0]);


	if ($est['estadoCompra'] == "Abierto") 
	{
		$sql = DB::Select('UPDATE compra SET estadoCompra = "Cerrado"
		where idCompra = '.$compra);

		$destinatario = 'comercio1@ciiblu.com;comercio4@ciiblu.com';

        $mail['destinatarioCorreo'] = explode(';', $destinatario);
        $mail['asuntoCorreo'] = 'Cierre de compra';
        

        $styleTableEnc = 'style="border: 1px solid; background-color: #255986; color: white;"';
        $styleTableBody = 'style="border: 1px solid;"';
        $styleTableBodyN = 'style="border: 1px solid;  text-align: right;"';  

        $mail['mensaje'] = 'Se ha cerrado la compra número <b>'.$est["numeroCompra"].'</b>, realizada por el usuario '.\Session::get("nombreUsuario").'.</br></br>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
        <table cellspacing="0" class="table table-striped table-bordered table-hover" style="width:100%;">
            <tr>
                <th colspan="6" style=" background-color:#255986; color:white;">Compra: '.$est['numeroCompra'].'</th>
            </tr>
            <tr>
                <th '.$styleTableEnc.'>Compra</th>
                <th '.$styleTableEnc.'>Temporada</th>
                <th '.$styleTableEnc.'>Proveedor</th>
                <th '.$styleTableEnc.'>Cliente</th>
                <th '.$styleTableEnc.'>Valor</th>
                <th '.$styleTableEnc.'>Usuario</th>
              </tr>
            <tr>
                <td '.$styleTableBody.'>'.$est["numeroCompra"].'</td>
                <td '.$styleTableBody.'>'.$est["nombreTemporadaCompra"].'</td>
                <td '.$styleTableBody.'>'.$est["nombreProveedorCompra"].'</td>
                <td '.$styleTableBody.'>'.$est["nombreClienteCompra"].'</td>
                <td '.$styleTableBodyN.'>'.$est["valorCompra"].'</td>
                <td '.$styleTableBody.'>'.\Session::get("nombreUsuario").'</td>
            </tr>
        </table>';

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

		$destinatario = 'comercio1@ciiblu.com;comercio4@ciiblu.com';

        $mail['destinatarioCorreo'] = explode(';', $destinatario);
        $mail['asuntoCorreo'] = 'Re apertura de compra';

        $styleTableEnc = 'style="border: 1px solid; background-color: #255986; color: white;"';
        $styleTableBody = 'style="border: 1px solid;"';
        $styleTableBodyN = 'style="border: 1px solid;  text-align: right;"';  

        $mail['mensaje'] = 'Se ha re abierto la compra número <b>'.$est["numeroCompra"].'</b>, realizada por el usuario '.\Session::get("nombreUsuario").'.</br></br>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
        <table cellspacing="0" class="table table-striped table-bordered table-hover" style="width:100%;">
            <tr>
                <th colspan="6" style=" background-color:#255986; color:white;">Compra: '.$est['numeroCompra'].'</th>
            </tr>
            <tr>
                <th '.$styleTableEnc.'>Compra</th>
                <th '.$styleTableEnc.'>Temporada</th>
                <th '.$styleTableEnc.'>Proveedor</th>
                <th '.$styleTableEnc.'>Cliente</th>
                <th '.$styleTableEnc.'>Valor</th>
                <th '.$styleTableEnc.'>Usuario</th>
              </tr>
            <tr>
                <td '.$styleTableBody.'>'.$est["numeroCompra"].'</td>
                <td '.$styleTableBody.'>'.$est["nombreTemporadaCompra"].'</td>
                <td '.$styleTableBody.'>'.$est["nombreProveedorCompra"].'</td>
                <td '.$styleTableBody.'>'.$est["nombreClienteCompra"].'</td>
                <td '.$styleTableBodyN.'>'.$est["valorCompra"].'</td>
                <td '.$styleTableBody.'>'.\Session::get("nombreUsuario").'</td>
            </tr>
        </table>';

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