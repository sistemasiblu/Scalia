@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Cumplimiento de forward</center></h3>@stop
@section('content')
@include('alerts.request')
{!!Html::script('js/pagoforward.js')!!}

<script>
  
  var pagoforward = '<?php echo (isset($pagoforward) ? json_encode($pagoforward->pagoforwarddetalle) : "");?>';
    pagoforward = (pagoforward != '' ? JSON.parse(pagoforward) : '');

    eventoclick1 = ['onclick','abrirModalDocumento(this.id)'];

    eventochange = ['onchange','calcularTotales()'];


    var valorPagoForward = [0,0,'','',0,'',0,'','','',0,0,0];

    $(document).ready(function(){

      $("#fechaPagoForward").datetimepicker
        (
            ({
               format: "YYYY-MM-DD"
             })
        ); 

      pagosforward = new Atributos('pagosforward','contenedor_pagoforward','forwarddetalle');

      pagosforward.altura = '35px';
      pagosforward.campoid = 'idPagoForwardDetalle';
      pagosforward.campoEliminacion = 'eliminarPagoForwardDetalle';

      pagosforward.campos   = [
      'idPagoForwardDetalle',
      'Temporada_idTemporada',
      'nombreTemporadaPagoForwardDetalle',
      'Compra_idCompra',
      'numeroCompraPagoForwardDetalle',
      'DocumentoFinanciero_idDocumentoFinanciero',
      'numeroDocumentoFinancieroPagoForwardDetalle',
      'facturaPagoForwardDetalle',
      'fechaFacturaPagoForwardDetalle',
      'valorFacturaPagoForwardDetalle',
      'valorPagadoPagoForwardDetalle',
      'PagoForward_idPagoForward'];

      pagosforward.etiqueta = [
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input'];

      pagosforward.tipo = [
      'hidden',
      'hidden',
      'text',
      'hidden',
      'text',
      'hidden',
      'text',
      'text',
      'text',
      'text',
      'text',
      'hidden'];

      pagosforward.estilo = [
      '',
      '',
      'width: 325px;height:35px;',
      '',
      'width: 200px;height:35px;',
      '',
      'width: 200px;height:35px;',
      'width: 200px;height:35px;',
      'width: 200px;height:35px;',
      'width: 200px;height:35px; text-align: right;',
      'width: 200px;height:35px; text-align: right;',
      ''];

      pagosforward.clase    = ['','','','','','','','','','','',''];

      pagosforward.sololectura = [true,true,true,true,true,true,true,false,false,true,false,true];  

      pagosforward.funciones = ['','','','','','','','','','',eventochange,''];

      pagosforward.opciones = ['','','','','','','','','','','',''];

      pagosforward.completar = ['off','off','off','off','off','off','off','off','off','off','off','off'];

      for(var j=0, k = pagoforward.length; j < k; j++)
      {
        pagosforward.agregarCampos(JSON.stringify(pagoforward[j]),'L');
      }

      if(document.getElementById('Forward_idForward').value > 0)
      {
        consultarDatosForward($("#Forward_idForward").val())
      }
      
      calcularTotales();

      for (var i = 0; i < window.parent.pagosforward.contador; i++) 
      {
        $("#fechaFacturaPagoForwardDetalle"+i+", #fechaPagoForwardDetalle"+i+", #fechaGiroPagoForwardDetalle"+i+", #fechaPagoGiroPagoForwardDetalle"+i).datetimepicker
        (
            ({
               format: "YYYY-MM-DD"
             })
        );  
      }

    });
</script>

@if(isset($pagoforward))
	@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
		{!!Form::model($pagoforward,['route'=>['pagoforward.destroy',$pagoforward->idPagoForward],'method'=>'DELETE'])!!}
	@else
		{!!Form::model($pagoforward,['route'=>['pagoforward.update',$pagoforward->idPagoForward],'method'=>'PUT'])!!}
	@endif
@else
	{!!Form::open(['route'=>'pagoforward.store','method'=>'POST'])!!}
@endif

<div id='form-section' >

	<fieldset id="pagoforward-form-fieldset">	
    <div id="padre" class="col-md-12">

		    <div class="form-group col-md-6" id='test'>
          {!!Form::label('Forward_idForward', 'Forward', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::select('Forward_idForward',$forward, (isset($pagoforward) ? $pagoforward->Forward_idForward : 0),["class" => "chosen-select form-control", "placeholder" =>"Seleccione", 'onchange' => 'consultarDatosForward(this.value)'])!!}
            </div>
          </div>
        </div>

        {!!Form::hidden('idPagoForward', null, array('id' => 'idPagoForward')) !!}
        {!!Form::hidden('eliminarPagoForwardDetalle', null, array('id' => 'eliminarPagoForwardDetalle')) !!}

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaPagoForward', 'Fecha Pago', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaPagoForward',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('descripcionPagoForward', 'Descripción', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
              {!!Form::text('descripcionPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaNegociacionPagoForward', 'Negociación', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::input('date','fechaNegociacionPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaVencimientoPagoForward', 'Vencimiento', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::input('date','fechaVencimientoPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('modalidadPagoForward', 'Modalidad', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-list"></i>
              </span>
              {!!Form::text('modalidadPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('valorDolarPagoForward', 'Valor USD', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!!Form::text('valorDolarPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('tasaPagoForward', 'Tasa', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-sliders"></i>
              </span>
              {!!Form::text('tasaPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('tasaInicialPagoForward', 'Tasa Inicial', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-rss"></i>
              </span>
              {!!Form::text('tasaInicialPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('valorPesosPagoForward', 'Valor COP', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-usd"></i>
              </span>
              {!!Form::text('valorPesosPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('bancoPagoForward', 'Banco', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bank"></i>
              </span>
              {!!Form::text('bancoPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('rangePagoForward', 'Range', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-expand"></i>
              </span>
              {!!Form::text('rangePagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('devaluacionPagoForward', 'Devaluación', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-retweet"></i>
              </span>
              {!!Form::text('devaluacionPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('spotPagoForward', 'Spot', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-share-alt"></i>
              </span>
              {!!Form::text('spotPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('estadoPagoForward', 'Estado', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-server"></i>
              </span>
              {!!Form::text('estadoPagoForward',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('ForwardPadre_idForwardPadre', 'Forward Padre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::text('ForwardPadre_idForwardPadre',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

    <input type="hidden" id="token" value="{{csrf_token()}}"/>
  <br><br><br><br><br>

    <div class="panel-body" style="width:1280px;">
      <div class="form-group" id='test'>
        <div class="col-sm-10">
          <div class="panel-body" style="width:1280px;">
            <div class="form-group" id='test'>
              <div class="col-sm-12">
                <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
                  <div style="overflow:auto; height:350px;">
                    <div style="width: 1570px; display: inline-block;">
                      <div class="col-md-1" title="PI o Temporada" style="width: 20px; height: 42px; cursor:pointer;" onclick="abrirModalForward();">
                      <input type="hidden" id="estadoModalForward" value="0">
                        <span class="glyphicon glyphicon-file"></span>
                      </div>
                      <div class="col-md-1" title="IM" style="width: 20px; height: 42px; cursor:pointer;" onclick="abrirModalIM();">
                      <input type="hidden" id="estadoModalIm" value="0">
                        <span class="glyphicon glyphicon-edit"></span>
                      </div>
                      <div class="col-md-1" style="width: 300px;">Temporada</div>
                      <div class="col-md-1" style="width: 200px;">PI</div>
                      <div class="col-md-1" style="width: 200px;">Documento Financiero</div>
                      <div class="col-md-1" style="width: 200px;">Factura</div>
                      <div class="col-md-1" style="width: 200px;">Fecha de factura</div>
                      <div class="col-md-1" style="width: 200px;">Valor factura</div>
                      <div class="col-md-1" style="width: 200px;">Valor pagado</div>
                      <div id="contenedor_pagoforward">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-4" id='test' style="display:inline-block; float:right;">
                  {!!Form::label('valorTotalPagoForward', 'Valor total: ', array('class' => 'col-sm-3 control-label')) !!}
                  <div class="col-md-8">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-usd"></i>
                      </span>
                      {!!Form::text('valorTotalPagoForward',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
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
	@if(isset($pagoforward))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
        {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
      @else
        {!!Form::submit('Modificar',["class"=>"btn btn-primary", 'id'=>'Modificar',"onclick"=>'validarFormulario(event);'])!!}
      @endif
  @else
      {!!Form::submit('Adicionar',["class"=>"btn btn-primary",'id'=>'Adicionar',"onclick"=>'validarFormulario(event);'])!!}
  @endif

  {!!Form::button('Calcular totales',["class"=>"btn btn-primary",'id'=>'Calcular',"onclick"=>'calcularTotales();'])!!}

	{!! Form::close() !!}
</div>

<script type="text/javascript">
    $(document).ready(function (){
      var config = {
        '.chosen-select'           : {},
        '.chosen-select-deselect'  : {allow_single_deselect:true},
        '.chosen-select-no-single' : {disable_search_threshold:10},
        '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
        '.chosen-select-width'     : {width:"95%"}
      }
      for (var selector in config) {
        $(selector).chosen(config[selector]);
      }
  });
</script>  
@stop

<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE TEMPORADAS Y PI -->
<div id="modalForward" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" style="width:1200px; left:-300px">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Seleccione un registro de la lista</h4>
      </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
                <div class="container">
                    <table id="tforward" name="tforward" class="display table-bordered">
                        <thead>
                          <tr class="btn-primary active">
                              <th><b>Temporada</b></th>
                              <th ><b>PI</b></th>
                              <th ><b>Documento Financiero</b></th>
                              <th ><b>Valor Programado</b></th>
                              <th ><b>Restante</b></th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr class="btn-default active">
                              <th>Temporada</th>
                              <th>PI</th>
                              <th>Documento Financiero</th>
                              <th>Valor Programado</th>
                              <th>Restante</th>
                          </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
          </div>
        </div>
      <div class="modal-footer">
        <button id="botonForward" name="botonForward" type="button" class="btn btn-primary">Seleccionar</button>
        <button type="button" class="btn btn-danger" id="botonCloseForward" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE IMS DE SAYA QUE TIENEN SALDO PENDIENTE EN CARTERA -->
<div id="modalIM" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" style="width:1200px; left:-300px">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Seleccione un registro de la lista</h4>
      </div>
        <div class="modal-body">
          <div class="container">
            <div class="row">
                <div class="container">
                    <table id="tim" name="tim" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">
                                <th style="width:10px;"><b>Numero</b></th>
                                <th style="width:10px;"><b>Fecha</b></th>
                                <th style="width:10px;"><b>Tercero</b></th>
                                <th style="width:10px;"><b>Valor</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">
                                <th>Numero</th>
                                <th>Fecha</th>
                                <th>Tercero</th>
                                <th>Valor</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="botonIM" name="botonIM" type="button" class="btn btn-primary">Seleccionar</button>
          <button type="button" class="btn btn-danger" id="botonCloseIM" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
  </div>
</div>

<!-- ABRO EL MODAL Y DENTRO DE EL ESTAN LOS DATOS DETALLADOS DE LA COMPRA -->
<div id="modalDetallesCompras" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" style="width:100%x;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Detalles de la compra</h4>
      </div>
        <div class="modal-body">
          <div id="detalleCompra"></div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
