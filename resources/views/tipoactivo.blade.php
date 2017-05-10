@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>TIPO ACTIVO</center></h3>@stop

@section('content')
@include('alerts/request')
@if(isset($tipoactivo))
@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
{!!Form::model($tipoactivo,['route'=>['tipoactivo.destroy',$tipoactivo->idTipoActivo],'method'=>'DELETE'])!!}
@else
{!!Form::model($tipoactivo,['route'=>['tipoactivo.update',$tipoactivo->idTipoActivo],'method'=>'PUT'])!!}
@endif
@else
{!!Form::open(['route'=>'tipoactivo.store','method'=>'POST'])!!}
@endif
<!DOCTYPE html>
<html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>Tipo Activo</title>
{!!Html::script('js/general.js')!!}
{!!Html::script('assets/jquery-v2.1.4/jquery-2.1.4.min.js'); !!}
{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.css'); !!}
{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.map'); !!}
{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.css'); !!}
{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap-theme.css'); !!}
{!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap-theme.min.css'); !!}
{!!Html::script('assets/bootstrap-v3.3.5/js/bootstrap.min.js'); !!}    
{!!Html::style('assets/font-awesome-v4.3.0/css/font-awesome.min.css'); !!}  
    
<script>
//CONTENEDOR PESTAÑA 1
var tipoactivocaracteristica = '<?php echo (isset($tipoactivo) ? json_encode($tipoactivo->tipoactivocaracteristica) : "");?>';
tipoactivocaracteristica = (tipoactivocaracteristica != '' ? JSON.parse(tipoactivocaracteristica) : '');

var tipoactivodocumento= '<?php echo (isset($tipoactivo) ? json_encode($tipoactivo->tipoactivodocumento) : "");?>';
tipoactivodocumento = (tipoactivodocumento != '' ? JSON.parse(tipoactivodocumento) : '');

//CONTENEDOR PESTAÑA 3

var valorCaracteristicaTipoActivo = [0,0,''];
$(document).ready(function()
{
    caracteristicaTipoActivo=new Atributos('caracteristicaTipoActivo','contenedor-caracteristicaTipoActivo','caracteristicaTipoActivo-');
    caracteristicaTipoActivo.campoid = 'idTipoActivoCaracteristica';
    caracteristicaTipoActivo.campoEliminacion = 'caracteristicaEliminar';
    caracteristicaTipoActivo.campos=['idTipoActivoCaracteristica','nombreTipoActivoCaracteristica'];
    caracteristicaTipoActivo.etiqueta=['input','input'];
    caracteristicaTipoActivo.tipo=['hidden',''];
    caracteristicaTipoActivo.estilo=['','width:200px; height:35px;'];
    caracteristicaTipoActivo.clase=['',''];
    caracteristicaTipoActivo.sololectura=[false,false];
    caracteristicaTipoActivo.completar=['off','off'];
    caracteristicaTipoActivo.funciones=['','',''];
    var idTipoActivo = '<?php echo isset($idTipoActivo) ? $idTipoActivo : "";?>';
    var nombreTipoActivo = '<?php echo isset($nombreTipoActivo) ? $nombreTipoActivo : "";?>';

    for(var j=0; j < tipoactivocaracteristica.length; j++)
    {
       caracteristicaTipoActivo.agregarCampos(JSON.stringify(tipoactivocaracteristica[j]),'L');
    }

});

//CONTENEDOR PESTAÑA 4

var valorDocumentoTipoActivo = [0,0,''];
$(document).ready(function()
{
    documentoTipoActivo=new Atributos('documentoTipoActivo','contenedor-documentoTipoActivo','documentoTipoActivo-');
    documentoTipoActivo.campoid = 'idTipoActivoDocumento';
    documentoTipoActivo.campoEliminacion = 'documentoEliminar';
    documentoTipoActivo.campos=['idTipoActivoDocumento','descripcionTipoActivoDocumento','tipoTipoActivoDocumento','vigenciaTipoActivoDocumento','costoTipoActivoDocumento'];
    documentoTipoActivo.etiqueta=['input','input','select','input','input'];
    documentoTipoActivo.tipo=['hidden','','','','',''];
    documentoTipoActivo.estilo=['', 'width:210px; height:35px;','width:100px; height:35px;','width:120px; height:35px;','width:80px; height:35px;'];
    documentoTipoActivo.clase=['','','','','',''];
    documentoTipoActivo.sololectura=[false,false,false,false,false];
    documentoTipoActivo.completar=['off','off','off','off','off'];
    documentoTipoActivo.opciones = [[],[],[['Oem','Glp','Bsd','Documento'],['Oem','Glp','Bsd','Documento']],[],[]];      
    documentoTipoActivo.funciones=['','',''];

    var idTipoActivo = '<?php echo isset($idTipoActivo) ? $idTipoActivo : "";?>';
    var nombreTipoActivo = '<?php echo isset($nombreTipoActivo) ? $nombreTipoActivo : "";?>';

    for(var j=0; j < tipoactivodocumento.length; j++)
    {
       documentoTipoActivo.agregarCampos(JSON.stringify(tipoactivodocumento[j]),'L');
    }

});


</script>
</head>
<body>
<div class="container">
<br><br><br><br>
  <div class='form-group'>
      {!!Form::label('codigoTipoActivo', 'Codigo', array('class' => 'col-sm-2 control-label')) !!}
      {!!Form::hidden('idTipoActivo', null, array('id' => 'idTipoActivo')) !!}
      {!!Form::hidden('documentoEliminar', null, array('id' => 'documentoEliminar')) !!}
      {!!Form::hidden('caracteristicaEliminar', null, array('id' => 'caracteristicaEliminar')) !!}
    <div class="col-sm-9">
      {!!Form::text('codigoTipoActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el codigo'])!!}
    </div>
      {!!Form::label('nombreTipoActivo', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-9">
      {!!Form::text('nombreTipoActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre'])!!}
    </div>
    <br><br><br><br><br>
      <div id="pestanas">
        <ul id=lista class="nav nav-tabs">
        <li  class="active" id="pestana3"><a data-toggle="tab" href='#cpestana3'>Caracteristicas</a>
        </li>
        <li id="pestana4"><a data-toggle="tab" href='#cpestana4'>Documentos/Licencias</a></li>
        </ul>
      </div>

<div class="tab-content" id="contenidopestanas">
    <div class="tab-pane fade in active" id="cpestana3">
      <br><br>
      <div class="form-group">
        <fieldset id='varioslistachequeo-form-fieldset'>
            <div class="form-group"  id='test'>
                <div class="col-sm-12">
                    <div class="row show-grid">
                        <div class="col-md-1" style="width: 40px;height: 35px;" >
                            <span class="glyphicon glyphicon-plus" onclick="caracteristicaTipoActivo.agregarCampos(valorCaracteristicaTipoActivo,'A')" ></span>
                        </div>
                        <div class="col-md-1" style="width: 200px;height: 35px;"><b>Nombre Caracteristica</b></div>
                        <div id="contenedor-caracteristicaTipoActivo">
                    </div>      
                </div>
            </div>
        </fieldset>
    </div>
</div>

<div  class="tab-pane fade" id="cpestana4">
  <br><br>
  <div class="form-group">
    <fieldset id='fieldset-documentos'>
        <div class="form-group"  id='test'>
            <div class="col-sm-12">
                <div class="row show-grid">
                    <div class="col-md-1" style="width: 40px;height: 35px;" >
                        <span class="glyphicon glyphicon-plus" onclick="documentoTipoActivo.agregarCampos(valorDocumentoTipoActivo,'A')" ></span>
                    </div>
                    <div class="col-md-1" style="width: 210px;height: 35px;"><b>Descripcion</b></div>
                    <div class="col-md-1" style="width: 100px;height: 35px;"><b>Tipo</b></div>
                    <div class="col-md-1" style="width: 120px;height: 35px;"><b>Vigencia</b></div>
                    <div class="col-md-1" style="width: 80px;height: 35px;"><b>Costo</b></div>
                    <div id="contenedor-documentoTipoActivo"></div>
                </div>
            </div>
        </div>
    </fieldset>
  </div>
</div>

    
    @if(isset($tipoactivo))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
    @else
    {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
    @endif
    @else
    {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
    @endif

    {!! Form::close() !!}          
</body>
</html>
@stop
