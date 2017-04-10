@extends('layouts.formato')

<title>Programación de importaciones</title>
@section('contenido')

{!!Form::model($compra)!!}
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
    // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
       for ($i = 0, $c = count($compra); $i < $c; ++$i) 
       {
          $campos[$i] = (array) $compra[$i];
       }
?>

<div>
  <!-- IMPRIMO EL NUMERO DE LA COMPRA -->
    <div class="col-md-12" style="border:1px; background-color:#CEECF5;">
      <div class="col-md-1" style="top:15px"> <?php echo $img ?> </div>
      <div class="col-md-11"><center><h1>Compra N°: <?php echo $campos[0]['numeroCompra'] ?></h1></center></div>
    </div>

    </br> </br> </br>

<div class="list-group" style="border:1px;">
    <div class="panel panel-primary">
      <div class="panel-heading" style="height:45px;"><h4>Datos generales</h4></div>
      <div class="panel-body">
        <?php 
        echo 
        '<div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>Fecha de Compra:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['fechaCompra'].'</div>
         </div>

        <div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>Temporada:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['nombreTemporadaCompra'].'</div>

           <div style="width:150px; display:inline-block;"><b>Proveedor:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['nombreProveedorCompra'].'</div>
         </div>

         <div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>PI:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['numeroCompra'].'</div>

           <div style="width:150px; display:inline-block;"><b>Pago proveedor:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['formaPagoProveedorCompra'].'</div>
         </div>

         <div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>Cliente:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['nombreClienteCompra'].'</div>

           <div style="width:150px; display:inline-block;"><b>Pago cliente:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['formaPagoClienteCompra'].'</div>
         </div>

         <div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>Valor FOB:</b> </div>
           <div style="width:450px; display:inline-block;">'.number_format($campos[0]['valorCompra'],2,".",",").'</div>


           <div style="width:150px; display:inline-block;"><b>Cantidad:</b> </div>
           <div style="width:450px; display:inline-block;">'.number_format($campos[0]['cantidadCompra'],2,".",",").'</div>
         </div>

         <div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>Peso:</b> </div>
           <div style="width:450px; display:inline-block;">'.number_format($campos[0]['pesoCompra'],0,".",",").'</div>

           <div style="width:150px; display:inline-block;"><b>Volumen:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['volumenCompra'].'</div>
         </div>

         <div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>Bultos:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['bultoCompra'].'</div>

           <div style="width:150px; display:inline-block;"><b>Puerto:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['nombreCiudadCompra'].'</div>
         </div>

         <div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>Delivery:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['fechaDeliveryCompra'].'</div>

           <div style="width:150px; display:inline-block;"><b>Fecha de forward:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['fechaForwardCompra'].'</div>
         </div>

         <div style="height:25px;">
          <div style="width:150px; display:inline-block;"><b>Forward:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['valorForwardCompra'].'</div>

           <div style="width:150px; display:inline-block;"><b>Días pagos cliente:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['diaPagoClienteCompra'].'</div>
         </div>

         <div style="height:45px;">
          <div style="width:150px; display:inline-block;"><b>Tiempo permanencia:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['tiempoBodegaCompra'].'</div>
         </div>

         <div>
          <div style="width:150px; color:red; display:inline-block;"><b>Observación:</b> </div>
           <div style="width:450px; display:inline-block;">'.$campos[0]['observacionCompra'].'</div
         </div>




         ';
      ?>
      </div>
    </div>
    </div>

</div>

  
{!!Form::close()!!}
@stop