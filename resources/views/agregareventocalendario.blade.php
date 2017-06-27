@extends('layouts.modal')
@section('titulo')<h3 id="titulo"><center>Agenda</center></h3>@stop

@section('content')
@include('alerts.request')

{!!Html::script('js/agenda.js')!!}
{!!Html::script('js/movimientocrm.js'); !!}

<script>

    var consultarTercero = ['onchange','consultarTercero(this.id, this.value)'];

    var agendaasistente = '<?php echo (isset($agendaAsistente) ? json_encode($agendaAsistente) : "");?>';
    agendaasistente = (agendaasistente != '' ? JSON.parse(agendaasistente) : '');

    var valorAgendaAsistente = [0, 0, '', '', 0];

    $(document).ready(function(){

      asistente = new Atributos('asistente','contenedor_asistente','agendaasistente');

      asistente.altura = '35px';
      asistente.campoid = 'idAgendaAsistente';
      asistente.campoEliminacion = 'eliminarAgendaAsistente';

      asistente.campos   = [
      'idAgendaAsistente',
      'Tercero_idAsistente',
      'nombreAgendaAsistente',
      'correoElectronicoAgendaAsistente',
      'Agenda_idAgenda'
      ];

      asistente.etiqueta = [
      'input',
      'input',
      'input',
      'input',
      'input'
      ];

      asistente.tipo = [
      'hidden',
      'hidden',
      'text',
      'text',
      'hidden'
      ];

      asistente.estilo = [
      '',
      '',
      'width: 310px;height:35px;',
      'width: 150px;height:35px;',
      ''
      ];

      asistente.clase    = ['','','','','','','',''];
      asistente.sololectura = [true,true,false,false,true];  
      asistente.funciones = ['','',consultarTercero,'',''];
      asistente.completar = ['off','off','off','off','off'];
      asistente.opciones = ['','','','',''];
      for(var j=0, k = agendaasistente.length; j < k; j++)
      {
        asistente.agregarCampos(JSON.stringify(agendaasistente[j]),'L');
        // llenarDatosCampo($('#CampoCRM_idCampoCRM'+j).val(), j);
      }

    });

  </script>

  <script>

    var agendaseguimiento = '<?php echo (isset($agendaSeguimiento) ? json_encode($agendaSeguimiento) : "");?>';
    agendaseguimiento = (agendaseguimiento != '' ? JSON.parse(agendaseguimiento) : '');

    var valorAgendaSeguimiento = [
                    0,
                    "<?php echo \Session::get("idUsuario");?>",
                    0,
                    "<?php echo date('Y-m-d H:i:s');?>",
                    ''
                    ];
  </script>


   @if(isset($agenda))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($agenda,['route'=>['agenda.destroy',$agenda->idAgenda],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($agenda,['route'=>['agenda.update',$agenda->idAgenda],'method'=>'PUT'])!!}
    @endif
  @else
      {!!Form::open(['route'=>'agenda.store','method'=>'POST', 'action' => 'AgendaController@store', 'id' => 'agenda'])!!}
  @endif

<?php
  if(isset($_GET['id']))
  {
    $datosagenda = DB::Select('SELECT * FROM agenda WHERE idAgenda = '.$_GET['id']);
    $agenda = get_object_vars($datosagenda[0]);

    $fechaInicio =  substr($agenda['fechaHoraInicioAgenda'], 0, -3);
    $agenda['fechaHoraInicioAgenda'] = date("d-m-Y H:m:s",$fechaInicio);

    $fechaFin =  substr($agenda['fechaHoraFinAgenda'], 0, -3);
    $agenda['fechaHoraFinAgenda'] = date("d-m-Y H:m:s",$fechaFin);

    echo "<script> 
            $(document).ready(function(){
               consultarCamposAgenda($('#CategoriaAgenda_idCategoriaAgenda').val());
            });
          </script>";
  }
?>
<div id='form-section'>
<input type="hidden" id="token" value="{{csrf_token()}}"/>
  <fieldset id="agenda-form-fieldset"> 

        <div class="form-group" id='test'>
          {!!Form::label('CategoriaAgenda_idCategoriaAgenda', 'Categoria', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::select('CategoriaAgenda_idCategoriaAgenda',$categoriaagenda, (isset($agenda) ? $agenda['CategoriaAgenda_idCategoriaAgenda'] : 0),["class" => "form-control", "placeholder" =>"Seleccione tipo de categoria", 'onchange'=>'consultarCamposAgenda(this.value)'])!!}
            {!!Form::hidden('idAgenda', (isset($agenda) ? $agenda["idAgenda"] : null), array('id' => 'idAgenda')) !!}
            {!!Form::hidden('eliminarAgendaAsistente',null,['id'=>'eliminarAgendaAsistente'])!!}
            {!!Form::hidden('eliminarAgendaSeguimiento',null,['id'=>'eliminarAgendaSeguimiento'])!!}
          </div>
        </div>
      </div>


    
        <div class="form-group" id='test'>
          {!!Form::label('asuntoAgenda', 'Asunto', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
            {!!Form::text('asuntoAgenda',(isset($agenda) ? $agenda['asuntoAgenda'] : null),['class'=>'form-control','placeholder'=>'Ingresa el asunto de la agenda'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
           {!!Form::label('fechaHoraInicioAgenda', 'Fecha Inicial', array('class' => 'col-sm-2 control-label')) !!}
            <div class="col-sm-10">
                <div class="input-group">
                   <span class="input-group-addon">
                      <i class="fa fa-calendar" aria-hidden="true"></i>
                   </span>
                    {!!Form::text('fechaHoraInicioAgenda',(isset($agenda) ? $agenda['fechaHoraInicioAgenda'] : null),['class'=> 'form-control','placeholder'=>'Ingrese la fecha inicial'])!!}
                 </div>
            </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('fechaHoraFinAgenda', 'Fecha Final', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group" >
             <span class="input-group-addon">
                <i class="fa fa-calendar" aria-hidden="true"></i>
             </span>
              {!!Form::text('fechaHoraFinAgenda',(isset($agenda) ? $agenda['fechaHoraFinAgenda'] : null),['class'=> 'form-control','placeholder'=>'Ingrese la fecha final'])!!}
            </div>
          </div>
         </div>

        <div class="form-group" id='test'>
          {!!Form::label('Tercero_idSupervisor', 'Supervisor', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::select('Tercero_idSupervisor',$supervisor, (isset($agenda) ? $agenda['Tercero_idSupervisor'] : null),["class" => "form-control", "placeholder" =>"Seleccione el supervisor"])!!}  
          </div>
        </div>
      </div>


      <br><br><br><br><br>

      <div class="form-group" id='MovimientoCRM' style='display:none;'>
          {!!Form::label('MovimientoCRM_idMovimientoCRM', 'Caso CRM', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!!Form::select('MovimientoCRM_idMovimientoCRM',$casocrm, (isset($agenda) ? $agenda['MovimientoCRM_idMovimientoCRM'] : null),["class" => "form-control", "placeholder" =>"Seleccione un caso del CRM"])!!}  
          </div>
        </div>
      </div>

        <div class="form-group" id='ubicacion' style='display:none;'>
          {!!Form::label('ubicacionAgenda', 'Ubicación', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group" >
             <span class="input-group-addon">
                <i class="fa fa-sitemap" aria-hidden="true"></i>
             </span>
              {!!Form::text('ubicacionAgenda',(isset($agenda) ? $agenda['ubicacionAgenda'] : null),['class'=> 'form-control','placeholder'=>'Ingrese la ubicacion'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='Tercero' style='display:none;'>
          {!!Form::label('Tercero_idResponsable', 'Responsable', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!!Form::select('Tercero_idResponsable',$responsable, (isset($agenda) ? $agenda['Tercero_idResponsable'] : null),["class" => "form-control", "placeholder" =>"Seleccione un responsable"])!!}  
          </div>
        </div>
      </div>

        <div class="form-group" id='porcentajeEjecucion' style='display:none;'>
          {!!Form::label('porcentajeEjecucionAgenda', '% Ejecución', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group" >
             <span class="input-group-addon">
                <i class="" aria-hidden="true">%</i>
             </span>
              {!!Form::text('porcentajeEjecucionAgenda',(isset($agenda) ? $agenda['porcentajeEjecucionAgenda'] : null),['class'=> 'form-control','placeholder'=>'Ingrese el porcentaje ejecutado'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='estado' style='display:none;'>
          {!!Form::label('estadoAgenda', 'Estado', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group" >
             <span class="input-group-addon">
                <i class="fa fa-tasks" aria-hidden="true"></i>
             </span>
              {!! Form::select('estadoAgenda', ['Sin finalizar' => 'Sin finalizar', 'Finalizado' => 'Finalizado'],null,['class' => 'form-control', 'placeholder' => 'Seleccione un estado']) !!}
            </div>
          </div>
        </div>

        <br><br><br><br><br><br><br><br><br><br><br>

        <div class="form-group">
          <div class="col-md-12">
            <div class="panel panel-primary">
              <div class="panel-heading">Contenido</div>
              <div class="panel-body">
                <div class="panel-group" id="accordion">

                <ul class="nav nav-tabs"> <!--Pestañas de navegacion-->
                  <li class="active"><a data-toggle="tab" href="#detalles">Detalles</a></li>
                  <li id="liseguimiento" style="display:none;"><a data-toggle="tab" href="#divseguimiento">Seguimiento</a></li>
                  <li id="liasistentes" style="display:none;"><a data-toggle="tab" href="#divasistentes">Asistentes</a></li>
                </ul>

                <div class="tab-content">
                  
                  <div id="detalles" class="tab-pane fade in active">

                    <div class="form-group" id='test'>
                      
                      <div class="col-sm-10">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-pencil-square-o"></i>
                          </span>
                          {!!Form::textarea('detallesAgenda',(isset($agenda) ? $agenda['detallesAgenda'] : null),['class'=>'form-control','style'=>'height:100px;','placeholder'=>'Ingresa el detalle de la agenda'])!!}
                        </div>
                      </div>
                    </div>

                  </div>

                  <div id="divseguimiento" class="tab-pane fade" >

                    <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-12">
                            <div class="row show-grid">
                              <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="seguimiento.agregarSeguimiento(valorAgendaSeguimiento,'A')">
                                <span class="glyphicon glyphicon-plus"></span>
                              </div>
                              <div class="col-md-1" style="width: 150px;">Fecha</div>
                              <div class="col-md-1" style="width: 310px;">Detalles</div>
                              <div id="contenedor_seguimiento"> 
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                  </div>

                  <div id="divasistentes" class="tab-pane fade">

                    <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-12">
                            <div class="row show-grid">
                              <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="asistente.agregarCampos(valorAgendaAsistente,'A')">
                                <span class="glyphicon glyphicon-plus"></span>
                              </div>
                              <div class="col-md-1" style="width: 310px;">Nombre</div>
                              <div class="col-md-1" style="width: 150px;">Correo Electrónico</div>
                              <div id="contenedor_asistente"> 
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
    </fieldset>

  @if(isset($agenda))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
        {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
      @else
        {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
        {!!Form::button('Cancelar cita',["class"=>"btn btn-danger","onclick"=>"cancelarCita($('#idAgenda').val())"])!!}
      @endif
  @else
    @if(isset($_GET['crear']))
      {!!Form::button('Agregar',["class"=>"btn btn-primary", 'id'=>'btnAdicionarTareaCRM', 'onclick'=>'agregarRegistroTareaCRM(
      $(\'#CategoriaAgenda_idCategoriaAgenda\').val(),
      $(\'#CategoriaAgenda_idCategoriaAgenda option:selected\').text(),
      $(\'#asuntoAgenda\').val(),
      $(\'#ubicacionAgenda\').val(),
      $(\'#fechaHoraInicioAgenda\').val(),
      $(\'#fechaHoraFinAgenda\').val(),
      $(\'#Tercero_idResponsable\').val(),
      $(\'#Tercero_idResponsable option:selected\').text(),
      $(\'#estadoAgenda\').val());'])!!}
    @else
      {!!Form::submit('Adicionar',["class"=>"btn btn-primary",'onclick'=>'validarFormulario(event);'])!!}
    @endif    
  @endif

  {!! Form::close() !!}
</div>
<script>
  // CKEDITOR.replace(('detallesAgenda'), {
  //     fullPage: true,
  //     allowedContent: true
  //   });  

  $('#fechaHoraInicioAgenda').datetimepicker(({
      format: "DD-MM-YYYY HH:mm:ss"
    }));

    $('#fechaHoraFinAgenda').datetimepicker(({
      format: "DD-MM-YYYY HH:mm:ss"
    }));

    $(document).ready(function(){
    
      //**************************
      // 
      //   S E G U I M I E N T O
      //
      //**************************
      seguimiento = new AtributosSeguimiento('seguimiento','contenedor_seguimiento','seguimiento_');

      seguimiento.alto = '42px;';
      seguimiento.ancho = '100%;';
      seguimiento.campoid = 'idAgendaSeguimiento';
      seguimiento.campoEliminacion = 'eliminarAgendaSeguimiento';

      for(var j=0, k = agendaseguimiento.length; j < k; j++)
      {
          seguimiento.agregarSeguimiento(JSON.stringify(agendaseguimiento[j]),'L');
      }
    });
</script>
@stop

<div id="modalTercero" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:50%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Seleccionar terceros</h4>
      </div>
      <div class="modal-body">
        <div class="container">
            <div class="row">
              <div class="container">                      
                <table id="tlistaselect" name="tlistaselect" class="display table-bordered" width="100%">
                  <thead>
                    <tr class="btn-primary active">
                      <th><b>Nombre</b></th>
                      <th><b>Correo</b></th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr class="btn-default active">
                      <th>Nombre</th>
                      <th>Correo</th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>