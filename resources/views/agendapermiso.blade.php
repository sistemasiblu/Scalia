@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Permisos de Visualización de Agenda</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/agendapermiso.js')!!}

<script>
    var idUsuario = '<?php echo isset($idUsuario) ? $idUsuario : "";?>';
    var nombreUsuario = '<?php echo isset($nombreUsuario) ? $nombreUsuario : "";?>';

    var usuarios = [JSON.parse(idUsuario), JSON.parse(nombreUsuario)];

    var idCategoriaAgenda = '<?php echo isset($idCategoriaAgenda) ? $idCategoriaAgenda : "";?>';
    var nombreCategoriaAgenda = '<?php echo isset($nombreCategoriaAgenda) ? $nombreCategoriaAgenda : "";?>';

    var categoria = [JSON.parse(idCategoriaAgenda), JSON.parse(nombreCategoriaAgenda)];

    var agendapermisodetalle = '<?php echo (isset($agendapermisodetalle) ? json_encode($agendapermisodetalle) : "");?>';
    agendapermisodetalle = (agendapermisodetalle != '' ? JSON.parse(agendapermisodetalle) : '');
    var valorAgendaPermiso = ['','', 0];

    $(document).ready(function(){

      agendapermiso = new Atributos('agendapermiso','contenedor_permisos','permisos_');

      agendapermiso.altura = '35px';
      agendapermiso.campoid = 'idAgendaPermisoDetalle';
      agendapermiso.campoEliminacion = 'eliminarAgendaPermiso';

      agendapermiso.campos   = ['Users_idPropietario', 'CategoriaAgenda_idCategoriaAgenda', 'adicionarAgendaPermisoDetalle', 'modificarAgendaPermisoDetalle', 'eliminarAgendaPermisoDetalle', 'consultarAgendaPermisoDetalle', 'idAgendaPermisoDetalle','AgendaPermiso_idAgendaPermiso'];
      agendapermiso.etiqueta = ['select', 'select', 'checkbox', 'checkbox', 'checkbox', 'checkbox', 'input', 'input'];
      agendapermiso.tipo     = ['', '', 'checkbox', 'checkbox', 'checkbox', 'checkbox', 'hidden', 'hidden'];
      agendapermiso.estilo   = ['width: 250px;height:35px;', 'width: 250px;height:35px;' ,'width: 100px;height:35px;display:inline-block', 'width: 100px;height:35px;display:inline-block', 'width: 100px;height:35px;display:inline-block', 'width: 100px;height:35px;display:inline-block', '', ''];
      agendapermiso.clase    = ['','', '', '', '', '', '', ''];
      agendapermiso.sololectura = [false,false,false,false,false,false,false,false];
      agendapermiso.opciones = [usuarios, categoria, '', '', '', '', '', '']
      for(var j=0, k = agendapermisodetalle.length; j < k; j++)
      {
        agendapermiso.agregarCampos(JSON.stringify(agendapermisodetalle[j]),'L');
        console.log(JSON.stringify(agendapermisodetalle[j]))
      }

    });

  </script>

	 @if(isset($agendapermiso))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($agendapermiso,['route'=>['agendapermiso.destroy',$agendapermiso->idAgendaPermiso],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($agendapermiso,['route'=>['agendapermiso.update',$agendapermiso->idAgendaPermiso],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'agendapermiso.store','method'=>'POST'])!!}
  @endif


<div id='form-section' >

  <fieldset id="agendapermiso-form-fieldset"> 
      <div class="form-group" id='test'>
        {!!Form::label('Users_idAutorizado', 'Usuario', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-bars  "></i>
            </span>
            {!!Form::select('Users_idAutorizado',$usuario, (isset($agendapermiso) ? $agendapermiso->Users_idAutorizado : 0),["class" => "select form-control", "placeholder" =>"Seleccione"])!!}
            {!!Form::hidden('idAgendaPermiso', null, array('id' => 'idAgendaPermiso')) !!}
            {!!Form::hidden('eliminarAgendaPermiso', null, array('id' => 'eliminarAgendaPermiso')) !!}
          </div>
        </div>
      </div>      

      <br><br>

      <div class="panel panel-primary">
        <div class="panel-heading">Permisos</div>
          <div class="panel-body">
              <div class="col-md-12">
                  <div id="casoscrm">
                      <div class="form-group" id='test'>
                        <div class="col-sm-12">
                          <div class="row show-grid">
                            <div class="col-md-1" style="width: 42px; height: 42px; cursor: pointer;" onclick="agendapermiso.agregarCampos(valorAgendaPermiso,'A');">
                              <span class="glyphicon glyphicon-plus"></span>
                            </div>
                            <div class="col-md-1" style="width: 250px;">Acceso a la agenda de</div>
                            <div class="col-md-1" style="width: 250px;">En la categoria</div>
                            <div class="col-md-1" style="width: 100px;">Adicionar</div>
                            <div class="col-md-1" style="width: 100px;">Modificar</div>
                            <div class="col-md-1" style="width: 100px;">Eliminar</div>
                            <div class="col-md-1" style="width: 100px;">Consultar</div>
                            <div id="contenedor_permisos"> 
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
    </div>
    <input type="hidden" id="token" value="{{csrf_token()}}"/>
    </fieldset>

	@if(isset($agendapermiso))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
 	@endif

	{!! Form::close() !!}
</div>
@stop
