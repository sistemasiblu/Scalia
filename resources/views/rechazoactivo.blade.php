<?php 

?>

@include('alerts/request')

@if(isset($rechazoactivo))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($rechazoactivo,['route'=>['rechazoactivo.destroy',$rechazoactivo->idRechazoActivo],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($rechazoactivo,['route'=>['rechazoactivo.update',$rechazoactivo->idRechazoActivo],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'rechazoactivo.store','method'=>'POST'])!!}
@endif

@extends('layouts.vista')
@section('titulo')<br><h4 id="titulo"><center>MOTIVO RECHAZO ACTIVO</center></h4>@stop
@section('content')
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Rechazo Activo</title>

 


  
</head>
<body >

 <div class="container">
 

    <div class="form-group">
        {!!Form::label('codigoRechazoActivo', 'Codigo', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-9">
          {!!Form::text('codigoRechazoActivo',null,['required'=>'required','class'=>'form-control','placeholder'=>'Ingresa El Codigo del Motivo'])!!}
        </div>
          {!!Form::label('nombreRechazoActivo', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-9">
          {!!Form::text('nombreRechazoActivo',null,['required'=>'required','class'=>'form-control','placeholder'=>'Ingresa el Nombre del Motivo'])!!}
        </div>
         {!!Form::label('Observacion', 'Observacion:', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-11">
          {!!Form::textarea('observacionRechazoActivo',null,['class'=>'ckeditor'])!!}
        </div>
         


  

</div>

</div>
<center><br><br>
@if(isset($rechazoactivo))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  @else
    {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  @endif
@else
   {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
@endif
{!! Form::close() !!}  
</center>        
</body>
</html>
@stop

