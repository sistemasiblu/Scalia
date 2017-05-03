@extends('layouts.formato')

<title>Compras - Forward</title>
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
    <div class="col-md-11"><center><h1>Compras - Forward</h1></center></div>
  </div>

  <?php
  	if ($visualizacion == 'excel') 
  	{
	    header('Content-type: application/vnd.ms-excel');
	    header("Content-Disposition: attachment; filename=Informe_".$filtro."_".date('Y-m-d').".xls");
	    header("Pragma: no-cache");
	    header("Expires: 0");
  	}
	if ($filtro == 'forward') 
  	{
		echo '
  		<table table class="table table-striped table-bordered table-hover" style="width:100%;">';

  		$i = 0;
  		$comprasTotal = 0;
  		$forwardTotal = 0;
  		$diferenciaTotalP = 0;
  		$diferenciaTotalN = 0;
  		$total = count($datos);

  		while ($i < $total) 
  		{
  			$forward = $datos[$i]['numeroForward'];
  			$valorCompras = 0;
  			$valorForward = 0;

  			
			if ($datos[$i]['numeroCompra'] == '' && $datos[$i]['nombreTemporadaCompra'] == '') 
			{
				echo '
			  	<tr>
					<th colspan="1" style=" background-color:#585858; color:white;">Forward '.$datos[$i]['numeroForward'].'</th>
					<th colspan="1" style=" background-color:#585858; color:white;">Descripción '.$datos[$i]['descripcionForward'].'</th>
					<th colspan="1" style=" background-color:#585858; color:white;">Vencimiento '.$datos[$i]['fechaVencimientoForward'].'</th>
					<th colspan="1" style=" background-color:#585858; color:white;">'.number_format($datos[$i]['valorDolarForward'],2,".",",").'</th>
				</tr>';

				$forwardTotal += $datos[$i]['valorDolarForward'];

				$i++;
			}
			else
			{
				echo '
			  	<tr>
					<th colspan="1" style=" background-color:#255986; color:white;">Forward '.$datos[$i]['numeroForward'].'</th>
					<th colspan="1" style=" background-color:#255986; color:white;">Descripción '.$datos[$i]['descripcionForward'].'</th>
					<th colspan="1" style=" background-color:#255986; color:white;">Vencimiento '.$datos[$i]['fechaVencimientoForward'].'</th>
					<th colspan="1" style=" background-color:#255986; color:white;">'.number_format($datos[$i]['valorDolarForward'],2,".",",").'</th>
				</tr>';

				echo '
				<tr>
					<td><b>PI</b></td>
					<td><b>Temporada</b></td>
					<td><b>Proveedor</b></td>
					<td><b>Valor FOB</b></td>
				<tr>';

				while ($i < $total and $forward == $datos[$i]["numeroForward"])
				{
					echo '
					<tr>
						<td>'.$datos[$i]['numeroCompra'].'</td>
						<td>'.$datos[$i]['nombreTemporadaCompra'].'</td>
						<td>'.$datos[$i]['nombreProveedorCompra'].'</td>
						<td style="text-align:right;">'.number_format($datos[$i]['valorCompra'],2,".",",").'</td>
					</tr>';

					$valorCompras += $datos[$i]['valorCompra'];
					$valorForward += $datos[$i]['valorDolarForward'];

					$diferencia = $datos[$i]['valorDolarForward'] - $valorCompras;

					$i++;
				}

				echo '
				<tr>
					<th colspan="3">TOTALES DEL FORWARD '.$forward.'</th>
					<th style="text-align:right;" colspan="1">'.number_format($valorCompras,2,".",",").'</th>
				</tr>
				<tr>
					<th colspan="3" style="color:blue;">DIFERENCIA ENTRE EL VALOR DEL FORWARD '.$forward.' Y LA SUMATORIA DE SUS COMPRAS</th>
					<th style="text-align:right; color:blue;" colspan="1">'.number_format($diferencia,2,".",",").'</th>
				</tr>';

				$forwardTotal += $valorForward;
				$comprasTotal += $valorCompras;
				if ($diferencia > 0) 
					$diferenciaTotalP += $diferencia;	
				else
					$diferenciaTotalN += $diferencia;

			}
		}


			echo '
				<tr>
					<th colspan="4"></b></th>
				</tr>
				<tr>
					<th colspan="3"><b>TOTAL DE TODOS LOS FORWARDS</b></th>
					<th style="text-align:right;" colspan="1"><b>'.number_format($forwardTotal,2,".",",").'</b></th>
				</tr>
				<tr>
					<th colspan="3" style="color:green;"><b>SALDO POSITIVO</b></th>
					<th style="text-align:right; color:green;" colspan="1"><b>'.number_format($diferenciaTotalP,2,".",",").'</b></th>
				</tr>
				<tr>
					<th colspan="3" style="color:red;"><b>SALDO NEGATIVO<b></th>
					<th style="text-align:right; color:red;" colspan="1"><b>'.number_format($diferenciaTotalN,2,".",",").'</b></th>
				</tr>';
	}
	else
	{
  		echo '
  		<table table class="table table-striped table-bordered table-hover" style="width:100%;">';

  		$i = 0;
  		$total = count($datos);

  		while ($i < $total) 
  		{
  			$compra = $datos[$i]['numeroCompra'];

  			if ($datos[$i]['numeroForward'] == '') 
			{
				echo '
			  	<tr>
					<th colspan="1" style=" background-color:#585858; color:white;">Compra '.$datos[$i]['numeroCompra'].'</th>
					<th colspan="1" style=" background-color:#585858; color:white;">Temporada '.$datos[$i]['nombreTemporadaCompra'].'</th>
					<th colspan="1" style=" background-color:#585858; color:white;">Proveedor '.$datos[$i]['nombreProveedorCompra'].'</th>
					<th colspan="1" style=" background-color:#585858; color:white;">'.number_format($datos[$i]['valorCompra'],2,".",",").'</th>
				</tr>';
				$i++;
			}
			else
			{

	  			echo '
			  	<tr>
					<th colspan="1" style=" background-color:#255986; color:white;">Compra '.$datos[$i]['numeroCompra'].'</th>
					<th colspan="1" style=" background-color:#255986; color:white;">Temporada '.$datos[$i]['nombreTemporadaCompra'].'</th>
					<th colspan="1" style=" background-color:#255986; color:white;">Proveedor '.$datos[$i]['nombreProveedorCompra'].'</th>
					<th colspan="1" style=" background-color:#255986; color:white;">'.number_format($datos[$i]['valorCompra'],2,".",",").'</th>
				</tr>';

				echo '
				<tr>
					<td><b>Forward</b></td>
					<td><b>Descripción</b></td>
					<td><b>Vencimiento</b></td>
					<td><b>Valor</b></td>
				<tr>';

				while ($i < $total and $compra == $datos[$i]["numeroCompra"])
				{
					echo '
					<tr>
						<td>'.$datos[$i]['numeroForward'].'</td>
						<td>'.$datos[$i]['descripcionForward'].'</td>
						<td>'.$datos[$i]['fechaVencimientoForward'].'</td>
						<td style="text-align:right;">'.number_format($datos[$i]['valorDolarForward'],2,".",",").'</td>
					</tr>';


					$i++;
				}
			}
  		}
  	}

  ?>

 
</div>

  		
{!!Form::close()!!}
@stop