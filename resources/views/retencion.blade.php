@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Tablas de Retenci&oacute;n Documental</center></h3>@stop

@section('content')
@include('alerts.request')

<!-- {!!Html::script('js/retencion.js')!!} -->

<script>

    var idDependencia = '<?php echo isset($idDependencia) ? $idDependencia : "";?>';
    var nombreDependencia = '<?php echo isset($nombreDependencia) ? $nombreDependencia : "";?>';
    var idSerie = '<?php echo isset($idSerie) ? $idSerie : "";?>';
    var nombreSerie = '<?php echo isset($nombreSerie) ? $nombreSerie : "";?>';
    var idSubSerie = '<?php echo isset($idSubSerie) ? $idSubSerie : "";?>';
    var nombreSubSerie = '<?php echo isset($nombreSubSerie) ? $nombreSubSerie : "";?>';
    var idDocumento = '<?php echo isset($idDocumento) ? $idDocumento : "";?>';
    var nombreDocumento = '<?php echo isset($nombreDocumento) ? $nombreDocumento : "";?>';
    valorSoporte =  Array("Papel", "Electrónico");
    nombreSoporte =  Array("Papel", "Electrónico");
    valorDisposicionFinal =  Array("E","S", "C");
    nombreDisposicionFinal =  Array("Eliminación","Selección", "Conservación total");

    var dependencia = [JSON.parse(idDependencia), JSON.parse(nombreDependencia)];
    var serie = [JSON.parse(idSerie), JSON.parse(nombreSerie)];
    var subserie = [JSON.parse(idSubSerie), JSON.parse(nombreSubSerie)];
    var documento = [JSON.parse(idDocumento), JSON.parse(nombreDocumento)];
    var soporte = [valorSoporte,nombreSoporte];
    var disposicion = [valorDisposicionFinal,nombreDisposicionFinal];

    var eventochange1 = ['onchange','buscarDependencia(this.value);'];
    var eventochange2 = ['onchange','buscarSubSerie(this.value);'];


    var retenciones = '<?php echo (isset($retencion) ? json_encode($retencion->Retenciondocumental) : "");?>';
    retenciones = (retenciones != '' ? JSON.parse(retenciones) : '');
    var valorRetencion = ['','','','',0,0,'','','','',0];

    $(document).ready(function(){ 

      retencion = new Atributos('retencion','contenedor_retencion','retencion_');

      retencion.altura = '35px';
      retencion.campoid = 'idRetencionDocumental';
      retencion.campoEliminacion = 'eliminarRetencionDocumental';

      retencion.campos   = ['Dependencia_idDependencia', 'Serie_idSerie','SubSerie_idSubSerie','Documento_idDocumento','retencionGestionRetencionDocumental','retencionCentralRetencionDocumental','soporteRetencionDocumental','disposicionFinalRetencionDocumental','microfilmRetencionDocumental','procedimientoRetencionDocumental', 'idRetencionDocumental'];
      retencion.etiqueta = ['select', 'select','select','select','input','input','select','select','checkbox','input', 'input'];
      retencion.tipo     = ['', '','','','text','text','','','checkbox','text', 'hidden'];
      retencion.estilo   = ['width: 110px;height:35px;;','width: 90px;height:35px;','width: 100px;height:35px;','width: 160px;height:35px;','width: 65px;height:35px;','width: 65px;height:35px;','width: 80px;height:35px;','width: 150px;height:35px;','width: 95px;height:30px;display:inline-block;','width: 300px;height:35px;', ''];
      retencion.clase    = ['chosen-select ','chosen-select ','chosen-select ','chosen-select ','','','chosen-select ','chosen-select ','','', ''];
      retencion.opciones = [dependencia, serie, subserie, documento, '', '', soporte, disposicion, '', '', '']      
      // retencion.nombreDependencia =  JSON.parse(nombreDependencia);
      // retencion.idDependencia =  JSON.parse(idDependencia);
      // retencion.nombreSerie =  JSON.parse(nombreSerie);
      // retencion.idSerie =  JSON.parse(idSerie);
      // retencion.nombreSubSerie =  JSON.parse(nombreSubSerie);
      // retencion.idSubSerie =  JSON.parse(idSubSerie);
      // retencion.nombreDocumento =  JSON.parse(nombreDocumento);
      // retencion.idDocumento =  JSON.parse(idDocumento);
      retencion.sololectura = [false,false,false,false,false,false,false,false,false,false,false];
      retencion.funciones = [eventochange1, eventochange2, '', '', '', '', '', '', '', '', ''];
      
      for(var j=0, k = retenciones.length; j < k; j++)
      {
        retencion.agregarCampos(JSON.stringify(retenciones[j]),'L');
      }

    });

  </script>


   @if(isset($retencion))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($retencion,['route'=>['retencion.destroy',$retencion->idRetencion],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($retencion,['route'=>['retencion.update',$retencion->idRetencion],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'retencion.store','method'=>'POST'])!!}
  @endif


<div id='form-section' >

  <fieldset id="retencion-form-fieldset">
    <div class="form-group" id='test'>
          {!!Form::label('anioRetencion', 'A&ntilde;o', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar "></i>
              </span>
              {!!Form::text('anioRetencion',null,['class'=>'form-control','placeholder'=>'Ingresa el a&ntilde;o de la retencion'])!!}
              {!!Form::hidden('idRetencion', null, array('id' => 'idRetencion')) !!}
              {!!Form::hidden('eliminarRetencionDocumental', null, array('id' => 'eliminarRetencionDocumental')) !!}
                 <input type="hidden" id="token" value="{{csrf_token()}}"/>
            </div>
          </div>
        </div>
</br>
        <div class="panel-body">
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
              <div style="width: 1310px; display: inline-block; ">
              <div class="col-md-1" style="width: 500px;">&nbsp;</div>
              <div class="col-md-1" style="width: 130px;"><center>Ciclo Vital</center></div>
              <div class="col-md-1" style="width: 670px;">&nbsp;</div>
                <div class="col-md-1" style="width: 40px; cursor: pointer;" onclick="retencion.agregarCampos(valorRetencion,'A')">
                  <span class="glyphicon glyphicon-plus"></span>
                </div>
                <div class="col-md-1" style="width: 110px;">Dependencia</div>
                <div class="col-md-1" style="width: 90px;">Serie</div>
                <div class="col-md-1" style="width: 100px;">Sub Serie</div>
                <div class="col-md-1" style="width: 160px;">Tipo de Documento</div>
                <div class="col-md-1" style="width: 65px;">Gestion</div>
                <div class="col-md-1" style="width: 65px;">Central</div>
                <div class="col-md-1" style="width: 80px;">Soporte</div>
                <div class="col-md-1" style="width: 150px;">Disposicion Final</div>
                <div class="col-md-1" style="width: 95px;">Digitalización</div>
                <div class="col-md-1" style="width: 300px;">Procedimiento</div>
                <div id="contenedor_retencion"> 
                </div>
                <!-- </div> -->
              </div>
            </div>
          </div>
        </div>

    </fieldset>

  @if(isset($retencion))
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
</div>
@stop