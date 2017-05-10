<?php
/*echo $plmantenimiento;
echo "<hr>";
echo $activo;
echo "<hr>";
echo $parte;
echo "<hr>";
echo $tipoaccion;
echo "<hr>";
echo $tiposervicio;
echo "<hr>";
/*return;*/
?>
<style>
  .tabs-left, .tabs-right 
  {
    border-bottom: none;
    padding-top: 2px;
  }
  .tabs-left 
  {
    border-right: 1px solid #ddd;
  }
  .tabs-right 
  {
    border-left: 1px solid #ddd;
  }
  .tabs-left>li, .tabs-right>li 
  {
    float: none;
    margin-bottom: 2px;
  }
  .tabs-left>li 
  {
    margin-right: -1px;
  }
  .tabs-right>li 
  {
    margin-left: -1px;
  }
  .tabs-left>li.active>a,
  .tabs-left>li.active>a:hover,
  .tabs-left>li.active>a:focus 
  {
    border-bottom-color: #ddd;
    border-right-color: transparent;
  }

  .tabs-right>li.active>a,
  .tabs-right>li.active>a:hover,
  .tabs-right>li.active>a:focus 
  {
    border-bottom: 1px solid #ddd;
    border-left-color: transparent;
  }
  .tabs-left>li>a {
    border-radius: 4px 0 0 4px;
    margin-right: 0;
    display:block;
  }
  .tabs-right>li>a 
  {
    border-radius: 0 4px 4px 0;
    margin-right: 0;
  }
  .vertical-text 
  {
    margin-top:50px;
    border: none;
    position: relative;
  }
  .vertical-text>li 
  {
    height: 20px;
    width: 120px;
    margin-bottom: 100px;
  }
  .vertical-text>li>a 
  {
    border-bottom: 1px solid #ddd;
    border-right-color: transparent;
    text-align: center;
    border-radius: 4px 4px 0px 0px;
  }
  .vertical-text>li.active>a,
  .vertical-text>li.active>a:hover,
  .vertical-text>li.active>a:focus 
  {
    border-bottom-color: transparent;
    border-right-color: #ddd;
    border-left-color: #ddd;
  }
  .vertical-text.tabs-left 
  {
    left: -50px;
  }
  .vertical-text.tabs-right 
  {
    right: -50px;
  }
  .vertical-text.tabs-right>li 
  {
    -webkit-transform: rotate(90deg);
    -moz-transform: rotate(90deg);
    -ms-transform: rotate(90deg);
    -o-transform: rotate(90deg);
    transform: rotate(90deg);
  }
  .vertical-text.tabs-left>li 
  {
    -webkit-transform: rotate(-90deg);
    -moz-transform: rotate(-90deg);
    -ms-transform: rotate(-90deg);
    -o-transform: rotate(-90deg);
    transform: rotate(-90deg);
  }   
</style>
<script>

  var partesPlanMantenimiento = '<?php echo (isset($plmantenimiento) ? json_encode($plmantenimiento->partesPlanMantenimiento) : "");?>';
  partesPlanMantenimiento = (partesPlanMantenimiento != '' ? JSON.parse(partesPlanMantenimiento) : '');

  var valorpartesPlanMantenimiento = [0,0,''];
  $(document).ready(function()
  {
      partes=new Atributos('partes','contenedor-partes','partes-');
      partes.campoid = 'idPlanMantenimientoParte';
      partes.campoEliminacion = 'parteEliminar';
      partes.campos=['idPlanMantenimientoParte','nombreActivo'];
      partes.etiqueta=['input','input'];
      partes.tipo=['hidden',''];
      partes.estilo=['','width:200px; height:35px;'];
      partes.clase=['',''];
      partes.sololectura=[false,false];
      partes.completar=['off','off'];
      partes.funciones=['','',''];
      var idPlanMantenimientoParte = '<?php echo isset($idPlanMantenimientoParte) ? $idPlanMantenimientoParte : "";?>';
      var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreTipoActivo : "";?>';

      for(var j=0; j < partesPlanMantenimiento.length; j++)
      {
         partes.agregarCampos(JSON.stringify(partesPlanMantenimiento[j]),'L');
      }

  });
  
     function abrirModalCampos()
      {
        $('#ModalCampos').modal('show');
      }



</script>
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

@extends('layouts.vista')
@section('titulo')<br><h4 id="titulo"><center>Plan Mantenimiento</center></h4>@stop
@section('content')
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Plan Mantenimiento</title>

</head>
<body >
<div class="container">
  <br><br><br><br>
    <div class='form-group'>
    <div class="col-sm-12">
        {!!Form::label('Activo_idActivo', 'Nombre', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            {!!Form::select('Activo_idActivo', @$activo, @$plmantenimiento->Activo_idActivo,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('Activo_idParte', 'Parte', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            {!!Form::select('Activo_idParte', @$parte, @$plmantenimiento->Activo_idActivo,['class' => 'form-control'])!!}
          </div>
        {!!Form::label('actividadPlanMantenimiento', 'Actividad', array('class' => 'col-sm-4 control-label')) !!}     
          <div class="col-sm-8">
            {!!Form::select('actividadPlanMantenimiento',['Revision General'=>'Revision General', 'Mantenimiento'=>'Mantenimiento','Reparacion'=>'Reparacion'],null,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}  
          </div>       
        {!!Form::label('programacion', 'Programacion', array('class' => 'col-sm-4 control-label')) !!}
          <div class="col-sm-8">
            <div class="input-group" >
              {!!Form::text('programacion',null,['class'=>'form-control','readonly'=>'true', 'style'=>'background-color:white;'])!!}<span class="input-group-addon" onclick="abrirModalCampos();" style="cursor:pointer;"><img src="/imagenes/barras.png"  height="15px;" /></span>        
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

        <div id="pestanas">
          <ul id=lista class="nav nav-tabs">
            <li class="active" id="pestana1"><a data-toggle="tab" href='#cpestana1'>Partes</a></li>
            <li id="pestana2"><a data-toggle="tab" href='#cpestana2'>Procedimientos</a></li>
          </ul>
        </div> 

        <div class="tab-content" id="contenidopestanas">
          <div class="tab-pane fade in active" id="cpestana1">
               <br><br>
          
            <fieldset id='varioslistachequeo-form-fieldset'>
                <div class="form-group"  id='test'>
                    <div class="col-sm-12">
                        <div class="row show-grid">
                            <div class="col-md-1" style="width: 40px;height: 35px;" >
                                <span class="glyphicon glyphicon-plus" onclick="partes.agregarCampos(valorpartesPlanMantenimiento,'A')" ></span>
                            </div>
                            <div class="col-md-1" style="width: 200px;height: 35px;"><b>Nombre Parte</b></div>
                            <div id="contenedor-partes"></div>      
                        </div>
                    </div>
                </div>
            </fieldset>
         
         </div>
          <div class="tab-pane fade" id="cpestana2">
            {!!Form::label('procedimientoPlanMantenimiento', 'Procedimientos', array('class' => 'col-sm-4 control-label')) !!}
              <div class="col-sm-12">
                {!!Form::textarea('procedimientoPlanMantenimiento', null,['class' => 'ckeditor'])!!}
              </div>
          </div>
          </div>
    </div><!--Fin div col-sm-12 -->
  </div> <!--Fin div form-group -->
  @if(isset($plmantenimiento))
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
</body>
</html>
@stop
<div id="ModalCampos" class="modal fade" role="dialog" style="display: none;">
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
                      <div class="col-sm-12" position="left">
                        {!!Form::label('nombrePlanMantenimiento', 'Descripcion', array('class' => 'col-sm-2 control-label')) !!}
                        <div class="col-sm-8">
                          {!!Form::text('nombrePlanMantenimiento',null,['class'=>'form-control','placeholder'=>'Ingresa la descripcion'])!!}
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
                                              {!!Form::checkbox('tareaDiaLaboralPlanMantenimientoAlerta', 'value', true,['class' => 'form-control'])!!}
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
                                              <div class="col-sm-2">
                                                  Lunes
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta', 'Lunes', true)!!}
                                              </div>
                                              <div class="col-sm-2">
                                                  Martes
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta', 'Martes', true)!!}
                                              </div>
                                              <div class="col-sm-2">
                                                  Miercoles
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta', 'Miercoles', true)!!}
                                              </div>
                                              <div class="col-sm-2">
                                                  Jueves
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta', 'Jueves', true)!!}
                                              </div>
                                              <div class="col-sm-2">
                                                  Viernes
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta', 'Viernes', true)!!}
                                              </div>
                                              <div class="col-sm-2">
                                                  Sabado
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta', 'Sabado', false)!!}
                                              </div>
                                              <div class="col-sm-2"><br>
                                                  Domingo
                                                  {!!Form::checkbox('tareaDiasPlanMantenimientoAlerta', 'Domingo', false)!!}
                                              </div>                        
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
                                          <div class="col-sm-2">
                                          <h6>Enero
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Enero', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <h6>Febrero
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Febrero', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <h6>Marzo
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Marzo', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <h6>Abril
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Abril', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <h6>Mayo
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Mayo', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <h6>Junio
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Junio', true)!!}
                                          </div>
                                          <div class="col-sm-2"><br>
                                          <h6>Julio
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Julio', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <br><h6>Agosto
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Agosto', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <br><h6>Septiembre
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Septiembre', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <br> <h6>Octubre
                                             {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Octubre', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <br><h6>Noviembre
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Noviembre', true)!!}
                                          </div>
                                          <div class="col-sm-2">
                                          <br><h6>Diciembre
                                              {!!Form::checkbox('tareaMesesPlanMantenimientoAlerta', 'Diciembre', true)!!}
                                          </div>
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
</div>
</div>
