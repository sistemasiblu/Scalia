@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Sitio Web</center></h3>@stop

@section('content')
@include('alerts.request')
	@if(isset($sitioweb))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($sitioweb,['route'=>['sitioweb.destroy',$sitioweb->idSitioWeb],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($sitioweb,['route'=>['sitioweb.update',$sitioweb->idSitioWeb],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'sitioweb.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="sitioweb-form-fieldset">	
      <div class="form-group" id='test'>
          {!!Form::label('descripcionSitioWeb', 'Descripci&oacute;n', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
        {!!Form::textarea('descripcionSitioWeb',null,['class'=>'form-control','style'=>'height:60px','placeholder'=>'Ingresa la descripci&oacute;n del sitio Web'])!!}
            </div>
          </div>
  

    
    <div class="form-group" id='test'>
          {!!Form::label('urlSitioWeb', 'URL', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-share-alt "></i>
              </span>
        {!!Form::text('urlSitioWeb',null,['class'=>'form-control','placeholder'=>'Ingrese la URL del sitio Web'])!!}
            </div>
          </div>

    </fieldset>
	@if(isset($sitioweb))
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
</div>
@stop