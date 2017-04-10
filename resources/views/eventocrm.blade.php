@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Eventos / Campañas</center></h3>@stop

@section('content')
@include('alerts.request')

	@if(isset($eventocrm))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($eventocrm,['route'=>['eventocrm.destroy',$eventocrm->idEventoCRM],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($eventocrm,['route'=>['eventocrm.update',$eventocrm->idEventoCRM],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'eventocrm.store','method'=>'POST'])!!}
	@endif
		<div id='form-section' >
				<fieldset id="eventocrm-form-fieldset">	
					<div class="form-group" id='test'>
						{!!Form::label('codigoEventoCRM', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								{!!Form::text('codigoEventoCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la Línea'])!!}
						      	{!!Form::hidden('idEventoCRM', null, array('id' => 'idEventoCRM'))!!}
							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('nombreEventoCRM', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('nombreEventoCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la Línea'])!!}
				    		</div>
				    	</div>
				    </div>	
					
				</fieldset>	
				@if(isset($eventocrm))
					{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary"])!!}
				@else
  					{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 				@endif
		</div>
	{!!Form::close()!!}		
@stop