@extends('layouts.formato')

<title>Certificado laboral</title>
@section('contenido')

{!!Form::model($certificado)!!}
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
  

    $camposcertificado = array();
  	// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($certificado); $i < $c; ++$i) 
       {
          $camposcertificado[$i] = (array) $certificado[$i];
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
    font-size: 12pt;
  }

  table
  {
    font-size: 11pt;
  }
</STYLE>


<div>
  <!-- IMPRIMO EL ENCABEZADO DEL RECIBO DE PAGO -->
  <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
    <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
    <div class="col-md-11"><center><h1>Certificado laboral</h1></center></div>
  </div>

  <?php
    setlocale(LC_TIME, 'spanish');
    $mes = strftime("%B",mktime(0, 0, 0, date('m'), 1, 2000));

    echo 'Medellín, '.$mes.' '.date('d').' de '.date('Y').'<br><br><br>

    Señores(as):<br>
    <b>'.$camposcertificado[0]['destinatarioCertificado'].'</b><br><br><br>

    Cordial saludo:<br><br><br>

    Les informamos que '.($camposcertificado[0]['sexoTercero'] == 'Masculino' ? 'el señor ' : 'la señora').'<b>'.$camposcertificado[0]['nombre1Tercero'].'</b> identificado(a) con '.$camposcertificado[0]['nombreIdentificacion'].' No. <b>'.$camposcertificado[0]['documentoTercero'].'</b> ha suscrito los siguientes contratos de trabajo con nuestra compañía desempeñando el cargo de <b>'.$camposcertificado[0]['nombreCargo'].'</b>:<br><br><br>


      <table table class="table table-striped table-bordered table-hover" style="width:100%;">
        <thead class="thead-inverse">
          <tr class="table-info">
            <th>Número</th>
            <th>Tipo de Contrato</th>
            <th>Salario</th>
            <th>Inicio</th>
            <th>Fin</th>
          </tr>
        </thead>
        <tbody>';
          for ($i=0; $i < count($certificado); $i++) 
          { 
            $certificadol = get_object_vars($certificado[$i]);
            echo '
            <tr>
              <td>'.$certificadol["codigoAlternoContrato"].'</td>
              <td>'.$certificadol["nombreTipoContrato"].'</td>
              <td style="text-align:right;">'.number_format($certificadol["valorContrato"],2,".",",").'</td>
              <td>'.$certificadol["fechaInicioContrato"].'</td>
              <td>'.$certificadol["fechaTerminacionContrato"].'</td>
            </tr>';
          }
      echo '
      </tbody>
    </table>
    <br><br>

    La presente constancia se expide a petición '.($camposcertificado[0]['sexoTercero'] == 'Masculino' ? 'del interesado' : 'de la interesada').'.<br><br><br><br>


    Atentamente,<br><br><br><br><br>     



    ________________________________<br>
    <b>MILENA M. VILLAMIZAR REYES</b><br>
    Directora Gestión Humana';
  ?>

</div>