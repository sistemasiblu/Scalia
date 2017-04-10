@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Línea de Negocio</center></h3>@stop

@section('content')
@include('alerts.request')

	@if(isset($lineanegocio))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($lineanegocio,['route'=>['lineanegocio.destroy',$lineanegocio->idLineaNegocio],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($lineanegocio,['route'=>['lineanegocio.update',$lineanegocio->idLineaNegocio],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'lineanegocio.store','method'=>'POST'])!!}
	@endif
		<div id='form-section' >
				<fieldset id="lineanegocio-form-fieldset">	
					<div class="form-group" id='test'>
						{!!Form::label('codigoLineaNegocio', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								{!!Form::text('codigoLineaNegocio',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la Línea'])!!}
						      	{!!Form::hidden('idLineaNegocio', null, array('id' => 'idLineaNegocio'))!!}
							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('nombreLineaNegocio', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('nombreLineaNegocio',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la Línea'])!!}
				    		</div>
				    	</div>
				    </div>	
					
				</fieldset>	
				@if(isset($lineanegocio))
					{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary"])!!}
				@else
  					{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 				@endif
		</div>
	{!!Form::close()!!}		
@stop