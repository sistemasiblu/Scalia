<?php 
$idDocumentoImportacion = $_GET['idDocumento'];

$docImportacion  = DB::Select('SELECT * from documentoimportacion where idDocumentoImportacion = '.$idDocumentoImportacion);

$importacion = get_object_vars($docImportacion[0]);
?>

@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Embarque <?php echo $importacion['nombreDocumentoImportacion'];?></center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/embarque.js')!!}

<script>

    var embarque = '<?php echo (isset($embarque) ? json_encode($embarque->embarquedetalle) : "");?>';
    embarque = (embarque != '' ? JSON.parse(embarque) : '');

    eventoclick1  = ['onclick','mostrarDatosCompra(this,\'compra\');'];

    eventoblur = ['onblur','validarValorUnidad(this)'];

    eventoclick2 = ['onclick','abrirModalEmbarque(this)'];

    eventochange = ['onchange','reenviarCorreoEmbarque(this)'];

    valorTipoContenedor =  Array("1x20","1x40", "LCL");
    nombreTipoContenedor =  Array("1x20","1x40", "LCL");

    tipoContenedor = [valorTipoContenedor, nombreTipoContenedor]; 

    var valorEmbarquesDetalle = [0,'','','','','','',0,0,0,0,0,0,'',0,'','','','','','',0,'',0,'','','','','',0,0,0,0,'',0,0,'',0,0,''];

    $(document).ready(function(){

      var stilocheck = 'width: 150px;height:35px;display:inline-block;';
      var stilocheck1 = 'width: 100px;height:35px;display:inline-block;';
      embarques = new Atributos('embarques','contenedor_embarque','embarquedetalles');

      embarques.altura = '35px';
      embarques.campoid = 'idEmbarqueDetalle';
      embarques.campoEliminacion = 'eliminarEmbarqueDetalle';

      embarques.campos   = [
      'Compra_idCompra',
      'modalEmbarque',
      'nombreTemporadaEmbarqueDetalle',
      'proveedorTemporadaEmbarqueDetalle',
      'numeroCompraEmbarqueDetalle',
      'fechaDeliveryEmbarqueDetalle',
      'proformaEmbarqueDetalle',
      'volumenEmbarqueDetalle',
      'valorEmbarqueDetalle',
      'unidadEmbarque',
      'unidadEmbarqueDetalle',
      'pesoEmbarqueDetalle',
      'bultoEmbarqueDetalle',
      'facturaEmbarqueDetalle',
      'volumenFacturaEmbarqueDetalle',
      'valorFacturaEmbarqueDetalle',
      'unidadFacturaEmbarqueDetalle',
      'pesoFacturaEmbarqueDetalle',
      'bultoFacturaEmbarqueDetalle',
      'fechaReservaEmbarqueDetalle',
      'fechaRealEmbarqueDetalle',
      'fechaMaximaEmbarqueDetalle',
      'fechaLlegadaZonaFrancaEmbarqueDetalle',
      'compradorEmbarqueDetalle',
      'eventoEmbarqueDetalle',
      'dolarEmbarqueDetalle',
      'fechaArriboPuertoEstimadaEmbarqueDetalle',
      'fechaArriboPuertoEmbarqueDetalle',
      'soportePagoEmbarqueDetalle',
      'compradorVendedorEmbarqueDetalle',
      'cantidadContenedorEmbarqueDetalle',
      'tipoContenedorEmbarqueDetalle',
      'numeroContenedorEmbarqueDetalle',
      'blEmbarqueDetalle',
      'numeroCourrierEmbarqueDetalle',
      'pagoEmbarqueDetalle',
      'originalEmbarqueDetalle',
      'descripcionEmbarqueDetalle',
      'idEmbarqueDetalle',
      'pagoCorreoEmbarqueDetalle',
      'fileEmbarqueDetalle',
      'observacionEmbarqueDetalle'];

      embarques.etiqueta = [
      'input',
      'button',
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
      'input',
      'input',
      'input',
      'input',
      'checkbox',
      'input',
      'input',
      'select',
      'input',
      'input',
      'input',
      'checkbox',
      'checkbox',
      'input',
      'input',
      'input',
      'input',
      'input'];

      embarques.tipo = [
      'hidden',
      'button',
      'text',
      'text',
      'text',
      'date',
      'text',
      'text',
      'text',
      'hidden',
      'text',
      'text',
      'text',
      'text',
      'text',
      'text',
      'text',
      'text',
      'text',
      'date',
      'date',
      'date',
      'date',
      'text',
      'text',
      'text',
      'date',
      'date',
      'checkbox',
      'text',
      'text',
      '',
      'text',
      'text',
      'text',
      'checkbox',
      'checkbox',
      'text',
      'hidden',
      'hidden',
      'text',
      'text'];

      embarques.estilo = [
      '',
      'width: 60px;height:35px;',
      'width: 300px;height:35px;',
      'width: 300px;height:35px;',
      'width: 200px;height:35px;',
      'width: 100px;height:35px;',
      'width: 150px;height:35px;text-align: right;',
      'width: 100px;height:35px; text-align: right;',
      'width: 150px;height:35px;text-align: right;',
      '',
      'width: 150px;height:35px;text-align: right;',
      'width: 100px;height:35px;text-align: right;',
      'width: 100px;height:35px;text-align: right;',
      'width: 150px;height:35px;text-align: right;',
      'width: 100px;height:35px; text-align: right;',
      'width: 150px;height:35px;text-align: right;',
      'width: 150px;height:35px;text-align: right;',
      'width: 100px;height:35px;text-align: right;',
      'width: 100px;height:35px;text-align: right;',
      'width: 120px;height:35px;',
      'width: 120px;height:35px;',
      'width: 140px;height:35px;',
      'width: 120px;height:35px;',
      'width: 300px;height:35px;',
      'width: 300px;height:35px;',
      'width: 100px;height:35px;text-align: right;',
      'width: 190px;height:35px;',
      'width: 150px;height:35px;',
      stilocheck,
      'width: 300px;height:35px;',
      'width: 200px;height:35px;',
      'width: 150px;height:35px;',
      'width: 150px;height:35px;',
      'width: 200px;height:35px;',
      'width: 200px;height:35px;',
      stilocheck1,
      stilocheck1,
      'width: 300px;height:35px;',
      '',
      '',
      'width: 150px;height:35px;',
      'width: 300px;height:35px;'];

      embarques.clase    = ['','fa fa-external-link btn btn-primary','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',''];

      embarques.sololectura = [true,false,true,true,true,true,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,true,false,false,false,false,true,false,false,false,false,false,false,false,false,false,false,false,false];  

      embarques.funciones = ['',eventoclick2,'','',eventoclick1,'','','',eventochange,'',eventoblur,'','',eventochange,'',eventochange,eventochange,'',eventochange,'','','','','','',eventochange,'','','','','','',eventochange,eventochange,'','','',eventochange,'','','',''];

      embarques.completar = ['off','','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off','off'];

      embarques.opciones = ['','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','',tipoContenedor,'','','','','','','','','','','',''];

      for(var j=0, k = embarque.length; j < k; j++)
      {
        embarques.agregarCampos(JSON.stringify(embarque[j]),'L');
        llenarDatosCompra(document.getElementById('Compra_idCompra'+j));
      }

      calcularTotales();
        
    });

  </script>

  @if(isset($embarque))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($embarque,['route'=>['embarque.destroy',$embarque->idEmbarque],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($embarque,['route'=>['embarque.update',$embarque->idEmbarque],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'embarque.store','method'=>'POST'])!!}
  @endif

<div id='form-section'>

  <fieldset id="embarque-form-fieldset">  
    <div id="padre" class="col-md-12">
   
    <br/> <br/>

    <?php 
    // if(!isset($_GET['accion'])) 
    // {
    //   $numeroEmbarque = DB::Select('SELECT max(numeroEmbarque)+1 as numeroEmbarque from embarque
    //   where DocumentoImportacion_idDocumentoImportacion = '.$idDocumentoImportacion);
    //   $embarque = get_object_vars($numeroEmbarque[0]);
    // }
    ?>

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('numeroEmbarque', 'Embarque N°', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::text('numeroEmbarque',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','onchange'=>'validarNumeroEmbarque(this.value,document.getElementById("sufijoEmbarque").value)'])!!}
              <span class="input-group-addon">
              {!!Form::text('sufijoEmbarque',null,['style' => 'width:25px;', 'autocomplete' => 'off','onchange'=>'validarNumeroEmbarque(document.getElementById("numeroEmbarque").value,this.value)','id' => 'sufijoEmbarque'])!!}
              </span>
            </div>
          </div>
        </div>

        {!!Form::hidden('idEmbarque', null, array('id' => 'idEmbarque')) !!}
        {!!Form::hidden('DocumentoImportacion_idDocumentoImportacion', $importacion["idDocumentoImportacion"], array('id' => 'DocumentoImportacion_idDocumentoImportacion')) !!}
        {!! Form::hidden('eliminarEmbarqueDetalle', null, array('id' => 'eliminarEmbarqueDetalle')) !!}

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('fechaElaboracionEmbarque', 'Fecha elaboración', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaElaboracionEmbarque',(isset($embarque) ? $embarque->fechaElaboracionEmbarque : date('Y-m-d')),['class'=>'form-control','readonly'=>true])!!}
            </div>
          </div>
        </div>


        <div class="form-group col-md-4" id='test'>
          {!!Form::label('tipoTransporteEmbarque', 'Tipo de transporte', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-truck"></i>
              </span>
              {!!Form::text('tipoTransporteEmbarque',null,['class'=>'form-control','onchange'=>'abrirModal("TipoTransporte", "nombreTipoTransporte", "codigoAlternoTipoTransporte", this, "9999")','placeholder'=>'', 'autocomplete' => 'off'])!!}
              {!!Form::hidden('TipoTransporte_idTipoTransporte', null, array('id' => 'TipoTransporte_idTipoTransporte')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('puertoCargaEmbarque', 'Puerto de carga', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-anchor"></i>
              </span>
              {!!Form::text('puertoCargaEmbarque',null,['class'=>'form-control','onchange'=>'abrirModal("Ciudad", "nombreCiudad", "codigoAlternoCiudad", this, "9999")','placeholder'=>'', 'autocomplete' => 'off'])!!}
              {!!Form::hidden('Ciudad_idPuerto_Carga', null, array('id' => 'Ciudad_idPuerto_Carga')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('puertoDescargaEmbarque', 'Puerto de descarga', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-anchor"></i>
              </span>
              {!!Form::text('puertoDescargaEmbarque',null,['class'=>'form-control','onchange'=>'abrirModal("Ciudad", "nombreCiudad", "codigoAlternoCiudad", this, "9999")','placeholder'=>'', 'autocomplete' => 'off'])!!}
              {!!Form::hidden('Ciudad_idPuerto_Descarga', null, array('id' => 'Ciudad_idPuerto_Descarga')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('agenteCargaEmbarque', 'Agente de carga', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-cubes"></i>
              </span>
              {!!Form::text('agenteCargaEmbarque',null,['class'=>'form-control','onchange'=>'abrirModal("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "02")','placeholder'=>'', 'autocomplete' => 'off'])!!}
              {!!Form::hidden('Tercero_idAgenteCarga', null, array('id' => 'Tercero_idAgenteCarga')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('navieraEmbarque', 'Naviera', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-ship"></i>
              </span>
              {!!Form::text('navieraEmbarque',null,['class'=>'form-control','onchange'=>'abrirModal("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "02"); reenviarCorreoEmbarque();','placeholder'=>'', 'autocomplete' => 'off'])!!}
              {!!Form::hidden('Tercero_idNaviera', null, array('id' => 'Tercero_idNaviera')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('fechaRealEmbarque', 'Fecha real embarque', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::input('date','fechaRealEmbarque',null,['class'=>'form-control', 'placeholder'=>'', 'autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <br><br><br><br><br><br><br><br><br><br>

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('bodegaEmbarque', 'Bodega', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-check"></i>
              </span>
              {!! Form::checkbox('bodegaEmbarque', null, null, ['class' => 'form-control']) !!}
              {!!Form::hidden('bodegaCorreoEmbarque', null, array('id' => 'bodegaCorreoEmbarque')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-4" id='test'>
          {!!Form::label('otmEmbarque', 'OTM', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-check"></i>
              </span>
              {!! Form::checkbox('otmEmbarque', null, null, ['class' => 'form-control']) !!}
              {!!Form::hidden('otmCorreoEmbarque', null, array('id' => 'otmCorreoEmbarque')) !!}
            </div>
          </div>
        </div>

    <input type="hidden" id="token" value="{{csrf_token()}}"/>
    </div>

    <h4><center><b>Detalles del embarque</b></center></h4>
    <div class="panel-body">
      <div class="form-group" id='test'>
        <div class="col-sm-10">
          <div class="panel-body" style="width:1280px;">
            <div class="form-group" id='test'>
              <div class="col-sm-12">
                <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
                  <div style="overflow:auto; height:350px;">
                    <div style="width: 6345px; display: inline-block;">
                      <div class="col-md-1" style="width: 1000px;">&nbsp;</div>
                      <div class="col-md-1" style="width: 750px;"><center><b>Proforma</b></center></div>
                      <div class="col-md-1" style="width: 750px;"><center><b>Factura</b></center></div>
                      <div class="col-md-1" style="width: 3840px;">&nbsp;</div>
                      <div class="col-md-1" style="width: 40px; height: 42px; cursor:pointer;" onclick="abrirModalCompra();">
                        <span class="glyphicon glyphicon-plus"></span>
                      </div>
                      <div class="col-md-1" style="width: 60px;">Modal</div>
                      <div class="col-md-1" style="width: 300px;">Compra</div>
                      <div class="col-md-1" style="width: 300px;">Proveedor</div>
                      <div class="col-md-1" style="width: 200px;">PI</div>
                      <div class="col-md-1" style="width: 100px;">Delivery</div>
                      <div class="col-md-1" style="width: 150px;">Proforma</div>
                      <div class="col-md-1" style="width: 100px;">Volumen</div>
                      <div class="col-md-1" style="width: 150px;">Valor</div>
                      <div class="col-md-1" style="width: 150px;">Unidades</div>
                      <div class="col-md-1" style="width: 100px;">Peso</div>
                      <div class="col-md-1" style="width: 100px;">Bultos</div>
                      <div class="col-md-1" style="width: 150px;">Factura</div>
                      <div class="col-md-1" style="width: 100px;">Volumen</div>
                      <div class="col-md-1" style="width: 150px;">Valor</div>
                      <div class="col-md-1" style="width: 150px;">Unidades</div>
                      <div class="col-md-1" style="width: 100px;">Peso</div>
                      <div class="col-md-1" style="width: 100px;">Bultos</div>
                      <div class="col-md-1" style="width: 120px;">Reserva</div>
                      <div class="col-md-1" style="width: 120px;">Fecha Real</div>
                      <div class="col-md-1" style="width: 140px;">Fecha Maxima</div>
                      <div class="col-md-1" style="width: 120px;">Llegada ZF</div>
                      <div class="col-md-1" style="width: 300px;">Comprador</div>
                      <div class="col-md-1" style="width: 300px;">Evento</div>
                      <div class="col-md-1" style="width: 100px;">Dolar</div>
                      <div class="col-md-1" style="width: 190px;">Arribo a puerto estimado</div>
                      <div class="col-md-1" style="width: 150px;">Arribo a puerto</div>
                      <div class="col-md-1" style="width: 150px;">Soporte pago</div>
                      <div class="col-md-1" style="width: 300px;">Comprador/Vendedor</div>
                      <div class="col-md-1" style="width: 200px;">Cantidad contenedores</div>
                      <div class="col-md-1" style="width: 150px;">Tipo contenedor</div>
                      <div class="col-md-1" style="width: 150px;">N° contenedor</div>
                      <div class="col-md-1" style="width: 200px;">Doc. Transporte</div>
                      <div class="col-md-1" style="width: 200px;">N° courrier</div>
                      <div class="col-md-1" style="width: 100px;">Pagos</div>
                      <div class="col-md-1" style="width: 100px;">Originales</div>
                      <div class="col-md-1" style="width: 300px;">Descripcion</div>
                      <div class="col-md-1" style="width: 150px;">File</div>
                      <div class="col-md-1" style="width: 300px;">Observación</div>

                      <div id="contenedor_embarque">
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-md-4" id='test' style="display:inline-block">
                      {!!Form::label('volumenTotalEmbarque', 'Volumen: ', array('class' => 'col-sm-3 control-label')) !!}
                      <div class="col-md-8">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-expand"></i>
                          </span>
                          {!!Form::text('volumenTotalEmbarque',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-md-4" id='test' style="display:inline-block">
                      {!!Form::label('valorTotalEmbarque', 'Valor: ', array('class' => 'col-sm-3 control-label')) !!}
                      <div class="col-md-8">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-usd"></i>
                          </span>
                          {!!Form::text('valorTotalEmbarque',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-md-4" id='test' style="display:inline-block">
                      {!!Form::label('unidadTotalEmbarque', 'Unidades: ', array('class' => 'col-sm-3 control-label')) !!}
                      <div class="col-md-8">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-cubes"></i>
                          </span>
                          {!!Form::text('unidadTotalEmbarque',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-md-4" id='test' style="display:inline-block">
                      {!!Form::label('pesoTotalEmbarque', 'Peso: ', array('class' => 'col-sm-3 control-label')) !!}
                      <div class="col-md-8">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-sort-amount-desc"></i>
                          </span>
                          {!!Form::text('pesoTotalEmbarque',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-md-4" id='test' style="display:inline-block">
                      {!!Form::label('bultoTotalEmbarque', 'Bultos: ', array('class' => 'col-sm-3 control-label')) !!}
                      <div class="col-md-8">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-cube"></i>
                          </span>
                          {!!Form::text('bultoTotalEmbarque',null,['class'=>'form-control','readonly', 'placeholder'=>'', 'style'=>'width:150px; height:30px; text-align: right;'])!!}
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-md-4" id='test' style="display:inline-block">
                      <button class="btn btn-primary" type="button" onclick="calcularTotales();">Calcular</button>
                      {!!Form::button('Duplicar compras',["class"=>"btn btn-success", 'id'=>'duplicarCompraEmbarque',"onclick"=>'duplicarCompras();'])!!}
                    </div>
              </div>
            </div>
          </div>
        </div>
      </div>  
    </div>
    

    
  </fieldset>

      


  @if(isset($embarque))
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

<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID DE LOS CAMPOS TIPO LISTA -->
    <div id="ListaSelect" class="modal fade" role="dialog">
      <div class="modal-dialog" style="width:100%;">
        <!-- Modal content-->
        <div class="modal-content" style="width:1200px; left:100px;">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Seleccione un registro de la lista</h4>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="row">
                  <div class="container">
                      
                      
                      <table id="tlistaselectemb" name="tlistaselectemb" class="display table-bordered" width="100%">
                          <thead>
                              <tr class="btn-default active">

                                  <th style="width:10px;"><b>ID</b></th>
                                  <th style="width:10px;"><b>Nombre</b></th>
                                  <th style="width:10px;"><b>Codigo</b></th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr class="btn-default active">

                                  <th>ID</th>
                                  <th>Nombre</th>
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

<!-- ABRO EL MODAL Y DENTRO ESTA LA GRID DE COMPRAS -->
<div id="myModalCompra" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:100%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Compras</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:500px; " id="compra" name="compra" src=http://'.$_SERVER["HTTP_HOST"].'/compragridselect?idDocumento='.$idDocumentoImportacion.'></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>


<!-- ABRO EL MODAL Y DENTRO ESTA EL FORMULARIO DE EMBARQUE DETALLE -->
<div id="myModalEmbarque" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:100%;">

    <!-- Modal content-->
    <div style="height:550px; overflow:scroll;" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Embarque</h4>
      </div>
      <div class="modal-body">
          
        <div class="form-group col-md-6" id='test'>
          {!!Form::label('compraEmbarque', 'Compra', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::text('compraEmbarque',null,['class'=>'form-control','readonly','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('proveedorEmbarque', 'Proveedor', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::text('proveedorEmbarque',null,['class'=>'form-control','readonly','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('numeroCompraEmbarque', 'PI', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-file"></i>
              </span>
              {!!Form::text('numeroCompraEmbarque',null,['class'=>'form-control','readonly','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('deliveryEmbarque', 'Delivery', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('deliveryEmbarque',null,['class'=>'form-control','readonly','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('proformaEmbarque', 'Proforma', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-file-o"></i>
              </span>
              {!!Form::text('proformaEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('volumenEmbarque', 'Volumen', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-expand"></i>
              </span>
              {!!Form::text('volumenEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('valorEmbarque', 'Valor', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-cubes"></i>
              </span>
              {!!Form::text('valorEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('unidadEmbarque', 'Unidades', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-usd"></i>
              </span>
              {!!Form::text('unidadEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('pesoEmbarque', 'Peso', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-sort-amount-desc"></i>
              </span>
              {!!Form::text('pesoEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('bultoEmbarque', 'Bultos', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-cube"></i>
              </span>
              {!!Form::text('bultoEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('facturaEmbarque', 'Factura', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-file-o"></i>
              </span>
              {!!Form::text('facturaEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('volumenFactura', 'Volumen', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-expand"></i>
              </span>
              {!!Form::text('volumenFactura',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('valorFactura', 'Valor', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-cubes"></i>
              </span>
              {!!Form::text('valorFactura',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('unidadFactura', 'Unidades', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-usd"></i>
              </span>
              {!!Form::text('unidadFactura',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('pesoFactura', 'Peso', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-sort-amount-desc"></i>
              </span>
              {!!Form::text('pesoFactura',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('bultoFactura', 'Bultos', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-cube"></i>
              </span>
              {!!Form::text('bultoFactura',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaReservaE', 'Reserva', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaReservaE',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaRealE', 'Fecha Real', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaRealE',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaMaximaE', 'Fecha maxima', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaMaximaE',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaLlegadaZFEmbarque', 'Llegada ZF', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaLlegadaZFEmbarque',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('compradorEmbarque', 'Comprador', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::text('compradorEmbarque',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('eventoEmbarque', 'Evento', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-star-half-o"></i>
              </span>
              {!!Form::text('eventoEmbarque',null,['class'=>'form-control','readonly','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('dolarEmbarque', 'Dolar', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-usd"></i>
              </span>
              {!!Form::text('dolarEmbarque',null,['class'=>'form-control','autocomplete' => 'off', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('arriboPuertoEmbarque', 'Arribo puerto estimado', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('arriboPuertoEmbarque',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('arriboPuertoE', 'Arribo puerto', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('arriboPuertoE',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('soportePagoEmbarque', 'Soporte pago', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-check"></i>
              </span>
              {!! Form::checkbox('soportePagoEmbarque', null, null, ['class' => 'form-control']) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('compradorVendedorEmbarque', 'Comp/Vend', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::text('compradorVendedorEmbarque',null,['class'=>'form-control', 'readonly'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('cantidadContenedor', 'Cantidad contenedores', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-arrows"></i>
              </span>
              {!!Form::text('cantidadContenedor',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('tipoContenedor', 'Tipo contenedor', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-arrows"></i>
              </span>
              {!! Form::select('tipoContenedor', ['1x20' => '1x20','1x40' => '1x40','LCL' => 'LCL'],null,['class' => 'form-control']) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('contenedorEmbarque', 'N° Contenedor', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-cube"></i>
              </span>
              {!!Form::text('contenedorEmbarque',null,['class'=>'form-control', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('blEmbarque', 'BL', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bold"></i>
              </span>
              {!!Form::text('blEmbarque',null,['class'=>'form-control', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('courrierEmbarque', 'N° Courrier', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-tty"></i>
              </span>
              {!!Form::text('courrierEmbarque',null,['class'=>'form-control'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('pagoEmbarque', 'Pago', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-check"></i>
              </span>
              {!! Form::checkbox('pagoEmbarque', null, null, ['class' => 'form-control']) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('originalEmbarque', 'Originales', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-check"></i>
              </span>
              {!! Form::checkbox('originalEmbarque', null, null, ['class' => 'form-control']) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('descripcionEmbarque', 'Descripción', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
            {!!Form::textarea('descripcionEmbarque',null,['class'=>'form-control', 'onchange' => 'reenviarCorreoEmbarque();','style'=>'height:60px; width:350px;'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fileEmbarque', 'File', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
            {!!Form::text('fileEmbarque',null,['class'=>'form-control', 'onchange' => 'reenviarCorreoEmbarque();'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('observacionEmbarque', 'Observación', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
            {!!Form::textarea('observacionEmbarque',null,['class'=>'form-control', 'onchange' => 'reenviarCorreoEmbarque();','style'=>'height:60px; width:350px;'])!!}
            </div>
          </div>
        </div>

        {!!Form::hidden('numeroRegistroEmbarque', null, array('id' => 'numeroRegistroEmbarque')) !!}
        <button class="btn btn-primary" id="btnActualizar" name="btnActualizar" type="button" onclick="llenarRegistrosModal(document.getElementById('numeroRegistroEmbarque').value);">OK</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>

      </div>
    </div>
  </div>
</div>