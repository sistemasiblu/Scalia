@extends('layouts.modal')
@section('titulo')<h3 id="titulo"><center>Inventario Documental</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/ubicaciondocumento.js')!!}

  {!!Form::open(['route'=>'ubicaciondocumento.store','method'=>'POST', 'action' => 'UbicacionDocumentoController@store', 'id' => 'ubicacion'])!!}

<?php 
  
  $idUbicacion = isset($_GET['idUbicacion']) ? $_GET['idUbicacion'] : ''; 
  $idDependencia = '';
  $pl = '';
  if ($idUbicacion != '0' and $_GET['estado'] == 'Prestada' or $idUbicacion != '0' and $_GET['estado'] == 'Extraviada' or $idUbicacion != '0' and $_GET['estado'] == 'Averiada') 
  {
      $ubicacion = DB::Select('
      SELECT
        idUbicacionDocumento,
        tipoUbicacionDocumento,
        DependenciaLocalizacion_idDependenciaLocalizacion,
        posicionUbicacionDocumento,
        descripcionUbicacionDocumento,
        Tercero_idTercero,
        "" as nombreTerceroUbicacionDocumento,
        "" as documentoTerceroUbicacionDocumento,
        numeroLegajoUbicacionDocumento,
        numeroFolioUbicacionDocumento,
        fechaInicialUbicacionDocumento,
        fechaFinalUbicacionDocumento,
        nombreTipoSoporteDocumental,
        idTipoSoporteDocumental,
        nombreDependencia,
        idDependencia,
        nombreCompania,
        idCompania,
        estadoUbicacionDocumento,
        observacionUbicacionDocumento
      FROM
        ubicaciondocumento ud
          LEFT JOIN
        tiposoportedocumental tsd ON ud.TipoSoporteDocumental_idTipoSoporteDocumental = tsd.idTipoSoporteDocumental
          LEFT JOIN
        dependencia d ON ud.Dependencia_idProductora = d.idDependencia
          LEFT JOIN
        compania c ON ud.Compania_idCompania = c.idCompania
      WHERE idUbicacionDocumento = '.$idUbicacion);

      $datosUbicacion = get_object_vars($ubicacion[0]);

      $localizacion = $_GET['localizacion'].' '.$datosUbicacion['posicionUbicacionDocumento'];

      echo 
      '<script>
        $(document).ready( function () {
          mostrarCamposTipoUbicacion("'.$datosUbicacion["tipoUbicacionDocumento"].'");
        });
        </script>';
  }
  else if ($idUbicacion != '0' and $_GET['estado'] == 'Destruida')
  {
    $ubicacion = DB::Select('
      SELECT
        "" idUbicacionDocumento,
        "" as tipoUbicacionDocumento,
        DependenciaLocalizacion_idDependenciaLocalizacion,
        posicionUbicacionDocumento,
        "" as descripcionUbicacionDocumento,
        Tercero_idTercero,
        "" as nombreTerceroUbicacionDocumento,
        "" as documentoTerceroUbicacionDocumento,
        "" as numeroLegajoUbicacionDocumento,
        "" as numeroFolioUbicacionDocumento,
        "" as fechaInicialUbicacionDocumento,
        "" as fechaFinalUbicacionDocumento,
        "" as nombreTipoSoporteDocumental,
        "" as idTipoSoporteDocumental,
        "" as nombreDependencia,
        "" as idDependencia,
        "" as nombreCompania,
        "" as idCompania,
        estadoUbicacionDocumento,
        "" as observacionUbicacionDocumento
      FROM
        ubicaciondocumento ud
          LEFT JOIN
        tiposoportedocumental tsd ON ud.TipoSoporteDocumental_idTipoSoporteDocumental = tsd.idTipoSoporteDocumental
          LEFT JOIN
        dependencia d ON ud.Dependencia_idProductora = d.idDependencia
          LEFT JOIN
        compania c ON ud.Compania_idCompania = c.idCompania
      WHERE idUbicacionDocumento = '.$idUbicacion);

      $datosUbicacion = get_object_vars($ubicacion[0]);

      $localizacion = $_GET['localizacion'].' '.$datosUbicacion['posicionUbicacionDocumento'];

      echo 
      '<script>
        $(document).ready( function () {
          mostrarCamposTipoUbicacion("'.$datosUbicacion["tipoUbicacionDocumento"].'");
        });
        </script>';
  }
  else
  {
    $idDependencia = isset($_GET['idDependencia']) ? $_GET['idDependencia'] : ''; 

    $pl = DB::Select("
      SELECT 
         LPAD((IF(posicionUbicacionDocumento IS NULL, '00', MAX(posicionUbicacionDocumento))+1),2,'0') as posicionUbicacionDocumento
      FROM
          ubicaciondocumento
      WHERE
        DependenciaLocalizacion_idDependenciaLocalizacion = ".$idDependencia);

    $pl = get_object_vars($pl[0])['posicionUbicacionDocumento'];

    $localizacion = $_GET['localizacion'].' '.$pl;
  }



?>

<div id='form-ubicacion'>

	<fieldset id="ubicaciondocumento-form-fieldset">	
  <input type="hidden" id="token" value="{{csrf_token()}}"/>
		<div class="form-group" id='test'>
      {!!Form::label('tipoUbicacionDocumento', 'Tipo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-bars"></i>
          </span>
          {!! Form::select('tipoUbicacionDocumento', ['Historias' => 'Historias','Otros' => 'Otros'],($idUbicacion != 0) ? $datosUbicacion['tipoUbicacionDocumento'] : null,['class' => 'form-control', 'onchange' => 'mostrarCamposTipoUbicacion(this.value)', 'placeholder' => 'Seleccione el tipo', 'required' => 'required']) !!}

          {!!Form::hidden('idUbicacionDocumento', ($idUbicacion != 0) ? $datosUbicacion['idUbicacionDocumento'] : null, array('id' => 'idUbicacionDocumento')) !!}

          {!!Form::hidden('DependenciaLocalizacion_idDependenciaLocalizacion', ($idDependencia != '' ? $idDependencia : ($idUbicacion != 0 ? $datosUbicacion['DependenciaLocalizacion_idDependenciaLocalizacion'] : null)), array('id' => 'DependenciaLocalizacion_idDependenciaLocalizacion')) !!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('localizacionUbicacionDocumento', 'P.L.', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-sitemap"></i>
          </span>
          {!!Form::text('localizacionUbicacionDocumento',$localizacion,['class'=>'form-control', 'readonly', 'placeholder'=>'Punto de localización', 'required' => 'required'])!!}

          {!!Form::hidden('posicionUbicacionDocumento', ($pl != '' ? $pl : ($idUbicacion != 0 ? $datosUbicacion['posicionUbicacionDocumento'] : null)), array('id' => 'posicionUbicacionDocumento')) !!}
        </div>
      </div>
    </div>

    <div id="descripcion" style="display:none" class="form-group" id='test'>
      {!!Form::label('descripcionUbicacionDocumento', 'Descripción', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
          {!!Form::text('descripcionUbicacionDocumento',($idUbicacion != 0) ? $datosUbicacion['descripcionUbicacionDocumento'] : null,['class'=>'form-control','placeholder'=>'Ingresa la descripción'])!!}
        </div>
      </div>
    </div>

    <div id="documento" style="display:none" class="form-group" id='test'>
      {!!Form::label('documentoTerceroUbicacionDocumento', 'Cedula', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-credit-card"></i>
          </span>
          {!!Form::text('documentoTerceroUbicacionDocumento',($idUbicacion != 0) ? $datosUbicacion['documentoTerceroUbicacionDocumento'] : null,['class'=>'form-control','placeholder'=>'Ingresa el documento del empleado', 'onchange'=>'llenarMetadatos(this.value)'])!!}
        </div>
      </div>
    </div>

    <div id="nombre" style="display:none" class="form-group" id='test'>
      {!!Form::label('nombreTerceroUbicacionDocumento', 'Nombre', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-user"></i>
          </span>
          {!!Form::text('nombreTerceroUbicacionDocumento',($idUbicacion != 0) ? $datosUbicacion['nombreTerceroUbicacionDocumento'] : null,['class'=>'form-control','placeholder'=>'Ingresa el nombre del empleado'])!!}
          {!!Form::hidden('Tercero_idTercero', ($idUbicacion != 0) ? $datosUbicacion['Tercero_idTercero'] : null, array('id' => 'Tercero_idTercero')) !!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('numeroLegajoUbicacionDocumento', 'No. legajo', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-th-large"></i>
          </span>
          {!!Form::text('numeroLegajoUbicacionDocumento',($idUbicacion != 0) ? $datosUbicacion['numeroLegajoUbicacionDocumento'] : null,['class'=>'form-control','placeholder'=>'Ingresa el número de legajos', 'required' => 'required', 'readonly'])!!}
        </div>
      </div>
    </div>

    <div id="folio" style="display:none" class="form-group" id='test'>
      {!!Form::label('numeroFolioUbicacionDocumento', 'No. folios', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-th-list"></i>
          </span>
          {!!Form::text('numeroFolioUbicacionDocumento',($idUbicacion != 0) ? $datosUbicacion['numeroFolioUbicacionDocumento'] : null,['class'=>'form-control','placeholder'=>'Ingresa el número de folios'])!!}
        </div>
      </div>
    </div>

    <div id="fechaInicial" style="display:none" class="form-group" id='test'>
      {!!Form::label('fechaInicialUbicacionDocumento', 'Fecha Inicial', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </span>
          {!!Form::text('fechaInicialUbicacionDocumento',($idUbicacion != 0) ? $datosUbicacion['fechaInicialUbicacionDocumento'] : null,['class'=>'form-control','placeholder'=>'Seleccione la fecha Inicial'])!!}
        </div>
      </div>
    </div>

    <div id="fechaFinal" style="display:none" class="form-group" id='test'>
      {!!Form::label('fechaFinalUbicacionDocumento', 'Fecha final', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </span>
          {!!Form::text('fechaFinalUbicacionDocumento',($idUbicacion != 0) ? $datosUbicacion['fechaFinalUbicacionDocumento'] : null,['class'=>'form-control','placeholder'=>'Seleccione la fecha Final'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('TipoSoporteDocumental_idTipoSoporteDocumental', 'Tipo Soporte', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-tasks"></i>
          </span>
          {!!Form::select('TipoSoporteDocumental_idTipoSoporteDocumental',$tiposoporte, ($idUbicacion != 0) ? $datosUbicacion['idTipoSoporteDocumental'] : null,["class" => "select form-control","placeholder" =>"Seleccione el tipo de soporte", 'required' => 'required'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('Dependencia_idProductora', 'Dependencia', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-bank"></i>
          </span>
          {!!Form::select('Dependencia_idProductora',$dependenciaproductora, ($idUbicacion != 0) ? $datosUbicacion['idDependencia'] : null,["class" => "select form-control","placeholder" =>"Seleccione la dependencia productora", 'required' => 'required'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('Compania_idCompania', 'Compañía', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-bank"></i>
          </span>
          {!!Form::select('Compania_idCompania',$compania, ($idUbicacion != 0) ? $datosUbicacion['idCompania'] : null,["class" => "select form-control","placeholder" =>"Seleccione la compañía", 'required' => 'required'])!!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('estadoUbicacionDocumento', 'Estado', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-bars"></i>
          </span>
          {!! Form::select('estadoUbicacionDocumento', ['Activa' => 'Activa','Destruida' => 'Destruida', 'Prestada' => 'Prestada', 'Extraviada' => 'Extraviada', 'Averiada' => 'Averiada'],($idUbicacion != 0) ? $datosUbicacion['estadoUbicacionDocumento'] : null,['class' => 'form-control', 'placeholder' => 'Seleccione el estado', 'required' => 'required']) !!}
        </div>
      </div>
    </div>

    <div class="form-group" id='test'>
      {!!Form::label('observacionUbicacionDocumento', 'Observaciones', array('class' => 'col-sm-2 control-label')) !!}
      <div class="col-sm-10">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
          {!!Form::textarea('observacionUbicacionDocumento',($idUbicacion != 0) ? $datosUbicacion['observacionUbicacionDocumento'] : null,['class'=>'form-control ckeditor','style'=>'height:100px','placeholder'=>'Ingresa las observaciones'])!!}
        </div>
      </div>
    </div>


  </fieldset>
  @if($idUbicacion != '0' and $_GET['estado'] != 'Destruida')
    {!!Form::button('Modificar',["class"=>"btn btn-primary", 'onclick' => 'guardarDatos()'])!!}
    <!-- <button type="button" class="btn btn-danger" onclick="eliminarDatos(<?php echo $datosUbicacion['idUbicacionDocumento'] ?>)">Eliminar</button> -->
  @else
    {!!Form::button('Adicionar',["class"=>"btn btn-primary", 'onclick' => 'guardarDatos()'])!!}
 	@endif

	
</div>

{!! Form::close() !!}
@stop