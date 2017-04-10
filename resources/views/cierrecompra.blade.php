@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Cierre de compras</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/cierrecompra.js')!!}


<script>

    var cierrecompracartera = '<?php echo (isset($cierrecompracartera) ? json_encode($cierrecompracartera) : "");?>';
    cierrecompracartera = (cierrecompracartera != '' ? JSON.parse(cierrecompracartera) : '');

    var valorAbonoCartera = [0,0,'',0,'','','','',0];

    $(document).ready(function(){

      abonocartera = new Atributos('abonocartera','contenedor_abonocartera','cierrecompracartera');

      abonocartera.altura = '35px';
      abonocartera.campoid = 'idCierreCompraCartera';
      abonocartera.campoEliminacion = 'eliminarAbonoCartera';

      abonocartera.campos   = [
      'idCierreCompraCartera',
      'Documento_idDocumento',
      'nombreDocumentoCierreCompraCartera',
      'Movimiento_idMovimiento',
      'numeroMovimientoCierreCompraCartera',
      'facturaCierreCompraCartera',
      'numeroCompraCierreCompraCartera',
      'valorCierreCompraCartera',
      'CierreCompra_idCierreCompra'
      ];

      abonocartera.etiqueta = [
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input'
      ];

      abonocartera.tipo = [
      'hidden',
      'hidden',
      'text',
      'hidden',
      'text',
      'text',
      'text',
      'text',
      'hidden'
      ];

      abonocartera.estilo = [
      '',
      '',
      'width: 310;height:35px;',
      '',
      'width: 150px;height:35px; display:inline-block;',
      'width: 150px;height:35px; display:inline-block;',
      'width: 150px;height:35px; display:inline-block;',
      'width: 150px;height:35px; display:inline-block;',
      ''
      ];

      abonocartera.clase    = ['','','','','','','','',''];
      abonocartera.sololectura = [true,true,true,true,true,true,true,true,true];  
      abonocartera.funciones = ['','','','','','','','',''];
      abonocartera.completar = ['off','off','off','off','off','off','off','off','off'];
      abonocartera.opciones = ['','','','','','','','',''];
      for(var j=0, k = cierrecompracartera.length; j < k; j++)
      {
        abonocartera.agregarCampos(JSON.stringify(cierrecompracartera[j]),'L');
        llenarDatosAbonoCartera($('#Movimiento_idMovimiento'+j).val(), j);
      }

    });

  </script>

  <script>

    var cierrecomprasaldo = '<?php echo (isset($cierrecomprasaldo) ? json_encode($cierrecomprasaldo) : "");?>';
    cierrecomprasaldo = (cierrecomprasaldo != '' ? JSON.parse(cierrecomprasaldo) : '');

    var valorSaldoCartera = [0,0,'','',0,0,0];

    $(document).ready(function(){

      saldocartera = new Atributos('saldocartera','contenedor_saldocartera','cierrecomprasaldo');

      saldocartera.altura = '35px';
      saldocartera.campoid = 'idCierreCompraSaldo';
      saldocartera.campoEliminacion = 'eliminarSaldoCartera';

      saldocartera.campos   = [
      'idCierreCompraSaldo',
      'Compra_idCompra',
      'numeroCompraCierreCompraSaldo',
      'nombreTemporadaCierreCompraSaldo',
      'valorCierreCompraSaldo',
      'numeroForwardCierreCompraSaldo',
      'Forward_idForward',
      'CierreCompra_idCierreCompra'
      ];

      saldocartera.etiqueta = [
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input',
      'input'
      ];

      saldocartera.tipo = [
      'hidden',
      'hidden',
      'text',
      'text',
      'text',
      'text',
      'hidden',
      'hidden'
      ];

      saldocartera.estilo = [
      '',
      '',
      'width: 410px;height:35px;',
      'width: 350px;height:35px; display:inline-block;',
      'width: 150px;height:35px; display:inline-block;',
      'width: 250px;height:35px; display:inline-block;',
      '',
      ''
      ];

      saldocartera.clase    = ['','','','','',''];
      saldocartera.sololectura = [true,true,true,true,true,true,true,true];  
      saldocartera.funciones = ['','','','','','','',''];
      saldocartera.completar = ['off','off','off','off','off','off','off','off'];
      saldocartera.opciones = ['','','','','','','',''];
      for(var j=0, k = cierrecomprasaldo.length; j < k; j++)
      {
        saldocartera.agregarCampos(JSON.stringify(cierrecomprasaldo[j]),'L');
        llenarDatosCampo($('#Compra_idCompra'+j).val(), j);
      }

    });

  </script>


	 @if(isset($cierrecompra))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($cierrecompra,['route'=>['cierrecompra.destroy',$cierrecompra->idCierreCompra],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($cierrecompra,['route'=>['cierrecompra.update',$cierrecompra->idCierreCompra],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'cierrecompra.store','method'=>'POST'])!!}
  @endif


<div id='form-section'>
<input type="hidden" id="token" value="{{csrf_token()}}"/>
  <fieldset id="cierrecompra-form-fieldset"> 
    
        <div class="form-group" id='test'>
           {!!Form::label('numeroCierreCompra', 'Número', array('class' => 'col-sm-2 control-label')) !!}
            <div class="col-md-4">
                <div class="input-group">
                   <span class="input-group-addon">
                      <i class="fa fa-bars" aria-hidden="true"></i>
                   </span>
                    {!!Form::text('numeroCierreCompra',null,['class'=> 'form-control','placeholder'=>''])!!}
                    {!!Form::hidden('idCierreCompra', null, array('id' => 'idCierreCompra')) !!}
                    {!!Form::hidden('eliminarAbonoCartera', null, array('id' => 'eliminarAbonoCartera')) !!}
                    {!!Form::hidden('eliminarSaldoCartera', null, array('id' => 'eliminarSaldoCartera')) !!}
                 </div>
            </div>
                                    <!-- Fecha final -->

            <div class="form-group" id='test'>
              {!!Form::label('fechaCierreCompra', 'Fecha', array('class' => 'col-sm-1 control-label')) !!}
              <div class="col-md-4">
                <div class="input-group" >
                 <span class="input-group-addon">
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                 </span>
                  {!!Form::text('fechaCierreCompra',null,['class'=> 'form-control','placeholder'=>''])!!}
                </div>
              </div>
             </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('descripcionCierreCompra', 'Descripción', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
            {!!Form::text('descripcionCierreCompra',null,['class'=>'form-control','placeholder'=>''])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('nombreProveedorCierreCompra', 'Proveedor', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
            {!!Form::text('nombreProveedorCierreCompra',(isset($nombreTercero) ? $nombreTercero["nombreProveedorCierreCompra"] : null),['class'=>'form-control','onchange'=>'abrirModalTercero("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "02")','placeholder'=>'','autocomplete' => 'off'])!!}
            {!!Form::hidden('Tercero_idProveedor', null, array('id' => 'Tercero_idProveedor')) !!}
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

                <!-- <ul class="nav nav-tabs"> 
                  <li class="active"><a data-toggle="tab" href="#cierrecompracartera">Abono a cartera</a></li>
                  <li class="active"><a data-toggle="tab" href="#cierrecomprasaldo">Cierre de compras</a></li>
                </ul>

                <div class="tab-content">
                   -->
                  <!-- <div id="cierrecompracartera" class="tab-pane fade in active">

                    <div class="panel-body">
                      <div class="form-group" id='test'>
                        <div class="col-sm-12">
                          <div class="row show-grid">
                            <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="abrirModalCierreCartera();">
                              <span class="glyphicon glyphicon-plus"></span>
                            </div>
                            <div class="col-md-1" style="width: 310px;">Tipo de documento</div>
                            <div class="col-md-1" style="width: 150px;">Numero</div>
                            <div class="col-md-1" style="width: 150px;">Factura</div>
                            <div class="col-md-1" style="width: 150px;">PI</div>
                            <div class="col-md-1" style="width: 150px;">Saldo</div>
                            <div id="contenedor_abonocartera"> 
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div> -->

                  <!-- <div id="cierrecomprasaldo" class="tab-pane fade"> -->

                    <div class="panel-body">
                      <div class="form-group" id='test'>
                        <div class="col-sm-12">
                          <div class="row show-grid">
                            <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="abrirModalCierreCompra();">
                              <span class="glyphicon glyphicon-plus"></span>
                            </div>
                            <div class="col-md-1" style="width: 410px;">PI</div>
                            <div class="col-md-1" style="width: 350px;">Temporada</div>
                            <div class="col-md-1" style="width: 150px;">Saldo</div>
                            <div class="col-md-1" style="width: 250px;">Forward</div>
                            <div id="contenedor_saldocartera"> 
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  <!-- </div> -->

                </div>
              </div>
            </div>
          </div>
        </div>
    </fieldset>

	@if(isset($cierrecompra))
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
<script>

    $('#fechaCierreCompra').datetimepicker(({
      format: "YYYY-MM-DD HH:mm:ss"
    }));
</script>
@stop

<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE TERCERO -->
    <div id="ListaSelectTercero" class="modal fade" role="dialog">
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
                      
                      
                      <table id="tlistaselecttercero" name="tlistaselecttercero" class="display table-bordered" width="100%">
                          <thead>
                              <tr class="btn-primary active">

                                  <th style="width:10px;"><b>ID</b></th>
                                  <th style="width:10px;"><b>Nombre</b></th>
                                  <th style="width:10px;"><b>Nombre Comercial</b></th>
                                  <th style="width:10px;"><b>Codigo</b></th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr class="btn-default active">

                                  <th>ID</th>
                                  <th>Nombre</th>
                                  <th>Nombre Comercial</th>
                                  <th>Codigo</th>
                              </tr>
                          </tfoot>
                      </table>
                  </div>
              </div>
            </div>
          </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE RR O DI POR CERRAR -->
    <div id="modalCierreCartera" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <input type="hidden" name="estadoModalCartera" id="estadoModalCartera" value="0">
        <!-- Modal content-->
        <div class="modal-content" style="width:1200px; left:-300px">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Compras por cerrar</h4>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="row">
                  <div class="container">
                      <table id="tcierrecartera" name="tcierrecartera" class="display table-bordered" width="100%">
                          <thead>
                              <tr class="btn-primary active">

                                  <th style="width:10px;"><b>Documento</b></th>
                                  <th style="width:10px;"><b>Numero</b></th>
                                  <th style="width:10px;"><b>Factura</b></th>
                                  <th style="width:10px;"><b>Saldo</b></th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr class="btn-default active">

                                  <th>Documento</th>
                                  <th>Numero</th>
                                  <th>Factura</th>
                                  <th>Saldo</th>
                              </tr>
                          </tfoot>
                      </table>
                  </div>
              </div>
            </div>
          </div>
      <div class="modal-footer">
        <button type="button" id="btnCartera" class="btn btn-primary">Seleccionar</button>
        <button type="button" id="btnCloseCartera" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE COMPRAS POR CERRAR -->
    <div id="modalCierreCompra" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <input type="hidden" name="estadoModalCompra" id="estadoModalCompra" value="0">
        <!-- Modal content-->
        <div class="modal-content" style="width:1200px; left:-300px">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Compras por cerrar</h4>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="row">
                  <div class="container">
                      
                      
                      <table id="tcierrecompra" name="tcierrecompra" class="display table-bordered" width="100%">
                          <thead>
                              <tr class="btn-primary active">

                                  <th style="width:10px;"><b>PI</b></th>
                                  <th style="width:10px;"><b>Temporada</b></th>
                                  <th style="width:10px;"><b>Saldo a favor</b></th>
                                  <th style="width:10px;"><b>Forward</b></th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr class="btn-default active">

                                  <th>PI</th>
                                  <th>Temporada</th>
                                  <th>Saldo a favor</th>
                                  <th>Forward</th>
                              </tr>
                          </tfoot>
                      </table>
                  </div>
              </div>
            </div>
          </div>
      <div class="modal-footer">
        <button type="button" id="btnCompra" class="btn btn-primary">Seleccionar</button>
        <button type="button" id="btnCloseCompra" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>