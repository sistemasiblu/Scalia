<?php


//********************************
// GENERACION DE INFORMES
//
// Autor: Andres Sierra M
//********************************

/*
En este proceso se genera el informe requerido por el usuario, el cual consta de una 
plantilla de informe prediseñada y los filtros de información especificados

inicialmente tenemos que armar una consulta a la base de datos que traiga agrupada la información segun los conceptos creados
cada uno de estos conceptos puede ser de un tipo diferente, en este caso solo nos interesan los concpeots de tipo PUC
adicionalmente cada concepto puede consultarse desde la tabla de MovimientoContable o desde la tabla Contabilidad (Saldo Contable)
en las cuentas contables que debe consultar pueden haber cuentas individuales, rangos de cuentas y cuentas excluídas
el campo de Nits excluídos debe ser tenido en cuenta para que dicha informacion que tenga esos NIT no sea sumada al concepto
*/

// Parametros recibidos
// Este proceso es ejecutado desde la vista de generador de informes en la cual
// se parametrizan los filtros requeridos para el informe, le cual nos envia los siguientes parametros:
// idInforme. Id de la plantilla de informe a generar (la cual puede incluir varias capas o subinformes)
$idInforme = (isset($_GET['idInforme']) ? $_GET['idInforme'] : 0);
$condicion = (isset($_GET['condicion']) ? $_GET['condicion'] : '');
$idSistema = (isset($_GET['idSistema']) ? $_GET['idSistema'] : '');
$tipoContabilidad = explode(',', (isset($_GET['tipoContabilidad']) ? $_GET['tipoContabilidad'] : 'Local')); 

// sistemaInformacion. son los nombres de las bases de datos que se van a consolidar en el informe (solo aplica si dichas bases de datos tienen la misma estructura en las tablas con las que se diseño el informe)
$idSistemaInformacion =  explode(',', $idSistema); 

$sisInf = DB::table('sistemainformacion')
->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
->whereIn('idSistemaInformacion',  $idSistemaInformacion)
->get();

$conexionBD = get_object_vars($sisInf[0]);
 
$sistemaInformacion = array();
$valores = array();
for($i = 0; $i < count($sisInf); $i++) 
{
    $sistemaInformacion[] = get_object_vars($sisInf[$i])["bdSistemaInformacion"];
} 


// Rango de fechas. indica la fecha inicial y la fecha final de los datos a consultar
$fechaInicial = (isset($_GET['fechaInicial']) ? $_GET['fechaInicial'] : '2016-05-01');
$fechaFinal = (isset($_GET['fechaFinal']) ? $_GET['fechaFinal'] : '2016-05-31');

// Otros parámetros. son otras propiedades para mejoramiento del informe, el formato de numeros, si se adicionan columnas de consolidados o porcentaje, si es informe detallado o resumido, etc 
$cifra = (isset($_GET['cifra']) ? $_GET['cifra'] : '1');
$formato = (isset($_GET['formato']) ? $_GET['formato'] : 'Det');
$colPorcentaje = (isset($_GET['colPorcentaje']) ? $_GET['colPorcentaje'] : 0);
$colVariacion = (isset($_GET['colVariacion']) ? $_GET['colVariacion'] : 0);
$colPorcentajeVert = (isset($_GET['colPorcentajeVert']) ? $_GET['colPorcentajeVert'] : '');
$colPorcentajeFormula = (isset($_GET['colPorcentajeFormula']) ? $_GET['colPorcentajeFormula'] : '');

// Primero consultamos las capas del informe, con el fin de recorrerlas, ya que cada capa 
// puede ser de diferente tipo (1:General, 2:Contable), por lo tanto llaman a diferentes procesos

$capas = DB::table('informe as I')
			->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
			->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, Icap.nombreInformeCapa, Icap.tituloInformeCapa'))
			->where('I.idInforme', '=', $idInforme)
			->get();

//for($c = 6; $c < 7; $c++) 
for($c = 0; $c < count($capas); $c++) 
{
    $capa = get_object_vars($capas[$c]);

    switch ($capa["tipoInformeCapa"]) {
    	// Informe General
    	case '1':
    		generarInformeGeneral($capa["idInformeCapa"], $sistemaInformacion, $conexionBD, $condicion);
    		break;

    	// Informe Contable
    	case '2':
    		echo $capa["tituloInformeCapa"].'<br><br>';
    		generarInformeContable($capa["idInformeCapa"], $sistemaInformacion, $conexionBD, $fechaInicial, $fechaFinal, $cifra, $formato, $colVariacion, $colPorcentaje, $colPorcentajeVert, $colPorcentajeFormula, $tipoContabilidad);
    		break;
    	
    	default:
    		# code...
    		break;
    }
}





function generarInformeContable($idInformeCapa, $sistemaInformacion, $conexionBD, $fechaInicial, $fechaFinal, $cifra, $formato, $colVariacion, $colPorcentaje, $colPorcentajeVert, $colPorcentajeFormula, $tipoContabilidad)
{

	// Para los informes contables, consultamos la tabla de Informe relacionada con la tabla de InformeConcepto
	$datos = DB::table('informe as I')
				->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
				->leftjoin('informeconcepto as Icon', 'Icap.idInformeCapa', '=', 'Icon.InformeCapa_idInformeCapa')
				->leftjoin('estiloinforme as Einf','Icon.EstiloInforme_idEstiloInforme','=', 'Einf.idEstiloInforme') 
				->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, ordenInformeConcepto, nombreInformeConcepto, tipoValorInformeConcepto, tipoMovimientoInformeConcepto, valorInformeConcepto, excluirTerceroInformeConcepto, detalleInformeConcepto, resumenInformeConcepto, concat("background-color:",colorFondoEstiloInforme,";border: solid 1px ", colorBordeEstiloInforme,";color: ", colorTextoEstiloInforme, ";font-family: ",fuenteTextoEstiloInforme, ";font-size: ",tamañoTextoEstiloInforme, ";font-weight: ",IF(negrillaEstiloInforme = 1, "bold", ""), ";font-style: ",IF(italicaEstiloInforme = 1, "italic", ""), ";text-decoration: ",IF(subrayadoEstiloInforme = 1, "underline", "")) as estilo'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->where('Icon.tipoValorInformeConcepto', '!=', 'Formula')
				// ->where('Icon.tipoMovimientoInformeConcepto', '=', 'SaldoContable')
				//->where('Icon.valorInformeConcepto', '!=', '')
				->get();



	//*******************************************
	// orden de los ciclos
	// 1. Bases de datos
	// 2. Concepto
	//*******************************************
	$consultaConcepto = '';
	
	foreach ($sistemaInformacion as $db => $baseDatos) 
	{
		

		//  Cada registro que recorremos es un concepto contable diferente
		
		$informecapa = array();
		for($i = 0; $i < count($datos); $i++) 
		{

		    $informecapa = get_object_vars($datos[$i]);

			// inicializamos las variables apara almacenar las condiciones de cuentas
			$condicionCuenta = '';
			$condicionNit = '';
			$cuentaRango = '';
			$cuentaIndividual = '';
			$cuentaExcluida = '';
			$cuentaRangoExcluido = '';
			// echo $informecapa["nombreInformeConcepto"].'<br>';
		    if($informecapa["tipoValorInformeConcepto"] == 'Puc')
		    {
		    	//*************************************************************************
				// PASO 1: Crear un condicion con los numeros de cuenta
				//*************************************************************************
				// Se deben tener en cuenta 2 formatos de cuentas diferentes:
				// 1. Rango de cuentas, separadas por guion, ejemplo: 1001-1001999999
				// 2. Cuentas individuales, simplemente separadas por coma, emeplo: 24080101, 24080102
				// 3. Cuentas Excluídas, son numeros de cuenta individuales pero comienzan por una letra x (mayuscula o minuscula), ejemplo: x10010501

		    	// lo primero es convertir el string de cuentas en un array explotandolo por comas
		    	$cuentas = explode(',', $informecapa["valorInformeConcepto"]);
		    	//print_r($cuentas);
		    		
		    	foreach ($cuentas as $pos => $valorCuenta) 
		    	{
		    		// 1. Rangos de Cuentas
		    		if(strpos( $valorCuenta, '-') !== false)
		    		{
		    			// el rango de cuentas puede ser tambien de tipo exclusion, si empieza por una letra X
		    			if(strpos(strtolower($valorCuenta),'x') !== false)
			    		{
			    			$cuentaRangoExcluido .= ($cuentaRangoExcluido != '' ? ' AND ' : ''). "(numeroCuentaContable NOT BETWEEN '". str_replace('-', "' AND '", trim($valorCuenta))."')";
			    		}
			    		else
			    		{
		    				$cuentaRango .= ($cuentaRango != '' ? ' OR ' : ''). "(numeroCuentaContable BETWEEN '". str_replace('-', "' AND '", trim($valorCuenta))."')";
		    			}
		    		}
		    		else if(strpos(strtolower($valorCuenta),'x') !== false)
		    		{
		    			$cuentaExcluida .= ($cuentaExcluida != '' ? ' , ' : '').  "'".trim($valorCuenta)."'";
		    		}
		    		else
		    		{
		    			$cuentaIndividual .= ($cuentaIndividual != '' ? ' , ' : ''). "'".trim($valorCuenta)."'";
		    		}

		    	}

			   
			    // Quitamos las X de las cuentas excluidas
			    $cuentaExcluida = str_replace('X', '', str_replace('x', '', $cuentaExcluida));
			    $cuentaRangoExcluido = str_replace('X', '', str_replace('x', '', $cuentaRangoExcluido));


				$cuentaIndividual = ($cuentaIndividual != '' ? 'numeroCuentaContable IN ('.$cuentaIndividual.')' : '');
				$cuentaExcluida = ($cuentaExcluida != '' ? 'numeroCuentaContable NOT IN ('.$cuentaExcluida.')' : '');
				
				// ahora juntamos todas las condiciones en una sola
				$condicionCuenta = 	($cuentaRango != '' ? $cuentaRango : '').
										(($cuentaRango != '' and $cuentaIndividual != '') ? ' OR ' : '').
									($cuentaIndividual != '' ? $cuentaIndividual : '');
				
				if($condicionCuenta != '')
					$condicionCuenta = '('.$condicionCuenta.')';

				$condicionCuenta .= 	(($condicionCuenta != '' and $cuentaRangoExcluido != '') ? ' AND ' : '').
									($cuentaRangoExcluido != '' ? $cuentaRangoExcluido : '');

				$condicionCuenta .=	(($condicionCuenta != '' and $cuentaExcluida != '') ? ' AND ' : '').
									($cuentaExcluida != '' ? $cuentaExcluida : '');

				
				// Exclusion de NITS
				$nitExcluido = '';
				// lo primero es convertir el string de nits en un array explotandolo por comas
		    	$nits = explode(',', $informecapa["excluirTerceroInformeConcepto"]);

		    	foreach ($nits as $pos => $valorNit) 
		    	{
		    		if($valorNit != '')
	    					$nitExcluido .= ($nitExcluido != '' ? ' , ' : ''). "'".trim($valorNit)."'";
		    	}
		    	$condicionNit = ($nitExcluido != '' ? '(T.documentoTercero NOT IN ('.$nitExcluido.')  or T.documentoTercero IS NULL)' : '');

 			}

			// Dependiendo del tipo de Movimiento (MovimientoContable o Saldo), cambian los nombres de los 
			// campos y y el nombre de la tabla principal
			if($informecapa["tipoMovimientoInformeConcepto"] == 'SaldoContable')
			{
				$campoValor = '((CON.saldoFinalDebitosContabilidad - CON.saldoFinalCreditosContabilidad)/'.$cifra.')';
				$campoValorNIIF = '((CON.saldoFinalDebitosNIIFContabilidad - CON.saldoFinalCreditosNIIFContabilidad)/'.$cifra.')';
				$tablaValor = $baseDatos.'.Contabilidad CON';
				$aliasEnc = 'CON';
				$aliasDet = 'CON';
			}
			else
			{
				$campoValor = '((MCD.debitosMovimientoContableDetalle - MCD.creditosMovimientoContableDetalle)/'.$cifra.')';
				$campoValorNIIF = '((MCD.debitosNIIFMovimientoContableDetalle - MCD.creditosNIIFMovimientoContableDetalle)/'.$cifra.')';
				$tablaValor = $baseDatos.'.MovimientoContableDetalle MCD left join '.$baseDatos.'.MovimientoContable MC 
								on MCD.MovimientoContable_idMovimientoContable = MC.idMovimientoContable';	
				$aliasEnc = 'MC';
				$aliasDet = 'MCD';
			}


			// Segun el rango de fechas del filtro, creamos para cada Mes o cada Año una columna 
			// independiente
			// ------------------------------------------------
			// Enero 	Febrero 	Marzo 	Abril......
			// ------------------------------------------------
			$inicio = $fechaInicial;
			$anioAnt = date("Y", strtotime($inicio));
			$columnas = '';
			while($inicio < $fechaFinal)
			{

				// cada que cambiemos de año, le adicionamos la columna con el total del año
				if($anioAnt != date("Y", strtotime($inicio)))
				{
					// $columnas .= "SUM(if(DATE_FORMAT(fechaInicialPeriodo,\"%Y\") = '".substr($anioAnt,0,4)."', (".$campoValor."), 0)) as Total_".substr($anioAnt,0,4).", ";
					$anioAnt = date("Y", strtotime($inicio));
				}

				// adicionamos la columna del mes
				// en el caso de cuentas PUC, se toma el dato desde la base de datos 
				// ($campoValor), pero cuando sea de tipo Valor fijo o porcentaje, simplemente
				// quemamos dicho valor con el alias del mes
				$anioVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($inicio)));
				if($informecapa["tipoValorInformeConcepto"] == 'Puc')
		    	{
					$columnas .= "IFNULL(SUM(if(fechaInicialPeriodo = '".$inicio."', (".$campoValor."), 0)),0) as ". nombreMes($inicio).'_Local_'.substr($inicio,0,4).", ";

					$columnas .= "IFNULL(SUM(if(fechaInicialPeriodo = '".$anioVariacion."', (".$campoValor."), 0)),0) as ". nombreMes($anioVariacion).'_Local_'.substr($anioVariacion,0,4).", ";

					$columnas .= "IFNULL(SUM(if(fechaInicialPeriodo = '".$inicio."', (".$campoValorNIIF."), 0)),0) as ". nombreMes($inicio).'_Niif_'.substr($inicio,0,4).", ";

					$columnas .= "IFNULL(SUM(if(fechaInicialPeriodo = '".$anioVariacion."', (".$campoValorNIIF."), 0)),0) as ". nombreMes($anioVariacion).'_Niif_'.substr($anioVariacion,0,4).", ";
				}
				else // si es $ , % o Titulo (porque en la consulta ya se excluyeron las formulas)
				{
					$columnas .= "'".$informecapa["valorInformeConcepto"]."' as ". nombreMes($inicio).'_Local_'.substr($inicio,0,4).", ";	
					$columnas .= "'".$informecapa["valorInformeConcepto"]."' as ". nombreMes($anioVariacion).'_Local_'.substr($anioVariacion,0,4).", ";	

					$columnas .= "'".$informecapa["valorInformeConcepto"]."' as ". nombreMes($inicio).'_Niif_'.substr($inicio,0,4).", ";	
					$columnas .= "'".$informecapa["valorInformeConcepto"]."' as ". nombreMes($anioVariacion).'_Niif_'.substr($anioVariacion,0,4).", ";	
				}
				//Avanzamos al siguiente mes
				$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
			}

			// Quitamos la ultima coma del concatenado de columnas
			$columnas = substr($columnas,0, strlen($columnas)-2);

			//calculamos el mismo rango de fechas pero con el año anterior para los campos de  variacion
			$fechaInicialVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($fechaInicial)));
			$fechaFinalVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($fechaFinal)));

			// Armamos la consulta a la base de datos con las condiciones del concepto 
			$consultaConcepto .= 
					"SELECT 
						'".$baseDatos."' as BaseDatos,
						'".$informecapa["ordenInformeConcepto"]."' as orden, 
						'".$informecapa["nombreInformeConcepto"]."' as concepto, 
						'".$informecapa["tipoValorInformeConcepto"]."' as tipoValor, 
						'".$informecapa["valorInformeConcepto"]."' as contenido,
						'".$informecapa["detalleInformeConcepto"]."' as detalle,
						'".$informecapa["resumenInformeConcepto"]."' as resumen, 
						'".$informecapa["estilo"]."' as estilo, 
						".$columnas."
					FROM ".$tablaValor." 
						left join ".$baseDatos.".CuentaContable CC
							on ".$aliasDet.".CuentaContable_idCuentaContable = CC.idCuentaContable
						left join ".$baseDatos.".Periodo P 
							on ".$aliasEnc.".Periodo_idPeriodo = P.idPeriodo 
						left join ".$baseDatos.".Ano A 
							on P.Ano_idAno = A.idAno
						".($condicionNit != '' 
							? "left join ".$baseDatos.".Tercero T
								on ".$aliasDet.".Tercero_idTercero = T.idTercero" 
							: "")."
					Where 	(
								(fechaInicialPeriodo >= '".$fechaInicial."' and 
								fechaFinalPeriodo <= '".$fechaFinal."') OR
								(fechaInicialPeriodo >= '".$fechaInicialVariacion."' and 
								fechaFinalPeriodo <= '".$fechaFinalVariacion."')
							)  and
							esAfectableCuentaContable = 1  
							".($condicionCuenta != '' ? ' and ' : ''). $condicionCuenta." 
							".($condicionNit != '' ? ' and ': '').$condicionNit." 
					 UNION ";

				// (".$campoValor.") <> 0 and 
				// echo $consultaConcepto.'<br>';

		}

	}
	// echo $columnasPpal;
	$consultaConcepto = substr($consultaConcepto, 0 , strlen($consultaConcepto)-6);
	//echo $consultaConcepto.'<br><br>';

	$columnasPpal = '';
	foreach ($sistemaInformacion as $db => $baseDatos) 
	{
	    // recorremos cada uno de los periodos para biuscar su valor
		$inicio = $fechaInicial;
		$anioAnt = date("Y", strtotime($inicio));
		while($inicio < $fechaFinal)
		{
			$anioAnt = date("Y", strtotime($inicio));

			foreach ($tipoContabilidad as $tc => $tipoCont) 
			{
				$mes = nombreMes($inicio).'_'.$tipoCont.'_'.substr($inicio,0,4);

				$anioVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($inicio)));
				$anioVariacion = nombreMes($anioVariacion).'_'.$tipoCont.'_'.substr($anioVariacion,0,4);
				// creamos las columnas para la consulta principal que convierte los datos de las compañias en columnas
				$columnasPpal .= "SUM(if(BaseDatos = '".$baseDatos."', ".$mes.", 0)) as ". $baseDatos.'_'.$mes.", ";

				$columnasPpal .= "SUM(if(BaseDatos = '".$baseDatos."', ". $anioVariacion.", 0)) as ". $baseDatos.'_'.$anioVariacion.", ";
			}
			//Avanzamos al siguiente mes
			$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
		}				
	}
	$columnasPpal = substr($columnasPpal, 0 , strlen($columnasPpal)-2);
	$consultaConcepto = "SELECT 
							orden, 
							concepto, 
							tipoValor,
							contenido,
							detalle,
							resumen, 
							estilo, 
							".$columnasPpal."
						FROM (".$consultaConcepto.") as Final
						Group By concepto ";
	//echo $consultaConcepto;

	// Config::set( 'database.connections.'.$conexionBD['bdSistemaInformacion'], array 
 //    ( 
 //        'driver'     =>  $conexionBD['motorbdSistemaInformacion'], 
 //        'host'       =>  $conexionBD['ipSistemaInformacion'], 
 //        'port'       =>  $conexionBD['puertoSistemaInformacion'], 
 //        'database'   =>  $conexionBD['bdSistemaInformacion'], 
 //        'username'   =>  $conexionBD['usuarioSistemaInformacion'], 
 //        'password'   =>  $conexionBD['claveSistemaInformacion'], 
 //        'charset'    =>  'utf8', 
 //        'collation'  =>  'utf8_unicode_ci', 
 //        'prefix'     =>  ''
 //    )); 

 //    $conexion = DB::connection($conexionBD['bdSistemaInformacion'])->getDatabaseName();
 //    $datos = DB::connection($conexionBD['bdSistemaInformacion'])->select($consultaConcepto);

	$datos = DB::select($consultaConcepto);

	// por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
	$consulta = array();
	for($i = 0; $i < count($datos); $i++) 
	{
	    $consulta[] = get_object_vars($datos[$i]);
	}

	
	
	//**************************************
	//
	//            C A L C U L O
	//        D E   F O R M U L A S
	//
	//**************************************

	// Luego de tener todos los datos de cuentas PUC, vlaores y porcentajes fijos, procedemos 
	// a consultar las formulas del informe para calcularlas

	$datos = DB::table('informe as I')
				->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
				->leftjoin('informeconcepto as Icon', 'Icap.idInformeCapa', '=', 'Icon.InformeCapa_idInformeCapa')
				->leftjoin('estiloinforme as Einf','Icon.EstiloInforme_idEstiloInforme','=', 'Einf.idEstiloInforme') 
				->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, ordenInformeConcepto, nombreInformeConcepto, tipoValorInformeConcepto, tipoMovimientoInformeConcepto, valorInformeConcepto, excluirTerceroInformeConcepto, detalleInformeConcepto, resumenInformeConcepto, concat("background-color:",colorFondoEstiloInforme,";border: solid 1px ", colorBordeEstiloInforme,";color: ", colorTextoEstiloInforme, ";font-family: ",fuenteTextoEstiloInforme, ";font-size: ",tamañoTextoEstiloInforme, ";font-weight: ",IF(negrillaEstiloInforme = 1, "bold", ""), ";font-style: ",IF(italicaEstiloInforme = 1, "italic", ""), ";text-decoration: ",IF(subrayadoEstiloInforme = 1, "underline", "")) as estilo'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->where('Icon.tipoValorInformeConcepto', '=', 'Formula')
				->where('Icon.valorInformeConcepto', '!=', '')
				->orderby('Icon.ordenInformeConcepto')
				->get();


	$informecapa = array();
	for($i = 0; $i < count($datos); $i++) 
	{

	    $informecapa = get_object_vars($datos[$i]);
	    
	    // Ejecutamos el calculo de formula enviando el concepto de formula actual y la consulta de datos
	    // el valor retornado sera la misma consulta de datos modificada
	    $consulta = calcularFormula($informecapa, $consulta, $informecapa["idInformeCapa"], $sistemaInformacion, $fechaInicial, $fechaFinal, $tipoContabilidad, $colPorcentajeFormula);
	}
	

	//**************************************
	//
	//      C O N S O L I D A D O  
	// (V A R I A S   E M P R E S A S)
	//
	//**************************************
	// calculamos el valor consolidado de las empresas
	if(count($sistemaInformacion) > 1)
	 	$consulta = calcularConsolidadoEmpresas($consulta, $fechaInicial, $fechaFinal, $sistemaInformacion, $tipoContabilidad);


	//**************************************
	//
	// 			V A R I A C I O N   
	// 		  E N T R E   M E S E S  
	//
	//**************************************
	// calculamos el valor y porcentaje de variacion entre meses (del mismo año o anterior)
	if($colVariacion != 0)
	{
		$consulta = calcularVariacion($consulta, $fechaInicial, $fechaFinal, $sistemaInformacion, $tipoContabilidad, $colVariacion);

	}

	//**************************************
	//
	// V A R I A C I O N   V E R T I C A L 
	//
	//**************************************
	// calculamos el porcentaje de variacion vertical
	// obtenemos le valor del concepto seleccionado por el usuario como base del calculo
	if($colPorcentajeVert != '')
	{

		$consulta = calcularPorcentajeVertical($colPorcentajeVert, $consulta, $fechaInicial, $fechaFinal, $sistemaInformacion, $tipoContabilidad);
	}
	
	//imprimirTabla($consulta,'');

	//**************************************
	//
	//  V A R I A C I O N   V E R T I C A L 
	//
	//  B A S A D A   E N   F O R M U L A
	//  ( B A L A N C E   G E N E R A L )
	//
	//**************************************
	// por ultimo antes de imprimir los resultados, calculamos el porcentaje de variacion vertical
	// obtenemos le valor del concepto seleccionado por el usuario como base del calculo
	if($colPorcentajeFormula != '')
	{
	 	$informecapa = array();
	 	// recorremos la consulta de formulas
		for($i = 0; $i < count($datos); $i++) 
		{

	    	$informecapa = get_object_vars($datos[$i]);

	    	// recorremos el rango de meses del informe
		    $inicio = $fechaInicial;
			while($inicio < $fechaFinal)
			{
				
				// para cada tipo de contabilidad (Local o NIIF)
				foreach ($tipoContabilidad as $tc => $tipoCont) 
				{
					// para el año actual y el año anterior
					for($anio = 0; $anio <= 1; $anio++)
					{
						// calculamos el año
						$anioactual = date("Y-m-d",strtotime("-".$anio." Year", strtotime($inicio)));
						$mes = nombreMes($anioactual).'_'.$tipoCont.'_'.substr($anioactual,0,4);
						

						//$mes = nombreMes($inicio).'_'.$tipoCont.'_'.substr($inicio,0,4);


						// por cada mes creamos titulos de las bases de datos,
						foreach ($sistemaInformacion as $db => $baseDatos) 
						{
							$consulta = calcularPorcentajeVerticalBasadoFormula($informecapa["valorInformeConcepto"], $mes, $baseDatos, $consulta, $idInformeCapa, $informecapa["nombreInformeConcepto"]);
						}

						// Luego de calcular el porcentaje del mes en todas las empresas
						// calculamos el porcentaje consolidade de ellas
						// en esta recorremos primero el array de datos y luego cada empresa 
						// para sumarlas
						// con la suma de las empresas calculamos el porcentaje
						$consulta = calcularPorcentajeVerticalBasadoFormula($informecapa["valorInformeConcepto"], $mes, 'Cons', $consulta, $idInformeCapa, $informecapa["nombreInformeConcepto"]);
					}
				}

				//Avanzamos al siguiente mes
				$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
			}
		}
	}


	// imprimirTabla($consulta,'Despues de porc vertical');
	//**************************************
	//
	// I M P R E S I O N   D E L   I N F O R M E 
	//
	//**************************************

	echo '<style>
		@media print{
		   div.saltopagina{ 
		      display:block; 
		      page-break-before:always;
		   }
		}
		</style>';
	// Ejecutamos la funcion que genera el codigo HTML con la informacion enviada
	imprimirInformeContable($consulta, $formato, $fechaInicial, $fechaFinal, $sistemaInformacion, $colVariacion, $colPorcentaje, $colPorcentajeVert, $colPorcentajeFormula, $tipoContabilidad);

	echo '<div class="saltopagina"></div>';
}



function calcularFormula($informecapa, $consulta, $idInformeCapa, $sistemaInformacion, $fechaInicial, $fechaFinal, $tipoContabilidad, $colPorcentajeFormula)
{
	
	$conceptoFormula = $informecapa["nombreInformeConcepto"];
	$formula = $informecapa["valorInformeConcepto"];
    $formulaOriginal = $formula;


	$regcon = count($consulta);
	$sw = false;
	// recorremos el array de datos, buscando el nombre del concepto
	for($buscar = 0; $buscar < $regcon; $buscar++)
	{
		if(trim($consulta[$buscar]["concepto"]) == trim($informecapa["nombreInformeConcepto"]))
		{
			$regcon = $buscar;
			$sw = true;
		}
	}

	if($sw === false)
	{
		// comenzamos adicionando al array $consulta el concepto que vamos a calcular
		// y dentro del ciclo de calculo de formula, le vamos llenando el valor en cada mes
		$consulta[$regcon]["orden"] = $informecapa["ordenInformeConcepto"];
		$consulta[$regcon]["concepto"] = $informecapa["nombreInformeConcepto"];
		$consulta[$regcon]["tipoValor"] = $informecapa["tipoValorInformeConcepto"];
		$consulta[$regcon]["contenido"] = $informecapa["valorInformeConcepto"];
		$consulta[$regcon]["detalle"] = $informecapa["detalleInformeConcepto"];
		$consulta[$regcon]["resumen"] = $informecapa["resumenInformeConcepto"];
		$consulta[$regcon]["estilo"] = $informecapa["estilo"];
	}
    
   
	// recorremos las columnas por cada Base de datos, mes y tipo de contabilidad
	foreach ($sistemaInformacion as $db => $baseDatos) 
	{
	    // recorremos cada uno de los periodos para biuscar su valor
		$inicio = $fechaInicial;
		$anioAnt = date("Y", strtotime($inicio));
		while($inicio <= $fechaFinal)
		{
			$anioAnt = date("Y", strtotime($inicio));

			foreach ($tipoContabilidad as $tc => $tipoCont) 
			{
				
				for($anio = 0; $anio <= 1; $anio++)
				{
					// calculamos el año
					$anioactual = date("Y-m-d",strtotime("-".$anio." Year", strtotime($inicio)));
					$mes = nombreMes($anioactual).'_'.$tipoCont.'_'.substr($anioactual,0,4);
					// $anioVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($inicio)));
					// $mesVariacion = nombreMes($anioVariacion).'_'.$tipoCont.'_'.substr($anioVariacion,0,4);
					
					
	    			$formula = $formulaOriginal;

					$nuevaFormula = '';
				    $total = strlen($formula);
				    $pos = 0;
				    $aux = 0;
				    $ind = 0;
				    $valor = 0;

				    // las formulas tienen variables o valores fijos los cuales estan separados
				    // por signos o parentesis "+ - * / ( )", todo lo que se encuentre entre 2 signos lo 
				    // trataremos como un valor a calcular o valor fijo

				    // tomamos le strig de la formula y lo recorremos caracter por caracter, preguntando 
				    // si es un signo, si es asi lo vamos almacenand en un array con su correspondiente 
				    // valor que tomaremos desde la consulta de datos de PUC

				    while($formula != '' and $pos <= $total)
				    {

				    	$letra = substr($formula, $aux, 1);
				    	

				    	if($letra == '+' or $letra == '-' or $letra == '*' or $letra == '/' or $letra == '(' or $letra == ')')
				    	{
				    		
				    		// tomamos el concepto de formula
				    		$concepto = trim(substr($formula, 0, $aux));
							
							//**************************************
							//
							// C A L C U L O   D E   F O R M U L A
							//
							//**************************************
							// obtenemos le valor del concepto en el mes actual
							$valorConcepto = obtenerValor(
								$baseDatos, 
								$concepto, 
								$consulta,
								$mes
								);

							// Si no existe ese concepto en la $consulta, entonces es porque es
							// una formula que debemos calcular previamente
							if($valorConcepto === false)
							{
								$valorConcepto = 0;

								$datosnuevo = DB::table('informe as I')
								->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
								->leftjoin('informeconcepto as Icon', 'Icap.idInformeCapa', '=', 'Icon.InformeCapa_idInformeCapa')
								->leftjoin('estiloinforme as Einf','Icon.EstiloInforme_idEstiloInforme','=', 'Einf.idEstiloInforme') 
								->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, ordenInformeConcepto, nombreInformeConcepto, tipoValorInformeConcepto, tipoMovimientoInformeConcepto, valorInformeConcepto, excluirTerceroInformeConcepto, detalleInformeConcepto, resumenInformeConcepto, concat("background-color:",colorFondoEstiloInforme,";border: solid 1px ", colorBordeEstiloInforme,";color: ", colorTextoEstiloInforme, ";font-family: ",fuenteTextoEstiloInforme, ";font-size: ",tamañoTextoEstiloInforme, ";font-weight: ",IF(negrillaEstiloInforme = 1, "bold", ""), ";font-style: ",IF(italicaEstiloInforme = 1, "italic", ""), ";text-decoration: ",IF(subrayadoEstiloInforme = 1, "underline", "")) as estilo'))
								->where('Icap.idInformeCapa', '=', $idInformeCapa)
								->where('Icon.tipoValorInformeConcepto', '=', 'Formula')
								->where('Icon.nombreInformeConcepto', '=', $concepto)
								->get();

								if(count($datosnuevo) > 0)
								{
									$consulta  = calcularFormula(get_object_vars($datosnuevo[0]), $consulta, $idInformeCapa, $sistemaInformacion, $fechaInicial, $fechaFinal, $tipoContabilidad, $colPorcentajeFormula);
									
									// si tuvimos que calcularlo, hay que volverlo a buscar en el array de $consulta que ya deberia estar su valor
									
									$valorConcepto = obtenerValor(
													$baseDatos, 
													$concepto, 
													$consulta,
													$mes
													);

								}
								
							}
							
							// con el valor del concepto, vamos armando la misma formula en otra variable, pero 
							// ya en valores en vez de conceptos
							$nuevaFormula .= $valorConcepto . $letra;
				    		$ind++;

				    		$formula = substr($formula, $aux+1);
				    		//echo $num.'Resto de Formula '.$formula.'<br>';
				    		$aux = 0;
				    	}
				    	$aux++;
				    	$pos++;
				    }
				    
				    // obtenemos le valor del ultimo concepto de la formula en el mes actual
					$valorConcepto = obtenerValor(
						$baseDatos, 
						$formula, 
						$consulta,
						$mes
						);
					
					// con el valor del concepto, vamos armando la misma formula en otra variable, pero 
					// ya en valores en vez de conceptos
					$nuevaFormula .= $valorConcepto . $letra;
				
					try {
						$valor = eval('return '.$nuevaFormula.';');

					} catch (\Exception $e) {
					    echo 'Error de Calculo: En la formula <br>'.$formulaOriginal.' ',  $e->getMessage(), "<br>";
					    $valor = 0;
					    //return 0;
					}
					
					$consulta[$regcon][$baseDatos.'_'.$mes] = $valor;
				}	
				
			}

			

			//Avanzamos al siguiente mes
			$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
		}
	}
    		
 	return $consulta;
}




function imprimirTabla($tabla, $titulo)
{
    echo "<br>$titulo<br><table>";
        foreach ($tabla as $indice => $valor)
        {
            echo '<tr>';
            //echo $indice.' = '.$valor.'<br>';
            foreach ($valor  as $indice2 => $valor2)
            {
                echo '<td>'.$indice2.'='.$valor2.'</td>';
            }
            echo '</tr>';
        }
        echo '</table><br><br>'; 
}


function calcularPorcentajeVerticalBasadoFormula($formula, $mes, $baseDatos, $consulta, $idInformeCapa, $nombreFormula)
{
	// recorremos el array de datos, buscando el nombre del concepto de formula a calcular
	// para obtener el valor total
	$valorFormula = 1;
	for($buscar = 0; $buscar < count($consulta); $buscar++)
	{
		if(trim($consulta[$buscar]["concepto"]) == trim($nombreFormula))
		{
			$valorFormula = (isset($consulta[$buscar][$baseDatos.'_'.$mes]) ? $consulta[$buscar][$baseDatos.'_'.$mes] : 0) ;
		}
	}

	// las formulas tienen variables o valores fijos los cuales estan separados
    // por signos o parentesis "+ - * / ( )", todo lo que se encuentre entre 2 signos lo 
    // trataremos como un valor a calcular o valor fijo

    // tomamos le string de la formula y lo recorremos caracter por caracter, preguntando 
    // si es un signo, si es asi lo vamos almacenando en un array con su correspondiente 
    // valor 
    $resultado = $formula;
    $variable = array();

    $formulaOriginal = $formula;

    $total = strlen($formula);
    $pos = 0;
    $aux = 0;
    $ind = 0;
    $valor = 0;
    while($formula != '' and $pos <= $total)
    {
    	$letra = substr($formula, $aux, 1);
    	if($letra == '+' or $letra == '-' or $letra == '*' or $letra == '/' or $letra == '(' or $letra == ')')
    	{
    		// tomamos el concepto de formula
    		$concepto = trim(substr($formula, 0, $aux));
    		
    		// recorremos el array de datos, buscando el nombre del concepto
			for($buscar = 0; $buscar < count($consulta); $buscar++)
			{
				if(trim($consulta[$buscar]["concepto"]) == trim($concepto))
				{
					$consulta[$buscar]['Porc_'.$baseDatos.'_'.$mes] = 
						(isset($consulta[$buscar][$baseDatos.'_'.$mes]) ? $consulta[$buscar][$baseDatos.'_'.$mes] : 0)/
						($valorFormula == 0 ? 1 : $valorFormula) * 100;
				}
			}
			$formula = substr($formula, $aux+1);
    		$aux = 0;

    	}
    	$aux++;
    	$pos++;
    }
    $concepto = $formula;
    // recorremos el array de datos, buscando el nombre del concepto
	for($buscar = 0; $buscar < count($consulta); $buscar++)
	{
		if(trim($consulta[$buscar]["concepto"]) == trim($concepto))
		{
			$consulta[$buscar]['Porc_'.$baseDatos.'_'.$mes] = 
				(isset($consulta[$buscar][$baseDatos.'_'.$mes]) ? $consulta[$buscar][$baseDatos.'_'.$mes] : 0)/
				($valorFormula == 0 ? 1 : $valorFormula) * 100;
		}
	}
	
 	return $consulta;
}


function calcularVariacion($consulta, $fechaInicial, $fechaFinal, $sistemaInformacion, $tipoContabilidad, $parVariacion)
{
	
	// segun los periodos del informe, es una variacion diferente
	// si el usuario filtra un solo mes, la variacion se hace contra el mismo mes pero en el año anterior
	// si el usuario filtra varios meses (rango), la variacion se hace en cada mes contra el mes anterior (del mismo año)
	// para esto definimos una variable para saber el tipo de variacion
	if(date("Y-m", strtotime($fechaInicial)) == date("Y-m", strtotime($fechaFinal)))
		$tipoVariacion = 'Anual';
	else
		$tipoVariacion = 'Mensual';


	// para saber cuantas columnas vamos a imprimir de cada compania, debemos tener en cuenta
	// si el usuario pide columnas de % y de variacion
	//verificamos si se va a imprimir la columna de consilacion (cuando hay mas de una empresa)
	$totalEmpresas	= count($sistemaInformacion);
	if($totalEmpresas > 1)
		$consolidar = 1;
	else
		$consolidar = 0;


	$i = 0;
	while( $i < count($consulta)) 
	{
		
		// recorremos cada uno de los periodos  y por cada periodo, recorreremos cada una de las bases de datos
		$inicio = $fechaInicial;
		$anioAnt = date("Y", strtotime($inicio));
		$primerMes = false;

		$variacion = 0;
		while($inicio < $fechaFinal)
		{
			$consolidadoConcepto = 0;
			$consolidadoConceptoVariacion = 0;

			$anioAnt = date("Y", strtotime($inicio));

			foreach ($tipoContabilidad as $tc => $tipoCont) 
			{
				$mes = nombreMes($inicio).'_'.$tipoCont.'_'.substr($inicio,0,4);
				$primerMes = ($inicio == $fechaInicial ? true : false);
				
				$colVariacion = ($parVariacion and 
						(($tipoVariacion == 'Mensual' and $primerMes == false) or 
						$tipoVariacion == 'Anual' )) ? true : false;


				foreach ($sistemaInformacion as $db => $baseDatos) 
				{
					
					$consolidadoConcepto += $consulta[$i][$baseDatos.'_'.$mes];
				}

				if($colVariacion )
				{
					// segun el tipo de variacion tomamos un periodo diferente para comparar
					if($tipoVariacion == 'Anual')
						$anioVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($inicio)));
					else
						$anioVariacion = date("Y-m-d",strtotime("-1 Month", strtotime($inicio)));

					$mesVariacion = nombreMes($anioVariacion).'_'.$tipoCont.'_'.substr($anioVariacion,0,4);

					// recorremos cada base de datos
					
					foreach ($sistemaInformacion as $db => $baseDatos) 
					{
						// el mes de variacion solo se imprime cuando es variacion anual, 
						// ya que es el valor del año anterior
						$valorVariacion = 0;
						if($tipoVariacion == 'Anual')
						{		

							if(isset($consulta[$i][$baseDatos.'_'.$mesVariacion]))
								$valorVariacion = $consulta[$i][$baseDatos.'_'.$mesVariacion];
							else
								$valorVariacion = 0;

						}

						$consolidadoConceptoVariacion += $consulta[$i][$baseDatos.'_'.$mesVariacion];
					}
					

					// cada que termina de recorrer las bases de datos, imprime el total consolidado
					if($consolidar)
					{
						$consulta[$i]['Var_'.$mes] = 
							($consulta[$i]["tipoValor"] == 'Titulo' ? '' : $consolidadoConceptoVariacion);
					}
					
					$variacion = $consolidadoConcepto - $consolidadoConceptoVariacion;
					$porcvariacion = (1 - ($consolidadoConceptoVariacion / ($consolidadoConcepto == 0 ? 1 : $consolidadoConcepto))) * 100;
				}				

				if($colVariacion and $tipoVariacion == 'Mensual')
				{	
					$consulta[$i]['Var_'.$mes] = 
						($consulta[$i]["tipoValor"] == 'Titulo' ? '' :$variacion);
					$consulta[$i]['PorcVar_'.$mes] = 
						($consulta[$i]["tipoValor"] == 'Titulo' ? '' :$porcvariacion );
				}
			}
			//Avanzamos al siguiente mes
			$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
		}
		if($colVariacion and $tipoVariacion == 'Anual')
		{	
			$consulta[$i]['Var_'.$mes] = 
				($consulta[$i]["tipoValor"] == 'Titulo' ? '' :$variacion);
			$consulta[$i]['PorcVar_'.$mes] = 
				($consulta[$i]["tipoValor"] == 'Titulo' ? '' :$porcvariacion );
		}
		
		$i++;
	}
	return $consulta;
}

function calcularConsolidadoEmpresas($consulta, $fechaInicial, $fechaFinal, $sistemaInformacion, $tipoContabilidad)
{
	// recorremos cada uno de los conceptos, calculando el porcentaje de participacion sobre el concepto base
	for($i = 0; $i < count($consulta); $i++)
	{

		// recorremos cada uno de los periodos  y por cada periodo, recorreremos cada una de las bases de datos
		$inicio = $fechaInicial;
		$anioAnt = date("Y", strtotime($inicio));
		while($inicio < $fechaFinal)
		{
			foreach ($tipoContabilidad as $tc => $tipoCont) 
			{
				$anioAnt = date("Y", strtotime($inicio));
				$mes = nombreMes($inicio).'_'.$tipoCont.'_'.substr($inicio,0,4);
			
				// recorremos cada base de datos y vamos acumulando sus valores por mes
				$consulta[$i]['Cons_'.$mes] = 0;
				foreach ($sistemaInformacion as $db => $baseDatos) 
				{
					$consulta[$i]['Cons_'.$mes] += $consulta[$i][$baseDatos.'_'.$mes];
				}
			}
			//Avanzamos al siguiente mes
			$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
		}
	}
	return $consulta;
}

function calcularPorcentajeVertical($colPorcentajeVert, $consulta, $fechaInicial, $fechaFinal, $sistemaInformacion, $tipoContabilidad)
{
	// primero buscamos el concepto de base enviado por parametro
	// recorremos el array de datos, buscando el nombre del concepto
	$posBase = -1;
	for($buscar = 0; $buscar < count($consulta); $buscar++)
	{
		if($consulta[$buscar]["concepto"] == $colPorcentajeVert)
		{
			$posBase = $buscar;

			// modificamos el nombre del conceptos base con asteriscos para identificarlo 
			$consulta[$buscar]["concepto"] = '* '.$consulta[$buscar]["concepto"].' *';	
		}
	}
	// si encontramos el concepto base, calculamos los porcentajes
	if($posBase >= 0)
	{
		// recorremos cada uno de los conceptos, calculando el porcentaje de participacion sobre el concepto base
		for($i = 0; $i < count($consulta); $i++)
		{
			$inicio = $fechaInicial;
			$anioAnt = date("Y", strtotime($inicio));
			while($inicio < $fechaFinal)
			{
				$anioAnt = date("Y", strtotime($inicio));
				
				foreach ($tipoContabilidad as $tc => $tipoCont) 
				{
					$mes = nombreMes($inicio).'_'.$tipoCont.'_'.substr($inicio,0,4);

					$anioVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($inicio)));
					$mesVariacion = nombreMes($anioVariacion).'_'.$tipoCont.'_'.substr($anioVariacion,0,4);
				

					// por cada mes creamos titulos de las bases de datos,
					foreach ($sistemaInformacion as $db => $baseDatos) 
					{
						// si el valor base de calculo es cero, lo convertimos en 1 para que no se genere error de division por cero
						$valorBase = $consulta[$posBase][$baseDatos.'_'.$mes] == 0 
										? 1 
										: $consulta[$posBase][$baseDatos.'_'.$mes];

						if(isset($consulta[$posBase][$baseDatos.'_'.$mesVariacion]))
						{	
							$valorVariacion = $consulta[$i][$baseDatos.'_'.$mesVariacion];
							$valorBaseVariacion = $consulta[$posBase][$baseDatos.'_'.$mesVariacion] == 0 
										? 1 
										: $consulta[$posBase][$baseDatos.'_'.$mesVariacion];
						}
						else
						{
							$valorVariacion = 1;
							$valorBaseVariacion = 1;
						}

						if(isset($consulta[$posBase]['Cons_'.$mes]))
						{							
							$valorBaseConsolidado = $consulta[$posBase]['Cons_'.$mes] == 0 
													? 1 
													: $consulta[$posBase]['Cons_'.$mes];
												
						}						// creamos una nueva posicion en el array de consulta con el calculo de porcentaje pro cada mes y empresa
						
						$consulta[$i]['Porc_'.$baseDatos.'_'.$mes] = $consulta[$i][$baseDatos.'_'.$mes] / $valorBase * 100;

						$consulta[$i]['Porc_'.$baseDatos.'_'.$mesVariacion] = $valorVariacion / $valorBaseVariacion * 100;

						if(isset($consulta[$posBase]['Cons_'.$mes]))
						{
							$consulta[$i]['Porc_Cons_'.$mes] = $consulta[$i]['Cons_'.$mes] / $valorBaseConsolidado * 100;
						}
						
					}

					if(isset($consulta[$posBase]['Var_'.$mes]))
					{
						$valorBaseVariacion = $consulta[$posBase]['Var_'.$mes] == 0 
										? 1 
										: $consulta[$posBase]['Var_'.$mes];

						$consulta[$i]['PorcV_Var_'.$mes] = $consulta[$i]['Var_'.$mes] / $valorBaseVariacion * 100;
					}

				}

				//Avanzamos al siguiente mes
				$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
			}

		}


	}
	// devolvemos la consulta ya que le hicimos cambios de estructura adicionandole el porcentaje en cada mes y empresa
	return $consulta;

}

function obtenerValor($bd, $Variable, $datos, $mes)
{
	// recorremos el array de datos, buscando el nombre del concepto
	for($buscar = 0; $buscar < count($datos); $buscar++)
	{
		if(trim($datos[$buscar]["concepto"]) == trim($Variable))
		{
			return '('.$datos[$buscar][$bd.'_'.$mes].')';
		}
	}
	return false;
}

function imprimirInformeContable($consulta, $formato, $fechaInicial, $fechaFinal, $sistemaInformacion, $parVariacion, $colPorcentaje, $colPorcentajeVert, $colPorcentajeFormula, $tipoContabilidad)
{
	$MC = false;
	
	 // imprimirTabla($consulta, 'CON FORMULAS');

	// antes de enviar a imprimir, ordenamos el array por el campo "orden"
	// para esto primero se crea 1 array auxiliar con los datos de este campo y luego se 
	// envia a la funcion array_multisort
	foreach ($consulta as $key => $registro) 
	{
    	$ORD[$key] = $registro['orden'];
	}
	array_multisort($ORD, SORT_ASC, $consulta);


	// para saber cuantas columnas vamos a imprimir de cada compania, debemos tener en cuenta
	// si el usuario pide columnas de % y de variacion
	//verificamos si se va a imprimir la columna de consilacion (cuando hay mas de una empresa)
	$totalEmpresas	= count($sistemaInformacion);
	if($totalEmpresas > 1)
		$consolidar = 1;
	else
		$consolidar = 0;

	// calculamos el total de columnas por mes, si son varias empresas, va a ser igual al numero de empresas + 1 columnas (Consolidado), si solo es una empresa, este sera el total de columnas
	$totalColumnas = 0;
	if($totalEmpresas > 1)
		$totalColumnas = $totalEmpresas +1;
	else
		$totalColumnas = 1;

	// si el usuario pidio porcentaje de participacion (porcentaje vertical), le sumamos una columna mas de cada empresa a cada mes
	if($colPorcentajeVert != '' or $colPorcentajeFormula != '')
			$totalColumnas += 1 * $totalEmpresas;

	// si es de varias empresas y adicionalmente con porcentaje vertical adicionamos una columna mas
	if($totalEmpresas > 1 and ($colPorcentajeVert != '' or $colPorcentajeFormula != ''))
		$totalColumnas++;

	// segun los periodos del informe, es una variacion diferente
	// si el usuario filtra un solo mes, la variacion se hace contra el mismo mes pero en el año anterior
	// si el usuario filtra varios meses (rango), la variacion se hace en cada mes contra el mes anterior (del mismo año)
	// para esto definimos una variable para saber el tipo de variacion
	if(date("Y-m", strtotime($fechaInicial)) == date("Y-m", strtotime($fechaFinal)))
		$tipoVariacion = 'Anual';
	else
		$tipoVariacion = 'Mensual';


	$titulosSis = '';
	$detalleInforme = '';
	$detalleInforme .= '<table style="width:100%; border: 1px solid;" cellspacing="0" cellpadding="2">';
	$detalleInforme .= '<tr>';
	$detalleInforme .= '<td style="text-align:center; border: 1px solid;">Detalle de</td>';

	$primerMes = false;
	$inicio = $fechaInicial;
	$anioAnt = date("Y", strtotime($inicio));
	while($inicio < $fechaFinal)
	{
		$anioAnt = date("Y", strtotime($inicio));
		foreach ($tipoContabilidad as $tc => $tipoCont) 
		{
			$mes = nombreMes($inicio).' '.$tipoCont.' '.substr($inicio,0,4);

			$primerMes = ($inicio == $fechaInicial ? true : false);

			$colVariacion = ($parVariacion and 
							(($tipoVariacion == 'Mensual' and $primerMes == false) or 
							$tipoVariacion == 'Anual' )) ? true : false;

			$detalleInforme .= '<td style="text-align:center; border: 1px solid;" colspan="'.$totalColumnas.'"> '.$mes.'</td>';
			
			// por cada mes creamos titulos de las base de datos,
			foreach ($sistemaInformacion as $db => $baseDatos) 
			{
				$titulosSis .= '<td style="text-align:center; border: 1px solid;">'.$baseDatos.'</td>';

				if($colPorcentajeVert != '' or $colPorcentajeFormula != '')
					$titulosSis .= '<td style="text-align:center; border: 1px solid;">% Vert</td>';					
			}
			if($consolidar)
			{
				$titulosSis .= '<td style="text-align:center; border: 1px solid;">Consolidado</td>';

				if($colPorcentajeVert != '' or $colPorcentajeFormula != '')
							$titulosSis .= '<td style="text-align:center; border: 1px solid;">% Vert Cons</td>';
			}

			if($colVariacion)
			{
				// segun el tipo de variacion tomamos un periodo diferente apra comparar
				if($tipoVariacion == 'Anual')
					$anioVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($inicio)));
				else
					$anioVariacion = date("Y-m-d",strtotime("-1 Month", strtotime($inicio)));

				$mesVariacion = nombreMes($anioVariacion).' '.$tipoCont.' '.substr($anioVariacion,0,4);
				
				// por cada base de datos, creamos titulos de los meses
				if($tipoVariacion == 'Anual')
				{
					$detalleInforme .= '<td style="text-align:center; border: 1px solid;" colspan="'.$totalColumnas.'"> '.$mesVariacion.'</td>';
										
				}
				
				foreach ($sistemaInformacion as $db => $baseDatos) 
				{
					if($tipoVariacion == 'Anual')
					{
						$titulosSis .= '<td style="text-align:center; border: 1px solid;">'.$baseDatos.'</td>';

						if($colPorcentajeVert != '' or $colPorcentajeFormula != '')
							$titulosSis .= '<td style="text-align:center; border: 1px solid;">% Vert</td>';
					}
				}
				if($consolidar)
				{
					$titulosSis .= '<td style="text-align:center; border: 1px solid;">Consolidado Anterior</td>';
				}
			

				// cada que terminemos un mes, debemos poner la variacion
				if($colVariacion)
					$titulosSis .= '<td  style="text-align:center; border: 1px solid;">Variacion</td><td style="text-align:center; border: 1px solid;">% Var.</td>';

				if($colVariacion and $tipoVariacion == 'Mensual')
				{	
					$detalleInforme .= '<td style="text-align:center; border: 1px solid;" colspan="'.(($totalEmpresas > 1 ? 1 : 0)+2).'">Variación</td>';
				}

			}
		}
		//Avanzamos al siguiente mes
		$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
	}

	if($colVariacion and $tipoVariacion == 'Anual')
	{	
		$detalleInforme .= '<td style="text-align:center; border: 1px solid;" colspan="'.(($totalEmpresas > 1 ? 1 : 0)+2).'">Variación</td>';
	}

	$detalleInforme .= '</tr>';

	$detalleInforme .= '<tr>';
	$detalleInforme .= '<td style="text-align:center; border: 1px solid;">Conceptos</td>'.$titulosSis.'</tr>';

	
	$i = 0;
	while( $i < count($consulta)) 
	{
		$imprime = false;
		$estiloTexto = ' style="'.$consulta[$i]["estilo"].'; text-align:left;"';
		$estiloNumero = ' style="'.$consulta[$i]["estilo"].'; text-align:right;"';

		if(($formato == 'Det' and $consulta[$i]["detalle"] == 1) or
		($formato == 'Res' and $consulta[$i]["resumen"] == 1))
		{
			$detalleInforme .= '<tr>';
			$detalleInforme .= '<td '.$estiloTexto.' title="'.$consulta[$i]["contenido"].'">'.$consulta[$i]["concepto"].'</td>';
			$imprime = true;
		


			// recorremos cada uno de los periodos  y por cada periodo, recorreremos cada una de las bases de datos
			$inicio = $fechaInicial;
			$anioAnt = date("Y", strtotime($inicio));
			$primerMes = false;

			$variacion = 0;
			while($inicio < $fechaFinal)
			{
				$consolidadoConcepto = 0;
				$consolidadoConceptoVariacion = 0;

				$anioAnt = date("Y", strtotime($inicio));

				foreach ($tipoContabilidad as $tc => $tipoCont) 
				{
					$mes = nombreMes($inicio).'_'.$tipoCont.'_'.substr($inicio,0,4);
					$primerMes = ($inicio == $fechaInicial ? true : false);
					
					$colVariacion = ($parVariacion and 
							(($tipoVariacion == 'Mensual' and $primerMes == false) or 
							$tipoVariacion == 'Anual' )) ? true : false;

					// recorremos cada base de datos
					
					foreach ($sistemaInformacion as $db => $baseDatos) 
					{

						
							$detalleInforme .= '<td '.$estiloNumero.'>'.($MC ? $baseDatos.'_'.$mes.'=' : '').($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' : number_format($consulta[$i][$baseDatos.'_'.$mes],2,'.',',')).'</td>';
						
							

						// al lado de cada periodo, imprimimos el porcentaje vertical si fue solicitado por el usuario

						if(($colPorcentajeVert != '' or $colPorcentajeFormula != ''))
							$detalleInforme .= '<td '.$estiloNumero.'>'.($MC ? 'Porc_'.$baseDatos.'_'.$mes.'=' : '').
								(isset($consulta[$i]['Porc_'.$baseDatos.'_'.$mes]) 
									? ($consulta[$i]["tipoValor"] == 'Titulo' 
										? '&nbsp;' 
										: number_format($consulta[$i]['Porc_'.$baseDatos.'_'.$mes],2,'.',',')).'%' 
									: '&nbsp;')
								.'</td>'; 

						$consolidadoConcepto += $consulta[$i][$baseDatos.'_'.$mes];
					}

					// cada que termina de recorrer las bases de datos, imprime el total consolidado
					if($consolidar)
					{
						$detalleInforme .= '<td '.$estiloNumero.'>'.($MC ? 'Cons_'.$mes : '').($consulta[$i]["tipoValor"] == 'Titulo' 
								? '&nbsp;' 
								: number_format($consulta[$i]['Cons_'.$mes],2,'.',',')).'</td>';

						if($colPorcentajeVert != '' or $colPorcentajeFormula != '')
						{
							if(isset($consulta[$i]['Porc_Cons_'.$mes]))
								$detalleInforme .= '<td '.$estiloNumero.'>'.
									($MC ? 'Porc_Cons_'.$mes.'=' : '').
									($consulta[$i]["tipoValor"] == 'Titulo' 
										? '&nbsp;' 
										: number_format($consulta[$i]['Porc_Cons_'.$mes],2,'.',',').'%' ).
									'</td>'; 
							else
								$detalleInforme .= '<td '.$estiloNumero.'>'.($MC ? 'Porc_Cons_'.$mes.'=' : '').($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' :number_format(0,2,'.',',').'%').'</td>'; 
						}
					}
					

					if($colVariacion )
					{
						// segun el tipo de variacion tomamos un periodo diferente para comparar
						if($tipoVariacion == 'Anual')
							$anioVariacion = date("Y-m-d",strtotime("-1 Year", strtotime($inicio)));
						else
							$anioVariacion = date("Y-m-d",strtotime("-1 Month", strtotime($inicio)));

						$mesVariacion = nombreMes($anioVariacion).'_'.$tipoCont.'_'.substr($anioVariacion,0,4);

						// recorremos cada base de datos
						
						foreach ($sistemaInformacion as $db => $baseDatos) 
						{
							// el mes de variacion solo se imprime cuando es variacion anual, 
							// ya que es el valor del año anterior
							$valorVariacion = 0;
							if($tipoVariacion == 'Anual')
							{		

								if(isset($consulta[$i][$baseDatos.'_'.$mesVariacion]))
									$valorVariacion = $consulta[$i][$baseDatos.'_'.$mesVariacion];
								else
									$valorVariacion = 0;

								$detalleInforme .= '<td '.$estiloNumero.'>'.($MC ? $baseDatos.'_'.$mesVariacion.'=' : '').($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' :number_format($valorVariacion,2,'.',',')).'</td>';

								if(($colPorcentajeVert != '' or $colPorcentajeFormula != '') and isset($consulta[$i]['Porc_'.$baseDatos.'_'.$mesVariacion]))
									$detalleInforme .= '<td '.$estiloNumero.'>'.($MC ? 'Porc_'.$baseDatos.'_'.$mesVariacion.'=' : '').($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' :number_format($consulta[$i]['Porc_'.$baseDatos.'_'.$mesVariacion],2,'.',',').'%').'</td>';
							}

							$consolidadoConceptoVariacion += $consulta[$i][$baseDatos.'_'.$mesVariacion];
						}
						

						// cada que termina de recorrer las bases de datos, imprime el total consolidado
						if($consolidar)
						{
							$detalleInforme .= '<td '.$estiloNumero.'>'.($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' :number_format($consolidadoConceptoVariacion,2,'.',',')).'</td>';
						}
						
						$variacion = $consolidadoConcepto - $consolidadoConceptoVariacion;
						$porcvariacion = (1 - ($consolidadoConcepto / ($consolidadoConceptoVariacion == 0 ? 1 : $consolidadoConceptoVariacion))) * 100;
					}				

					if($colVariacion and $tipoVariacion == 'Mensual')
					{	
						$detalleInforme .= '<td '.$estiloNumero.'>'.($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' :number_format($consulta[$i]["Var_".$mes],2,'.',',')).'</td>';
						$detalleInforme .= '<td '.$estiloNumero.'>'.($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' :number_format($consulta[$i]["PorcVar_".$mes] ,2,'.',',').'%').'</td>';
					}
				}
				//Avanzamos al siguiente mes
				$inicio = date("Y-m-d", strtotime("+1 MONTH", strtotime($inicio)));
			}
			if($colVariacion and $tipoVariacion == 'Anual')
			{	
				$detalleInforme .= '<td '.$estiloNumero.'>'.($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' :number_format($consulta[$i]["Var_".$mes],2,'.',',')).'</td>';
				$detalleInforme .= '<td '.$estiloNumero.'>'.($consulta[$i]["tipoValor"] == 'Titulo' ? '&nbsp;' :number_format($consulta[$i]["PorcVar_".$mes] ,2,'.',',').'%').'</td>';
			}
		}
		$i++;
		$detalleInforme .= '</tr>';
	}				


	echo $detalleInforme. '</table>';
   
	echo ' 			</div>
                </div>
			</div><br><br>';
}


function generarInformeGeneral($idInformeCapa, $sistemaInformacion, $conexionBD, $condicion)
{
	
	// las condiciones que tienen LIKE, vienen con el * en vez del % para que no molesten en la URL
	// aca las convertimos para que la condicion funcione
	$condicion = str_replace('*', '%', $condicion);
	
	// Para los informes generales, consultamos la tabla de Informe relacionada con la tabla de InformeObjeto
	// $datos = DB::table('informe as I')
	// 			->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
	// 			->leftjoin('informeobjeto as Iobj', 'Icap.idInformeCapa', '=', 'Iobj.InformeCapa_idInformeCapa')
	// 			->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, 
	// 				idInformeObjeto, bandaInformeObjeto, nombreInformeObjeto,
	// 				estiloInformeObjeto, EstiloInforme_idEstiloInforme, 
	// 				tipoInformeObjeto, etiquetaInformeObjeto, campoInformeObjeto'))
	// 			->where('Icap.idInformeCapa', '=', $idInformeCapa)
	// 			->get();


	// Lo primero que requerimos es elaborar una consulta a la base de datos
	// con la tabla y los campos que el usuario adiciono a la capa en el 
	// diseñador de informes, para esto consultamos los campos que contienen dicha 
	// informacion filtrando los objetos que son campos de base de datos (no etiquetas, ni 
	// imagenes, etc)
	$campos = DB::table('informe as I')
				->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
				->leftjoin('informeobjeto as Iobj', 'Icap.idInformeCapa', '=', 'Iobj.InformeCapa_idInformeCapa')
				->leftjoin('sistemainformacion as Sinf', 'Icap.SistemaInformacion_idSistemaInformacion', '=', 'Sinf.idSistemaInformacion')
				->select(DB::raw('Icap.tablaInformeCapa,
					Iobj.bandaInformeObjeto, Iobj.campoInformeObjeto,
					Sinf.motorbdSistemaInformacion, Sinf.ipSistemaInformacion, 
					Sinf.puertoSistemaInformacion, Sinf.bdSistemaInformacion, 
					Sinf.usuarioSistemaInformacion, Sinf.claveSistemaInformacion'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->where('Iobj.tipoInformeObjeto', '=', 'CampoClon')
				->get();
				
	//--------------------------------------------
	//
	//  C O N D I C I O N   D E   C O M P A N I A
	//
	//--------------------------------------------
	// a la condicion de la consulta le debemos adicionar el id de la compañia actual
	// para adicionar el id de la compania, primero verificamos si en la tabla existe ese campo
	$consulta = DB::table('information_schema.COLUMNS')
				->select(DB::raw('COLUMN_NAME'))
				->where('TABLE_SCHEMA', '=', get_object_vars($campos[0])["bdSistemaInformacion"])
				->where('TABLE_NAME', '=', get_object_vars($campos[0])["tablaInformeCapa"])
				->where('COLUMN_NAME', 'like', '%idCompania%') 
				->get();

	$datowhere = '';
	// si la tabla tiene campo de id de compania, armamos una condicion con el id de compania de la session actual sino la dejamos en blanco
	if(isset($consulta[0]))
	{	$datowhere = isset(get_object_vars($consulta[0])["COLUMN_NAME"]) 
					? get_object_vars($consulta[0])["COLUMN_NAME"] .' = '. \Session::get("idCompania").' '
					: '';
		$condicion = $condicion . (($condicion != '' and $datowhere != '') ? ' and ' : '') . $datowhere;
	}


	$camposConsulta = '';
	$camposOrden = '';
	$tabla = get_object_vars($campos[0])["bdSistemaInformacion"].'.'.
			 get_object_vars($campos[0])["tablaInformeCapa"];

	for($i = 0; $i < count($campos); $i++) 
	{
	    $registro = get_object_vars($campos[$i]);
	    $camposConsulta .= $registro["campoInformeObjeto"].',';

	    // Si el registro es de una banda de encabezado de grupo, tomamos le campo apra el order by
	    if(substr($registro["bandaInformeObjeto"],0,14) == 'layoutGrupoEnc')
	        $camposOrden .= $registro["campoInformeObjeto"].',';
	}
	$camposConsulta = substr($camposConsulta, 0, strlen($camposConsulta) -1);
	$camposOrden = substr($camposOrden, 0, strlen($camposOrden) -1);
	$sisinfo = get_object_vars($campos[0]);
	$programa = '<?php 
	
	$idInformeCapa = "'.$idInformeCapa.'";
	$camposConsulta = "'.$camposConsulta.'";
	$condicion = "'.$condicion.'";
	$camposOrden = "'.$camposOrden.'";
	
		Config::set( \'database.connections.\'.$sisinfo[\'bdSistemaInformacion\'], array 
	    ( 
	        \'driver\'     =>  $sisinfo[\'motorbdSistemaInformacion\'], 
	        \'host\'       =>  $sisinfo[\'ipSistemaInformacion\'], 
	        \'port\'       =>  $sisinfo[\'puertoSistemaInformacion\'], 
	        \'database\'   =>  $sisinfo[\'bdSistemaInformacion\'], 
	        \'username\'   =>  $sisinfo[\'usuarioSistemaInformacion\'], 
	        \'password\'   =>  $sisinfo[\'claveSistemaInformacion\'], 
	        \'charset\'    =>  \'utf8\', 
	        \'collation\'  =>  \'utf8_unicode_ci\', 
	        \'prefix\'     =>  \'\',
	        \'strict\'    => false,
	        \'options\'   => [ 
	        				\PDO::ATTR_EMULATE_PREPARES => true
	        				]
	    )); ';

	$programa .= '
    	$conexion = DB::connection($sisinfo[\'bdSistemaInformacion\'])->getDatabaseName();';
    
    $programa .= '
    	$consulta = DB::connection($sisinfo[\'bdSistemaInformacion\'])->select(
    			"SELECT $camposConsulta 
				FROM $tabla ".
				($condicion != \'\' ? "WHERE ".$condicion : "").
				($camposOrden != \'\' ? "ORDER BY  ".$camposOrden : ""));';

	
	$programa .= '			
		// por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
		$valores = array();
		for($i = 0; $i < count($consulta); $i++) 
		{
		    $valores[] = get_object_vars($consulta[$i]);
		} 
		';

	
		// Consultamos solo las bandas de Grupo para armar un codigo PHP de rompimiento de control
		$campos = DB::table('informecapa as Icap')
					->leftjoin('informeobjeto as Iobj', 'Icap.idInformeCapa', '=', 'Iobj.InformeCapa_idInformeCapa')
					->select(DB::raw('Icap.idInformeCapa, Iobj.bandaInformeObjeto, Iobj.campoInformeObjeto, Iobj.tipoInformeObjeto, idInformeObjeto'))
					->where('Icap.idInformeCapa', '=', $idInformeCapa)
					// ->where('Iobj.bandaInformeObjeto', 'like', 'layoutGrupoEnc%')
					// ->where('tipoInformeObjeto' , '=', 'campoClon')
					// ->groupby('bandaInformeObjeto')
					// ->groupby('tipoInformeObjeto')
					->orderby('idInformeObjeto')
					->get();

	


	$grupos = array();
	for($i = 0; $i < count($campos); $i++) 
	{
	    $grupos[] = get_object_vars($campos[$i]);
	}


	// variable para probar el codigo dinamico, si es 1, es para que se imprima en pantalla con saltos de linea, si es 2, es para que no tenga los saltos y se ejecute el eval
	$modo = 2;

	$posGru = 0;
	$condicionCiclo = '$pos < count($valores)';
	$cierreCiclo = '';

	$programa .= 	'$estructura = \'<html>
										<head>
										<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
										</head>
										<body>\';

					$estructura .= \'<table style="border: 1px solid gray;" width="100%" cellpadding="1" cellspacing="1">\';

					$pos = 0;

					$estructura .= imprimirBandaDetalle(\'layoutEncabezadoContenedor1\', $idInformeCapa, $valores[$pos], $pos);
					
					while('.$condicionCiclo.')
					{';



	// print_r($grupos);
	while($posGru < count($grupos))
	{
		$bandaAnt = $grupos[$posGru]["bandaInformeObjeto"];

		// creamos condiciones de rompimiento solo si la banda es de grupo encabezado
		// y tiene campos de base de datos (CampoClon)
		$rompe = 0;
		if(substr($grupos[$posGru]["bandaInformeObjeto"],0,14) == 'layoutGrupoEnc' and 
			$grupos[$posGru]["tipoInformeObjeto"] == 'CampoClon')
		{
			$rompe = 1;
			$condicionCiclo .= ' and $'.$grupos[$posGru]["campoInformeObjeto"].'Ant == $valores[$pos]["'.$grupos[$posGru]["campoInformeObjeto"].'"]';

			$programa .= '
			    	$estructura .= imprimirBandaDetalle(\''.$bandaAnt.'\', $idInformeCapa, $valores[$pos], $pos);
				';
			
			$programa .= 	
				'$'.$grupos[$posGru]["campoInformeObjeto"].'Ant = $valores[$pos]["'.$grupos[$posGru]["campoInformeObjeto"].'"];
				
			
			

			while('.$condicionCiclo.')
			{
				';
		}

		// while($posGru < count($grupos) and $bandaAnt == $grupos[$posGru]["bandaInformeObjeto"])
		// {
			

			// si es es eltimo grupo, debe tener el incremento de la variable $pos del ciclo
			if(($posGru+1) == count($grupos))
			{
			    $programa .= '
			    	
			    	$estructura .= imprimirBandaDetalle(\''.$bandaAnt.'\', $idInformeCapa, $valores[$pos], $pos);
					$pos++;
				';
			}
							
			$posGru++;
		// }
		$cierreCiclo .= ($rompe == 1) ? "}  \n" : "";
	}
	$programa .= $cierreCiclo.'
				} 
				$estructura .= \'</body>
								</html>\';
				echo $estructura;
				?>';
	
	$arch = fopen("/var/www/html/informe.php", "w");
	fputs ($arch, $programa);
	fclose($arch);
	include "/var/www/html/informe.php";

	return ;

//

	// Realizamos la misma consulta pero esta vez solo nos interesa saber que bandas tiene
	// la capa, por ejemplo: Encabezado, Detalle, Pie, para esto agrupamos la consulta para que solo nos arroje estos nombres sin repetir
	$campos = DB::table('informecapa as Icap')
				->leftjoin('informeobjeto as Iobj', 'Icap.idInformeCapa', '=', 'Iobj.InformeCapa_idInformeCapa')
				->select(DB::raw('Icap.idInformeCapa, Iobj.bandaInformeObjeto'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->groupby('bandaInformeObjeto')
				->orderby('idInformeObjeto')
				->get();

	

	// // recorremos la lista de bandas, si es una banda de detalle se trata diferente a las 
	// // demas por ser de registros repetitivos, las demas bandas son de registro unico
	// // por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
	// $registro = array();
	// for($i = 0; $i < count($campos); $i++) 
	// {
	//     $registro = get_object_vars($campos[$i]);
	//     // si el nombre de la banda contiene la palabra Detalle, ejecutamos un proceso especial
	//     // de lo contrario (las demas bandas) ejecutamos el proceso simple
	//     if($registro["bandaInformeObjeto"] == 'layoutDetalleContenedor1')
	//     	$estructura .= imprimirBandaDetalle('Detalle', $registro["idInformeCapa"], $valores);
	//     else
	//     	$estructura .= imprimirBandaSencilla('Encabezado', $registro["idInformeCapa"]);

	// }

	// Luego de que tenemos los datos consultados, debemos generar el informe como esta en el diseñador
	// Para esto vamos recorriendo cada capa, simplemente poniendo los objetos que esta 
	// tiene, con el estilo y posicion que estaban en el diseñador, teniendo en cuenta que si 
	// son de tipo campo, cambiamos el nombre del campo por el valor que este tiene en la 
	// base de datos
	
	echo $estructura;

}



function nombreMes($fecha)
{
    $mes = date("m", strtotime($fecha));
    switch($mes) 
    {
	    case '01':
	        $mes = 'Enero';
	        break;
	    case '02':
	        $mes = 'Febrero';
	        break;
	    case '03':
	        $mes = 'Marzo';
	        break;
	    case '04':
	        $mes = 'Abril';
	        break;
	    case '05':
	        $mes = 'Mayo';
	        break;
	    case '06':
	        $mes = 'Junio';
	        break;
	    case '07':
	        $mes = 'Julio';
	        break;
	    case '08':
	        $mes = 'Agosto';
	        break;
	    case '09':
	        $mes = 'Septiembre';
	        break;
	    case '10':
	        $mes = 'Octubre';
	        break;
	    case '11':
	        $mes = 'Noviembre';
	        break;
	    case '12':
	        $mes = 'Diciembre';
	        break;

	    default:
	        $mes = 'Enero';
    }  
    return $mes;
}

function estiloObjeto($objetos)
{
	$estilo= ''; //$objetos["estiloInformeObjeto"] 
	if($objetos["idEstiloInforme"] != null)
	{
		$estilo .=   
	        'background-color:'.$objetos["colorFondoEstiloInforme"].';'.
	        'border:'.($objetos["colorBordeEstiloInforme"] != '' ? ' solid 1px ' : '').' '.$objetos["colorBordeEstiloInforme"].';'.
	        'color: '.$objetos["colorTextoEstiloInforme"].';'.
	        'font-family: '.$objetos["fuenteTextoEstiloInforme"].';'.
	        'font-size: '.$objetos["tamañoTextoEstiloInforme"].';'.
	        'font-weight: '.($objetos["negrillaEstiloInforme"] == 1 ? 'bold' : 'normal').';'.
	        'font-style: '.($objetos["italicaEstiloInforme"] == 1 ? 'italic' : '').';'.
	        'text-decoration: '.($objetos["subrayadoEstiloInforme"] == 1 ? 'underline' : '').';';
	}
    return $estilo;
}

function estiloInforme($consulta, $pos)
{

	// verificamos si la posicion es par o impar
	if(fmod($pos, 2) == 0) 
		$fila = 'Par';
	else
		$fila = 'Impar';

	$estilo= '';
	if($consulta["idInformePropiedad"] != null)
	{
		$estilo .=   
	        'background-color:'.$consulta["colorFondo".$fila."InformePropiedad"].';'.
	        'border:'.($consulta["colorBorde".$fila."InformePropiedad"] != '' ? ' solid 1px ' : '').' '.$consulta["colorBorde".$fila."InformePropiedad"].';'.
	        'color: '.$consulta["colorTexto".$fila."InformePropiedad"].';'.
	        'font-family: '.$consulta["fuenteTexto".$fila."InformePropiedad"].';'.
	        'font-size: '.$consulta["tamañoTexto".$fila."InformePropiedad"].';'.
	        'font-weight: '.($consulta["negrilla".$fila."InformePropiedad"] == 1 ? 'bold' : 'normal').';'.
	        'font-style: '.($consulta["italica".$fila."InformePropiedad"] == 1 ? 'italic' : '').';'.
	        'text-decoration: '.($consulta["subrayado".$fila."InformePropiedad"] == 1 ? 'underline' : '').';';
	}
    return $estilo;
}


function imprimirBandaSencilla($tipoBanda, $idInformeCapa)
{

	$datos = DB::select(
			'Select I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, 
					Iobj.idInformeObjeto, Iobj.bandaInformeObjeto, Iobj.nombreInformeObjeto,
					Iobj.estiloInformeObjeto, Iobj.EstiloInforme_idEstiloInforme, 
					Iobj.tipoInformeObjeto, Iobj.etiquetaInformeObjeto, 
					Iobj.campoInformeObjeto,
					Einf.idEstiloInforme, Einf.colorFondoEstiloInforme, Einf.colorBordeEstiloInforme, 
					Einf.colorTextoEstiloInforme, Einf.fuenteTextoEstiloInforme, 
					Einf.tamañoTextoEstiloInforme, Einf.negrillaEstiloInforme, 
					Einf.italicaEstiloInforme, Einf.subrayadoEstiloInforme
			From informe as I 
			left join informecapa as Icap 
				on I.idInforme = Icap.Informe_idInforme
			left join informeobjeto as Iobj 
				on Icap.idInformeCapa = Iobj.InformeCapa_idInformeCapa
			left join estiloinforme as Einf 
				on Iobj.EstiloInforme_idEstiloInforme = Einf.idEstiloInforme
			where 	Icap.idInformeCapa = '. $idInformeCapa. ' and 
			 		Iobj.bandaInformeObjeto like  "%'.$tipoBanda.'%"
			order by bandaInformeObjeto');

	// por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
	$objetos = array();
	for($i = 0; $i < count($datos); $i++) 
	{
	    $objetos[] = get_object_vars($datos[$i]);
	}


	// Todo el proceso va a crear un codigo en PHP dentro de la variable $estructura, simulando el programa que haría el desarrollador para generar el informe, finalmente esta variable se imprime con un ECHO

	// recorremos los datos de la capa actual haciendo un rompimiento por cada 
	// Banda (encabezado, detalle, pie, etc.)
	$estructura = '';
	$reg = 0;
	while($reg < count($objetos))
	{
		$bandaAnt = $objetos[$reg]["bandaInformeObjeto"];
		// $estructura.=
  //               '<div 	id="'.$objetos[$reg]["bandaInformeObjeto"].'" 
  //               		style="'.$objetos[$reg]["estiloInformeObjeto"].'">';

		$estructura .= '<tr style="'.$objetos[$reg]["estiloInformeObjeto"].'">';                		
                
		while($reg < count($objetos) and $bandaAnt == $objetos[$reg]["bandaInformeObjeto"])
		{
			
            // $estructura .= 
            //     	'<div style="'.estiloObjeto($objetos[$reg]).'" '.
            //             '<div  style="width: 100%; height: 100%;">'.

            //             // ACA PREGUNTA SI ES CAMPO PARA TOMAR EL DATO DE LA CONSULTA
            //             	$objetos[$reg]["campoInformeObjeto"].
            //             '</div>'.
            //         '</div>';

            $estructura .= 
                '<td style="'.estiloObjeto($objetos[$reg]).'">'.
                	$objetos[$reg]["campoInformeObjeto"].
                '</td>';

			$reg++;
		}
		$estructura .= '</tr>';
	}
	return $estructura;
}


function imprimirBandaDetalle($tipoBanda, $idInformeCapa, $valores, $pos)
{

	$datos = DB::select(
			'Select I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, 
					Iobj.idInformeObjeto, Iobj.bandaInformeObjeto, Iobj.nombreInformeObjeto,
					Iobj.estiloInformeObjeto, Iobj.EstiloInforme_idEstiloInforme, 
					Iobj.tipoInformeObjeto, Iobj.etiquetaInformeObjeto, 
					Iobj.campoInformeObjeto,
					Einf.idEstiloInforme, Einf.colorFondoEstiloInforme, Einf.colorBordeEstiloInforme, 
					Einf.colorTextoEstiloInforme, Einf.fuenteTextoEstiloInforme, 
					Einf.tamañoTextoEstiloInforme, Einf.negrillaEstiloInforme, 
					Einf.italicaEstiloInforme, Einf.subrayadoEstiloInforme,
					Ipro.idInformePropiedad, Ipro.Informe_idInforme, Ipro.colorFondoParInformePropiedad, 
					Ipro.colorFondoImparInformePropiedad, Ipro.colorBordeParInformePropiedad, 
					Ipro.colorBordeImparInformePropiedad, Ipro.colorTextoParInformePropiedad, 
					Ipro.colorTextoImparInformePropiedad, Ipro.fuenteTextoParInformePropiedad, 
					Ipro.fuenteTextoImparInformePropiedad, Ipro.tamañoTextoParInformePropiedad, 
					Ipro.tamañoTextoImparInformePropiedad, Ipro.negrillaParInformePropiedad, 
					Ipro.negrillaImparInformePropiedad, Ipro.italicaParInformePropiedad, 
					Ipro.italicaImparInformePropiedad, Ipro.subrayadoParInformePropiedad, Ipro.subrayadoImparInformePropiedad
			From informe as I 
			left join informecapa as Icap 
				on I.idInforme = Icap.Informe_idInforme
			left join informeobjeto as Iobj 
				on Icap.idInformeCapa = Iobj.InformeCapa_idInformeCapa
			left join estiloinforme as Einf 
				on Iobj.EstiloInforme_idEstiloInforme = Einf.idEstiloInforme
			left join informepropiedad as Ipro 
				on I.idInforme = Ipro.Informe_idInforme
			where 	Icap.idInformeCapa = '. $idInformeCapa. ' and 
			 		Iobj.bandaInformeObjeto =  "'.$tipoBanda.'"
			order by bandaInformeObjeto');


	// por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
	$objetos = array();
	for($i = 0; $i < count($datos); $i++) 
	{
	    $objetos[] = get_object_vars($datos[$i]);
	}

	if(count($objetos) == 0)
		return '';
	// recorremos los datos de la capa actual haciendo un rompimiento por cada 
	// Banda (encabezado, detalle, pie, etc.)
	//$estructura = '';
	
	$estructura = '';
	// while($pos < count($valores))
	// {
		
		// $estructura .= '<div id="'.$objetos[0]["bandaInformeObjeto"].'" style="height:30px;">';
		$topAnt = 0;
		$estructura .= '<tr style="'.estiloInforme($objetos[0], $pos).'">';
        $reg = 0;      
		while($reg < count($objetos) )
		{	
			if(posicionObjeto($objetos[$reg]["estiloInformeObjeto"]) >= ($topAnt + 10))
				$estructura .= '</tr>';				

            $estructura .= 
                        '<td style="'.estiloObjeto($objetos[$reg]).'">'.
                        	(
                        		($objetos[$reg]["tipoInformeObjeto"] == 'CampoClon')
	                        		? $valores[$objetos[$reg]["campoInformeObjeto"]]
	                        		: $objetos[$reg]["campoInformeObjeto"]
                        	).
                        '</td>';

            
			$topAnt = posicionObjeto($objetos[$reg]["estiloInformeObjeto"]);
			$reg++;
		}
		// $estructura .= '</div>';
		$estructura .= '</tr>';
		// $pos++;
	// }
	return $estructura;
}

function posicionObjeto($estilo)
{
	$postop = strpos($estilo, 'top:');
	$top = $postop != -1 ? substr($estilo, $postop+4, strpos(substr($estilo, $postop), 'px') ) : '';
	return (int)str_replace('px', '', $top);
}
?>