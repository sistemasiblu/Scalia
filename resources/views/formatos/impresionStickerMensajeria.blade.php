@extends('layouts.formato')

<title>Mensajería</title>
@section('contenido')

{!!Form::model($remitente)!!}
{!!Html::script('js/barcode.js'); !!}
<style type="text/css">
    @font-face {
        font-family: "EanP36Tt";
        src: url("fuentes/EAN13_36.TTF");
    }

    @font-face {
        font-family: "BarCode128";
        src: url("fuentes/Bc128c.ttf");
    }

    H1.SaltoDePagina
    {
        PAGE-BREAK-AFTER: always
    }

    body { font: 0.9em Century Gothic; background-color:#FFF;}
    td {font: 1.2em Century Gothic;}
    label {font: 1.2em Century Gothic; font-weight: bold;}
</style>

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
       $logo =  '<img src="data:image/png;base64,' . $base64 .'" alt="Texto alternativo" style="width:3cm; height=1.5cm;"/>';
    }
    return $logo;
}    
    $img = base64('imagenes/Logo_iblu.png');

    $codigo = get_object_vars($codigoR[0]);
    $remite = get_object_vars($remitente[0]);
    $destino = get_object_vars($destinatario[0]);

?>

<div style="width:10cm; height:5cm; border:1px solid;">
  <div style="display:table-cell; width:60%; height:100%; font-size: 14px;  font-family: Arial, Helvetica, sans-serif;">
    <?php 
      echo '<b>Remitente:</b></br>'.$remite["nombre1Tercero"].'</br>
      '.$remite["direccionTercero"].'</br>
      '.$remite["telefono1Tercero"].'</br></br>
      <b>Destinatario:</b></br>'.$destino["nombre1Tercero"].'
      </br>
      '.$destino["direccionTercero"].'</br>
      '.$destino["seccionEntregaMensajeria"].'</br></br>'

    ?>

  </div>
  <div style="display:table-cell; width:30%; height:25%;">
    <?php echo $img.
    '<br><br>';
    echo '<div id="codigo128_'.$codigo["codigoRadicado"].'" style="display:inline-block; float: right; width:3.2cm; height:0.9cm; font-size: 10px;"></div>
    <script>
      $("#codigo128_'.$codigo["codigoRadicado"].'").barcode("'.$codigo["codigoRadicado"].'", "code128"  , {barWidth:1, barHeight:20});
    </script>';
  ?>
  </div>
</div>


{!!Form::close()!!}
@stop
