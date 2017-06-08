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

    $formato.='Medellín, '.$mes.' '.date('d').' de '.date('Y').'<br><br><br>

    Señores(as):<br>
    <b>'.$camposcertificado[0]['destinatarioCertificado'].'</b><br><br><br>

    Cordial saludo:<br><br><br>

    Les informamos que '.($camposcertificado[0]['sexoTercero'] == 'Masculino' ? 'el señor ' : 'la señora').'<b>'.$camposcertificado[0]['nombre1Tercero'].'</b> identificado(a) con '.$camposcertificado[0]['nombreIdentificacion'].' No. <b>'.$camposcertificado[0]['documentoTercero'].'</b> ha suscrito los siguientes contratos de '.($camposcertificado[0]['fechaTerminacionContrato'] == 'Vigente' ? 'labora' : 'laboró').' en nuestra compañía desempeñando el cargo de <b>'.$camposcertificado[0]['nombreCargo'].'</b>:<br><br><br>


      <table table class="table table-striped table-bordered table-hover" style="width:100%;">
        <thead class="thead-inverse">
          <tr class="table-info">
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
            $formato.='
            <tr>
              <td>'.$certificadol["nombreTipoContrato"].'</td>
              <td style="text-align:right;">'.number_format($certificadol["valorContrato"],2,".",",").'</td>
              <td>'.$certificadol["fechaInicioContrato"].'</td>
              <td>'.$certificadol["fechaTerminacionContrato"].'</td>
            </tr>';
          }
      $formato.='
      </tbody>
    </table>
    <br><br>

    La presente constancia se expide a petición '.($camposcertificado[0]['sexoTercero'] == 'Masculino' ? 'del interesado' : 'de la interesada').'.<br><br><br><br>


    Atentamente,<br><br><br><br><br>     



    <img src="http://'.$_SERVER["HTTP_HOST"].'/imagenes/kiosko/Firma_Milena.png" style="width:40%;"><br>
    ________________________________<br>
    <b>MILENA M. VILLAMIZAR REYES</b><br>
    Directora Gestión Humana

    </body>
    </html>';

    echo $formato;

    //creamos un archivo (fopen) extension html
    $arch = fopen(public_path().'/certificadolaboral.html', "w");

    // escribimos en el archivo todo el HTML del informe (fputs)
    fputs ($arch, $formato);

    // cerramos el archivo (fclose)
    fclose($arch);

    // enviamos un correo con la informacion del certificado y le adjuntamos el archivo que acabamos de crear

    $correo = array();
    $correo['asunto'] = 'Certificado laboral '.$camposcertificado[0]['nombre1Tercero'];
    $correo['destinatario'] = $camposcertificado[0]['correoElectronicoTercero'];
    $correo['mensaje'] = 'Certificado laboral generado en Kiosko - Scalia.';

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
              $msj->attach(public_path().'/certificadolaboral.html'); 
          }); 
      }
    }
  ?>

</div>