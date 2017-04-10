@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Categorías CRM</center></h3>@stop

@section('content')
@include('alerts.request')

	@if(isset($categoriacrm))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($categoriacrm,['route'=>['categoriacrm.destroy',$categoriacrm->idCategoriaCRM],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($categoriacrm,['route'=>['categoriacrm.update',$categoriacrm->idCategoriaCRM],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'categoriacrm.store','method'=>'POST'])!!}
	@endif
		<div id='form-section' >
				<fieldset id="categoriacrm-form-fieldset">
				<div class="form-group" id='test'>
						{!!Form::label('codigoCategoriaCRM', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								{!!Form::text('codigoCategoriaCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la Categoria'])!!}
						      	{!!Form::hidden('idCategoriaCRM', null, array('id' => 'idCategoriaCRM'))!!}
							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('nombreCategoriaCRM', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('nombreCategoriaCRM',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la Categoria'])!!}
				    		</div>
				    	</div>
				    </div>	
					
				</fieldset>	
				@if(isset($categoriacrm))
					{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary"])!!}
				@else
  					{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 				@endif
		</div>
	{!!Form::close()!!}		
@stop