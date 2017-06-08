@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Digitalizar Documento</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::style('css/modal.css'); !!}
{!!Html::script('js/radicado.js')!!}
{!!Html::script('js/dropzone.js'); !!}<!--Llamo al dropzone-->
{!!Html::style('assets/dropzone/dist/min/dropzone.min.css'); !!}<!--Llamo al dropzone-->
{!!Html::style('css/dropzone.css'); !!}<!--Llamo al dropzone-->
{!!Html::style('css/BootSideMenu.css'); !!}
{!!Html::script('js/BootSideMenu.js'); !!}

<!--Con este css ajusto el tamaño del dropzone-->
<style> 
.dropzone-previews 
  { 
    height: 200px; border: groove; 1px black; background-color: white; overflow-y: scroll; 
  } 
</style> 
<?php 
#

$titulos = DB::Select('
  SELECT 
    idDependencia,
    nombreDependencia,
    abreviaturaDependencia,
    idSerie,
    nombreSerie,
    idSubSerie,
    nombreSubSerie,
    idDocumento,
    nombreDocumento
FROM
    retenciondocumental rd
        LEFT JOIN
    dependencia dep ON rd.Dependencia_idDependencia = dep.idDependencia
        LEFT JOIN
    serie s ON rd.Serie_idSerie = s.idSerie
        LEFT JOIN
    subserie ss ON rd.SubSerie_idSubSerie = ss.idSubSerie
        LEFT JOIN
    documento d ON rd.Documento_idDocumento = d.idDocumento
        LEFT JOIN
    dependenciapermiso depp ON dep.idDependencia = depp.Dependencia_idDependencia
        LEFT JOIN
    seriepermiso sp ON s.idSerie = sp.Serie_idSerie
        LEFT JOIN
    subseriepermiso ssp ON ss.idSubSerie = ssp.SubSerie_idSubSerie
        LEFT JOIN
    documentopermiso dp ON d.idDocumento = dp.Documento_idDocumento
        LEFT JOIN
    documentopermisocompania dpc ON d.idDocumento = dpc.Documento_idDocumento
        LEFT JOIN
    users udep ON depp.Rol_idRol = udep.Rol_idRol
        LEFT JOIN
    users us ON sp.Rol_idRol = us.Rol_idRol
        LEFT JOIN
    users uss ON ssp.Rol_idRol = uss.Rol_idRol
        LEFT JOIN
    users ud ON dp.Rol_idRol = ud.Rol_idRol
WHERE
    dpc.Compania_idCompania = '.\Session::get("idCompania").'
        AND udep.id = '.\Session::get("idUsuario").'
        AND us.id = '.\Session::get("idUsuario").'
        AND uss.id = '.\Session::get("idUsuario").'
        AND ud.id = '.\Session::get("idUsuario").' 
        AND cargarDocumentoPermiso = 1
        AND tipoDocumento = 2
GROUP BY idDependencia , idSerie , idSubSerie , idDocumento
ORDER BY nombreDependencia , nombreSerie , nombreSubSerie , nombreDocumento');

$datos = array();
// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
 for ($i = 0, $c = count($titulos); $i < $c; ++$i) 
 {
    $datos[$i] = (array) $titulos[$i];
 }

$i=0;
$registros = count($titulos);

$ns = 0;
$liserie = array();
$divserie = array();

while ($i < $registros) 
{
  $dependencia = $datos[$i]['abreviaturaDependencia'];

  while ($i < $registros && $dependencia == $datos[$i]['abreviaturaDependencia']) 
  {
    $serie = $datos[$i]['nombreSerie'];
    $nss = 0;
    $lisubserie = array();
    $divsubserie = array();

    if(!isset($liserie[$ns]))
      $liserie[$ns] = '';

    if(!isset($divserie[$ns]))
      $divserie[$ns] = '';

    $liserie[$ns] .= '
      <li onclick="asignarIdDependenciaSerie('.$datos[$i]["idDependencia"].','.$datos[$i]["idSerie"].')"><a data-toggle="tab" href="#'.$datos[$i]["idDependencia"].'_'.$datos[$i]["idSerie"].'">'.$datos[$i]["abreviaturaDependencia"].'-'.$datos[$i]["nombreSerie"].'</a></li>';

    $divserie[$ns] .= '
        <div id="'.$datos[$i]["idDependencia"].'_'.$datos[$i]["idSerie"].'" class="tab-pane fade">
          <ul class="nav nav-tabs">';

      while ($i < $registros && $dependencia == $datos[$i]['abreviaturaDependencia'] && $serie = $datos[$i]['nombreSerie']) 
      {
        $subserie = $datos[$i]['nombreSubSerie'];
        if(!isset($lisubserie[$nss]))
          $lisubserie[$nss] = '';

        if(!isset($divsubserie[$nss]))
          $divsubserie[$nss] = '';

        $lisubserie[$nss] .= '
            <li onclick="asignarIdSubSerie('.$datos[$i]["idSubSerie"].')"><a data-toggle="tab" href="#'.$datos[$i]["idSubSerie"].'">'.$datos[$i]["nombreSubSerie"].'</a></li>';
        $divsubserie[$nss] .= '
              <div id="'.$datos[$i]["idSubSerie"].'" class="tab-pane fade">';

        while ($i < $registros && $dependencia == $datos[$i]['abreviaturaDependencia'] && $serie = $datos[$i]['nombreSerie'] && $subserie == $datos[$i]['nombreSubSerie']) 
        {
          $divsubserie[$nss] .= 
              '<div class="panel-body">
                <div class="panel-group" id="accordion">
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#documento_'.$datos[$i]["idDocumento"].'">'.$datos[$i]["nombreDocumento"].'</a>
                      </h4>
                    </div>
                    <div id="documento_'.$datos[$i]["idDocumento"].'" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id="test">
                          <div class="col-sm-12">
                            
                            <div class="input-group"">
                              <div class="container">
                                <div class="dropzone dropzone-previews" id="dropzoneFileUpload_'.$datos[$i]["idDocumento"].'" >
                                </div>
                              </div>  
                              </br>
                               <input id="botonRadicar_'.$datos[$i]['idDocumento'].'" type="button" value="Digitalizar sin adjunto" class="btn btn-primary" onclick="limpiarDivPreview(); radicar(\'\', this.id); irArriba();">
                            </div>

                          </div>
                        </div>
                      </div>
                    </div>
                  </div>  
                </div>
              </div>
          
          <script type="text/javascript">
            var baseUrl = "http://'.$_SERVER["HTTP_HOST"].'";
            var token = "'.Session::getToken().'";
            Dropzone.autoDiscover = false;

            var myDropzone = new Dropzone("div#dropzoneFileUpload_'.$datos[$i]["idDocumento"].'", 
            {
              url: baseUrl + "/dropzone/uploadFilesRadicado",
              params: 
              {
                _token: token
              }
            });

            myDropzone.options.myAwesomeDropzone =  
            {
              paramName: "file", 
              maxFilesize: 20,
              addRemoveLinks: true,
              clickable: true,
              previewsContainer: ".dropzone-previews",
              clickable: false,
              accept: function(file, done) 
              {

              }
            };

            myDropzone.on("addedfile", function(file) 
            {
              file.previewElement.addEventListener("click", function(reg, idDoc) 
              {
                var idDrop = this.parentNode.id;
                radicar(file, idDrop);
                irArriba();
              });
            });
          </script>';

          $i++;  
        }
        $divsubserie[$nss] .= '</div>';
        $nss ++;
      }

      $divserie[$ns] .= implode('',$lisubserie).
                      '</ul>
                      <div class="tab-content">'.
                      implode('',$divsubserie).'
                      </div>
                    </div>';
      $ns ++;
  }
}

echo '
<div id="form-radicado">
  <ul class="nav nav-tabs">
    '.implode('', $liserie).'
  </ul>
    <div class="tab-content">
      '.implode('', $divserie).'
    </div>
</div>';
?>

{!!Form::open(['route'=>'radicado.store','method'=>'POST', 'action' => 'RadicadoController@store', 'id' => 'radicado' , 'files' => true])!!}

{!!Form::hidden('registro', 0, array('id' => 'registro'))!!}
{!!Form::hidden('archivoRadicado', 0, array('id' => 'archivoRadicado'))!!}
{!!Form::hidden('Dependencia_idDependencia', null, array('id' => 'Dependencia_idDependencia'))!!}
{!!Form::hidden('Serie_idSerie', null, array('id' => 'Serie_idSerie'))!!}
{!!Form::hidden('SubSerie_idSubSerie', null, array('id' => 'SubSerie_idSubSerie'))!!}
{!!Form::hidden('Documento_idDocumento', null, array('id' => 'Documento_idDocumento'))!!} 
{!!Form::hidden('numeroRadicadoVersion', '1.0', array('id' => 'numeroRadicadoVersion'))!!} 
{!!Form::hidden('tipoRadicadoVersion', 0, array('id' => 'tipoRadicadoVersion'))!!} 
<input type="hidden" id="token" value="{{csrf_token()}}"/>
<!-- Modal de radicado -->
  <div id="myModalRadicado" class="modalDialog" style="display:none;">
    <div id="modal-dialog">
    <div class="modal-header">
      <a onclick="cerrarModal()" title="Cerrar" class="close">X</a>
      <h4 class="modal-title">Digitalizar documento</h4>
    </div>
      <div class="modal-radicado">

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('codigoRadicado', 'Código', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode "></i>
              </span>
                {!!Form::text('codigoRadicado',null,['class'=>'form-control','readonly','placeholder'=>'Código de radicado'])!!}
                {!! Form::hidden('idRadicado', null, array('id' => 'idRadicado')) !!} 
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('fechaRadicadoVersion', 'Fecha', array('class' => 'col-sm-3 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar "></i>
              </span>
                {!!Form::text('fechaRadicadoVersion',date('Y-m-d H:m:s') ,['class'=>'form-control','readonly','placeholder'=>'Fecha de radicado',])!!}
            </div>
          </div>
        </div>

        <div class="form-group col-md-6" id='test'>
          {!!Form::label('ubicacionEstanteRadicado', 'P.L', array('class' => 'col-sm-3 control-label'))!!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-sitemap"></i>
              </span>
            {!!Form::text('ubicacionEstanteRadicado',null,['class'=>'form-control', 'placeholder'=>' Punto de localización'])!!}
            <span title="Crear punto de localización" class="input-group-addon btn btn-primary" 
            onclick="mostrarModalPL()"
            style="cursor:pointer">
              <i class="fa fa-check"></i>
            </span>
          </div>
        </div>
      </div>

      <div class="form-group col-md-6" id='test'> 
        {!!Form::label('numeroPaginasRadicado', 'Páginas', array('class' => 'col-sm-3 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-file"></i>
            </span>
              {!!Form::text('numeroPaginasRadicado',null,['class'=>'form-control','placeholder'=>'Número de páginas'])!!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-12" id='test'>
        {!!Form::label('ubicacionEtiquetaRadicado', 'Ubicación', array('class' => 'col-sm-3 control-label'))!!}
        <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-exchange"></i>
              </span>
              Izquierda Derecha <br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {!! Form::radio('ubicacionEtiquetaRadicado', 'derecha', true, ['onclick' => 'asignarCheck(this)']) !!}
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              {!! Form::radio('ubicacionEtiquetaRadicado', 'derecha', false, ['onclick' => 'asignarCheck(this)']) !!}
          </div>
        </div>
      </div>

      <div class="form-group col-md-12" id='test'> 
        {!! Form::label('etiquetaRadicado', 'Etiquetas', array('class' => 'col-sm-3 control-label')) !!}
        <div class="col-sm-11">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-tags"></i>
            </span>
            {!!Form::text('nombreEtiqueta',null,['id' => 'nombreEtiqueta','class'=>'form-control','onclick' => 'mostrarModalEtiqueta()', 'readonly', 'placeholder'=>'Seleccione las etiquetas'])!!}
            {!!Form::hidden('etiquetaRadicado', null, array('id' => 'etiquetaRadicado'))!!}
          </div>
        </div>
      </div>

        <div class="form-group">
          <div class="col-md-12">
            <div class="panel panel-primary">
              <div class="panel-heading">Indexación</div>
              <div class="panel-body">
                <div class="panel-group" id="accordion">
                  <div id="divMetadatos">
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="preview" class="col-md-6">
          <div style="clear:both">
            <iframe id="viewer" frameborder="0" scrolling="no" width="100%" height="490px"></iframe>    
          </div>
        </div>

        {!!Form::button('Adicionar',["class"=>"btn btn-primary", "onclick"=>"guardarDatos();"])!!}

      </div>
    </div>
</div>
@stop

<!-- Modal etiqueta -->
<div id="myModalEtiqueta" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:1000px;">

    <!-- Modal content-->
    <div style="" class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecciona las etiquetas</h4>
      </div>
      <div class="modal-body">
        <iframe style="width:100%; height:510px; z-index: 964790"; id="etiqueta" name="etiqueta" src="{!! URL::to ('etiquetaselect')!!}"> </iframe> 
      </div>
    </div>
  </div>
</div>   

<!-- Modal PL -->
<div id="myModalPL" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:90%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Punto de localización</h4>
      </div>
      <div class="modal-body">
        <?php
          echo '<iframe style="width:100%; height:400px; " id="localizacion" name="localizacion" src="http://'.$_SERVER["HTTP_HOST"].'/puntolocalizacion?tipo=\'digital\'"></iframe>';
        ?>
      </div>
    </div>
  </div>
</div>   