@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Sistema de Informaci&oacute;n</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/sistemainformacion.js')!!}
	@if(isset($sistemainformacion))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($sistemainformacion,['route'=>['sistemainformacion.destroy',$sistemainformacion->idSistemaInformacion],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($sistemainformacion,['route'=>['sistemainformacion.update',$sistemainformacion->idSistemaInformacion],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'sistemainformacion.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="sistemainformacion-form-fieldset">	
		<div class="form-group" id='test'>
          {!!Form::label('codigoSistemaInformacion', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoSistemaInformacion',null,['class'=>'form-control','placeholder'=>'Ingresa el código del sistema de informaci&oacute;n'])!!}
              {!!Form::hidden('idSistemaInformacion', null, array('id' => 'idSistemaInformacion')) !!}
            </div>
          </div>
        </div>


		
		<div class="form-group" id='test'>
          {!!Form::label('nombreSistemaInformacion', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
				{!!Form::text('nombreSistemaInformacion',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del sistema de informaci&oacute;n'])!!}
            </div>
          </div>
    </div>

          <div class="form-group" id='test'>
        {!! Form::label('webSistemaInformacion', 'Sistema Web', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-1">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-check-circle "></i>
            </span>
            {!! Form::checkbox('webSistemaInformacion', 1, null, ['class' => 'form-control']) !!}
          </div>
        </div>
        </div>
</br></br></br></br></br>
        <div class="form-group" id='test'>
          {!!Form::label('ipSistemaInformacion', 'Host', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-certificate"></i>
              </span>
        {!!Form::text('ipSistemaInformacion',null,['class'=>'form-control','placeholder'=>'Ingresa la IP del sistema de informaci&oacute;n'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('puertoSistemaInformacion', 'Puerto', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-cubes"></i>
              </span>
        {!!Form::text('puertoSistemaInformacion',null,['class'=>'form-control','placeholder'=>'Ingresa el puerto del sistema de informaci&oacute;n'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('usuarioSistemaInformacion', 'Usuario', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
        {!!Form::text('usuarioSistemaInformacion',null,['class'=>'form-control','placeholder'=>'Ingresa el usuario del sistema de informaci&oacute;n'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('claveSistemaInformacion', 'Clave', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>
              </span>
        {!!Form::password('claveSistemaInformacion',array('class'=>'form-control','placeholder'=>'Ingresa la contrase&ntilde;a'))!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('bdSistemaInformacion', 'Base de datos', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-database"></i>
              </span>
        {!!Form::text('bdSistemaInformacion',null,['class'=>'form-control','placeholder'=>'Ingresa la base del sistema de informaci&oacute;n'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('motorbdSistemaInformacion', 'Motor base de datos', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-database"></i>
              </span>
        {!!Form::text('motorbdSistemaInformacion',null,['class'=>'form-control','placeholder'=>'Ingresa el motor de la base del sistema de informaci&oacute;n'])!!}
            </div>
          </div>
        </div>

    </br></br></br></br></br></br></br></br></br></br>   
    <input type="hidden" id="token" value="{{csrf_token()}}"/>
    </fieldset>
	@if(isset($sistemainformacion))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
        {!!Form::button('Probar conexión',["class"=>"btn btn-success", 'onclick' => 'conectar(document.getElementById(\'ipSistemaInformacion\').value, document.getElementById(\'puertoSistemaInformacion\').value, document.getElementById(\'usuarioSistemaInformacion\').value, document.getElementById(\'claveSistemaInformacion\').value, document.getElementById(\'bdSistemaInformacion\').value, document.getElementById(\'motorbdSistemaInformacion\').value)'])!!} 
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  		@endif
 	@else
        {!!Form::button('Probar conexión',["class"=>"btn btn-success", 'onclick' => 'conectar(document.getElementById(\'ipSistemaInformacion\').value, document.getElementById(\'puertoSistemaInformacion\').value, document.getElementById(\'usuarioSistemaInformacion\').value, document.getElementById(\'claveSistemaInformacion\').value, document.getElementById(\'bdSistemaInformacion\').value, document.getElementById(\'motorbdSistemaInformacion\').value)'])!!} 
    		{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 	@endif

	{!! Form::close() !!}
	</div>
</div>
@stop