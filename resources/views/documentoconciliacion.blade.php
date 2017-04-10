@extends('layouts.vista')

@section('titulo')<h3 id="titulo"><center>Parámetros de Conciliación Contable</center></h3>@stop

@section('content')
  @include('alerts.request')

{!!Html::script('js/documentoconciliacion.js')!!}

<script>
  var DocumentoConciliacionComercial = '<?php echo (isset($documentoconciliacion) ? json_encode($documentoconciliacion->DocumentoConciliacionComercial) : "");?>';
  DocumentoConciliacionComercial = (DocumentoConciliacionComercial != '' ? JSON.parse(DocumentoConciliacionComercial) : '');
  
  var valorDetalle = [0,0,'','',''];

  
  $(document).ready(function(){
    
    comercial = new Atributos('comercial','contenedor_comercial','comercial_');
    
    comercial.altura = '36px;';
    comercial.campoid = 'idDocumentoConciliacionComercial';
    comercial.campoEliminacion = 'eliminarOperacion';

    comercial.campos = ['idDocumentoConciliacionComercial', 'ValorConciliacion_idValorConciliacion', 'nombreValorConciliacion', 'cuentasLocalDocumentoConciliacionComercial', 'cuentasNiifDocumentoConciliacionComercial'];
    comercial.etiqueta = ['input','input','input','input','input'];
    comercial.tipo = ['hidden','hidden','text','text','text'];
    comercial.estilo = ['','','width: 400px;height:35px;','width: 500px;height:35px;','width: 500px;height:35px;'];
    comercial.clase = ['','','','',''];
    comercial.sololectura = [false,false,false,false,false];
    
    for(var j=0, k = DocumentoConciliacionComercial.length; j < k; j++)
    {
        comercial.agregarCampos(JSON.stringify(DocumentoConciliacionComercial[j]),'L');
    }

   
  });
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
              {!! Form::hidden('eliminarDocumentoComercial', null, array('id' => 'eliminarDocumentoComercial')) !!}
              
            </div>
          </div>
        </div>


    </fieldset>


    <div class="panel-group" id="accordion">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
            Conmercial</a>
          </h4>
        </div>
        <div id="collapse1" class="panel-collapse collapse in">
          <div class="panel-body">
            <div class="form-group" id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                    <div class="col-md-1" style="width: 40px;" onclick="abrirModalValor();">
                      <span class="glyphicon glyphicon-plus"></span>
                    </div>
                    <div class="col-md-1" style="width: 400px;">Valor</div>
                    <div class="col-md-1" style="width: 500px;">Cuentas LOCAL</div>
                    <div class="col-md-1" style="width: 500px;">Cuentas NIIF</div>
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
          <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
          sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
          minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
          commodo consequat.</div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
            Créditos</a>
          </h4>
        </div>
        <div id="collapse3" class="panel-collapse collapse">
          <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
          sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad
          minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
          commodo consequat.</div>
        </div>
      </div>
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

<div id="ModalValor" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:70%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Conceptos</h4>
      </div>
      <div class="modal-body">
      <?php 
        echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/valorconciliaciongridselect"></iframe>'
      ?>
      </div>
    </div>
  </div>
</div>
