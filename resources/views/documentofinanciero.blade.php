@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Documentos Financieros</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/documentofinanciero.js')!!}
<script>

    var documentofinancierodetalle = '<?php echo (isset($documentofinanciero) ? json_encode($documentofinanciero->documentofinancierodetalle) : "");?>';
    documentofinancierodetalle = (documentofinancierodetalle != '' ? JSON.parse(documentofinancierodetalle) : '');

    eventochange = ['onchange','calcularTotales()'];

    var valorDocumentoFinanciero = [0,'','','','',0,0];

    $(document).ready(function(){

      documentofinanciero = new Atributos('documentofinanciero','contenedor_documentofinanciero','documentofinanciero_');

      documentofinanciero.altura = '35px';
      documentofinanciero.campoid = 'idDocumentoFinancieroDetalle';
      documentofinanciero.campoEliminacion = 'eliminarDocumentoFinanciero';

      documentofinanciero.campos   = ['DocumentoFinanciero_idDocumentoFinanciero', 'Compra_idCompra', 'numeroCompraDocumentoFinancieroDetalle', 'Factura_idFactura', 'numeroFacturaDocumentoFinancieroDetalle', 'valorFobDocumentoFinancieroDetalle', 'valorPagoDocumentoFinancieroDetalle', 'idDocumentoFinancieroDetalle'];
      documentofinanciero.etiqueta = ['input', 'input', 'input', 'input', 'input', 'input', 'input', 'input'];
      documentofinanciero.tipo     = ['hidden', 'hidden','text','hidden','text','text','text','hidden'];
      documentofinanciero.estilo   = ['', '', 'width: 240px;height:35px;', '', 'width: 240px;height:35px;', 'width: 240px;height:35px;', 'width: 240px;height:35px;', ''];
      documentofinanciero.clase    = ['', '', '', '', '', '', '', ''];
      documentofinanciero.sololectura = [true, true, true, true, true, true, false, true];  
      documentofinanciero.completar = ['off','off','off','off','off','off', 'off', 'off'];
      documentofinanciero.funciones = ['','','','','','',eventochange, ''];
      for(var j=0, k = documentofinancierodetalle.length; j < k; j++)
      {
        documentofinanciero.agregarCampos(JSON.stringify(documentofinancierodetalle[j]),'L');
        console.log(JSON.stringify(documentofinancierodetalle[j]))
      }

      calcularTotales();

    });

  </script>

  <script>

    var documentofinancieroprorroga = '<?php echo (isset($documentofinanciero) ? json_encode($documentofinanciero->documentofinancieroprorroga) : "");?>';
    documentofinancieroprorroga = (documentofinancieroprorroga != '' ? JSON.parse(documentofinancieroprorroga) : '');

    eventochange1 = ['onchange','validarFecha(this.id,this.value)'];

    var valorDocumentoFinancieroProrroga = ['','',0];

    $(document).ready(function(){

      documentoprorroga = new Atributos('documentoprorroga','contenedor_documentoprorroga','documentofinanciero_');

      documentoprorroga.altura = '35px';
      documentoprorroga.campoid = 'idDocumentoFinancieroProrroga';
      documentoprorroga.campoEliminacion = 'eliminarDocumentoFinancieroProrroga';

      documentoprorroga.campos   = ['fechaProrrogaDocumentoFinancieroProrroga', 'observacionDocumentoFinancieroProrroga' ,'DocumentoFinanciero_idDocumentoFinanciero','idDocumentoFinancieroProrroga'];
      documentoprorroga.etiqueta = ['input', 'input', 'input','input'];
      documentoprorroga.tipo     = ['date', 'text','hidden','hidden'];
      documentoprorroga.estilo   = ['width: 240px;height:35px;', 'width: 560px;height:35px;', '', ''];
      documentoprorroga.clase    = ['', '', '', ''];
      documentoprorroga.sololectura = [false, false, true, true];  
      documentoprorroga.completar = ['off','off','off', 'off'];
      documentoprorroga.funciones = [eventochange1,'','',''];
      for(var j=0, k = documentofinancieroprorroga.length; j < k; j++)
      {
        documentoprorroga.agregarCampos(JSON.stringify(documentofinancieroprorroga[j]),'L');
        console.log(JSON.stringify(documentofinancieroprorroga[j]))
      }

    });

  </script>
	
			@if(isset($documentofinanciero))
        @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
          {!!Form::model($documentofinanciero,['route'=>['documentofinanciero.destroy',$documentofinanciero->idDocumentoFinanciero],'method'=>'DELETE'])!!}
        @else
          {!!Form::model($documentofinanciero,['route'=>['documentofinanciero.update',$documentofinanciero->idDocumentoFinanciero],'method'=>'PUT'])!!}
        @endif
      @else
        {!!Form::open(['route'=>'documentofinanciero.store','method'=>'POST'])!!}
      @endif
	
<div id='form-section' >

	<fieldset id="documentofinanciero-form-fieldset">	

		    <div class="form-group col-md-6" id='test'>
          {!!Form::label('ListaFinanciacion_idListaFinanciacion', 'Tipo de financiación', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!! Form::select('ListaFinanciacion_idListaFinanciacion', $listafinanciacion ,null,['class' => 'chosen-select form-control','placeholder'=>'Seleccione un tipo de financiación']) !!}
              {!!Form::hidden('idDocumentoFinanciero', null, array('id' => 'idDocumentoFinanciero')) !!}
              {!!Form::hidden('eliminarDocumentoFinanciero', null, array('id' => 'eliminarDocumentoFinanciero')) !!}
              {!!Form::hidden('eliminarDocumentoFinancieroProrroga', null, array('id' => 'eliminarDocumentoFinancieroProrroga')) !!}
            </div>
          </div>
        </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('numeroDocumentoFinanciero', 'N° Documento', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-barcode "></i>
            </span>
            {!!Form::text('numeroDocumentoFinanciero',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off', 'onchange'=>'consultarDocumentoFinanciero(this.value)', 'onclick'=>'validarListaFinanciacion()'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('fechaNegociacionDocumentoFinanciero', 'Fecha de Negociación', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaNegociacionDocumentoFinanciero',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','readonly'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('fechaVencimientoDocumentoFinanciero', 'Fecha de Vencimiento', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </span>
            {!!Form::text('fechaVencimientoDocumentoFinanciero',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','readonly'])!!}
          </div>
        </div>
      </div>


      <div class="form-group col-md-6" id='test'>
        {!!Form::label('nombreEntidadDocumentoFinanciero', 'Entidad', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-bank "></i>
            </span>
            {!!Form::text('nombreEntidadDocumentoFinanciero',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','readonly'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='entrada'>
        {!!Form::label('valorDocumentoFinanciero', 'Valor', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-usd"></i>
            </span>
            {!!Form::text('valorDocumentoFinanciero',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','readonly'])!!}
          </div>
        </div>
      </div>

      <br><br><br><br><br>

        <div class="form-group">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">Detalle</div>
              <div class="panel-body">
                <div class="panel-group" id="accordion">
                  <div class="panel panel-info">
                  <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#detalle">Detalle</a>
                      </h4>
                    </div>
                    <div id="detalle" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-12">
                            <div class="row show-grid">
                              <div class="col-md-1" style="width: 40px; height:42px; cursor: pointer;" onclick="agregarCompras();">
                                <span class="glyphicon glyphicon-plus"></span>
                              </div>
                              <div class="col-md-1" style="width: 240px;">Compra</div>
                              <div class="col-md-1" style="width: 240px;">Factura</div>
                              <div class="col-md-1" style="width: 240px;">Valor FOB</div>
                              <div class="col-md-1" style="width: 240px;">Programado</div>
                              <div id="contenedor_documentofinanciero"> 
                              </div>
                            </div>
                          </div>
                        </div>
                      

                        <div class="form-group col-md-5" id='test' style="display:inline-block; float:right;">
                          {!!Form::label('totalProgramadoDocumentoFinanciero', 'Total Programado: ', array('class' => 'col-sm-3 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-usd"></i>
                              </span>
                              {!!Form::text('totalProgramadoDocumentoFinanciero',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
                            </div>
                          </div>
                        </div>

                        <div class="form-group col-md-4" id='test' style="display:inline-block; float:right;">
                          {!!Form::label('totalFobDocumentoFinanciero', 'Total FOB: ', array('class' => 'col-sm-2 control-label')) !!}
                          <div class="col-md-8">
                            <div class="input-group">
                              <span class="input-group-addon">
                                <i class="fa fa-usd"></i>
                              </span>
                              {!!Form::text('totalFobDocumentoFinanciero',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>  
                </div>

                <div class="panel-group" id="accordion">
                  <div class="panel panel-info">
                  <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#prorroga" href="#prorroga">Prórroga</a>
                      </h4>
                    </div>
                    <div id="prorroga" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-12">
                            <div class="row show-grid">
                              <div class="col-md-1" style="width: 40px; height:42px; cursor: pointer;" onclick="documentoprorroga.agregarCampos(valorDocumentoFinancieroProrroga,'A');">
                                <span class="glyphicon glyphicon-plus"></span>
                              </div>
                              <div class="col-md-1" style="width: 240px;">Fecha</div>
                              <div class="col-md-1" style="width: 560px;">Observacion</div>
                              <div id="contenedor_documentoprorroga"> 
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
<input type="hidden" id="token" value="{{csrf_token()}}"/>
    </fieldset>

    @if(isset($documentofinanciero))
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


<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE COMPRAS -->
  <input type="hidden" id="estadoModalCompra" value="0">
    <div id="modalCompras" class="modal fade" role="dialog">
      <div class="modal-dialog" style="width:100%">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Seleccione un registro de la lista</h4>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="row">
                <div class="container col-md-12">
                      <table id="tcompradocumentofinanciero" name="tcompradocumentofinanciero" class="display table-bordered" width="100%">
                          <thead>
                              <tr class="btn-primary active">
                                  <th><b>Compra</b></th>
                                  <th><b>Factura</b></th>
                                  <th><b>Valor FOB</b></th>
                                  <th><b>Programado</b></th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr class="btn-default active">
                                  <th>Compra</th>
                                  <th>Factura</th>
                                  <th>Valor FOB</th>
                                  <th>Programado</th>
                              </tr>
                          </tfoot>
                      </table>
                  </div>
              </div>
            </div>
          </div>
      <div class="modal-footer">
        <button id="botonCompra" name="botonCompra" type="button" class="btn btn-primary">Seleccionar</button>
        <button type="button" class="btn btn-danger" id="botonCloseCompra" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>