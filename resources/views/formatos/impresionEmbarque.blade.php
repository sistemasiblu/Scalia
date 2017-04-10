@extends('layouts.formato')

<title>Embarque</title>
@section('contenido')

{!!Form::model($embarque)!!}
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
       for ($i = 0, $c = count($embarque); $i < $c; ++$i) 
       {
          $campos[$i] = (array) $embarque[$i];
       }
?>

<div>
  <!-- IMPRIMO EL NUMERO DE LA COMPRA -->
    <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
      <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
      <div class="col-md-11"><center><h1>Embarque N°: <?php echo $campos[0]['numeroEmbarque'] ?></h1></center></div>
    </div>

    </br> </br> </br>

    <div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Datos generales</h4></div>
		  <div class="panel-body">
		  	<?php 
		  	echo 
		  	'<div>
			  	<div style="width:150px; display:inline-block;"><b>Fecha elaboración:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['fechaElaboracionEmbarque'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Tipo de transporte:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['tipoTransporteEmbarque'].'</div>
		  	 </div>

		  	<div>
			 	 <div style="width:150px; display:inline-block;"><b>Puerto de carga:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['puertoCargaEmbarque'].'</div>
			
			  	 <div style="width:150px; display:inline-block;"><b>Puerto de descarga:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['puertoDescargaEmbarque'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>Agente de carga:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['agenteCargaEmbarque'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Naviera:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['navieraEmbarque'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>Fecha real de embarque:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['fechaRealEmbarque'].'</div>

			  	 <div style="width:150px; display:inline-block;"><b>Reporte a bodega:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['bodegaEmbarque'].'</div>
			</div>

			<div>
			  	 <div style="width:150px; display:inline-block;"><b>OTM:</b> </div>
			  	 <div style="width:450px; display:inline-block;">'.$campos[0]['otmEmbarque'].'</div>
			</div>';
			?>
			</br>
			<div class="panel panel-primary">
			  	<div class="panel-heading" style="height:45px;"><h4>Detalle</h4></div>
				   <div class="panel-body">
					
					<table class="table table-striped table-bordered table-hover" style="width:100%; overflow:scroll;">
					  <tr>
					    <th>Compra</th>
					    <th>Proveedor</th>
					    <th>PI</th>
					    <th>Delivery</th>
					    <th>Proforma</th>
					    <th>Volumen</th>
					    <th>Valor</th>
					    <th>Unidades</th>
					    <th>Peso</th>
					    <th>Bultos</th>
					    <th>Factura</th>
					    <th>Volumen</th>
					    <th>Valor</th>
					    <th>Unidades</th>
					    <th>Peso</th>
					    <th>Bultos</th>
					    <th>Reserva</th>
					    <th>Fecha Real</th>
					    <th>Fecha Maxima</th>
					    <th>Llegada ZF</th>
					    <th>Comprador</th>
					    <th>Evento</th>
					    <th>Dolar</th>
					    <th>Arribo Estimado</th>
					    <th>Arribo Puerto</th>
					    <th>Soporte Pago</th>
					    <th>Comp/Vend</th>
					    <th>Cantidad Contenedores</th>
					    <th>Tipo Contenedor</th>
					    <th>N° Contenedor</th>
					    <th>BL</th>
					    <th>N° Courrier</th>
					    <th>Pago</th>
					    <th>Originales</th>
					    <th>File</th>
					    <th>Descripción</th>
					    <th>Observación</th>
					  </tr>

					  <?php
					  	$volumenEmbarque = 0;
					  	$valorEmbarque = 0;
					  	$unidadEmbarque = 0;
					  	$pesoEmbarque = 0;
					  	$bultoEmbarque = 0;
					  	$volumenFactura = 0;
					  	$valorFactura = 0;
					  	$unidadFactura = 0;
					  	$pesoFactura = 0;
					  	$bultoFactura = 0;

						  for ($i=0; $i <count($embarquedetalle); $i++) 
						  { 
						    $emb = get_object_vars($embarquedetalle[$i]);
						    echo '
						    <tr>
						    <td>'.$emb['nombreTemporadaCompra'].'</td>
						    <td>'.$emb['nombreProveedorCompra'].'</td>
						    <td>'.$emb['numeroCompra'].'</td>
						    <td>'.$emb['fechaDeliveryCompra'].'</td>
						    <td>'.$emb['proformaEmbarqueDetalle'].'</td>
						    <td>'.$emb['volumenEmbarqueDetalle'].'</td>
						    <td>'.$emb['valorEmbarqueDetalle'].'</td>
						    <td>'.$emb['unidadEmbarqueDetalle'].'</td>
						    <td>'.$emb['pesoEmbarqueDetalle'].'</td>
						    <td>'.$emb['bultoEmbarqueDetalle'].'</td>
						    <td>'.$emb['facturaEmbarqueDetalle'].'</td>
						    <td>'.$emb['volumenFacturaEmbarqueDetalle'].'</td>
						    <td>'.$emb['valorFacturaEmbarqueDetalle'].'</td>
						    <td>'.$emb['unidadFacturaEmbarqueDetalle'].'</td>
						    <td>'.$emb['pesoFacturaEmbarqueDetalle'].'</td>
						    <td>'.$emb['bultoFacturaEmbarqueDetalle'].'</td>
						    <td>'.$emb['fechaReservaEmbarqueDetalle'].'</td>
						    <td>'.$emb['fechaRealEmbarqueDetalle'].'</td>
						    <td>'.$emb['fechaMaximaEmbarqueDetalle'].'</td>
						    <td>'.$emb['fechaLlegadaZonaFrancaEmbarqueDetalle'].'</td>
						    <td>'.$emb['compradorEmbarqueDetalle'].'</td>
						    <td>'.$emb['eventoEmbarqueDetalle'].'</td>
						    <td>'.$emb['dolarEmbarqueDetalle'].'</td>
						    <td>'.$emb['fechaArriboPuertoEstimadaEmbarqueDetalle'].'</td>
						    <td>'.$emb['fechaArriboPuertoEmbarqueDetalle'].'</td>
						    <td>'.($emb['soportePagoEmbarqueDetalle'] == 1 ? "Si" : "No").'</td>
						    <td>'.$emb['compradorVendedorEmbarqueDetalle'].'</td>
						    <td>'.$emb['cantidadContenedorEmbarqueDetalle'].'</td>
						    <td>'.$emb['tipoContenedorEmbarqueDetalle'].'</td>
						    <td>'.$emb['numeroContenedorEmbarqueDetalle'].'</td>
						    <td>'.$emb['blEmbarqueDetalle'].'</td>
						    <td>'.$emb['numeroCourrierEmbarqueDetalle'].'</td>
						    <td>'.($emb['pagoEmbarqueDetalle'] == 1 ? "Si" : "No").'</td>
						    <td>'.($emb['originalEmbarqueDetalle'] == 1 ? "Si" : "No").'</td>
						    <td>'.$emb['fileEmbarqueDetalle'].'</td>
						    <td>'.$emb['descripcionEmbarqueDetalle'].'</td>
						    <td>'.$emb['numeroCompra'].'</td>
						    </tr>
						    ';

						$volumenEmbarque += $emb['volumenEmbarqueDetalle'];
					  	$valorEmbarque += $emb['valorEmbarqueDetalle'];
					  	$unidadEmbarque += $emb['unidadEmbarqueDetalle'];
					  	$pesoEmbarque += $emb['pesoEmbarqueDetalle'];
					  	$bultoEmbarque += $emb['bultoEmbarqueDetalle'];
					  	$volumenFactura += $emb['volumenFacturaEmbarqueDetalle'];
					  	$valorFactura += $emb['valorFacturaEmbarqueDetalle'];
					  	$unidadFactura += $emb['unidadFacturaEmbarqueDetalle'];
					  	$pesoFactura += $emb['pesoFacturaEmbarqueDetalle'];
					  	$bultoFactura += $emb['bultoFacturaEmbarqueDetalle'];
						  }

						  echo'
						<tr>
							<th colspan="5" style="color: red;">TOTALES GENERALES</th>
							<th style="text-align:right;" colspan="1">'.number_format($volumenEmbarque,2,".",",").'</th>
							<th style="text-align:right;" colspan="1">'.number_format($valorEmbarque,2,".",",").'</th>
							<th style="text-align:right;" colspan="1">'.number_format($unidadEmbarque,2,".",",").'</th>
							<th style="text-align:right;" colspan="1">'.number_format($pesoEmbarque,2,".",",").'</th>
							<th style="text-align:right;" colspan="1">'.number_format($bultoEmbarque,2,".",",").'</th>
							<th colspan="1"></th>
							<th style="text-align:right;" colspan="1">'.number_format($volumenFactura,2,".",",").'</th>
							<th style="text-align:right;" colspan="1">'.number_format($valorFactura,2,".",",").'</th>
							<th style="text-align:right;" colspan="1">'.number_format($unidadFactura,2,".",",").'</th>
							<th style="text-align:right;" colspan="1">'.number_format($pesoFactura,2,".",",").'</th>
							<th style="text-align:right;" colspan="1">'.number_format($bultoFactura,2,".",",").'</th>
						</tr>';
					   ?>
					</table>
					</div>
				</div>
			</div>
		  </div>
		</div>
  	</div>