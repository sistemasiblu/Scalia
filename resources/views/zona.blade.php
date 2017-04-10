@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Zonas</center></h3>@stop

@section('content')
@include('alerts.request')

	@if(isset($zona))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($zona,['route'=>['zona.destroy',$zona->idZona],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($zona,['route'=>['zona.update',$zona->idZona],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'zona.store','method'=>'POST'])!!}
	@endif
		<div id='form-section' >
				<fieldset id="zona-form-fieldset">	
					<div class="form-group" id='test'>
						{!!Form::label('codigoZona', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								{!!Form::text('codigoZona',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la Zona'])!!}
						      	{!!Form::hidden('idZona', null, array('id' => 'idZona'))!!}
							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('nombreZona', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('nombreZona',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la Zona'])!!}
				    		</div>
				    	</div>
				    </div>	
					
				</fieldset>	
				@if(isset($zona))
					{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary"])!!}
				@else
  					{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 				@endif
		</div>
	{!!Form::close()!!}		
@stop