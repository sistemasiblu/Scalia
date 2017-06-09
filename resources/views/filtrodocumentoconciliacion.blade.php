@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de Importación</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/filtrodocumentoconciliacion.js')!!}

<?php

?>
<div id='form-section' >

  <fieldset id="metadato-form-fieldset"> 
      <input type="hidden" id="token" value="{{csrf_token()}}"/>

      <div class="form-group col-md-6">
        {!!Form::label('Documento', 'Documento', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Documento', $documento ,null,['class' => 'chosen-select form-control', 'id'=>'Documento','multiple'=>'']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('ValorConciliacion_idValorConciliacion', 'Concepto', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('ValorConciliacion', $valorconciliacion ,null,['class' => 'chosen-select form-control', 'id'=>'ValorConciliacion','multiple'=>'']) !!}
          </div>
        </div>
      </div>


      <div class="form-group col-md-6">
        {!!Form::label('fechaElaboracionMovimientoInicial', 'Fecha desde:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaElaboracionMovimientoInicial',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaElaboracionMovimientoFinal', 'Fecha hasta:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaElaboracionMovimientoFinal',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-12">
        {!!Form::button('Consultar',["class"=>"btn btn-primary", 'onclick' => 'consultarInformacion(
        $(\'#Documento\').val(), 
        $(\'#ValorConciliacion\').val(),
        $(\'#fechaElaboracionMovimientoInicial\').val(),
        $(\'#fechaElaboracionMovimientoFinal\').val());'])!!}
      </div>

     </fieldset>

</div>
@stop