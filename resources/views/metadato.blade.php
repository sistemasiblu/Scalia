@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Metadatos</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/metadato.js')!!}

	 @if(isset($metadato))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($metadato,['route'=>['metadato.destroy',$metadato->idMetadato],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($metadato,['route'=>['metadato.update',$metadato->idMetadato],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'metadato.store','method'=>'POST'])!!}
  @endif


<div id='form-section' >

  <fieldset id="metadato-form-fieldset"> 
      <div class="form-group" id='test'>
        {!!Form::label('tituloMetadato', 'Título', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-font"></i>
            </span>
            {!!Form::text('tituloMetadato',null,['class'=>'form-control','placeholder'=>'Ingrese el título del metadato'])!!}
            {!!Form::hidden('idMetadato', null, array('id' => 'idMetadato')) !!}
          </div>
        </div>
      </div>


    
      <div class="form-group" id='test'>
          {!!Form::label('tipoMetadato', 'Tipo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
        {!! Form::select('tipoMetadato', ['' => 'Seleccione','Texto' =>'Texto','Fecha' => 'Fecha','Numero' => 'Numero','Hora' => 'Hora', 'Lista' => 'Lista', 'Editor' => 'Editor', 'EleccionUnica' => 'Elección Unica', 'EleccionMultiple' => 'Elección Multiple', 'PaginaWeb' => 'Página Web', 'CorreoElectronico' => 'Correo Electrónico', 'Imagen' => 'Imagen'],null,['class' => 'form-control', 'onchange' => 'validarCampos()']) !!}
            </div>
          </div>
      </div>

      <div class="form-group" id='test'>
        {!!Form::label('Lista_idLista', 'Lista', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-bars  "></i>
            </span>
          {!!Form::select('Lista_idLista',$lista, (isset($metadato) ? $metadato->Lista_idLista : 0),["class" => "form-control", "placeholder" =>"Seleccione", 'disabled'])!!}
          </div>
        </div>
      </div>  

      <div class="form-group" id='test'>
        {!!Form::label('opcionMetadato', 'Opcion', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o"></i>
            </span>
          {!!Form::text('opcionMetadato',null,['class'=>'form-control','placeholder'=>'Ingresa la opción del metadato', 'readonly'])!!}
          </div>
        </div>
      </div>  

      <div class="form-group" id='test'>
        {!!Form::label('longitudMetadato', 'Longitud', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-expand"></i>
            </span>
        {!!Form::text('longitudMetadato',null,['class'=>'form-control','placeholder'=>'Ingresa la longitud del metadato'])!!}
          </div>
        </div>
      </div> 

      <div class="form-group" id='test'>
        {!!Form::label('valorBaseMetadato', 'Valor Base', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-file"></i>
            </span>
        {!!Form::text('valorBaseMetadato',null,['class'=>'form-control','placeholder'=>'Ingresa el valor base del metadato'])!!}
          </div>
        </div>
      </div> 

     </fieldset>



	@if(isset($metadato))
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