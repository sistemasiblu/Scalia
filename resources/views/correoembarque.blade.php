@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Configuración de correo</center></h3>@stop

@section('content')
@include('alerts.request')

	@if(isset($correoembarque))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($correoembarque,['route'=>['correoembarque.destroy',$correoembarque->idCorreoEmbarque],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($correoembarque,['route'=>['correoembarque.update',$correoembarque->idCorreoEmbarque],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'correoembarque.store','method'=>'POST'])!!}
	@endif

<div id='form-section' >

	<fieldset id="correoembarque-form-fieldset">	
    <div id="padre" class="col-md-12">

		    <div class="form-group col-md-12" id='test'>
          {!!Form::label('tipoCorreoEmbarque', 'Tipo:', array('class' => 'col-sm-1 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!! Form::select('tipoCorreoEmbarque', ['Bodega' => 'Bodega','Pago' => 'Pago','OTM' => 'OTM'],null,['class' => 'form-control']) !!}
            </div>
          </div>
        </div>

        {!!Form::hidden('idCorreoEmbarque', null, array('id' => 'idCorreoEmbarque')) !!}

        <div class="form-group col-md-12" id='test'>
          {!!Form::label('destinatarioCorreoEmbarque', 'Para: ', array('class' => 'col-sm-1 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-at"></i>
              </span>
              {!!Form::text('destinatarioCorreoEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-12" id='test'>
          {!!Form::label('copiaCorreoEmbarque', 'CC: ', array('class' => 'col-sm-1 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::text('copiaCorreoEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-12" id='test'>
          {!!Form::label('asuntoCorreoEmbarque', 'Asunto: ', array('class' => 'col-sm-1 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
              {!!Form::text('asuntoCorreoEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-12" id='test'>
          {!!Form::label('mensajeCorreoEmbarque', 'Mensaje: ', array('class' => 'col-sm-1 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!!Form::textarea('mensajeCorreoEmbarque',null,['class'=>'form-control','style'=>'height:100px'])!!}
            </div>
          </div>
        </div>

	@if(isset($correoembarque))
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