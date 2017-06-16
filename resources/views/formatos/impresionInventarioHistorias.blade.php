@extends('layouts.formato')

<title>Historias Laborales</title>
@section('contenido')

{!!Form::model($historias)!!}
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

?>
<div>
		<!-- IMPRIMO EL ENCABEZADO DEL INVENTARIO DE HISTORIAS LABORALES -->
  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
    <div class="col-md-11"><center><h1>Inventario de Historias Laborales</h1></center></div>
  </div>

  </br> </br> </br>

  		
  	<!-- IMPRIMO EL INVENTARIO DE LAS HISTORIAS DENTRO DE UN PANEL -->

  	<div class="panel panel-primary">
		<div class="panel-heading" style="height:45px;"><h4>Historias</h4></div>
		  <div class="panel-body">
		  	<?php 
		  		echo '
		  		<table  class="table table-striped table-bordered table-hover">
					<thead class="thead-inverse">
						<tr class="table-info">
							<th>Primer Apellido</th>
							<th>Segundo Apellido</th>
							<th>Primer Nombre</th>
							<th>Segundo Nombre</th>
							<th>Tipo de Identificación</th>
							<th>Identificación</th>
							<th>Tipo de Soporte</th>
							<th>Estado del Empleado</th>
							<th>Estado del Soporte</th>
							<th style="width:20%">Punto de localización</th>
							<th>Observación</th>
						</tr>
					</thead>
					<tbody>';
					for ($i=0; $i < count($historias); $i++) 
					{ 
						$hist = get_object_vars($historias[$i]);
						echo '
						<tr>
							<td>'.$hist["apellidoATercero"].'</td>
							<td>'.$hist["apellidoBTercero"].'</td>
							<td>'.$hist["nombreATercero"].'</td>
							<td>'.$hist["nombreBTercero"].'</td>
							<td>'.$hist["nombreIdentificacion"].'</td>
							<td>'.$hist["documentoTercero"].'</td>
							<td>'.$hist["nombreTipoSoporteDocumental"].'</td>
							<td>'.$hist["estadoTercero"].'</td>
							<td>'.$hist["estadoUbicacionDocumento"].'</td>
							<td>'.$hist["posicionUbicacionDocumento"].'</td>
							<td>'.$hist["observacionUbicacionDocumento"].'</td>
						</tr>';
					}
				echo '
					</tbody>
				</table>'
		  	?>
		</div>
	</div>

</div>

  		
{!!Form::close()!!}
@stop