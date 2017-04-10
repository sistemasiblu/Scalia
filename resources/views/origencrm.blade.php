@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Origen CRM</center></h3>@stop

@section('content')
@include('alerts.request')

	@if(isset($origencrm))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($origencrm,['route'=>['origencrm.destroy',$origencrm->idOrigenCRM],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($origencrm,['route'=>['origencrm.update',$origencrm->idOrigenCRM],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'origencrm.store','method'=>'POST'])!!}
	@endif
		<div id='form-section' >
				<fieldset id="origencrm-form-fieldset">	
					<div class="form-group" id='test'>
						{!!Form::label('codigoOrigenCRM', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								{!!Form::text('codigoOrigenCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el código del Origen'])!!}
						      	{!!Form::hidden('idOrigenCRM', null, array('id' => 'idOrigenCRM'))!!}
							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('nombreOrigenCRM', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('nombreOrigenCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del Origen'])!!}
				    		</div>
				    	</div>
				    </div>	
					
				</fieldset>	
				@if(isset($origencrm))
					{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary"])!!}
				@else
  					{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 				@endif
		</div>
	{!!Form::close()!!}		
@stop