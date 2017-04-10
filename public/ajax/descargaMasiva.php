<?php

$idRadicado = $_POST['Radicado_idRadicado'];

// $archivo = DB::table('radicado')
// ->select(DB::raw('archivoRadicado'))
// ->whereIn ('idRadicado', $idRadicado)
// ->get();

$archivo = DB::select('SELECT archivoRadicadoVersion from radicadoversion as rv
left join radicado as r 
on rv.Radicado_idRadicado = r.idRadicado
where Radicado_idRadicado IN ('.$idRadicado.')');

$files = array();
foreach ($archivo as $pos => $valor) 
{
	$files[] = get_object_vars($valor);
}

$zipname = 'documentos.zip';
$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);
foreach ($files as $file) 
{
	  $zip->addFromString(basename($file['archivoRadicadoVersion']),  file_get_contents($file['archivoRadicadoVersion']));  
}
$zip->close();

echo json_encode($zipname);
?>