@extends('layouts.formato')

<title>Mensajería</title>
@section('contenido')

{!!Form::model($datosMensajeria)!!}
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
  

    $datos = array();
    if (count($datosMensajeria) > 0) 
    {
  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($datosMensajeria); $i < $c; ++$i) 
       {
          $datos[$i] = (array) $datosMensajeria[$i];
       }
		?>
		<div>
				<!-- IMPRIMO EL ENCABEZADO DEL INFORME DE FICHA TECNICA -->
		  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
		    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
		    <div class="col-md-11"><center><h1>Mensajería</h1></center></div>
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
					  	<div style="width:150px; display:inline-block;"><b>Tipo:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['tipoCorrespondenciaMensajeria'].'</div>

					  	 <div style="width:150px; display:inline-block;"><b>Prioridad:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['prioridadMensajeria'].'</div>
				  	 </div>

				  	<div>
					 	 <div style="width:150px; display:inline-block;"><b>Codigo de radicado:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['codigoRadicado'].'</div>
					
					  	 <div style="width:150px; display:inline-block;"><b>Fecha de envio:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['fechaEnvioMensajeria'].'</div>
					</div>

					<div>
					  	 <div style="width:150px; display:inline-block;"><b>Descripcion:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['descripcionMensajeria'].'</div>
					</div>

					<div>
					  	 <div style="width:150px; display:inline-block;"><b>Transportador:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['transportadorMensajeria'].'</div>

					  	 <div style="width:150px; display:inline-block;"><b>Estado:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.str_replace('_', ' ', $datos[0]['estadoEntregaMensajeria']).'</div>
					</div>

					<div>
					  	 <div style="width:150px; display:inline-block;"><b>Destinatario:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['destinatarioMensajeria'].'</div>

					  	 <div style="width:150px; display:inline-block;"><b>Seccion:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['seccionEntregaMensajeria'].'</div>
					</div>

					<div>
					  	 <div style="width:150px; display:inline-block;"><b>Direccion:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['direccionEntregaMensajeria'].'</div>

					  	 <div style="width:150px; display:inline-block;"><b>Guia:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['numeroGuiaMensajeria'].'</div>
					</div>

					<div>
					  	 <div style="width:150px; display:inline-block;"><b>Fecha de entrega:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.($datos[0]['fechaEntregaMensajeria'] == '0000-00-00 00:00:00' ? 'Sin entregar' : $datos[0]['fechaEntregaMensajeria']).'</div>

					  	 <div style="width:150px; display:inline-block;"><b>Fecha limite:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['fechaLimiteMensajeria'].'</div>
					</div>

					<div>
					  	 <div style="width:150px; display:inline-block;"><b>Usuario creador:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['nombreUsuario'].'</div>
					</div>

					<div>
					  	 <div style="width:150px; display:inline-block;"><b>Observacion:</b> </div>
					  	 <div style="width:450px; display:inline-block;">'.$datos[0]['observacionMensajeria'].'</div>
					</div>';
					?>
				  </div>
				</div>
		  	</div>

		</div>
	<?php 
	}
	else
	{
		echo '
		<div>
				<!-- IMPRIMO EL ENCABEZADO DEL INFORME DE FICHA TECNICA -->
		  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
		    <div class="col-md-1" style="top:15px">'. $img.'</div>
		    <div class="col-md-11"><center><h1>¡EL REGISTRO HA SIDO ELIMINADO!</h1></center></div>
		  </div>
		</div>';
	}
	?>

  		
{!!Form::close()!!}
@stop