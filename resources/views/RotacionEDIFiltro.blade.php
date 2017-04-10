@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Consulta de Rotación EDI<br>Inventarios y Ventas</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/informeedi.js')!!}

<?php

?>
<div id='form-section' >

  <fieldset id="metadato-form-fieldset"> 
      <input type="hidden" id="token" value="{{csrf_token()}}"/>
       <div class="form-group" class="col-md-6">
        {!!Form::label('idVentaEDI', 'Periodo Venta', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('idVentaEDI', $periodoVenta ,null,['class' => 'chosen-select form-control', 'id'=>'idVentaEDI']) !!}
          </div>
        </div>
      </div>

      <div class="form-group" class="col-md-6">
        {!!Form::label('idInventarioEDI', 'Periodo Inventario', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('idInventarioEDI', $periodoInventario ,null,['class' => 'chosen-select form-control', 'id'=>'idInventarioEDI']) !!}
          </div>
        </div>
      </div>


      <div class="form-group" class="col-md-6">
        {!!Form::label('Marca_idMarca', 'Marca', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Marca_idMarca', $marca ,null,['class' => 'chosen-select form-control', 'id'=>'Marca_idMarca']) !!}
          </div>
        </div>
      </div>

      <div class="form-group" class="col-md-6">
        {!!Form::label('TipoProducto_idTipoProducto', 'Tipo de Producto', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('TipoProducto_idTipoProducto', $tipoproducto ,null,['class' => 'chosen-select form-control', 'id'=>'TipoProducto_idTipoProducto']) !!}
          </div>
        </div>
      </div>

      <div class="form-group" class="col-md-6">
        {!!Form::label('Categoria_idCategoria', 'Categoria', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Categoria_idCategoria', $categoria ,null,['class' => 'chosen-select form-control', 'id'=>'Categoria_idCategoria']) !!}
          </div>
        </div>
      </div>

      <div class="form-group" class="col-md-6">
        {!!Form::label('EsquemaProducto_idEsquemaProducto', 'Esquema', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('EsquemaProducto_idEsquemaProducto', $esquema ,null,['class' => 'chosen-select form-control', 'id'=>'EsquemaProducto_idEsquemaProducto']) !!}
          </div>
        </div>
      </div>

      <div class="form-group" class="col-md-6">
        {!!Form::label('TipoNegocio_idTipoNegocio', 'Tipo de Negocio', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('TipoNegocio_idTipoNegocio', $tiponegocio ,null,['class' => 'chosen-select form-control', 'id'=>'TipoNegocio_idTipoNegocio']) !!}
          </div>
        </div>
      </div>

      <div class="form-group" class="col-md-6">
        {!!Form::label('Temporada_idTemporada', 'Temporada', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('Temporada_idTemporada', $temporada ,null,['class' => 'chosen-select form-control', 'id'=>'Temporada_idTemporada']) !!}
          </div>
        </div>
      </div>
      <div class="form-group" class="col-md-6">
        {!!Form::label('grupo', 'Agrupar por', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
        <div class="col-sm-8 col-md-4">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-list"></i>
            </span>
            {!! Form::select('grupo', ['codigoAlternoProducto' => 'Referencia Base', 'referenciaProducto' => 'SKU (Referencia + Color + Talla)'] ,null,['class' => 'chosen-select form-control', 'id'=>'grupo']) !!}
          </div>
        </div>
      </div>

     </fieldset>


        {!!Form::button('Consultar',["class"=>"btn btn-primary", 'onclick' => 'consultarRotacionEDI(
        $(\'#idVentaEDI\').val(), 
        $(\'#idInventarioEDI\').val(), 
        $(\'#Marca_idMarca\').val(), 
        $(\'#TipoProducto_idTipoProducto\').val(), 
        $(\'#Categoria_idCategoria\').val(), 
        $(\'#EsquemaProducto_idEsquemaProducto\').val(), 
        $(\'#TipoNegocio_idTipoNegocio\').val(), 
        $(\'#Temporada_idTemporada\').val(), 
        $(\'#grupo\').val());'])!!}
</div>
@stop