@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>ANS Acuerdo de Nivel de Servicio</center></h3>@stop

@section('content')
@include('alerts.request')

	@if(isset($acuerdoservicio))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($acuerdoservicio,['route'=>['acuerdoservicio.destroy',$acuerdoservicio->idAcuerdoServicio],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($acuerdoservicio,['route'=>['acuerdoservicio.update',$acuerdoservicio->idAcuerdoServicio],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'acuerdoservicio.store','method'=>'POST'])!!}
	@endif
		<div id='form-section' >
				<fieldset id="acuerdoservicio-form-fieldset">	
					<div class="form-group" id='test'>
						{!!Form::label('codigoAcuerdoServicio', 'C&oacute;digo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								{!!Form::text('codigoAcuerdoServicio',null,['class'=>'form-control','placeholder'=>'Ingresa el código del acuerdo de servicio'])!!}
						      	{!!Form::hidden('idAcuerdoServicio', null, array('id' => 'idAcuerdoServicio'))!!}
							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('nombreAcuerdoServicio', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('nombreAcuerdoServicio',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del acuerdo de servicio'])!!}
				    		</div>
				    	</div>
				    </div>	

				    <div class="form-group" id='test'>
						{!!Form::label('tiempoAcuerdoServicio', 'Tiempo', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('tiempoAcuerdoServicio',null,['class'=>'form-control','placeholder'=>'Ingresa el tiempo del ANS'])!!}
				    		</div>
				    	</div>
				    </div>	
					
					<div class="form-group" >
			          {!!Form::label('unidadTiempoAcuerdoServicio', 'Unidad Tiempo', array('class' => 'col-sm-2 control-label'))!!}
			          <div class="col-sm-10" >
			            <div class="input-group">
			              <span class="input-group-addon">
			                <i class="fa fa-credit-card" ></i>
			              </span>
			              {!!Form::select('unidadTiempoAcuerdoServicio',
            				array('Minutos'=>'Minutos','Horas'=>'Horas','Dias'=>'Días'), (isset($acuerdoservicio) ? $acuerdoservicio->unidadTiempoAcuerdoServicio : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione Unidad de Tiempo"])!!}
			            </div>
			          </div>
			        </div>
				</fieldset>	
				@if(isset($acuerdoservicio))
					{!!Form::submit(((isset($_GET['accion']) and $_GET['accion'] == 'eliminar') ? 'Eliminar' : 'Modificar'),["class"=>"btn btn-primary"])!!}
				@else
  					{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 				@endif
		</div>
	{!!Form::close()!!}		
@stop