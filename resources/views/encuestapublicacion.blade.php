@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Publicación de Encuesta</center></h3>@stop

@section('content')


@include('alerts.request')
{!!Html::script('js/encuestapublicacion.js')!!}

<script>
	

	var EncuestaDestino = '<?php echo (isset($encuestapublicacion) ? json_encode($encuestapublicacion->EncuestaPublicacionDestino) : "");?>';
	EncuestaDestino = (EncuestaDestino != '' ? JSON.parse(EncuestaDestino) : '');



	var valorDestino = [0,'','',''];

	$(document).ready(function(){

	  destino = new Atributos('destino','contenedor_destino','encuestadestino');

	  destino.altura = '35px';
	  destino.campoid = 'idEncuestaPublicacionDestino';
	  destino.campoEliminacion = 'eliminarDestino';

	  destino.campos   = [
	  'idEncuestaPublicacionDestino',
	  'nombreEncuestaPublicacionDestino',
	  'correoEncuestaPublicacionDestino',
	  'telefonoEncuestaPublicacionDestino'
	  ];

	  destino.etiqueta = [
	  'input',
	  'input',
	  'input',
	  'input'
	  ];

	  destino.tipo = [
	  'hidden',
	  'text',
	  'text',
	  'text'
	  ];

	  destino.estilo = [
	  '',
	  'width: 400px;height:35px;',
	  'width: 400px;height:35px;',
	  'width: 300px;height:35px;'
	  ];

	  destino.clase    = ['destinatario','','',''];
	  destino.sololectura = [false,false,false,false];  
	  destino.funciones = ['','','','','','','',''];
	  destino.completar = ['off','off','off','off'];
	  destino.opciones = ['','','','','']

	  for(var j=0, k = EncuestaDestino.length; j < k; j++)
	  {
	    destino.agregarCampos(JSON.stringify(EncuestaDestino[j]),'L');
	  }

	});

</script>


@if(isset($encuestapublicacion))
	@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
		{!!Form::model($encuestapublicacion,['route'=>['encuestapublicacion.destroy',$encuestapublicacion->idEncuestaPublicacion],'method'=>'DELETE'])!!}
	@else
		{!!Form::model($encuestapublicacion,['route'=>['encuestapublicacion.update',$encuestapublicacion->idEncuestaPublicacion],'method'=>'PUT'])!!}
	@endif
@else
	{!!Form::open(['route'=>'encuestapublicacion.store','method'=>'POST'])!!}
@endif



		<div id='form-section' >
				<fieldset id="encuestapublicacion-form-fieldset">	
					<div class="form-group" id='test'>
						{!!Form::label('nombreEncuestaPublicacion', 'Nombre', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-barcode"></i>
				              	</span>
								{!!Form::text('nombreEncuestaPublicacion',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre o descripción para la publicación'])!!}
						      	{!!Form::hidden('idEncuestaPublicacion', null, array('id' => 'idEncuestaPublicacion'))!!}
							</div>
						</div>
					</div>
					<div class="form-group" id='test'>
						{!!Form::label('fechaEncuestaPublicacion', 'Fecha', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-pencil-square-o"></i>
				              	</span>
								{!!Form::text('fechaEncuestaPublicacion',null,['class'=>'form-control','placeholder'=>'Ingresa la fecha de publicación'])!!}
				    		</div>
				    	</div>
				    </div>	
					<div class="form-group" id='test'>
						{!!Form::label('Encuesta_idEncuesta', 'Encuesta', array('class' => 'col-sm-2 control-label'))!!}
						<div class="col-sm-10">
				            <div class="input-group">
				              	<span class="input-group-addon">
				                	<i class="fa fa-flag"></i>
				              	</span>
								{!!Form::select('Encuesta_idEncuesta',$encuesta, (isset($encuestapublicacion) ? $encuestapublicacion->Encuesta_idEncuesta : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione la encuesta a publicar"])!!}
							</div>
						</div>
					</div>



        <div class="panel-body" >
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="panel-body" >
                <div class="form-group" id='test'>
                  <div class="col-sm-12">
                    <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
                      <div style="overflow:auto; height:350px;">
                        <div style="width: 100%; display: inline-block;">
                          <div class="col-md-1" style="width:40px;height: 42px; cursor:pointer;" onclick="destino.agregarCampos(valorDestino, 'A');">
                            <span class="glyphicon glyphicon-plus"></span>
                          </div>
                          <div class="col-md-1" style="width: 400px;" >Nombre</div>
                          <div class="col-md-1" style="width: 400px;" >Correo Electrónico</div>
                          <div class="col-md-1" style="width: 300px;" >Teléfono</div>
                          <div id="contenedor_destino">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>



	</fieldset>	
	@if(isset($encuestapublicacion))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary","onclick"=>'validarCampos(event);'])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary","onclick"=>'validarCampos(event);'])!!}
 	@endif


	</div>
	{!!Form::close()!!}	

	<script type="text/javascript">
	    $('#fechaEncuestaPublicacion').datetimepicker(({
    	  format: "YYYY-MM-DD"
    	}));
	</script>

@stop