$idInformeCapa = "4";$camposConsulta = "nombreDocumento,documentoTercero,codigoAlternoPosicionArancelaria,codigoAlternoBodegaOrigen,numeroMovimiento,fechaElaboracionMovimiento,fechaVencimientoMovimiento,valorTotalMovimiento,totalUnidadesMovimiento,referenciaProducto";$condicion = " fechaElaboracionMovimiento = '2016-06-29'  AND codigoAlternoDocumento = 'FC'  AND codigoAlternoPosicionArancelaria >= '0'";$camposOrden = "nombreDocumento,documentoTercero,codigoAlternoPosicionArancelaria,codigoAlternoBodegaOrigen";
		Config::set( 'database.connections.'.$conexionBD['bdSistemaInformacion'], array 
	    ( 
	        'driver'     =>  $conexionBD['motorbdSistemaInformacion'], 
	        'host'       =>  $conexionBD['ipSistemaInformacion'], 
	        'port'       =>  $conexionBD['puertoSistemaInformacion'], 
	        'database'   =>  $conexionBD['bdSistemaInformacion'], 
	        'username'   =>  $conexionBD['usuarioSistemaInformacion'], 
	        'password'   =>  $conexionBD['claveSistemaInformacion'], 
	        'charset'    =>  'utf8', 
	        'collation'  =>  'utf8_unicode_ci', 
	        'prefix'     =>  '',
	        'strict'    => false,
	        'options'   => [ 
	        				\PDO::ATTR_EMULATE_PREPARES => true
	        				]
	    )); 
    	$conexion = DB::connection($conexionBD['bdSistemaInformacion'])->getDatabaseName();
    	$consulta = DB::connection($conexionBD['bdSistemaInformacion'])->select(
    			"SELECT $camposConsulta 
				FROM $tabla ".
				($condicion != '' ? "WHERE ".$condicion : "").
				($camposOrden != '' ? "ORDER BY  ".$camposOrden : ""));			
		// por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
		$valores = array();
		for($i = 0; $i < count($consulta); $i++) 
		{
		    $valores[] = get_object_vars($consulta[$i]);
		}$estructura = '';<br>$pos = 0;<br>while($pos < count($valores))<br>{<br>$nombreDocumentoAnt = $valores[$pos]["nombreDocumento"];<br>echo $nombreDocumentoAnt;<br>$valores[$pos]["nombreDocumento"];<br>while($pos < count($valores) and $nombreDocumentoAnt == $valores[$pos]["nombreDocumento"])<br>{<br>$documentoTerceroAnt = $valores[$pos]["documentoTercero"];<br>echo $documentoTerceroAnt;<br>$valores[$pos]["documentoTercero"];<br>while($pos < count($valores) and $nombreDocumentoAnt == $valores[$pos]["nombreDocumento"] and $documentoTerceroAnt == $valores[$pos]["documentoTercero"])<br>{<br>$codigoAlternoPosicionArancelariaAnt = $valores[$pos]["codigoAlternoPosicionArancelaria"];<br>echo $codigoAlternoPosicionArancelariaAnt;<br>$valores[$pos]["codigoAlternoPosicionArancelaria"];<br>while($pos < count($valores) and $nombreDocumentoAnt == $valores[$pos]["nombreDocumento"] and $documentoTerceroAnt == $valores[$pos]["documentoTercero"] and $codigoAlternoPosicionArancelariaAnt == $valores[$pos]["codigoAlternoPosicionArancelaria"])<br>{<br>$codigoAlternoBodegaOrigenAnt = $valores[$pos]["codigoAlternoBodegaOrigen"];<br>echo $codigoAlternoBodegaOrigenAnt;<br>$valores[$pos]["codigoAlternoBodegaOrigen"];<br>while($pos < count($valores) and $nombreDocumentoAnt == $valores[$pos]["nombreDocumento"] and $documentoTerceroAnt == $valores[$pos]["documentoTercero"] and $codigoAlternoPosicionArancelariaAnt == $valores[$pos]["codigoAlternoPosicionArancelaria"] and $codigoAlternoBodegaOrigenAnt == $valores[$pos]["codigoAlternoBodegaOrigen"])<br>{<br>// si el nombre de la banda contiene la palabra Detalle, ejecutamos un proceso especial<br>// de lo contrario (las demas bandas) ejecutamos el proceso simple<br>echo 'layoutGrupoEncContenedor1_3' == 'layoutDetalleContenedor1';<br>if('layoutGrupoEncContenedor1_3' == 'layoutDetalleContenedor1')<br>$estructura .= imprimirBandaDetalle('Detalle', '4', $valores);<br>else<br>echo $valores[$pos]["nombreDocumento"].'<br>';<br>$pos++;<br>}  }  }  }  } echo $estructura;<br>