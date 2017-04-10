@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de Importación Detallada</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/filtroimportaciondetallado.js')!!}

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
        {!!Form::label('Puerto', 'Puerto', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Puerto', $puerto ,null,['class' => 'chosen-select form-control', 'id'=>'Puerto']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('filtroAgrupado', 'Agrupado', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('filtroAgrupado', ['1' => 'Si','0' => 'No'],null,['class' => 'chosen-select form-control']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('filtroBodega', 'Compras en bodega', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('filtroBodega', ['todas' => 'Todas','1' => 'Si','0' => 'No'],null,['class' => 'chosen-select form-control']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">

      </div>
      <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>

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

      <div class="form-group col-md-6">
        {!!Form::label('fechaInicialEmbarque', 'Embarque desde:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaInicialEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaFinalEmbarque', 'Embarque hasta:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaFinalEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      

     </fieldset>

        {!!Form::button('Consultar',["class"=>"btn btn-primary", 'onclick' => 'consultarImportacion(
        $(\'#Temporada\').val(), 
        $(\'#Compra\').val(), 
        $(\'#Cliente\').val(), 
        $(\'#Proveedor\').val(), 
        $(\'#Puerto\').val(),
        $(\'#fechaInicialCompra\').val(),
        $(\'#fechaFinalCompra\').val(),
        $(\'#fechaInicialEmbarque\').val(),
        $(\'#fechaFinalEmbarque\').val(),
        $(\'#filtroAgrupado option:selected\').val(),
        $(\'#filtroBodega option:selected\').val());'])!!}
</div>
@stop