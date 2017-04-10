@extends('layouts.formato')

<title>Programación de forward</title>
@section('contenido')

{!!Form::model($forward)!!}
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
  

    $campos = array();
    
    // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($forward); $i < $c; ++$i) 
       {
          $campos[$i] = (array) $forward[$i];
       }

	$padre = array();

    if ($forwardp != "") 
    {
       for ($i = 0, $c = count($forwardp); $i < $c; ++$i) 
       {
          $padre[$i] = (array) $forwardp[$i];
       }
	}       
?>

<div>
  <!-- IMPRIMO EL NUMERO DE LA COMPRA -->
    <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
      <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
      <div class="col-md-11"><center><h1>Forward N°: <?php echo $campos[0]['numeroForward'] ?></h1></center></div>
    </div>

    </br> </br> </br>

    <div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Datos generales</h4></div>
		  <div class="panel-body">
		  	<?php 
		  	echo 
		  	'<div>
			  	<div style="width:200px; display:inline-block;"><b>Fecha de negociación:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['fechaNegociacionForward'].'</div>

			  	 <div style="width:200px; display:inline-block;"><b>Fecha de vencimiento:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['fechaVencimientoForward'].'</div>
		  	 </div>

		  	<div>
			 	 <div style="width:200px; display:inline-block;"><b>Modalidad:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.str_replace("_", " ", $campos[0]['modalidadForward']).'</div>
			
			  	 <div style="width:200px; display:inline-block;"><b>Valor forward USD:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.number_format($campos[0]['valorDolarForward'],2,".",",").'</div>
			</div>

			<div>
			  	 <div style="width:200px; display:inline-block;"><b>Tasa:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.number_format($campos[0]['tasaForward'],2,".",",").'</div>

			  	 <div style="width:200px; display:inline-block;"><b>Tasa Inicial:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.number_format($campos[0]['tasaInicialForward'],2,".",",").'</div>
			</div>

			<div>
			  	 <div style="width:200px; display:inline-block;"><b>Valor Forward COP:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.number_format($campos[0]['valorPesosForward'],2,".",",").'</div>

			  	 <div style="width:200px; display:inline-block;"><b>Banco:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['bancoForward'].'</div>
			</div>

			<div>
			  	 <div style="width:200px; display:inline-block;"><b>Range:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.str_replace("_", " ", $campos[0]['rangeForward']).'</div>

			  	 <div style="width:200px; display:inline-block;"><b>Devaluación:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['devaluacionForward'].'</div>
			</div>

			<div>
			  	 <div style="width:200px; display:inline-block;"><b>Spot:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.number_format($campos[0]['spotForward'],2,".",",").'</div>

			  	 <div style="width:200px; display:inline-block;"><b>Estado:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['estadoForward'].'</div>
			</div>

			<div>
			  	 <div style="width:200px; display:inline-block;"><b>Descripción:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['descripcionForward'].'</div>

			  	 <div style="width:200px; display:inline-block;"><b>Forward Padre:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$padre[0]['numeroForward'].'</div>
			</div>';
			?>
			</br>
			<div class="panel panel-primary">
			  	<div class="panel-heading" style="height:45px;"><h4>Negociación</h4></div>
				   <div class="panel-body">
					
					<table class="table table-striped table-bordered table-hover" style="width:100%; overflow:scroll;">
					  <tr>
					    <th>Temporada</th>
					    <th>PI</th>
					    <th>Valor</th>
					    <th>Valor Real</th>
					  </tr>

					  <?php
					  $total = 0;
						  for ($i=0; $i <count($forwarddetalle); $i++) 
						  { 
						    $fwd = get_object_vars($forwarddetalle[$i]);
						    echo '
						    <tr>
							    <td>'.$fwd['nombreTemporadaForwardDetalle'].'</td>
							    <td>'.$fwd['numeroCompraForwardDetalle'].'</td>
							    <td style="text-align:right;">'.number_format($fwd['valorForwardDetalle'],2,".",",").'</td>
							    <td style="text-align:right;">'.number_format($fwd['valorRealForwardDetalle'],2,".",",").'</td>							   
						    </tr>
						    ';
						    $total += $fwd['valorRealForwardDetalle'];						    
						  }
					   ?>
					</table>
					<div style="float:right; font-size: 16px; font-weight:bold;">TOTAL: <?php echo number_format($total,2,".",",");?></div>
					</div>
				</div>
			</div>
		  </div>
		</div>
  	</div>