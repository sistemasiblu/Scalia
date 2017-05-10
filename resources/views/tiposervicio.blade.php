@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>TIPO SERVICIO</center></h3>@stop

@include('alerts/request')
@section('content')
{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.css'); !!}
{!!Html::script('assets/bootstrap-v3.3.5/js/bootstrap.min.js'); !!}
{!!Html::script('/sb-admin/bower_components/ckeditor.js'); !!}

@if(isset($tiposervicio))
@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
{!!Form::model($tiposervicio,['route'=>['tiposervicio.destroy',$tiposervicio->idTipoServicio],'method'=>'DELETE'])!!}
@else
{!!Form::model($tiposervicio,['route'=>['tiposervicio.update',$tiposervicio->idTipoServicio],'method'=>'PUT'])!!}
@endif
@else
{!!Form::open(['route'=>'tiposervicio.store','method'=>'POST'])!!}
@endif

<div class="container">
<br>
<br>
<br>

  <div id='form-section' >

   <fieldset id="tiposervicio-form-fieldset">	
    <div class="form-group" id='test'>
      {!!Form::label('codigoTipoServicio', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-barcode"></i>
          </span>
          {!!Form::text('codigoTipoServicio',null,['class'=>'form-control','placeholder'=>'Ingresa el código del Tipo Servicio'])!!} 
          {!!Form::hidden('idTipoServicio', null, array('id' => 'idTipoServicio')) !!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('nombreTipoServicio', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o "></i>
          </span>
          {!!Form::text('nombreTipoServicio',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del Tipo de Servicio'])!!}
        </div>
      </div>
      
      <div class="form-group" id='test'>
        {!!Form::label('observacionTipoServicio', 'Observacion', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o "></i>
            </span>
            {!!Form::textarea('observacionTipoServicio',null,['class'=>'ckeditor','placeholder'=>'Ingresa la observacion'])!!}
          </div>
        </div>
      </fieldset>
      <br>
      <center>
      @if(isset($tiposervicio))
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

