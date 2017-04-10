@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Documento de importación</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/documentoimportacion.js')!!}
<?php 

$datos =  isset($documentoimportacion) ? $documentoimportacion->DocumentoImportacionPermiso : array();

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

<script>

    // DOCUMENTO CORREO

    valorTipoCorreo =  Array("Bodega", "Pagos", "OTM");
    nombreTipoCorreo =  Array("Bodega", "Pagos", "OTM");
    var tipocorreo = [valorTipoCorreo, nombreTipoCorreo];

    var idDocumento = '<?php echo isset($idDocumento) ? $idDocumento : "";?>';
    var nombreDocumento = '<?php echo isset($nombreDocumento) ? $nombreDocumento : "";?>';


    var documentocorreo = '<?php echo (isset($documentocorreo) ? json_encode($documentocorreo) : "");?>';
    documentocorreo = (documentocorreo != '' ? JSON.parse(documentocorreo) : '');
    var valorDocumentoCorreo = ['','',0,0,0,0,0,0,0,0];

    $(document).ready(function(){

      documento = new Atributos('documento','contenedor_correo','correo_');

      documento.altura = '35px';
      documento.campoid = 'idDocumentoImportacionCorreo';
      documento.campoEliminacion = 'eliminarImportacionCorreo';

      documento.campos   = ['Documento_idDocumento','nombreDocumento', 'tipoDocumentoImportacionCorreo', 'DocumentoImportacion_idDocumentoImportacion','idDocumentoImportacionCorreo'];
      documento.etiqueta = ['input', 'input', 'select', 'input', 'input'];
      documento.tipo     = ['hidden', 'text', '', 'hidden', 'hidden'];
      documento.estilo   = ['','width: 900px;height:35px;', 'width: 200px;height:35px' ,'', ''];
      documento.clase    = ['','','','', ''];
      documento.sololectura = [false,false,false,false,false];  
      documento.opciones = ['','',tipocorreo,'',''];
      documento.nombreDocumento =  JSON.parse(nombreDocumento);
      documento.idDocumento =  JSON.parse(idDocumento);

      for(var j=0, k = documentocorreo.length; j < k; j++)
      {
        documento.agregarCampos(JSON.stringify(documentocorreo[j]),'L');
        console.log(JSON.stringify(documentocorreo[j]))
      }
        
    });

    // DOCUMENTO PERMISOS 
    var idRol = '<?php echo isset($idRol) ? $idRol : "";?>';
    var nombreRol = '<?php echo isset($nombreRol) ? $nombreRol : "";?>';

    // rol = [JSON.parse(idRol),JSON.parse(nombreRol)];

    var documentopermiso = '<?php echo (isset($documentoimportacion) ? json_encode($documentoimportacion->DocumentoImportacionPermiso) : "");?>';
    documentopermiso = (documentopermiso != '' ? JSON.parse(documentopermiso) : '');
    var valorDocumentoPermisos = ['','',0,0,0,0,0,0,0,0];

    $(document).ready(function(){

      var stilocheck = 'width: 70px;height:30px;display:inline-block;';
      permisos = new Atributos('permisos','contenedor_permisos','permisos_');

      permisos.altura = '35px';
      permisos.campoid = 'idDocumentoImportacionPermiso';
      permisos.campoEliminacion = 'eliminarImportacionPermiso';

      permisos.campos   = ['Rol_idRol','nombreRolPermiso', 'agregarDocumentoImportacionPermiso', 'descargarDocumentoImportacionPermiso','consultarDocumentoImportacionPermiso','modificarDocumentoImportacionPermiso','imprimirDocumentoImportacionPermiso','correoDocumentoImportacionPermiso','eliminarDocumentoImportacionPermiso','idDocumentoImportacionPermiso'];
      permisos.etiqueta = ['input','input', 'checkbox','checkbox','checkbox','checkbox','checkbox','checkbox','checkbox','input'];
      permisos.tipo     = ['hidden','text', 'checkbox','checkbox','checkbox','checkbox','checkbox','checkbox','checkbox','hidden'];
      permisos.estilo   = ['width: 600px;height:35px;','width: 600px;height:35px', stilocheck, stilocheck, stilocheck, stilocheck, stilocheck, stilocheck, stilocheck,''];
      permisos.clase    = ['','','','','','','','','',''];
      permisos.sololectura = [false,false,false,false,false,false,false,false,false,false];  
      permisos.eventoclick = ['','','','','','','','','',''];
      permisos.nombreRol =  JSON.parse(nombreRol);
      permisos.idRol =  JSON.parse(idRol);
      for(var j=0, k = documentopermiso.length; j < k; j++)
      {
        permisos.agregarCampos(JSON.stringify(documentopermiso[j]),'L');
        console.log(JSON.stringify(documentopermiso[j]))
      }
        
    });

  </script>

  @if(isset($documentoimportacion))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($documentoimportacion,['route'=>['documentoimportacion.destroy',$documentoimportacion->idDocumentoImportacion],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($documentoimportacion,['route'=>['documentoimportacion.update',$documentoimportacion->idDocumentoImportacion],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'documentoimportacion.store','method'=>'POST'])!!}
  @endif

<div id='form-section'>

  
  <fieldset id="documentoimportacion-form-fieldset"> 
    <div class="form-group" id='test'>
          {!! Form::label('codigoDocumentoImportacion', 'C&oacute;digo', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-barcode"></i>
              </span>
              {!!Form::text('codigoDocumentoImportacion',null,['class'=>'form-control','placeholder'=>'Ingresa el código del documento'])!!}
              {!! Form::hidden('idDocumentoImportacion', null, array('id' => 'idDocumentoImportacion')) !!}
              {!! Form::hidden('eliminarImportacionPermiso', null, array('id' => 'eliminarImportacionPermiso')) !!}
              {!! Form::hidden('eliminarImportacionCorreo', null, array('id' => 'eliminarImportacionCorreo')) !!}

            </div>
          </div>
        </div>

        {!! Form::hidden('registro', null, array('id' => 'registro')) !!}
    
        <div class="form-group" id='test'>
          {!! Form::label('nombreDocumentoImportacion', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
          <div class="col-sm-10">
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>
              {!!Form::text('nombreDocumentoImportacion',null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del documento'])!!}
            </div>
          </div>
        </div>
        </div>

        </br> 

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
                            {!! Form::label('origenDocumentoImportacion', 'Origen', array('class' => 'col-sm-2 control-label')) !!}
                            <div class="col-sm-6">
                              <div class="input-group">
                                {!!Form::radio('origenDocumentoImportacion', '2', true, ['onclick' => 'ocultarSistema(this)'])!!} Sistema
                                &nbsp;
                                {!!Form::radio('origenDocumentoImportacion', '1', false, ['onclick' => 'ocultarSistema(this)'])!!} Manual
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
                                {!!Form::select('SistemaInformacion_idSistemaInformacion',$sistemainformacion, (isset($documentoimportacion) ? $documentoimportacion->SistemaInformacion_idSistemaInformacion : 0),['class'=>'select form-control','placeholder'=>'Selecciona el sistema de informaci&oacute;n'])!!}
                                </div>
                              </div>
                            </div>
                          </div>

                            <div id="consulta">
                              <div class="form-group" id='test'>
                                  {!!Form::label('tipoDocumentoImportacion', 'Tipo', array('class' => 'col-sm-2 control-label')) !!}
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <span class="input-group-addon">
                                        <i class="fa fa-search "></i>
                                      </span>
                                {!!Form::text('tipoDocumentoImportacion',null,['class'=>'form-control','placeholder'=>'Ingresa el tipo de documento'])!!}
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
                        <a data-toggle="collapse" data-parent="#accordion" href="#correoDocumento">Documentos</a>
                      </h4>
                    </div>
                    <div id="correoDocumento" class="panel-collapse collapse">
                      <div class="panel-body">
                        <div class="form-group" id='test'>
                          <div class="col-sm-10" style="width: 100%;">
                            <div class="panel-body">
                              <div class="form-group" id='test'>
                                <div class="col-sm-12">
                                  <div class="row show-grid">
                                    <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="mostrarModalDocumento(this.id)">
                                      <span class="glyphicon glyphicon-plus"></span>
                                    </div>
                                    <div class="col-md-1" style="width: 900px;">Documento</div>
                                    <div class="col-md-1" style="width: 200px;">Tipo</div>
                                    <div id="contenedor_correo">
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
                                    <div class="col-md-1" style="width: 40px; height: 42px; cursor: pointer;" onclick="mostrarModalRol(this.id)">
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
                                    <div id="contenedor_permisos">
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

  @if(isset($documentoimportacion))
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
@stop

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

<div id="myModalDocumento" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:100%;">

    <!-- Modal content-->
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Documentos</h4>
      </div>
      <div class="modal-body">
      <iframe style="width:100%; height:500px; " id="rol" name="rol" src="{!! URL::to ('documentoselect')!!}"> </iframe> 
      </div>
    </div>
  </div>
</div>