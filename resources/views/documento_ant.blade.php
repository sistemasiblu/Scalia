@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Documento</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::style('css/divopciones.css'); !!}
{!!Html::script('js/documento.js')!!}
{!!Html::script('js/ocultarsistema.js')!!}
{!!Html::script('js/ocultarconsulta.js')!!}


<?php 

$datos =  isset($documento) ? $documento->Documentopermiso : array();

for($i = 0; $i < count($datos); $i++)
{
  $ids = explode(',', $datos[$i]["Rol_idRol"]);

   $nombres = DB::table('rol')
             ->select(DB::raw('group_concat(nombreRol) AS nombreRol'))
            ->whereIn('idRol',$ids)
            ->get();
  $vble = get_object_vars($nombres[0] );
  $datos[$i]["nombreRolPermiso"] = $vble["nombreRol"];
}

?>

<!-- LLENO NUEVAMENTE LOS CAMPOS DE LA CONEXION CUANDO ENTRO A EDITAR EL FORMULARIO -->

<script>
    $(document).ready(function(){

      var tabla = "<?php echo isset($documento->tablaDocumento) ? $documento->tablaDocumento : '';?>";
      if(document.getElementById('SistemaInformacion_idSistemaInformacion').value != '')
      {
        consultarTablaVista(document.getElementById('SistemaInformacion_idSistemaInformacion').value, tabla); 
      }

      //  if(document.getElementById('tablaDocumento').value != null)
      // {
      //   consultarCampos(document.getElementById('SistemaInformacion_idSistemaInformacion').value, tabla); 
      // }

    });
</script>

<!-- DOCUMENTO PERMISOS -->
{!!Html::script('js/documentopermisos.js')!!}
<script>
    var idRol = '<?php echo isset($idRol) ? $idRol : "";?>';
    var nombreRol = '<?php echo isset($nombreRol) ? $nombreRol : "";?>';

    // rol = [JSON.parse(idRol),JSON.parse(nombreRol)];

    var documentopermiso = '<?php echo (isset($documento) ? json_encode($documento->Documentopermiso) : "");?>';
    documentopermiso = (documentopermiso != '' ? JSON.parse(documentopermiso) : '');
    var valorDocumentoPermisos = ['','',0,0,0,0,0,0,0,0];

    $(document).ready(function(){

      var stilocheck = 'width: 70px;height:30px;display:inline-block;';
      documentopermisos = new Atributos('documentopermisos','contenedor_documentopermisos','documentopermisos_');

      documentopermisos.altura = '35px';
      documentopermisos.campoid = 'idDocumentoPermiso';
      documentopermisos.campoEliminacion = 'eliminarPermiso';

      documentopermisos.campos   = ['Rol_idRol','nombreRolPermiso', 'cargarDocumentoPermiso', 'descargarDocumentoPermiso','consultarDocumentoPermiso','modificarDocumentoPermiso','imprimirDocumentoPermiso','correoDocumentoPermiso','eliminarDocumentoPermiso','idDocumentoPermiso'];
      documentopermisos.etiqueta = ['input','input', 'checkbox','checkbox','checkbox','checkbox','checkbox','checkbox','checkbox','input'];
      documentopermisos.tipo     = ['hidden','text', 'checkbox','checkbox','checkbox','checkbox','checkbox','checkbox','checkbox','hidden'];
      documentopermisos.estilo   = ['width: 600px;height:35px;','width: 600px;height:35px', stilocheck, stilocheck, stilocheck, stilocheck, stilocheck, stilocheck, stilocheck,''];
      documentopermisos.clase    = ['','','','','','','','','',''];
      documentopermisos.sololectura = [false,false,false,false,false,false,false,false,false,false];  
      documentopermisos.eventoclick = ['','','','','','','','','',''];
      documentopermisos.nombreRol =  JSON.parse(nombreRol);
      documentopermisos.idRol =  JSON.parse(idRol);
      for(var j=0, k = documentopermiso.length; j < k; j++)
      {
        documentopermisos.agregarCampos(JSON.stringify(documentopermiso[j]),'L');
        console.log(JSON.stringify(documentopermiso[j]))
      }
        
    });

  </script>

  <!-- DOCUMENTO PROPIEDADES -->
{!!Html::script('js/documentopropiedades.js')!!}

  <script>

    var idLista = '<?php echo isset($idLista) ? $idLista : "";?>';
    var nombreLista = '<?php echo isset($nombreLista) ? $nombreLista : "";?>';

    lista = [JSON.parse(idLista), JSON.parse(nombreLista)];

    valorTipo =  Array("Texto", "Fecha", "Numero", "Hora", "Lista", "Editor","EleccionUnica", "EleccionMultiple", "Página web", "Correo electronico", "Imagen");
    nombreTipo =  Array("Texto", "Fecha", "Numero", "Hora", "Lista", "Editor","Eleccion Unica", "Eleccion Multiple", "Página web", "Correo electronico", "Imagen");

    tipo = [valorTipo, nombreTipo];

   var valorCampo =  Array();
   var nombreCampo =  Array();

   campotabla = [valorCampo,nombreCampo];

    eventochange1 = ['onchange','activarLista(this.id);'];
    eventochange2 = ['onchange','enviarid(this.id, this.value);'];

    eventoclick = ['onclick','divValidar(this.id);'];

    var documentopropiedad = '<?php echo (isset($documento) ? json_encode($documento->Documentopropiedad) : "");?>';
    documentopropiedad = (documentopropiedad != '' ? JSON.parse(documentopropiedad) : '');

    var valorDocumentoPropiedades = [0,0,'','','',0,'',0,'',0,0,0,''];

    $(document).ready(function(){

      // var tabla = "<?php echo isset($documento->tablaDocumento) ? $documento->tablaDocumento : '';?>";
      // if(document.getElementById('tablaDocumento').value != null)
      //   {
      //     consultarCampos(document.getElementById('SistemaInformacion_idSistemaInformacion').value, tabla);
      //   }

      documentopropiedades = new Atributos('documentopropiedades','contenedor_documentopropiedades','documentopropiedades_');

      documentopropiedades.altura = '35px';
      documentopropiedades.campoid = 'idDocumentoPropiedad';
      documentopropiedades.campoEliminacion = 'eliminarPropiedad';

      documentopropiedades.campos   = ['idDocumentoPropiedad','ordenDocumentoPropiedad', 'tituloDocumentoPropiedad', 'campoDocumentoPropiedad','tipoDocumentoPropiedad','lista','Lista_idLista','longitudDocumentoPropiedad','valorDefectoDocumentoPropiedad','gridDocumentoPropiedad','indiceDocumentoPropiedad','versionDocumentoPropiedad','validacionDocumentoPropiedad'];
      documentopropiedades.etiqueta = ['input','input', 'input','select','select','input','select','input','input','checkbox','checkbox','checkbox','input'];
      documentopropiedades.tipo     = ['hidden','text','text','','','hidden','','text','text','checkbox','checkbox','checkbox','text'];
      documentopropiedades.estilo   = ['','width: 60px;height:35px;','width: 210px;height:35px;','width: 210px;height:35px;','width: 150px;height:35px;','width: 150px;height:35px;','width: 150px;height:35px; disabled: true;','width: 70px;height:35px;','width: 210px;height:35px;','width: 70px;height:35px;display:inline-block;','width: 70px;height:35px;display:inline-block;','width: 70px;height:35px;display:inline-block;','width: 200px;height:35px;'];
      documentopropiedades.clase    = ['','','','','','','','','','','','',''];
      documentopropiedades.opciones = ['','','', campotabla, tipo, '', lista, '','','',''];
      documentopropiedades.funciones = ['', '','','',eventochange1,'',eventochange2,'','','','','',eventoclick];
      documentopropiedades.sololectura = [false,false,false,false,false,false,true,false,false,false,false,false,true];



      for(var j=0, k = documentopropiedad.length; j < k; j++)
      { 
        documentopropiedades.agregarCampos(JSON.stringify(documentopropiedad[j]),'L');
        document.getElementById("lista"+j).value = document.getElementById('Lista_idLista'+j).value;
        activarLista(document.getElementById("tipoDocumentoPropiedad"+j).id);



      }

    });

  </script>

  <!-- DOCUMENTO VERSION -->
<!-- {!!Html::script('js/documentoversion.js')!!} -->

  <script>
    var documentoversion = '<?php echo (isset($documento) ? json_encode($documento->Documentoversion) : "");?>';
    documentoversion = (documentoversion != '' ? JSON.parse(documentoversion) : '');

    var valorDocumentoVersion = [0,0,'',0,0,0];
    $(document).ready(function(){

      version = new Atributos('version','contenedor_version','documentoversion_');

      version.altura = '35px';
      version.campoid = 'idDocumentoVersion';
      version.campoEliminacion = 'eliminarVersion';

      version.campos   = ['idDocumentoVersion','nivelDocumentoVersion', 'tipoDocumentoVersion','longitudDocumentoVersion','inicioDocumentoVersion','rellenoDocumentoVersion'];
      version.etiqueta = ['input','input', 'input','input','input','input'];
      version.tipo     = ['hidden','text','text','text','text','text'];
      version.estilo   = ['','width: 100px;height:35px;','width: 350px;height:35px;','width: 220px;height:35px;','width: 220px;height:35px;','width: 220px;height:35px;'];
      version.clase    = ['','','','','',''];
      version.sololectura = [false,false,false,false,false,false];

      for(var j=0, k = documentoversion.length; j < k; j++)
      {
        version.agregarCampos(JSON.stringify(documentoversion[j]),'L');
    
      }

    });

  </script>

  <!-- DOCUMENTO VALIDACION -->
{!!Html::script('js/documentovalidacion.js')!!}

  <script>
    var documentovalidacion = '<?php echo (isset($documento) ? json_encode($documento->Documentopropiedad) : "");?>';
    documentovalidacion = (documentovalidacion != '' ? JSON.parse(documentovalidacion) : '');

    valorOpcion =  Array("Minimo","Maximo","Obligatorio");
    nombreOpcion =  Array("Minimo","Maximo","Obligatorio");
    opcion = [valorOpcion,valorOpcion];

    var valorDocumentoValidaciones = ['',0,0];
    $(document).ready(function(){

      validacion = new Atributos('validacion','contenedor_validacion','documentovalidacion_');

      validacion.altura = '35px';
      validacion.campoid = 'idDocumentoValidacion';
      validacion.campoEliminacion = 'eliminarValidacion';

      validacion.campos   = ['validacionDocumentoValidacion', 'valorDocumentoValidacion','idDocumentoValidacion'];
      validacion.etiqueta = ['select', 'input', 'input'];
      validacion.tipo     = ['','text', 'hidden'];
      validacion.estilo   = ['width: 200px;height:35px;','width: 200px;height:35px;', ''];
      validacion.clase    = ['','', ''];
      validacion.sololectura = [false,false,false];
      validacion.opciones = [opcion, '',''];
      

      for(var j=0, k = documentovalidacion.length; j < k; j++)
      {
        validacion.agregarCampos(JSON.stringify(documentovalidacion[j]),'L');
    
      }

    });

  </script>

  <!-- Multiregistro de validaciones -->
  <div class="col-sm-12" id="validacion" style="width: 600px; height:310px; background-color: white; z-index: 1000 ; border: 1px solid; border-color: #ddd; position: absolute; top: 450px; left: 400px; display: none;">
  <a class='cerrar' href='javascript:void(0);' onclick='cerrarValidar(); document.getElementById(&apos;validacion&apos;).style.display = &apos;none&apos;'>x</a> <!--Es la funcion la cual cierra el div flotante-->

    <div class="panel-body">
      <div class="form-group" id='test'>
        <div class="col-sm-12">
          <div class="row show-grid">
            <div class="col-md-1" style="height: 42px; width:40px; cursor: pointer;" onclick="validacion.agregarCampos(valorDocumentoValidaciones,'A');">
              <span class="glyphicon glyphicon-plus"></span>
            </div>
            <div class="col-md-1" style="width: 200px;">Validación</div>
            <div class="col-md-1" style="width: 200px;">Valor</div>
            <div id="contenedor_validacion">
            </div>
            <input id="registro" name="registro" type="hidden">
          </div>
          {!!Form::button('Enviar',["class"=>"btn btn-success", 'onclick' => 'concatenarValidacion(document.getElementById(\'registro\').value)'])!!}
          <!-- {!!Form::button('Borrar Ultimo',["class"=>"btn btn-warning", 'onclick' => 'borrarUltimoConcatenado();'])!!} -->
          {!!Form::button('Borrar',["class"=>"btn btn-danger", 'onclick' => 'borrarConcatenado(document.getElementById(\'registro\').value);'])!!}
        </div>
      </div>
    </div>
  </div>

  @if(isset($documento))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($documento,['route'=>['documento.destroy',$documento->idDocumento],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($documento,['route'=>['documento.update',$documento->idDocumento],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'documento.store','method'=>'POST'])!!}
  @endif

<div id='form-section'>

  
  <fieldset id="documento-form-fieldset"> 
    <div class="form-group" id='test'>
          {!! Form::label('codigoDocumento', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoDocumento',null,['class'=>'form-control','placeholder'=>'Ingresa el código del documento'])!!}
              {!! Form::hidden('idDocumento', null, array('id' => 'idDocumento')) !!}
              {!! Form::hidden('eliminarVersion', null, array('id' => 'eliminarVersion')) !!}
              {!! Form::hidden('eliminarValidacion', null, array('id' => 'eliminarValidacion')) !!}
              {!! Form::hidden('eliminarPropiedad', null, array('id' => 'eliminarPropiedad')) !!}
              {!! Form::hidden('eliminarPermiso', null, array('id' => 'eliminarPermiso')) !!}

            </div>
          </div>
        </div>

        {!! Form::hidden('registro', null, array('id' => 'registro')) !!}
    
        <div class="form-group" id='test'>
          {!! Form::label('nombreDocumento', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
              {!!Form::text('nombreDocumento',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del documento'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!! Form::label('directorioDocumento', 'Directorio', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-folder-open"></i>
              </span>
              {!!Form::text('directorioDocumento',null,['class'=>'form-control','placeholder'=>'Ingresa el directorio del documento'])!!}
            </div>
          </div>
        </div>

        <div class="form-group" id='test'>
          {!! Form::label('tipoDocumento', 'Tipo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-bars"></i>
              </span>
              {!! Form::select('tipoDocumento', ['Seleccione', 'Formulario', 'Gestión Documental'], null, ['class' => 'select form-control']) !!}
            </div>
          </div>
        </div>

        </br> </br> </br> </br> </br>
        <div class="form-group">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">Detalles</div>
              <div class="panel-body">
                <div class="panel-group" id="accordion">
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#conexionDocumento">Conexi&oacute;n</a>
                      </h4>
                    </div>
                    <div id="conexionDocumento" class="panel-collapse collapse">
                      <div class="panel-body">

                        <input type="hidden" id="token" value="{{csrf_token()}}"/>

                          <div class="form-group" id='test'>
                            {!! Form::label('origenDocumento', 'Origen', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-6">
                              <div class="input-group">
                                {!!Form::radio('origenDocumento', '2', true, ['onclick' => 'ocultarSistema(this)'])!!} Sistema
                                &nbsp;
                                {!!Form::radio('origenDocumento', '1', false, ['onclick' => 'ocultarSistema(this)'])!!} Manual
                              </div>
                            </div>
                            </div>
                            </br>

                          <div id="sistemainformacion">
                            <div class="form-group" id='test'>
                              {!! Form::label('SistemaInformacion_idSistemaInformacion', 'Sistema de informaci&oacute;n', array('class' => 'col-sm-2 control-label')) !!}
                              <div class="col-sm-10">
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <i class="fa fa-paper-plane   "></i>
                                  </span>
                                {!!Form::select('SistemaInformacion_idSistemaInformacion',$sistemainformacion, (isset($documento) ? $documento->SistemaInformacion_idSistemaInformacion : 0),['class'=>'select form-control', 'onchange' => 'consultarTablaVista(this.value)','placeholder'=>'Selecciona el sistema de informaci&oacute;n'])!!}
                                </div>
                              </div>
                              </br>
                            </div>

                            </div>
                          

                            <div class="form-group" id='test'>
                            {!! Form::label('tipoConsultaDocumento', 'Tipo de consulta', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-6">
                              <div class="input-group">
                                {!!Form::radio('tipoConsultaDocumento', '1', true, ['onclick' => 'ocultarConsulta(this)'])!!} Tabla
                                &nbsp;
                                {!!Form::radio('tipoConsultaDocumento', '2', false, ['onclick' => 'ocultarConsulta(this)'])!!} Vista
                                &nbsp;
                                {!!Form::radio('tipoConsultaDocumento', '3', false, ['onclick' => 'ocultarConsulta(this)'])!!} SQL
                                &nbsp;
                                {!!Form::radio('tipoConsultaDocumento', '4', false, ['onclick' => 'ocultarConsulta(this)'])!!} Ninguna
                              </div>
                            </div>
                            </div>
                            </br>

                            <div id="lista">
                            <div class="form-group" id='test'>
                              {!! Form::label('tablaDocumento', 'Tabla / Vista', array('class' => 'col-sm-2 control-label')) !!}
                              <div class="col-sm-10">
                                <div class="input-group">
                                  <span class="input-group-addon">
                                    <i class="fa fa-paper-plane-o  "></i>
                                  </span>
                                {!!Form::select('tablaDocumento',array('Seleccione'), (isset($documento) ? $documento->tablaDocumento : null),['class'=>'select form-control', 'onchange'=>'consultarCampos(document.getElementById(\'SistemaInformacion_idSistemaInformacion\').value, this.value);'])!!}
                                </div>
                              </div>
                            </div>
                          </div>


                            <div id="consulta">
                              <div class="form-group" id='test'>
                                  {!!Form::label('consultaDocumento', 'Consulta', array('class' => 'col-sm-2 control-label')) !!}
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <span class="input-group-addon">
                                        <i class="fa fa-search "></i>
                                      </span>
                                {!!Form::textarea('consultaDocumento',null,['class'=>'form-control','style'=>'height:100px','placeholder'=>'Ingresa la consulta'])!!}
                                    </div>
                              </div>
                              </div>
                            </div>
                            </div>
                          </div>
                        </div>  
                    
                      <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#versionDocumento">Control de versiones</a>
                      </h4>
                    </div>
                    <div id="versionDocumento" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-10" style="width: 100%;">

                           <div class="form-group" id='test'>
                            {!! Form::label('controlVersionDocumento', 'Manejar control de versiones', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-1">
                              <div class="input-group">
                                <span class="input-group-addon">
                                  <i class="fa fa-check-circle "></i>
                                </span>
                                {!! Form::checkbox('controlVersionDocumento', 1, null, ['class' => 'form-control']) !!}
                              </div>
                            </div>
                            </div>

                            </br></br>

                            <div class="form-group" id='test'>
                            {!! Form::label('trazabilidadMetadatosDocumento', 'Trazabilidad de metadatos por versión', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-1">
                              <div class="input-group">
                                <span class="input-group-addon">
                                  <i class="fa fa-check-circle "></i>
                                </span>
                                {!! Form::checkbox('trazabilidadMetadatosDocumento', 1, null, ['class' => 'form-control']) !!}
                              </div>
                            </div>                            
                            </div>

                            </br></br>

                            <div class="form-group" id='test'>
                            {!! Form::label('concatenarNombreDocumento', 'Concatenar versión al nombre del archivo', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-1">
                              <div class="input-group">
                                <span class="input-group-addon">
                                  <i class="fa fa-check-circle "></i>
                                </span>
                                {!! Form::checkbox('concatenarNombreDocumento', 1, null, ['class' => 'form-control']) !!}
                              </div>
                            </div>
                            </div>

                            <!-- Multiregistro de versiones -->
                            <div class="panel-body">
                              <div class="form-group" id='test'>
                                <div class="col-sm-12">
                                  <div class="row show-grid">
                                    <div class="col-md-1" style="height: 42px; width:40px; cursor: pointer;" onclick="version.agregarCampos(valorDocumentoVersion,'A');">
                                      <span class="glyphicon glyphicon-plus"></span>
                                    </div>
                                    <div class="col-md-1" style="width: 100px;">Nivel</div>
                                    <div class="col-md-1" style="width: 350px;">Tipo</div>
                                    <div class="col-md-1" style="width: 220px;">Longitud</div>
                                    <div class="col-md-1" style="width: 220px;">Inicio</div>
                                    <div class="col-md-1" style="width: 220px;">Relleno</div>
                                    <div id="contenedor_version">
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

                  
                  <div class="panel panel-info">
                    <div class="panel-heading">

                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#propiedadDocumento">Propiedades</a>
                      </h4>
                    </div>
                   <div id="propiedadDocumento" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-10">
                            <div class="panel-body" style="width:1220px;">
                              <div class="form-group" id='test'>
                                <div class="col-sm-12">
                                  <div class="row show-grid">
                                  <div style="overflow:auto; height:350px;">
                                  <div style="width: 1520px;">
                                    <div class="col-md-1" style="height: 42px; width:40px; cursor: pointer;" onclick="documentopropiedades.agregarCampos(valorDocumentoPropiedades,'A');">
                                      <span class="glyphicon glyphicon-plus"></span>
                                    </div>
                                    <div class="col-md-1" style="width: 60px;">Orden</div>
                                    <div class="col-md-1" style="width: 210px;">Titulo</div>
                                    <div class="col-md-1" style="width: 210px;">Campo</div>
                                    <div class="col-md-1" style="width: 150px;">Tipo</div>
                                    <div class="col-md-1" style="width: 150px;">Lista</div>
                                    <div class="col-md-1" style="width: 70px;">Long</div>
                                    <div class="col-md-1" style="width: 210px;">Valor Base</div>
                                    <div class="col-md-1" style="width: 70px;">Grid</div>
                                    <div class="col-md-1" style="width: 70px;">Indice</div>
                                    <div class="col-md-1" style="width: 70px;">Versión</div>
                                    <div class="col-md-1" style="width: 200px;">Validación</div>
                                    <div id="contenedor_documentopropiedades">
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
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#permisoDocumento">Permisos</a>
                      </h4>
                    </div>
                    <div id="permisoDocumento" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-10" style="width: 100%;">
                            <div class="panel-body">
                              <div class="form-group" id='test'>
                                <div class="col-sm-12">
                                  <div class="row show-grid">
                                    <div class="col-md-1" style="width: 40px; cursor: pointer;" onclick="mostrarModalRol(this.id)">
                                      <span class="glyphicon glyphicon-plus"></span>
                                    </div>
                                    <div class="col-md-1" style="width: 600px;">Rol</div>
                                    <div class="col-md-1" style="width: 70px;"><center><span title="Adicionar" class="fa fa-upload"></span></center></div>
                                    <div class="col-md-1" style="width: 70px;"><center><span title="Descargar" class="fa fa-download "></span></center></div>
                                    <div class="col-md-1" style="width: 70px;"><center><span title="Consultar" class="fa fa-search "></span></center></div>
                                    <div class="col-md-1" style="width: 70px;"><center><span title="Modificar" class="fa fa-pencil"></span></center></div>
                                    <div class="col-md-1" style="width: 70px;"><center><span title="Imprimir" class="fa fa-print"></span></center></div>
                                    <div class="col-md-1" style="width: 70px;"><center><span title="Email" class="fa fa-envelope-o "></span></center></div>
                                    <div class="col-md-1" style="width: 70px;"><center><span title="Eliminar / Anular" class="fa fa-trash"></span></center></div>
                                    <div id="contenedor_documentopermisos">
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
            </div>  
          </div>
        </div>
      </div>
    </div>
  </div> 
</div>
    </fieldset>
    <!-- Modal -->
<div id="myModalRol" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:1000px;">

    <!-- Modal content-->
    <div style="" class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Roles</h4>
      </div>
      <div class="modal-body">
        <iframe style="width:100%; height:500px; " id="rol" name="rol" src="{!! URL::to ('rolselect')!!}"> </iframe> 
      </div>
    </div>
  </div>
</div>
    </div>

  @if(isset($documento))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
        {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
      @else
        {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
      @endif
  @else
      {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
  @endif
  {!!Form::close()!!}
  </div>
</div>
@stop