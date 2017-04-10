@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Usuarios</center></h3>@stop


@section('content')
@include('alerts.request')

	@if(isset($usuario))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($usuario,['route'=>['users.destroy',$usuario->id],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($usuario,['route'=>['users.update',$usuario->id],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'users.store','method'=>'POST'])!!}
	@endif


<div id='form-section'>

	<fieldset id="usuario-form-fieldset">	
		<div class="form-group" id='test'>
      {!! Form::label('name', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-user"></i>
          </span>
          {!!Form::text('name',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del usuario'])!!}
          {!! Form::hidden('id', null, array('id' => 'id')) !!}
        </div>
      </div>
    </div>

		
    <div class="form-group" id='test'>
      {!! Form::label('email', 'Correo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-at "></i>
          </span>
				{!!Form::text('email',null,['class'=>'form-control','placeholder'=>'Ingresa el correo electronico'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!! Form::label('password', 'Contrase&ntilde;a', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-key "></i>
          </span>
        {!!Form::password('password',array('class'=>'form-control','placeholder'=>'Ingresa la contrase&ntilde;a'))!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!! Form::label('password_confirmation', 'Confirmar Contrase&ntilde;a', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-key "></i>
          </span>
        {!!Form::password('password_confirmation',array('class'=>'form-control','placeholder'=>'Ingresa de nuevo la contrase&ntilde;a'))!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('Compania_idCompania', 'Compa&ntilde;&iacute;a', array('class' => 'col-sm-2 control-label'))!!}
      <div class="col-sm-10">
              <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-bank"></i>
                  </span>
          {!!Form::select('Compania_idCompania',$compania, (isset($usuario) ? $usuario->Compania_idCompania : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione la compa&ntilde;&iacute;a"])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('Rol_idRol', 'Rol', array('class' => 'col-sm-2 control-label'))!!}
      <div class="col-sm-10">
              <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-pencil-square-o "></i>
                  </span>
          {!!Form::select('Rol_idRol',$rol, (isset($usuario) ? $usuario->Rol_idRol : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione el rol"])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!! Form::label('Tercero_idAsociado', 'Tercero asociado', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-user"></i>
          </span>
          {!!Form::select('Tercero_idAsociado',$tercero, (isset($usuario) ? $usuario->Tercero_idAsociado : 0),["class" => "chosen-select form-control"])!!}
        </div>
      </div>
    </div>


    </fieldset>
	@if(isset($usuario))
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
@stop