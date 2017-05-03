@extends('layouts.formato')

<title>Ficha tecnica</title>
@section('contenido')

{!!Form::model($encabezado)!!}
<?php 
function base64($archivo)
{
    $logo = '&nbsp;';
    $fp = fopen($archivo,"r", 0);
    if($archivo != '' and $fp)
    {
       $imagen = fread($fp,filesize($archivo));
       fclose($fp);
       // devuelve datos cifrados en base64
       //  formatear $imagen usando la sem ntica del RFC 2045

       $base64 = chunk_split(base64_encode($imagen));
       $logo =  '<img src="data:image/png;base64,' . $base64 .'" alt="Texto alternativo" width="130px"/>';
    }
    return $logo;
}    
    $img = base64('imagenes/Logo_iblu.png');
  

    $camposencabezado = array();
  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($encabezado); $i < $c; ++$i) 
       {
          $camposencabezado[$i] = (array) $encabezado[$i];
       }

     $modulo = (isset($_GET['modulo']) ? $_GET['modulo'] : 'todo');
?>
<div>
		<!-- IMPRIMO EL ENCABEZADO DEL INFORME DE FICHA TECNICA -->
  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
    <div class="col-md-11"><center><h1>Ficha Técnica Producto</h1></center></div>
  </div>

  </br> </br> </br>

  		<!-- IMPRIMO LOS DATOS GENERALES DEL INFORME DE FICHA TECNICA DENTRO DE UN PANEL -->
  	<div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Datos generales</h4></div>
		  <div class="panel-body">
		  	<?php 
		  	echo 
		  	'<div>
			  	<div style="width:150px; display:inline-block;"><b>Cliente:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombre1Tercero'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Marca:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombreMarca'].'</div>
		  	 </div>

		  	<div>
			 	 <div style="width:150px; display:inline-block;"><b>Tipo de Negocio:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombreTipoNegocio'].'</div>
			
			  	 <div style="width:150px; display:inline-block;"><b>Tipo de Producto:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombreTipoProducto'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>Temporada:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombreTemporada'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Categoría:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombreCategoria'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>Referencia base:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['referenciaBaseFichaTecnica'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Codigo alterno:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['codigoAlternoFichaTecnica'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>Número de molde:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['numeroMoldeFichaTecnica'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Descripción larga:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombreLargoFichaTecnica'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>Composición:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombreComposicion'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Area:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['areaMoldeFichaTecnica'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>Tallas:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombre1Talla'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Colores:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$camposencabezado[0]['nombre1Color'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>Precio:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.number_format($camposencabezado[0]['precioFichaTecnica'],2,".",",").'</div>
			</div>';
			?>
		  </div>
		</div>
  	</div>

  	<!-- IMPRIMO LOS COMPONENTES DE LA FICHA TECNICA DENTRO DE UN PANEL -->
  	<?php 
  	if(strpos($modulo, 'todo') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Componentes</h4></div>
		  <div class="panel-body">
		  	<?php 
		  		echo '
		  		<table  class="table table-striped table-bordered table-hover">
					<thead class="thead-inverse">
						<tr class="table-info">
							<th>Componente</th>
							<th>Tipo</th>
							<th>Tejido</th>
							<th>Peso</th>
							<th>Composicion</th>
						</tr>
					</thead>
					<tbody>';
					for ($i=0; $i < count($componentes); $i++) 
					{ 
						$componente = get_object_vars($componentes[$i]);
						echo '
						<tr>
							<td>'.$componente["componenteFichaTecnicaComponente"].'</td>
							<td>'.$componente["tipoFichaTecnicaComponente"].'</td>
							<td>'.$componente["tejidoFichaTecnicaComponente"].'</td>
							<td>'.$componente["pesoFichaTecnicaComponente"].'</td>
							<td>'.$componente["composicionFichaTecnicaComponente"].'</td>
						</tr>';
					}
				echo '
					</tbody>
				</table>'
		  	?>
		</div>
	</div>
	<?php
	}
	?>

  	<!-- INSERTO LAS IMAGENES GENERALES DEL INFORME DE FICHA TECNICA DENTRO DE UN PANEL -->
  	<?php 
  	if(strpos($modulo, 'todo') !== false or strpos($modulo, 'materiales') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Imagenes</h4></div>
		  <div class="panel-body">
		  	<?php 
		  	for ($i=0; $i <count($imagen) ; $i++) 
		  	{ 
		  		$imagenes = get_object_vars($imagen[$i]);

		  		echo '
		  		<div class="col-md-6">
			  		<div class="col-md-12" style="width:100%; border:1px solid; display:inline-block;"><b>Nombre:</b> '.$imagenes['nombreFichaTecnicaImagen'].' </div>

			  		<div class="col-md-12" style="width:100%; border:1px solid; display:inline-block;"><img src="http://190.248.133.146:8001/iblu/fotosficha/diseno/'.$imagenes['imagenFichaTecnicaImagen'].'" height="300" width="300"> </div>

			  		<div class="col-md-12" style="width:100%; border:1px solid; display:inline-block;"><b>Observación:</b> '.$imagenes['observacionFichaTecnicaImagen'].' </div>
		  		</div>';
		  	}
		  	?>
		</div>
	</div>
	<?php
	}
	?>

	<!-- INSERTO LAS IMAGENES GENERALES DEL INFORME DE FICHA TECNICA DENTRO DE UN PANEL -->
	<?php 
  	if(strpos($modulo, 'todo') !== false or strpos($modulo, 'procesos') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Procesos Especiales</h4></div>
		  <div class="panel-body">
		  	<?php 
			  	if (empty($procesos)) 
			  	{
			  		echo '<b>NO EXISTEN PROCESOS ESPECIALES PARA ESTA REFERENCIA</b>';	
			  	}
			  	else
			  	{
			  		$proceso = get_object_vars($procesos[0]);
			  		echo '<h4><b>PROCESO DE : '.$proceso['nombreFichaTecnicaProceso'].'</b></h4>';

			  		echo ($proceso['imagen1FichaTecnicaProceso'] == "" ? "" : '<img src="http://190.248.133.146:8001/iblu'.substr($proceso['imagen1FichaTecnicaProceso'], 2).'">');

			  		echo ($proceso['imagen2FichaTecnicaProceso'] == "" ? "" : '<img src="http://190.248.133.146:8001/iblu'.substr($proceso['imagen2FichaTecnicaProceso'], 2).'">');

			  		echo ($proceso['imagen3FichaTecnicaProceso'] == "" ? "" : '<img src="http://190.248.133.146:8001/iblu'.substr($proceso['imagen3FichaTecnicaProceso'], 2).'">');
			  	}
		  	?>
		</div>
	</div>
	<?php
	}
	?>

	<!-- IMPRIMO LAS COMBINACIONES DE COLOR DEL PROCESO DE LA FICHA TECNICA DENTRO DE UN PANEL -->
	<?php 
  	if(strpos($modulo, 'todo') !== false or strpos($modulo, 'procesos') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Combinaciones de Color del Proceso</h4></div>
		  <div class="panel-body">
		  	<?php 
			  	if (empty($procesos)) 
			  	{
			  		echo '<b>NO EXISTEN COMBINACIONES DE COLOR PARA ESTA REFERENCIA</b>';	
			  	}
				else
				{			  	
			  		echo '
			  		<table  class="table table-striped table-bordered table-hover">
						<thead class="thead-inverse">
							<tr class="table-info">
								<th>Color de fondo</th>
								<th>Color de proceso</th>
								<th>Tecnica de proceso</th>
							</tr>
						</thead>
						<tbody>';
							for ($i=0; $i < count($procesoscolor); $i++) 
							{ 
								$procesocolor = get_object_vars($procesoscolor[$i]);
								echo '
								<tr>
									<td>'.$procesocolor["colorFondo"].'</td>
									<td>'.$procesocolor["colorProceso"].'</td>
									<td>'.$procesocolor["nombreCentroProduccionTecnica"].'</td>
								</tr>';
							}
					echo '
						</tbody>
					</table>';
				}
		  	?>
		</div>
	</div>
	<?php
	}
	?>

	<!-- IMPRIMO LAS OBSERVACIONES DEL PROCESO ESPECIAL DE LA FICHA TECNICA DENTRO DE UN PANEL -->
	<?php 
  	if(strpos($modulo, 'todo') !== false or strpos($modulo, 'procesos') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Observaciones del proceso especial</h4></div>
		  <div class="panel-body">
		  	<?php 
		  		if (empty($procesos)) 
			  	{
			  		echo '<b>NO EXISTEN OBSERVACIONES DEL PROCESO ESPECIAL PARA ESTA REFERENCIA</b>';	
			  	}
			  	else
			  	{
			  		$proceso = get_object_vars($procesos[0]);
		  			echo $proceso['observacionFichaTecnicaProceso'];
			  	}
		  	?>
		</div>
	</div>
	<?php
	}
	?>


	<!-- IMPRIMO LAS OBSERVACIONES DE LA FICHA TECNICA DENTRO DE UN PANEL -->
	<?php 
  	if(strpos($modulo, 'todo') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Especificaciones de confección</h4></div>
		  <div class="panel-body">
		  	<?php 
		  		if (empty($observaciones)) 
			  	{
			  		echo '<b>NO EXISTEN ESPECIFICACIONES DE CONFECCIÓN PARA ESTA REFERENCIA</b>';	
			  	}
			  	else
			  	{
			  		$observacion = get_object_vars($observaciones[0]);
		  			echo $observacion['observacionConstruccionFichaTecnica'];
			  	}
		  	?>
		</div>
	</div>
	<?php
	}
	?>

	<!-- IMPRIMO LAS OBSERVACIONES DE LA FICHA TECNICA DENTRO DE UN PANEL -->
	<?php 
  	if(strpos($modulo, 'todo') !== false or strpos($modulo, 'materiales') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Observaciones del producto</h4></div>
		  <div class="panel-body">
		  	<?php 
		  		if (empty($observaciones)) 
			  	{
			  		echo '<b>NO EXISTEN OBSERVACIONES PARA ESTA REFERENCIA</b>';	
			  	}
			  	else
			  	{
			  		$observacion = get_object_vars($observaciones[0]);
		  			echo $observacion['observacionesFichaTecnica'];
			  	}
		  	?>
		</div>
	</div>
	<?php
	}
	?>


	<!-- IMPRIMO LAS ESPECIFICACIONES DE HILOS Y SESGOS DE LA FICHA TECNICA DENTRO DE UN PANEL -->
	<?php 
  	if(strpos($modulo, 'todo') !== false or strpos($modulo, 'materiales') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Especificaciones de Hilos y Sesgos</h4></div>
		  <div class="panel-body">
		  	<?php 
		  		echo '
		  		<table  class="table table-striped table-bordered table-hover">
					<thead class="thead-inverse">
						<tr class="table-info">
							<th>Tipo</th>
							<th>Descripción</th>
							<th>Especificación</th>
							<th>Observación</th>
						</tr>
					</thead>
					<tbody>';
						for ($i=0; $i < count($especificacioneshs); $i++) 
						{ 
							$especificaciones = get_object_vars($especificacioneshs[$i]);
							echo '
							<tr>
								<td>'.$especificaciones["tipoFichaTecnicaEspecificacion"].'</td>
								<td>'.$especificaciones["nombreFichaTecnicaEspecificacion"].'</td>
								<td>'.$especificaciones["especificacionFichaTecnicaEspecificacion"].'</td>
								<td>'.$especificaciones["observacionFichaTecnicaEspecificacion"].'</td>
							</tr>';
						}
				echo '
					</tbody>
				</table>'
		  	?>
		</div>
	</div>
	<?php
	}
	?>

	<!-- IMPRIMO LA TABLA DE MEDIDAS ANTES DEL PROCESO DE LA FICHA TECNICA DENTRO DE UN PANEL -->	
	<?php 
  	if(strpos($modulo, 'todo') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Tablas de medidas antes del proceso</h4></div>
		  <div class="panel-body">
		  	<?php 

		  	if (empty($medidas)) 
		  	{
		  		echo '<b>NO HAY TABLA DE MEDIDAS PARA ESTA REFERENCIA.</b>';		
		  	}
		  	else
		  	{
		  		$imagen = get_object_vars($medidas[0]);

		  		echo '<center><img src="http://190.248.133.146:8001/iblu'.substr($imagen['imagenMedida1FichaTecnica'], 2).'" height="300" width="300"></center>';
		  		echo '<table  class="table table-striped table-bordered table-hover">
		  				<thead class="thead-inverse">
							<tr class="table-info">
								<th>Descripción de la medida</th>
								<th>Observación de la medida</th>
								<th>Tolerancia</th>
								<th>Escala</th>';
				$totTalla = array();
	            for($i = 0; $i < count($tallas); $i++)
	            {
	            	$Ntallas = get_object_vars($tallas[$i]);
	                echo '<th>'.$Ntallas["codigoAlternoTalla"].'</td>';
	                $totTalla[$i] = 0;
	            }
	           		echo '</tr>
	           			</thead>';
		  
			  	$s = 0;
			  	
	      		$datos = count($medidas);

	      		while ($s < $datos)
				{
					echo '<tbody>';
					$medida = get_object_vars($medidas[$s]);

	      			echo '
	      			<td>'.$medida["nombreParteMedida"].'</td>
	      			<td>'.$medida["observacionFichaTecnicaMedida"].'</td>
	      			<td>'.number_format($medida["toleranciaFichaTecnicaMedida"],2,".",",").'</td>
	      			<td>'.number_format($medida["escalaFichaTecnicaMedida"],2,".",",").'</td>';
	      			
	                for($j = 0; $j < count($tallas); $j++)
	                {
	                	$Ntallas = get_object_vars($tallas[$j]);
	                    echo '<td>'.number_format($medida['T_'.($Ntallas["idTalla"] == '' ? 0: $Ntallas["idTalla"])],2,".",",").'</td>';
	                }
	                echo '</tbody>';
	               $s++;	
		      	}

	      		echo'
				</table>';
		  	}
		  	?>
		</div>
	</div>
	<?php
	}
	?>

	<!-- IMPRIMO LAS MATERIAS PRIMAS POR CENTRO DE PRODUCCION DE LA FICHA TECNICA DENTRO DE UN PANEL -->
	<?php 
  	if(strpos($modulo, 'todo') !== false or strpos($modulo, 'materiales') !== false)
	{ 
	?>
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Materias primas por Centro de Produccion</h4></div>
		  <div class="panel-body">
		  	<?php 
		  	$datos = array();
		  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
		       for ($i = 0, $c = count($materias); $i < $c; ++$i) 
		       {
		          $datos[$i] = (array) $materias[$i];
		       }

		  		echo '<table  class="table table-striped table-bordered table-hover">';
		  		$i = 0;
		  		$total = count($materias);

		  		while ($i < $total) 
		  		{
		  			$centro = $datos[$i]['nombreCentroProduccion'];

		  			echo '
		  			<thead class="thead-inverse">
					  	<tr class="table-info">
							<th colspan="20" style=" background-color:#255986; color:white;">'.$datos[$i]['nombreCentroProduccion'].'</th>
						</tr>
						<tr class="table-info">
							<td><b>Imagen de Referencia</b></td>
							<td><b>Referencia</b></td>
							<td><b>Descripción</b></td>
							<td><b>Color de producto</b></td>
							<td><b>Tipo</b></td>
							<td><b>Consumo</b></td>
							<td><b>Cantidad de producto</b></td>
							<td><b>Observaciones</b></td>
						</tr>
					</thead>';

					while ($i < $total and $centro == $datos[$i]["nombreCentroProduccion"])
					{
						echo 
						'<tbody>
						<td>'.($datos[$i]['imagen1Producto'] == "" ? "" : '<img src="http://190.248.133.146:8001/iblu'.substr($datos[$i]['imagen1Producto'], 2).'" height="50%;">').'</td>
						<td>'.$datos[$i]["referenciaProducto"].'</td>
						<td>'.$datos[$i]["nombreCortoProducto"].'</td>
						<td>'.$datos[$i]["nombre1Color"].'</td>
						<td>'.$datos[$i]["tipoProductoMaterial"].'</td>
						<td>'.$datos[$i]["consumoMaterialConversionProductoMaterial"].'</td>
						<td>'.$datos[$i]["consumoProductoProductoMaterial"].'</td>
						<td>'.$datos[$i]["observacionProductoMaterial"].'</td>
						</tbody>';

						$i++;
					}

					
		  		}
		  		echo '</table>';
		  	?>
		</div>
	</div>
	<?php
	}
	?>



		<!-- IMPRIMO LA RUTA DE PROCESOS DEL INFORME DE FICHA TECNICA DENTRO DE UN PANEL -->
		<?php 
  	if(strpos($modulo, 'todo') !== false)
	{ 
	?>
  		<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Ruta de procesos</h4></div>
		  <div class="panel-body">
		  <?php 
		  	echo '
		  		<table  class="table table-striped table-bordered table-hover">
					<thead class="thead-inverse">
						<tr class="table-info">
							<th>Centro de producción</th>
							<th>Costo</th>
							<th>Observaciones</th>
						</tr>
					</thead>
					<tbody>';
						for ($i=0; $i < count($centroproduccion); $i++) 
						{ 
							$centroprodu = get_object_vars($centroproduccion[$i]);
							echo '
							<tr>
								<td>'.$centroprodu["nombreCentroProduccion"].'</td>
								<td>'.$centroprodu["costoEstimadoFichaTecnicaCentroProduccion"].'</td>
								<td>'.$centroprodu["observacionFichaTecnicaCentroProduccion"].'</td>
							</tr>';
						}
				echo '
					</tbody>
				</table>'
		  ?>
		  </div>
		</div>
		<?php
		}
		?>

</div>

  		
{!!Form::close()!!}
@stop