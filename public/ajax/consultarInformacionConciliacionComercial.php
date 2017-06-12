<?php 

	//RECEPCION DE PARAMETROS ENVIADOS
	$idConCom = ($_POST['idConCom'] != 0) ? $_POST['idConCom'] : 0;
	$idDoc = ($_POST['idDoc'] != 0) ? $_POST['idDoc'] : 0;
	$tipo = ($_POST['tipo'] != 0) ? $_POST['tipo'] : 0;

	$informacion = "";

	if($idConCom == 0 OR $tipo == 0)
	{
		$informacion = 'Error. No se encontro informacion, por favor verifique.';
	    $respuesta = array("valid"=>false,"informacion"=>$informacion);
	    
	    echo json_encode($respuesta);
	
    	return;
	}

	if($tipo == 1)
	{
		//DETALLE POR MOVIMIENTO
		$tabla = '<table id="tconciliacioncomercialmovimiento" name="tconciliacioncomercialmovimiento" class="display table-bordered" width="100%">
			          <thead>
			              <tr class="btn-primary active">
			                  <th><b>Numero Documento</b></th>
			                  <th><b>Fecha Elaboracion</b></th>
			                  <th><b>Tercero</b></th>
			                  <th><b>Total Comercial</b></th>
			                  <th><b>Total Contabilidad</b></th>
			                  <th><b>Diferencia</b></th>
			                  <th><b>Observaciones</b></th>
			              </tr>
			          </thead>';

	    $tablaFinal = '<tfoot>
			              <tr class="btn-default active">
			                  <th><b>Numero Documento</b></th>
			                  <th><b>Fecha Elaboracion</b></th>
			                  <th><b>Tercero</b></th>
			                  <th><b>Total Comercial</b></th>
			                  <th><b>Total Contabilidad</b></th>
			                  <th><b>Diferencia</b></th>
			                  <th><b>Observaciones</b></th>
			              </tr>
			          </tfoot>        
			      </table>';

		//SE CONSULTA EL DETALLE DE LA CONCILIACION GUARDADA AGRUPADA POR DOCUMENTO
		$concomdoc = DB::select("SELECT idMovimiento, numeroMovimiento AS 'descripcion', idDocumento, fechaElaboracionMovimiento, nombre1Tercero,
								IFNULL(observacionConciliacionComercialMovimiento,'') AS observacionConciliacionComercialMovimiento,
								SUM(valorComercialConciliacionComercialDetalle) AS valorComercialConciliacionComercialDetalle,
								SUM(valorContableConciliacionComercialDetalle) AS valorContableConciliacionComercialDetalle,
								SUM(valorComercialConciliacionComercialDetalle-valorContableConciliacionComercialDetalle) AS diferencia
								FROM conciliacioncomercialdetalle 
								LEFT JOIN conciliacioncomercialmovimiento 
								ON conciliacioncomercialdetalle.ConciliacionComercial_idConciliacionComercial = conciliacioncomercialmovimiento.ConciliacionComercial_idConciliacionComercial
									AND conciliacioncomercialdetalle.Movimiento_idMovimiento = conciliacioncomercialmovimiento.Movimiento_idMovimiento 
								LEFT JOIN ".\Session::get("baseDatosCompania").".Movimiento 
								ON conciliacioncomercialdetalle.Movimiento_idMovimiento = Movimiento.idMovimiento 
								LEFT JOIN ".\Session::get("baseDatosCompania").".Documento 
								ON Movimiento.Documento_idDocumento = Documento.idDocumento 
								LEFT JOIN ".\Session::get("baseDatosCompania").".Tercero 
								ON Movimiento.Tercero_idTercero = Tercero.idTercero 
								WHERE conciliacioncomercialdetalle.ConciliacionComercial_idConciliacionComercial = $idConCom 
										AND idDocumento = $idDoc
										AND (valorComercialConciliacionComercialDetalle-valorContableConciliacionComercialDetalle) != 0
								GROUP BY idMovimiento");
	}
	else if($tipo == 2)
	{
		//DETALLE POR CONCEPTO
		$tabla = '<table id="tconciliacioncomercialdetalle" name="tconciliacioncomercialdetalle" class="display table-bordered" width="100%">
			          <thead>
			              <tr class="btn-primary active">
			                  <th><b>Concepto</b></th>
			                  <th><b>Total Comercial</b></th>
			                  <th><b>Total Contabilidad</b></th>
			                  <th><b>Diferencia</b></th>
			              </tr>
			          </thead>';

	    $tablaFinal = '<tfoot>
			              <tr class="btn-default active">
			                  <th><b>Concepto</b></th>
			                  <th><b>Total Comercial</b></th>
			                  <th><b>Total Contabilidad</b></th>
			                  <th><b>Diferencia</b></th>
			              </tr>
			          </tfoot>        
			      </table>';

		//SE CONSULTA EL DETALLE DE LA CONCILIACION GUARDADA AGRUPADA POR DOCUMENTO
		$concomdoc = DB::select("SELECT idDocumento, idValorConciliacion, idMovimiento, numeroMovimiento, nombreValorConciliacion AS 'descripcion',
								(valorComercialConciliacionComercialDetalle) AS valorComercialConciliacionComercialDetalle,
								(valorContableConciliacionComercialDetalle) AS valorContableConciliacionComercialDetalle,
								(valorComercialConciliacionComercialDetalle-valorContableConciliacionComercialDetalle) AS diferencia
								FROM conciliacioncomercialdetalle 
								LEFT JOIN valorconciliacion 
								ON ValorConciliacion_idValorConciliacion = idValorConciliacion 
								LEFT JOIN ".\Session::get("baseDatosCompania").".Movimiento 
								ON conciliacioncomercialdetalle.Movimiento_idMovimiento = Movimiento.idMovimiento 
								LEFT JOIN ".\Session::get("baseDatosCompania").".Documento 
								ON Movimiento.Documento_idDocumento = Documento.idDocumento 
								WHERE conciliacioncomercialdetalle.ConciliacionComercial_idConciliacionComercial = $idConCom 
										AND idMovimiento = $idDoc");
	}
	else if($tipo == 3)
	{
		//DETALLE POR DOCUMENTO
		$tabla = '<table id="tconciliacioncomercialdocumento" name="tconciliacioncomercialdocumento" class="display table-bordered" width="100%">
			          <thead>
			              <tr class="btn-primary active">
			                  <th><b>Documento</b></th>
			                  <th><b>Total Comercial</b></th>
			                  <th><b>Total Contabilidad</b></th>
			                  <th><b>Diferencia</b></th>
			                  <th><b>Observaciones</b></th>
			              </tr>
			          </thead>';

	    $tablaFinal = '<tfoot>
			              <tr class="btn-default active">
			                  <th><b>Documento</b></th>
			                  <th><b>Total Comercial</b></th>
			                  <th><b>Total Contabilidad</b></th>
			                  <th><b>Diferencia</b></th>
			                  <th><b>Observaciones</b></th>
			              </tr>
			          </tfoot>        
			      </table>';

		//SE CONSULTA EL DETALLE DE LA CONCILIACION GUARDADA AGRUPADA POR DOCUMENTO
		$concomdoc = DB::select("SELECT idDocumento, idMovimiento, nombreDocumento AS 'descripcion', 
								IFNULL(observacionConciliacionComercialDocumento,'') AS observacionConciliacionComercialDocumento,
								SUM(valorComercialConciliacionComercialDetalle) AS valorComercialConciliacionComercialDetalle,
								SUM(valorContableConciliacionComercialDetalle) AS valorContableConciliacionComercialDetalle,
								SUM(valorComercialConciliacionComercialDetalle-valorContableConciliacionComercialDetalle) AS diferencia
								FROM conciliacioncomercialdetalle 
								LEFT JOIN ".\Session::get("baseDatosCompania").".Movimiento 
								ON conciliacioncomercialdetalle.Movimiento_idMovimiento = Movimiento.idMovimiento 
								LEFT JOIN ".\Session::get("baseDatosCompania").".Documento 
								ON Movimiento.Documento_idDocumento = Documento.idDocumento 
								LEFT JOIN conciliacioncomercialdocumento 
									ON conciliacioncomercialdetalle.ConciliacionComercial_idConciliacionComercial AND conciliacioncomercialdocumento.ConciliacionComercial_idConciliacionComercial
										AND Documento.idDocumento = conciliacioncomercialdocumento.Documento_idDocumento 
								WHERE conciliacioncomercialdetalle.ConciliacionComercial_idConciliacionComercial = $idConCom 
								GROUP BY idDocumento");

	}

    $datosConComDoc = array();

    foreach ($concomdoc as $key => $value) 
    {  
        foreach ($value as $datoscampo => $campo) 
        {
            $datosConComDoc[$datoscampo][] = $campo;
        }                        
    }

    $totalDoc = count($datosConComDoc['idMovimiento']);
    $cont = 0;

    while($cont < $totalDoc) 
    {
    	$idMov = $datosConComDoc['idMovimiento'][$cont];
    	$idDoc = $datosConComDoc['idDocumento'][$cont];

    	$style = " style='cursor: pointer;'";

    	if($datosConComDoc['diferencia'][$cont] != 0)
    	{
    		$style = " style='color: red; cursor: pointer;'";
    	}

		if($tipo == 1)
		{
			$tabla .= "<tr>
    					<td>
    						<a href='javascript:consultarInformacion($idMov,2);'>
    							<label $style>".$datosConComDoc['descripcion'][$cont]."</label>
							</a>
    					</td>
    					<td>
    						".$datosConComDoc['fechaElaboracionMovimiento'][$cont]."
    					</td>
    					<td>
    						".$datosConComDoc['nombre1Tercero'][$cont]."
    					</td>";
		}
		else if($tipo == 2)
		{
			$tabla .= "<tr>
    					<td>
    						<label $style>".$datosConComDoc['descripcion'][$cont]."</label>
    					</td>";
		}
		else if($tipo == 3)
		{
			$tabla .= "<tr>
    					<td>
    						<a href='javascript:consultarInformacion($idDoc,1);'>
    							<label $style>".$datosConComDoc['descripcion'][$cont]."</label>
							</a>
    					</td>";
		}

    	$tabla .= "<td>
						".number_format($datosConComDoc['valorComercialConciliacionComercialDetalle'][$cont], 2, '.', ',')."
					</td>
					<td>
						".number_format($datosConComDoc['valorContableConciliacionComercialDetalle'][$cont], 2, '.', ',')."
					</td>
					<td>
						<label $style>".number_format($datosConComDoc['diferencia'][$cont], 2, '.', ',')."</label>
					</td>";

		if($tipo == 1)
		{
			$tabla .= "<td>
							<input type='text' id='observacionConciliacionComercialMovimiento$cont' name='observacionConciliacionComercialMovimiento$cont' value='".$datosConComDoc['observacionConciliacionComercialMovimiento'][$cont]."' style='width:100%' onchange='guardarObservacion($idMov,$idConCom,this.value,2);'>
						</td>";
		}
		else if($tipo == 3)
		{
			$tabla .= "<td>
							<input type='text' id='observacionConciliacionComercialDocumento$cont' name='observacionConciliacionComercialDocumento$cont' value='".$datosConComDoc['observacionConciliacionComercialDocumento'][$cont]."' style='width:100%' onchange='guardarObservacion($idDoc,$idConCom,this.value,1);'>
						</td>";
		}

		$tabla .= "</tr>";

		$cont++;
    }

    $tabla = $tabla.$tablaFinal;

	// echo $idConciliacionComercial;
	// return;
    $respuesta = array("valid"=>true,"informacion"=>$informacion,"tabla"=>$tabla);
    
    echo json_encode($respuesta);

?>