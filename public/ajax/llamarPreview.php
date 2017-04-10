<?php

$idRadicado = $_POST['Radicado_idRadicado'];
$numeroVersion = $_POST['version'];

if ($numeroVersion == '') 
{
//Consulto para saber el numero de version minimo osea el inicial
$versionmaxima = DB::Select('SELECT max(numeroRadicadoVersion) as version from radicadoversion
where Radicado_idRadicado = '.$idRadicado.'');
$versionmaxima = get_object_vars($versionmaxima[0]);

$archivo = DB::select('SELECT archivoRadicadoVersion from radicadoversion
where Radicado_idRadicado = '.$idRadicado.' and numeroRadicadoVersion = '.$versionmaxima['version'].'');
}
else
{
  $versionmaxima = $numeroVersion;
  
  $archivo = DB::select('SELECT archivoRadicadoVersion from radicadoversion
	where Radicado_idRadicado = '.$idRadicado.' and numeroRadicadoVersion = '.$versionmaxima.'');
}

//Convierto un array en string
$nombrearchivo = get_object_vars($archivo[0]);

echo json_encode($nombrearchivo['archivoRadicadoVersion']);
?>