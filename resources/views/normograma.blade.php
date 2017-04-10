@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Normograma</center></h3>@stop

@section('content')
@include('alerts.request')
	@if(isset($normograma))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($normograma,['route'=>['normograma.destroy',$normograma->idNormograma],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($normograma,['route'=>['normograma.update',$normograma->idNormograma],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'normograma.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="normograma-form-fieldset">	
		<div class="form-group" id='test'>
          {!!Form::label('nombreNormograma', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::text('nombreNormograma',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la norma'])!!}
              {!!Form::hidden('idNormograma', null, array('id' => 'idNormograma')) !!}
            </div>
          </div>
        </div>


      <div class="form-group" id='test'>
          {!!Form::label('descripcionNormograma', 'Descripci&oacute;n', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
        {!!Form::textarea('descripcionNormograma',null,['class'=>'form-control ckeditor','style'=>'height:100px','placeholder'=>'Ingresa la descripci&oacute;n de la norma'])!!}
            </div>
          </div>
  

    
    <div class="form-group" id='test'>
          {!!Form::label('derogada_vigenteNormograma', 'Derogada / Vigente', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-gavel "></i>
              </span>
        {!!Form::text('derogada_vigenteNormograma',null,['class'=>'form-control','placeholder'=>''])!!}
            </div>
          </div>

    </fieldset>
	@if(isset($normograma))
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

<script>
  CKEDITOR.replace(('descripcionNormograma'), {
      fullPage: true,
      allowedContent: true
    });  
</script>
@stop