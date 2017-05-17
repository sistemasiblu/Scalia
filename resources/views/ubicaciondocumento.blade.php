@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Ubicacion</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/ubicaciondocumento.js')!!}
	@if(isset($ubicaciondocumento))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($ubicaciondocumento,['route'=>['ubicaciondocumento.destroy',$ubicaciondocumento->idUbicacionDocumento],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($ubicaciondocumento,['route'=>['ubicaciondocumento.update',$ubicaciondocumento->idUbicacionDocumento],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'ubicaciondocumento.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="ubicaciondocumento-form-fieldset">	

		<div class="form-group" id='test'>
      {!!Form::label('tipoUbicacionDocumento', 'Tipo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-bars"></i>
          </span>
          {!! Form::select('tipoUbicacionDocumento', ['historiaslaborales' => 'Historias Laborales','otros' => 'Otros'],null,['class' => 'form-control', 'onchange' => 'mostrarCamposTipoUbicacion(this.value)', 'placeholder' => 'Seleccione el tipo', 'required' => 'required']) !!}
          {!!Form::hidden('idUbicacionDocumento', null, array('id' => 'idUbicacionDocumento')) !!}
          {!!Form::hidden('DependenciaLocalizacion_idDependenciaLocalizacion', null, array('id' => 'DependenciaLocalizacion_idDependenciaLocalizacion')) !!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('posicionUbicacionDocumento', 'P.L.', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-sitemap"></i>
          </span>
          {!!Form::text('posicionUbicacionDocumento',null,['class'=>'form-control', 'readonly', 'placeholder'=>'Punto de localización', 'required' => 'required'])!!}
        </div>
      </div>
    </div>

    <div id="descripcion" style="display:none" class="form-group" id='test'>
      {!!Form::label('descripcionUbicacionDocumento', 'Descripción', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
          {!!Form::text('descripcionUbicacionDocumento',null,['class'=>'form-control','placeholder'=>'Ingresa la descripción'])!!}
        </div>
      </div>
    </div>

    <div id="documento" style="display:none" class="form-group" id='test'>
      {!!Form::label('documentoTerceroUbicacionDocumento', 'Cedula', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-credit-card"></i>
          </span>
          {!!Form::text('documentoTerceroUbicacionDocumento',null,['class'=>'form-control','placeholder'=>'Ingresa el documento del empleado'])!!}
        </div>
      </div>
    </div>

    <div id="nombre" style="display:none" class="form-group" id='test'>
      {!!Form::label('nombreTerceroUbicacionDocumento', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-user"></i>
          </span>
          {!!Form::text('nombreTerceroUbicacionDocumento',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del empleado'])!!}
          {!!Form::hidden('Tercero_idTercero', null, array('id' => 'Tercero_idTercero')) !!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('numeroLegajoUbicacionDocumento', 'No. legajo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-th-large"></i>
          </span>
          {!!Form::text('numeroLegajoUbicacionDocumento',null,['class'=>'form-control','placeholder'=>'Ingresa el número de legajos', 'required' => 'required'])!!}
        </div>
      </div>
    </div>

    <div id="folio" style="display:none" class="form-group" id='test'>
      {!!Form::label('numeroFolioUbicacionDocumento', 'No. folios', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-th-list"></i>
          </span>
          {!!Form::text('numeroFolioUbicacionDocumento',null,['class'=>'form-control','placeholder'=>'Ingresa el número de folios'])!!}
        </div>
      </div>
    </div>

    <div id="fechaInicial" style="display:none" class="form-group" id='test'>
      {!!Form::label('fechaInicialUbicacionDocumento', 'Fecha Inicial', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </span>
          {!!Form::text('fechaInicialUbicacionDocumento',null,['class'=>'form-control','placeholder'=>'Seleccione la fecha Inicial'])!!}
        </div>
      </div>
    </div>

    <div id="fechaFinal" style="display:none" class="form-group" id='test'>
      {!!Form::label('fechaFinalUbicacionDocumento', 'Fecha final', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </span>
          {!!Form::text('fechaFinalUbicacionDocumento',null,['class'=>'form-control','placeholder'=>'Seleccione la fecha Final'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('TipoSoporteDocumental_idTipoSoporteDocumental', 'Tipo Soporte', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-tasks"></i>
          </span>
          {!!Form::select('TipoSoporteDocumental_idTipoSoporteDocumental',$tiposoporte, (isset($ubicaciondocumento) ? $ubicaciondocumento->TipoSoporteDocumental_idTipoSoporteDocumental : 0),["class" => "select form-control","placeholder" =>"Seleccione el tipo de soporte", 'required' => 'required'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('Dependencia_idProductora', 'Area Productora', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-bank"></i>
          </span>
          {!!Form::select('Dependencia_idProductora',$dependenciaproductora, (isset($ubicaciondocumento) ? $ubicaciondocumento->Dependencia_idProductora : 0),["class" => "select form-control","placeholder" =>"Seleccione el área productora", 'required' => 'required'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('Compania_idCompania', 'Compania', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-bank"></i>
          </span>
          {!!Form::select('Compania_idCompania',$compania, (isset($ubicaciondocumento) ? $ubicaciondocumento->Compania_idCompania : 0),["class" => "select form-control","placeholder" =>"Seleccione la compañía", 'required' => 'required'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('observacionUbicacionDocumento', 'Observaciones', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
          {!!Form::textarea('observacionUbicacionDocumento',null,['class'=>'form-control ckeditor','style'=>'height:100px','placeholder'=>'Ingresa las observaciones'])!!}
        </div>
      </div>
    </div>


  </fieldset>
	@if(isset($ubicaciondocumento))
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