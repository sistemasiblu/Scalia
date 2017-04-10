<?php 

$id = isset($_GET["P"]) ? $_GET["P"] : 0; 
$idDestino = isset($_GET["D"]) ? $_GET["D"] : 0; 

$encuesta = DB::select(
    'SELECT idEncuesta, nombreEncuestaPublicacion, fechaEncuestaPublicacion,
    		tituloEncuesta, descripcionEncuesta,
		    idEncuestaPregunta, preguntaEncuestaPregunta, detalleEncuestaPregunta, tipoRespuestaEncuestaPregunta,
			idEncuestaOpcion, valorEncuestaOpcion, nombreEncuestaOpcion
    FROM encuestapublicacion
	left join encuesta
	on encuestapublicacion.Encuesta_idEncuesta = encuesta.idEncuesta
    left join encuestapregunta
    on encuesta.idEncuesta = encuestapregunta.Encuesta_idEncuesta
    left join encuestaopcion
    on encuestapregunta.idEncuestaPregunta = encuestaopcion.EncuestaPregunta_idEncuestaPregunta
    where encuestapublicacion.idEncuestaPublicacion = '.$id);


$datos = array();

for($i = 0; $i < count($encuesta); $i++)
{
    $datos[] = get_object_vars($encuesta[$i]); 
}

?>
@extends('layouts.formato')
<h3 id="titulo"><center>Encuesta<br><?php echo $datos[0]["nombreEncuestaPublicacion"];?></center></h3>
    		

@section('contenido')

{!!Html::style('css/encuesta.css')!!}

{!!Form::open(['route'=>['grabarRespuesta',$id],'method'=>'POST'])!!}
<?php 

echo '<div class="PublicacionForm">
		<input type="hidden" id="idEncuesta" name="idEncuesta" value="'.$datos[0]["idEncuesta"].'">
		<input type="hidden" id="idPublicacionEncuesta" name="idPublicacionEncuesta" value="'.$id.'">
		<input type="hidden" id="idEncuestaPublicacionDestino" name="idEncuestaPublicacionDestino" value="'.$idDestino.'">
		<center><label class="PublicacionTitulo">'.$datos[0]["tituloEncuesta"].'</label></center>
		<label class="PublicacionSubtitulo">'.$datos[0]["descripcionEncuesta"].'</label>';

$i = 0;
$numPreg = 0;
while($i < count($datos))
{
	$preguntaAnt = $datos[$i]["idEncuestaPregunta"];

	echo '<div class="divEncuesta">
			<input type="hidden" id="idEncuestaPregunta" name="idEncuestaPregunta['.$numPreg.']" value="'.$datos[$i]["idEncuestaPregunta"].'">
			<div class="PublicacionPregunta">'.($numPreg+1).') '.$datos[$i]["preguntaEncuestaPregunta"].'</div> 
			
			<div class="PublicacionDetalle">
				'.$datos[$i]["detalleEncuestaPregunta"].'
			</div>';

	$tipo = '';
	switch($datos[$i]["tipoRespuestaEncuestaPregunta"])
	{
		case 'Respuesta Corta':
			$tipo = '<input type="text" id="respuesta" name="respuesta['.$numPreg.'][]" value="">';
			$i++;
			break;
		case 'Párrafo':
			$tipo = '<textarea id="respuesta" name="respuesta['.$numPreg.'][]"></textarea>';
			$i++;
			break;
		case 'Fecha':
			$tipo = '<input type="date" id="respuesta" name="respuesta['.$numPreg.'][]" value="">';
			$i++;
			break;
		case 'Hora':
			$tipo = '<input type="time" id="respuesta" name="respuesta['.$numPreg.'][]" value="">';
			$i++;
			break;


		default:

				switch($datos[$i]["tipoRespuestaEncuestaPregunta"])
				{
					case 'Selección Múltiple':
						$tipo = '';
						while($i < count($datos) and $preguntaAnt == $datos[$i]["idEncuestaPregunta"])
						{
							$tipo .= '<input type="radio" id="respuesta" name="respuesta['.$numPreg.'][]" value="'.$datos[$i]["valorEncuestaOpcion"].'" ><label class="PublicacionOpcion">'.$datos[$i]["nombreEncuestaOpcion"].'</label>';
							$i++;
						}
						$tipo .= '</select>';
						break;
					case 'Casillas de Verificación':
						$tipo = '';
						while($i < count($datos) and $preguntaAnt == $datos[$i]["idEncuestaPregunta"])
						{
							$tipo .= '<input type="checkbox" id="respuesta" name="respuesta['.$numPreg.'][]" value="'.$datos[$i]["valorEncuestaOpcion"].'" ><label class="PublicacionOpcion">'.$datos[$i]["nombreEncuestaOpcion"].'</label>';
							$i++;
						}
						break;
					case 'Lista de Opciones':
						$tipo = '<select id="respuesta" name="respuesta['.$numPreg.'][]" class="PublicacionSelect">';
						while($i < count($datos) and $preguntaAnt == $datos[$i]["idEncuestaPregunta"])
						{
							$tipo .= '<option value="'.$datos[$i]["valorEncuestaOpcion"].'">'.$datos[$i]["nombreEncuestaOpcion"].'</option>';
							$i++;
						}
						$tipo .= '</select>';
						break;
					case 'Escala Lineal':
						$tipo = 'rango';
						break;
				}
		
			break;
	}

	echo '<div class="PublicacionSelect">'.
			$tipo.'
		</div>
		</div>';
	$numPreg++;
}

?>
		<br><br>
		<center>{!!Form::submit('Enviar Respuestas',["class"=>"btn btn-success"])!!}</center>

		</div>
		</div>




	</fieldset>	
</div>	
{!!Form::close()!!}	
@stop