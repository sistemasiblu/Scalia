<?php 

	//RECEPCION DE PARAMETROS ENVIADOS
	$idDoc = ($_POST['idDoc'] != '') ? $_POST['idDoc'] : '';	
	$idConCom = ($_POST['idConCom'] != '') ? $_POST['idConCom'] : '';	
	$observacion = ($_POST['observacion'] != '') ? $_POST['observacion'] : '';	
	$tipo = ($_POST['tipo'] != '') ? $_POST['tipo'] : '';	

	//SE VALIDA SI EXISTE ID DE LA CONCILIACION PARA ELIMINARLA Y CREAR UNA NUEVA 
	if($idDoc != '' AND $idConCom != '' AND $tipo != '' )
	{
		if($tipo == 1)
		{
			$query = "DELETE 
					FROM conciliacioncomercialdocumento 
					WHERE ConciliacionComercial_idConciliacionComercial = '$idConCom'
							AND Documento_idDocumento = $idDoc ";
			// echo $query;
			$resp = DB::delete($query);
			
			$query = "INSERT INTO conciliacioncomercialdocumento VALUES(0,$idConCom,$idDoc,'$observacion')";
			// echo $query;
			$resp = DB::insert($query);
		}
		else
		{
			$query = "DELETE 
					FROM conciliacioncomercialmovimiento 
					WHERE ConciliacionComercial_idConciliacionComercial = '$idConCom'
							AND Movimiento_idMovimiento = $idDoc ";
			// echo $query;
			$resp = DB::delete($query);
			
			$query = "INSERT INTO conciliacioncomercialmovimiento VALUES(0,$idConCom,$idDoc,'$observacion')";
			// echo $query;
			$resp = DB::insert($query);
		}

		if($resp == 1)
		{
			$respuesta = array("valid"=>true);
		    
		    echo json_encode($respuesta);

		}
		else
		{
			$respuesta = array("valid"=>false);
		    
		    echo json_encode($respuesta);
		}
	
	}
	else
	{

			$respuesta = array("valid"=>false);
		    
		    echo json_encode($respuesta);
	}

?>