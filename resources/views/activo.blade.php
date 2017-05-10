@include('alerts/request')

@if(isset($activo))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($activo,['route'=>['activo.destroy',$activo->idActivo],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($activo,['route'=>['activo.update',$activo->idActivo],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'activo.store','method'=>'POST'])!!}
@endif

@extends('layouts.vista')
@section('titulo')<br><h4 id="titulo"><center>ACTIVO</center></h4>@stop
@section('content')
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Activo</title>

  {!!Html::style('/css/select2.min.css');!!}
  {!!Html::script('/js/select2.min.js');!!}
  {!!Html::script('/js/activo.js');!!}
  {!!Html::script('/js/general.js');!!}


  <script>




    $(document).ready(function() {
      $("#nombreTipoActivo").select2();
    });


    function abrirModalCampos()
    {
      $('#ModalCampos').modal('show');

    }

    function abrirModalCampos1()
    {
      $('#ModalCampos1').modal('show');

    }

/*function ensayo()
    {
    alert('hola');
var arrayResult = mysql_select_query ("SELECT * FROM activo");
for (i=0; i< arrayResult.length i++) {
    var fila = arrayResult[i];
    var columna = arrayResult[i][0];

    
}
}*/


/*$(document).ready(function() {
  $(".form-control").select2();
});
$(document).ready(function() {
  $("#nombresTipoActivo").select2();
});*/

  $(document).ready(function () 
  {
        // inicializamos el plugin
        $('#tags').select2(
        {
            // Activamos la opcion "Tags" del plugin
            tags: true,
            tokenSeparators: [','],
            ajax: 
            {
              dataType: 'json',
              url: '{{ url("tags") }}',
              delay: 250,
              data: function(params) 
              {
                return 
                {
                  term: params.term
                }
              },
              processResults: function (data, page)
              {
                return 
                {
                  results: data
                };
              },
            }
        });
  });


var popup 
function abrir(){ 
  popup = window.open("/mostrarPartes","popup","width=700,height=300,scrollbars=yes");
  popup.focus();
} 

    var tipoactivocaracteristica = '<?php echo (isset($activoCaracteristica) ? json_encode($activoCaracteristica) : "");?>';
    tipoactivocaracteristica = (tipoactivocaracteristica != '' ? JSON.parse(tipoactivocaracteristica) : '');

    var tipoactivodocumento = '<?php echo isset($activoDocumento)? json_encode($activoDocumento) : "";?>';
    tipoactivodocumento = (tipoactivodocumento != '' ? JSON.parse(tipoactivodocumento) : '');

    var tipoactivoparte = '<?php echo isset($activoParte)? json_encode($activoParte) : "";?>';
    tipoactivoparte = (tipoactivoparte != '' ? JSON.parse(tipoactivoparte) : '');

    var tipoactivocomponente = '<?php echo isset($activoComponente)? json_encode($activoComponente) : "";?>';
    tipoactivocomponente = (tipoactivocomponente != '' ? JSON.parse(tipoactivocomponente) : '');


// var tipoactivocaracteristica= '<?php //echo $tipoactivo; ?>'
// CONTENEDOR PESTAÑA 2


var valorParteactivo = [0,0,''];
$(document).ready(function()
{

  
  parteactivo=new Atributos('parteactivo','contenedor-parteactivo','parteactivo-');
  parteactivo.campoid = 'idActivoParte';
  parteactivo.campoEliminacion = 'parteEliminar';
  parteactivo.campos=['idActivoParte', 'Activo_idParte','nombreActivoParte'];
  parteactivo.etiqueta=['input','input','input'];
  parteactivo.tipo=['hidden','hidden',''];
  parteactivo.estilo=['','','width:510px; height:35px;'];
  parteactivo.clase=['','',''];
  parteactivo.sololectura=[false,false,true];
  parteactivo.completar=['off','off','off'];
  parteactivo.opciones = [[],[],[]];      
  parteactivo.funciones=['','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';

  for(var j=0; j < tipoactivoparte.length; j++)
  {
      parteactivo.agregarCampos(JSON.stringify(tipoactivoparte[j]),'L');
  }

});

// CONTENEDOR PESTAÑA 3
var valorComponenteactivo = [0,0,''];
$(document).ready(function()
{

  componenteactivo=new Atributos('componenteactivo','contenedor-componenteactivo','componenteactivo-');
  componenteactivo.campoid = 'idActivoComponente';
  componenteactivo.campoEliminacion = 'componenteEliminar';
  componenteactivo.campos=['idActivoComponente','Activo_idComponente','nombreActivoComponente',
  'cantidadActivoComponente'];
  componenteactivo.etiqueta=['input','input','input','input'];
  componenteactivo.tipo=['hidden','hidden','',''];
  componenteactivo.estilo=['','','width:510px; height:35px;','width:90px; height:35px;'];
  componenteactivo.clase=['','','',''];
  componenteactivo.sololectura=[false,false,true,false];
  componenteactivo.completar=['off','off','off','off'];
  componenteactivo.opciones = [[],[],[],[]];      
  componenteactivo.funciones=['','','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';

  for(var j=0; j < tipoactivocomponente.length; j++)
  {
      componenteactivo.agregarCampos(JSON.stringify(tipoactivocomponente[j]),'L');
  }

});


//CONTENEDOR PESTAÑA 4

var valorCaracteristicaactivo = [0,0,''];
$(document).ready(function()
{

  caracteristicaactivo=new Atributos('caracteristicaactivo','contenedor-caracteristicaactivo','caracteristicaactivo-');
  caracteristicaactivo.campoid = '';
  caracteristicaactivo.campoEliminacion = '';
  caracteristicaactivo.botonEliminacion = false;
  caracteristicaactivo.campos=['idActivoCaracteristica','idTipoActivoCaracteristica','nombreTipoActivoCaracteristica','descripcionActivoCaracteristica'];
  caracteristicaactivo.etiqueta=['input','input','input','input'];
  caracteristicaactivo.tipo=['hidden','hidden','',''];
  caracteristicaactivo.estilo=['','','width:250px; height:35px;','width:450px; height:35px;'];
  caracteristicaactivo.clase=['','','',''];
  caracteristicaactivo.sololectura=[false,false,true,false];
  caracteristicaactivo.completar=['off','off','off','off'];
  caracteristicaactivo.funciones=['','','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';

  for(var j=0; j < tipoactivocaracteristica.length; j++)
  {
    caracteristicaactivo.agregarCampos(JSON.stringify(tipoactivocaracteristica[j]),'L');
  }

});

// CONTENEDOR PESTAÑA 5
var valorDocumentoactivo = [0,0,''];
$(document).ready(function()
{

  documentoactivo=new Atributos('documentoactivo','contenedor-documentoactivo','documentoactivo-');
  documentoactivo.campoid = 'idActivoDocumento';
  documentoactivo.campoEliminacion = 'documentoEliminar';
  documentoactivo.campos=['idActivoDocumento','idTipoActivoDocumento', 'descripcionTipoActivoDocumento',
  'versionActivoDocumento', 'proveedorActivoDocumento',
  'serialActivoDocumento', 'tipoTipoActivoDocumento',
  'fechainicialActivoDocumento', 'costoTipoActivoDocumento'];
  documentoactivo.etiqueta=['input','input','input','input','input','input','input','input','input'];
  documentoactivo.tipo=['hidden','hidden','','','','','','date',''];
  documentoactivo.estilo=['','','width:210px; height:35px;','width:100px; height:35px;','width:210px;  height:35px;','width:100px; height:35px;','width:100px; height:35px;','width:120px; height:35px;','width:80px; height:35px;'];

  documentoactivo.clase=['','','','','','','','',''];
  documentoactivo.sololectura=[false,false,true,false,false,false,true,false,false];
  documentoactivo.completar=['off','off','off','off','off','off','off','off','off'];

  documentoactivo.opciones = [[],[],[],[],[],[],[['Oem','Glp','Bsd','Documento'],['Oem','Glp','Bsd','Documento']],[],[]];      
  documentoactivo.funciones=['','','','','','','','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';


  for(var j=0; j < tipoactivodocumento.length; j++)
  {
      documentoactivo.agregarCampos(JSON.stringify(tipoactivodocumento[j]),'L');
  }

});

  var adOption = new Object();
  adOption.checkList = function(list, optval) 
  {
    var re = 0;           // variable that will be returned
    var opts = document.getElementById(list).getElementsByTagName('option');
    for(var i=0; i<opts.length; i++) 
      {
        if(opts[i].value == document.getElementById(optval).value) 
        {
          re = 1;
          break;
        }
      }

    return re;         
  };

   adOption.addOption = function(list, optval) {
   var opt_val = document.getElementById(optval).value;
    if(opt_val.length > 0) 
    {
      if(this.checkList(list, optval) == 0) 
      {
        var myoption = document.createElement('option');
        myoption.value = opt_val;
        myoption.innerHTML = opt_val;
        document.getElementById(list).insertBefore(myoption, document.getElementById(list).lastChild);
        document.getElementById(optval).value = '';          
      }
      else alert('The value "'+opt_val+'" already added');
    }
      else alert('Add a value for option');
  };

    adOption.delOption = function(list, optval) {
    var opt_val = document.getElementById(optval).value;
      if(this.checkList(list, optval) == 1) 
      {
         var opts = document.getElementById(list).getElementsByTagName('option');
        for(var i=0; i<opts.length; i++) 
        {
          if(opts[i].value == opt_val) 
          {
            document.getElementById(list).removeChild(opts[i]);
            break;
          }
        }
      }
      else alert('The value "'+opt_val+'" not exist');
  }

  adOption.selOpt = function(opt, txtbox) { document.getElementById(txtbox).value = opt; }


</script>


</head>
<body onload="ensayo();" >

 <div class="container">
 <br>  
 <br>
 <br>

 <br>
<div class='form-group'>
  <div class="col-sm-6" position="left">
    {!!Form::label('codigoActivo', 'Codigo', array('class' => 'col-sm-4 control-label')) !!}
    <div class="col-sm-6">
      {!!Form::text('codigoActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el codigo'])!!}
      {!!Form::hidden('idActivo', null, array('id' => 'idActivo')) !!}
      {!!Form::hidden('idTipoActivo', null, array('id' => 'idTipoActivo')) !!}
      {!!Form::hidden('Activo_idActivoParte', null, array('id' => 'Activo_idActivoParte')) !!}
      {!!Form::hidden('idTipoActivoCaracteristica', null, array('id' => 'idTipoActivoCaracteristica')) !!}
      {!!Form::hidden('idTipoActivoDocumento', null, array('id' => 'idTipoActivoDocumento')) !!}
      {!!Form::hidden('documentoEliminar', null, array('id' => 'documentoEliminar')) !!}
      {!!Form::hidden('caracteristicaEliminar', null, array('id' => 'caracteristicaEliminar')) !!}
      {!!Form::hidden('parteEliminar', null, array('id' => 'parteEliminar')) !!}
      {!!Form::hidden('componenteEliminar', null, array('id' => 'componenteEliminar')) !!}
    </div>
      {!!Form::label('nombreActivo', 'Nombre', array('class' => 'col-sm-4 control-label')) !!}
    <div class="col-sm-6">
      {!!Form::text('nombreActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre'])!!}
    </div>
      {!!Form::label('TipoActivo_idTipoActivo', 'Tipo de Activo', array('class' => 'col-sm-4 control-label')) !!} 
    <div class="col-sm-6" >
     {!!Form::select('TipoActivo_idTipoActivo', @$tipoactivo, @$activo->TipoActivo_idTipoActivo,['class' => 'form-control', 'id'=>'TipoActivo_idTipoActivo','onchange'=>'llamarCaracteristicas(this.value);llamarDocumentos(this.value);','onready'=>'llamarCaracteristicas(this.value);llamarDocumentos(this.value);'])!!}
    </div>
 </div>
     <input type="hidden" id="token" value="{{csrf_token()}}"/>
  <div class="col-sm-6" position="right">
     {!!Form::label('codigobarraActivo', 'Codigo de Barras', array('class' => 'col-sm-4 control-label')) !!}
     <div class="col-sm-6">
       {!!Form::text('codigobarraActivo',@$activo->codigobarraActivo,['class'=>'form-control','placeholder'=>'Ingresa el codigo de Barras'])!!}
     </div>
       {!!Form::label('estadoActivo', 'Estado', array('class' => 'col-sm-4 control-label')) !!}
     <div class="col-sm-6">
       {!!Form::select('estadoActivo',['En uso'=>'En uso', 'Disponible'=>'Disponible'],@$activo->estadoActivo,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}
     </div>
       {!!Form::label('clasificacionActivo', 'Clasificacion', array('class' => 'col-sm-4 control-label')) !!}
     <div class="col-sm-6" >
       {!!Form::select('clasificacionActivo',['Activo'=>'Activo','Parte'=>'Parte','Componente'=>'Componente','Consumible'=>'Consumible'],null,['class' => 'form-control'])!!}
     </div>
  </div>
</div>


<br>
<br>
<br>
<br>
<br>
<br>
<br>

<div id="pestanas">
  <ul id=lista class="nav nav-tabs">
    <li class="active" id="pestana1"><a data-toggle="tab" href='#cpestana1'>Datos Generales</a></li>
    <li id="pestana2"><a data-toggle="tab" href='#cpestana2'>Partes</a></li>
    <li id="pestana3"><a data-toggle="tab" href='#cpestana3'>Componentes/Adiciones</a></li>
    <li   id="pestana4"><a data-toggle="tab" href='#cpestana4'>Caracteristicas</a></li>
    <li id="pestana5"><a data-toggle="tab" href='#cpestana5'>Documentos/Licencias</a></li>
  </ul>
</div>


<div class="tab-content" id="contenidopestanas">
  <div class="tab-pane fade in active" id="cpestana1">
   <br><br>
    <div class="form-group">
      <div class="col-sm-6">
        {!!Form::label('marcaActivo', 'Marca', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-9">
          {!!Form::text('marcaActivo',null,['class'=>'form-control','placeholder'=>'Ingresa la Marca del Activo'])!!}
        </div>
          {!!Form::label('serieActivo', 'Serie', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-9">
          {!!Form::text('serieActivo',null,['class'=>'form-control','placeholder'=>'Ingresa la serie'])!!}
        </div>
          {!!Form::label('pesoActivo', 'Peso', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-9">
          {!!Form::text('pesoActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el peso'])!!}
        </div>
          {!!Form::label('altoActivo', 'Alto', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-9">
          {!!Form::text('altoActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el alto'])!!}
        </div>
          {!!Form::label('anchoActivo', 'Ancho', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-9">
          {!!Form::text('anchoActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el ancho'])!!}
        </div>
      </div>
        <div class="col-sm-6">
            {!!Form::label('largoActivo', 'Largo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-9">
            {!!Form::text('largoActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el largo'])!!}
          </div>
            {!!Form::label('modeloActivo', 'Modelo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-9">
            {!!Form::text('modeloActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el modelo'])!!}
          </div>
          {!!Form::label('volumenActivo', 'Volumen', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-9">
           {!!Form::text('volumenActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el volumen'])!!}
          </div>
        </div>

    <br><br><br><br><br><br><br><br><br><br>


  </div>
</div>
<div class="tab-pane fade" id="cpestana2" onready="alert('hola');">
  <br><br>
    <div class="form-group">
    <fieldset id='varioslistachequeo-form-fieldset'>
      <div class="form-group"  id='test'>
        <div class="col-sm-12">
          <div class="row show-grid">
            <div class="col-md-1" style="width: 40px;height: 35px;" >
              <span class="glyphicon glyphicon-plus" onclick="abrirModalCampos();" ></span> 
            </div>
            <div class="col-md-1" style="width: 510px;height: 35px;"><b>Descripcion</b></div>
            <div id="contenedor-parteactivo"></div>
          </div>      
        </div>
      </div>
    </fieldset>
  </div>
</div>



<div class="tab-pane fade " id="cpestana3">
  <br><br>
  <div class="form-group">
    <fieldset id='varioslistachequeo-form-fieldset'>
      <div class="form-group"  id='test'>
        <div class="col-sm-12">
          <div class="row show-grid">
            <div class="col-md-1" style="width: 40px;height: 35px;" >
             <span class="glyphicon glyphicon-plus" onclick="abrirModalCampos1();" ></span> 
           </div>
           <div class="col-md-1" style="width: 510px;height: 35px;"><b>Descripcion</b></div>
           <div class="col-md-1" style="width: 90px;height: 35px;" center-text><b>Cantidad</b></div>
           <div id="contenedor-componenteactivo"></div>
        </div>      
       </div>
     </div>
   </fieldset>
 </div>

        <!-- <div class="form-group">
        <div class="col-sm-10">
        <hr><hr>

          {!!Form::label('tags', 'Tags', array('class' => 'col-sm-4 control-label')) !!}
             {!!Form::select('tags[]',[],null,['class' => 'form-control','data-width'=>"75%",'id'=>'tags'])!!}
             
            {!!Form::label('TipoActivo_idTipoActivo', 'Natalia', array('class' => 'col-sm-4 control-label')) !!}
             {!!Form::select('nombreTipoActivos', @$tipoactivo, @$activo->TipoActivo_idTipoActivo,['class' => 'form-control', 'id'=>"nombresTipoActivo", 'onchange'=>"adOption.selOpt(this.value, 'optval')" ])!!}
            
             Add an option: <input type="text" name="optval" id="optval" /><br /><br/>
  <input type="button" id="addopt" name="addopt" value="Add Option" onclick="adOption.addOption('nombresTipoActivo', 'optval');" /> &nbsp;
  <input type="button" id="del_opt" name="del_opt" value="Delete Option" onclick="adOption.delOption('nombresTipoActivo', 'optval');" />
            <!-- <label for="tags" class="control-label">Tags</label>
            <select name="tags[]" class="form-control" multiple="multiple" id="tags"></select> -->
       <!--  </div>
     </div> --> 

</div>

  <div  class="tab-pane fade" id="cpestana4">
    <br><br>
    <div class="form-group">
      <fieldset id='varioslistachequeo-form-fieldset'>
        <div class="form-group"  id='test'>
          <div class="col-sm-12">
            <div class="row show-grid">
            <div class="col-md-1" style="width: 250px;height: 35px;"><b>Caracteristica</b></div>
            <div class="col-md-1" style="width: 450px;height: 35px;"><b>Descripcion</b></div>
            <div id="contenedor-caracteristicaactivo"></div>
            </div>      
          </div>
        </div>
      </fieldset>
    </div>
  </div>

                          

<div class="tab-pane fade" id='cpestana5'>
  <br><br>
  <div class="form-group">
    <fieldset id='fieldset-documentos'>
      <div class="form-group"  id='test'>
        <div class="col-sm-12">
          <div class="row show-grid">
             <div class="col-md-1" style="width: 40px;height: 35px;" ></div>
             <div class="col-md-1" style="width: 210px;height: 35px;"><b>Descripcion</b></div>
             <div class="col-md-1" style="width: 100px;height: 35px;"><b>Version</b></div>
             <div class="col-md-1" style="width: 210px;height: 35px;"><b>Proveedor</b></div>
             <div class="col-md-1" style="width: 100px;height: 35px;"><b>Serial o N°</b></div>
             <div class="col-md-1" style="width: 100px;height: 35px;"><b>Tipo</b></div>
             <div class="col-md-1" style="width: 120px;height: 35px;"><b>Fecha Inicial</b></div>
             <div class="col-md-1" style="width: 80px;height: 35px;"><b>Costo</b></div>
             <div id="contenedor-documentoactivo"></div>
          </div>
        </div>
     </div>
    </fieldset>
  </div>
</div>
</div>

@if(isset($activo))
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

<div id="ModalCampos" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/mostrarpartesgridselect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>

<div id="ModalCampos1" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/mostrarcomponentesgridselect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>
