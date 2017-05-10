<!-- <?php
//echo $localizacion;
//return;
?> -->

@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>LOCALIZACION</center></h3>@stop

@include('alerts/request')
@section('content')
{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.css'); !!}
{!!Html::script('assets/bootstrap-v3.3.5/js/bootstrap.min.js'); !!}
{!!Html::script('/sb-admin/bower_components/ckeditor.js'); !!}	

@if(isset($localizacion))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($localizacion,['route'=>['localizacion.destroy',$localizacion->idLocalizacion],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($localizacion,['route'=>['localizacion.update',$localizacion->idLocalizacion],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'localizacion.store','method'=>'POST'])!!}
@endif

<div class="container">
<br><br><br>
  <div id='form-section' >
   <fieldset id="localizacion-form-fieldset">	
    <div class="form-group" id='test'>
      {!!Form::label('codigoLocalizacion', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-barcode"></i>
          </span>
          {!!Form::text('codigoLocalizacion',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la Localizacion'])!!}
          {!!Form::hidden('idLocalizacion', null, array('id' => 'idLocalizacion')) !!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('nombreLocalizacion', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
          {!!Form::text('nombreLocalizacion',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la Localizacion'])!!}
          </div>
      </div>
    </div>
    
    <div class="form-group" id='test'>
      {!!Form::label('Localizacion_idPadre', 'Padre', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o "></i>
          </span>
          {!!Form::select('Localizacion_idPadre', @$localizacionPadre, @$localizacion->Localizacion_idPadre,['class' => 'form-control'])!!}
        </div>
      </div>
    </div>
    
    <div class="form-group" id='test'>
      {!!Form::label('observacionLocalizacion', 'Observacion', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o "></i>
          </span>
          {!!Form::textarea('observacionLocalizacion',null,['class'=>'ckeditor','placeholder'=>'Ingresa la observacion'])!!}
        </div>
      </div>
    </div>
  </fieldset>
  <br>
  <center>
@if(isset($localizacion))
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
</div>
@stop
