@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Dispositivo</center></h3>@stop

@section('content')
@include('alerts.request')
 
	@if(isset($dispositivo))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($dispositivo,['route'=>['dispositivo.destroy',$dispositivo->idDispositivo],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($dispositivo,['route'=>['dispositivo.update',$dispositivo->idDispositivo],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'dispositivo.store','method'=>'POST'])!!}
	@endif

<div id='form-section' >

	
	<fieldset id="dispositivo-form-fieldset">	
		<div class="form-group" id='test'>
          {!! Form::label('codigoDispositivo', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoDispositivo',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
              {!! Form::hidden('idDispositivo', null, array('id' => 'idDispositivo')) !!}
            </div>
          </div>
        </div>

		    <div class="form-group" id='test'>
          {!! Form::label('nombreDispositivo', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
				      {!!Form::text('nombreDispositivo',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

    </fieldset>
	@if(isset($dispositivo))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 	@endif
	{!!Form::close()!!}
	</div>
</div>
@stop