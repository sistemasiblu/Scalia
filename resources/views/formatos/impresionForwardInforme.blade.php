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
    <div class="col-md-11"><center><h1>Forward</h1></center></div>
  </div>

  <?php

$s = 0;
$dolarForward = '';
$tasaForward = '';
$tasaInicialForward = '';
$pesosForward = '';
$devaluacionForward = '';
$spotForward = '';
$total = count($consulta);
  echo '
  	<table table class="table table-striped table-bordered table-hover" style="width:100%;">
	  	<tr>
			<th colspan="12" style=" background-color:#255986; color:white;"></th>
		</tr>
		<tr>
			<td><b>Número de forward</b></td>
			<td><b>Fecha Negociación</b></td>
			<td><b>Modalidad</b></td>
			<td><b>Banco</b></td>
      <td><b>Fecha Vencimiento</b></td>
      <td><b>Valor USD</b></td>
      <td><b>Tasa FW</b></td>
      <td><b>Tasa Inicial</b></td>
      <td><b>Valor Pesos</b></td>
      <td><b>Devaluación</b></td>
      <td><b>Spot</b></td>
      <td><b>Forward Padre</b></td>
		<tr>';

	while ($s < $total) 
	{  	
		echo'
		</tr>
			<td>'.$datos[$s]['numeroForward'].'</td>
      <td>'.$datos[$s]['fechaNegociacionForward'].'</td>
      <td>'.$datos[$s]['modalidadForward'].'</td>
      <td>'.$datos[$s]['nombre1Tercero'].'</td>
      <td>'.$datos[$s]['fechaVencimientoForward'].'</td>
			<td style="text-align:right;">'.number_format($datos[$s]['valorDolarForward'],2,".",",").'</td>
      <td style="text-align:right;">'.number_format($datos[$s]['tasaForward'],2,".",",").'</td>
      <td style="text-align:right;">'.number_format($datos[$s]['tasaInicialForward'],2,".",",").'</td>
      <td style="text-align:right;">'.number_format($datos[$s]['valorPesosForward'],2,".",",").'</td>
      <td style="text-align:right;">'.number_format($datos[$s]['devaluacionForward'],2,".",",").'</td>
      <td style="text-align:right;">'.number_format($datos[$s]['spotForward'],2,".",",").'</td>
      <td>'.$datos[$s]['numeroForwardPadre'].'</td>
		</tr>';

    $dolarForward += $datos[$s]['valorDolarForward'];
    $tasaForward += $datos[$s]['tasaForward'];
    $tasaInicialForward += $datos[$s]['tasaInicialForward'];
    $pesosForward += $datos[$s]['valorPesosForward'];
    $devaluacionForward += $datos[$s]['devaluacionForward'];
    $spotForward += $datos[$s]['spotForward'];

		$s++;
	}	  
		echo'
    <tr>
        <th colspan="5">TOTALES DE LOS FORWARDS</th>
        <th style="text-align:right;" colspan="1">'.number_format($dolarForward,2,".",",").'</th>
        <th style="text-align:right;" colspan="1">'.number_format($tasaForward,2,".",",").'</th>
        <th style="text-align:right;" colspan="1">'.number_format($tasaInicialForward,2,".",",").'</th>
        <th style="text-align:right;" colspan="1">'.number_format($pesosForward,2,".",",").'</th>
        <th style="text-align:right;" colspan="1">'.number_format($devaluacionForward,2,".",",").'</th>
        <th style="text-align:right;" colspan="1">'.number_format($spotForward,2,".",",").'</th>
        <th colspan="1"></th>
    </tr>        
	</table>';

  ?>

 
</div>

  		
{!!Form::close()!!}
@stop