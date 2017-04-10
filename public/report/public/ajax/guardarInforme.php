<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idInforme = (isset($_POST['idInforme']) ? $_POST['idInforme'] : 0);
$informe = (isset($_POST['informe']) ? $_POST['informe'] : array());
$propiedad = (isset($_POST['propiedad']) ? $_POST['propiedad'] : array());

// tomamos la variable conceptos que trae toda la información concatenada con separadores
// Separador de Capas = °
// Separador de registros = ¬
// separador de campos = |
$datoObjeto = (isset($_POST['objetos']) ? $_POST['objetos'] : '');

switch ($accion) {
	case 'adicionar':
		
		$datos = DB::select(
				"INSERT INTO informe (nombreInforme, descripcionInforme, vistaPreviaInforme)
				VALUES ('".$informe[1]."', 
						'".$informe[2]."', 
						'');");

		$idInforme = DB::select(
				"SELECT LAST_INSERT_ID() as ID");
		$idInforme = get_object_vars($idInforme[0])["ID"];

		$datos = DB::select(
				"INSERT INTO informepropiedad (Informe_idInforme, colorFondoParInformePropiedad, colorFondoImparInformePropiedad, colorBordeParInformePropiedad, colorBordeImparInformePropiedad, colorTextoParInformePropiedad, colorTextoImparInformePropiedad, fuenteTextoParInformePropiedad, fuenteTextoImparInformePropiedad, tamañoTextoParInformePropiedad, tamañoTextoImparInformePropiedad, negrillaParInformePropiedad, negrillaImparInformePropiedad, italicaParInformePropiedad, italicaImparInformePropiedad, subrayadoParInformePropiedad, subrayadoImparInformePropiedad)
				VALUES (".$idInforme.",
						'".$propiedad[1]."', 
						'".$propiedad[2]."', 
						'".$propiedad[3]."', 
						'".$propiedad[4]."', 
						'".$propiedad[5]."', 
						'".$propiedad[6]."', 
						'".$propiedad[7]."', 
						'".$propiedad[8]."', 
						'".$propiedad[9]."', 
						'".$propiedad[10]."', 
						'".$propiedad[11]."', 
						'".$propiedad[12]."', 
						'".$propiedad[13]."', 
						'".$propiedad[14]."', 
						'".$propiedad[15]."', 
						'".$propiedad[16]."');");

		guardarTablasDetalle($idInforme, $datoObjeto);	
		

{
		// $dato = explode('°',substr($datoConcepto,0,strlen($datoConcepto)-1));
		// foreach ($dato as $pc => $capa) 
		// {
		// 	//echo 'Capa '.$pc.'<br>';

		// 	$datos = DB::select(
		// 		"INSERT INTO informecapa (Informe_idInforme, nombreInformeCapa)
		// 		VALUES (".$idInforme.", 
		// 				'Capa ".($pc + 1)."');");

		// 	$idCapa = DB::select(
		// 		"SELECT LAST_INSERT_ID() as ID");
		// 	$idCapa = get_object_vars($idCapa[0])["ID"];

		// 	$arrRegistros = explode('¬',substr($capa,0,strlen($capa)-1));
		// 	foreach ($arrRegistros as $pr => $registro) 
		// 	{
				

		// 		$arrCampos = explode('|',$registro);
		// 		if($arrCampos[0] != '')
		// 		{
		// 			// print_r($arrCampos );
		// 			$campos = $idCapa.', '. $pr .',';
		// 			foreach ($arrCampos as $pa => $campo) 
		// 			{
		// 				$campos .= "'".$campo."',";
		// 			}
		// 			$campos = substr($campos,0,strlen($campos)-1);
					
		// 			$datos = DB::select(
		// 			"INSERT INTO informeconcepto (InformeCapa_idInformeCapa, ordenInformeConcepto, nombreInformeConcepto, tipoMovimientoInformeConcepto, tipoValorInformeConcepto, valorInformeConcepto, EstiloInforme_idEstiloInforme, detalleInformeConcepto, resumenInformeConcepto, graficoInformeConcepto)
		// 			VALUES (".$campos."
		// 					);");
		// 		}
		// 	}
			
		// }
		}
		echo json_encode($datos);
		break;

	case 'modificar':
		$datos = DB::select(
				"UPDATE  informe 
				SET nombreInforme = '".$informe[1]."',
					descripcionInforme = '".$informe[2]."'
				WHERE idInforme = ".$idInforme.";");

		// los datos de Conceptos los adicionamos o modificamos segun existan
		$datos = DB::select(
				"UPDATE informepropiedad 
				SET Informe_idInforme = ".$idInforme.", 
					colorFondoParInformePropiedad = '".$propiedad[1]."',
					colorFondoImparInformePropiedad = '".$propiedad[2]."',  
					colorBordeParInformePropiedad = '".$propiedad[3]."', 
					colorBordeImparInformePropiedad = '".$propiedad[4]."', 
					colorTextoParInformePropiedad = '".$propiedad[5]."', 
					colorTextoImparInformePropiedad = '".$propiedad[6]."', 
					fuenteTextoParInformePropiedad = '".$propiedad[7]."', 
					fuenteTextoImparInformePropiedad = '".$propiedad[8]."', 
					tamañoTextoParInformePropiedad =  '".$propiedad[9]."', 
					tamañoTextoImparInformePropiedad =  '".$propiedad[10]."', 
					negrillaParInformePropiedad =  '".$propiedad[11]."', 
					negrillaImparInformePropiedad =  '".$propiedad[12]."',
					italicaParInformePropiedad =  '".$propiedad[13]."', 
					italicaImparInformePropiedad =  '".$propiedad[14]."', 
					subrayadoParInformePropiedad =  '".$propiedad[15]."', 
					subrayadoImparInformePropiedad = '".$propiedad[16]."'
				WHERE Informe_idInforme = ".$idInforme.";");
						
		
		guardarTablasDetalle($idInforme, $datoObjeto);	

		echo json_encode($datos);
		break;

	case 'eliminar':
		$datos = DB::select(
				"DELETE FROM  informe 
				WHERE idInforme = ".$idInforme.";");

		echo json_encode($datos);
		break;

	default:
		# code...
		break;
}

function guardarTablasDetalle($idInforme, $datoObjeto)
{
	$arrRegistros = explode('¬',substr($datoObjeto,0,strlen($datoObjeto)-1));
			
	foreach ($arrRegistros as $pr => $registro) 
	{
		$arrCampos = explode('|',$registro);

		// si el registro inicia por el tipo capa, adicionamos la capa a la tabla y tomamos su id
		if($arrCampos[0] == 'capa')
		{
			$idCapa = $arrCampos[1];
			 $datos = DB::select(
					"INSERT INTO informecapa (idInformeCapa, Informe_idInforme, nombreInformeCapa, SistemaInformacion_idSistemaInformacion, tablaInformeCapa, tipoInformeCapa)
					VALUES (".$arrCampos[1].", ".$idInforme.", '". $arrCampos[2]."', ". ($arrCampos[3] == '' ? 'null' : "'".$arrCampos[3]."'").", ". ($arrCampos[4] == '' ? 'null' : "'".$arrCampos[4]."'").", '". $arrCampos[5]."')
					ON DUPLICATE KEY UPDATE
					 	Informe_idInforme = ".$idInforme.", 
						nombreInformeCapa = '".$arrCampos[2]."',
						SistemaInformacion_idSistemaInformacion = ".($arrCampos[3] == '' ? 'null' : "'".$arrCampos[3]."'").",
						tablaInformeCapa = ".($arrCampos[4] == '' ? 'null' : "'".$arrCampos[4]."'").",
						tipoInformeCapa = '".$arrCampos[5]."';");
			
			if($arrCampos[1] == 0)
			{
				$idCapa = DB::select(
							"SELECT LAST_INSERT_ID() as ID");
				$idCapa = get_object_vars($idCapa[0])["ID"];
			}
			else
			{
				$idCapa = $arrCampos[1];
			}
		}

		// si el registro inicia por el tipo objeto, adicionamos el registro en la tabla informeobjeto
		if($arrCampos[0] == 'concepto')
		{
			$datos = DB::select(
				"INSERT INTO informeconcepto (idInformeConcepto, InformeCapa_idInformeCapa, ordenInformeConcepto, nombreInformeConcepto, tipoMovimientoInformeConcepto, tipoValorInformeConcepto, valorInformeConcepto, EstiloInforme_idEstiloInforme, detalleInformeConcepto, resumenInformeConcepto, graficoInformeConcepto,excluirTerceroInformeConcepto)
				VALUES (".$arrCampos[11].", ".$idCapa.', '. $pr .','.
						 "'".$arrCampos[2]."',".
						 "'".$arrCampos[3]."',".
						 "'".$arrCampos[4]."',".
						 "'".$arrCampos[5]."',".
						 (($arrCampos[6] == '' or $arrCampos[6] == 0) ? 'null' : "'".$arrCampos[6]."'").",".
						 "'".$arrCampos[7]."',".
						 "'".$arrCampos[8]."',".
						 "'".$arrCampos[9]."',".
						 "'".$arrCampos[10]."'
						)
				ON DUPLICATE KEY UPDATE 
						InformeCapa_idInformeCapa = '".$idCapa."', 
                        ordenInformeConcepto = '".$pr."', 
                        nombreInformeConcepto = '".$arrCampos[2]."', 
                        tipoMovimientoInformeConcepto = '".$arrCampos[3]."', 
                        tipoValorInformeConcepto = '".$arrCampos[4]."', 
                        valorInformeConcepto = '".$arrCampos[5]."', 
                        EstiloInforme_idEstiloInforme = ".(($arrCampos[6] == '' or $arrCampos[6] == 0) ? 'null' : "'".$arrCampos[6]."'").", 
                        detalleInformeConcepto = '".$arrCampos[7]."', 
                        resumenInformeConcepto = '".$arrCampos[8]."', 
                        graficoInformeConcepto = '".$arrCampos[9]."',
                        excluirTerceroInformeConcepto = '".$arrCampos[10]."';");
		}

		// si el registro inicia por el tipo objeto, adicionamos el registro en la tabla informeconcepto
		if($arrCampos[0] == 'objeto')
		{
			 $datos = DB::select(
				"INSERT INTO informeobjeto (idInformeObjeto, InformeCapa_idInformeCapa, bandaInformeObjeto, nombreInformeObjeto, estiloInformeObjeto, EstiloInforme_idEstiloInforme, tipoInformeObjeto, campoInformeObjeto)
				VALUES ('".$arrCampos[1]."',".
						$idCapa.', '.
						"'".$arrCampos[2]."',".
						"'".$arrCampos[3]."',".
						"'".$arrCampos[4]."',".
						($arrCampos[5] == '' ? 'null' : $arrCampos[5]).",".
						"'".$arrCampos[6]."',".
						"'".$arrCampos[7]."'
						)
				ON DUPLICATE KEY UPDATE 
					InformeCapa_idInformeCapa = '".$idCapa."', 
					bandaInformeObjeto = '".$arrCampos[2]."', 
					nombreInformeObjeto = '".$arrCampos[3]."', 
					estiloInformeObjeto = '".$arrCampos[4]."', 
					EstiloInforme_idEstiloInforme = ".($arrCampos[5] == '' ? 'null' : $arrCampos[5]).", 
					tipoInformeObjeto = '".$arrCampos[6]."', 
					campoInformeObjeto = '".$arrCampos[7]."';");

			
		}
	}
}
?>