@extends('layouts.formato')

<title>Inventario Documental</title>
@section('contenido')

{!!Form::model($otros)!!}
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
    <div class="col-md-11"><center><h1>Inventario Documental</h1></center></div>
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
							<th>Descripción</th>
							<th>Fecha Inicial</th>
							<th>Fecha Final</th>
							<th>No Folios</th>
							<th>Tipo de Soporte</th>
							<th>Dependencia Productora</th>
							<th>Compañía</th>
							<th style="width:20%">Punto de localización</th>
							<th>Estado</th>
							<th>Observación</th>
						</tr>
					</thead>
					<tbody>';
					for ($i=0; $i < count($otros); $i++) 
					{ 
						$otr = get_object_vars($otros[$i]);
						echo '
						<tr>
							<td>'.$otr["descripcionUbicacionDocumento"].'</td>
							<td>'.$otr["fechaInicialUbicacionDocumento"].'</td>
							<td>'.$otr["fechaFinalUbicacionDocumento"].'</td>
							<td>'.$otr["numeroFolioUbicacionDocumento"].'</td>
							<td>'.$otr["nombreTipoSoporteDocumental"].'</td>
							<td>'.$otr["nombreDependencia"].'</td>
							<td>'.$otr["nombreCompania"].'</td>
							<td>'.$otr["posicionUbicacionDocumento"].'</td>
							<td>'.$otr["estadoUbicacionDocumento"].'</td>
							<td>'.$otr["observacionUbicacionDocumento"].'</td>
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