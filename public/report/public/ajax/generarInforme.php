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

// sistemaInformacion. son los nombres de las bases de datos que se van a consolidar en el informe (solo aplica si dichas bases de datos tienen la misma estructura en als tablas con las que se diseño el informe)
$sistemaInformacion = array('Iblu', 'Extiblu');

// Rango de fechas. indica la fecha inicial y la fecha final de los datos a consultar
// Otros parámetros. son otras propiedades para mejoramiento del informe, el formato de numeros, si se asicionan columnas de consolidados o porcentaje, si es informe detallado o resumido, etc 

// $condicionMeses


// Primero consultamos las capas del informe, con el fin de recorrerlas, ya que cada capa 
// puede ser de diferente tipo (1:General, 2:Contable), por lo tanto llaman a diferentes procesos

$capas = DB::table('informe as I')
			->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
			->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa'))
			->where('I.idInforme', '=', $idInforme)
			->get();

for($c = 0; $c < count($capas); $c++) 
{
    $capa = get_object_vars($capas[$c]);

    switch ($capa["tipoInformeCapa"]) {
    	// Informe General
    	case '1':
    		generarInformeGeneral($capa["idInformeCapa"]);
    		break;

    	// Informe Contable
    	case '2':
    		generarInformeContable($capa["idInformeCapa"]);
    		break;
    	
    	default:
    		# code...
    		break;
    }
}


function generarInformeContable($idInformeCapa)
{

	// Para los informes contables, consultamos la tabla de Informe relacionada con la tabla de InformeConcepto
	$datos = DB::table('informe as I')
				->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
				->leftjoin('informeconcepto as Icon', 'Icap.idInformeCapa', '=', 'Icon.InformeCapa_idInformeCapa')
				->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, nombreInformeConcepto, tipoValorInformeConcepto, tipoMovimientoInformeConcepto, valorInformeConcepto'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->get();



	//*******************************************
	// orden de los ciclos
	// 1. Bases de datos
	// 2. Concepto
	//*******************************************


	foreach ($sistemaInformacion as $db => $baseDatos) 
	{
		

		//  Cada registro que recorremos es un concepto contable diferente
		$consultaConcepto = '';
		$informecapa = array();
		for($i = 0; $i < count($datos); $i++) 
		{
		    $informecapa = get_object_vars($datos[$i]);
			

			


			// inicializamos las variables apara almacenar las condiciones de cuentas
			$condicionCuenta = '';
			$cuentaRango = '';
			$cuentaIndividual = '';
			$cuentaExcluida = '';

		    if($informecapa["tipoValorInformeConcepto"] == 'Puc')
		    {
		    	//*************************************************************************
				// PASO 1: Crear un condicion con los numeros de cuenta
				//*************************************************************************
				// Se deben tener en cuenta 2 formatos de cuentas diferentes:
				// 1. Rango de cuentas, separadas por guion, ejemplo: 1001-1001999999
				// 2. Cuentas individuales, simplemente separadas por coma, emeplo: 24080101, 24080102
				// 3. Cuantas Excluídas, son numeros de cuenta individuales pero comienzan por una letra x (mayuscula o minuscula), ejemplo: x10010501

		    	// lo primero es convertir el string de cuentas en un array explotandolo por comas
		    	$cuentas = explode(',', $informecapa["valorInformeConcepto"]);
		    	//print_r($cuentas);

		    	foreach ($cuentas as $pos => $valorCuenta) 
		    	{
		    		// 1. Rangos de Cuentas
		    		if(strpos( $valorCuenta, '-') !== false)
		    		{
		    			$cuentaRango .= ($cuentaRango != '' ? ' AND ' : ''). "(numeroCuentaContable BETWEEN '". str_replace('-', "' AND '", $valorCuenta)."')";
		    		}
		    		else if(strpos(strtolower($valorCuenta),'x') !== false)
		    		{
		    			$cuentaExcluida .= ($cuentaExcluida != '' ? ' , ' : '').  "'".str_replace('x', '', $valorCuenta)."'";
		    		}
		    		else
		    		{
		    			$cuentaIndividual .= ($cuentaIndividual != '' ? ' , ' : ''). "'".$valorCuenta."'";
		    		}

		    	}

		    }

			$cuentaIndividual = ($cuentaIndividual != '' ? 'numeroCuentaContable IN ('.$cuentaIndividual.')' : '');
			$cuentaExcluida = ($cuentaExcluida != '' ? 'numeroCuentaContable NOT IN ('.$cuentaExcluida.')' : '');
			
			// ahora juntamos todas las condiciones en una sola
			$condicionCuenta = 	($cuentaRango != '' ? $cuentaRango : '').
									(($cuentaRango != '' and $cuentaIndividual != '') ? ' AND ' : '').
								($cuentaIndividual != '' ? $cuentaIndividual : '');

			$condicionCuenta .=	(($condicionCuenta != '' and $cuentaExcluida != '') ? ' AND ' : '').
								($cuentaExcluida != '' ? $cuentaExcluida : '');

			

			$consultaConcepto = "SELECT 
									'".$baseDatos."' as BaseDatos,
									'".$informecapa["nombreInformeConcepto"]."' as concepto, 
									if(fechaInicialPeriodo = '2016-01-01', (CON.saldoFinalDebitosContabilidad - CON.saldoFinalCreditosContabilidad), 0) as Enero,
									if(fechaInicialPeriodo = '2016-02-01', (CON.saldoFinalDebitosContabilidad - CON.saldoFinalCreditosContabilidad), 0) as Febrero,
									if(fechaInicialPeriodo = '2016-01-01', (CON.saldoFinalDebitosContabilidad - CON.saldoFinalCreditosContabilidad), 0) as Marzo
								FROM ".$baseDatos.".Contabilidad CON 
									left join ".$baseDatos.".CuentaContable CC
										on CON.CuentaContable_idCuentaContable = CC.idCuentaContable
									left join ".$baseDatos.".Periodo P 
										on CON.Periodo_idPeriodo = P.idPeriodo 
									left join ".$baseDatos.".Ano A 
										on P.Ano_idAno = A.idAno
								Where fechaInicialPeriodo like '2016%' and ".$condicionCuenta." and (CON.saldoFinalDebitosContabilidad - CON.saldoFinalCreditosContabilidad) <> 0 and esAfectableCuentaContable = 1
								Group By concepto

								UNION 

								";

			echo $consultaConcepto.'<br>';

		}

	}
}

function generarInformeGeneral($idInformeCapa)
{
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
	// con la tabla y los campos que el usaurio adiciono a la capa en el 
	// diseñador de informes, para esto consultamos los campos que contienen dicha 
	// informacion filtrando los objetos que son campos de base de datos (no etiquetas, ni 
	// imagenes, etc)
	$campos = DB::table('informe as I')
				->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
				->leftjoin('informeobjeto as Iobj', 'Icap.idInformeCapa', '=', 'Iobj.InformeCapa_idInformeCapa')
				->leftjoin('sistemainformacion as Sinf', 'Icap.SistemaInformacion_idSistemaInformacion', '=', 'Sinf.idSistemaInformacion')
				->select(DB::raw('Sinf.bdSistemaInformacion, Icap.tablaInformeCapa,
					Iobj.campoInformeObjeto'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->where('Iobj.tipoInformeObjeto', '=', 'CampoClon')
				->get();
				
				

	//  Cada registro que recorremos es un concepto contable diferente
	$camposConsulta = '';
	$tabla = get_object_vars($campos[0])["bdSistemaInformacion"].'.'.
			 get_object_vars($campos[0])["tablaInformeCapa"];

	for($i = 0; $i < count($campos); $i++) 
	{
	    $registro = get_object_vars($campos[$i]);
	    $camposConsulta .= $registro["campoInformeObjeto"].',';
	}
	$camposConsulta = substr($camposConsulta, 0, strlen($camposConsulta) -1);

	$consulta = DB::select(
				"SELECT $camposConsulta 
				FROM $tabla");

	// por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
	$valores = array();
	for($i = 0; $i < count($consulta); $i++) 
	{
	    $valores[] = get_object_vars($consulta[$i]);
	}


	// Realizamos la misma consulta pero esta vez solo nos interesa saber que bandas tiene
	// la capa, por ejemplo: Encabezado, Detalle, Pie, para esto agrupamos la consulta apra que solo nos arroje estos nombres sin repetir
	$campos = DB::table('informecapa as Icap')
				->leftjoin('informeobjeto as Iobj', 'Icap.idInformeCapa', '=', 'Iobj.InformeCapa_idInformeCapa')
				->select(DB::raw('Iobj.bandaInformeObjeto'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->groupby('bandaInformeObjeto')
				->get();

	// recorremos la lista de bandas, si es una banda de detalle se trata diferente a las 
	// demas por ser de registros repetitivos, las demas bandas son de registro unico
	// por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
	$registro = array();
	for($i = 0; $i < count($datos); $i++) 
	{
	    $registro = get_object_vars($datos[$i]);
	    // si el nombre de la banda contiene la palabra Detalle, ejecutamos un proceso especial
	    // de lo contrario (las demas bandas) ejecutamos el proceso simple
	    if($registro["bandaInformeObjeto"] == 'layoutDetalleContenedor1')
	    	imprimirBandaDetalle('Detalle');
	    else
	    	imprimirBandaSencilla('Encabezado');
			

	}

	// Luego de que tenemos los datos consultados, debemos generar el informe como esta en el diseñador
	// Para esto vamos recorriendo cada capa, simplemente poniendo los objetos que esta 
	// tiene, con el estilo y posicion que estaban en el diseñador, teniendo en cuenta que si 
	// son de tipo campo, cambiamos el nombre del campo por el valor que este tiene en la 
	// base de datos
	
	echo $estructura;
	

    

        
       


}

function estilo($objetos)
{
	$estilo =   $objetos["estiloInformeObjeto"] .
        'background-color:'.$objetos["colorFondoEstiloInforme"].';'.
        'border: solid 1px '.$objetos["colorBordeEstiloInforme"].';'.
        'color: '.$objetos["colorTextoEstiloInforme"].';'.
        'font-family: '.$objetos["fuenteTextoEstiloInforme"].';'.
        'font-size: '.$objetos["tamañoTextoEstiloInforme"].';'.
        'font-weight: '.($objetos["negrillaEstiloInforme"] == 1 ? 'bold' : 'normal').';'.
        'font-style: '.($objetos["italicaEstiloInforme"] == 1 ? 'italic' : '').';'.
        'text-decoration: '.($objetos["subrayadoEstiloInforme"] == 1 ? 'underline' : '').';';
    return $estilo;
}

function imprimirBandaSencilla($tipoBanda)
{
	$datos = DB::table('informe as I')
				->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
				->leftjoin('informeobjeto as Iobj', 'Icap.idInformeCapa', '=', 'Iobj.InformeCapa_idInformeCapa')
				->leftjoin('estiloinforme as Einf', 'Iobj.EstiloInforme_idEstiloInforme', '=', 'Einf.idEstiloInforme')
				->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, 
					Iobj.idInformeObjeto, Iobj.bandaInformeObjeto, Iobj.nombreInformeObjeto,
					Iobj.estiloInformeObjeto, Iobj.EstiloInforme_idEstiloInforme, 
					Iobj.tipoInformeObjeto, Iobj.etiquetaInformeObjeto, 
					Iobj.campoInformeObjeto,
					Einf.colorFondoEstiloInforme, Einf.colorBordeEstiloInforme, 
					Einf.colorTextoEstiloInforme, Einf.fuenteTextoEstiloInforme, 
					Einf.tamañoTextoEstiloInforme, Einf.negrillaEstiloInforme, 
					Einf.italicaEstiloInforme, Einf.subrayadoEstiloInforme
					'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->where('Iobj.bandaInformeObjeto', 'like', "'%".$tipoBanda."%'")
				->orderby('bandaInformeObjeto')
				->get();

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
		$estructura.=
                '<div 	id="'.$objetos[$reg]["bandaInformeObjeto"].'" 
                		style="'.$objetos[$reg]["estiloInformeObjeto"].'">';
                
		while($reg < count($objetos) and $bandaAnt == $objetos[$reg]["bandaInformeObjeto"])
		{
			
            $estructura .= 
                	'<div style="'.estilo($objetos[$reg]).'" '.
                        '<div  style="width: 100%; height: 100%;">'.

                        // ACA PREGUNTA SI ES CAMPO PARA TOMAR EL DATO DE LA CONSULTA
                        	$objetos[$reg]["campoInformeObjeto"].
                        '</div>'.
                    '</div>';
			$reg++;
		}
		$estructura .= '</div>';
	}
	return $estructura;
}


function imprimirBandaDetalle($tipoBanda)
{

	$datos = DB::table('informe as I')
				->leftjoin('informecapa as Icap', 'I.idInforme', '=', 'Icap.Informe_idInforme')
				->leftjoin('informeobjeto as Iobj', 'Icap.idInformeCapa', '=', 'Iobj.InformeCapa_idInformeCapa')
				->leftjoin('estiloinforme as Einf', 'Iobj.EstiloInforme_idEstiloInforme', '=', 'Einf.idEstiloInforme')
				->select(DB::raw('I.idInforme, Icap.idInformeCapa, Icap.tipoInformeCapa, 
					Iobj.idInformeObjeto, Iobj.bandaInformeObjeto, Iobj.nombreInformeObjeto,
					Iobj.estiloInformeObjeto, Iobj.EstiloInforme_idEstiloInforme, 
					Iobj.tipoInformeObjeto, Iobj.etiquetaInformeObjeto, 
					Iobj.campoInformeObjeto,
					Einf.colorFondoEstiloInforme, Einf.colorBordeEstiloInforme, 
					Einf.colorTextoEstiloInforme, Einf.fuenteTextoEstiloInforme, 
					Einf.tamañoTextoEstiloInforme, Einf.negrillaEstiloInforme, 
					Einf.italicaEstiloInforme, Einf.subrayadoEstiloInforme
					'))
				->where('Icap.idInformeCapa', '=', $idInformeCapa)
				->where('Iobj.bandaInformeObjeto', 'like', "'%".$tipoBanda."%'")
				->orderby('bandaInformeObjeto')
				->get();

	// por facilidad de manejo, convertimos el stdObject devuelto por la consulta en un array
	$objetos = array();
	for($i = 0; $i < count($datos); $i++) 
	{
	    $objetos[] = get_object_vars($datos[$i]);
	}

	// recorremos los datos de la capa actual haciendo un rompimiento por cada 
	// Banda (encabezado, detalle, pie, etc.)
	//$estructura = '';
	$pos = 0;
	while($pos < count($valores))
	{
		
		$estructura .= '<div id="'.$objetos[0]["bandaInformeObjeto"].'" style="height:30px;">';
        $reg = 0;      
		while($reg < count($objetos) )
		{
            $estructura .= 
                	'<div style="'.estilo($objetos[$reg]).'" >'.
                        '<div  style="width: 100%; height: 100%;">'.
                        	$valores[$pos][$objetos[$reg]["campoInformeObjeto"]].
                        '</div>'.
                    '</div>';
			$reg++;
		}
		$estructura .= '</div>';
		$pos++;
	}
	return $estructura;
}

/*

$estructura.='
	
  echo  \'
  
<link rel="stylesheet" href="../css/uniform.agent.css" type="text/css" media="screen">
	<link href="../estilos.css" rel="stylesheet" type="text/css" />
	<script src="../js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="../js/jquery.uniform.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" charset="utf-8">
		$(function(){
			$("input, textarea, select, button").uniform();
		});
		</script>
	<link href="http://fonts.googleapis.com/css?family=Oxygen" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Arimo" rel="stylesheet" type="text/css">
	<style>
    
     </style>\';';
    
$estructura.='
    require_once \'../clases/informe.class.php\';';
    
    //Instanciamos la clase
$estructura.='
    $Informe = new Informe();
    
    $detalle = $Informe->ConsultarVistaInformeDetalle("idInforme = '.$id.'", "numeroInformeDetalle ASC");';
    
  $estructura.='  
    $campoInformeDetalle = \'\';
    $totaldetalle = count($detalle);
    for($i=0; $i < $totaldetalle; $i++)
    {
        $campoInformeDetalle .= $detalle[$i]["campoInformeDetalle"]. \', \';
        
        
    }';
    //Le quitamos la última coma a la consulta que hicimos en la base de datos

$estructura.='
    $campoInformeDetalle = substr($campoInformeDetalle, 0 , strlen($campoInformeDetalle)-2);
    require_once\'../clases/db.class.php\'; 
    require_once\'../clases/conf.class.php\';
    
    $orden = \'\';

    for($campo = 0; $campo < $totaldetalle; $campo++)
    {
        if($detalle[$campo]["ordenInformeDetalle"] == \'1\')
        {
            $orden .= $detalle[$campo]["campoInformeDetalle"] .\', \';
        }
    }
    if($orden != \'\')
        $orden = " order by ".substr($orden, 0 , strlen($orden)-2);
    
    $sql =  "select ".$campoInformeDetalle. " from ".$detalle[0]["consultaInforme"] . " '.$condicion.' ".$orden. " ";
    
    $bd = Db::getInstance();
    $query = $bd->ejecutar($sql);
    $datos = array();

    $i = 0;

    if ($query)     
    {
        while ($objetos = $bd->obtener_fila($query, 0)) 
        {
            $datos[$i] = $objetos;
            $i++;
        }
    }';

    

$estructura.='  
    if(count($datos) == 0)
    {
        echo \'<h1>No existen datos para su consulta</h1>\';
        return;
    }
    
    echo "<html>
    <head>
        <title>Dise&ntilde;o Informes</title>
        
        <style type=\"text/css\">
	
            H1.SaltoDePagina
            {
                PAGE-BREAK-AFTER: always
            }
        </style>
    
    </head>
    <body>";

    
      
    $reg=0;
    $linea = 1;
    $totalreg = count($datos);';

    
    require_once '../clases/informe.class.php';
    $Informe = new Informe();
    $detalle = $Informe->ConsultarVistaInformeDetalle("idInforme = $id", "numeroInformeDetalle ASC");
    $totaldetalle = count($detalle);
    
        
    $totalgrupos = 0;   
    $totalcolumnas = 0;
    for ($campo3 = 0; $campo3 < $totaldetalle; $campo3++)
    {   
        if($detalle[$campo3]["grupoInformeDetalle"] == '1' )
        {    
            $totalgrupos++;
        }    

        if($detalle[$campo3]["ocultoInformeDetalle"] == '0' )
        {   
            $totalcolumnas++;
        }
        
        $estructura.='
            
        $SumaTotal["'.$detalle[$campo3]["campoInformeDetalle"].'"] = 0;
        $ConteoTotal["'.$detalle[$campo3]["campoInformeDetalle"].'"] = 0;
        $PromedioTotal["'.$detalle[$campo3]["campoInformeDetalle"].'"] = 0;
        $NingunaTotal["'.$detalle[$campo3]["campoInformeDetalle"].'"] = 0;';
    }
    
    $estructura .= 'Encabezado($detalle);
        $linea+=3;';
    
       
       
       
    $estructura .='     
    while($reg < $totalreg)
    {';
    
        
       
        
        for($campo = 0; $campo < $totaldetalle; $campo++)
        {
            if($detalle[$campo]["grupoInformeDetalle"] == '1' )
            {
                
        
                $estructura .= '
                    $total= $Informe->ConsultarVistaInformeTotal("idInforme = '.$id.' and columnaInformeTotal = \''.$detalle[$campo]["campoInformeDetalle"].'\'") ;
                        //print_r($total);';
                
                $estructura .= '
                    $'.$detalle[$campo]["campoInformeDetalle"].'Anterior = $datos[$reg][$detalle['.$campo.']["campoInformeDetalle"]];
                    $'.$detalle[$campo]["campoInformeDetalle"].'TituloEnc = isset($total[0]["tituloEncabezadoInformeTotal"]) ? $total[0]["tituloEncabezadoInformeTotal"] : \'\';
                    $'.$detalle[$campo]["campoInformeDetalle"].'TituloTot = isset($total[0]["tituloTotalInformeTotal"]) ? $total[0]["tituloTotalInformeTotal"] : \'\';
                    echo \'<tr><td colspan="'.($totalcolumnas+1).'">\'.$'.$detalle[$campo]["campoInformeDetalle"].'TituloEnc.$'.$detalle[$campo]["campoInformeDetalle"].'Anterior.\'</td></tr>\';
                    $linea++;';
                
                
                if(($campo+1) == $totalgrupos)
                {
                    $estructura .= '
                    echo \'<tr><td>&nbsp;</td>\';
        
                    $totaldetalle = count($detalle);
                    
                        for($campo = 0; $campo < $totaldetalle; $campo++)
                        {
                            if($detalle[$campo]["ocultoInformeDetalle"] == 0 )
                            {
                                echo \'<td>\'.$detalle[$campo]["tituloInformeDetalle"].\'</td>\';
                            }    
                        }
                    
                    echo \'</tr>\';
                    $linea++;';
                }
                
                for($campo2 = 0; $campo2 < $totaldetalle; $campo2++)
                    {
                      $estructura.= '
                          $SumaGrupo["'.$detalle[$campo]["campoInformeDetalle"].'"]["'.$detalle[$campo2]["campoInformeDetalle"].'"] = 0;
                          $ConteoGrupo["'.$detalle[$campo]["campoInformeDetalle"].'"]["'.$detalle[$campo2]["campoInformeDetalle"].'"] = 0;
                          $PromedioGrupo["'.$detalle[$campo]["campoInformeDetalle"].'"]["'.$detalle[$campo2]["campoInformeDetalle"].'"] = 0; 
                          $NingunaGrupo["'.$detalle[$campo]["campoInformeDetalle"].'"]["'.$detalle[$campo2]["campoInformeDetalle"].'"] = 0; ';
                    }
                  
                  
                  $estructura .= '
                    if($linea >= $detalle[0]["lineasInforme"] )
                    {           
                        ';

                        $estructura .= 'Pie($detalle);
                                echo \'<H1 class=SaltoDePagina> </H1>\';';


                        $estructura .= 'Encabezado($detalle);';

                        $estructura .='
                        $linea = 1;


                    }';

                
                  $estructura .= '
                    while ($reg < $totalreg && $'.$detalle[$campo]["campoInformeDetalle"].'Anterior == $datos[$reg][$detalle['.$campo.']["campoInformeDetalle"]])
                    {';
            }
        }
        
       
        
        
        $estructura .=' 
            
            //Definimos si la fila es par o impar
            if((($reg+1)%2) == 0)
                $estilo = \'style= " font: \'.$detalle[0]["fuenteParInforme"].\'; font-size: \'.$detalle[0]["tamanoFuenteParInforme"].\'; font-weight: \'.($detalle[0]["negrillaParInforme"]== 1 ? \'bold\' : \'\').\'; font-style: \'.($detalle[0]["italicaParInforme"] == 1 ? \'italic\' : \'\').\'; text-decoration: \'.($detalle[0]["subrayadoParInforme"] == 1 ? \'underline\' : \'\').\'; color: \'.$detalle[0]["colorFuenteParInforme"].\'; background-color: \'.$detalle[0]["colorFondoParInforme"].\'; "\';
            else
                $estilo = \'style= " font: \'.$detalle[0]["fuenteImparInforme"].\'; font-size: \'.$detalle[0]["tamanoFuenteImparInforme"].\'; font-weight: \'.($detalle[0]["negrillaImparInforme"] == 1 ? \'bold\' : \'\').\'; font-style: \'.($detalle[0]["italicaImparInforme"] == 1 ? \'italic\' : \'\').\'; text-decoration: \'.($detalle[0]["subrayadoImparInforme"] == 1 ? \'underline\' : \'\').\'; color: \'.$detalle[0]["colorFuenteImparInforme"].\'; background-color: \'.$detalle[0]["colorFondoImparInforme"].\'; "\';
            
            echo \'<tr \'.$estilo.\'><td width="180">&nbsp;</td>\';
            //Recorremos todos los datos que hay en InformeDetalle
            for($campo = 0; $campo < $totaldetalle; $campo++)
            {
                if($detalle[$campo]["ocultoInformeDetalle"] == 0)
                {
                            
                    for($campo2 = 0; $campo2 < $totaldetalle; $campo2++)
                    {                    
                        if ($detalle[$campo]["calculoInformeDetalle"] != \'\' and isset($SumaGrupo[$detalle[$campo2]["campoInformeDetalle"]]))
                        {
                            $SumaGrupo[$detalle[$campo2]["campoInformeDetalle"]][$detalle[$campo]["campoInformeDetalle"]] += (float)$datos[$reg][$campo];
                            $ConteoGrupo[$detalle[$campo2]["campoInformeDetalle"]][$detalle[$campo]["campoInformeDetalle"]]++;
                            $PromedioGrupo[$detalle[$campo2]["campoInformeDetalle"]][$detalle[$campo]["campoInformeDetalle"]] = $SumaGrupo[$detalle[$campo2]["campoInformeDetalle"]][$detalle[$campo]["campoInformeDetalle"]] / $ConteoGrupo[$detalle[$campo2]["campoInformeDetalle"]][$detalle[$campo]["campoInformeDetalle"]];
                            $NingunaGrupo[$detalle[$campo2]["campoInformeDetalle"]][$detalle[$campo]["campoInformeDetalle"]]++;
                        }
                    }
                    

                    if ($detalle[$campo]["calculoInformeDetalle"] != \'\')
                    {
                        $SumaTotal[$detalle[$campo]["campoInformeDetalle"]] += (float)$datos[$reg][$campo];
                        $ConteoTotal[$detalle[$campo]["campoInformeDetalle"]]++;
                        $PromedioTotal[$detalle[$campo]["campoInformeDetalle"]] = $SumaTotal[$detalle[$campo]["campoInformeDetalle"]] / $ConteoTotal[$detalle[$campo]["campoInformeDetalle"]];
                        $NingunaTotal[$detalle[$campo]["campoInformeDetalle"]]++;
                    }
                    
                    $decimal = $detalle[$campo]["decimalInformeDetalle"];
                    $long = ($detalle[$campo]["longitudInformeDetalle"] == 0 ? 10 : $detalle[$campo]["longitudInformeDetalle"]);
                    $relleno = ($detalle[$campo]["rellenoInformeDetalle"] == \'\' ? \' \' : $detalle[$campo]["rellenoInformeDetalle"]);
                    $tipo = ($detalle[$campo]["alineacionHorizontalInformeDetalle"] == \'left\' 
                                ? 1 
                                : ($detalle[$campo]["alineacionHorizontalInformeDetalle"] == \'right\' 
                                    ? 0 
                                    : 2));
                    if($datos[$reg][$campo] != \'\' and $relleno != \' \')
                        $datos[$reg][$campo] = str_pad($datos[$reg][$campo],$long,$relleno, $tipo);

                    $negativo = (($detalle[$campo]["valoresNegativosInforme"] == \'Si\' and $datos[$reg][$campo] < 0) ? \'style=" color: #EC1E1E; "\' : \'\');';

                    //Selector del caso para elegir el formato 
        $estructura.='
                    switch($detalle[$campo]["formatoInformeDetalle"])
                    {
                        case \'Texto\':
                            $valor = $datos[$reg][$campo];
                            break;

                        case \'NumSeparador\':
                            $valor = number_format($datos[$reg][$campo], $decimal,".",",");
                            break;

                        case \'Numero\':
                            $valor = $datos[$reg][$campo];
                            break;

                        case \'MonSeparador\':
                            if($detalle[$campo]["ubicacionMonedaInforme"] == \'Antes\')
                            {    
                                $valor = $detalle[$campo]["simboloMonedaInforme"].\' \'.number_format($datos[$reg][$campo], $decimal,".",",");
                            }
                            else
                            {
                                $valor = number_format($datos[$reg][$campo], $decimal,".",",").\' \'. $detalle[$campo]["simboloMonedaInforme"];
                            }    
                            break;

                        case \'Moneda\':
                            if($detalle[$campo]["ubicacionMonedaInforme"] == \'Antes\')
                            {    
                                $valor = $detalle[$campo]["simboloMonedaInforme"].\' \'.number_format($datos[$reg][$campo], $decimal,".",",");
                            }
                            else
                            {
                                $valor = number_format($datos[$reg][$campo], $decimal,".",",").\' \'. $detalle[$campo]["simboloMonedaInforme"];
                            }
                            break;

                        case \'DMA\':

                            $valor = substr($valor = $datos[$reg][$campo],8,2).\'\'.$detalle[$campo]["separadorFechasInforme"].\'\'.substr($valor = $datos[$reg][$campo],5,2).\'\'.$detalle[$campo]["separadorFechasInforme"].\'\'.substr($valor = $datos[$reg][$campo],0,4);

                            break;
                        case \'MDA\':

                            $valor = substr($valor = $datos[$reg][$campo],5,2).\'\'.$detalle[$campo]["separadorFechasInforme"].\'\'.substr($valor = $datos[$reg][$campo],8,2).\'\'.$detalle[$campo]["separadorFechasInforme"].\'\'.substr($valor = $datos[$reg][$campo],0,4); 
                            break;

                       case \'AMD\':

                            $valor = substr($valor = $datos[$reg][$campo],0,4).\'\'.$detalle[$campo]["separadorFechasInforme"].\'\'.substr($valor = $datos[$reg][$campo],5,2).\'\'.$detalle[$campo]["separadorFechasInforme"].\'\'.substr($valor = $datos[$reg][$campo],8,2);    
                            break;

                        case \'Hora\':

                            $valor = substr($valor = $datos[$reg][$campo],0,2).\':\'.substr($valor = $datos[$reg][$campo],2,2);

                            break;

                        case \'Porcentaje\':
                            $valor = $datos[$reg][$campo] . \'%\';
                            break;

                        case \'Foto\':
                            $valor = ($datos[$reg][$campo] == \'\' ? \'&nbsp;\' : \'<img width="205" height="154" src="\'.$datos[$reg][$campo].\'"/>\');
                            break;

                        default :
                            $valor = $datos[$reg][$campo];
                            break;

                    }';



    $estructura .= '
                    $valor = ($valor==\'\' ? \'&nbsp;\' : $valor);

                    echo \'<td \'.$negativo.\' align="\'.$detalle[$campo]["alineacionHorizontalInformeDetalle"].\'">\'.$valor.\'</td>\';
                }
            }';
            
            $estructura .= '
            echo \'</tr>\';
        
            if($linea >= $detalle[0]["lineasInforme"] )
            {           
                ';
        
                $estructura .= 'Pie($detalle);
                        echo \'<H1 class=SaltoDePagina> </H1>\';';
        
             
                $estructura .= 'Encabezado($detalle);';
        
                $estructura .='
                $linea = 1;
            
            
            }
            
            
            $linea++;
            
            $reg++;
            
         ';
          
          for($campo = $totaldetalle-1; $campo >= 0; $campo--)
            {
                if($detalle[$campo]["grupoInformeDetalle"] == '1')
                {
                    
                        $estructura .= '}
                            
                            
                            echo \'<tr \'.$estilo.\'><td width="180">\'.$'.$detalle[$campo]["campoInformeDetalle"].'TituloTot.$'.$detalle[$campo]["campoInformeDetalle"].'Anterior.\'</td>\';';
                    
                    for($campo2 = 0; $campo2 < $totaldetalle; $campo2++)
                    {

                            $estructura .='
                                if($detalle['.$campo2.']["ocultoInformeDetalle"] == 0 )
                                {
                                 echo \'<td>\'.(($detalle['.$campo2.']["calculoInformeDetalle"] != \'Ninguna\' ) ? $'.$detalle[$campo2]["calculoInformeDetalle"].'Grupo[$detalle['.$campo.']["campoInformeDetalle"]]["'.$detalle[$campo2]["campoInformeDetalle"].'"] : \'&nbsp;\' ).\'</td>\';
                                }';

                    }
                    
                    $estructura .='
                        echo \'</tr>\';
                        $linea++;';
                    
                    
                    $estructura .='
                    if(isset($total[0]["espacioPosteriorInformeTotal"]))    
                    {
                        for($campo3 = 0; $campo3 < $total[0]["espacioPosteriorInformeTotal"]; $campo3++)
                        {

                            echo \'<tr><td  style="border: 0px;" colspan="'.($totalcolumnas+1).'">&nbsp;</td></tr>\';
                            $linea++;    
                        }
                    }';
                    
                }
                
                    
                    
            }
            
           
      $estructura .= '      
   }';  
      $estructura .='
                        echo \'</tr>\';
                        $linea++;';
            
                $estructura .= '
                        echo \'<tr><td  width="180">Total Informe</td>\';';
                    
      
                 for($campo3 = 0; $campo3 < $totaldetalle; $campo3++)
                    {

                            $estructura .='
                               
                                if($detalle['.$campo3.']["ocultoInformeDetalle"] == 0)
                                {
                                    echo \'<td>\'.($detalle['.$campo3.']["calculoInformeDetalle"] != \'Ninguna\'  ? $'.$detalle[$campo3]["calculoInformeDetalle"].'Total["'.$detalle[$campo3]["campoInformeDetalle"].'"] : \'&nbsp;\' ).\'</td>\';
                                
                                }';

                    }
                
                
                    
                    $estructura.= '
                echo \'</tr>\';
                $linea++;';
                    
                $estructura .= '
                if($linea >= $detalle[0]["lineasInforme"] )
                {           
                    ';

                    $estructura .= 'Pie($detalle);
                            echo \'<H1 class=SaltoDePagina> </H1>\';';


                    $estructura .= 'Encabezado($detalle);';

                    $estructura .='
                    $linea = 1;


                }';
     
                         
              
            
            
            
            
      $estructura .= 'Pie($detalle);
          $linea+=3;'; 
        
         $estructura .= '
             
        echo \'</body> 
            </html>\';';
        
		// $handle = fopen("resource.txt", "w+");
		// fputs($handle, $estructura);
		// fclose($handle);
		 
        //echo $estructura;
        eval($estructura);

      

*/
?>