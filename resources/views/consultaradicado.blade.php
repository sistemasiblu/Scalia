@extends('layouts.modal')
@section('titulo')<h3 id="titulo"><center>Consultar Documentos</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::style('css/divopciones.css'); !!}
{!!Html::script('js/consultaradicado.js'); !!}


<div id='form-section'>

	<fieldset id="consultaradicado-form-fieldset">	
		
        <!-- <div class="form-group" id='test'>
            {!!Form::label('Documento_idDocumento', 'Documento', array('class' => 'col-sm-1 control-label'))!!}
            <div class="col-sm-10">
                    <div class="input-group">
                        <span class="input-group-addon">
                          <i class="fa fa-search"></i>
                        </span>
                {!!Form::select('Documento_idDocumento',$documento, (isset($consultaradicado) ? $consultaradicadoradicado->Documento_idDocumento : 0),["class" => "select form-control", 'onchange' => 'cuerpoGrid(this.value);',"placeholder" =>"Seleccione un documento"])!!}
              </div>
            </div>
          </div> -->

<?php 
  //Pregunto si desde la consulta llega el id de la dependencia para ponerlo en la condición y de no ser asi,
  //busco todas las dependencias
  $Dep = $_GET["idDep"] != null ? 'AND d.idDependencia in ('.$_GET["idDep"].')' : '';

  //Pregunto si desde la consulta llega el id de el documento para ponerlo en la condición y de no ser asi,
  //busco todos los documentos
  $Doc = $_GET["idDoc"] != null ? 'AND doc.idDocumento in ('.$_GET["idDoc"].')' : '';

  //Recibo la condicion de la consulta
  $query = $_GET["consulta"];

  // Consulto el rol con el que esta logueado
  $Rol = DB::Select('Select Rol_idRol from users where id = '.Session::get('idUsuario'));
  $idRol = get_object_vars($Rol[0]);

  // Consulto el nombre de la dependencia teniendo como condición que el usuario logueado y la compañía tenga permiso a ver el documento que va a consultar dentro de Dependencia - Serie - Sub Serie - Documento
  $tituloDep = DB::Select('
    SELECT 
    nombreDependencia, idDependencia
FROM
    retenciondocumental rd
        LEFT JOIN
    dependencia d ON rd.Dependencia_idDependencia = d.idDependencia
        LEFT JOIN
    dependenciapermiso dp ON dp.Dependencia_idDependencia = d.idDependencia
        LEFT JOIN
    serie s ON rd.Serie_idSerie = s.idSerie
        LEFT JOIN
    seriepermiso sp ON sp.Serie_idSerie = s.idSerie
        LEFT JOIN
    subserie ss ON rd.SubSerie_idSubSerie = ss.idSubSerie
        LEFT JOIN
    subseriepermiso ssp ON ssp.SubSerie_idSubSerie = ss.idSubSerie
        LEFT JOIN
    documento doc ON rd.Documento_idDocumento = doc.idDocumento
        LEFT JOIN
    documentopermiso docp ON docp.Documento_idDocumento = doc.idDocumento
        LEFT JOIN
    documentopermisocompania dpc ON dpc.Documento_idDocumento = doc.idDocumento
        LEFT JOIN
    compania c ON dpc.Compania_idCompania = c.idCompania
    WHERE
        idCompania = '.Session::get("idCompania").' AND dp.Rol_idRol = '.$idRol["Rol_idRol"].' 
            AND sp.Rol_idRol = '.$idRol["Rol_idRol"].'
            AND ssp.Rol_idRol = '.$idRol["Rol_idRol"].'
            AND docp.Rol_idRol = '.$idRol["Rol_idRol"].'
            AND consultarDocumentoPermiso = 1 '.
            $Dep. '
    GROUP BY rd.Dependencia_idDependencia');


    $tituloDoc = DB::Select('
    SELECT 
    nombreDocumento, idDocumento
FROM
    retenciondocumental rd
        LEFT JOIN
    dependencia d ON rd.Dependencia_idDependencia = d.idDependencia
        LEFT JOIN
    dependenciapermiso dp ON dp.Dependencia_idDependencia = d.idDependencia
        LEFT JOIN
    serie s ON rd.Serie_idSerie = s.idSerie
        LEFT JOIN
    seriepermiso sp ON sp.Serie_idSerie = s.idSerie
        LEFT JOIN
    subserie ss ON rd.SubSerie_idSubSerie = ss.idSubSerie
        LEFT JOIN
    subseriepermiso ssp ON ssp.SubSerie_idSubSerie = ss.idSubSerie
        LEFT JOIN
    documento doc ON rd.Documento_idDocumento = doc.idDocumento
        LEFT JOIN
    documentopermiso docp ON docp.Documento_idDocumento = doc.idDocumento
        LEFT JOIN
    documentopermisocompania dpc ON dpc.Documento_idDocumento = doc.idDocumento
        LEFT JOIN
    compania c ON dpc.Compania_idCompania = c.idCompania
    WHERE
        idCompania = '.Session::get("idCompania").' AND dp.Rol_idRol = '.$idRol["Rol_idRol"].' 
            AND sp.Rol_idRol = '.$idRol["Rol_idRol"].'
            AND ssp.Rol_idRol = '.$idRol["Rol_idRol"].'
            AND docp.Rol_idRol = '.$idRol["Rol_idRol"].'
            AND cargarDocumentoPermiso = 1 '.
            $Doc. '
    GROUP BY rd.Documento_idDocumento');
?>


<div class="container">
  <ul class="nav nav-tabs">
    <?php
      for ($i=0; $i < count($tituloDep) ; $i++) 
      {
        $nombDep = get_object_vars($tituloDep[$i]);
        echo '<li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">'.$nombDep["nombreDependencia"].'<span class="caret"></span></a>
                <ul class="dropdown-menu">';
          for ($j=0; $j < count($tituloDoc); $j++) 
          { 
            $nombDoc = get_object_vars($tituloDoc[$j]);
            echo '
                  <li><a href="javascript:cuerpoGrid('.$nombDoc["idDocumento"].',\''.$query.'\');">'.$nombDoc["nombreDocumento"].'</a></li>';
          }
        echo '  </ul>
              </li>';
      }
    ?>
  </ul>
</div>

      <iframe id="formulario"  name="formulario" height="530px" width="100%" style="border:hidden;"></iframe>

</fieldset>
</div>

@stop