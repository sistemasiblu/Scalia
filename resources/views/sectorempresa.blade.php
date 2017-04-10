@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Sector de Empresa</center></h3>@stop

@section('content')
@include('alerts.request')

	@if(isset($sectorempresa))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($sectorempresa,['route'=>['sectorempresa.destroy',$sectorempresa->idSectorEmpresa],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($sectorempresa,['route'=>['sectorempresa.update',$sectorempresa->idSectorEmpresa],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'sectorempresa.store','method'=>'POST'])!!}
	@endif
		<div id='form-section' >
				<fieldset id="sectorempresa-form-fieldset">	
					<div class="form-group" id='test'>
						{!!Form::label('codigoSectorEmpresa', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								{!!Form::text('codigoSectorEmpresa',null,['class'=>'form-control','placeholder'=>'Ingresa el código del Sector'])!!}
						      	{!!Form::hidden('idSectorEmpresa', null, array('id' => 'idSectorEmpresa'))!!}
							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('nombreSectorEmpresa', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('nombreSectorEmpresa',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del Sector'])!!}
				    		</div>
				    	</div>
				    </div>	
					
				</fieldset>	
				@if(isset($sectorempresa))
					{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary"])!!}
				@else
  					{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 				@endif
		</div>
	{!!Form::close()!!}		
@stop