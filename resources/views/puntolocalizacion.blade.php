@extends('layouts.modal')
@section('titulo')<h3 id="titulo"><center>Punto de localización</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/puntolocalizacion.js')!!}

<div id='form-section' >

  <fieldset id="ubicacionDocumento-form-fieldset">  
  <input type="hidden" id="token" value="{{csrf_token()}}"/>

  <?php 
    $localizacion = DB::Select('
      SELECT 
          idDependencia, nombreDependencia, dl.*
      FROM
          dependencialocalizacion dl
              LEFT JOIN
          dependencia d ON dl.Dependencia_idDependencia = d.idDependencia
      GROUP BY idDependencia');  

    $clocalizacion = array();
    // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
    for ($i = 0, $c = count($localizacion); $i < $c; ++$i) 
    {
      $clocalizacion[$i] = (array) $localizacion[$i];
    }

    $select = '<select class="form-control" onchange="cargarEstanteDependencia(this.value, 001)">
    <option value="" disabled selected>Seleccione una dependencia</option>';

    for ($i=0; $i < count($localizacion); $i++) 
    { 
      $select .= '<option value="'.$clocalizacion[$i]["idDependencia"].'">'.$clocalizacion[$i]["nombreDependencia"].'</option>';
    }

    $select .= '</select>';
  ?>

  <div class="form-group" id='test'>
    {!!Form::label('Dependencia', 'Dependencia', array('class' => 'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-bank"></i>
        </span>
        <?php
          echo $select
        ?>
      </div>
    </div>
  </div>

  

  <div class="container-fluid" style="margin-top:10px;">
    <div id="botones">
    </div>
    <div id="divContenido" style="height:100%;">
        <div id="contenido_pestanas" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php
                // echo $menu;
            ?>
        </div>
        <div id="contenidoEstante" class="tab-content">
        <?php
            // echo $estructura; 
        ?>
        </div>
    </div>                
  </div>

  </fieldset>

  {!! Form::close() !!}
</div>
@stop