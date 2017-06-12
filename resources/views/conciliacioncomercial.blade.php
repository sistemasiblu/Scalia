<?php

  $fecha = Carbon\Carbon::now();
  $idUsuario = \Session::get("idUsuario");
  $usuario = \Session::get("nombreUsuario");

  $arrayDocumentos = array();

?>
@extends('layouts.vista')

@section('titulo')<h3 id="titulo"><center>Conciliacion Comercial - Contable</center></h3>@stop

@section('content')
  @include('alerts.request')

{!!Html::script('js/conciliacioncomercial.js')!!}

{!!Html::style('css/loading.css'); !!}<!--clase de objeto de carga-->

<script>
  var arrayDocumentos = "<?php echo (isset($conciliacioncomercial) ? ($conciliacioncomercial->Documento_idDocumento) : '');?>";  
  arrayDocumentos = arrayDocumentos.split(",");

  // console.log(arrayDocumentos);
  // var DocumentoConciliacionCartera = '<?php echo (isset($cartera) ? json_encode($cartera) : "");?>';
  
  // DocumentoConciliacionComercial = (DocumentoConciliacionComercial != '' ? JSON.parse(DocumentoConciliacionComercial) : '');
  // DocumentoConciliacionCartera = (DocumentoConciliacionCartera != '' ? JSON.parse(DocumentoConciliacionCartera) : '');

</script>


	@if(isset($conciliacioncomercial))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($conciliacioncomercial,['route'=>['conciliacioncomercial.destroy',$conciliacioncomercial->idConciliacionComercial],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($conciliacioncomercial,['route'=>['conciliacioncomercial.update',$conciliacioncomercial->idConciliacionComercial],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'conciliacioncomercial.store','method'=>'POST'])!!}
	@endif

<div id='form-section' >
	<fieldset id="conciliacioncomercial-form-fieldset">	
		<div class="form-group" id='test'>

      <!-- <div class="container"> -->
      <div class="row">
        <div class="col-md-6">
            {!! Form::hidden('idConciliacionComercial', null, array('id' => 'idConciliacionComercial')) !!}  
            <input type="hidden" id="token" value="{{csrf_token()}}"/>
            {!! Form::label('Documento_idDocumento', 'Documento:', array('class' => 'col-sm-2 col-md-2 control-label')) !!}
            <div class="col-sm-8 col-md-8">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-barcode"></i>
                </span>
                {!!Form::select('Documento_idDocumento',$documento, null,['id'=>'Documento_idDocumento','class' => 'chosen-select form-control','style'=>'padding-left:2px;','multiple'=>'multiple'])!!}
              </div>
            </div>
        </div>
      </div>

      <br>
      <div class="row">
        <div class="col-md-6">
          {!!Form::label('fechaInicialConciliacionComercial', 'Fecha desde:', array('class' => 'col-sm-2 col-md-2 control-label')) !!}
          <div class="col-sm-8 col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaInicialConciliacionComercial',(isset($conciliacioncomercial) ? $conciliacioncomercial->fechaInicialConciliacionComercial : null),['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>

        <div class="col-md-6">
          {!!Form::label('fechaFinalConciliacionComercial', 'Fecha hasta:', array('class' => 'col-sm-2 col-md-2 control-label')) !!}
          <div class="col-sm-8 col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaFinalConciliacionComercial',(isset($conciliacioncomercial) ? $conciliacioncomercial->fechaFinalConciliacionComercial : null),['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>
      </div>

      <br>
      <div class="row">
        <div class="col-md-6">
          {!!Form::label('fechaElaboracionConciliacionComercial', 'Fecha Elaboracion:', array('class' => 'col-sm-2 col-md-2 control-label')) !!}
          <div class="col-sm-8 col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!!Form::text('fechaElaboracionConciliacionComercial', (isset($conciliacioncomercial) ? $conciliacioncomercial->fechaElaboracionConciliacionComercial : $fecha),['readonly'=>'readonly', 'class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
            </div>
          </div>
        </div>
      
        <div class="col-md-6">
          {!! Form::hidden('Users_idCrea', (isset($conciliacioncomercial) ? $conciliacioncomercial->Users_idCrea : $idUsuario), array('id' => 'Users_idCrea')) !!} 
          {!!Form::label('nameUsers', 'Usuario:', array('class' => 'col-sm-2 col-md-2 control-label')) !!}
          <div class="col-sm-8 col-md-8">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('nameUsers', (isset($users) ? $users[0]['name'] : $usuario),['readonly'=>'readonly','class'=>'form-control','id'=>'nameUsers']) !!}  
            </div>
          </div>
        </div>
      </div>

      <center>
        <div class="loader" style="display:none;position:fixed;z-index:1001;margin-left: 45%;">
          <div class="circle"></div>
          <div class="circle1"></div>
        </div> 
      </center>
      </div>

    <div id="resultadoConciliacion" name="resultadoConciliacion">      
    </div>

  </fieldset>

  <br>
	@if(isset($conciliacioncomercial))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::button('Eliminar',["id"=>"btnGuardar", "name"=>"btnGuardar", "class"=>"btn btn-primary", "onclick" => "validarProceso();"])!!}
  		@else
   			{!!Form::button('Consultar',["id"=>"btnGuardar", "name"=>"btnGuardar", "class"=>"btn btn-primary", 'onclick' => 'validarProceso();'])!!}
  		@endif
 	@else
  		{!!Form::button('Conciliar',["id"=>"btnGuardar", "name"=>"btnGuardar", "class"=>"btn btn-primary", 'onclick' => 'validarProceso();'])!!}
 	@endif

	{!! Form::close() !!}
</div>
@stop
<!-- Modal Resultado Documento-->
<div id="ModalResultadoDocumento" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 90%;height: 100%">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header btn-default active" style="border-radius: 3px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class="glyphicon glyphicon-info-sign"></span>&nbsp; Información</h4>
            </div>
            <div class="modal-body" style="height:500px;">
                <div class="container" style="width: 100%;height: 100%;overflow-y:scroll;">
                    <div id="resultadoDocumento" name="resultadoDocumento">
                        
                    </div>                                               
                </div> 
            </div>
            <div class="modal-footer btn-default active" style="border-radius: 3px; text-align:center;">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal --> 
<!-- Modal Resultado Movimiento-->
<div id="ModalResultadoMovimiento" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 90%;height: 100%">
        <!-- Modal content-->
        <div class="modal-content" style="width: 100%;">
            <div class="modal-header btn-default active" style="border-radius: 3px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class="glyphicon glyphicon-info-sign"></span>&nbsp; Información</h4>
            </div>
            <div class="modal-body" style="height:500px;">
                <div class="container" style="width: 100%;height: 100%;overflow-y:scroll;">
                    <div id="resultadoMovimiento" name="resultadoMovimiento">
                        
                    </div>                                               
                </div> 
            </div>
            <div class="modal-footer btn-default active" style="border-radius: 3px; text-align:center;">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<!--Fin Modal --> 