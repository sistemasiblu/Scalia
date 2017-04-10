<?php

$idRadicado = $_POST['Radicado_idRadicado'];

$archivo = DB::select('SELECT archivoRadicadoVersion from radicadoversion as rv
left join radicado as r 
on rv.Radicado_idRadicado = r.idRadicado
where Radicado_idRadicado IN ('.$idRadicado.')');

$nombreArchivo = array();
foreach ($archivo as $pos => $valor) 
{
	$nombrearchivo[] = get_object_vars($valor);
}

echo json_encode($nombrearchivo);
?>