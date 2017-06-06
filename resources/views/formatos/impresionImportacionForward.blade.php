@extends('layouts.formato')

<title>Importacion - Forward</title>
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
    <div class="col-md-11"><center><h1>Importacion Forward</h1></center></div>
  </div>

  <?php

  echo '
  <table table class="table table-striped table-bordered table-hover" style="width:100%;">';

  	$s = 0;
  	$valorFobGeneral = 0;
  	$valorEmbarcadoGeneral = 0;
	$valorPendienteGeneral = 0;
	$valorReportadoPagoGeneral = 0;
	$totalReportadoPagoPendienteGeneral = 0;
	$total = count($datos);
	
	while ($s < $total) 
	{  
		$documento = $datos[$s]["nombreDocumentoImportacion"];
	  	echo '
	  	<tr>
			<th colspan="20" style=" background-color:#255986; color:white;">Compras '.$datos[$s]['nombreDocumentoImportacion'].'</th>
		</tr>';

	  while ($s < $total and $documento == $datos[$s]["nombreDocumentoImportacion"])
		{
			echo '
			<tr>
				<th colspan="20" style=" background-color:#255986; color:white;">Cliente: '.$datos[$s]['nombreClienteCompra'].'</th>
			</tr>';

			$cliente = $datos[$s]['nombreClienteCompra'];
			$valorFobEspecifico = 0;
			$valorEmbarcadoEspecifico = 0;
			$valorPendienteEspecifico = 0;
			$valorReportadoPagoEspecifico = 0;
			$valorReportadoPagoPendienteEspecifico = 0;

			echo '
			<tr>
				<td><b>Compra</b></td>
				<td><b>Temporada</b></td>
				<td><b>Fecha Final Temporada</b></td>
				<td><b>Proveedor</b></td>
				<td><b>Valor FOB</b></td>
				<td><b>Valor embarcado</b></td>
				<td><b>Diferencia</b></td>
				<td><b>Reportado a pago</b></td>
				<td><b>Valor reportado a pago</b></td>
				<td><b>Valor pendiente a pago</b></td>
				<td><b>Factura</b></td>
				<td><b>Forward</b></td>
				<td><b>No. Forward</b></td>
				<td><b>Fecha Final Forward</b></td>
				<td><b>Tiempo en bodega</b></td>
				<td><b>Dias pagos</b></td>
				<td><b>Reserva</b></td>
				<td><b>Fecha de embarque</b></td>
				<td><b>Fecha maxima despacho</b></td>
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
						<td>'.$datos[$s]['fechaFinalTemporada'].'</td>
						<td>'.$datos[$s]['nombreProveedorCompra'].'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['valorCompra'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['valorEmbarcado'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['valorDiferencia'],2,".",",").'</td>
						<td>'.$datos[$s]['reportePagoEmbarqueDetalle'].'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['valorFacturaEmbarqueDetallePagada'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['valorFacturaEmbarqueDetallePendiente'],2,".",",").'</td>
						<td>'.$datos[$s]['facturaEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['idForward'].'</td>
						<td>'.$datos[$s]['numeroForward'].'</td>
						<td>'.$datos[$s]['fechaVencimientoForward'].'</td>
						<td>'.$datos[$s]['tiempoBodegaCompra'].'</td>
						<td>'.$datos[$s]['diaPagoClienteCompra'].'</td>
						<td>'.$datos[$s]['fechaReservaEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['fechaElaboracionEmbarque'].'</td>
						<td>'.$datos[$s]['fechaMaximaDespachoCompra'].'</td>
						<td>'.$datos[$s]['estadoCompra'].'</td>
					</tr>';

						// if($rowspan == 0)
						// {
						// 	$informe .= '
						// 	<td style="text-align:right;">'.number_format($datos[$s]['valorCompra'],2,".",",").'</td>

						// 	<td style="text-align:right;">'.number_format($datos[$s]['cantidadCompra'],2,".",",").'</td>';

						// 	$valorCliente += $datos[$s]['valorCompra'];
						// 	$cantidadCliente += $datos[$s]['cantidadCompra'];
						// }
						// else
						// {
						// 	$informe .= '
						// 	<td style="text-align:right;">&nbsp;</td>
						// 	<td style="text-align:right;">&nbsp;</td>';
						// }
							
						
					$valorFobEspecifico += $datos[$s]['valorCompra'];
					$valorEmbarcadoEspecifico += $datos[$s]['valorEmbarcado'];
					$valorPendienteEspecifico += $datos[$s]['valorDiferencia'];
					$valorReportadoPagoEspecifico += $datos[$s]['valorFacturaEmbarqueDetallePagada'];
					$valorReportadoPagoPendienteEspecifico += $datos[$s]['valorFacturaEmbarqueDetallePendiente'];

					$rowspan++;
					$s++;

				}	
				echo $informe;			
			}

			

			$valorFobGeneral += $valorFobEspecifico;
		  	$valorEmbarcadoGeneral += $valorEmbarcadoEspecifico;
			$valorPendienteGeneral += $valorPendienteEspecifico;
			$valorReportadoPagoGeneral += $valorReportadoPagoEspecifico;
			$totalReportadoPagoPendienteGeneral += $valorReportadoPagoPendienteEspecifico;

			echo '
			<tr>
				<th colspan="4">TOTALES DEL CLIENTE '.$cliente.'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorFobEspecifico,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorEmbarcadoEspecifico,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorPendienteEspecifico,2,".",",").'</th>
				<th colspan="1"></th>
				<th style="text-align:right;" colspan="1">'.number_format($valorReportadoPagoEspecifico,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorReportadoPagoEspecifico,2,".",",").'</th>
				<th colspan="10"></th>
			</tr>';
		}
	}	  
	echo'
			<tr>
				<th colspan="4" style="color: red;">TOTALES GENERALES</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorFobGeneral,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorEmbarcadoGeneral,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorPendienteGeneral,2,".",",").'</th>
				<th colspan="1"></th>
				<th style="text-align:right;" colspan="1">'.number_format($valorReportadoPagoGeneral,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalReportadoPagoPendienteGeneral,2,".",",").'</th>
				<th colspan="10"></th>
			</tr>
</table>';

  ?>

 
</div>

  		
{!!Form::close()!!}
@stop