@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Etiquetas</center></h3>@stop

@section('content')
@include('alerts.request')
	@if(isset($etiqueta))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($etiqueta,['route'=>['etiqueta.destroy',$etiqueta->idEtiqueta],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($etiqueta,['route'=>['etiqueta.update',$etiqueta->idEtiqueta],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'etiqueta.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="etiqueta-form-fieldset">	
		<div class="form-group" id='test'>
          {!!Form::label('nombreEtiqueta', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-tag"></i>
              </span>
              {!!Form::text('nombreEtiqueta',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la Etiqueta'])!!}
              {!!Form::hidden('idEtiqueta', null, array('id' => 'idEtiqueta')) !!}
            </div>
          </div>
        </div>

    </fieldset>
	@if(isset($etiqueta))
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
</div>
@stop