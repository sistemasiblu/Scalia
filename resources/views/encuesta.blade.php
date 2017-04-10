@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Encuesta</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/encuesta.js')!!}
{!!Html::style('css/encuesta.css')!!}

<script>
    var EncuestaDetalle = '<?php echo (isset($encuestaDetalle) ? json_encode($encuestaDetalle) : "");?>';
    EncuestaDetalle = (EncuestaDetalle != '' ? JSON.parse(EncuestaDetalle) : '');

    var EncuestaRol = '<?php echo (isset($encuestaRol) ? json_encode($encuestaRol) : "");?>';
    EncuestaRol = (EncuestaRol != '' ? JSON.parse(EncuestaRol) : '');

  
   
    var valorPregunta = [0,'',0,'0000-00-00','00:00',0];

    $(document).ready(function(){


      pregunta = new Propiedades('pregunta','contenedor_pregunta','pregunta');

      pregunta.altura = '36px;';
      pregunta.campoid = 'idEncuestaPregunta';
      pregunta.campoEliminacion = 'eliminarPregunta';

      var reg = 0;
      var nPreg = 0;
      while(reg < EncuestaDetalle.length)
      {
        var preguntaAnt = EncuestaDetalle[reg]["idEncuestaPregunta"];
        pregunta.agregarPregunta(JSON.stringify(EncuestaDetalle[reg]),'L');
        
        // ejecutamos el onchange de la lista de tipo para que muestre el detalle de opciones
        $("#tipoRespuestaEncuestaPregunta"+nPreg).trigger("change");


        while(reg < EncuestaDetalle.length &&  preguntaAnt == EncuestaDetalle[reg]["idEncuestaPregunta"])
        {
          opcionPregunta.agregarCampos(JSON.stringify(EncuestaDetalle[reg]),'L', nPreg);
          reg++;
        }
        nPreg++;
      }


      protRol = new Atributos('protRol','contenedor_protRol','encuestarol');

      protRol.altura = '35px';
      protRol.campoid = 'idEncuestaRol';
      protRol.campoEliminacion = 'eliminarRol';

      protRol.campos   = [
      'idEncuestaRol',
      'Rol_idRol',
      'nombreRol',
      'adicionarEncuestaRol',
      'modificarEncuestaRol',
      'consultarEncuestaRol',
      'eliminarEncuestaRol',
      'publicarEncuestaRol'
      ];

      protRol.etiqueta = [
      'input',
      'input',
      'input',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox'
      ];

      protRol.tipo = [
      'hidden',
      'hidden',
      'text',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox',
      'checkbox'
      ];

      protRol.estilo = [
      '',
      '',
      'width: 530px;height:35px;',
      'width: 70px;height:35px; display:inline-block;',
      'width: 70px;height:35px; display:inline-block;',
      'width: 70px;height:35px; display:inline-block;',
      'width: 70px;height:35px; display:inline-block;',
      'width: 70px;height:35px; display:inline-block;'
      ];

      protRol.clase    = ['','','','','','','',''];
      protRol.sololectura = [true,true,true,true,true,true,true,true];  
      protRol.funciones = ['','','','','','','',''];
      protRol.completar = ['off','off','off','off','off','off','off','off'];
      protRol.opciones = ['','','','','','','','']

      for(var j=0, k = EncuestaRol.length; j < k; j++)
      {
        protRol.agregarCampos(JSON.stringify(EncuestaRol[j]),'L');
      }

    });

  </script>


	@if(isset($encuesta))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($encuesta,['route'=>['encuesta.destroy',$encuesta->idEncuesta],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($encuesta,['route'=>['encuesta.update',$encuesta->idEncuesta],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'encuesta.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >
<input type="hidden" id="token" value="{{csrf_token()}}"/>
	<fieldset id="encuesta-form-fieldset">	
		<div class="form-group" id='test'>
      {!!Form::label('tituloEncuesta', 'Título', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-barcode"></i>
          </span>
          {!!Form::text('tituloEncuesta',null,['class'=>'form-control','placeholder'=>'Ingrese el Título de la Encuesta'])!!}
          {!!Form::hidden('idEncuesta', null, array('id' => 'idEncuesta')) !!}
          {!!Form::hidden('totalPreguntas', null, array('id' => 'totalPreguntas')) !!}
          {!!Form::hidden('eliminarPregunta', null, array('id' => 'eliminarPregunta')) !!}
          {!!Form::hidden('eliminarOpcion', null, array('id' => 'eliminarOpcion')) !!}
          {!!Form::hidden('eliminarRol', null, array('id' => 'eliminarRol')) !!}
        </div>
      </div>
    </div>


		
		<div class="form-group" id='test'>
      {!!Form::label('descripcionEncuesta', 'Descripción', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o "></i>
          </span>
		      {!!Form::textarea('descripcionEncuesta',null,['class'=>'ckeditor','placeholder'=>'Ingresa la descripción de la Encuesta'])!!}
        </div>
      </div>
    </div>
    

    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#preguntas">Preguntas</a></li>
      <li><a data-toggle="tab" href="#permisos">Permisos</a></li>
    </ul>

    <div class="tab-content">
      <div id="preguntas" class="tab-pane fade in active">
        
        <div id="contenedor_pregunta">
        </div>

      <!-- <div class="row show-grid"> -->
          <div class="col-md-1" style="width: 40px;height: 50px;"  onclick="pregunta.agregarPregunta(valorPregunta,'A')">
            <span class="fa fa-plus-square fa-2x"></span>
          </div>
          <!-- <div class="col-md-1" style="width: 40px;height: 50px;"  onclick="pregunta.agregarPregunta(valorPregunta,'A')">
            <span class="fa fa-pencil-square fa-2x"></span>
          </div>
          <div class="col-md-1" style="width: 40px;height: 50px;"  onclick="pregunta.agregarPregunta(valorPregunta,'A')">
            <span class="fa fa-photo fa-2x"></span>
          </div>
          <div class="col-md-1" style="width: 40px;height: 50px;"  onclick="pregunta.agregarPregunta(valorPregunta,'A')">
            <span class="fa fa-film fa-2x"></span>
          </div> -->
        <!-- </div> -->
          
      </div>
      <div id="permisos" class="tab-pane fade">
        <div class="panel-body" >
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="panel-body" >
                <div class="form-group" id='test'>
                  <div class="col-sm-12">
                    <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
                      <div style="overflow:auto; height:350px;">
                        <div style="width: 100%; display: inline-block;">
                          <div class="col-md-1" style="width:40px;height: 42px; cursor:pointer;" onclick="abrirModalRol();">
                            <span class="glyphicon glyphicon-plus"></span>
                          </div>
                          <div class="col-md-1" style="width: 530px;" >Rol</div>
                          <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Adicionar" class="fa fa-plus"></span></center></div>
                      <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Modificar" class="fa fa-pencil"></span></center></div>
                      <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Consultar" class="fa fa-search"></span></center></div>
                      <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Anular" class="fa fa-trash"></span></center></div>
                      <div class="col-md-1" style="width: 70px;height: 42px; cursor:pointer;"><center><span title="Aprobar" class="fa fa-cloud-upload"></span></center></div>
                          
                          <div id="contenedor_protRol">
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
      </div> 
    </div>

  </fieldset>


	@if(isset($encuesta))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary","onclick"=>'validarCampos(event);'])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary","onclick"=>'validarCampos(event);'])!!}
 	@endif

	{!! Form::close() !!}
	</div>
</div>

<script>
    CKEDITOR.replace(('descripcionEncuesta'), {
        fullPage: true,
        allowedContent: true
      }); 
</script>

@stop

<div id="ModalRoles" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Roles</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="roles" name="roles" src="http://'.$_SERVER["HTTP_HOST"].'/rolgridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>