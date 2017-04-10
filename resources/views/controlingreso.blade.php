@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Control de Ingreso</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/controlingreso.js')!!}
<script>

    var idDispositivo = '<?php echo isset($idDispositivo) ? $idDispositivo : "";?>';
    var nombreDispositivo = '<?php echo isset($nombreDispositivo) ? $nombreDispositivo : "";?>';
    var idMarca = '<?php echo isset($idMarca) ? $idMarca : "";?>';
    var nombreMarca = '<?php echo isset($nombreMarca) ? $nombreMarca : "";?>';

    var dispositivo = [JSON.parse(idDispositivo), JSON.parse(nombreDispositivo)];
    var marca = [JSON.parse(idMarca), JSON.parse(nombreMarca)];

    var controlingresodetalle = '<?php echo (isset($controlingreso) ? json_encode($controlingreso->controlingresodetalle) : "");?>';
    controlingresodetalle = (controlingresodetalle != '' ? JSON.parse(controlingresodetalle) : '');
    var valorControlIngreso = [0,'','','','','',0];

    $(document).ready(function(){

      control = new Atributos('control','contenedor_control','control_');

      control.altura = '35px';
      control.campoid = 'idControlIngresoDetalle';
      control.campoEliminacion = 'eliminarControlIngreso';

      control.campos   = ['ControlIngreso_idControlIngreso', 'Dispositivo_idDispositivo', 'Marca_idMarca','referenciaDispositivoControlIngresoDetalle', 'observacionControlIngresoDetalle', 'retiraDispositivoControlIngresoDetalle','idControlIngresoDetalle'];
      control.etiqueta = ['input', 'select', 'select', 'input', 'input', 'checkbox','input'];
      control.tipo     = ['hidden', '', '', 'text', 'text', 'checkbox','hidden'];
      control.estilo   = ['', 'width: 240px;height:35px;', 'width: 240px;height:35px;','width: 240px;height:35px;', 'width: 240px;height:35px;', 'width: 240px;height:35px;display:none;', ''];
      control.clase    = ['', '', '', '', '', '', ''];
      control.opciones = ['', dispositivo, marca, '', '', '', ''];      
      control.sololectura = [true, false, false, false, false, false, true];
      control.completar = ['off','off','off','off','off','off', 'off'];
      for(var j=0, k = controlingresodetalle.length; j < k; j++)
      {
        control.agregarCampos(JSON.stringify(controlingresodetalle[j]),'L');
        console.log(JSON.stringify(controlingresodetalle[j]))
      }

    });

  </script>
	
			{!!Form::open(['route'=>'controlingreso.store','method'=>'POST'])!!}
	
<div id='form-section' >

	<fieldset id="controlingreso-form-fieldset">	

		    <div class="form-group col-md-6" id='test'>
          {!!Form::label('TipoIdentificacion_idTipoIdentificacion', 'Tipo de documento', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!! Form::select('TipoIdentificacion_idTipoIdentificacion', $tipodocumento ,null,['class' => 'chosen-select form-control']) !!}
              {!!Form::hidden('idControlIngreso', null, array('id' => 'idControlIngreso')) !!}
              {!!Form::hidden('eliminarControlIngreso', null, array('id' => 'eliminarControlIngreso')) !!}
            </div>
          </div>
        </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('numeroDocumentoVisitanteControlIngreso', 'N° Documento', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-barcode "></i>
            </span>
            {!!Form::text('numeroDocumentoVisitanteControlIngreso',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off', 'onchange'=>'consultarControlIngreso(this.value)'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('nombreVisitanteControlIngreso', 'Nombre(s)', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user "></i>
            </span>
            {!!Form::text('nombreVisitanteControlIngreso',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('apellidoVisitanteControlIngreso', 'Apellido(s)', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user "></i>
            </span>
            {!!Form::text('apellidoVisitanteControlIngreso',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off'])!!}
          </div>
        </div>
      </div>


      <div class="form-group col-md-6" id='test'>
        {!!Form::label('Tercero_idResponsable', 'Responsable', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-user "></i>
            </span>
            {!! Form::select('Tercero_idResponsable', $responsable ,null,['class' => 'chosen-select form-control', 'onchange'=>'consultarCentroTrabajo(this.value)']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'>
        {!!Form::label('dependenciaControlIngreso', 'Dependencia', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-bank "></i>
            </span>
            {!!Form::text('dependenciaControlIngreso',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','readonly'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-12" id='test'>
        {!!Form::label('accionControlIngreso', 'Accion', array('class' => 'col-sm-1 control-label')) !!}
        <div class="col-sm-11">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-external-link"></i>
            </span>
            {!!Form::text('accionControlIngreso',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','readonly'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='entrada' style="display:none">
        {!!Form::label('fechaIngresoControlIngreso', 'Fecha de ingreso', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar "></i>
            </span>
            {!!Form::text('fechaIngresoControlIngreso',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','readonly'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='salida' style="display:none">
        {!!Form::label('fechaSalidaControlIngreso', 'Fecha de salida', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-calendar "></i>
            </span>
            {!!Form::text('fechaSalidaControlIngreso',null,['class'=>'form-control','placeholder'=>'', 'autocomplete' => 'off','readonly'])!!}
          </div>
        </div>
      </div>


      <div class="form-group col-md-12" id='test'>
        {!!Form::label('observacionControlIngreso', 'Observación', array('class' => 'col-sm-1 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o "></i>
            </span>
            {!!Form::textarea('observacionControlIngreso',null,['class'=> 'form-control','placeholder'=>''])!!}
          </div>
        </div>
      </div>

      <br><br><br><br><br>

        <div class="form-group">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">Elementos</div>
              <div class="panel-body">
                <div class="panel-group" id="accordion">
                  <div class="panel panel-info">
                    <div id="elementos">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-12">
                            <div class="row show-grid">
                              <div class="col-md-1" id="agregarRegistro" style="width: 40px; height:42px; cursor: pointer;" onclick="control.agregarCampos(valorControlIngreso,'A')">
                                <span class="glyphicon glyphicon-plus"></span>
                              </div>
                              <div class="col-md-1" style="width: 240px;">Dispositivo</div>
                              <div class="col-md-1" style="width: 240px;">Marca</div>
                              <div class="col-md-1" style="width: 240px;">Referencia/Serie</div>
                              <div class="col-md-1" style="width: 240px;">Observaciones</div>
                              <div id="check" class="col-md-1" style="width: 240px; display:none;">Retira dispositivo</div>
                              <div id="contenedor_control"> 
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

  {!!Form::submit('Guardar',["class"=>"btn btn-primary"])!!}
  {!!Form::button('Consultar',["class"=>"btn btn-success", "onclick"=>"location.href = '/controlingresogrid';"])!!}


	{!! Form::close() !!}
</div>
@stop
