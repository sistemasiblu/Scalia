<?php

/*
echo $tipoaccion;
echo "<hr>";
echo $tipoactivo;
echo "<hr>";
echo $protmantenimiento;
echo "<hr>";
echo $localizacion;
return;*/
?>


@include('alerts/request')
@if(isset($prmantenimiento))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($prmantenimiento,['route'=>['programacionmantenimiento.destroy',$prmantenimiento->idProgramacionMantenimiento],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($prmantenimiento,['route'=>['programacionmantenimiento.update',$prmantenimiento->idProgramacionMantenimiento],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'programacionmantenimiento.store','method'=>'POST'])!!}
@endif

@extends('layouts.vista')
@section('titulo')<br><h4 id="titulo"><center>Programacion Mantenimiento</center></h4>@stop
@section('content')
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Plan Mantenimiento</title>

</head>
<body >
<div class="container">
  <br>
    <div class='form-group'>
    <div class="col-sm-12">
        {!!Form::label('ProtocoloMantenimiento_idProtocoloMantenimiento', 'Descripcion Mantto', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            {!!Form::select('ProtocoloMantenimiento_idProtocoloMantenimiento',@$protmantenimiento, @$prmantenimiento->ProtocoloMantenimiento_idProtocoloMantenimiento,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('TipoActivo_idTipoActivo', 'Tipo Activo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            {!!Form::select('TipoActivo_idTipoActivo', @$tipoactivo, @$prmantenimiento->TipoActivo_idTipoActivo,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('TipoAccion_idTipoAccion', 'Tipo Servicio', array('class' => 'col-sm-2 control-label')) !!}     
          <div class="col-sm-10">
            {!!Form::select('TipoAccion_idTipoAccion',@$tipoaccion,@$prmantenimiento->TipoAccion_idTipoAccion,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}  
          </div>       
         
        {!!Form::label('Dependencia_idDependencia', 'Dependencia', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            {!!Form::select('Dependencia_idDependencia',@$localizacion,@$prmantenimiento->Dependencia_idDependencia,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}
          </div>
          <br>
     
          
        {!!Form::label('fechaInicialProgramacionMantenimiento', 'Fecha Inicial', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-3">
            {!!Form::date('fechaInicialProgramacionMantenimiento', null,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('fechaMaximaProgramacionMantenimiento', 'Fecha Maxima', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-3">
            {!!Form::date('fechaMaximaProgramacionMantenimiento', null,['class' => 'form-control'])!!}
          </div>

        
</div>
<br><br><br><br>
</div>
<br><br><br><br><br><br><center><br><br>
@if(isset($prmantenimiento))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  @else
    {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  @endif
@else
   {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
@endif
{!! Form::close() !!}
</div>
</body>
</html>
@stop