<?php 

$lista = $_POST['listaFinanciacion'];
$numeroDocumento = $_POST['numeroDocumento'];


$codigoDoc = DB::Select('
	SELECT codigoSayaListaFinanciacion
	FROM listafinanciacion
	WHERE idListaFinanciacion = '.$lista);

$codigoAlternoDoc = get_object_vars($codigoDoc[0]);


$consulta = DB::Select('
	SELECT numeroMovimiento, fechaElaboracionMovimiento, fechaVencimientoMovimiento, nombre1Tercero, valorTotalMovimiento
    FROM Iblu.Movimiento m 
    LEFT JOIN Iblu.Documento d on m.Documento_idDocumento = d.idDocumento 
    LEFT JOIN Iblu.Tercero t on m.Tercero_idTercero = t.idTercero 
    WHERE numeroMovimiento = "'.$numeroDocumento.'" 
    AND codigoAlternoDocumento = "'.$codigoAlternoDoc["codigoSayaListaFinanciacion"].'"');

if ($consulta != NULL) 
	$movimiento = get_object_vars($consulta[0]);	
else
	$movimiento = '';


echo json_encode($movimiento)
?>