@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de Temporadas</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/filtrotemporada.js')!!}

<?php

?>
<div id='form-section' >

  <fieldset id="metadato-form-fieldset"> 
      <input type="hidden" id="token" value="{{csrf_token()}}"/>

      <div class="form-group col-md-12">
        {!!Form::label('Temporada', 'Temporada', array('class' => 'col-md-1 control-label')) !!}
        <div class="col-md-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Temporada', $temporada ,null,['class' => 'chosen-select form-control', 'id'=>'Temporada', 'multiple'=>'']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaInicialTemporada', 'Fecha Inicial:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaInicialTemporada',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaFinalTemporada', 'Fecha Final:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaFinalTemporada',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

     </fieldset>

        {!!Form::button('Consultar',["class"=>"btn btn-primary", 'onclick' => 'consultarImportacion(
        $(\'#Temporada\').val(), 
        $(\'#fechaInicialTemporada\').val(),
        $(\'#fechaFinalTemporada\').val());'])!!}
</div>
@stop