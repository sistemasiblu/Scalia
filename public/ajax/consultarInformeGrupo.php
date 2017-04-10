<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idInformeCapa = (isset($_POST['idInformeCapa']) ? $_POST['idInformeCapa'] : 0);

$datos = DB::table('informecapa as ICap')
			->select(DB::raw('REPLACE(nombreInformeCapa,"capa", "") as numeroCapa, bandaInformeObjeto'))
			->leftjoin('informeobjeto as IObj', 'ICap.idInformeCapa', '=', 'IObj.InformeCapa_idInformeCapa')
			->leftjoin('estiloinforme as Est', 'IObj.EstiloInforme_idEstiloInforme', '=', 'Est.idEstiloInforme')
			->where('ICap.idInformeCapa', '=', $idInformeCapa)
			->where('IObj.bandaInformeObjeto','like', 'layoutGrupoEnc%')
			->groupby('IObj.bandaInformeObjeto')
			->get();

$informe = array();
for($i = 0; $i < count($datos); $i++) 
{
    $informe[] = get_object_vars($datos[$i]);
}

echo json_encode($informe);
?>