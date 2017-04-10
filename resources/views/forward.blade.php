@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Forward</center></h3>@stop
@section('content')
@include('alerts.request')
{!!Html::script('js/forward.js')!!}

<script>

    var forward = '<?php echo (isset($forward) ? json_encode($forward->forwarddetalle) : "");?>';
    forward = (forward != '' ? JSON.parse(forward) : '');

    eventoclick1 = ['onclick','abrirModalTemporada(this)'];

    eventoclick2 = ['onclick','abrirModalCompra(this)'];

    eventochange = ['onchange','calcularTotales()']

    var valorForward = [0,0,'','',0,'','','',0,0,0];

    $(document).ready(function(){

      forwards = new Atributos('forwards','contenedor_forward','forwarddetalle');

      forwards.altura = '35px';
      forwards.campoid = 'idForwardDetalle';
      forwards.campoEliminacion = 'eliminarForwardDetalle';

      forwards.campos   = [
      'idForwardDetalle',
      'Temporada_idTemporada',
      'nombreTemporadaForwardDetalle',
      'botonTemporada',
      'Compra_idCompra',
      'numeroCompraForwardDetalle',
      'botonCompra',
      'proveedorCompraForwardDetalle',
      'valorForwardDetalle',
      'valorRealForwardDetalle',
      'Forward_idForward'];

      forwards.etiqueta = [
      'input',
      'input',
      'input',
      'button',
      'input',
      'input',
      'button',
      'input',
      'input',
      'input',
      'input'];

      forwards.tipo = [
      'hidden',
      'hidden',
      'text',
      'button',
      'hidden',
      'text',
      'button',
      'text',
      'text',
      'text',
      'hidden'];

      forwards.estilo = [
      '',
      '',
      'width: 260px;height:35px;',
      'width: 40px;height:35px;',
      '',
      'width: 160px;height:35px;',
      'width: 40px;height:35px;',
      'width: 300px;height:35px;',
      'width: 190px;height:35px; text-align: right;',
      'width: 190px;height:35px;text-align: right;',
      ''];

      forwards.clase    = ['','','','fa fa-external-link btn btn-primary','','','fa fa-external-link btn btn-primary','','','',''];

      forwards.sololectura = [true,true,true,true,true,true,true,true,true,false,true];  

      forwards.funciones = ['','','',eventoclick1,'','',eventoclick2,'','',eventochange,''];

      forwards.completar = ['off','off','off','off','off','off','off','off','off','off','off'];

      for(var j=0, k = forward.length; j < k; j++)
      {
        forwards.agregarCampos(JSON.stringify(forward[j]),'L');
        llenarDatosCompra(document.getElementById('Compra_idCompra'+j));
      }

      calcularTotales();

    });

  </script>

@if(isset($forward))
	@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
		{!!Form::model($forward,['route'=>['forward.destroy',$forward->idForward],'method'=>'DELETE'])!!}
	@else
		{!!Form::model($forward,['route'=>['forward.update',$forward->idForward],'method'=>'PUT'])!!}
	@endif
@else
	{!!Form::open(['route'=>'forward.store','method'=>'POST'])!!}
@endif

<div id='form-section' >

	<fieldset id="forward-form-fieldset">	
    <div id="padre" class="col-md-12">

		    <div class="form-group col-md-6" id='test'>
          {!!Form::label('numeroForward', 'Forward', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::text('numeroForward',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        {!!Form::hidden('idForward', null, array('id' => 'idForward')) !!}
        {!!Form::hidden('eliminarForwardDetalle', null, array('id' => 'eliminarForwardDetalle')) !!}

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('descripcionForward', 'Descripción', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
              {!!Form::text('descripcionForward',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaNegociacionForward', 'Negociación', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::input('date','fechaNegociacionForward',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaVencimientoForward', 'Vencimiento', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::input('date','fechaVencimientoForward',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('modalidadForward', 'Modalidad', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-list"></i>
              </span>
              {!! Form::select('modalidadForward', ['Delivery'=>'Delivery','Non_Delivery' => 'Non Delivery'],null,['class' => 'form-control']) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('valorDolarForward', 'Valor USD', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!!Form::text('valorDolarForward',null,['class'=>'form-control','autocomplete'=> 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('tasaForward', 'Tasa', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-sliders"></i>
              </span>
              {!!Form::text('tasaForward',null,['class'=>'form-control','onchange'=>'consultarValorPesos(this.value)','autocomplete'=> 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('tasaInicialForward', 'Tasa Inicial', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-rss"></i>
              </span>
              {!!Form::text('tasaInicialForward',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('valorPesosForward', 'Valor COP', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-usd"></i>
              </span>
              {!!Form::text('valorPesosForward',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('bancoForward', 'Banco', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bank"></i>
              </span>
              {!!Form::text('bancoForward',null,['class'=>'form-control','onchange'=>'abrirModalTercero("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "06")','placeholder'=>'','autocomplete' => 'off'])!!}
              {!!Form::hidden('Tercero_idBanco', null, array('id' => 'Tercero_idBanco')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('rangeForward', 'Range', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-expand"></i>
              </span>
              {!! Form::select('rangeForward', ['Con_Range'=>'Con Range','Sin_Range' => 'Sin Range'],null,['class' => 'form-control']) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('devaluacionForward', 'Devaluación', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-retweet"></i>
              </span>
              {!!Form::text('devaluacionForward',null,['class'=>'form-control','autocomplete'=> 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('spotForward', 'Spot', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-share-alt"></i>
              </span>
              {!!Form::text('spotForward',null,['class'=>'form-control','autocomplete'=> 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('estadoForward', 'Estado', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-server"></i>
              </span>
              {!! Form::select('estadoForward', ['Abierto'=>'Abierto','Cerrado' => 'Cerrado', 'Prorrogado'=>'Prorrogado'],null,['class' => 'form-control']) !!}
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
              {!!Form::select('ForwardPadre_idForwardPadre',$forwardP, (isset($forward) ? $forward->ForwardPadre_idForwardPadre : 0),["class" => "chosen-select form-control", "onchange" => "cargarForwardPadre(this.value)", "placeholder" =>"Seleccione"])!!}
            </div>
          </div>
        </div>

    <input type="hidden" id="token" value="{{csrf_token()}}"/>

    <br><br><br><br><br>

    <div class="panel-body" style="width:1280px;">
      <div class="form-group" id='test'>
        <div class="col-sm-10">
          <div class="panel-body" style="width:1255px;">
            <div class="form-group" id='test'>
              <div class="col-sm-12">
                <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
                  <div style="overflow:auto; height:350px;">
                    <div style="width: 1223px; display: inline-block;">
                      <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="forwards.agregarCampos(valorForward,'A')">
                        <span class="glyphicon glyphicon-plus"></span>
                      </div>
                      <div class="col-md-1" style="width: 300px;">Temporada</div>
                      <div class="col-md-1" style="width: 200px;">PI</div>
                      <div class="col-md-1" style="width: 300px;">Proveedor</div>
                      <div class="col-md-1" style="width: 190px;">Valor</div>
                      <div class="col-md-1" style="width: 190px;">Valor Programado</div>
                      <div id="contenedor_forward"> 
                      </div>
                    </div>
                  </div>
                </div>
                 <div class="form-group col-md-4" id='test' style="display:inline-block; float:right;">
                  {!!Form::label('totalForward', 'Total: ', array('class' => 'col-sm-3 control-label')) !!}
                  <div class="col-md-8">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-usd"></i>
                      </span>
                      {!!Form::text('totalForward',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
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
	@if(isset($forward))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
        {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
      @else
        {!!Form::submit('Modificar',["class"=>"btn btn-primary", 'id'=>'Modificar',"onclick"=>'validarFormulario(event);'])!!}
      @endif
  @else
      {!!Form::submit('Adicionar',["class"=>"btn btn-primary",'id'=>'Adicionar',"onclick"=>'validarFormulario(event);'])!!}
  @endif

	{!! Form::close() !!}
</div>
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
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE TEMPORADAS -->
    <div id="modalTemporada" class="modal fade" role="dialog">
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
                      
                      
                      <table id="ttemporada" name="ttemporada" class="display table-bordered" width="100%">
                          <thead>
                              <tr class="btn-primary active">
                                  <th><b>Nombre</b></th>
                                  <th><b>Valor total compras</b></th>
                                  <th><b>Pagado</b></th>
                                  <th><b>Saldo Final</b></th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr class="btn-default active">

                                  <th>Nombre</th>
                                  <th>Valor total compras</th>
                                  <th>Pagado</th>
                                  <th>Saldo Final</th>
                              </tr>
                          </tfoot>
                      </table>
                  </div>
              </div>
            </div>
          </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE COMPRAS -->
    <div id="modalCompras" class="modal fade" role="dialog">
      <div class="modal-dialog" style="width:100%">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Seleccione un registro de la lista</h4>
          </div>
          <div class="modal-body">
            <!-- <div class="container"> -->
              <div class="row">
                <div class="container col-md-12">
                      <table id="tcompra" name="tcompra" class="display table-bordered">
                          <thead>
                              <tr class="btn-primary active">
                                  <th><b>Temporada</b></th>
                                  <th><b>PI</b></th>
                                  <th><b>Proveedor</b></th>
                                  <th><b>Valor FOB</b></th>
                                  <th><b>Programado</b></th>
                                  <th><b>Restante</b></th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr class="btn-default active">
                                  <th>Temporada</th>
                                  <th>PI</th>
                                  <th>Proveedor</th>
                                  <th>Valor FOB</th>
                                  <th>Programado</th>
                                  <th>Restante</th>
                              </tr>
                          </tfoot>
                      </table>
                  </div>
              </div>
            <!-- </div> -->
          </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>