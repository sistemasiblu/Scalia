<?php 

		#Se hace un update a la tabla compra donde primero se obtiene el id de movimiento que está en saya
		#para luego actualizar ese id en la tabla compra en el campo Movimiento_idMovimiento
		DB::update(
			'UPDATE compra 
			LEFT JOIN Iblu.Movimiento
			ON numeroMovimiento = numeroCompra AND Documento_idDocumento = 28
			SET compra.Movimiento_idMovimiento = Movimiento.idMovimiento
			WHERE (compra.Movimiento_idMovimiento = 1 or compra.Movimiento_idMovimiento IS NULL) AND 
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

		return;

		#Consulto todas las compras de las que aún no se ha enviado correo
		$compras = DB::Select('
			SELECT 
			    numeroCompra, envioCorreoCompra
			FROM
			    (SELECT 
			        numeroCompra, envioCorreoCompra
			    FROM
			        (SELECT 
			        	numeroCompra, envioCorreoCompra
			    FROM
			        compra c
			    GROUP BY numeroCompra , numeroVersionCompra
			    ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
			    GROUP BY numeroCompra) AS comp
			WHERE envioCorreoCompra = 0');

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
            	$mail['destinatario'] = '';
            	$mail['asunto'] = '';
            	$mail['mensaje'] = '';

            	Mail::send('emails.contact',$mail,function($msj) use ($mail, $adjunto)
            	{
	                $msj->to($mail['destinatario']);
	                $msj->subject($mail['asunto']);
	                for($i=0; $i < count($adjunto); $i++)
	                {
	                    $archivos = get_object_vars($adjunto[$i]);
	                    $msj->attach($archivos['archivoRadicadoVersion']);
	                }
            	}); 

            	#Por último actualizo el campo de correo de compra para 
            	#saber que ya esta compra se ha enviado correo
            	DB::Select('UPDATE compra SET envioCorreoCompra = 1 
            		WHERE numeroCompra = '.$compra['numeroCompra']);
            }
		}
			


?>