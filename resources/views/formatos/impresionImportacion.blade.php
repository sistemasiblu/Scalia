@extends('layouts.formato')

<title>Importacion</title>
@section('contenido')

{!!Form::model($consulta)!!}
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
  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($consulta); $i < $c; ++$i) 
       {
          $datos[$i] = (array) $consulta[$i];
       }

?>
<div>
  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
    <div class="col-md-11"><center><h1>Importaciones</h1></center></div>
  </div>

  <?php

  echo '
  <table table class="table table-striped table-bordered table-hover" style="width:100%;">';

  	$s = 0;
  	$totalValorGeneral = 0;
  	$totalValorFaltante = 0;
	$totalCantidadGeneral = 0;
	$totalCantidadFaltante = 0;
	$totalReportadoGeneral = 0;
	$totalVolumenGeneral = 0;
	$total = count($datos);
	
	while ($s < $total) 
	{  
		$documento = $datos[$s]["nombreDocumentoImportacion"];
	  	echo '
	  	<tr>
			<th colspan="24" style=" background-color:#255986; color:white;">Compras '.$datos[$s]['nombreDocumentoImportacion'].'</th>
		</tr>';

	  while ($s < $total and $documento == $datos[$s]["nombreDocumentoImportacion"])
		{
			echo '
			<tr>
				<th colspan="24" style=" background-color:#255986; color:white;">Cliente: '.$datos[$s]['nombreClienteCompra'].'</th>
			</tr>';

			$cliente = $datos[$s]['nombreClienteCompra'];
			$valorCliente = 0;
			$valorFaltante = 0;
			$cantidadCliente = 0;
			$cantidadFaltante = 0;
			$reportadoCliente = 0;
			$volumenCliente = 0;

			echo '
			<tr>
				<td><b>Compra</b></td>
				<td><b>Temporada</b></td>
				<td><b>Proveedor</b></td>
				<td><b>Valor FOB</b></td>
				<td><b>Unidades compra</b></td>
				<td><b>Valor embarcado</b></td>
				<td><b>Unidades embarcadas</b></td>
				<td><b>Reportado a pago</b></td>
				<td><b>Valor reportado a pago</b></td>
				<td><b>Forward</b></td>
				<td><b>Puerto embarque</b></td>
				<td><b>Volumen</b></td>
				<td><b>Delivery</b></td>
				<td><b>Fecha de forward</b></td>
				<td><b>Tiempo en bodega</b></td>
				<td><b>Dias pagos</b></td>
				<td><b>Reserva</b></td>
				<td><b>Fecha de embarque</b></td>
				<td><b>Arribo a puerto</b></td>
				<td><b>Dias de transito</b></td>
				<td><b>Fecha maxima despacho</b></td>
				<td><b>Fecha maxima embarque</b></td>
				<td><b>Fecha de Ingreso a bodega</b></td>
				<td><b>Estado de compra</b></td>
			<tr>';

			while ($s < $total and $documento == $datos[$s]["nombreDocumentoImportacion"] and $cliente == $datos[$s]['nombreClienteCompra']) 
			{

				$informe = '';
				$rowspan = 0;
				$compra = $datos[$s]['numeroCompra'];

				while ($s < $total and $documento == $datos[$s]["nombreDocumentoImportacion"] and $cliente == $datos[$s]['nombreClienteCompra'] and $compra == $datos[$s]['numeroCompra']) 
				{
					$informe .= '
					<tr>
						<td>'.$datos[$s]['numeroCompra'].'</td>
						<td>'.$datos[$s]['nombreTemporadaCompra'].'</td>
						<td>'.$datos[$s]['nombreProveedorCompra'].'</td>';

						if($rowspan == 0)
						{
							$informe .= '
							<td style="text-align:right;">'.number_format($datos[$s]['valorCompra'],2,".",",").'</td>

							<td style="text-align:right;">'.number_format($datos[$s]['cantidadCompra'],2,".",",").'</td>';

							$valorCliente += $datos[$s]['valorCompra'];
							$cantidadCliente += $datos[$s]['cantidadCompra'];
						}
						else
						{
							$informe .= '
							<td style="text-align:right;">&nbsp;</td>
							<td style="text-align:right;">&nbsp;</td>';
						}
							
						$informe .=
						'<td style="text-align:right;">'.number_format($datos[$s]['valorFaltante'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['cantidadFaltante'],2,".",",").'</td>
						<td>'.$datos[$s]['pagoEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['valorFacturaEmbarqueDetallePagada'].'</td>
						<td>'.$datos[$s]['idForward'].'</td>
						<td>'.$datos[$s]['nombreCiudadCompra'].'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['volumenCompra'],2,".",",").'</td>
						<td>'.$datos[$s]['fechaDeliveryCompra'].'</td>
						<td>'.$datos[$s]['fechaVencimientoForward'].'</td>
						<td>'.$datos[$s]['tiempoBodegaCompra'].'</td>
						<td>'.$datos[$s]['diaPagoClienteCompra'].'</td>
						<td>'.$datos[$s]['fechaReservaEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['fechaRealEmbarque'].'</td>
						<td>'.$datos[$s]['fechaArriboPuertoEstimadaEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['diasCiudadTipoTransporte'].'</td>
						<td>'.$datos[$s]['fechaMaximaCliente'].'</td>
						<td>'.$datos[$s]['fechaMaximaEmbarqueCumplirForward'].'</td>
						<td>'.$datos[$s]['fechaLlegadaZonaFrancaEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['estadoCompra'].'</td>
					</tr>';

					$valorFaltante += $datos[$s]['valorFaltante'];
					$cantidadFaltante += $datos[$s]['cantidadFaltante'];
					$reportadoCliente += $datos[$s]['valorFacturaEmbarqueDetallePagada'];
					$volumenCliente += $datos[$s]['volumenCompra'];

					$rowspan++;
					$s++;

				}	
				echo $informe;			
			}

			

			$totalValorGeneral += $valorCliente;
			$totalValorFaltante += $valorFaltante;
			$totalCantidadGeneral += $cantidadCliente;
			$totalCantidadFaltante += $cantidadFaltante;
  			$totalReportadoGeneral += $reportadoCliente;
  			$totalVolumenGeneral += $volumenCliente;

			echo '
			<tr>
				<th colspan="3">TOTALES DEL CLIENTE '.$cliente.'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorCliente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($cantidadCliente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorFaltante,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($cantidadFaltante,2,".",",").'</th>
				<th colspan="1"></th>
				<th style="text-align:right;" colspan="1">'.number_format($reportadoCliente,2,".",",").'</th>
				<th colspan="2"></th>
				<th style="text-align:right;" colspan="1">'.number_format($volumenCliente,2,".",",").'</th>
				<th colspan="12"></th>
			</tr>';
		}
	}	  
	echo'
			<tr>
				<th colspan="3" style="color: red;">TOTALES GENERALES</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalValorGeneral,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalCantidadGeneral,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalValorFaltante,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalCantidadFaltante,2,".",",").'</th>
				<th colspan="1"></th>
				<th style="text-align:right;" colspan="1">'.number_format($totalReportadoGeneral,2,".",",").'</th>
				<th colspan="2"></th>
				<th style="text-align:right;" colspan="1">'.number_format($totalVolumenGeneral,2,".",",").'</th>
				<th colspan="12"></th>
			</tr>
</table>';

  ?>

 
</div>

  		
{!!Form::close()!!}
@stop