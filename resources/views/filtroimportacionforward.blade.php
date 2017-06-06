@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de Importación - Forward</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/filtroimportacionforward.js')!!}

<?php

?>
<div id='form-section' >

  <fieldset id="metadato-form-fieldset"> 
      <input type="hidden" id="token" value="{{csrf_token()}}"/>

      <div class="form-group col-md-6">
        {!!Form::label('Temporada', 'Temporada', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Temporada', $temporada ,null,['class' => 'chosen-select form-control', 'id'=>'Temporada']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('Compra', 'Compra', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Compra', $compra ,null,['class' => 'chosen-select form-control', 'id'=>'Compra']) !!}
          </div>
        </div>
      </div>


      <div class="form-group col-md-6">
        {!!Form::label('Cliente', 'Cliente', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Cliente', $cliente ,null,['class' => 'chosen-select form-control', 'id'=>'Cliente']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('Proveedor', 'Proveedor', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Proveedor', $proveedor ,null,['class' => 'chosen-select form-control', 'id'=>'Proveedor']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('documentoImportacion', 'Documento', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('documentoImportacion', $documento ,null,['class' => 'chosen-select form-control', 'id'=>'documentoImportacion']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">

      </div>
      <br/><br/><br/><br/><br/><br/><br/>

      <div class="form-group col-md-6">
        {!!Form::label('fechaInicialCompra', 'Compras desde:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaInicialCompra',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaFinalCompra', 'Compras hasta:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaFinalCompra',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

     </fieldset>

        {!!Form::button('Consultar',["class"=>"btn btn-primary", 'onclick' => 'consultarImportacion(
        $(\'#Temporada\').val(), 
        $(\'#Compra\').val(), 
        $(\'#Cliente\').val(), 
        $(\'#Proveedor\').val(), 
        $(\'#fechaInicialCompra\').val(),
        $(\'#fechaFinalCompra\').val(),
        $(\'#documentoImportacion\').val());'])!!}
</div>
@stop