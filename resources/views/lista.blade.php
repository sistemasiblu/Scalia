@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Lista</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/lista.js')!!}

<script>


    var sublista = '<?php echo (isset($lista) ? json_encode($lista->sublistas) : "");?>';
    sublista = (sublista != '' ? JSON.parse(sublista) : '');
    var valorlista = ['','', 0];

    $(document).ready(function(){
      tipoBoton();

      lista = new Atributos('lista','contenedor_lista','lista_');

      lista.altura = '35px';
      lista.campoid = 'idSubLista';
      lista.campoEliminacion = 'eliminarSubLista';

      lista.campos   = ['codigoSubLista', 'nombreSubLista', 'idSubLista'];
      lista.etiqueta = ['input', 'input', 'input'];
      lista.tipo     = ['text', 'text', 'hidden'];
      lista.estilo   = ['width: 100px;height:35px;','width: 200px;height:35px;', ''];
      lista.clase    = ['','', ''];
      lista.sololectura = [false,false,false];
      for(var j=0, k = sublista.length; j < k; j++)
      {
        lista.agregarCampos(JSON.stringify(sublista[j]),'L');
      }

    });

  </script>


	@if(isset($lista))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($lista,['route'=>['lista.destroy',$lista->idLista],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($lista,['route'=>['lista.update',$lista->idLista],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'lista.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="lista-form-fieldset">
    <div class="form-group" id='test'>
      {!!Form::label('codigoLista', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-barcode"></i>
          </span>
          {!!Form::text('codigoLista',null,['class'=>'form-control','placeholder'=>'Ingresa el código de la lista'])!!}
          {!!Form::hidden('idLista', null, array('id' => 'idLista')) !!}
          {!!Form::hidden('eliminarSubLista', null, array('id' => 'eliminarSubLista')) !!}
        </div>
      </div>
    </div>


    
    <div class="form-group" id='test'>
      {!!Form::label('nombreLista', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o "></i>
            </span>
            {!!Form::text('nombreLista',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la lista'])!!}
          </div>
        </div>
    </div>

<br/> <br/> <br/> <br/>

<div class="form-group">
  <div class="col-lg-12">
    <div class="panel panel-primary">
      <div class="panel-heading">Detalles</div>
      <div class="panel-body">
        <div class="panel-group" id="accordion">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#conexionLista">Conexi&oacute;n</a>
              </h4>
            </div>
            <div id="conexionLista" class="panel-collapse collapse">
              <div class="panel-body">
                <input type="hidden" id="token" value="{{csrf_token()}}"/>

                <div class="form-group" id='test'>
                  {!! Form::label('origenLista', 'Origen', array('class' => 'col-sm-2 control-label')) !!}
                  <div class="col-sm-6">
                    <div class="input-group">
                      {!!Form::radio('origenLista', '2', true, ['onclick' => 'ocultarSistema(this)'])!!} Sistema
                      &nbsp;
                      {!!Form::radio('origenLista', '1', false, ['onclick' => 'ocultarSistema(this)'])!!} Manual
                    </div>
                  </div>
                </div>
                </br>

                <div id="sistemainformacion">
                  <div class="form-group" id='test'>
                    {!! Form::label('SistemaInformacion_idSistemaInformacion', 'Sistema de informaci&oacute;n', array('class' => 'col-sm-2 control-label')) !!}
                    <div class="col-sm-10">
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-paper-plane   "></i>
                        </span>
                      {!!Form::select('SistemaInformacion_idSistemaInformacion',$sistemainformacion, (isset($documento) ? $documento->SistemaInformacion_idSistemaInformacion : 0),['class'=>'select form-control', 'onchange' => 'consultarTablaVista(this.value)','placeholder'=>'Selecciona el sistema de informaci&oacute;n'])!!}
                      </div>
                    </div>
                    </br>
                  </div>
                </div>
                        

                <div class="form-group" id='test'>
                {!! Form::label('tipoConsultaLista', 'Tipo de consulta', array('class' => 'col-sm-2 control-label')) !!}
                  <div class="col-sm-6">
                    <div class="input-group">
                      {!!Form::radio('tipoConsultaLista', '1', true, ['onclick' => 'ocultarConsulta(this)'])!!} Tabla
                      &nbsp;
                      {!!Form::radio('tipoConsultaLista', '2', false, ['onclick' => 'ocultarConsulta(this)'])!!} Vista
                      &nbsp;
                      {!!Form::radio('tipoConsultaLista', '3', false, ['onclick' => 'ocultarConsulta(this)'])!!} SQL
                      &nbsp;
                      {!!Form::radio('tipoConsultaLista', '4', false, ['onclick' => 'ocultarConsulta(this)'])!!} Ninguna
                    </div>
                  </div>
                </div>
                </br>

                <div id="lista">
                  <div class="form-group" id='test'>
                    {!! Form::label('tablaLista', 'Tabla / Vista', array('class' => 'col-sm-2 control-label')) !!}
                    <div class="col-sm-10">
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-paper-plane-o  "></i>
                        </span>
                      {!!Form::select('tablaLista',array('Seleccione'), (isset($documento) ? $documento->tablaDocumento : null),['class'=>'select form-control', 'onchange'=>'consultarCampos(document.getElementById(\'SistemaInformacion_idSistemaInformacion\').value, this.value);'])!!}
                      </div>
                    </div>
                  </div>
                </div>


                <div id="consulta">
                  <div class="form-group" id='test'>
                    {!!Form::label('consultaLista', 'Consulta', array('class' => 'col-sm-2 control-label')) !!}
                    <div class="col-sm-10">
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-search "></i>
                        </span>
                    {!!Form::textarea('consultaLista',null,['class'=>'form-control','style'=>'height:100px','placeholder'=>'Ingresa la consulta'])!!}
                      </div>
                    </div>
                  </div>
                </div>
                <div id="consulta">
                  <div class="form-group" id='test'>
                    {!!Form::label('condicionLista', 'Filtrar por', array('class' => 'col-sm-2 control-label')) !!}
                    <div class="col-sm-10">
                      <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-search "></i>
                        </span>
                    {!!Form::text('condicionLista',null,['class'=>'form-control','placeholder'=>'Ingresa la condición'])!!}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>  

          <div class="panel panel-info">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#opcionLista">Opciones</a>
              </h4>
            </div>
            <div id="opcionLista" class="panel-collapse collapse">
              <div class="panel-body">
                <div class="form-group" id='test'>
                  <div class="col-sm-10" style="width: 100%;">
                    <div class="panel-body">
                      <div class="form-group" id='test'>
                        <div class="col-sm-12">
                          <div class="row show-grid">
                            <div class="col-md-1" style="width: 40px; height: 69px; cursor: pointer;" onclick="lista.agregarCampos(valorlista,'A')">
                              <span id="boton" class="glyphicon glyphicon-plus"></span>
                            </div>
                            <div class="col-md-1" style="width: 100px; height: 69px; ">Código <br/> <select id="select1"></select></div>
                            <div class="col-md-1" style="width: 200px; height: 69px;">Nombre <br/> <select id="select2"></select></div>
                            <div class="col-md-1" style="width: 218px; height: 69px;"><input type="text" name="a"> <br/> <select id="select3"></select></div>
                            <div class="col-md-1" style="width: 218px; height: 69px;"><input type="text" name="a"> <br/> <select id="select4"></select></div>
                            <div class="col-md-1" style="width: 218px; height: 69px;"><input type="text" name="a"> <br/> <select id="select5"></select></div>
                            <div id="contenedor_lista">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="llenarMultiregistro(document.getElementById('SistemaInformacion_idSistemaInformacion').value, document.getElementById('condicionLista').value)">Prueba</button>
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
    
	@if(isset($lista))
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