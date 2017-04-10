<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idInformeCapa = (isset($_POST['idInformeCapa']) ? $_POST['idInformeCapa'] : 0);

$datos = DB::table('informecapa as ICap')
			->select(DB::raw('nombreInformeCapa, idInformeConcepto, InformeCapa_idInformeCapa, ordenInformeConcepto, nombreInformeConcepto, tipoMovimientoInformeConcepto, tipoValorInformeConcepto, valorInformeConcepto, EstiloInforme_idEstiloInforme, detalleInformeConcepto, resumenInformeConcepto, graficoInformeConcepto, idEstiloInforme, nombreEstiloInforme, colorFondoEstiloInforme, colorBordeEstiloInforme, colorTextoEstiloInforme, fuenteTextoEstiloInforme, tamañoTextoEstiloInforme, negrillaEstiloInforme, italicaEstiloInforme, subrayadoEstiloInforme,  excluirTerceroInformeConcepto'))
			->leftjoin('informeconcepto as ICon', 'ICap.idInformeCapa', '=', 'ICon.InformeCapa_idInformeCapa')
			->leftjoin('estiloinforme as Est', 'ICon.EstiloInforme_idEstiloInforme', '=', 'Est.idEstiloInforme')
			->where('ICap.idInformeCapa', '=', $idInformeCapa)
			->get();
$informe = array();
for($i = 0; $i < count($datos); $i++) 
{
    $informe[] = get_object_vars($datos[$i]);
}

echo json_encode($informe);
?>