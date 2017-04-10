<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idInformeCapa = (isset($_POST['idInformeCapa']) ? $_POST['idInformeCapa'] : 0);

$datos = DB::table('informecapa as ICap')
			->select(DB::raw('nombreInformeCapa, idInformeObjeto, InformeCapa_idInformeCapa, bandaInformeObjeto, nombreInformeObjeto, estiloInformeObjeto, EstiloInforme_idEstiloInforme, tipoInformeObjeto, etiquetaInformeObjeto, campoInformeObjeto, idEstiloInforme, nombreEstiloInforme, colorFondoEstiloInforme, colorBordeEstiloInforme, colorTextoEstiloInforme, fuenteTextoEstiloInforme, tamañoTextoEstiloInforme, negrillaEstiloInforme, italicaEstiloInforme, subrayadoEstiloInforme'))
			->leftjoin('informeobjeto as IObj', 'ICap.idInformeCapa', '=', 'IObj.InformeCapa_idInformeCapa')
			->leftjoin('estiloinforme as Est', 'IObj.EstiloInforme_idEstiloInforme', '=', 'Est.idEstiloInforme')
			->where('ICap.idInformeCapa', '=', $idInformeCapa)
			->get();
$informe = array();
for($i = 0; $i < count($datos); $i++) 
{
    $informe[] = get_object_vars($datos[$i]);
}

echo json_encode($informe);
?>