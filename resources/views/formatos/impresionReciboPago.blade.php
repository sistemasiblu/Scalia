@extends('layouts.formato')

<title>Comprobante de Nomina</title>
@section('contenido')

{!!Form::model($recibo)!!}
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
  

    $camposrecibo = array();
  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($recibo); $i < $c; ++$i) 
       {
          $camposrecibo[$i] = (array) $recibo[$i];
       }
?>

<STYLE>
  h1.SaltoDePagina
  {
    PAGE-BREAK-AFTER: always
  }
  body
  {
    font-family: "Times New Roman";
    font-size: 10pt;
  }
</STYLE>


<div>
  <!-- IMPRIMO EL ENCABEZADO DEL RECIBO DE PAGO -->
  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
    <div class="col-md-11"><center><h1>Comprobante de nomina</h1></center></div>
  </div>

  <?php
    $formato = '
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        </head>
        <body>';
    $reg = 0;
    $totalreg = count($recibo);
    
   

    while ($reg < $totalreg) 
    { 
      $formato.='
      <table table class="table table-striped table-bordered table-hover" style="width:100%;">';

      $totalDevengado = 0;
      $totalDeduccion = 0;
      $totalPagar = 0;
      $liquidacion = $camposrecibo[$reg]['idLiquidacionNomina'];
      $formato.='
      <tr>
        <td colspan="11"><b>Liquidación: </b>'.$camposrecibo[$reg]['numeroLiquidacionNomina'].'</td>
      </tr>

      <tr>
        <td colspan="5"><b>Desde: </b>'.$camposrecibo[$reg]['fechaInicioLiquidacionNomina'].'</td>
        <td colspan="6"><b>Hasta: </b>'.$camposrecibo[$reg]['fechaFinLiquidacionNomina'].'</td>
      </tr>

      <tr>
        <td colspan="5"><b>Documento: </b>'.$camposrecibo[0]['documentoTercero'].'</td>
        <td colspan="6"><b>Empleado: </b>'.$camposrecibo[0]['nombre1Tercero'].'</td>
      </tr>

      <tr>
        <td colspan="5"><b>Salario Básico: </b>'.number_format($camposrecibo[0]["valorContrato"],0,".",",").'</td>
        <td colspan="6"><b>Fecha Inicio Contrato: </b>'.$camposrecibo[0]['fechaInicioContrato'].'</td>
      </tr>

      <tr>
        <td colspan="5"><b>Cargo: </b>'.$camposrecibo[0]['nombreCargo'].'</td>
        <td colspan="6"><b>Centro Trabajo: </b>'.$camposrecibo[0]['nombreCentroTrabajo'].'</td>
      </tr>';

      $titDet = '<tr>
        <th colspan="5">DEVENGADO</th>
        <th colspan="6">DEDUCCION</th>
      </tr>
        <tr>
          <td>Concepto</td>
          <td>Horas / Días</td>
          <td>Base</td>
          <td>%</td>
          <td>Valor Total</td>
        </tr>';

      $dev = '<table table class="table table-striped table-bordered table-hover" style="width:100%;">
                <tr>
        <th colspan="5">DEVENGADO</th>
      </tr>
        <tr>
          <td><b>Concepto</b></td>
          <td><b>Horas / Días</b></td>
          <td><b>Base</b></td>
          <td><b>%</b></td>
          <td><b>Valor Total</b></td>
        </tr>';
      $ded = '<table table class="table table-striped table-bordered table-hover" style="width:100%;">
                <tr>
        <th colspan="6">DEDUCCION</th>
      </tr>
        <tr>
          <td><b>Concepto</b></td>
          <td><b>Horas / Días</b></td>
          <td><b>Base</b></td>
          <td><b>%</b></td>
          <td><b>Valor Total</b></td>
        </tr>';

      while ($reg < $totalreg && $liquidacion == $camposrecibo[$reg]['idLiquidacionNomina']) 
      {
        $concepto = $camposrecibo[$reg]['naturalezaConceptoNomina'];
          
        while ($reg < $totalreg && $liquidacion == $camposrecibo[$reg]['idLiquidacionNomina'] && $concepto == $camposrecibo[$reg]['naturalezaConceptoNomina']) 
        {
          if ($camposrecibo[$reg]['naturalezaConceptoNomina'] == 'DEVENGADO') 
          {
            $dev .=  '
            <tr>
              <td>'.$camposrecibo[$reg]["nombreConceptoNomina"].'</td>
              <td style="text-align:right;">'.number_format($camposrecibo[$reg]["horasLiquidacionNominaDetalle"],2,".",",")." / ".($camposrecibo[$reg]["horasLiquidacionNominaDetalle"]/8).'</td>
              <td style="text-align:right;">$'.number_format($camposrecibo[$reg]["baseLiquidacionNominaDetalle"],0,".",",").'</td>
              <td style="text-align:right;">'.number_format($camposrecibo[$reg]["porcentajeLiquidacionNominaDetalle"],2,".",",").'</td>
              <td style="text-align:right;">$'.number_format($camposrecibo[$reg]["valorLiquidacionNominaDetalle"],0,".",",").'</td>
            </tr>';

            $totalDevengado += $camposrecibo[$reg]["valorLiquidacionNominaDetalle"];
          }
          else
          {
            $ded .=  '
            <tr>
              <td>'.$camposrecibo[$reg]["nombreConceptoNomina"].'</td>
              <td style="text-align:right;">'.number_format($camposrecibo[$reg]["horasLiquidacionNominaDetalle"],2,".",",")." / ".($camposrecibo[$reg]["horasLiquidacionNominaDetalle"]/8).'</td>
              <td style="text-align:right;">$'.number_format($camposrecibo[$reg]["baseLiquidacionNominaDetalle"],0,".",",").'</td>
              <td style="text-align:right;">'.number_format($camposrecibo[$reg]["porcentajeLiquidacionNominaDetalle"],2,".",",").'</td>
              <td style="text-align:right;">$'.number_format($camposrecibo[$reg]["valorLiquidacionNominaDetalle"],0,".",",").'</td>
            </tr>';

            $totalDeduccion += $camposrecibo[$reg]["valorLiquidacionNominaDetalle"];
          }
          $reg++;
        }
      }

      $dev .= '
        <tr>
          <th colspan="4">Total DEVENGADO</th>
          <th style="text-align:right;" colspan="1">$'.number_format($totalDevengado,0,".",",").'</th>
        </tr></table>';

      $ded .= '<tr>
          <th colspan="4">Total DEDUCCION</th>
          <th style="text-align:right;" colspan="1">$'.number_format($totalDeduccion,0,".",",").'</th>
        </tr></table>';

      $formato.='
        <tr>
          <td colspan="5">'.$dev.'</td>
          <td colspan="6">'.$ded.'</td>
        </tr>';
        $totalPagar = $totalDevengado - $totalDeduccion;


        $formato.='
        <tr>
          <th colspan="10">Total a pagar '.$camposrecibo[0]['nombre1Tercero'].'</th>
          <th style="text-align:right;" colspan="1">$'.number_format($totalPagar,0,".",",").'</th>
        </tr>';
        $formato.='</table>

        <h1 class="SaltoDePagina"></h1>
        </body>
      </html>';
    }

    echo $formato;

    //creamos un archivo (fopen) extension html
    $arch = fopen(public_path().'/recibopago.html', "w");

    // escribimos en el archivo todo el HTML del informe (fputs)
    fputs ($arch, $formato);

    // cerramos el archivo (fclose)
    fclose($arch);

    // enviamos un correo con la informacion del certificado y le adjuntamos el archivo que acabamos de crear

    $correo = array();
    $correo['asunto'] = 'Recibo de pago '.$camposrecibo[0]['nombre1Tercero'];
    $correo['destinatario'] = $camposrecibo[0]['correoElectronicoTercero'];
    $correo['mensaje'] = 'Recibo de pago generado en Kiosko - Scalia.';

    if ($mail == 'si') 
    {
      if ($correo['destinatario'] == 'NULL') 
      {
        echo '<script>alert("No tiene un correo electrónico asociado.")</script>';
      }
      else
      {
        Mail::send('emails.contact',$correo,function($msj) use ($correo)
          {
              $msj->to($correo['destinatario']);
              $msj->subject($correo['asunto']);
              $msj->attach(public_path().'/recibopago.html'); 
          }); 
      }
    }
    
  ?>

</div>