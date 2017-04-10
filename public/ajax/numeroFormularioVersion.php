<?php 

$nivel = $_POST['nivelVersion'];
$idRadicado = $_POST['Radicado_idRadicado'];

$numero = DB::Select('SELECT MAX(numeroRadicadoVersion) as numero from radicadoversion
where Radicado_idRadicado = '.$idRadicado.'');
$numero = get_object_vars($numero[0]);

$numeroVersion = explode(".",$numero["numero"]);

$nuevaVersion = '';

for ($i=0; $i <count($numeroVersion) ; $i++) 
{
	if ($i+1 < $nivel) 
	{
		$nuevaVersion .= $numeroVersion[$i].'.';
	}
	else if ($i+1 == $nivel)
	{
		$nuevaVersion .= $numeroVersion[$i]+ 1 .'.';
	}
	else if ($i+1 > $nivel)
	{
		$nuevaVersion .= '0'.'.';
	}
}

$nuevaVersion = substr($nuevaVersion, 0, strlen($nuevaVersion)-1);

echo json_encode($nuevaVersion);

?>