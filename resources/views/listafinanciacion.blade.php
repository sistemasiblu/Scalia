@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Lista de financiación</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/forward.js')!!}

@if(isset($listafinanciacion))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($listafinanciacion,['route'=>['listafinanciacion.destroy',$listafinanciacion->idListaFinanciacion],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($listafinanciacion,['route'=>['listafinanciacion.update',$listafinanciacion->idListaFinanciacion],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'listafinanciacion.store','method'=>'POST'])!!}
@endif

<div id='form-section' >

  <fieldset id="listafinanciacion-form-fieldset">  
    <div id="padre" class="col-md-12">

        <div class="form-group col-md-12" id='test'>
          {!!Form::label('codigoListaFinanciacion', 'Codigo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoListaFinanciacion',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        {!!Form::hidden('idListaFinanciacion', null, array('id' => 'idListaFinanciacion')) !!}

        <div class="form-group col-md-12" id='test'>
          {!!Form::label('nombreListaFinanciacion', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
              {!!Form::text('nombreListaFinanciacion',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-12" id='test'>
          {!!Form::label('codigoSayaListaFinanciacion', 'Codigo SAYA', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::text('codigoSayaListaFinanciacion',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>


        <div class="form-group col-md-12" id='test'>
          {!!Form::label('tipoListaFinanciacion', 'Tipo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-list"></i>
              </span>
              {!! Form::select('tipoListaFinanciacion', ['Credito'=>'Crédito','RecursoPropio' => 'Recurso Propio'],null,['class' => 'form-control']) !!}
            </div>
          </div>
        </div>
  </div>


  </fieldset>

  @if(isset($listafinanciacion))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
        {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
      @else
        {!!Form::submit('Modificar',["class"=>"btn btn-primary", 'id'=>'Modificar'])!!}
      @endif
  @else
      {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
  @endif

  {!! Form::close() !!}
</div>
@stop