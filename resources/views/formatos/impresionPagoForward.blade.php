@extends('layouts.formato')

<title>Cumplimiento de forward</title>
@section('contenido')

{!!Form::model($pagofwd)!!}
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
       for ($i = 0, $c = count($pagofwd); $i < $c; ++$i) 
       {
          $campos[$i] = (array) $pagofwd[$i];
       }
    
?>

<div>
  <!-- IMPRIMO EL NUMERO DE LA COMPRA -->
    <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
      <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
      <div class="col-md-11"><center><h1>Pago de forward N°: <?php echo $campos[0]['numeroForward'] ?></h1></center></div>
    </div>

    </br> </br> </br>

    <div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Datos generales</h4></div>
		  <div class="panel-body">
		  	<?php 
		  	echo 
		  	'<div>
			  	<div style="width:200px; display:inline-block;"><b>Fecha de pago:</b></div>
			  	<div style="width:450px; display:inline-block;">'.$campos[0]['fechaPagoForward'].'</div>
		  	 </div>
		  	 <br>
		  	<div>
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
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['padreForward'].'</div>
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
					    <th>Factura</th>
					    <th>Fecha de factura</th>
					    <th>Valor factura</th>
					    <th>Valor pagado</th>
					  </tr>

					  <?php
					  $total = 0;
						  for ($i=0; $i <count($pagofwddetalle); $i++) 
						  { 
						    $fwd = get_object_vars($pagofwddetalle[$i]);
						    echo '
						    <tr>
							    <td>'.$fwd['nombreTemporadaPagoForwardDetalle'].'</td>
							    <td>'.$fwd['numeroCompraPagoForwardDetalle'].'</td>
							    <td>'.$fwd['facturaPagoForwardDetalle'].'</td>
							    <td>'.$fwd['fechaFacturaPagoForwardDetalle'].'</td>
							    <td style="text-align:right;">'.number_format($fwd['valorFacturaPagoForwardDetalle'],2,".",",").'</td>
							    <td style="text-align:right;">'.number_format($fwd['valorPagadoPagoForwardDetalle'],2,".",",").'</td>							   
						    </tr>
						    ';
						    $total += $fwd['valorPagadoPagoForwardDetalle'];						    
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