<?php 

			// use Mail;
// include '../../vendor/laravel/framework/src/Illuminate/Support/Facades/Mail.php';
			// $mail = array();
            // 	$destinatario = 'santiago.viana@ciiblu.com';


            // 	$mail['destinatario'] = explode(';', $destinatario);
            // 	$mail['asunto'] = 'Cron';
            // 	$mail['mensaje'] = 'Se ejecutó el cron';

            // 	Mail::send('emails.contact',$mail,function($msj) use ($mail)
            // 	{
	        //         $msj->to($mail['destinatario']);
	        //         $msj->subject($mail['asunto']);
            // 	}); 


		#Se hace un update a la tabla compra donde primero se obtiene el id de movimiento que está en saya
		#para luego actualizar ese id en la tabla compra en el campo Movimiento_idMovimiento
		DB::update(
			'UPDATE compra 
			LEFT JOIN Iblu.Movimiento
			ON numeroMovimiento = numeroCompra AND Documento_idDocumento = 28
			SET compra.Movimiento_idMovimiento = Movimiento.idMovimiento
			WHERE (compra.Movimiento_idMovimiento = 1 or compra.Movimiento_idMovimiento IS NULL or compra.Movimiento_idMovimiento = 0) AND 
					compra.DocumentoImportacion_idDocumentoImportacion = 1');	

		#Se hace otro update a la tabla compra para actalizar las temporadas que estén NULL o con ID 1
		DB::update(
			'UPDATE compra
			LEFT JOIN
			    Iblu.Temporada ON nombreTemporadaCompra = nombreTemporada
			SET 
			    compra.Temporada_idTemporada = Temporada.idTemporada
			WHERE
			    (compra.Temporada_idTemporada = 1
			        OR compra.Temporada_idTemporada IS NULL)');

		#Update para los proveedores
		DB::update('
			UPDATE compra
        	LEFT JOIN
			    Iblu.Tercero ON nombreProveedorCompra = nombre1Tercero
			SET 
			    compra.Tercero_idProveedor = Tercero.idTercero
			WHERE
			    (compra.Tercero_idProveedor = 1
			        OR compra.Tercero_idProveedor IS NULL)');

		#Update para los vendedores
		DB::update('
			UPDATE compra
        	LEFT JOIN
			    Iblu.Tercero ON compradorVendedorCompra = nombre1Tercero
			SET 
			    compra.Tercero_idVendedor = Tercero.idTercero
			WHERE
			    (compra.Tercero_idVendedor = 1
			        OR compra.Tercero_idVendedor IS NULL)');

		#Update para los clientes
		DB::update('
			UPDATE compra
        	LEFT JOIN
			    Iblu.Tercero ON nombreClienteCompra = nombre1Tercero
			SET 
			    compra.Tercero_idCliente = Tercero.idTercero
			WHERE
			    (compra.Tercero_idCliente = 1
			        OR compra.Tercero_idCliente IS NULL)');

		#Update para el puerto
		DB::update('
			UPDATE compra
        	LEFT JOIN
			    Iblu.Ciudad ON nombreCiudadCompra = nombreCiudad
			SET 
			    compra.Ciudad_idPuerto = Ciudad.idCiudad
			WHERE
			    (compra.Ciudad_idPuerto = 1
			        OR compra.Ciudad_idPuerto IS NULL)');

		// return;

		#Estilos para la tabla
		$styleTableEnc = 'style="border: 1px solid; background-color: #255986; color: white;"';
        $styleTableBody = 'style="border: 1px solid;"';
        $styleTableBodyN = 'style="border: 1px solid;  text-align: right;"';

		#Consulto todas las compras de las que aún no se ha enviado correo
		$compras = DB::Select(
			'SELECT 
				numeroCompra,
				envioCorreoCompra,
				nombreTemporadaCompra,
				nombreProveedorCompra,
				formaPagoProveedorCompra,
				nombreClienteCompra,
				valorCompra,
				cantidadCompra,
				fechaDeliveryCompra,
				observacionCompra,
				nombreDocumentoImportacion
			FROM
				(SELECT 
					numeroCompra,
						envioCorreoCompra,
						nombreTemporadaCompra,
						nombreProveedorCompra,
						formaPagoProveedorCompra,
						nombreClienteCompra,
						valorCompra,
						cantidadCompra,
						fechaDeliveryCompra,
						observacionCompra,
						DocumentoImportacion_idDocumentoImportacion
				FROM
					(SELECT 
					numeroCompra,
						envioCorreoCompra,
						nombreTemporadaCompra,
						nombreProveedorCompra,
						formaPagoProveedorCompra,
						nombreClienteCompra,
						valorCompra,
						cantidadCompra,
						fechaDeliveryCompra,
						observacionCompra,
						DocumentoImportacion_idDocumentoImportacion
				FROM
					compra c
				GROUP BY numeroCompra , numeroVersionCompra
				ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
				GROUP BY numeroCompra) AS comp
				LEFT JOIN documentoimportacion di ON comp.DocumentoImportacion_idDocumentoImportacion = di.idDocumentoImportacion
			WHERE
				envioCorreoCompra = 0');

		$mail = array();
		#Recorro todas las compras encontradas
		for ($i=0; $i < count($compras); $i++) 
		{ 
			$compra = get_object_vars($compras[$i]);
			#Consulto los adjuntos radicados en el sistema relacionados a las compras y que aún no se han enviado por correo
			$adjunto = DB::Select('
				SELECT 
					archivoRadicadoVersion
				FROM
					radicadoversion rv
					LEFT JOIN 
						radicadodocumentopropiedad rdp ON rv.Radicado_idRadicado = rdp.Radicado_idRadicado
					LEFT JOIN
						compra c ON rdp.valorRadicadoDocumentoPropiedad = c.numeroCompra
				WHERE valorRadicadoDocumentoPropiedad = "'.$compra['numeroCompra'].'"');

			#Si encuentra archivos asociados a esta compra, envía el correo con los adjuntos encontrados
			if (count($adjunto) > 0) 
            {
            	#Armo la tabla que irá en el mensaje del correo
            	$mail['mensaje'] ='Se ha realizado la creación de una nueva compra en Scalia con las siguientes especificaciones: <br><br>
            	<table cellspacing="0" class="table table-striped table-bordered table-hover" style="width:100%;">
		            <tr>
		                <th colspan="10" style=" background-color:#255986; color:white;">Compra: '.$compra['numeroCompra'].'</th>
		            </tr>
		            <tr>
		                <th '.$styleTableEnc.'>Temporada</th>
		                <th '.$styleTableEnc.'>Proveedor</th>
		                <th '.$styleTableEnc.'>Forma de pago proveedor</th>
		                <th '.$styleTableEnc.'>Cliente</th>
		                <th '.$styleTableEnc.'>Valor</th>
		                <th '.$styleTableEnc.'>Unidades</th>
		                <th '.$styleTableEnc.'>Factor</th>
		                <th '.$styleTableEnc.'>Delivery</th>
						<th '.$styleTableEnc.'>Observación</th>
						<th '.$styleTableEnc.'>Compra</th>
		            </tr>
		            <tr>
	                    <td '.$styleTableBody.'>'.$compra["nombreTemporadaCompra"].'</td>
	                    <td '.$styleTableBody.'>'.$compra["nombreProveedorCompra"].'</td>
	                    <td '.$styleTableBody.'>'.$compra["formaPagoProveedorCompra"].'</td>
	                    <td '.$styleTableBody.'>'.$compra["nombreClienteCompra"].'</td>
	                    <td '.$styleTableBodyN.'>'.$compra["valorCompra"].'</td>
	                    <td '.$styleTableBody.'>'.$compra["cantidadCompra"].'</td>
	                    <td '.$styleTableBody.'>'."".'</td>
	                    <td '.$styleTableBody.'>'.$compra["fechaDeliveryCompra"].'</td>
						<td '.$styleTableBody.'>'.$compra["observacionCompra"].'</td>
						<td '.$styleTableBody.'>'.$compra["nombreDocumentoImportacion"].'</td>
	                </tr>
	            </table>';

            	$destinatario = 'comercio1@ciiblu.com;mariae.palacio@ciiblu.com;claudiagomez@ciiblu.com;victoria.perez@ciiblu.com;yudyrendon@ciiblu.com;comercioextiblu@ciiblu.com;extiblu4@ciiblu.com;comercio4@ciiblu.com;extiblu11@ciiblu.com';

            	$mail['destinatario'] = explode(';', $destinatario);
            	$mail['asunto'] = 'Creación de compra en Scalia';

            	Mail::send('emails.contact',$mail,function($msj) use ($mail, $adjunto)
            	{
	                $msj->to($mail['destinatario']);
	                $msj->subject($mail['asunto']);
	                for($j=0; $j < count($adjunto); $j++)
	                {
	                    $archivos = get_object_vars($adjunto[$j]);
	                    $msj->attach($archivos['archivoRadicadoVersion']);
	                }
            	}); 

            	#Por último actualizo el campo de correo de compra para 
            	#saber que ya esta compra se ha enviado correo
            	DB::Select('UPDATE compra SET envioCorreoCompra = 1 
            		WHERE numeroCompra = "'.$compra['numeroCompra'].'"');

				echo 'Se envió el mensaje';
            }
		}
			


?>