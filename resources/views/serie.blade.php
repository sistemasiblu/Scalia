@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Serie</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/serie.js')!!}

<?php
$datos =  isset($serie) ? $serie->seriePermiso : array();


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

    var seriepermisos = '<?php echo (isset($serie) ? json_encode($serie->seriePermiso) : "");?>';
    seriepermisos = (seriepermisos != '' ? JSON.parse(seriepermisos) : '');
    var valorSerie = ['','', 0];

    $(document).ready(function(){

      protRol = new Atributos('protRol','contenedor_permisos','protRol_');

      protRol.altura = '35px';
      protRol.campoid = 'idSeriePermiso';
      protRol.campoEliminacion = 'eliminarSeriePermiso';

      protRol.campos   = ['Rol_idRol', 'nombreRolPermiso', 'idSeriePermiso'];
      protRol.etiqueta = ['input', 'input', 'input'];
      protRol.tipo     = ['hidden', 'text', 'hidden'];
      protRol.estilo   = ['', 'width: 900px;height:35px;' ,''];
      protRol.clase    = ['','', '', ''];
      protRol.sololectura = [true,true,true];
      for(var j=0, k = seriepermisos.length; j < k; j++)
      {
        protRol.agregarCampos(JSON.stringify(seriepermisos[j]),'L');
      }

    });

  </script>


	@if(isset($serie))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($serie,['route'=>['serie.destroy',$serie->idSerie],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($serie,['route'=>['serie.update',$serie->idSerie],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'serie.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="serie-form-fieldset">



		    <div class="form-group" id='test'>
          {!!Form::label('codigoSerie', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoSerie',null,['class'=>'form-control','placeholder'=>'Ingresa el código del sistema de la serie'])!!}
              {!!Form::hidden('idSerie', null, array('id' => 'idSerie')) !!}
              {!!Form::hidden('eliminarSeriePermiso', null, array('id' => 'eliminarSeriePermiso')) !!}
            </div>
          </div>
        </div>


		
		    <div class="form-group" id='test'>
          {!!Form::label('nombreSerie', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
				  {!!Form::text('nombreSerie',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la serie'])!!}
            </div>
          </div>
        </div>

          
        <div class="form-group" id='test'>
          {!! Form::label('directorioSerie', 'Directorio', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-folder-open"></i>
              </span>
              {!!Form::text('directorioSerie',null,['class'=>'form-control','placeholder'=>'Ingresa el directorio de la serie'])!!}
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
    
	@if(isset($serie))
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