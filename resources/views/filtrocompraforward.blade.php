@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de Forward - Compra</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/filtrocompraforward.js')!!}

<?php

?>
<div id='form-section' >

  <fieldset id="metadato-form-fieldset"> 
      <input type="hidden" id="token" value="{{csrf_token()}}"/>

      <div class="form-group col-md-6">
        {!!Form::label('Forward', 'Forward', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Forward', $forward ,null,['class' => 'chosen-select form-control', 'id'=>'Forward']) !!}
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
        {!!Form::label('filtroCompraForward', 'Filtrar por:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('filtroCompraForward', ['forward' => 'Forward','compra' => 'Compras'],null,['class' => 'chosen-select form-control']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('visualizacionCompraForward', 'Visualizar en:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-file-excel-o"></i>
            </span>
            {!! Form::select('visualizacionCompraForward', ['html' => 'HTML','excel' => 'Excel'],null,['class' => 'chosen-select form-control']) !!}
          </div>
        </div>
      </div>

      <br><br><br><br><br>

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
        {!!Form::label('fechaInicialForward', 'Forward desde:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaInicialForward',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaFinalForward', 'Forward hasta:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaFinalForward',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

     </fieldset>

        {!!Form::button('Consultar',["class"=>"btn btn-primary", 'onclick' => 'consultarCompraForward(
        $(\'#Forward\').val(), 
        $(\'#Compra\').val(), 
        $(\'#fechaInicialCompra\').val(),
        $(\'#fechaFinalCompra\').val(),
        $(\'#fechaInicialForward\').val(),
        $(\'#fechaFinalForward\').val(),
        $(\'#filtroCompraForward option:selected\').val(),
        $(\'#visualizacionCompraForward option:selected\').val());'])!!}
</div>
@stop