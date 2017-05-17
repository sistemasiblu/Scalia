@extends('layouts.modal')
@section('titulo')<h3 id="titulo"><center>Punto de localización</center></h3>@stop

@section('content')
@include('alerts.request')

<div id='form-section' >

  <fieldset id="ubicacionDocumento-form-fieldset">  

  <?php 
    $localizacion = DB::Select('
      SELECT 
          idDependencia, nombreDependencia, dl.*
      FROM
          dependencialocalizacion dl
              LEFT JOIN
          dependencia d ON dl.Dependencia_idDependencia = d.idDependencia
      ORDER BY nombreDependencia , numeroEstanteDependenciaLocalizacion , numeroNivelDependenciaLocalizacion DESC , numeroSeccionDependenciaLocalizacion');  

    $clocalizacion = array();
    // por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
    for ($i = 0, $c = count($localizacion); $i < $c; ++$i) 
    {
      $clocalizacion[$i] = (array) $localizacion[$i];
    }

    $i = 0;
    $menu = '<ul class="nav nav-tabs">';
    $estructura = '';
    $total = count($localizacion);

    while ($i < $total) 
    {
      $dependencia = $clocalizacion[$i]['idDependencia'];
      if ($i == 0) 
        {
          $menu .= '<li class="active"><a data-toggle="tab" href="#'.$clocalizacion[$i]["idDependencia"].'">'.$clocalizacion[$i]["nombreDependencia"].'</a></li>';
          $estructura .= '<div id="'.$clocalizacion[$i]["idDependencia"].'" class="tab-pane fade in active">';
        }
        else
        {
          $menu .= '<li><a data-toggle="tab" href="#'.$clocalizacion[$i]["idDependencia"].'">'.$clocalizacion[$i]["nombreDependencia"].'</a></li>';
          $estructura .= '<div id="'.$clocalizacion[$i]["idDependencia"].'" class="tab-pane fade">';
        }

        while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia']) 
        {
          $estante = $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'];

          $estructura .= "<table class='table table-bordered' style='width:100%;font-size: 12px;padding: 3px 10px;'>
                            <tr>
                              <td style='background-color: #F2F2F2;vertical-align: inherit;'>
                                <center><b>Estante ".strtoupper($clocalizacion[$i]["numeroEstanteDependenciaLocalizacion"])."</b></center>
                              </td>
                            </tr>";

            while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia'] and $estante == $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion']) 
            {

              $nivel = $clocalizacion[$i]['numeroNivelDependenciaLocalizacion'];

              $estructura .= "<tr>
                                <td style='width:60px;vertical-align: inherit;background-color: #F2F2F2;'>
                                    <center><b>Nivel ".$clocalizacion[$i]["numeroNivelDependenciaLocalizacion"]."</b></center>
                                </td>";

                while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia'] and $estante == $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'] and $nivel == $clocalizacion[$i]['numeroNivelDependenciaLocalizacion']) 
                {

                  $estructura .= "<td style='vertical-align: inherit;'>
                                    <center>Seccion 
                                      ".$clocalizacion[$i]['numeroSeccionDependenciaLocalizacion']."
                                    </center>
                                    <a href='javascript:ConsultarInformacion(".$i.");' style='height:40%; border:1px solid #737373;'>
                                        <label class='form-inline'>
                                          Ubicacion
                                        </label>
                                    </a>
                                    <input type='hidden' value='".$clocalizacion[$i]['idDependenciaLocalizacion']."'>
                                  </td>";
                  $i++;
                }

              $estructura .= "</tr>";

            }

          $estructura .= "</table>";
        }

        $estructura .= '</div>';
    }

  $menu .= '</ul>';

  ?>

  <div class="container-fluid" style="margin-top:10px;">
    <div id="divContenido" style="height:100%;">
        <div id="contenido_pestanas" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php
                echo $menu;
            ?>
        </div>
        <div id="contenido_pestanas" class="tab-content">
        <?php
            echo $estructura; 
        ?>
        </div>
    </div>                
  </div>

  <script type="text/javascript">
    function ConsultarInformacion(i)
    {
      alert(i);
    }
  </script>



<!-- <ul class="nav nav-tabs"> 
  <li class="active"><a data-toggle="tab" href="#div1">Div1</a></li>
  <li><a data-toggle="tab" href="#div2">Div2</a></li>
  <li><a data-toggle="tab" href="#div3">Div3</a></li>
</ul>

<div class="tab-content">
  <div id="div1" class="tab-pane fade in active">
    <table class="table table-striped table-bordered table-hover" style="width:100%;">
        <tr>
          <th>Div1</th>
          <th>Div1</th> 
          <th>Div1</th>
        </tr>
        <tr>
          <td>Jill</td>
          <td>Smith</td>
          <td>50</td>
        </tr>
        <tr>
          <td>Eve</td>
          <td>Jackson</td>
          <td>94</td>
        </tr>
        <tr>
          <td>John</td>
          <td>Doe</td>
          <td>80</td>
        </tr>
      </table>
  </div>

  <div id="div2" class="tab-pane fade">
    <table class="table table-striped table-bordered table-hover" style="width:100%;">
        <tr>
          <th>Div2</th>
          <th>Div2</th> 
          <th>Div2</th>
        </tr>
        <tr>
          <td>Jill</td>
          <td>Smith</td>
          <td>50</td>
        </tr>
        <tr>
          <td>Eve</td>
          <td>Jackson</td>
          <td>94</td>
        </tr>
        <tr>
          <td>John</td>
          <td>Doe</td>
          <td>80</td>
        </tr>
      </table>
  </div>

  <div id="div3" class="tab-pane fade">
    <table class="table table-striped table-bordered table-hover" style="width:100%;">
        <tr>
          <th>Div3</th>
          <th>Div3</th> 
          <th>Div3</th>
        </tr>
        <tr>
          <td>Jill</td>
          <td>Smith</td>
          <td>50</td>
        </tr>
        <tr>
          <td>Eve</td>
          <td>Jackson</td>
          <td>94</td>
        </tr>
        <tr>
          <td>John</td>
          <td>Doe</td>
          <td>80</td>
        </tr>
      </table>
  </div>
</div> -->
    
  </fieldset>

  {!! Form::close() !!}
</div>
@stop