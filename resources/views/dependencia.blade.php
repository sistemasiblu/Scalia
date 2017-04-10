@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Dependencia</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/dependencia.js')!!}

<?php
$datos =  isset($dependencia) ? $dependencia->dependenciaPermiso : array();


for($i = 0; $i < count($datos); $i++)
{
  $ids = explode(',', $datos[$i]["Rol_idRol"]);

   $nombres = DB::table('rol')
             ->select(DB::raw('group_concat(nombreRol) AS nombreRol'))
            ->whereIn('idRol',$ids)
            ->get();
  $vble = get_object_vars($nombres[0] );
  $datos[$i]["nombreRolPermiso"] = $vble["nombreRol"];
}
?>

<script>
    var idRol = '<?php echo isset($idRol) ? $idRol : "";?>';
    var nombreRol = '<?php echo isset($nombreRol) ? $nombreRol : "";?>';

    var dependenciapermisos = '<?php echo (isset($dependencia) ? json_encode($dependencia->dependenciaPermiso) : "");?>';
    dependenciapermisos = (dependenciapermisos != '' ? JSON.parse(dependenciapermisos) : '');
    var valorDependencia = ['','', 0];

    $(document).ready(function(){

      permisos = new Atributos('permisos','contenedor_permisos','permisos_');

      permisos.altura = '35px';
      permisos.campoid = 'idDependenciaPermiso';
      permisos.campoEliminacion = 'eliminarDependenciaPermiso';

      permisos.campos   = ['Rol_idRol', 'nombreRolPermiso', 'idDependenciaPermiso'];
      permisos.etiqueta = ['input', 'input', 'input'];
      permisos.tipo     = ['hidden', 'text', 'hidden'];
      permisos.estilo   = ['', 'width: 900px;height:35px;' ,''];
      permisos.clase    = ['','', '', ''];
      permisos.sololectura = [true,true,true];
      for(var j=0, k = dependenciapermisos.length; j < k; j++)
      {
        permisos.agregarCampos(JSON.stringify(dependenciapermisos[j]),'L');
        console.log(JSON.stringify(dependenciapermisos[j]))
      }

    });

  </script>


	 @if(isset($dependencia))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($dependencia,['route'=>['dependencia.destroy',$dependencia->idDependencia],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($dependencia,['route'=>['dependencia.update',$dependencia->idDependencia],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'dependencia.store','method'=>'POST'])!!}
  @endif


<div id='form-section' >

  <fieldset id="dependencia-form-fieldset"> 
      <div class="form-group" id='test'>
        {!!Form::label('codigoDependencia', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-barcode"></i>
            </span>
            {!!Form::text('codigoDependencia',null,['class'=>'form-control','placeholder'=>'Ingresa el código del sistema de la dependencia'])!!}
            {!!Form::hidden('idDependencia', null, array('id' => 'idDependencia')) !!}
            {!!Form::hidden('eliminarDependenciaPermiso', null, array('id' => 'eliminarDependenciaPermiso')) !!}
          </div>
        </div>
      </div>


    
      <div class="form-group" id='test'>
          {!!Form::label('nombreDependencia', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
        {!!Form::text('nombreDependencia',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la dependencia'])!!}
            </div>
          </div>
      </div>



      <div class="form-group" id='test'>
        {!!Form::label('abreviaturaDependencia', 'Abreviatura', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-font  "></i>
            </span>
      {!!Form::text('abreviaturaDependencia',null,['class'=>'form-control','placeholder'=>'Ingresa la abreviatura de la dependencia'])!!}
          </div>
        </div>
      </div>  

      <div class="form-group" id='test'>
        {!!Form::label('directorioDependencia', 'Directorio', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-folder-open  "></i>
            </span>
        {!!Form::text('directorioDependencia',null,['class'=>'form-control','placeholder'=>'Ingresa la abreviatura de la dependencia'])!!}
          </div>
        </div>
      </div>  

      <div class="form-group" id='test'>
        {!!Form::label('Dependencia_idPadre', 'Dependencia Administrativa', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-bars  "></i>
            </span>
        {!!Form::select('Dependencia_idPadre',$dependenciaP, (isset($dependencia) ? $dependencia->Dependencia_idPadre : 0),["class" => "select form-control", "placeholder" =>"Seleccione"])!!}
          </div>
        </div>
      </div>      

      <br><br><br><br><br>

        <div class="form-group">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">Permisos</div>
              <div class="panel-body">
                <div class="panel-group" id="accordion">
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#permisoDocumento">Permisos</a>
                      </h4>
                    </div>
                    <div id="permisoDocumento" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-12">
                            <div class="row show-grid">
                              <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="abrirModalRol();">
                                <span class="glyphicon glyphicon-plus"></span>
                              </div>
                              <div class="col-md-1" style="width: 900px;">Rol</div>
                              <div id="contenedor_permisos"> 
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

	@if(isset($dependencia))
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

<div id="myModalRol" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:100%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Roles</h4>
      </div>
      <div class="modal-body">
      <iframe style="width:100%; height:500px; " id="rol" name="rol" src="{!! URL::to ('rolselect')!!}"> </iframe> 
      </div>
    </div>
  </div>
</div>