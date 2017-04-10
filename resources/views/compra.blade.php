<?php 
$idDocumentoImportacion = $_GET['idDocumento'];

$accion = $_GET['accion'];

$docImportacion  = DB::Select('SELECT * from documentoimportacion where idDocumentoImportacion = '.$idDocumentoImportacion);

$importacion = get_object_vars($docImportacion[0]);

$readonly = '';
$onchange = '';

if ($importacion['SistemaInformacion_idSistemaInformacion'] > 0) 
{
  $readonly = 'readonly';
  $onchange = 'llenarMetadatos(this)';
}
else
{
  $readonly = '';
  $onchange = '';
}

?>

@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Compra <?php echo $importacion['nombreDocumentoImportacion'];?></center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/compra.js')!!}

	@if(isset($compra))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($compra,['route'=>['compra.destroy',$compra->idCompra],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($compra,['route'=>['compra.update',],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'compra.store','method'=>'POST'])!!}
	@endif

<div id='form-section' >

	<fieldset id="compra-form-fieldset">	
    <div id="padre" class="col-md-12">

    <?php 
    $readonlycompra = '';
      if ($accion == 'crear') 
      {
        echo '
        <label class= "col-sm-12 control-label">Versión</label> 
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-bars"></i>
            </span>
            <input id="numeroVersionInicialCompra" name="numeroVersionInicialCompra" style="height:30px; width:80px;" type="text" readonly="true" value="1">
        </div>
        <script>
          
        </script>';
      }
      else if ($accion == 'editar')
      {
        echo '
          <label class= "col-sm-12 control-label">Nueva Versión</label> 
          <div class="input-group">
              <span class="input-group-addon">
                  <i class="fa fa-bars"></i>
              </span>
              <select id="numeroVersionMaximaCompra" name= "numeroVersionMaximaCompra" style="height: 30px;" onchange="llenarMetadatosVersion(document.getElementById(\'DocumentoImportacion_idDocumentoImportacion\').value,this.value, document.getElementById(\'numeroCompra\').value);">
              </select>
          </div>';

        $version = DB::Select('SELECT 
            if(max(numeroVersionCompra) >= 1, numeroVersionCompra, 0) as numeroVersionCompra
        FROM compra c
            LEFT JOIN embarquedetalle ed
              ON  c.idCompra = ed.Compra_idCompra
        WHERE
            Compra_idCompra = '.$compra->idCompra);

        $versionModificar = get_object_vars($version[0]);
        if ($idDocumentoImportacion != 2) 
        $readonlycompra = 'readonly';


        if ($versionModificar["numeroVersionCompra"] == 0)  
        {  
          echo '
          <script>
            $(document).ready( function () {
              $("#idCompra").val(0);
            });
          </script>'; 
        }
      }
    ?>
    
    <br/> <br/>

		    <div class="form-group col-md-6" id='test'>
          {!!Form::label('nombreTemporadaCompra', 'Temporada', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::text('nombreTemporadaCompra',null,['class'=>'form-control','onchange'=>'abrirModal("Temporada", "nombreTemporada", "codigoAlternoTemporada", this, "9999")','placeholder'=>'','autocomplete' => 'off'])!!}
              {!!Form::hidden('Temporada_idTemporada', null, array('id' => 'Temporada_idTemporada')) !!}
              <span class="input-group-addon" style="cursor:pointer; background-color:#255986" title="Crear nueva temporada" onclick="crearTemporadaCompra();"><i class="fa fa-plus" style="color:white;"></i></span>
            </div>
          </div>
        </div>

        {!!Form::hidden('idCompra', null, array('id' => 'idCompra')) !!}
        {!!Form::hidden('numeroVersionCompra', null, array('id' => 'numeroVersionCompra')) !!}
        {!!Form::hidden('DocumentoImportacion_idDocumentoImportacion', $importacion["idDocumentoImportacion"], array('id' => 'DocumentoImportacion_idDocumentoImportacion')) !!}
        {!!Form::hidden('accion', $_GET["accion"], array('id' => 'accion')) !!}
        {!!Form::hidden('envioCorreoCompra', null, array('id' => 'envioCorreoCompra')) !!}

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaCompra', 'Fecha compra', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaCompra',null,['class'=>'form-control','placeholder'=>'',$readonly,'autocomplete' => 'off'])!!}
            </div>
          </div>
        </div> 

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('numeroCompra', 'PI', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon" style="background:#A9F5A9;">
                <i class="fa fa-file"></i>
              </span>
              {!!Form::text('numeroCompra',null,['class'=>'form-control', 'autocomplete' => 'off', $readonlycompra,'placeholder'=>'', 'onchange'=>$onchange])!!}
              {!!Form::hidden('Movimiento_idMovimiento', null, array('id' => 'Movimiento_idMovimiento')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('nombreProveedorCompra', 'Proveedor', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::text('nombreProveedorCompra',null,['class'=>'form-control','onchange'=>'abrirModalTercero("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "02")','placeholder'=>'','autocomplete' => 'off'])!!}
              {!!Form::hidden('Tercero_idProveedor', null, array('id' => 'Tercero_idProveedor')) !!}
              <!-- <span class="input-group-addon" style="cursor:pointer; background-color:#255986" title="Crear nuevo proveedor" onclick="crearProveedorCompra();"><i class="fa fa-plus" style="color:white;"></i></span> -->
            </div>
          </div>
        </div>


        <div class="form-group col-md-6" id='test'>
          {!!Form::label('formaPagoProveedorCompra', 'Pago proveedor', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-credit-card"></i>
              </span>
              {!!Form::text('formaPagoProveedorCompra',null,['class'=>'form-control','onchange'=>'abrirModal("FormaPago", "nombreFormaPago", "codigoAlternoFormaPago", this, "9999")','placeholder'=>'','autocomplete' => 'off'])!!}
              {!!Form::hidden('FormaPago_idFormaPago', null, array('id' => 'FormaPago_idFormaPago')) !!}
            </div>
          </div>
        </div>


        <div class="form-group col-md-6" id='test'>
          {!!Form::label('nombreClienteCompra', 'Cliente', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::text('nombreClienteCompra',null,['class'=>'form-control', 'onchange'=>'abrirModalTercero("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "01")','placeholder'=>'','autocomplete' => 'off'])!!}
              {!!Form::hidden('Tercero_idCliente', null, array('id' => 'Tercero_idCliente')) !!}
            </div>
          </div>
        </div>


        <div class="form-group col-md-6" id='test'>
          {!!Form::label('formaPagoClienteCompra', 'Pago cliente', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-credit-card"></i>
              </span>
              {!!Form::text('formaPagoClienteCompra',null,['class'=>'form-control',$readonly,'placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('eventoCompra', 'Evento', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-star-half-o"></i>
              </span>
              {!!Form::text('eventoCompra',null,['class'=>'form-control','placeholder'=>'','onchange'=>'abrirModal("Evento", "nombreEvento", "codigoAlternoEvento", this, "9999")','autocomplete' => 'off'])!!}
              <span class="input-group-addon" style="cursor:pointer; background-color:#255986" title="Crear nuevo evento" onclick="crearEventoCompra();"><i class="fa fa-plus" style="color:white;"></i></span>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('compradorVendedorCompra', 'Comp/Vend', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-users"></i>
              </span>
              {!!Form::text('compradorVendedorCompra',null,['class'=>'form-control','placeholder'=>'','onchange'=>'abrirModalTercero("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "03")','autocomplete' => 'off'])!!}
              {!!Form::hidden('Tercero_idVendedor', null, array('id' => 'Tercero_idVendedor')) !!}
            </div>
          </div>
        </div>


        <div class="form-group col-md-6" id='test'>
          {!!Form::label('valorCompra', 'Valor FOB', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-usd"></i>
              </span>
              {!!Form::text('valorCompra',null,['class'=>'form-control','autocomplete'=> 'off', 'id'=>'valorCompra'])!!}
            </div>
          </div>
        </div>


        <div class="form-group col-md-6" id='test'>
          {!!Form::label('cantidadCompra', 'Cantidad', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
              {!!Form::text('cantidadCompra',null,['class'=>'form-control','autocomplete'=> 'off'])!!}
              <span class="input-group-addon">
                <select id="unidadMedida" name="unidadMedida" style="height:20px;">
                  
                </select>
              </span>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('pesoCompra', 'Peso', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-sort-amount-desc"></i>
              </span>
              {!!Form::text('pesoCompra',null,['class'=>'form-control','autocomplete'=> 'off'])!!}
              <span class="input-group-addon">Kg</span>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('volumenCompra', 'Volumen', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-expand"></i>
              </span>
              {!!Form::text('volumenCompra',null,['class'=>'form-control','autocomplete'=> 'off'])!!}
              <span class="input-group-addon">m3</span>
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('bultoCompra', 'Bultos', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-cube"></i>
              </span>
              {!!Form::text('bultoCompra',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('nombreCiudadCompra', 'Puerto', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-anchor"></i>
              </span>
              {!!Form::text('nombreCiudadCompra',null,['class'=>'form-control','onchange'=>'abrirModal("Ciudad", "nombreCiudad", "codigoAlternoCiudad", this, "9999")','placeholder'=>'','autocomplete' => 'off'])!!}
              {!!Form::hidden('Ciudad_idPuerto', null, array('id' => 'Ciudad_idPuerto')) !!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaDeliveryCompra', 'Delivery', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::input('date','fechaDeliveryCompra',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaForwardCompra', 'Fecha forward', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::input('date','fechaForwardCompra',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('valorForwardCompra', 'Forward', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-usd"></i>
              </span>
              {!!Form::text('valorForwardCompra',null,['class'=>'form-control','autocomplete'=> 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('diaPagoClienteCompra', 'Días pago cliente', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
              {!!Form::text('diaPagoClienteCompra',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('tiempoBodegaCompra', 'Tiempo de permanencia', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o"></i>
              </span>
              {!!Form::text('tiempoBodegaCompra',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaMaximaDespachoCompra', 'Fecha máxima despacho', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaMaximaDespachoCompra',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

      <div class="form-group col-md-12" id='test'>
          {!!Form::label('observacionCompra', 'Observaciones', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-md-12">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
        {!!Form::textarea('observacionCompra',null,['class'=>'form-control','style'=>'height:100px','autocomplete' => 'off'])!!}
            </div>
          </div>
      </div>

      {!!Form::hidden('estadoCompra', null, array('id' => 'estadoCompra')) !!}

    <input type="hidden" id="token" value="{{csrf_token()}}"/>
    </div>

    

    
  </fieldset>

  <script type="text/javascript">
  listarVersiones(document.getElementById('DocumentoImportacion_idDocumentoImportacion').value, $('#numeroCompra').val());

  $(document).ready( function () {
      // $("#idCompra").val(0);
      listarUnidadMedida('<?php echo (isset($compra->codigoUnidadMedidaCompra) ? $compra->codigoUnidadMedidaCompra : "" ) ?>');
    });
</script>
	@if(isset($compra))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary", 'id'=>'Modificar'])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
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
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- ABRO EL MODAL Y DENTRO DE EL ESTA LA GRID -->
    <div id="ListaSelect" class="modal fade" role="dialog">
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
                      
                      
                      <table id="tlistaselect" name="tlistaselect" class="display table-bordered" width="100%">
                          <thead>
                              <tr class="btn-primary active">

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
<!-- ABRO EL MODAL PARA CREAR LAS TEMPORADAS EN SAYA -->
    <div id="modalTemporada" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="width:1200px; left:-300px">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Creación de temporadas</h4>
          </div>
            <div class="form-group col-md-6" id='test'>
              {!!Form::label('codigoAlternoTemporadaSAYA', 'Código', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-pencil-square-o"></i>
                  </span>
                  {!!Form::text('codigoAlternoTemporadaSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off','onchange'=>'validarCodigoAlterno(this.value, "codigoAlternoTemporada", "Temporada")'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('nombreTemporadaSAYA', 'Temporada', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-pencil-square-o"></i>
                  </span>
                  {!!Form::text('nombreTemporadaSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('fechaInicialTemporadaSAYA', 'Fecha Inicial', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!!Form::text('fechaInicialTemporadaSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('fechaFinalTemporadaSAYA', 'Fecha Final', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!!Form::text('fechaFinalTemporadaSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('porcentajeToleranciaTemporada', 'Tolerancia', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i>%</i>
                  </span>
                  {!!Form::text('porcentajeToleranciaTemporada',0,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="guardarTemporada(
            $('#codigoAlternoTemporadaSAYA').val(), 
            $('#nombreTemporadaSAYA').val(), 
            $('#fechaInicialTemporadaSAYA').val(), 
            $('#fechaFinalTemporadaSAYA').val(),
            $('#porcentajeToleranciaTemporada').val());">Crear</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ABRO EL MODAL PARA CREAR LOS EVENTOS EN SAYA -->
    <div id="modalEvento" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="width:1200px; left:-300px">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Creación de eventos</h4>
          </div>

            <div class="form-group col-md-6">
              {!!Form::label('Tercero_idEvento', 'Cliente/Proveedor', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
              <div class="col-sm-8 col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-list"></i>
                  </span>
                  {!!Form::select('Tercero_idEvento',$tercero, (isset($compra) ? $compra->evento : 0),["class" => "select form-control"])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('codigoAlternoEventoSAYA', 'Código', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-pencil-square-o"></i>
                  </span>
                  {!!Form::text('codigoAlternoEventoSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off','onchange'=>'validarCodigoAlterno(this.value, "codigoAlternoEvento", "Evento")'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('nombreEventoSAYA', 'Evento', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-pencil-square-o"></i>
                  </span>
                  {!!Form::text('nombreEventoSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('fechaInicialEventoSAYA', 'Fecha Inicial', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!!Form::text('fechaInicialEventoSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('fechaFinalEventoSAYA', 'Fecha Final', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!!Form::text('fechaFinalEventoSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('diaEntregaAnticipadaEvento', 'Días entrega anticipada', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-pencil-square-o"></i>
                  </span>
                  {!!Form::text('diaEntregaAnticipadaEvento',0,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="guardarEvento(
            $('#Tercero_idEvento option:selected').val(),
            $('#codigoAlternoEventoSAYA').val(), 
            $('#nombreEventoSAYA').val(), 
            $('#fechaInicialEventoSAYA').val(), 
            $('#fechaFinalEventoSAYA').val(),
            $('#diaEntregaAnticipadaEvento').val());">Crear</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ABRO EL MODAL PARA CREAR LOS TERCEROS EN SAYA -->
    <div id="modalTercero" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="width:1200px; left:-300px">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Creación de proveedores</h4>
          </div>

            <div class="form-group col-md-6">
              {!!Form::label('TipoIdentificacion_idTipoIdentificacion', 'Tipo', array('class' => 'col-sm-4 col-md-2 control-label')) !!}
              <div class="col-sm-8 col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-list"></i>
                  </span>
                  {!!Form::select('TipoIdentificacion_idTipoIdentificacion',$tipodocumento, (isset($compra) ? $compra->tipodocumento : 0),["class" => "select form-control"])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('documentoTerceroSAYA', 'Documento', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-pencil-square-o"></i>
                  </span>
                  {!!Form::text('documentoTerceroSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off','onchange'=>'validarCodigoAlterno(this.value, "documentoTercero", "Tercero"); calcularDv(this.value)'])!!}
                  <span class="input-group-addon">
                    {!!Form::text('digitoVerificacionSAYA',0,['style' => 'width:25px;','readonly','id'=>'digitoVerificacionSAYA', 'title'=>'Digito de verificación'])!!}
                  </span>
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('nombreATerceroSAYA', 'Primer nombre', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  {!!Form::text('nombreATerceroSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                  <span class="input-group-addon"><i>*</i></span>
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('nombreBTerceroSAYA', 'Segundo nombre', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  {!!Form::text('nombreBTerceroSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('apellidoATerceroSAYA', 'Primer apellido', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  {!!Form::text('apellidoATerceroSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                  <span class="input-group-addon"><i>*</i></span>
                </div>
              </div>
            </div>

            <div class="form-group col-md-6" id='test'>
              {!!Form::label('apellidoBTerceroSAYA', 'Segundo apellido', array('class' => 'col-sm-2 control-label')) !!}
              <div class="col-md-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  {!!Form::text('apellidoBTerceroSAYA',null,['class'=>'form-control', 'placeholder'=>'','autocomplete' => 'off'])!!}
                </div>
              </div>
            </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="guardarProveedor(
            $('#TipoIdentificacion_idTipoIdentificacion option:selected').val(),
            $('#documentoTerceroSAYA').val(),
            $('#digitoVerificacionSAYA').val(), 
            $('#nombreATerceroSAYA').val(), 
            $('#nombreBTerceroSAYA').val(), 
            $('#apellidoATerceroSAYA').val(),
            $('#apellidoBTerceroSAYA').val());">Crear</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>