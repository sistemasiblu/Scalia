@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de Inventario</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/filtroinventarioubicacion.js')!!}

<?php

?>
<div id='form-section' >

  <fieldset id="metadato-form-fieldset"> 
      <input type="hidden" id="token" value="{{csrf_token()}}"/>

      <div class="form-group col-md-6">
        {!!Form::label('tipoInventario', 'Tipo', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('tipoInventario', ['Historias' => 'Historias','Otros' => 'Otros'],null,['class' => 'chosen-select form-control', 'placeholder' => 'Seleccione un tipo de inventario']) !!}
          </div>
        </div>
      </div>

      <br/><br/><br/>

      <div class="form-group col-md-6">
        {!!Form::label('fechaInicial', 'Fecha Inicial:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaInicial',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6">
        {!!Form::label('fechaFinal', 'Fecha Final:', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-8">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaFinal',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

     </fieldset>

        {!!Form::button('Consultar',["class"=>"btn btn-primary", 'onclick' => 'consultarInventario(
        $(\'#tipoInventario\').val(), 
        $(\'#fechaInicial\').val(),
        $(\'#fechaFinal\').val());'])!!}
</div>
@stop