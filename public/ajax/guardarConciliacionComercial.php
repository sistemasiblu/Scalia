<?php 

	//RECEPCION DE PARAMETROS ENVIADOS
	$idConciliacionComercial = ($_POST['idConciliacionComercial'] != '' AND $_POST['idConciliacionComercial'] > 0) ? $_POST['idConciliacionComercial'] : 0;	

	$documentos = ($_POST['documentos'] != 0) ? implode(",", $_POST['documentos']) : '';
	$idUsuario = $_POST['idUsuario'];	

	$fechaElaboracionConciliacionComercial = $_POST['fechaElaboracionConciliacionComercial'];
	$fechaInicialConciliacionComercial = $_POST['fechaInicialConciliacionComercial'];
	$fechaFinalConciliacionComercial = $_POST['fechaFinalConciliacionComercial'];

	$condicionFechas = $_POST['condicionFechas'];
	$condicionDocumento = $_POST['condicionDocumento'];

    $condicionFechasContables = str_replace("fechaElaboracionMovimiento ", "fechaElaboracionMovimientoContable ", $condicionFechas);

	$condicionDocumento = ($condicionDocumento != "") ? " AND $condicionDocumento " : "";
	$condicionDocumentoCC = ($documentos != "") ? " AND dc.Documento_idDocumento = '$documentos' " : "";

	$informacion = '';
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


	//SE VALIDA SI EXISTE ID DE LA CONCILIACION PARA ELIMINARLA Y CREAR UNA NUEVA 
	if($idConciliacionComercial > 0)
	{
		$query = "DELETE FROM conciliacioncomercial WHERE idConciliacionComercial = '$idConciliacionComercial' ";
		// echo $query;
		$resp = DB::delete($query);

		// $idConciliacionComercial = 0;
	}

	//SE CONSULTA SI YA EXISTE UNA CONCILIACION CON LOS MISMOS PARAMETROS
	$concom = DB::select("SELECT idConciliacionComercial 
							FROM conciliacioncomercial cc
							LEFT JOIN users u 
							ON cc.Users_idCrea = u.id
							LEFT JOIN documentoconciliacion dc
							ON cc.Documento_idDocumento = dc.Documento_idDocumento AND dc.Compania_idCompania = u.Compania_idCompania 
							WHERE fechaElaboracionConciliacionComercial != '$fechaElaboracionConciliacionComercial'
								AND fechaInicialConciliacionComercial = '$fechaInicialConciliacionComercial'
								AND fechaFinalConciliacionComercial = '$fechaFinalConciliacionComercial'
								$condicionDocumentoCC AND dc.Compania_idCompania = ".\Session::get("idCompania")." ");

    $datosConCom = array();

    if(!empty($concom))
    {
	    foreach ($concom as $key => $value) 
	    {  
	        foreach ($value as $datoscampo => $campo) 
	        {
	            $datosConCom[$datoscampo][] = $campo;
	        }                        
	    }
	}
	
    if(!empty($datosConCom))
    {
    	$informacion = 'Error. Ya existe una conciliacion guardada con los mismos parametros, por favor verifique.';
	    $respuesta = array("valid"=>false,"informacion"=>$informacion,"idConciliacionComercial"=>0);
	    
	    echo json_encode($respuesta);
	
    	return;
    }

    //SE CONSULTAN LOS DOCUMENTOS Y CONCEPTOS A CONCILIAR
	$consultaValores = DB::Select("SELECT Documento_idDocumento, idValorConciliacion, campoValorConciliacion, cuentasLocalDocumentoConciliacionComercial
	                                FROM documentoconciliacion 
	                                LEFT JOIN documentoconciliacioncomercial 
	                                ON documentoconciliacion.idDocumentoConciliacion = documentoconciliacioncomercial.DocumentoConciliacion_idDocumentoConciliacion 
	                                LEFT JOIN valorconciliacion 
	                                ON documentoconciliacioncomercial.ValorConciliacion_idValorConciliacion = valorconciliacion.idValorConciliacion 
	                                WHERE Compania_idCompania = ".\Session::get("idCompania")." $condicionDocumento 
                                    GROUP BY idDocumentoConciliacion,idValorConciliacion
                                    ORDER BY Documento_idDocumento, idValorConciliacion");

    $datosValores = array();

    foreach ($consultaValores as $key => $value) 
    {  
        foreach ($value as $datoscampo => $campo) 
        {
            $datosValores[$datoscampo][] = $campo;
        }                        
    }

    if(empty($datosValores))
    {
    	$informacion = 'Error. No se encontro informacion para los filtros indicados, por favor verifique.';
	    $respuesta = array("valid"=>false,"informacion"=>$informacion,"idConciliacionComercial"=>0);
	    
	    echo json_encode($respuesta);
	
    	return;
    }

	//INICIALIZACION DE VARIABLES
    $sqlCampos = '';
    $whereCuentas = "";
    $reg = 0;
    $totalReg = count($datosValores['campoValorConciliacion']);

    //SE VALIDA SI EXISTE ID DE LA CONCILIACION, SINO PARA CREAR UNA NUEVA 	
    // if($idConciliacionComercial == 0)
    // {
		$query = "INSERT INTO conciliacioncomercial VALUES($idConciliacionComercial,
													'$fechaElaboracionConciliacionComercial',
													$idUsuario,
													'$fechaInicialConciliacionComercial',
													'$fechaFinalConciliacionComercial',
													'$documentos') ";
		// echo $query;
		$resp = DB::insert($query);

		$condicionDocumentoCC = str_replace(" dc.Documento_idDocumento ", " Documento_idDocumento ", $condicionDocumentoCC);

		$concom = DB::select("SELECT idConciliacionComercial 
								FROM conciliacioncomercial 
								WHERE fechaElaboracionConciliacionComercial = '$fechaElaboracionConciliacionComercial'
									AND fechaInicialConciliacionComercial = '$fechaInicialConciliacionComercial'
									AND fechaFinalConciliacionComercial = '$fechaFinalConciliacionComercial'
									AND Users_idCrea = $idUsuario
									$condicionDocumentoCC");

	    $datosConCom = array();

	    foreach ($concom as $key => $value) 
	    {  
	        foreach ($value as $datoscampo => $campo) 
	        {
	            $datosConCom[$datoscampo][] = $campo;
	        }                        
	    }

    	$idConciliacionComercial = $datosConCom['idConciliacionComercial'][0];
    // }

	$sqlInicial = "INSERT INTO conciliacioncomercialdetalle 
					SELECT * FROM ("; 

    while($reg < $totalReg)
    {
    	$idDocAnt = $datosValores['Documento_idDocumento'][$reg];

		$sql = "";

    	while($reg < $totalReg AND $idDocAnt == $datosValores['Documento_idDocumento'][$reg])
    	{
    		if($sql != "")
    		{
    			$sql .= "
    					UNION
    					";
    		}

    		$sql .= "SELECT '0' AS idConciliacionComercialDetalle,
    					'$idConciliacionComercial' AS ConciliacionComercial_idConciliacionComercial,
    					Movimiento.idMovimiento AS Movimiento_idMovimiento,   
    					'".$datosValores['idValorConciliacion'][$reg]."' AS ValorConciliacion_idValorConciliacion,
    					(Movimiento.".$datosValores['campoValorConciliacion'][$reg]." * IF(Movimiento.tasaCambioMovimiento = 0, 1, Movimiento.tasaCambioMovimiento)) AS valorComercialConciliacionComercialDetalle,
						SUM(ABS(MovimientoContableDetalle.debitosMovimientoContableDetalle-MovimientoContableDetalle.creditosMovimientoContableDetalle)) as valorContableConciliacionComercialDetalle ";

	        $cuentas = $datosValores['cuentasLocalDocumentoConciliacionComercial'][$reg];
	        $cuentas = explode(",", $cuentas);
	        
	        $estructuraWhere = "";
	        for ($i=0; $i < count($cuentas); $i++) 
	        { 
	            $cuentas2[$reg] = explode("-", $cuentas[$i]);

	            if(count($cuentas2[$reg]) > 1)
	            {
	                $estructuraWhere .= " (numeroCuentaContable BETWEEN '".$cuentas2[$reg][0]."' AND '".$cuentas2[$reg][1]."') OR ";
	            }
	            else
	            {
	            	$inicioCuenta = substr($cuentas2[$reg][0], 0, 1);

	            	if($inicioCuenta == 'X')
	            	{
	            		$cuentas2[$reg][0] = substr($cuentas2[$reg][0], 1);
	            		$estructuraWhere .= " (numeroCuentaContable NOT IN(".$cuentas2[$reg][0].")) OR ";
	            	}
	            	else
	            	{
	            		$estructuraWhere .= " (numeroCuentaContable IN(".$cuentas2[$reg][0].")) OR ";
	            	}
	                
	            }

	        }
	        
	        $whereCuentas = substr($estructuraWhere, 0, -3);

	        $sql .= "FROM ".\Session::get("baseDatosCompania").".Movimiento 
					LEFT JOIN ".\Session::get("baseDatosCompania").".MovimientoContable 
						ON Movimiento.idMovimiento = MovimientoContable.Movimiento_idMovimiento 
					LEFT JOIN (
							SELECT 
								MovimientoContable_idMovimientoContable, 
								SUM(
									MovimientoContableDetalle.debitosMovimientoContableDetalle
								) as debitosMovimientoContableDetalle, 
								SUM(
									MovimientoContableDetalle.creditosMovimientoContableDetalle
								) as creditosMovimientoContableDetalle 
							FROM 
								".\Session::get("baseDatosCompania").".MovimientoContable 
								LEFT JOIN ".\Session::get("baseDatosCompania").".MovimientoContableDetalle 
									ON MovimientoContable.idMovimientoContable = MovimientoContableDetalle.MovimientoContable_idMovimientoContable 
								LEFT JOIN ".\Session::get("baseDatosCompania").".CuentaContable 
									ON MovimientoContableDetalle.CuentaContable_idCuentaContable = CuentaContable.idCuentaContable 
							WHERE 
								(".$whereCuentas.") 
								AND MovimientoContable.Documento_idDocumento = ".$datosValores['Documento_idDocumento'][$reg]." 
								AND $condicionFechasContables 
								AND MovimientoContable.estadoMovimientoContable = 'ACTIVO'
							GROUP BY MovimientoContable_idMovimientoContable
						) MovimientoContableDetalle 
						ON MovimientoContable.idMovimientoContable = MovimientoContableDetalle.MovimientoContable_idMovimientoContable 
					WHERE 
						Movimiento.Documento_idDocumento = ".$datosValores['Documento_idDocumento'][$reg]." 
						AND $condicionFechas 
						AND Movimiento.estadoMovimiento = 'ACTIVO' 
					GROUP BY 
						Movimiento.idMovimiento";

	    	$reg++;
	    }

		$sql .= ") conciliacion";

		$sql = $sqlInicial.$sql;

	 //    echo '<br>----------------- consulta documento -----------<br>'.$sql.'<br>----------------- consulta documento -----------<br>';
		// return;
		$resp = DB::insert($sql);

		if($resp != 1)
		{
			$informacion .= "Error. No se registro el detalle de la conciliacion. ";
		}

    }

	//SE CONSULTA EL DETALLE DE LA CONCILIACION GUARDADA AGRUPADA POR DOCUMENTO
	$concomdoc = DB::select("SELECT idDocumento, nombreDocumento, 
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
								ON conciliacioncomercialdetalle.ConciliacionComercial_idConciliacionComercial = conciliacioncomercialdocumento.ConciliacionComercial_idConciliacionComercial
									AND Documento.idDocumento = conciliacioncomercialdocumento.Documento_idDocumento 
							WHERE conciliacioncomercialdetalle.ConciliacionComercial_idConciliacionComercial = $idConciliacionComercial 
							GROUP BY idDocumento");

    $datosConComDoc = array();

    foreach ($concomdoc as $key => $value) 
    {  
        foreach ($value as $datoscampo => $campo) 
        {
            $datosConComDoc[$datoscampo][] = $campo;
        }                        
    }

    $totalDoc = count($datosConComDoc['nombreDocumento']);
    $cont = 0;

    while ($cont < $totalDoc) 
    {
    	$idDoc = $datosConComDoc['idDocumento'][$cont];
    	$idConCom = $idConciliacionComercial;

    	$style = " style='cursor: pointer;'";

    	if($datosConComDoc['diferencia'][$cont] != 0)
    	{
    		$style = " style='color: red; cursor: pointer;'";
    	}

    	$tabla .= "<tr>
    					<td>
    						<a href='javascript:consultarInformacion($idDoc,1);'>
    							<label $style>".$datosConComDoc['nombreDocumento'][$cont]."</label>
							</a>
    					</td>
    					<td>
    						".number_format($datosConComDoc['valorComercialConciliacionComercialDetalle'][$cont], 2, '.', ',')."
    					</td>
    					<td>
    						".number_format($datosConComDoc['valorContableConciliacionComercialDetalle'][$cont], 2, '.', ',')."
    					</td>
    					<td>
    						<label $style>".number_format($datosConComDoc['diferencia'][$cont], 2, '.', ',')."</label>
    					</td>
    					<td>
    						<input type='text' id='observacionConciliacionComercialDocumento$cont' name='observacionConciliacionComercialDocumento$cont' value='".$datosConComDoc['observacionConciliacionComercialDocumento'][$cont]."' style='width:100%' onchange='guardarObservacion($idDoc,$idConCom,this.value,1);'>
    					</td>
    				</tr>";
		$cont++;
    }

    $tabla = $tabla.$tablaFinal;

	// echo $idConciliacionComercial;
	// return;
    $respuesta = array("valid"=>true,"informacion"=>$informacion,"idConciliacionComercial"=>$idConciliacionComercial,"tabla"=>$tabla);
    
    echo json_encode($respuesta);

	return;
	
?>