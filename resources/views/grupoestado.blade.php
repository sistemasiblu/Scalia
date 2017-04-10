<?php
//print_r($grupoEstado->estadoCRM);
//print_r($grupoEstado->eventoCRM);

//print_r($grupoEstado->origenCRM);

//print_r($grupoEstado->categoriaCRM);

//print_r($grupoEstado->acuerdoservicio);

//$cantAsesor=count($asesor);
//echo $cantAsesor;
//print_r($grupoEstado);

//return;

?>
@extends('layouts.vista')
@section('titulo')
  <h3 id="titulo">
    <center>Grupos de Estados</center>
  </h3>
@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/grupoestado.js'); !!}

<script>

function abrirModalAsesores()
{
  $('#ModalAsesores').modal('show');
}


  var estadoCRM = '<?php echo (isset($grupoEstado) ? json_encode($grupoEstado->estadoCRM) : "");?>';
  estadoCRM = (estadoCRM != '' ? JSON.parse(estadoCRM) : '');

  var eventoCRM = '<?php echo (isset($grupoEstado) ? json_encode($grupoEstado->eventoCRM) : "");?>';
  eventoCRM = (eventoCRM != '' ? JSON.parse(eventoCRM) : '');

  var origenCRM = '<?php echo (isset($grupoEstado) ? json_encode($grupoEstado->origenCRM) : "");?>';
  origenCRM = (origenCRM != '' ? JSON.parse(origenCRM) : '');
  
  var categoriaCRM = '<?php echo (isset($grupoEstado) ? json_encode($grupoEstado->categoriaCRM) : "");?>';
  categoriaCRM = (categoriaCRM != '' ? JSON.parse(categoriaCRM) : '');
  
  var acuerdoservicio = '<?php echo (isset($grupoEstado) ? json_encode($grupoEstado->acuerdoservicio) : "");?>';
  acuerdoservicio = (acuerdoservicio != '' ? JSON.parse(acuerdoservicio) : '');

  var asesorCRM = '<?php echo (isset($asesor) ? json_encode($asesor) : "");?>';
  asesorCRM = (asesorCRM != '' ? JSON.parse(asesorCRM) : '');

  // var cantasesores = '<?php echo (isset($cantAsesor) ? json_encode($cantAsesor) : "");?>';
  // cantasesores = (cantasesores != '' ? JSON.parse(cantasesores) : '');

  var valorEstado = [0,'', ''];
  var valorEvento = [0,'', ''];
  var valorOrigen = [0,'', ''];
  var valorCategoria = [0,'', ''];
  var valorAcuerdo = [0,'', '', '', ''];
  var valorAsesor = [0,'',''];

  
  var tipoestado = [["Nuevo","Pendiente","En Proceso","Cancelado","Fallido","Exitoso"], ["Nuevo","Pendiente","En Proceso","Cancelado","Fallido","Exitoso"]];

  $(document).ready(function(){
    
    estados = new Atributos('estados','contenedor_estados','estados_');

    estados.altura = '36px;';
    estados.campoid = 'idEstadoCRM';
    estados.campoEliminacion = 'eliminarDetalle';

    estados.campos = ['idEstadoCRM','nombreEstadoCRM','tipoEstadoCRM'];
    estados.etiqueta = ['input','input','select'];
    estados.tipo = ['hidden','text',''];
    estados.estilo = ['','width: 400px;height:35px;','width: 400px;height:35px;'];
    estados.clase = ['','',''];
    estados.sololectura = [false,false,false];
    estados.opciones = ['','',tipoestado];
    estados.completar = ['off', 'off','off'];
   
    for(var j=0, k = estadoCRM.length; j < k; j++)
    {
        estados.agregarCampos(JSON.stringify(estadoCRM[j]),'L');
    }

    document.getElementById('registros').value = j ;

    eventos = new Atributos('eventos','contenedor_eventos','eventos_');

    eventos.altura = '36px;';
    eventos.campoid = 'idEventoCRM';
    eventos.campoEliminacion = 'eliminarEvento';

    eventos.campos = ['idEventoCRM','codigoEventoCRM','nombreEventoCRM'];
    eventos.etiqueta = ['input','input','input'];
    eventos.tipo = ['hidden','text','text'];
    eventos.estilo = ['','width: 200px;height:35px;','width: 600px;height:35px;'];
    eventos.clase = ['','',''];
    eventos.sololectura = [false,false,false];
    eventos.opciones = ['','',''];
    eventos.completar = ['off', 'off','off'];
   
    for(var j=0, k = eventoCRM.length; j < k; j++)
    {
        eventos.agregarCampos(JSON.stringify(eventoCRM[j]),'L');
    }


    categorias = new Atributos('categorias','contenedor_categorias','categorias_');

    categorias.altura = '36px;';
    categorias.campoid = 'idCategoriaCRM';
    categorias.campoEliminacion = 'eliminarCategoria';

    categorias.campos = ['idCategoriaCRM','codigoCategoriaCRM','nombreCategoriaCRM'];
    categorias.etiqueta = ['input','input','input'];
    categorias.tipo = ['hidden','text','text'];
    categorias.estilo = ['','width: 200px;height:35px;','width: 600px;height:35px;'];
    categorias.clase = ['','',''];
    categorias.sololectura = [false,false,false];
    categorias.opciones = ['','',''];
    categorias.completar = ['off', 'off','off'];
   
    for(var j=0, k = categoriaCRM.length; j < k; j++)
    {
        categorias.agregarCampos(JSON.stringify(categoriaCRM[j]),'L');
    }

    origenes = new Atributos('origenes','contenedor_origenes','origenes_');

    origenes.altura = '36px;';
    origenes.campoid = 'idOrigenCRM';
    origenes.campoEliminacion = 'eliminarOrigen';

    origenes.campos = ['idOrigenCRM','codigoOrigenCRM','nombreOrigenCRM'];
    origenes.etiqueta = ['input','input','input'];
    origenes.tipo = ['hidden','text','text'];
    origenes.estilo = ['','width: 200px;height:35px;','width: 600px;height:35px;'];
    origenes.clase = ['','',''];
    origenes.sololectura = [false,false,false];
    origenes.opciones = ['','',''];
    origenes.completar = ['off', 'off','off'];
   
    for(var j=0, k = origenCRM.length; j < k; j++)
    {
        origenes.agregarCampos(JSON.stringify(origenCRM[j]),'L');
    }


    unidadTiempo = [['Minutos', 'Horas', 'Dias'],['Minutos', 'Horas', 'Dias']];
    acuerdos = new Atributos('acuerdos','contenedor_acuerdos','acuerdos_');

    acuerdos.altura = '36px;';
    acuerdos.campoid = 'idAcuerdoServicio';
    acuerdos.campoEliminacion = 'eliminarAcuerdoServicio';

    acuerdos.campos = ['idAcuerdoServicio','codigoAcuerdoServicio','nombreAcuerdoServicio', 'tiempoAcuerdoServicio', 'unidadTiempoAcuerdoServicio'];
    acuerdos.etiqueta = ['input','input','input','input','select'];
    acuerdos.tipo = ['hidden','text','text','text',''];
    acuerdos.estilo = ['','width: 200px;height:35px;','width: 400px;height:35px;','width: 200px;height:35px;','width: 200px;height:35px;'];
    acuerdos.clase = ['','','','',''];
    acuerdos.sololectura = [false,false,false,false,false];
    acuerdos.opciones = ['','','','',unidadTiempo];
    acuerdos.completar = ['off', 'off','off','off','off'];
   
    for(var j=0, k = acuerdoservicio.length; j < k; j++)
    {
        acuerdos.agregarCampos(JSON.stringify(acuerdoservicio[j]),'L');
    }


    asesores = new Atributos('asesores','contenedor_asesores','asesores_');

    asesores.altura = '40px;';
    asesores.campoid = 'idGrupoEstadoAsesor';
    asesores.campoEliminacion = 'eliminarAsesor';

                        

    asesores.campos = ['idGrupoEstadoAsesor','Tercero_idAsesor','nombre1Tercero'];
    asesores.etiqueta = ['input','input','input'];
    asesores.tipo = ['hidden','hidden',''];
    asesores.estilo = ['','','width: 600px;height:35px;'];
    asesores.clase = ['','',''];
    asesores.sololectura = [false,false,false];
    asesores.opciones = ['','',''];
    asesores.completar = ['off','off', 'off'];

    for(var j=0, k =asesorCRM.length; j < k; j++)
    {
        console.log(JSON.stringify(asesorCRM[j]));
        asesores.agregarCampos(JSON.stringify(asesorCRM[j]),'L');
    }


  });
</script>
  
	@if(isset($grupoEstado))
		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
			{!!Form::model($grupoEstado,['route'=>['grupoestado.destroy',$grupoEstado->idGrupoEstado],'method'=>'DELETE'])!!}
		@else
			{!!Form::model($grupoEstado,['route'=>['grupoestado.update',$grupoEstado->idGrupoEstado],'method'=>'PUT'])!!}
		@endif
	@else
		{!!Form::open(['route'=>'grupoestado.store','method'=>'POST'])!!}
	@endif


<div id='form-section' >

	<fieldset id="grupoestado-form-fieldset">	
		<div class="form-group" id='test'>
          {!!Form::label('codigoGrupoEstado', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              <input type="hidden" id="token" value="{{csrf_token()}}"/>
              {!!Form::text('codigoGrupoEstado',null,['class'=>'form-control','placeholder'=>'Ingresa el código del grupo'])!!}
              {!!Form::hidden('idGrupoEstado', null, array('id' => 'idGrupoEstado')) !!}
              {!! Form::hidden('registros', 0, array('id' => 'registros')) !!}
              {!!Form::hidden('eliminarDetalle', '', array('id' => 'eliminarDetalle'))!!}
              {!!Form::hidden('eliminarCategoria', '', array('id' => 'eliminarCategoria'))!!}

            </div>
          </div>
    </div>
    <div class="form-group" id='test'>
        {!!Form::label('nombreGrupoEstado', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-pencil-square-o "></i>
            </span>
			     {!!Form::text('nombreGrupoEstado',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del grupo'])!!}
          </div>
      </div>
      
     
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#estado">Estados</a></li>
        <li><a data-toggle="tab" href="#evento">Eventos</a></li>
        <li><a data-toggle="tab" href="#categoria">Categorías</a></li>
        <li><a data-toggle="tab" href="#origen">Orígenes</a></li>
        <li><a data-toggle="tab" href="#acuerdo">Acuerdos de Servicio</a></li>
        <li><a data-toggle="tab" href="#asesores">Asesores</a></li>

      </ul>
     
      <div class="tab-content">
        <div id="estado" class="tab-pane fade in active">
          
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;" onclick="estados.agregarCampos(valorEstado,'A')">
                    <span class="glyphicon glyphicon-plus"></span>
                  </div>
                  <div class="col-md-1" style="width: 400px;">Estado</div>
                  <div class="col-md-1" style="width: 400px;">Tipo</div>
                  <div id="contenedor_estados">
                  </div>
              </div>
            </div>
          </div> 

        </div>
        <div id="evento" class="tab-pane fade">
          
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;" onclick="eventos.agregarCampos(valorEvento,'A')">
                    <span class="glyphicon glyphicon-plus"></span>
                  </div>
                  <div class="col-md-1" style="width: 200px;">Código</div>
                  <div class="col-md-1" style="width: 600px;">Nombre</div>
                  <div id="contenedor_eventos">
                  </div>
              </div>
            </div>
          </div> 
        </div>

        <div id="categoria" class="tab-pane fade">
          
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;" onclick="categorias.agregarCampos(valorCategoria,'A')">
                    <span class="glyphicon glyphicon-plus"></span>
                  </div>
                  <div class="col-md-1" style="width: 200px;">Código</div>
                  <div class="col-md-1" style="width: 600px;">Nombre</div>
                  <div id="contenedor_categorias">
                  </div>
              </div>
            </div>
          </div> 
        </div>

        <div id="origen" class="tab-pane fade">
          
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;" onclick="origenes.agregarCampos(valorOrigen,'A')">
                    <span class="glyphicon glyphicon-plus"></span>
                  </div>
                  <div class="col-md-1" style="width: 200px;">Código</div>
                  <div class="col-md-1" style="width: 600px;">Nombre</div>
                  <div id="contenedor_origenes">
                  </div>
              </div>
            </div>
          </div> 
        </div>

        <div id="acuerdo" class="tab-pane fade">
          
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;" onclick="acuerdos.agregarCampos(valorAcuerdo,'A')">
                    <span class="glyphicon glyphicon-plus"></span>
                  </div>
                  <div class="col-md-1" style="width: 200px;">Código</div>
                  <div class="col-md-1" style="width: 400px;">Nombre</div>
                  <div class="col-md-1" style="width: 200px;">Tiempo</div>
                  <div class="col-md-1" style="width: 200px;">Unidad de Tiempo</div>
                  <div id="contenedor_acuerdos">
                  </div>
              </div>
            </div>
          </div> 

        </div>


        <div id="asesores" class="tab-pane fade">
          
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;height: 35px;" onclick="abrirModalAsesores();">
                    <span class="glyphicon glyphicon-plus"></span>
                  </div>
                  <div class="col-md-1" style="width: 600px;height: 35px;">Nombre</div>
                  <div id="contenedor_asesores">
                  </div>
              </div>
            </div>
          </div> 

        </div>

      </div>

      
    </fieldset>
  
    @if(isset($grupoEstado))
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
</div>
@stop


<div id="ModalAsesores" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/mostrarasesoresgridselect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>