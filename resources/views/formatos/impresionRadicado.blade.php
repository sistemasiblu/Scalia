@extends('layouts.formato')

<title>Impresion de Radicado</title>
@section('contenido')

{!!Form::model($codigoRadicado)!!}

<!-- <script src="../view/js/jquery-barcode-2.0.2.js"></script>   -->
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
       $logo =  '<img src="data:image/png;base64,' . $base64 .'" alt="Texto alternativo" style="width:1.8cm; height=0.74cm;"/>';
    }
    return $logo;
}    
    $img = base64('imagenes/Logo_iblu.png');

    $codigo = str_replace('"RADICADO No ', "", $codigoRadicado);
    $codigo = substr($codigo, 0, strlen($codigo)-1);

    $etiqueta = $_GET['etiqueta'];

    if ($etiqueta == 'derecha') 
      $posicion = 'float: right; margin-right:-0.4cm;';
    else
      $posicion = 'float: left; margin-left:0.3cm;';

?>

<div style="width:5cm; height:2.4cm; <?php echo $posicion ?> margin-top:-9px;">
  <div style="display:inline-block; width:1.7cm; height:0.74cm; top:10px;">
    <?php echo $img ?>
    </div>
  <div style="display:inline-block; float: right; width:3.2cm; height:0.74cm; font-size: 14px;">
    <center><b>RADICADO No.</b></center>
  </div>
  <?php
    echo '<div id="codigo128_'.$codigo.'" style="display:inline-block; float: right; width:3.2cm; height:0.9cm; font-size: 10px;"></div>
    <script>
      $("#codigo128_'.$codigo.'").barcode("'.$codigo.'", "code128"  , {barWidth:1, barHeight:20});
    </script>';
  ?>
  <div style="display:inline-block; width:4.9cm; height:0.5cm; top:10px; font-size: 10px;">
  <b>Fecha de radicacion:</b><?php echo ' '.$fecha.'<br/><b>Ubicación: </b>'.$ubicacion ?>
  </div>
</div>


{!!Form::close()!!}
@stop
