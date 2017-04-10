@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Compa&ntilde;&iacute;a</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/compania.js')!!}
 
	@if(isset($compania))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($compania,['route'=>['compania.destroy',$compania->idCompania],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($compania,['route'=>['compania.update',$compania->idCompania],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'compania.store','method'=>'POST'])!!}
	@endif

<div id='form-section' >

	
	<fieldset id="compania-form-fieldset">	
		<div class="form-group" id='test'>
          {!! Form::label('codigoCompania', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoCompania',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la compania'])!!}
              {!! Form::hidden('idCompania', null, array('id' => 'idCompania')) !!}
            </div>
          </div>
        </div>

		    <div class="form-group" id='test'>
          {!! Form::label('nombreCompania', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
				      {!!Form::text('nombreCompania',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la compania'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!! Form::label('directorioCompania', 'Directorio', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-folder-open"></i>
              </span>
              {!!Form::text('directorioCompania',null,['class'=>'form-control','placeholder'=>'Ingresa el directorio de la compania'])!!}
            </div>
          </div>
        </div>

    </fieldset>
	@if(isset($compania))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 	@endif
	{!!Form::close()!!}
	</div>
</div>
@stop