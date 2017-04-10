<?php 
$accion = (isset($_POST['accion']) ? $_POST['accion'] : 0);
$idInforme = (isset($_POST['idInforme']) ? $_POST['idInforme'] : 0);

$datos = DB::table('informecapa as ICapa')
			->select(DB::raw('ICapa.Informe_idInforme, ICapa.idInformeCapa, ICapa.tipoInformeCapa, ICapa.SistemaInformacion_idSistemaInformacion, ICapa.tablaInformeCapa'))
			->where('ICapa.Informe_idInforme', '=', $idInforme)
			->get();

// ->leftjoin('informeobjeto as IO', 'ICapa.idInformeCapa', '=', 'IO.InformeCapa_idInformeCapa')
// ->leftjoin('informeconcepto as IConc', 'ICapa.idInformeCapa', '=', 'IConc.InformeCapa_idInformeCapa')
			
$informe = array();
for($i = 0; $i < count($datos); $i++) 
{
    $informe[] = get_object_vars($datos[$i]);
}

echo json_encode($informe);
?> 