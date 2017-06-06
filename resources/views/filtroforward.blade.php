@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de Forward</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/filtroforward.js')!!}

<?php

?>
<div id='form-section' >

  <fieldset id="metadato-form-fieldset"> 
      <input type="hidden" id="token" value="{{csrf_token()}}"/>

      <div class="form-group col-md-12">
        {!!Form::label('Forward', 'Forward', array('class' => 'col-md-1 control-label')) !!}
        <div class="col-md-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Forward', $forward ,null,['class' => 'chosen-select form-control', 'id'=>'Forward', 'multiple'=>'']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaNegociacionForward', 'Fecha Negociacion:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaNegociacionForward',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaVencimientoForward', 'Fecha Vencimiento:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaVencimientoForward',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

     </fieldset>

        {!!Form::button('Consultar',["class"=>"btn btn-primary", 'onclick' => 'consultarImportacion(
        $(\'#Forward\').val(), 
        $(\'#fechaNegociacionForward\').val(),
        $(\'#fechaVencimientoForward\').val());'])!!}
</div>
@stop