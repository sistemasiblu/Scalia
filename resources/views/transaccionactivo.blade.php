<?php
//print_r( @$transaccionEncabezado);
//print_r( @$transaccionDetalle);
//print_r( @$transaccionCompania);
//print_r( @$transaccionConcepto);
//print_r( @$transaccionRol);



?>
@include('alerts/request')

@if(isset($transaccionactivo))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($transaccionactivo,['route'=>['transaccionactivo.destroy',$transaccionactivo->idTransaccionActivo],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($transaccionactivo,['route'=>['transaccionactivo.update',$transaccionactivo->idTransaccionActivo],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'transaccionactivo.store','method'=>'POST'])!!}
@endif

@extends('layouts.vista')
@section('titulo')<br><h4 id="titulo"><center>TRANSACCION ACTIVO</center></h4>@stop
@section('content')
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Activo</title>

  {!!Html::style('/css/select2.min.css');!!}
  {!!Html::script('/js/select2.min.js');!!}
  {!!Html::script('/js/activo.js');!!}

  <script>

  

    $(document).ready(function() {
      $("#nombreTipoActivo").select2();
    });



    function abrirModalCamposEncabezado()
       {
      $('#ModalCamposEncabezado').modal('show');

    }

    function abrirModalCamposDetalle()
    {
      $('#ModalCamposDetalle').modal('show');

    }

    function abrirModalCamposConcepto()
    {
      $('#ModalCamposConcepto').modal('show');

    }

    
    function abrirModalCamposRol()
    {
      $('#myModalRol').modal('show');

    }


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

    var transaccionEncabezado = '<?php echo (isset($transaccionEncabezado) ? json_encode($transaccionEncabezado) : "");?>';
    transaccionEncabezado = (transaccionEncabezado != '' ? JSON.parse(transaccionEncabezado) : '');

    
    var transaccionconcepto = '<?php echo isset($transaccionConcepto)? json_encode($transaccionConcepto) : "";?>';
    transaccionconcepto = (transaccionconcepto != '' ? JSON.parse(transaccionconcepto) : '');

       var transaccionrol = '<?php echo isset($transaccionRol)? json_encode($transaccionRol) : "";?>';
    transaccionrol = (transaccionrol != '' ? JSON.parse(transaccionrol) : '');



// var tipoactivocaracteristica= '<?php //echo $tipoactivo; ?>'
// CONTENEDOR PESTAÑA 2


var valorEncabezado = [0,0,''];
$(document).ready(function()
{

  encabezado=new Atributos('encabezado','contenedor-encabezado','encabezado-');
  encabezado.campoid = 'idTransaccionActivoCampoE';
  encabezado.campoEliminacion = 'encabezadoEliminar';
  encabezado.campos=['idTransaccionActivoCampoE', 'CampoTransaccion_idCampoTransaccionE', 'descripcionCampoTransaccionE','gridTransaccionActivoCampoE','vistaTransaccionActivoCampoE','obligatorioTransaccionActivoCampoE'];
  encabezado.etiqueta=['input','input','input','checkbox','checkbox','checkbox'];
  encabezado.tipo=['hidden','hidden','','checkbox','checkbox','checkbox'];
  encabezado.estilo=['','','width:200px; height:35px;','width:100px; height:31px;display: inline-block;','width:100px; height:31px;display: inline-block;','width:100px; height:31px;display: inline-block;'];
  encabezado.clase=['','','','','',''];
  encabezado.sololectura=[false,false,true,true,true];
  encabezado.completar=['off','off','off','off','off'];
  encabezado.opciones = [[],[],[],[],[]];      
  encabezado.funciones=['','','','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';

  for(var j=0; j < transaccionEncabezado.length; j++)
  {
      encabezado.agregarCampos(JSON.stringify(transaccionEncabezado[j]),'L');
  }

});


//CONTENEDOR PESTAÑA 4

var valorCaracteristicaactivo = [0,0,''];
$(document).ready(function()
{

  concepto=new Atributos('concepto','contenedor-concepto','concepto-');
  concepto.campoid = 'idTransaccionConcepto';
  concepto.campoEliminacion = 'conceptoEliminar';
  concepto.botonEliminacion = true;
  concepto.campos=['idTransaccionConcepto','idConceptoActivo','codigoConceptoActivo','nombreConceptoActivo'];
  concepto.etiqueta=['input','input','input','input'];
  concepto.tipo=['hidden','hidden','hidden',''];
  concepto.estilo=['','','','width:200px; height:35px;'];
  concepto.clase=['','','',''];
  concepto.sololectura=[false,false,false,true];
  concepto.completar=['off','off','off','off'];
  concepto.funciones=['','','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';

  for(var j=0; j < transaccionconcepto.length; j++)
  {
    concepto.agregarCampos(JSON.stringify(transaccionconcepto[j]),'L');
  }

});


// CONTENEDOR PESTAÑA 6
var valorpermisos = [0,0,''];
$(document).ready(function()
{

  permisos=new Atributos('permisos','contenedor-permisos','permisos-');
  permisos.campoid = 'idTransaccionRol';
  permisos.campoEliminacion = 'permisosEliminar';
  permisos.campos=['idTransaccionRol','nombreRol', 'Rol_idRol','adicionarTransaccionRol','modificarTransaccionRol','consultarTransaccionRol','anularTransaccionRol','autorizarTransaccionRol',
  ];
  permisos.etiqueta=['input','input','input','checkbox','checkbox','checkbox','checkbox','checkbox'];
  permisos.tipo=['hidden','','hidden','checkbox','checkbox','checkbox','checkbox','checkbox'];
  permisos.estilo=['','width: 200px;height: 35px','','width:45px;height:30px;display:inline-block;','width:45px;height:30px;display:inline-block;','width:45px;height:30px;display:inline-block;','width:45px;height:30px;display:inline-block;','width:45px;height:30px;display:inline-block;'];

  permisos.clase=['','','','','','','',''];
  permisos.sololectura=[false,false,false,false,false,false,false,false,];
  permisos.completar=['off','off','off','off','off','off','off','off'];

  permisos.opciones = [[],[],[],[],[],[],[],[]];      
  permisos.funciones=['','','','','','','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';


  for(var j=0; j < transaccionrol.length; j++)
  {
      permisos.agregarCampos(JSON.stringify(transaccionrol[j]),'L');
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
<body >

 <div class="container">
  <br>  
 <br>
 <br>

 <br>
<div class='form-group'>
 {!!Form::label('codigoTransaccionActivo', 'Codigo', array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-6">
      {!!Form::text('codigoTransaccionActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el codigo'])!!}<br>
      {!!Form::hidden('idDetalle', null, array('id' => 'idDetalle')) !!}
      {!!Form::hidden('TransaccionActivo_idTransaccionActivo', null, array('id' => 'TransaccionActivo_idTransaccionActivo')) !!}
      {!!Form::hidden('Rol_idRol', null, array('id' => 'Rol_idRol')) !!}
      {!!Form::hidden('Compania_idCompania', null, array('id' => 'Compania_idCompania')) !!}
      {!!Form::hidden('idTransaccionCompania', null, array('id' => 'idTransaccionCompania')) !!}
      {!!Form::hidden('encabezadoEliminar', null, array('id' => 'encabezadoEliminar')) !!}
      {!!Form::hidden('detalleEliminar', null, array('id' => 'detalleEliminar')) !!}
      {!!Form::hidden('rolEliminar', null, array('id' => 'rolEliminar')) !!}
      {!!Form::hidden('conceptoEliminar', null, array('id' => 'conceptoEliminar')) !!}
    </div>
     <br><br><br> {!!Form::label('nombreTransaccionActivo', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-6">
      {!!Form::text('nombreTransaccionActivo',null,['required'=>'required','class'=>'form-control','placeholder'=>'Ingresa el nombre'])!!}<br>
    </div>
      <br><br><br>{!!Form::label('formatoTransaccionActivo', 'Formato', array('class' => 'col-sm-2 control-label')) !!} 
    <div class="col-sm-6" >
      {!!Form::text('formatoTransaccionActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el formato'])!!}<br>
    </div><br><br>
<br>
<br>


  <div class="col-sm-6" position="left">
    {!!Form::label('tipoNumeracionTransaccionActivo', 'Tipo Numeracion', array('class' => 'col-sm-4 control-label')) !!}
    <div class="col-sm-6">
     {!!Form::select('tipoNumeracionTransaccionActivo',['Automatica'=>'Automatica', 'Manual'=>'Manual'],@$transaccionactivo->tipoNumeracionTransaccionActivo,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}
      </div>
      {!!Form::label('desdeTransaccionActivo', 'Desde', array('class' => 'col-sm-4 control-label')) !!}
    <div class="col-sm-6">
      {!!Form::text('desdeTransaccionActivo',null,['class'=>'form-control','placeholder'=>'Ingresa el inicio del consecutivo'])!!}
    </div>
      {!!Form::label('TransaccionGrupo_idTransaccionGrupo', 'Grupo Transaccion', array('class' => 'col-sm-4 control-label')) !!} 
    <div class="col-sm-6" >
     {!!Form::select('TransaccionGrupo_idTransaccionGrupo',@$transacciongrupo, @$transaccionactivo->TransaccionGrupo_idTransaccionGrupo,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}
    </div>
     {!!Form::label('estadoTransaccionActivo', 'Estado Por Defecto', array('class' => 'col-sm-4 control-label')) !!} 
    <div class="col-sm-6" >
    {!!Form::select('estadoTransaccionActivo',['Aprobado Total'=>'Aprobado Total','Proceso'=>'Proceso' ],@$transaccionactivo->estadoActivo,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}
    </div>
 </div>
     <input type="hidden" id="token" value="{{csrf_token()}}"/>
  <div class="col-sm-6" position="right">
     {!!Form::label('longitudTransaccionActivo', 'Longitud', array('class' => 'col-sm-4 control-label')) !!}
     <div class="col-sm-6">
       {!!Form::text('longitudTransaccionActivo',@$transaccionactivo->longitudTransaccionActivo,['class'=>'form-control','placeholder'=>'Ingresa la longitud del codigo'])!!}
     </div>
       {!!Form::label('hastaTransaccionActivo', 'Hasta', array('class' => 'col-sm-4 control-label')) !!}
     <div class="col-sm-6">
        {!!Form::text('hastaTransaccionActivo',@$transaccionactivo->hastaTransaccionActivo,['class'=>'form-control','placeholder'=>'Ingresa el limite del consecutivo'])!!}
     </div>
       {!!Form::label('accionTransaccionActivo', 'Accion', array('class' => 'col-sm-4 control-label')) !!}
     <div class="col-sm-6" >
       {!!Form::select('accionTransaccionActivo',['Entrada'=>'Entrada','Salida'=>'Salida','Traslado'=>'Traslado','No afecta'=>'No afecta'],null,['class' => 'form-control'])!!}
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
<br>
<br>
<br>


<div id="pestanas">
  <ul id=lista class="nav nav-tabs">
    <li class="active" id="pestana1"><a data-toggle="tab" href='#cpestana1'>Datos</a></li>
    <li id="pestana2"><a data-toggle="tab" href='#cpestana2'>Conceptos</a></li>
    <li id="pestana3"><a data-toggle="tab" href='#cpestana3'>Permisos</a></li>
  </ul>
</div>


<div class="tab-content" id="contenidopestanas">
  <div class="tab-pane fade in active" id="cpestana1">
    <div class="container"><br>
      
    </div>

    <div class="tab-content" id="contenidopestanasDatos">
     
       <br>
       <script type="text/javascript">
        
       </script>
          <div class="form-group">
          <fieldset id='varioslistachequeo-form-fieldset'>
            <div class="form-group"  id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;height: 35px;" >
                    <span class="glyphicon glyphicon-plus" onclick="abrirModalCamposEncabezado();"></span> 
                  </div>
                  <div class="col-md-1" style="width: 200px;height: 35px;"><b>Nombre del Campo</b></div>
                  <div class="col-md-1" style="width: 100px;height: 35px; display: inline-block;"><b>Grid</b></div>
                  <div class="col-md-1" style="width: 100px;height: 35px; display: inline-block;"><b>Formulario</b></div>
                  <div class="col-md-1" style="width: 100px;height: 35px; display: inline-block;"><b>Obligatorio</b></div>
                  <div id="contenedor-encabezado"></div>
                </div>      
              </div>
            </div>
          </fieldset>
        </div>
     

     
    </div><!--Fin Div Tab-content ContenidopestanasDatos -->
  </div><!--Fin Div cpestana1 -->

  <div class="tab-pane fade" id="cpestana2" >
    <br><br>
        <div class="form-group">
          <fieldset id='varioslistachequeo-form-fieldset'>
            <div class="form-group"  id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;height: 35px;" >
                    <span class="glyphicon glyphicon-plus" onclick="abrirModalCamposConcepto();"></span> 
                  </div>
                  <div class="col-md-1" style="width: 200px;height: 35px;"><b>Nombre del Concepto</b></div>
                  <div id="contenedor-concepto"></div>
                </div>      
              </div>
            </div>
          </fieldset>
        </div>
  </div><!--Fin Div cpestana2 -->

  <div class="tab-pane fade " id="cpestana3">
    <div class="container"><br>
      
            <div class="form-group"  id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;height: 40px;" >
                    <span class="glyphicon glyphicon-plus" onclick="abrirModalCamposRol();"></span> 
                  </div>
                  <div class="col-md-1" style="width: 200px;height: 40px;"><b>Rol</b></div>
                  <div class="col-md-1" style="width: 45px;height: 40px;"><b><span class="glyphicon glyphicon-plus"></span> </b></div>
                  <div class="col-md-1" style="width: 45px;height: 40px;"><b><span class="glyphicon glyphicon-pencil"></span></b></div>
                  <div class="col-md-1" style="width: 45px;height: 40px;"><b><span class="glyphicon glyphicon-search"></span></b></div>
                  <div class="col-md-1" style="width: 45px;height: 40px;"><b><span class="glyphicon glyphicon-trash"></span></b></div>
                  <div class="col-md-1" style="width: 45px;height: 40px;"><b><span class="glyphicon glyphicon-ok"></span></b></div>
                  <div id="contenedor-permisos"></div>
                </div>      
              </div>
            </div>
         
    </div>

    
  </div><!--Fin Div cpestana3-->
</div><!--Fin Div contenidopestanas-->

@if(isset($transaccionactivo))
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
<div id="ModalCamposEncabezado" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/campostransaccionencabezadogridselect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>

<div id="ModalCamposDetalle" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/campostransacciondetallegridselect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>

<div id="ModalCamposConcepto" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/campostransaccionconceptogridselect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>



<div id="myModalRol" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/RolGridSelect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>
