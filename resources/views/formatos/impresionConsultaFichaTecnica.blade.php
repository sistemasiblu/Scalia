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
			</div>';
			?>
		  </div>
		</div>
  	</div>

  	<!-- INSERTO LAS IMAGENES GENERALES DEL INFORME DE FICHA TECNICA DENTRO DE UN PANEL -->
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

		<!-- IMPRIMO LA RUTA DE PROCESOS DEL INFORME DE FICHA TECNICA DENTRO DE UN PANEL -->
  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Ruta de procesos</h4></div>
		  <div class="panel-body">
		  <?php 
		  	echo '
		  		<div style="width:400px; display:inline-block;"><b>Centro de producción:</b> </div>
		  		<div style="width:400px; display:inline-block;"><b>Costo:</b> </div>
		  		<div style="width:400px; display:inline-block;"><b>Observaciones:</b> </div>';

		  	for ($i=0; $i < count($centroproduccion); $i++) 
		  	{ 
		  		$datoscentroproduccion = get_object_vars($centroproduccion[$i]);

		  		echo '
		  		<div style="border:solid 1px;">
		  			<div style="width:400px; display:inline-block;">'.$datoscentroproduccion['nombreCentroProduccion'].'</div>
		  			<div style="width:400px; display:inline-block;">'.$datoscentroproduccion['costoEstimadoFichaTecnicaCentroProduccion'].'</div>
		  			<div style="width:400px; display:inline-block;">'.$datoscentroproduccion['observacionFichaTecnicaCentroProduccion'].'</div>
		  		</div>';
		  	}
		  	
		  ?>
		  </div>
		</div>
  	</div>

</div>

  		
{!!Form::close()!!}
@stop