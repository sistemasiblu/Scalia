<!-- <?php
echo $grupoestado;
//return;
?>  -->



@if(isset($clasificacioncrm))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($clasificacioncrm,['route'=>['clasificacioncrm.destroy',$clasificacioncrm->idClasificacionCRM],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($clasificacioncrm,['route'=>['clasificacioncrm.update',$clasificacioncrm->idClasificacionCRM],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'clasificacioncrm.store','method'=>'POST'])!!}
@endif

@extends('layouts.vista')
@section('titulo')<h3 id="titulo" style="font-weight:bold;font-family: Georgia;" ><center>Clasificación CRM</center></h4><hr>@stop
@section('content')
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

  <script>



  var clasificacion = '<?php echo (isset($clasificacioncrm) ? json_encode($clasificacioncrm->clasificacioncrmdetalle) : "");?>';
  clasificacion = (clasificacion != '' ? JSON.parse(clasificacion) : '');


var valorClasificacioncrm = [0,0,''];
$(document).ready(function()
{

  clasificacioncrm=new Atributos('clasificacioncrm','contenedor-clasificacioncrm','clasificacioncrm-');
  clasificacioncrm.campoid = 'idClasificacionCRMDetalle';
  clasificacioncrm.campoEliminacion = 'clasificacioncrmDetalleEliminar';
  clasificacioncrm.campos=['idClasificacionCRMDetalle', 'codigoClasificacionCRMDetalle','nombreClasificacionCRMDetalle'];
  clasificacioncrm.etiqueta=['input','input','input'];
  clasificacioncrm.tipo=['hidden','',''];
  clasificacioncrm.estilo=['','width:230px; height:35px;','width:600px; height:35px;'];
  clasificacioncrm.clase=['','',''];
  clasificacioncrm.sololectura=[false,false,false];
  clasificacioncrm.completar=['off','off','off'];
  clasificacioncrm.opciones = [[],[],[]];      
  clasificacioncrm.funciones=['','',''];

 
  for(var j=0; j < clasificacion.length; j++)
  {
      clasificacioncrm.agregarCampos(JSON.stringify(clasificacion[j]),'L');
  }

});



</script>


</head>
<body  >
@include('alerts/request')
 
 <br>  
 

 <br>
  <div class='form-group'>
      {!!Form::hidden('clasificacioncrmDetalleEliminar', null, array('id' => 'clasificacioncrmDetalleEliminar')) !!}
      {!!Form::hidden('idClasificacionCRM', null, array('id' => 'idClasificacionCRM')) !!}

      {!!Form::label('GrupoEstado_idGrupoEstado', 'Grupo de Estado', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10" position="left">
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-barcode" ></i></span>
          {!!Form::select('GrupoEstado_idGrupoEstado',@$grupoestado, @$clasificacioncrm->GrupoEstado_idGrupoEstado,['class' => 'form-control','style'=>'padding-left:2px;','placeholder'=>'Seleccione'])!!}
        </div>
      </div>
      <br><br>
      {!!Form::label('codigoClasificacionCRM', 'Código', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10" position="left">
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-barcode" ></i></span>
          {!!Form::text('codigoClasificacionCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la Clasificación'])!!}
        </div>
      </div>
      <br><br>
      {!!Form::label('nombreClasificacionCRM', '   Nombre', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon"><i class="fa fa-pencil-square-o" ></i></span>
          {!!Form::text('nombreClasificacionCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la Clasificación'])!!}
          </div>
      </div>
    
  </div>
      

<br>
<br>
<div style="margin-left: 31px;">
        <div class="row show-grid">
          <div class="col-md-1" style="width: 40px;height: 35px;" >
            <span title="Agregar" style="cursor:pointer;" class="glyphicon glyphicon-plus" onclick="clasificacioncrm.agregarCampos(valorClasificacioncrm,'A')" ></span>
          </div>
          <div class="col-md-1" style="width: 230px;height: 35px;"><b>Codigo</b></div>
          <div class="col-md-1" style="width: 601px;height: 35px;"><b>Descripcion</b></div>
          <div id="contenedor-clasificacioncrm"></div>
        </div>  


      
    

@if(isset($clasificacioncrm))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  @else
    {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  @endif
@else
   {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
@endif
{!! Form::close() !!}          
</body>
</html>
@stop



