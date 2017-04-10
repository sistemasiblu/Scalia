@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center>Mensajería</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/mensajeria.js')!!}

	@if(isset($mensajeria))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($mensajeria,['route'=>['mensajeria.destroy',$mensajeria->idMensajeria],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($mensajeria,['route'=>['mensajeria.update',$mensajeria->idMensajeria],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'mensajeria.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="mensajeria-form-fieldset">	

		    <div class="form-group col-md-6" id='test'>
          {!!Form::label('tipoCorrespondenciaMensajeria', 'Tipo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!! Form::select('tipoCorrespondenciaMensajeria', ['Recibo' => 'Recibo','Envio' => 'Envío'],null,['class' => 'form-control','placeholder'=>'Seleccione']) !!}
              {!!Form::hidden('idMensajeria', null, array('id' => 'idMensajeria')) !!}
            </div>
          </div>
        </div>

        <!-- <div class="form-group col-md-6" id='test'>
          {!!Form::label('tipoEnvioMensajeria', 'Tipo de envío', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span> -->
              {!!Form::hidden('tipoEnvioMensajeria', (isset($mensajeria) ? $mensajeria->tipoEnvioMensajeria : $_GET["tipo"]), array('id' => 'tipoEnvioMensajeria')) !!}
          <!--   </div>
          </div>
        </div> -->


      <div class="form-group col-md-6" id='test'>
        {!!Form::label('prioridadMensajeria', 'Prioridad', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-sort-amount-desc "></i>
            </span>
            {!! Form::select('prioridadMensajeria', ['Alta' => 'Alta','Media' => 'Media','Baja' => 'Baja'],null,['class' => 'form-control','placeholder'=>'Seleccione']) !!}
          </div>
        </div>
      </div>
  

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('Radicado_idRadicado', 'N° Radicado', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-barcode "></i>
            </span>
            {!!Form::select('Radicado_idRadicado',$radicado, (isset($mensajeria) ? $mensajeria->Radicado_idRadicado : 0),["class" => "form-control chosen-select", "placeholder" =>"Seleccione el numero de radicado"])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('fechaLimiteMensajeria', 'Fecha limite', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar "></i>
            </span>
            {!!Form::text('fechaLimiteMensajeria',null,['class'=>'form-control', 'placeholder'=>'', 'autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>


      <div class="form-group col-md-12" id='test'>
        {!!Form::label('descripcionMensajeria', 'Descripción', array('class' => 'col-sm-1 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o "></i>
            </span>
            {!!Form::textarea('descripcionMensajeria',null,['class'=> 'form-control','placeholder'=>''])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('destinatarioMensajeria', 'Destinatario', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user "></i>
            </span>
            {!!Form::text('destinatarioMensajeria',null,['class'=>'form-control','placeholder'=>'','onchange'=>'abrirModalTercero("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "01")', 'autocomplete' => 'off'])!!}
            {!!Form::hidden('Tercero_idDestinatario', null, array('id' => 'Tercero_idDestinatario')) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('seccionEntregaMensajeria', 'Seccion', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-bank "></i>
            </span>
            {!!Form::text('seccionEntregaMensajeria',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-12" id='test'>
        {!!Form::label('direccionEntregaMensajeria', 'Dirección', array('class' => 'col-sm-1 control-label')) !!}
        <div class="col-sm-12">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-road "></i>
            </span>
            {!!Form::text('direccionEntregaMensajeria',null,['class'=>'form-control','placeholder'=>''])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('estadoEntregaMensajeria', 'Estado', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-tasks "></i>
            </span>
            {!! Form::select('estadoEntregaMensajeria', ['Sin_Asignar' => 'Sin Asignar','En_Proceso' => 'En Proceso','Recibida' => 'Recibida','Rechazada' => 'Rechazada'],null,['class' => 'form-control']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('transportadorMensajeria', 'Transportador', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user "></i>
            </span>
            {!!Form::text('transportadorMensajeria',null,['class'=>'form-control','placeholder'=>'','onchange'=>'abrirModalTercero("Tercero", "nombre1Tercero", "codigoAlterno1Tercero", this, "04")', 'autocomplete' => 'off'])!!}
            {!!Form::hidden('Tercero_idTransportador', null, array('id' => 'Tercero_idTransportador')) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('numeroGuiaMensajeria', 'Guia', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-barcode "></i>
            </span>
            {!!Form::text('numeroGuiaMensajeria',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('fechaEnvioMensajeria', 'Fecha de envío', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar "></i>
            </span>
            {!!Form::text('fechaEnvioMensajeria',null,['class'=>'form-control', 'placeholder'=>'', 'autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('fechaEntregaMensajeria', 'Fecha real entrega', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar "></i>
            </span>
            {!!Form::text('fechaEntregaMensajeria',null,['class'=>'form-control', 'placeholder'=>'', 'autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-12" id='test'>
        {!!Form::label('observacionMensajeria', 'Observación', array('class' => 'col-sm-1 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o "></i>
            </span>
            {!!Form::textarea('observacionMensajeria',null,['class'=> 'form-control','placeholder'=>''])!!}
          </div>
        </div>
      </div>

    </fieldset>
	@if(isset($mensajeria))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 	@endif

	{!! Form::close() !!}
</div>

<script>
  CKEDITOR.replace(('descripcionMensajeria'), {
      fullPage: true,
      allowedContent: true
    });  

  CKEDITOR.replace(('observacionMensajeria'), {
      fullPage: true,
      allowedContent: true
    });  
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