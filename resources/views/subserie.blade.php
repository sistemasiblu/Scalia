@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Sub Serie</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/subserie.js')!!}

<?php
$datos =  isset($subserie) ? $subserie->subseriepermiso : array();

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

    var subseriepermisos = '<?php echo (isset($subserie) ? json_encode($subserie->subseriepermiso) : "");?>';
    subseriepermisos = (subseriepermisos != '' ? JSON.parse(subseriepermisos) : '');
    var valorSubSerie = ['','', 0, 0];

    $(document).ready(function(){

      permisos = new Atributos('permisos','contenedor_permisos','permisos_');

      permisos.altura = '35px';
      permisos.campoid = 'idSubSeriePermiso';
      permisos.campoEliminacion = 'eliminarSubSeriePermiso';

      permisos.campos   = ['Rol_idRol', 'nombreRolPermiso', 'idSubSeriePermiso', 'SubSerie_idSubSerie'];
      permisos.etiqueta = ['input', 'input', 'input', 'input'];
      permisos.tipo     = ['hidden', 'text', 'hidden', 'hidden'];
      permisos.estilo   = ['', 'width: 900px;height:35px;' ,'', ''];
      permisos.clase    = ['','', '', '', ''];
      permisos.sololectura = [true,true,true, true];
      for(var j=0, k = subseriepermisos.length; j < k; j++)
      {
        permisos.agregarCampos(JSON.stringify(subseriepermisos[j]),'L');
      }

    });

  </script>

<?php
$datos =  isset($subserie) ? $subserie->subseriedetalle : array();

for($i = 0; $i < count($datos); $i++)
{
  $ids = explode(',', $datos[$i]["Documento_idDocumento"]);

   $nombres = DB::table('documento')
             ->select(DB::raw('group_concat(nombreDocumento) AS nombreDocumento'))
            ->whereIn('idDocumento',$ids)
            ->get();
  $vble = get_object_vars($nombres[0] );
  $datos[$i]["nombreDocumentoSubserie"] = $vble["nombreDocumento"];
}
?>

  <script>

    var idDocumento = '<?php echo isset($idDocumento) ? $idDocumento : "";?>';
    var nombreDocumento = '<?php echo isset($nombreDocumento) ? $nombreDocumento : "";?>';

   // documento = [JSON.parse(idDocumento), JSON.parse(nombreDocumento)];

    var subseriedocumento = '<?php echo (isset($subserie) ? json_encode($subserie->subseriedetalle) : "");?>';
    subseriedocumento = (subseriedocumento != '' ? JSON.parse(subseriedocumento) : '');

    var valorSubserieDocumento = [0, '', 0, 0];

    $(document).ready(function(){

      documento = new Atributos('subseriedetalle','contenedor_subseriedetalle','subseriedetalle_');

      documento.altura = '35px';
      documento.campoid = 'idSubSerieDetalle';
      documento.campoEliminacion = 'eliminarSubSerieDetalle';

      documento.campos   = ['Documento_idDocumento', 'nombreDocumentoSubserie','idSubSerieDetalle', 'SubSerie_idSubSerie'];
      documento.etiqueta = ['input', 'input','input', 'input'];
      documento.tipo     = ['hidden', 'text','hidden', 'hidden'];
      documento.estilo   = ['','width: 900px;height:35px;', '', ''];
      documento.clase    = ['', '', '', ''];
      documento.sololectura = [false,false,false, false];

      for(var j=0, k = subseriedocumento.length; j < k; j++)
      { 
        documento.agregarCampos(JSON.stringify(subseriedocumento[j]),'L');
      }

    });

  </script>


	 @if(isset($subserie))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($subserie,['route'=>['subserie.destroy',$subserie->idSubSerie],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($subserie,['route'=>['subserie.update',$subserie->idSubSerie],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'subserie.store','method'=>'POST'])!!}
  @endif


<div id='form-section' >

  <fieldset id="subserie-form-fieldset"> 
      <div class="form-group" id='test'>
        {!!Form::label('codigoSubSerie', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-barcode"></i>
            </span>
            {!!Form::text('codigoSubSerie',null,['class'=>'form-control','placeholder'=>'Ingresa el código del sistema de la sub serie'])!!}
            {!!Form::hidden('idSubSerie', null, array('id' => 'idSubSerie')) !!}
            {!!Form::hidden('eliminarSubSeriePermiso', null, array('id' => 'eliminarSubSeriePermiso')) !!}
            {!!Form::hidden('eliminarSubSerieDetalle', null, array('id' => 'eliminarSubSerieDetalle')) !!}
          </div>
        </div>
      </div>


    
      <div class="form-group" id='test'>
          {!!Form::label('nombreSubSerie', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
        {!!Form::text('nombreSubSerie',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre de la sub serie'])!!}
            </div>
          </div>
      </div>

      <div class="form-group" id='test'>
        {!!Form::label('directorioSubSerie', 'Directorio', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-folder-open  "></i>
            </span>
      {!!Form::text('directorioSubSerie',null,['class'=>'form-control','placeholder'=>'Ingresa el directorio de la sub serie'])!!}
          </div>
        </div>
      </div>    

      <div class="form-group" id='test'>
        {!!Form::label('Serie_idSerie', 'Serie', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-bars"></i>
            </span>
          {!!Form::select('Serie_idSerie',$serie, (isset($subserie) ? $subserie->Serie_idSerie : 0),["class" => "select form-control" ,"placeholder" =>"Seleccione una serie"])!!}  
          </div>
        </div>
      </div>    

      <br><br><br><br><br><br><br>

        <div class="form-group">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">Detalles</div>
              <div class="panel-body">
                <div class="panel-group" id="accordion">

                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#documento">Documentos</a>
                      </h4>
                    </div>
                    <div id="documento" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-12">
                            <div class="row show-grid">
                              <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="abrirModalDocumento();">
                                <span class="glyphicon glyphicon-plus"></span>
                              </div>
                              <div class="col-md-1" style="width: 900px;">Documento</div>
                              <div id="contenedor_subseriedetalle"> 
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

<input type="hidden" id="token" value="{{csrf_token()}}"/>

	@if(isset($subserie))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary", 'id'=>'Modificar',"onclick"=>'validarFormulario(event);'])!!}
      @endif
  @else
      {!!Form::submit('Adicionar',["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
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

<div id="myModalDocumento" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:100%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Documentos</h4>
      </div>
      <div class="modal-body">
      <iframe style="width:100%; height:500px; " id="rol" name="rol" src="{!! URL::to ('documentoselect')!!}"> </iframe> 
      </div>
    </div>
  </div>
</div>