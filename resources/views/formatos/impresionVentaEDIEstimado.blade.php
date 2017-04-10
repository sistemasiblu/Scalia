@extends('layouts.formato')


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
  

    $campos = array();
    $informe = array();
    // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
    for ($i = 0, $c = count($consulta); $i < $c; ++$i) 
    {
       $informe[$i] = (array) $consulta[$i];
    }

?>

<div>
  <!-- IMPRIMO EL NUMERO DE LA COMPRA -->
    <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
      <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
      <div class="col-md-11"><center><h1>Informe de Proyección de Ventas</h1></center></div>
    </div>

    </br> </br> </br>

    <div class="list-group" style="border:1px;">
		<div class="panel panel-primary">
		  <div class="panel-heading" style="height:45px;"><h4>Informe de Proyección de Ventas</h4></div>
		  
			<div class="panel panel-primary">
				   <div class="panel-body">
					
					<table class="table table-striped table-bordered table-hover" style="width:100%; overflow:scroll;">
					  <tr>
					    <th>Referencia</th>
					    <th>Código EAN</th>
					    <th>Descripcion</th>
					    <th>Cantidad Venta EDI</th>
					    <th>Cantidad Inventario EDI</th>
					    <th>Bodega IBLU</th>
					    <th>Cantidad Entrada ZF</th>
					    <th>Cantidad Facturada</th>
					    <th>Compras Pendientes (prov)</th>
					    <th>Pedidos Pendientes (cliente)</th>
					    <th>Cantidad Fisica</th>
					    <th>Cantidad Disponible</th>
					    <th>Dias de venta</th>
					    <th>Fecha Inicial</th>
					    <th>Dias Inventario</th>
					    <th>Dias para meta de venta</th>
					    <th>Dias Proyectados Venta </th>
					    <th>Unidades promedio por semana </th>
					  </tr>

					  <?php
						$total = 0;

						for ($i=0; $i < count($informe); $i++) 
						{ 
							echo '
							<tr>
							    <td>'.$informe[$i]['referenciaProducto'].'</td>
							    <td>'.$informe[$i]['codigoBarrasProducto'].'</td>
							    <td>'.$informe[$i]['nombreLargoProducto'].'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['cantidadVentaEDIDetalle'],2,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['cantidadInventarioEDIDetalle'],2,".",",").'</td>
							    <td style="text-align:right;">'.$informe[$i]['codigoAlternoBodega'].'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['cantidadCompra'],0,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['cantidadVenta'],0,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['compraPendiente'],0,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['pedidoPendiente'],0,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['cantidadFisica'],0,".",",").'</td>
							    <td style="text-align:right;">'.number_format(($informe[$i]['cantidadFisica']-$informe[$i]['pedidoPendiente']),0,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['diasVenta'],2,".",",").'</td>
							    <td style="text-align:right;">'.$informe[$i]['fechaInicialVenta'].'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['diasInventario'],2,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['diasMetaVenta'],2,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['diasEstimadosVenta'],0,".",",").'</td>
							    <td style="text-align:right;">'.number_format($informe[$i]['unidadesPromedioSemana'],0,".",",").'</td>						   
							</tr>
							';
							$total += $informe[$i]['cantidadInventarioEDIDetalle'];						    
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