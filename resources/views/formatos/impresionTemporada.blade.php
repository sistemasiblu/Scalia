@extends('layouts.formato')

<title>Temporadas</title>
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
    <div class="col-md-11"><center><h1>Temporadas</h1></center></div>
  </div>

  <?php

$s = 0;
$totalTemp = '';
$totalUnid = '';
$total = count($consulta);
  echo '
  	<table table class="table table-striped table-bordered table-hover" style="width:100%;">
	  	<tr>
			<th colspan="5" style=" background-color:#255986; color:white;"></th>
		</tr>
		<tr>
			<td><b>Temporada</b></td>
			<td><b>Valor</b></td>
      <td><b>Unidades</b></td>
			<td><b>Fecha Inicial</b></td>
			<td><b>Fecha Final</b></td>
		<tr>';

	while ($s < $total) 
	{  	
		echo'
		</tr>
			<td>'.$datos[$s]['nombreTemporada'].'</td>
			<td style="text-align:right;">'.number_format($datos[$s]['valorCompra'],2,".",",").'</td>
      <td style="text-align:right;">'.number_format($datos[$s]['cantidadCompra'],2,".",",").'</td>
			<td>'.$datos[$s]['fechaInicialTemporada'].'</td>
			<td>'.$datos[$s]['fechaFinaltemporada'].'</td>
		</tr>';

    $totalTemp += $datos[$s]['valorCompra'];
    $totalUnid += $datos[$s]['cantidadCompra'];

		$s++;
	}	  
		echo'
    <tr>
        <th colspan="1">TOTALES DE LAS TEMPORADAS</th>
        <th style="text-align:right;" colspan="1">'.number_format($totalTemp,2,".",",").'</th>
        <th style="text-align:right;" colspan="1">'.number_format($totalUnid,2,".",",").'</th>
        <th colspan="2"></th>
    </tr>        
	</table>';

  ?>

 
</div>

  		
{!!Form::close()!!}
@stop