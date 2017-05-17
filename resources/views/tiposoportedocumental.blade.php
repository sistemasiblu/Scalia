@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Tipo de Soporte Documental</center></h3>@stop

@section('content')
@include('alerts.request')
	@if(isset($tiposoportedocumental))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($tiposoportedocumental,['route'=>['tiposoportedocumental.destroy',$tiposoportedocumental->idTipoSoporteDocumental],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($tiposoportedocumental,['route'=>['tiposoportedocumental.update',$tiposoportedocumental->idTipoSoporteDocumental],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'tiposoportedocumental.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="tiposoportedocumental-form-fieldset">	
		<div class="form-group" id='test'>
      {!!Form::label('codigoTipoSoporteDocumental', 'Código', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-barcode"></i>
          </span>
          {!!Form::text('codigoTipoSoporteDocumental',null,['class'=>'form-control','placeholder'=>'Ingresa el codigo del soporte documental'])!!}
          {!!Form::hidden('idTipoSoporteDocumental', null, array('id' => 'idTipoSoporteDocumental')) !!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('nombreTipoSoporteDocumental', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
          {!!Form::text('nombreTipoSoporteDocumental',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del tipo de soporte documental'])!!}
        </div>
      </div>
    </div>

    </fieldset>
	@if(isset($tiposoportedocumental))
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