@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Marca</center></h3>@stop

@section('content')
@include('alerts.request')
 
	@if(isset($marca))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($marca,['route'=>['marca.destroy',$marca->idMarca],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($marca,['route'=>['marca.update',$marca->idMarca],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'marca.store','method'=>'POST'])!!}
	@endif

<div id='form-section' >

	
	<fieldset id="marca-form-fieldset">	
		<div class="form-group" id='test'>
          {!! Form::label('codigoMarca', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoMarca',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
              {!! Form::hidden('idMarca', null, array('id' => 'idMarca')) !!}
            </div>
          </div>
        </div>

		    <div class="form-group" id='test'>
          {!! Form::label('nombreMarca', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
				      {!!Form::text('nombreMarca',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

    </fieldset>
	@if(isset($marca))
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