<?php 

$idTemporada = (isset($_POST['idTemporada']) ? $_POST['idTemporada'] : '');
$temporadahtml = '';

if ($idTemporada == '') 
{
	$temporadahtml = '<h4>Esta temporada no está asociada con una temporada de SAYA.</h4>';
}
else
{
	$temporada = DB::Select('
		SELECT 
		    nombreTemporada,
		    fechaInicialTemporada,
		    fechaFinalTemporada
		FROM
		    Iblu.Temporada
		WHERE
		    idTemporada = '.$idTemporada);

	$datosTemporada = get_object_vars($temporada[0]);


	$temporadahtml .= '
	<center><h3><b>Temporada </b> <span style="color: #2b2301;">'.$datosTemporada['nombreTemporada'].'</span></h3></center>
	<br>
	<h4><b>Nombre de temporada:</b> '.$datosTemporada['nombreTemporada'].'</h4>
	<h4><b>Fecha Inicial:</b> '.$datosTemporada['fechaInicialTemporada'].'</h4>
	<h4><b>Fecha Final:</b> '.$datosTemporada['fechaFinalTemporada'].'</h4>';

}

echo json_encode($temporadahtml);

?>