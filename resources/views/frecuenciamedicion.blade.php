@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>FRECUENCIA MEDICIÓN</center></h3>@stop

@include('alerts/request')
@section('content')
{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.css'); !!}
{!!Html::script('assets/bootstrap-v3.3.5/js/bootstrap.min.js'); !!}
{!!Html::script('/sb-admin/bower_components/ckeditor.js'); !!}

@if(isset($frecuenciamedicion))
@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
{!!Form::model($frecuenciamedicion,['route'=>['frecuenciamedicion.destroy',$frecuenciamedicion->idFrecuenciaMedicion],'method'=>'DELETE'])!!}
@else
{!!Form::model($frecuenciamedicion,['route'=>['frecuenciamedicion.update',$frecuenciamedicion->idFrecuenciaMedicion],'method'=>'PUT'])!!}
@endif
@else
{!!Form::open(['route'=>'frecuenciamedicion.store','method'=>'POST'])!!}
@endif

<div class="container">
<br>
<br>
<br>

  <div id='form-section' >

   <fieldset id="frecuenciamedicion-form-fieldset">	
    <div class="form-group" id='test'>
      {!!Form::label('codigoFrecuenciaMedicion', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-barcode"></i>
          </span>
          {!!Form::text('codigoFrecuenciaMedicion',null,['class'=>'form-control','placeholder'=>'Ingresa el código '])!!} 
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
          {!!Form::text('nombreFrecuenciaMedicion',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la frecuencia'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
        {!!Form::label('valorFrecuenciaMedicion', 'Medir cada:', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o "></i>
            </span>
            {!!Form::text('valorFrecuenciaMedicion',null,['class'=>'form-control','placeholder'=>''])!!}
          </div>
        </div>
        <div class="col-sm-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o "></i>
            </span>
           {!!Form::select('unidadFrecuenciaMedicion',['Dias'=>'Dias','Semanas'=>'Semanas','Meses'=>'Meses','Años'=>'Años'],null,['class'=>'form-control','placeholder'=>'seleccione'])!!}
          </div>
        </div>
    </div>
      
   

      </div>
      
      <br>
      @if(isset($frecuenciamedicion))
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

  @stop

