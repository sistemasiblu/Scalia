@extends('layouts.formato')

<title>Importacion detallada compra</title>
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
  <div class="col-md-12" style="border:1px; width: 100%; background-color:#CEECF5;">
    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
    <div class="col-md-11"><center><h1>Detalle de Importaciones</h1></center></div>
  </div>

  <?php

  echo '
  <table table class="table table-striped table-bordered table-hover" style="width:100%;">';

  	$s = 0;
	$totalVolumen = 0;
	$totalCantidadPedido = 0;
	$totalCantidadFactura = 0;
	$totalCantidadPendiente = 0;
	$totalCantidadCumplimiento = 0;
	$totalValorPedido = 0;
	$totalValorFactura = 0;
	$totalValorPendiente = 0;
	$totalValorCumplimiento = 0;
	$totalCantidadDatos = 0;
	$total = count($datos);
	
	while ($s < $total) 
	{  
		$documento = $datos[$s]["nombreDocumentoImportacion"];
	  	echo '
	  	<tr>
			<th colspan="34" style=" background-color:#255986; color:white; width:100%;">Compras '.$datos[$s]['nombreDocumentoImportacion'].'</th>
		</tr>';

	  while ($s < $total and $documento == $datos[$s]["nombreDocumentoImportacion"])
		{
			echo '
			<tr>
				<th colspan="34" style=" background-color:#255986; color:white; width:100%;">Cliente: '.$datos[$s]['nombreClienteCompra'].'</th>
			</tr>';

			$cliente = $datos[$s]['nombreClienteCompra'];

			$volumenCliente = 0;
			$cantidadPedidoCliente = 0;
			$cantidadFacturaCliente = 0;
			$cantidadPendienteCliente = 0;
			$cantidadCumplimientoCliente = 0;
			$cantidadDatosCliente = 0;
			$valorPedidoCliente = 0;
			$valorFacturaCliente = 0;
			$valorPendienteCliente = 0;
			$valorCumplimientoCliente = 0;
			
			while ($s < $total and $documento == $datos[$s]["nombreDocumentoImportacion"] and $cliente == $datos[$s]['nombreClienteCompra']) 
			{

				echo '
					<tr>
						<th colspan="34" style=" background-color:#255986; color:white; width:100%;">Compra: '.$datos[$s]['numeroCompra'].'</th>
					</tr>';

					$compra = $datos[$s]['numeroCompra'];

					$volumen = 0;
					$cantidadPedido = 0;
					$cantidadFactura = 0;
					$cantidadPendiente = 0;
					$cantidadCumplimiento = 0;
					$cantidadDatos = 0;
					$valorPedido = 0;
					$valorFactura = 0;
					$valorPendiente = 0;
					$valorCumplimiento = 0;
					echo '
					<tr>
						<td><b>Compra</b></td>
						<td><b>Codigo SubLinea</b></td>
						<td><b>Codigo Categoría</b></td>
						<td><b>Nombre Categoría</b></td>
						<td><b>Marca</b></td>
						<td><b>Temporada</b></td>
						<td><b>Esquema</b></td>
						<td><b>Evento</b></td>
						<td><b>Referencia</b></td>
						<td><b>Descripción</b></td>
						<td><b>Fecha de Compra</b></td>
						<td><b>Intermediario</b></td>
						<td><b>Proveedor</b></td>
						<td><b>Cliente</b></td>
						<td><b>Puerto de Embarque</b></td>
						<td><b>Embarcado</b></td>
						<td><b>Delivery</b></td>
						<td><b>Booking</b></td>
						<td><b>Volumen</b></td>
						<td><b>Fecha real de embarque</b></td>
						<td><b>Fecha de Ingreso a bodega</b></td>
						<td><b>Embarque</b></td>
						<td><b>Observacion</b></td>
						<td><b>Documento Interno</b></td>
						<td><b>Documento Proveedor</b></td>
						<td><b>Fecha de Elaboración Embarque</b></td>
						<td><b>Precio Unitario</b></td>
						<td><b>Cantidad Pedido</b></td>
						<td><b>Cantidad Factura</b></td>
						<td><b>Cantidad Pendiente</b></td>
						<td><b>Cumplimiento</b></td>
						<td><b>Valor Pedido</b></td>
						<td><b>Valor Factura</b></td>
						<td><b>Valor Pendiente</b></td>
						<td><b>Cumplimiento</b></td>
					<tr>';
				while ($s < $total and $documento == $datos[$s]["nombreDocumentoImportacion"] and $cliente == $datos[$s]['nombreClienteCompra'] and $compra == $datos[$s]['numeroCompra']) 
				{
					echo '
					<tr>
						<td>'.$datos[$s]['numeroCompra'].'</td>
						<td>'.substr($datos[$s]['codigoAlterno1Categoria'], 0, 2).'</td>
						<td>'.substr($datos[$s]['nombreCategoria'], 0, 4).'</td>
						<td>'.$datos[$s]['nombreCategoria'].'</td>
						<td>'.$datos[$s]['nombreMarca'].'</td>
						<td>'.$datos[$s]['nombreTemporadaCompra'].'</td>
						<td>'.$datos[$s]['nombreEsquemaProducto'].'</td>
						<td>'.$datos[$s]['eventoCompra'].'</td>
						<td>'.$datos[$s]['referenciaProducto'].'</td>
						<td>'.$datos[$s]['nombreLargoProducto'].'</td>
						<td>'.$datos[$s]['fechaCompra'].'</td>
						<td>'.$datos[$s]['compradorVendedorCompra'].'</td>
						<td>'.$datos[$s]['nombreProveedorCompra'].'</td>
						<td>'.$datos[$s]['nombreClienteCompra'].'</td>
						<td>'.$datos[$s]['nombreCiudadCompra'].'</td>
						<td>'.$datos[$s]['embarque'].'</td>
						<td>'.$datos[$s]['fechaDeliveryCompra'].'</td>
						<td>'.$datos[$s]['fechaReservaEmbarqueDetalle'].'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['volumenFacturaEmbarqueDetalle'],2,".",",").'</td>
						<td>'.$datos[$s]['fechaRealEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['fechaLlegadaZonaFrancaEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['numeroEmbarque'].'</td>
						<td>'.$datos[$s]['observacionEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['facturaEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['blEmbarqueDetalle'].'</td>
						<td>'.$datos[$s]['fechaElaboracionEmbarque'].'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['precioListaMovimientoDetalle'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['cantidadPedido'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['cantidadFactura'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['cantidadPendiente'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['cumplimientoCantidad'],2,".",",").'%</td>
						<td style="text-align:right;">'.number_format($datos[$s]['valorPedido'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['valorFactura'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['valorPendiente'],2,".",",").'</td>
						<td style="text-align:right;">'.number_format($datos[$s]['cumplimientoValor'],2,".",",").'%</td>
					</tr>';

					#Sumo los valores por compras
					$volumen += $datos[$s]['volumenFacturaEmbarqueDetalle'];
					$cantidadPedido += $datos[$s]['cantidadPedido'];
					$cantidadFactura += $datos[$s]['cantidadFactura'];
					$cantidadPendiente += $datos[$s]['cantidadPendiente'];
					$cantidadCumplimiento += $datos[$s]['cumplimientoCantidad'];
					$cantidadDatos += count($datos[$s]['cumplimientoCantidad']);
					$valorPedido += $datos[$s]['valorPedido'];
					$valorFactura += $datos[$s]['valorFactura'];
					$valorPendiente += $datos[$s]['valorPendiente'];
					$valorCumplimiento += $datos[$s]['cumplimientoValor'];

					$s++;

					
				}
					
				#Saco el promedio del cumplimiento de las cantidades y los valores de la compra
				$cantidadCumplimiento = $cantidadCumplimiento / ($cantidadDatos == 0 ? 1 : $cantidadDatos);
				$valorCumplimiento = $valorCumplimiento / ($cantidadDatos == 0 ? 1 : $cantidadDatos);

				#Imprimo los valores de las compras
				echo '
				<tr>
					<th colspan="18">TOTALES DE LA COMPRA '.$compra.'</th>
					<th style="text-align:right;" colspan="1">'.number_format($volumen,2,".",",").'</th>
					<th colspan="7"></th>
					<th style="text-align:right;" colspan="1">'.number_format($cantidadPedido,2,".",",").'</th>
					<th style="text-align:right;" colspan="1">'.number_format($cantidadFactura,2,".",",").'</th>
					<th style="text-align:right;" colspan="1">'.number_format($cantidadPendiente,2,".",",").'</th>
					<th style="text-align:right;" colspan="1">'.number_format($cantidadCumplimiento,2,".",",").'%</th>
					<th style="text-align:right;" colspan="1">'.number_format($valorPedido,2,".",",").'</th>
					<th style="text-align:right;" colspan="1">'.number_format($valorFactura,2,".",",").'</th>
					<th style="text-align:right;" colspan="1">'.number_format($valorPendiente,2,".",",").'</th>
					<th style="text-align:right;" colspan="1">'.number_format($valorCumplimiento,2,".",",").'%</th>
				</tr>';

				

				#Sumo los valores por clientes
					$volumenCliente += $volumen;
					$cantidadPedidoCliente += $cantidadPedido;
					$cantidadFacturaCliente += $cantidadFactura;
					$cantidadPendienteCliente += $cantidadPendiente;
					$cantidadCumplimientoCliente += $cantidadCumplimiento;
					$cantidadDatosCliente += count($compra);
					$valorPedidoCliente += $valorPedido;
					$valorFacturaCliente += $valorFactura;
					$valorPendienteCliente += $valorPendiente;
					$valorCumplimientoCliente += $valorCumplimiento;

			}
			#Saco el promedio del cumplimiento de las cantidades y los valores
			$cantidadCumplimientoCliente = $cantidadCumplimientoCliente / ($cantidadDatosCliente == 0 ? 1 : $cantidadDatosCliente);
			$valorCumplimientoCliente = $valorCumplimientoCliente / ($cantidadDatosCliente == 0 ? 1 : $cantidadDatosCliente);

			#Sumo los valores totales
			$totalVolumen += $volumenCliente;
			$totalCantidadPedido += $cantidadPedidoCliente;
			$totalCantidadFactura += $cantidadFacturaCliente;
			$totalCantidadPendiente += $cantidadPendienteCliente;
			$totalValorPedido += $valorPedidoCliente;
			$totalValorFactura += $valorFacturaCliente;
			$totalValorPendiente += $valorPendienteCliente;

			$totalCantidadCumplimiento += $cantidadCumplimientoCliente;
			$totalValorCumplimiento += $valorCumplimientoCliente;

			#Imprimo los valores por cliente
			echo '
			<tr>
				<th colspan="18">TOTALES DEL CLIENTE '.$cliente.'</th>
				<th style="text-align:right;" colspan="1">'.number_format($volumenCliente,2,".",",").'</th>
				<th colspan="7"></th>
				<th style="text-align:right;" colspan="1">'.number_format($cantidadPedidoCliente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($cantidadFacturaCliente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($cantidadPendienteCliente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($cantidadCumplimientoCliente,2,".",",").'%</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorPedidoCliente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorFacturaCliente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorPendienteCliente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($valorCumplimientoCliente,2,".",",").'%</th>
			</tr>';
		}
		$totalCantidadDatos += count($cliente);
		
	}	 
		$totalCantidadCumplimiento = $totalCantidadCumplimiento / ($totalCantidadDatos == 0 ? 1 : $totalCantidadDatos);
		$totalValorCumplimiento = $totalValorCumplimiento / ($totalCantidadDatos == 0 ? 1 : $totalCantidadDatos);
	
	#Imprimo los valores totales
	echo'
			<tr>
				<th colspan="18" style="color: red;">TOTALES GENERALES</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalVolumen,2,".",",").'</th>
				<th colspan="7"></th>
				<th style="text-align:right;" colspan="1">'.number_format($totalCantidadPedido,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalCantidadFactura,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalCantidadPendiente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalCantidadCumplimiento,2,".",",").'%</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalValorPedido,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalValorFactura,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalValorPendiente,2,".",",").'</th>
				<th style="text-align:right;" colspan="1">'.number_format($totalValorCumplimiento,2,".",",").'%</th>
			</tr>
		</table>';

  ?>

 
</div>

  		
{!!Form::close()!!}
@stop