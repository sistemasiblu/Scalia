<?php
//print_r( $planmantenimientoParte);
//return;
?> 
@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Plan Mantenimiento</center></h3>@stop

@section('content')
@include('alerts/request')
@if(isset($plmantenimiento))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($plmantenimiento,['route'=>['planmantenimiento.destroy',$plmantenimiento->idPlanMantenimiento],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($plmantenimiento,['route'=>['planmantenimiento.update',$plmantenimiento->idPlanMantenimiento],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'planmantenimiento.store','method'=>'POST'])!!}
@endif
<!DOCTYPE html>
<html>
 <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      
<script>

 var partesPlanMantenimiento = '<?php echo (isset($plmantenimientoParte) ? json_encode($plmantenimientoParte) : "");?>';
  partesPlanMantenimiento = (partesPlanMantenimiento != '' ? JSON.parse(partesPlanMantenimiento) : '');

  var valorpartesPlanMantenimiento = [0,0,''];
  $(document).ready(function()
  {
    numeroDias();
    numeroMeses();
      parteactivo=new Atributos('parteactivo','contenedor-parteactivo','parteactivo-');
      parteactivo.campoid = 'idPlanMantenimientoParte';
      parteactivo.campoEliminacion = 'parteEliminar';
      parteactivo.campos=['idPlanMantenimientoParte', 'Activo_idParte','nombreActivoParte'];
      parteactivo.etiqueta=['input','input','input'];
      parteactivo.tipo=['hidden','hidden',''];
      parteactivo.estilo=['','','width:510px; height:35px;'];
      parteactivo.clase=['','',''];
      parteactivo.sololectura=[false,false,true];
      parteactivo.completar=['off','off','off'];
      parteactivo.opciones = [[],[],[]];      
      parteactivo.funciones=['','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';

      for(var j=0; j < partesPlanMantenimiento.length; j++)
      {
         parteactivo.agregarCampos(JSON.stringify(partesPlanMantenimiento[j]),'L');
      }

  });
  

  $
     function abrirModalCampos()
      {
        $('#ModalCampos').modal('show');
      }

      function abrirModalCampos1()
      {
        $('#ModalCampos1').modal('show');
      }

     function numeroDias()
    {
        var checkboxValues = "";
        $('input[name="tareaDiasPlanMantenimientoAlerta[]"]:checked').each(function() 
        {
            checkboxValues += $(this).val() + ",";
        });

        $('#numeroDias').val(checkboxValues);

    }

    function numeroMeses()
    {
        var checkboxValues = "";
        $('input[name="tareaMesesPlanMantenimientoAlerta[]"]:checked').each(function() 
        {
            checkboxValues += $(this).val() + ",";
        });

        $('#numeroMeses').val(checkboxValues);

    }

      
</script>
</head>
<body>
<div class="container">
<br><br>
    <div class='form-group'>
    <div class="col-sm-12">
        {!!Form::label('Activo_idActivo', 'Nombre', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            {!!Form::select('Activo_idActivo', @$activo, @$plmantenimiento->Activo_idActivo,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('actividadPlanMantenimiento', 'Actividad', array('class' => 'col-sm-4 control-label')) !!}     
          <div class="col-sm-8">
            {!!Form::select('actividadPlanMantenimiento',['Revision General'=>'Revision General', 'Mantenimiento'=>'Mantenimiento','Reparacion'=>'Reparacion'],null,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}  
          </div>       
        {!!Form::label('programacion', 'Programacion', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            <div class="input-group" >
              {!!Form::text('programacion',null,['class'=>'form-control','readonly'=>'true', 'style'=>'background-color:white;'])!!}<span class="input-group-addon" onclick="abrirModalCampos1();" style="cursor:pointer;"><img src="/imagenes/barras.png"  height="15px;" /></span>        
            </div>        
          </div>   
        {!!Form::label('prioridadPlanMantenimiento', 'Prioridad', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            {!!Form::select('prioridadPlanMantenimiento',['Alta'=>'Alta', 'Media'=>'Media','Baja'=>'Baja'],null,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}
          </div>
        {!!Form::label('TipoServicio_idTipoServicio', 'Tipo Servicio', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            {!!Form::select('TipoServicio_idTipoServicio', @$tiposervicio, @$plmantenimiento->TipoServicio_idTipoServicio,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('tipoaccion', 'TipoAccion', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            {!!Form::select('TipoAccion_idTipoAccion', @$tipoaccion, @$plmantenimiento->TipoAccion_idTipoAccion,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('tiempotareaPlanMantenimiento', 'Tiempo Tarea', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            {!!Form::text('tiempotareaPlanMantenimiento', null,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('diasparoPlanMantenimiento', 'Dias de Paro', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            {!!Form::text('diasparoPlanMantenimiento', null,['class' => 'form-control'])!!}
          </div>
      
      
      {!!Form::hidden('idPlanMantenimientoParte', null, array('id' => 'idPlanMantenimientoParte')) !!}
      {!!Form::hidden('parteEliminar', null, array('id' => '´parteEliminar')) !!}
  
    </div>
    </div>

        <div id="pestanas">
            <ul id=lista class="nav nav-tabs">
                <li  class="active" id="pestana3"><a data-toggle="tab" href='#cpestana3'>Partes</a>
                </li>
                <li id="pestana4"><a data-toggle="tab" href='#cpestana4'>Procedimientos</a></li>
            </ul>
        </div>

<div class="tab-content" id="contenidopestanas">
        <div class="tab-pane fade in active" id="cpestana3">
              <br><br>
            <div class="form-group">
                <fieldset id='varioslistachequeo-form-fieldset'>
                    <div class="form-group"  id='test'>
                        <div class="col-sm-12">
                            <div class="row show-grid">
                                <div class="col-md-1" style="width: 40px;height: 35px;" >
                                    <span class="glyphicon glyphicon-plus" onclick="abrirModalCampos();" ></span>
                                </div>
                                <div class="col-md-1" style="width: 200px;height: 35px;"><b>Nombre Partes</b></div>
                                <div id="contenedor-parteactivo"></div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <div  class="tab-pane fade" id="cpestana4">
          {!!Form::label('procedimientoPlanMantenimiento', 'Procedimientos', array('class' => 'col-sm-4 control-label')) !!}
            <div class="col-sm-12">
                {!!Form::textarea('procedimientoPlanMantenimiento', null,['class' => 'ckeditor'])!!}
            </div>
        </div>
</div>


    
   @if(isset($plmantenimiento))
      @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
        {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
      @else
        {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
      @endif
      @else
       {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
    @endif

    <div id="ModalCampos1" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:95%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Configuraci&oacute;n de Alerta</h4>
      </div>
        <div class="modal-body">
          <div class="container">
                    <br>
                    <div class='form-group'>
                      <div class="col-sm-12">
                        {!!Form::label('nombrePlanMantenimientoAlerta', 'Descripcion', array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-8">
                          {!!Form::text('nombrePlanMantenimientoAlerta',null,['class'=>'form-control','placeholder'=>'Ingresa la descripcion'])!!}
                        </div>
                      </div>
                      <br><br><br>
                      <div id="pestanas principal">
                      
                        <ul id=lista class="nav nav-tabs">
                          <li class="active" id="pestana1"><a data-toggle="tab" class="glyphicons glyphicons-envelope" href='#cpestana1'><img src="/imagenes/correo.png"  height="45px;" /></a></li>
                          <li id="pestana2"><a data-toggle="tab" href='#cpestana2'><img src="/imagenes/clock.png"  height="45px;"/></a></li>
                        </ul>
                      </div>
                     

                    <div class="tab-content" id="contenidopestanasprincipal">
                      <div class="tab-pane fade in active" id="cpestana1">
                       <br><br>
                        <div class="form-group">
                            {!!Form::label('correoParaPlanMantenimientoAlerta', 'Para:', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-9">
                              {!!Form::text('correoParaPlanMantenimientoAlerta',null,['class'=>'form-control','placeholder'=>'Ingresa el Destinatario del correo'])!!}
                            </div>
                              {!!Form::label('correoCopiaPlanMantenimientoAlerta', 'CC:', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-9">
                              {!!Form::text('correoCopiaPlanMantenimientoAlerta',null,['class'=>'form-control','placeholder'=>'Ingresa las personas a poner en copia del correo'])!!}
                            </div>
                              {!!Form::label('correoCopiaOcultaPlanMantenimientoAlerta', 'CCO:', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-9">
                              {!!Form::text('correoCopiaOcultaPlanMantenimientoAlerta',null,['class'=>'form-control','placeholder'=>'Ingresa las personas a poner en copia oculta del correo'])!!}
                            </div>
                              {!!Form::label('correoAsuntoPlanMantenimientoAlerta', 'Asunto:', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-9">
                              {!!Form::text('correoAsuntoPlanMantenimientoAlerta',null,['class'=>'form-control','placeholder'=>'Ingresa el asunto del mensaje'])!!}
                            </div>
                              {!!Form::label('correoMensajePlanMantenimientoAlerta', 'Mensaje:', array('class' => 'col-sm-4 control-label')) !!}
                            <div class="col-sm-11">
                              {!!Form::textarea('correoMensajePlanMantenimientoAlerta',null,['class'=>'ckeditor','placeholder'=>'Ingresa el mensaje'])!!}
                            </div>
                            <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                        </div>
                      </div>

                      <div class="tab-pane fade " id="cpestana2"><br><br>
                      <div class="row" style="min-height:300px;">
                       <div  class="col-sm-5">
                          <div class="col-xs-3">
                             <ul class="nav nav-tabs tabs-left">
                                  <li class="active"><a href="#progDia" data-toggle="tab"><img src="/imagenes/Calendariodia.png" width="55px;"/></a></li>
                                  <li><a href="#progSemana" data-toggle="tab"><img src="/imagenes/CalendarioSemana.png" width="55px;"/></a></li>
                                  <li><a href="#progMes" data-toggle="tab"><img src="/imagenes/CalendarioMes.png" width="55px;"/></a></li>
                              </ul>
                          </div>
                         <div class="col-xs-9">
                                  <!-- Tab panes -->
                            <div class="tab-content">
                              <div class="tab-pane active" id="progDia">
                                <div class="container">
                                  <h4>Programacion Diaria</h4><br><br>
                                  <div class="form-group">
                                    <div class="col-sm-8">
                                      {!!Form::label('tareaFechaInicioPlanMantenimientoAlerta', 'Fecha Inicio', array('class' => 'col-sm-3 control-label')) !!}
                                      <div class=col-sm-7>
                                       <div class="input-group">
                                           <span class="input-group-addon"><img src="/imagenes/CalendarioMes.png"/ height="18px;"></span>
                                           {!!Form::date('tareaFechaInicioPlanMantenimientoAlerta',\Carbon\Carbon::now(),['class'=>'form-control'])!!}
                                       </div>
                                      </div>
                                      {!!Form::label('tareaHoraPlanMantenimientoAlerta', 'Hora Alarma', array('class' => 'col-sm-3 control-label')) !!}
                                        <div class=col-sm-7>
                                          <div class="input-group">
                                            <span class="input-group-addon"><img src="/imagenes/clock.png"/ height="19px;"></span>
                                            {!!Form::time('tareaHoraPlanMantenimientoAlerta','01:00:00',['class'=>'form-control'  ,'max'=>'22:30:00', 'min'=>'01:00:00' ,'step'=>'1'])!!} 
                                          </div>
                                       </div>
                                      {!!Form::label('tareaIntervaloPlanMantenimientoAlerta', 'Cada', array('class' => 'col-sm-3 control-label')) !!}
                                        <div class=col-sm-6>
                                          <div class="input-group">
                                            <span class="input-group-addon"><img src="/imagenes/barras.png"/ height="15px;"></span>
                                            {!!Form::number('tareaIntervaloPlanMantenimientoAlerta',null,['class'=>'form-control','placeholder'=>'Ingresa la periodicidad de dias'])!!}<span class="input-group-addon">Días</span>
                                          </div>
                                        </div>
                                      {!!Form::label('tareaDiaLaboralPlanMantenimientoAlerta', 'Ejecutar solo en Dias Laborales', array('class' => 'col-sm-4 control-label')) !!}
                                          <div class='col-sm-8'>
                                              {!!Form::checkbox('tareaDiaLaboralPlanMantenimientoAlerta','',true,['class' => 'form-control'])!!}
                                          </div>
                                     </div><!--Finaliza div col-sm-8 -->
                                  </div><!--Finaliza div form-group -->
                                </div><!--Finaliza div container -->  
                              </div><!--Finaliza div progDia -->
                              

                              <div class="tab-pane" id="progSemana">
                               <div class="container">
                                <h4>Programacion Semanal</h4><br><br>
                                 <div class="form-group">
                                  <div class="col-sm-8">
                                    {!!Form::label('tareaFechaInicioPlanMantenimientoAlerta', 'Fecha Inicio', array('class' => 'col-sm-3 control-label')) !!}
                                      <div class=col-sm-7>
                                       <div class="input-group">
                                          <span class="input-group-addon"><img src="/imagenes/CalendarioMes.png"/ height="18px;"></span>
                                         {!!Form::date('tareaFechaInicioPlanMantenimientoAlerta',\Carbon\Carbon::now(),['class'=>'form-control'])!!}
                                       </div>
                                      </div>
                                    {!!Form::label('tareaHoraPlanMantenimientoAlerta', 'Hora Alarma', array('class' => 'col-sm-3 control-label')) !!}
                                      <div class=col-sm-7>
                                        <div class="input-group">
                                            <span class="input-group-addon"><img src="/imagenes/clock.png"/ height="19px;"></span>
                                            {!!Form::time('tareaHoraPlanMantenimientoAlerta','01:00:00',['class'=>'form-control'  ,'max'=>'22:30:00', 'min'=>'01:00:00' ,'step'=>'1'])!!} 
                                        </div>
                                      </div>
                                    {!!Form::label('tareaIntervaloPlanMantenimientoAlerta', 'Cada', array('class' => 'col-sm-3 control-label')) !!}
                                      <div class=col-sm-6>
                                        <div class="input-group">
                                           <span class="input-group-addon"><img src="/imagenes/barras.png"/ height="15px;"></span>
                                           {!!Form::number('tareaIntervaloPlanMantenimientoAlerta',null,['class'=>'form-control','placeholder'=>'Ingresa la periodicidad de dias'])!!}<span class="input-group-addon">Semanas</span>
                                        </div>
                                      </div><br>

                                          <div class="col-sm-12"><br>
                                              <div class="col-sm-2" onclick='numeroDias();'>
                                                  Lunes
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta[]', '1', true)!!}
                                              </div>
                                              <div class="col-sm-2" onclick='numeroDias();'>
                                                  Martes
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta[]', '2', true)!!}
                                              </div>
                                              <div class="col-sm-2" onclick='numeroDias();'>
                                                  Miercoles
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta[]', '3', true)!!}
                                              </div>
                                              <div class="col-sm-2" onclick='numeroDias();'>
                                                  Jueves
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta[]', '4', true)!!}
                                              </div>
                                              <div class="col-sm-2" onclick='numeroDias();'>
                                                  Viernes
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta[]', '5', true)!!}
                                              </div>
                                              <div class="col-sm-2" onclick='numeroDias();'>
                                                  Sabado
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta[]', '6', false)!!}
                                              </div>
                                              <div class="col-sm-2" onclick='numeroDias();'><br>
                                                  Domingo
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta[]', '7', false)!!}
                                              </div> 
                                               {!!Form::hidden('numeroDias',null,['class'=>'form-control','id'=>'numeroDias'])!!}                      
                                          </div>
                                  </div>
                                 </div>
                               </div>
                              </div><!--Finaliza div ProgSemana -->

                              <div class="tab-pane" id="progMes">
                                  <div class="container">
                                  <h4>Programacion Mensual</h4><br><br>
                                      <div class="form-group">
                                         <div class="col-sm-8">
                                          {!!Form::label('tareaFechaInicioPlanMantenimientoAlerta', 'Fecha Inicio', array('class' => 'col-sm-3 control-label')) !!}
                                              <div class=col-sm-7>
                                                  <div class="input-group">
                                                      <span class="input-group-addon"><img src="/imagenes/CalendarioMes.png"/ height="18px;"></span>
                                                      {!!Form::date('tareaFechaInicioPlanMantenimientoAlerta',\Carbon\Carbon::now(),['class'=>'form-control'])!!}
                                                  </div>
                                              </div>
                                          {!!Form::label('tareaHoraPlanMantenimientoAlerta', 'Hora Alarma', array('class' => 'col-sm-3 control-label')) !!}
                                          <div class=col-sm-7>
                                              <div class="input-group">
                                                    <span class="input-group-addon"><img src="/imagenes/clock.png"/ height="19px;"></span>
                                                    {!!Form::time('tareaHoraPlanMantenimientoAlerta','01:00:00',['class'=>'form-control'  ,'max'=>'22:30:00', 'min'=>'01:00:00' ,'step'=>'1'])!!} 
                                              </div>
                                          </div>
                                        <div class="col-sm-12">
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <h6>Enero
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '1', true)!!}
                                          </div>
                                          <div class="col-sm-2"  onclick='numeroMeses();'>
                                          <h6>Febrero
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '2', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <h6>Marzo
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '3', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <h6>Abril
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '4', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <h6>Mayo
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '5', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <h6>Junio
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '6', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'><br>
                                          <h6>Julio
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '7', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <br><h6>Agosto
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '8', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <br><h6>Septiembre
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '9', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <br> <h6>Octubre
                                             {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '10', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <br><h6>Noviembre
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '11', true)!!}
                                          </div>
                                          <div class="col-sm-2" onclick='numeroMeses();'>
                                          <br><h6>Diciembre
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta[]', '12', true)!!}
                                          </div>
                                          {!!Form::hidden('numeroMeses',null,['class'=>'form-control','id'=>'numeroMeses'])!!}
                                        </div><!-- Finaliza div col-sm-8-->
                                      </div><!-- Finaliza div form-group-->
                                  </div><!-- Finaliza div container-->
                              </div><!-- Finaliza div progMes-->
                          </div><!-- Finaliza div tab-content-->
                         </div><!-- Finaliza div col-xs-9-->
                             
                       </div><!-- Finaliza div col-sm-6-->
                      </div><!-- Finaliza div row-->
                    </div><!-- Finaliza div cpestana2-->
                   </div><!--Finaliza div tab_content pestanias principal -->
                    </div><!--Fin div form-group -->
                    </div>
            </div>
        </div><!--Fin div modal-body -->
      </div><!--Fin div modal-content -->
    </div><!--Fin div modal-dialog -->
</div><!--Fin div modal-campos -->


    {!! Form::close() !!}          
</body>
</html>

<div id="ModalCampos" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/mostrarpartesgridselect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>
@stop



