@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Traslado de Documentos</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/trasladodocumento.js')!!}

<script>
    
    var TrasladoDoc = '<?php echo (isset($trasladodocumento) ? json_encode($trasladodocumento->TrasladoDocumentoDetalle) : "");?>';
    TrasladoDoc = (TrasladoDoc != '' ? JSON.parse(TrasladoDoc) : '');
    var valortrasladodocumento = [0,'',0,'','',0,'','',0,'','',0,'','',0,'','','',0,0];

    modalDestinoDocumento = ['onclick','abrirModalInterfaceDestino(\'Documento\',\'idDocumento\',\'nombreDocumento\',this.id)'];

    modalDestinoConcepto = ['onclick','abrirModalInterfaceDestino(\'DocumentoConcepto\',\'idDocumentoConcepto\',\'nombreDocumentoConcepto\',this.id)'];

    modalDestinoTercero = ['onclick','abrirModalInterfaceDestino(\'Tercero\',\'idTercero\',\'nombre1Tercero\',this.id)'];

    $(document).ready(function(){

      traslado = new Atributos('traslado','contenedor_traslado','traslado_');

      traslado.altura = '35px';
      traslado.campoid = 'idTrasladoDocumentoDetalle';
      traslado.campoEliminacion = 'eliminarTrasladoDocumentoDetalle';

      traslado.campos   = [
      'Documento_idOrigen', 
      'documentoOrigenTrasladoDocumentoDetalle', 
      'DocumentoConcepto_idOrigen',
      'documentoConceptoOrigenTrasladoDocumentoDetalle',
      'Movimiento_idOrigen',
      'numeroOrigenTrasladoDocumentoDetalle',
      'Tercero_idOrigen',
      'terceroOrigenTrasladoDocumentoDetalle',
      'fechaOrigenTrasladoDocumentoDetalle',
      'Documento_idDestino',
      'documentoDestinoTrasladoDocumentoDetalle',
      'botonDocumento',
      'DocumentoConcepto_idDestino',
      'documentoConceptoDestinoTrasladoDocumentoDetalle',
      'botonConceptoDestino',
      'Tercero_idDestino',
      'terceroDestinoTrasladoDocumentoDetalle',
      'botonTerceroDestino',
      'observacionTrasladoDocumentoDetalle',
      'TrasladoDocumento_idTrasladoDocumento',
      'idTrasladoDocumentoDetalle'];

      traslado.etiqueta = ['input', 'input', 'input','input', 'input', 'input', 'input','input', 'input', 'input','input','button', 'input', 'input','button','input', 'input', 'button','input','input', 'input'];

      traslado.tipo     = ['hidden', 'text', 'hidden', 'text', 'hidden','text', 'hidden', 'text', 'date', 'hidden', 'text','button', 'hidden', 'text', 'button','hidden', 'text', 'button', 'text', 'hidden', 'hidden'];

      traslado.estilo   = ['', 'width: 250px;height:35px;', '', 'width: 250px;height:35px;', '', 'width: 100px;height:35px;', '', 'width: 250px;height:35px;', 'width: 100px;height:35px;', '','width: 250px;height:35px;','width: 40px;height:35px;','','width: 250px;height:35px;','width: 40px;height:35px;','','width: 250px;height:35px;','width: 40px;height:35px;','width: 298px;height:35px;','',''];

      traslado.clase    = ['','','','','','','','','','','','fa fa-external-link btn btn-primary','','','fa fa-external-link btn btn-primary','','','fa fa-external-link btn btn-primary','','',''];
      
      traslado.sololectura = [true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,false,true,true];

      traslado.funciones = ['','','','','','','','','','','',modalDestinoDocumento,'','',modalDestinoConcepto,'','',modalDestinoTercero,'','',''];

      traslado.completar = ['off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off'];

      for(var j=0, k = TrasladoDoc.length; j < k; j++)
      {
        traslado.agregarCampos(JSON.stringify(TrasladoDoc[j]),'L');
        console.log(JSON.stringify(TrasladoDoc[j]))
      }

    });

  </script>


	 @if(isset($trasladodocumento))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($trasladodocumento,['route'=>['trasladodocumento.destroy',$trasladodocumento->idTrasladoDocumento],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($trasladodocumento,['route'=>['trasladodocumento.update',$trasladodocumento->idTrasladoDocumento],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'trasladodocumento.store','method'=>'POST'])!!}
  @endif

<?php
  $nombreUsuario = \Session::get('nombreUsuario');
?>


<div id='form-section' >

  <fieldset id="trasladodocumento-form-fieldset"> 
      <input type="hidden" id="token" value="{{csrf_token()}}"/>
        <div class="form-group col-md-6" id='test'>
          {!!Form::label('numeroTrasladoDocumento', 'No. Interface', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::text('numeroTrasladoDocumento',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off'])!!}
              {!!Form::hidden('idTrasladoDocumento', null, array('id' => 'idTrasladoDocumento')) !!}
              {!!Form::hidden('eliminarTrasladoDocumentoDetalle', null, array('id' => 'eliminarTrasladoDocumentoDetalle')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaElaboracionTrasladoDocumento', 'Fecha de elaboración', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaElaboracionTrasladoDocumento',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('descripcionTrasladoDocumento', 'Descripción', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
              {!!Form::text('descripcionTrasladoDocumento',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('estadoTrasladoDocumento', 'Estado', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!! Form::select('estadoTrasladoDocumento', ['EnProceso' =>'En proceso','Finalizado' => 'Finalizado'],null,['class' => 'form-control', 'onchange'=>'fechaInterface(this.value)']) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('Users_id', 'Usuario', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::text('Users_id',$nombreUsuario,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaTrasladoDocumento', 'Fecha interface', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaTrasladoDocumento',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off', 'readonly'])!!}
            </div>
          </div>
        </div>

        <br><br><br><br><br>

        <div class="form-group">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">Interface</div>
              <div class="panel-body">
                <div class="panel-group" id="accordion">
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#interface">Interface</a>
                      </h4>
                    </div>
                    <div id="interface" class="panel-collapse collapse in">
                      <div class="panel-body" style="width:1280px;">

                        <div class="form-group col-md-6" id='test'>
                          {!!Form::label('SistemaInformacion_idOrigen', 'BD Origen', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-database"></i>
                              </span>
                              {!!Form::select('SistemaInformacion_idOrigen',$sistemainformacion, (isset($trasladodocumento) ? $trasladodocumento->SistemaInformacion_idSistemaInformacion : 0),['class'=>'select form-control', 'placeholder'=>'Selecciona el origen'])!!}
                            </div>
                          </div>
                        </div>

                        <div class="form-group col-md-6" id='test'>
                          {!!Form::label('SistemaInformacion_idDestino', 'BD Destino', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-database"></i>
                              </span>
                              {!!Form::select('SistemaInformacion_idDestino',$sistemainformacion, (isset($trasladodocumento) ? $trasladodocumento->SistemaInformacion_idSistemaInformacion : 0),['class'=>'select form-control', 'placeholder'=>'Selecciona el destino'])!!}
                            </div>
                          </div>
                        </div>

                        <div class="form-group col-md-6" id='test'>
                          {!!Form::label('documentoOrigen', 'Documento', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-file"></i>
                              </span>
                              {!! Form::select('documentoOrigen', $documento ,null,['class' => 'select form-control']) !!}
                            </div>
                          </div>
                        </div>

                        <div class="form-group col-md-6" id='test'>
                          {!!Form::label('documentoDestino', 'Documento', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-file"></i>
                              </span>
                              {!! Form::select('documentoDestino', $documento ,null,['class' => 'select form-control', 'onchange' => 'llenarDestinoMasivo("Documento", this.value)']) !!}
                            </div>
                          </div>
                        </div>

                        <div class="form-group col-md-6" id='test'>
                          {!!Form::label('conceptoOrigen', 'Concepto', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-file-o"></i>
                              </span>
                              {!! Form::select('conceptoOrigen', $documentoconcepto ,null,['class' => 'select form-control']) !!}
                            </div>
                          </div>
                        </div>

                        <div class="form-group col-md-6" id='test'>
                          {!!Form::label('conceptoDestino', 'Concepto', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-file-o"></i>
                              </span>
                              {!! Form::select('conceptoDestino', $documentoconcepto ,null,['class' => 'select form-control', 'onchange' => 'llenarDestinoMasivo("Concepto", this.value)']) !!}
                            </div>
                          </div>
                        </div>

                        <div class="form-group col-md-6" id='test'>
                        {!!Form::label('terceroOrigen', 'Tercero', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                              </span>
                              {!! Form::select('terceroOrigen', $tercero ,null,['class' => 'select form-control'])!!}
                            </div>
                          </div>
                        </div>

                        <div class="form-group col-md-6" id='test'>
                          {!!Form::label('terceroDestino', 'Tercero', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-user"></i>
                              </span>
                              {!! Form::select('terceroDestino', $tercero ,null,['class' => 'select form-control', 'onchange' => 'llenarDestinoMasivo("Tercero", this.value)'])!!}
                            </div>
                          </div>
                        </div>
                        
                        <div class="form-group" id='test'>
                          <div class="col-sm-12">
                            <div class="row show-grid">
                              <div style="overflow:auto; height:350px;">
                                <div style="width: 2160px; display: inline-block;">
                                <div class="col-md-1" style="width: 40px;"><center><b>&nbsp;</b></center></div>
                                <div class="col-md-1" style="width: 950px;"><center><b>Origen</b></center></div>
                                <div class="col-md-1" style="width: 870px;"><center><b>Destino</b></center></div>
                                <div class="col-md-1" style="width: 300px;"><center><b>&nbsp;</b></center></div>
                                  <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="abrirModalDocumentos($('#SistemaInformacion_idOrigen').val(),$('#documentoOrigen').val(), $('#conceptoOrigen').val(),$('#terceroOrigen').val());">
                                    <span class="glyphicon glyphicon-file"></span>
                                  </div>
                                  <div class="col-md-1" style="width: 250px;">Documento</div>
                                  <div class="col-md-1" style="width: 250px;">Concepto</div>
                                  <div class="col-md-1" style="width: 100px;">Numero</div>
                                  <div class="col-md-1" style="width: 250px;">Tercero</div>
                                  <div class="col-md-1" style="width: 100px;">Fecha</div>
                                  <div class="col-md-1" style="width: 290px;">Documento</div>
                                  <div class="col-md-1" style="width: 290px;">Concepto</div>
                                  <div class="col-md-1" style="width: 290px;">Tercero</div>
                                  <div class="col-md-1" style="width: 300px;">Observación</div>
                                  <div id="contenedor_traslado"> 
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
          </div>
        </div>

    </fieldset>

	@if(isset($trasladodocumento))
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
</div>
@stop

<!-- MODAL PARA LOS DATOS DEL ORIGEN -->
<div id="modalInterface" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:100%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        
        <h4 class="modal-title">Origen</h4>
      </div>
      <div class="modal-body">

         <div class="container">
            <div class="row">
                <div class="container col-md-12">
                  <table id="tmodalInterface" name="tmodalInterface" class="display table-bordered" width="100%">
                      <thead>
                          <tr class="btn-default active">
                              <th><b>Documento</b></th>
                              <th><b>Concepto</b></th>
                              <th><b>Numero</b></th>
                              <th><b>Tercero</b></th>
                              <th><b>Fecha</b></th>
                          </tr>
                      </thead>
                      <tfoot>
                          <tr class="btn-default active">
                              <th>Documento</th>
                              <th>Concepto</th>
                              <th>Numero</th>
                              <th>Tercero</th>
                              <th>Fecha</th>
                          </tr>
                      </tfoot>
                  </table>                
                </div>
            </div>
        </div>
      </div>
       <div class="modal-footer">
            <button id="btnInterface" name="btnInterface" type="button" class="btn btn-primary">Seleccionar</button>
            <button type="button" class="btn btn-danger"  data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL PARA LOS DATOS DEL DESTINO -->
<div id="modalInterfaceDestino" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:100%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        
        <h4 class="modal-title">Origen</h4>
      </div>
      <div class="modal-body">

         <div class="container">
            <div class="row">
                <div class="container col-md-12">
                  <table id="tinterfacedestinoselect" name="tinterfacedestinoselect" class="display table-bordered" width="100%">
                      <thead>
                          <tr class="btn-default active">
                              <th><b>&nbsp;</b></th>
                          </tr>
                      </thead>
                      <tfoot>
                          <tr class="btn-default active">
                              <th>&nbsp;</th>
                          </tr>
                      </tfoot>
                  </table>                
                </div>
            </div>
        </div>
      </div>
       <div class="modal-footer">
            <button id="btnInterfaceDestino" name="btnInterfaceDestino" type="button" class="btn btn-primary">Seleccionar</button>
            <button type="button" class="btn btn-danger"  data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>