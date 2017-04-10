@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Roles</center></h3>@stop

@section('content')
@include('alerts.request')

<!-- {!!Html::script('js/rol.js')!!} -->
  <script>
    var idOpcion = '<?php echo isset($idOpcion) ? $idOpcion : "";?>';
    var nombreOpcion = '<?php echo isset($nombreOpcion) ? $nombreOpcion : "";?>';

    var opcion = [JSON.parse(idOpcion),JSON.parse(nombreOpcion)];

    var rolOpcion = '<?php echo (isset($rol) ? json_encode($rol->rolOpcion) : "");?>';
    rolOpcion = (rolOpcion != '' ? JSON.parse(rolOpcion) : '');
    var valorPermisos = ['',0,0,0,0,''];

    $(document).ready(function(){


      permisos = new Atributos('permisos','contenedor_permisos','permisos_');

      permisos.altura = '35px';
      permisos.campoid = 'idRolOpcion';
      permisos.campoEliminacion = 'eliminarRol';

      permisos.campos   = ['Opcion_idOpcion',   'adicionarRolOpcion','modificarRolOpcion','eliminarRolOpcion','consultarRolOpcion', 'idRolOpcion'];
      permisos.etiqueta = ['select', 'checkbox', 'checkbox', 'checkbox', 'checkbox', 'input'];
      permisos.tipo     = ['', 'checkbox', 'checkbox', 'checkbox', 'checkbox', 'hidden'];
      permisos.estilo   = ['width: 600px;height:35px;','width: 70px;height:30px;display:inline-block;','width: 70px;height:30px;display:inline-block;','width: 70px;height:30px;display:inline-block;','width: 70px;height:30px;display:inline-block;', ''];
      permisos.clase = ['chosen-select form-control','','','','', ''];
      permisos.sololectura = [false,false,false,false,false,false];
      permisos.opciones = [opcion,'','','','','']
      // permisos.nombreOpcion =  JSON.parse(nombreOpcion);
      // permisos.valorOpcion =  JSON.parse(idOpcion);
      for(var j=0, k = rolOpcion.length; j < k; j++)
      {
        permisos.agregarCampos(JSON.stringify(rolOpcion[j]),'L');
      }

    });

  </script>
	@if(isset($rol))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($rol,['route'=>['rol.destroy',$rol->idRol],'method'=>'DELETE', 'files' => true])!!}
		@else
			{!!Form::model($rol,['route'=>['rol.update',$rol->idRol],'method'=>'PUT', 'files' => true])!!}
		@endif
	@else
		{!!Form::open(['route'=>'rol.store','method'=>'POST', 'files' => true])!!}
	@endif


<div id='form-section' >

	<fieldset id="rol-form-fieldset">	
		<div class="form-group" id='test'>
          {!! Form::label('codigoRol', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoRol',null,['class'=>'form-control','placeholder'=>'Ingresa el codigo del rol'])!!}
              {!! Form::hidden('idRol', null, array('id' => 'idRol')) !!}
              {!! Form::hidden('eliminarRol', null, array('id' => 'eliminarRol')) !!}
            </div>
          </div>
        </div>


		
		    <div class="form-group" id='test'>
          {!! Form::label('nombreRol', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
      				{!!Form::text('nombreRol',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del rol'])!!}
            </div>
          </div>
        </div>
      <div class="panel-body">
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                <div class="col-md-1" style="width: 40px; cursor: pointer;" onclick="permisos.agregarCampos(valorPermisos,'A')">
                  <span class="glyphicon glyphicon-plus"></span>
                </div>
                <div class="col-md-1" style="width: 600px;">Opci&oacute;n</div>
                <div class="col-md-1" style="width: 70px;"><center><span title="Adicionar" class="fa fa-plus"></span></center></div>
                <div class="col-md-1" style="width: 70px;"><center><span title="Modificar" class="fa fa-pencil"></span></center></div>
                <div class="col-md-1" style="width: 70px;"><center><span title="Eliminar / Anular" class="fa fa-trash"></span></center></div>
                <div class="col-md-1" style="width: 70px;"><center><span title="Consultar / Imprimir" class="fa fa-print"></span></center></div>
                <div id="contenedor_permisos">
                </div>
              </div>
            </div>
          </div>
        </div>
    </fieldset>
	@if(isset($rol))
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