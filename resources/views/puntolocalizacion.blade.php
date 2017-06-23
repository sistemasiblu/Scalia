@extends('layouts.modal')
@section('titulo')<h3 id="titulo"><center>Inventario Documental</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/puntolocalizacion.js')!!}



<div id='form-section' >

  <fieldset id="ubicacionDocumento-form-fieldset">  
  <input type="hidden" id="token" value="{{csrf_token()}}"/>

<script>
  $(document).ready( function () {

      setInterval(function cargarEstanteDependencia(idDependencia, numeroEstante, tipoInventario)
      {
        idDependencia = $("#idDependencia").val();
        numeroEstante = ($("#estanteLocalizacion").val() == '' ? '001' : $("#estanteLocalizacion").val());
        tipoInventario = <?php echo $_GET['tipo']; ?>;
        var token = document.getElementById('token').value;

          $.ajax({
                  headers: {'X-CSRF-TOKEN': token},
                  dataType: "json",
                  data: {'idDependencia': idDependencia, 'numeroEstante' : numeroEstante, 'tipoInventario': tipoInventario},
                  url:   'http://'+location.host+'/cargarEstanteDependencia/',
                  type:  'post',
                  beforeSend: function(){
                      //Lo que se hace antes de enviar el formulario
                      },
                  success: function(respuesta){
                      $("#botones").html(respuesta['boton']);
                      $("#contenidoEstante").html(respuesta['estructura']);
                  },
                  error:    function(xhr,err){ 
                      alert("Error");
                  }
              });
      }, 30000);
    });
</script>

  <?php 
    $tipoInventario = $_GET['tipo'];

    $localizacion = DB::Select('
      SELECT 
          idDependencia, nombreDependencia, dl.*
      FROM
          dependencialocalizacion dl
              LEFT JOIN
          dependencia d ON dl.Dependencia_idDependencia = d.idDependencia
            LEFT JOIN
          dependenciapermiso depp ON d.idDependencia = depp.Dependencia_idDependencia
            LEFT JOIN
          users ud ON depp.Rol_idRol = ud.Rol_idRol
      WHERE ud.id = '.\Session::get("idUsuario").'
      GROUP BY idDependencia');  

    $clocalizacion = array();
    // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
    for ($i = 0, $c = count($localizacion); $i < $c; ++$i) 
    {
      $clocalizacion[$i] = (array) $localizacion[$i];
    }

    $select = '<select id="idDependencia" class="form-control" onchange="llenarCampoEstante(001); cargarEstanteDependencia(this.value, 001, '.$tipoInventario.')">
    <option value="0">Seleccione una dependencia</option>';

    for ($i=0; $i < count($localizacion); $i++) 
    { 
      $select .= '<option value="'.$clocalizacion[$i]["idDependencia"].'">'.$clocalizacion[$i]["nombreDependencia"].'</option>';
    }

    $select .= '</select>';
  ?>

  <div class="form-group col-md-8" id='test'>
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

<?php 
  $style = 'width: 13px; height: 13px; -moz-border-radius: 50%; -webkit-border-radius: 50%; border-radius: 50%; display:inline-block;'
?>

  <div class="form-group col-md-4" id='test'>
  <span>Convención de colores</span>
    <div class="btn-group" title="Convención de colores">
        <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
            <i class="fa fa-pie-chart"></i> 
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" role="menu">
            <li><a class="toggle-vis" data-column="0"><label> Disponible <div style="background-color:#A9F5A9; <?php echo $style ?>"></div></label></a></li>
            <li><a class="toggle-vis" data-column="1"><label> Llena <div style="background-color:white; border: 1px solid; <?php echo $style ?>"></div></label></a></li>
            <li><a class="toggle-vis" data-column="2"><label> Ocupada <div style="background-color:#F5A9A9; <?php echo $style ?>"></div></label></a></li>
            <li><a class="toggle-vis" data-column="3"><label> Destruída <div style="background-color:#F2F5A9; <?php echo $style ?>"></div></label></a></li>
            <li><a class="toggle-vis" data-column="4"><label> Prestada <div style="background-color:#A9BCF5; <?php echo $style ?>"></div></label></a></li>
            <li><a class="toggle-vis" data-column="5"><label> Extraviada <div style="background-color:#E6E6E6; <?php echo $style ?>"></div></label></a></li>
            <li><a class="toggle-vis" data-column="6"><label> Deteriorada <div style="background-color:#A9F5F2; <?php echo $style ?>"></div></label></a></li>
            <li><a class="toggle-vis" data-column="7"><label> Inactiva <div style="background-color:#C0C0C0; <?php echo $style ?>"></div></label></a></li>
        </ul>
    </div>
  </div>

  <br><br>

  <div class="container-fluid" style="margin-top:10px;">
    <div id="botones">
    </div>
    <div id="divContenido" style="height:100%;">
        <div id="contenido_pestanas" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <input type="hidden" id="estanteLocalizacion" value="">
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
<!-- Modal PL -->
<div id="myModalUbicacion" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:80%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Ubicación del documento</h4>
      </div>
      <div id="bodyUbicacion" class="modal-body">
        <?php 
          // echo '<iframe style="width:100%; height:510px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/ubicaciondocumentomodal?tipo=categoriaagenda"></iframe>'
        ?>
      </div>
    </div>
  </div>
</div>   