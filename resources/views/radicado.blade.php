@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Adjuntar Documento</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/radicado.js')!!}
{!!Html::script('js/dropzone.js'); !!}<!--Llamo al dropzone-->
{!!Html::style('assets/dropzone/dist/min/dropzone.min.css'); !!}<!--Llamo al dropzone-->
{!!Html::style('css/dropzone.css'); !!}<!--Llamo al dropzone-->
{!!Html::style('css/BootSideMenu.css'); !!}
{!!Html::script('js/BootSideMenu.js'); !!}
{!!Html::style('css/cerrardivs.css'); !!}

<!--Con este css ajusto el tamaño del dropzone-->
<style> 

.dropzone-previews 
  { height: 200px; border: groove; 1px black; background-color: white; overflow-y: scroll; } 
</style> 
<input id="tipoFormulario" type="hidden" readonly="true" value="radicado">
 <div class="container-fluid">
  <div class="row">
    <div class="col-lg-12" style="height:500px;">
      <div class="row">
        
        <div class="form-group">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading"><h4>Documentos</h4></div>
              <div class="panel-body">             

<?php 
//Realizo una consulta a la bd para saber que usuario tiene permiso a que documento mediante el idRol 
//y teniendo en cuenta que ese documento este en el CCD y en la sub serie para que el usuario lo pueda ver a la hora de radicar

$Rol = DB::Select('Select Rol_idRol from users where id = '.Session::get('idUsuario'));
$idRol = get_object_vars($Rol[0]);

$titulo = DB::Select('
  SELECT 
    idDocumento, nombreDocumento
FROM
    users u
        LEFT JOIN
    rol r ON u.Rol_idRol = r.idRol
        LEFT JOIN
    dependenciapermiso dp ON dp.Rol_idRol = r.idRol
        LEFT JOIN
    seriepermiso sp ON sp.Rol_idRol = r.idRol
        LEFT JOIN
    subseriepermiso ssp ON ssp.Rol_idRol = r.idRol
        LEFT JOIN
    subserie ss ON ssp.Subserie_idSubserie = ss.idSubSerie
        LEFT JOIN
    documentopermiso docp ON docp.Rol_idRol = r.idRol
        LEFT JOIN
    documento d ON docp.Documento_idDocumento = d.idDocumento
        LEFT JOIN
    documentopermisocompania dpc ON dpc.Documento_idDocumento = d.idDocumento
        LEFT JOIN
    compania c ON dpc.Compania_idCompania = c.idCompania
        LEFT JOIN
    clasificaciondocumental cd ON cd.Subserie_idSubserie = ss.idSubSerie
WHERE
    idCompania = '.Session::get("idCompania").' AND dp.Rol_idRol = '.$idRol["Rol_idRol"].' 
        AND sp.Rol_idRol = '.$idRol["Rol_idRol"].'
        AND ssp.Rol_idRol = '.$idRol["Rol_idRol"].'
        AND docp.Rol_idRol = '.$idRol["Rol_idRol"].'
        AND cargarDocumentoPermiso = 1
        AND tipoDocumento = 2
GROUP BY docp.Documento_idDocumento
');
  
    //Cuento cuantos documentos hay 
    for($i = 0; $i < count($titulo); $i++)
    {
//Convertir un array a un string      
$nombretitulo = get_object_vars($titulo[$i]);
    echo '
      <div class="panel-group" id="accordion">
        <div class="panel panel-info">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" href="#adjuntarRadicado'.$i.'">'.$nombretitulo["nombreDocumento"].'</a>
            </h4>
          </div>
          <div id="adjuntarRadicado'.$i.'" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                  <div class="col-sm-1">
                    <div class="input-group"">
                     <div class="container">
                      <div class="dropzone dropzone-previews" id="dropzoneFileUpload_'.$i.'_'.$nombretitulo["idDocumento"].'" >
                      </div>
                        </div>  
                        </br>
                       <input id="botonRadicar_'.$i.'_'.$nombretitulo['idDocumento'].'" type="button" value="Radicar sin adjunto" class="btn btn-primary" onclick="limpiarDivPreview(); Radicar(\'\', this.id); irArriba();">
                    </div>
                  </div>
                </div>
            </div>
        </div>  
      </div>
    </div>';

?>
<!--Llamo una libreria del dropzone-->
{!!Html::script('js/dropzone.js'); !!}<!--Llamo al dropzone-->
 <!--Funcion de laravel para llamar la fecha actual-->
 <?php $fechahoy = Carbon\Carbon::now();?>
<script type="text/javascript">
    var baseUrl = "{{ url("/") }}";
    var token = "{{ Session::getToken() }}";
    Dropzone.autoDiscover = false;
   //Le doy un nombre al dropzone (id)
    var myDropzone = new Dropzone("div#dropzoneFileUpload_<?php echo $i.'_'.$nombretitulo['idDocumento'];?>", {
        url: baseUrl + "/dropzone/uploadFilesRadicado",
        params: {
            _token: token
        }
        
    });

   
    //Configuro el dropzone
    myDropzone.options.myAwesomeDropzone =  {
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 20, // MB
    addRemoveLinks: true,
    clickable: true,
    previewsContainer: ".dropzone-previews",
    clickable: false,
    accept: function(file, done) {

      }
    };
    //envio las funciones al realizar cuando se de clic en la vista previa dentro del dropzone
     myDropzone.on("addedfile", function(file) {
          file.previewElement.addEventListener("click", function(reg, idDoc) {
            var idDrop = this.parentNode.id; //Con el indexOf obtengo la posicion (en este caso el numero (id) del dropzone)
            Radicar(file, idDrop);
            irArriba();
          });
        });

     function Radicar(file, idDrop)
     {
        $('#pestanas').append('<input type="hidden" name="idDropzone" id="idDropzone" value=""/>');

        $('#idDropzone').val(idDrop);


        if(file != '')
        {
            PreviewImage(file); //Vista previa en tamaño mayor
            document.getElementById("archivoRadicado").value = file["name"]; //Envio el nombre del archivo   

        }
        else
        {
          document.getElementById("archivoRadicado").value = ''; 
        }           
            
        idDrop = idDrop.substring( idDrop.indexOf("_")+1); //Le quito el nombre hasta el guion bajo para que me quede solo el numero (id)
        document.getElementById('Documento_idDocumento').value = idDrop.substring( idDrop.indexOf("_")+1); //Envio el id del documento
        Documento_idDocumentoP = idDrop.substring( idDrop.indexOf("_")+1); //Envio el id del documento
        
        document.getElementById('registro').value = idDrop.substring( 0, idDrop.indexOf("_")); //envio el id del registro
        document.getElementById("pestanas").style.display = "block"; //Al dar clic se abre el div 
        var token = document.getElementById('token').value;
        
        //Saber la ip del servidor
        var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

        $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: {'Documento_idDocumentoP': Documento_idDocumentoP},
                url:   ip+'/armarMetadatosDocumento/',
                type:  'post',
                beforeSend: function(){
                    //Lo que se hace antes de enviar el formulario
                    },
                success: function(respuesta){
                    //lo que se si el destino devuelve algo
                    $("#propiedades").html(respuesta);
                    // $("#nombreEstructuraPadre").style.width = 100%;
                },
                error:    function(xhr,err){ 
                    alert("Error");
                }
            });
      }
      
</script>
<?php
}
    ?>

    <script type="text/javascript">
       //Contiene la vista previa del documento adjunto
       function PreviewImage(archivo) {

                pdffile=archivo;
                pdffile_url=URL.createObjectURL(pdffile);
                $('#viewer').attr('src',pdffile_url);
            }
      </script>

  
            </div>


          </div>
        </div>
      </div>
      </div>
    </div>
    
  </div>
</div>

  @if(isset($radicado))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($radicado,['route'=>['radicado.destroy',$radicado->id],'method'=>'DELETE', 'action' => 'RadicadoController@store', 'files' => true])!!}
    @else
      {!!Form::model($radicado,['route'=>['radicado.update',$radicado->id],'method'=>'PUT', 'action' => 'RadicadoController@store', 'files' => true])!!}
    @endif
  @else
    {!!Form::open(['route'=>'radicado.store','method'=>'POST', 'action' => 'RadicadoController@store', 'id' => 'radicado' , 'files' => true])!!}
  @endif
</script>
<?php 
$codigoRadicado = DB::table('radicado')
->select(DB::raw('Dependencia_idDependencia','Serie_idSerie','SubSerie_idSubSerie'))
->get();
?>
<!--Creo el div que contiene las dos pestañas (Clasificacion y Propiedades)-->
<div class="col-sm-10" id="pestanas" style="width: 1345px; height:600px; background-color: white; z-index: 1000 ; border: 1px inset; border-color: #ddd; position: absolute; top: 115px; display: none;">
<a class='cerrar' href='javascript:void(0);' onclick='document.getElementById(&apos;pestanas&apos;).style.display = &apos;none&apos;'>x</a> <!--Es la funcion la cual cierra el div flotante-->
<div class="col-md-12">
<h2 id="titulo"><left>Radicar<h5>
<!-- <label class= "col-sm-12 control-label">Versión</label> -->
<div class="input-group">
<!-- <span class="input-group-addon">
<i class="fa fa-bars"></i>
</span> -->
  <input id="numeroRadicadoVersion" name="numeroRadicadoVersion" style="height:30px; width:80px;" type="hidden" readonly="true" value="1.0">
<input id="tipoRadicado" name="tipoRadicado" type="hidden"  value="radicado">
<input id="tipoRadicadoVersion" name="tipoRadicadoVersion" style="height:30px; width:80px;" type="hidden" value="0">
</div>
</h5></left></h2>
<div class="form-group col-md-6 form-inline" id='test'> <!--Encabezado dentro del div-->
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
<div class="form-group col-md-6 form-inline" id='test'>
  {!!Form::label('fechaRadicado', 'Fecha', array('class' => 'col-sm-3 control-label')) !!}
  <div class="col-sm-10">
    <div class="input-group">
      <span class="input-group-addon">
        <i class="fa fa-calendar "></i>
      </span>
        {!!Form::text('fechaRadicado',date('Y-m-d') ,['class'=>'form-control','readonly','placeholder'=>'Fecha de radicado',])!!}
    </div>
  </div>
</div>
  </br>
  </br>
  </br>
  </br>

<ul class="nav nav-tabs"> <!--Pestañas de navegacion-->
  <li class="active"><a data-toggle="tab" href="#clasificacion">Clasificación</a></li>
  <li><a data-toggle="tab" href="#propiedades">Propiedades</a></li>
</ul>
</br>

<div class="tab-content">
  <div id="clasificacion" class="tab-pane fade in active">
    <div id='form-radicado'>

  <fieldset id="archivo-form-fieldset"> 
</br>
<!--Campos de la pestaña clasificacion-->
        <div class="form-group" id='test'>
            {!!Form::label('Dependencia_idDependencia', 'Dependencia', array('class' => 'col-sm-2 control-label'))!!}
            <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-pencil-square-o"></i>
                        </span>
                {!!Form::select('Dependencia_idDependencia',$dependencia, (isset($archivo) ? $archivo->Dependencia_idDependencia : 0),["class" => "form-control", "onchange"=> "buscarDependencia(this.value)", "placeholder" =>"Seleccione la dependencia"])!!}
              </div>
            </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('Serie_idSerie', 'Serie', array('class' => 'col-sm-2 control-label'))!!}
          <div class="col-sm-10">
                  <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-pencil-square-o"></i>
                      </span>
              {!!Form::select('Serie_idSerie',$serie, (isset($archivo) ? $archivo->Serie_idSerie : 0),["class" => "form-control", "onchange"=> "buscarSubSerie(this.value)", "placeholder" =>"Seleccione la serie"])!!}
            </div>
          </div>
        </div>


        <div class="form-group" id='test'>
          {!!Form::label('SubSerie_idSubSerie', 'Sub Serie', array('class' => 'col-sm-2 control-label'))!!}
          <div class="col-sm-10">
                  <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-pencil-square-o"></i>
                      </span>
              {!!Form::select('SubSerie_idSubSerie',$subserie, (isset($archivo) ? $archivo->SubSerie_idSubSerie : 0),["class" => "form-control", "placeholder" =>"Seleccione la sub serie"])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('ubicacionEstanteRadicado', 'P.L', array('class' => 'col-sm-2 control-label'))!!}
          <div class="col-sm-10">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-sitemap"></i>
                </span>
              {!!Form::text('ubicacionEstanteRadicado',null,['class'=>'form-control', 'placeholder'=>'Digite la ubicación del estante'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!!Form::label('ubicacionEtiquetaRadicado', 'Ubicación', array('class' => 'col-sm-2 control-label'))!!}
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

        

      <div class="form-group" id='test'>
        {!! Form::label('etiquetaRadicado', 'Etiquetas', array('class' => 'col-sm-2 control-label')) !!}
        <div class="col-sm-10">
          <div class="input-group">
            <span class="input-group-addon">
              <i class="fa fa-tags"></i>
            </span>
            {!!Form::text('nombreEtiqueta',null,['id' => 'nombreEtiqueta','class'=>'form-control','onclick' => 'mostrarModalEtiqueta()', 'readonly', 'placeholder'=>'Seleccione las etiquetas'])!!}
            {!!Form::hidden('etiquetaRadicado', null, array('id' => 'etiquetaRadicado'))!!}
          </div>
        </div>
      </div>        
   {!!Form::hidden('Documento_idDocumento', 0, array('id' => 'Documento_idDocumento'))!!}
   {!!Form::hidden('registro', 0, array('id' => 'registro'))!!}
   {!!Form::hidden('archivoRadicado', 0, array('id' => 'archivoRadicado'))!!}
   <input type="hidden" id="token" value="{{csrf_token()}}"/>

</fieldset>
</div>
</div>

<!--Campos de la pestaña Propiedades-->
  <div id="propiedades" class="tab-pane fade" style="overflow-y: scroll; height:256px;" >
  
<!-- INSERTO LOS METADATOS -->

  </div>
  @if(isset($radicado))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
        {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
      @else
        {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
      @endif
  @else
      {!!Form::button('Adicionar',["class"=>"btn btn-primary", "onclick"=>"guardarDatos($('#idDropzone').val());"])!!}
  @endif

</div>
</div>
<div id="preview" class="col-md-6">
      <div style="clear:both">
        <iframe id="viewer" frameborder="0" scrolling="no" width="100%" height="490px"></iframe> <!--Defino el stylo de la vista previa-->       
      </div>
</div>
</div>
</div>
</div>

{!! Form::close() !!}

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

<script>
//Guardo las etiquetas en el formulario 
  function etiquetaSelect(ids,nombetiqueta)
  {
    document.getElementById('etiquetaRadicado').value=ids;
    document.getElementById('nombreEtiqueta').value=nombetiqueta;
  }
</script>
<!--Funcion para cerrar el div -->

@stop