@extends('layouts.grid')

@section('titulo')<h3 id="titulo"><center>Parámetros de Conciliación Contable</center></h3>@stop

@section('content')
  @include('alerts.request')

{!!Html::script('js/documentoconciliacion.js')!!}
<script>
  var DocumentoConciliacionComercial = '<?php echo (isset($comercial) ? json_encode($comercial) : "");?>';  
  var DocumentoConciliacionCartera = '<?php echo (isset($cartera) ? json_encode($cartera) : "");?>';
  
  DocumentoConciliacionComercial = (DocumentoConciliacionComercial != '' ? JSON.parse(DocumentoConciliacionComercial) : '');
  DocumentoConciliacionCartera = (DocumentoConciliacionCartera != '' ? JSON.parse(DocumentoConciliacionCartera) : '');

</script>


	@if(isset($documentoconciliacion))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($documentoconciliacion,['route'=>['documentoconciliacion.destroy',$documentoconciliacion->idDocumentoConciliacion],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($documentoconciliacion,['route'=>['documentoconciliacion.update',$documentoconciliacion->idDocumentoConciliacion],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'documentoconciliacion.store','method'=>'POST'])!!}
	@endif

<div id='form-section' >
	<fieldset id="documentoconciliacion-form-fieldset">	
		<div class="form-group" id='test'>
          {!! Form::label('Documento_idDocumento', 'Documento', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::select('Documento_idDocumento',$documento, @$documentoconciliacion->Documento_idDocumento,['id'=>'Documento_idDocumento','class' => 'chosen-select form-control','style'=>'padding-left:2px;','placeholder'=>'Seleccione'])!!}

              {!! Form::hidden('idDocumentoConciliacion', null, array('id' => 'idDocumentoConciliacion')) !!}
              {!! Form::hidden('eliminarDocumentoConciliacionComercial', null, array('id' => 'eliminarDocumentoConciliacionComercial')) !!}
              {!! Form::hidden('eliminarDocumentoConciliacionCartera', null, array('id' => 'eliminarDocumentoConciliacionCartera')) !!}
              
            </div>
          </div>
        </div>


    </fieldset>


    <div class="panel-group" id="accordion">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
            Comercial</a>
          </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse in">
          <div class="panel-body">
            <div class="form-group" id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                    <div class="col-md-1" style="width: 40px;height:41px;" onclick="abrirModalValor('comercial');">
                      <span class="glyphicon glyphicon-plus"></span>
                    </div>
                    <div class="col-md-1" style="width: 20%;">Valor</div>
                    <div class="col-md-1" style="width: 36%;">Cuentas LOCAL</div>
                    <div class="col-md-1" style="width: 36%;">Cuentas NIIF</div>
                    <div id="contenedor_comercial">
                    </div>
                </div>
              </div>
            </div>  
          </div> 
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
            Cartera</a>
          </h4>
        </div>
        <div id="collapse2" class="panel-collapse collapse">
          <div class="panel-body">
            <div class="form-group" id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                    <div class="col-md-1" style="width: 40px;height:41px;" onclick="abrirModalValor('cartera');">
                      <span class="glyphicon glyphicon-plus"></span>
                    </div>
                    <div class="col-md-1" style="width: 20%;">Tipo de Cartera</div>
                    <div class="col-md-1" style="width: 36%;">Cuentas LOCAL</div>
                    <div class="col-md-1" style="width: 36%;">Cuentas NIIF</div>
                    <div id="contenedor_cartera">
                    </div>
                </div>
              </div>
            </div>  
          </div> 
        </div>
      </div>
      <!-- <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
            Créditos</a>
          </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse">
          <div class="panel-body">
            <div class="form-group" id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                    <div class="col-md-1" style="width: 40px;height:41px;" onclick="abrirModalValor();">
                      <span class="glyphicon glyphicon-plus"></span>
                    </div>
                    <div class="col-md-1" style="width: 20%;">Concepto Comercial</div>
                    <div class="col-md-1" style="width: 36%;">Cuentas LOCAL</div>
                    <div class="col-md-1" style="width: 36%;">Cuentas NIIF</div>
                    <div id="contenedor_comercial">
                    </div>
                </div>
              </div>
            </div>  
          </div> 
        </div>
      </div>-->
    </div>


    


    <br>
	@if(isset($documentoconciliacion))
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
@stop

<div id="ModalValor" style="display:none;" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Conceptos</h4>
      </div>
      <div class="modal-body">
      <?php 
       //echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/valorconciliaciongridselect"></iframe>'
      ?>
              <div class="container">
            <div class="row">
                <div class="container">
                    <div id="divTabla" name="divTabla">
                      <table id="tvalorSelect" name="tvalorSelect" class="display table-bordered" width="100%">
                          <thead>
                              <tr class="btn-default active">

                                  <th><b>ID</b></th>
                                  <th><b>Valor</b></th>        
                              </tr>
                          </thead>
                          <tfoot>
                              <tr class="btn-default active">

                                  <th>ID</th>
                                  <th>Valor</th>                             
                              </tr>
                          </tfoot>
                      </table>
                    </div>
                    <div class="modal-footer">
                        <button id="botonCampo" name="botonCampo" type="button" class="btn btn-primary" >Seleccionar</button>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>
