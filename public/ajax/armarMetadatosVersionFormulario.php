<?php

// INSERTO LOS METADATOS -->
$idRadicado = $_POST['Radicado_idRadicado'];
$idDocumento = $_POST['idDocumento'];
$numeroVersion = $_POST['version'];

$estructura = '';
$campos = ''; //Se inicializa vacío para luego concatenar los campos de documento propiedad en la consulta para el llenado de los metadatos dinamicamente
$condicion = ''; //Se inicializa vacío para concatenar el where del campo índice que viene desde el formulario de documentopropiedad
$divpropiedades = '';
$divclasificacion = '';
$ids = '';
$fechahoy = Carbon\Carbon::now(); //Defino cual es la fecha actual la cual quedara guardad al momento de actualizar la version

if ($numeroVersion == '') 
{
  
//Consulto para saber el numero de version maxima osea la ultima
$versionmaxima = DB::Select('SELECT max(numeroRadicadoVersion) from radicadoversion
where Radicado_idRadicado = '.$idRadicado.'');
$versionmaxima = get_object_vars($versionmaxima[0]);
}
else
{
  $versionmaxima = $numeroVersion;
}

//Consulto los metadatos llenos 
$metadatos = DB::table('radicadodocumentopropiedad')
->leftJoin('documentopropiedad','radicadodocumentopropiedad.DocumentoPropiedad_idDocumentoPropiedad', "=", 'documentopropiedad.idDocumentoPropiedad')
->leftjoin('documento','documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
->leftjoin('radicadoversion', 'radicadodocumentopropiedad.RadicadoVersion_idRadicadoVersion', "=", 'radicadoversion.idRadicadoVersion')
->select(DB::raw('radicadodocumentopropiedad.*,documentopropiedad.*,documento.tablaDocumento,RadicadoVersion_idRadicadoVersion, metadato.*'))
->where('radicadodocumentopropiedad.Radicado_idRadicado', "=", $idRadicado)
->where('numeroRadicadoVersion', "=", $versionmaxima)
->get();

$sistemainformacion = DB::Select('SELECT idSistemaInformacion from sistemainformacion left join documento on documento.SistemaInformacion_idSistemaInformacion = sistemainformacion.idSistemaInformacion
where idDocumento = '.$idDocumento);

if ($sistemainformacion == null || $sistemainformacion == '') 
  $sistemainformacion = 0;
else
  $sistemainformacion = get_object_vars($sistemainformacion[0]);


$tipoVersion = DB::select('SELECT tipoDocumentoVersion from documentoversion dv
  left join documento d on dv.Documento_idDocumento = d.idDocumento 
  where idDocumento = '.$idDocumento.'');

$documento = \App\DocumentoVersion::where('Documento_idDocumento', "=", $idDocumento)->lists('tipoDocumentoVersion','nivelDocumentoVersion');

$estructura .= 
'<div class="form-group col-md-4 form-inline" id="test">
  <label id="fechaRadicado" class= "col-sm-3 control-label">Fecha</label>
    <div class="col-sm-12">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-calendar "></i>
        </span>
        <input id="FNV_Fecha" readonly name="FNV_Fecha" class="form-control" type="text" value="'.$fechahoy.'">
      </div>
    </div>
</div>

<div class="form-group col-md-4 form-inline" id="test">
  <label id="RadicadoVersion" class= "col-sm-2 control-label">Version</label>
    <div class="col-sm-12">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-server"></i>
        </span>';
        // Si la version del documento es igual a 1 no saldrá la lista de selección si no un campo input para evitarle al usuario abrir la lista de selección para seleccionar solo una opción sino que esta opción vendrá en el input por defecto
      $tipoV = get_object_vars($tipoVersion[0]); 
      if (count($tipoV) == 1) 
      {
        foreach ($documento as $tipoDoc => $nomTipo) 
              {
                $estructura .='<input id="FNV_tipoVersion" type="text" readonly class="form-control" name="FNV_tipoVersion" value = "'.$nomTipo.'">';
              }
        $estructura .= '<script>cambiarNumeroVersionFormulario('.$tipoDoc.')</script>';
      }
      else
        // Si la version del documento es superior a 1 entonces es saldrá la lista de selección
      {
        $estructura .='
        <select id="FNV_tipoVersion" name="FNV_tipoVersion" onchange="cambiarNumeroVersionFormulario(this.value)" class="form-control" >
          <option value="0">Inicial</option>';
            $tipoV = get_object_vars($tipoVersion[0]);
              foreach ($documento as $tipoDoc => $nomTipo) 
              {
                $estructura .= '<option value="'.$tipoDoc.'">'.$nomTipo.'</option>';
              }                      
  $estructura .= 
        '</select>';
      }
  $estructura .=
      '</div>
    </div>
</div>


</br> </br> </br> </br> </br>';


//Empiezo a llenar los metadatos 
$estructura .= '<div  class="tab-pane" id="propiedadd">';
    //Ciclo que cuenta los metadatos y arma el formulario dependiendo de los campos que hayan en documentopropiedad
    for($i = 0; $i < count($metadatos); $i++)
    {
      //Convertir array a string
      $nombremetadato = get_object_vars($metadatos[$i]);
      $style = '';
      $eventoblur ='';
      $clase = '';
      //Se pregunta por el incidedocumentopropiedad y en caso de estar chuleado en el formulario de documentos, este sera el campo por el cuel
      //se van a llenar los metadatos de manera automatica
      if ($nombremetadato['indiceDocumentoPropiedad'] == 1) 
      {
        $style = 'style="background-color: #b7ffc3"';
        $eventoblur = 'onchange="llenarMetadatos(this.value,\'V_\',\''.$sistemainformacion["idSistemaInformacion"].'\');"';
        $clase = 'campoBusqueda';
      }

      $readonly = '';
      $disabled = '';
      //El metadato que tiene chulo en version sera modificable mientras el que no lo tenga se mostrara en pantalla como solo lectura
      if ($nombremetadato['versionDocumentoPropiedad'] == 1) 
      {
        $readonly = '';
        $disabled = '';
      }
      else
      {
        $readonly = 'readonly';
        $disabled = 'disabled';
      }

      $estructura .= 
      '<div id="camposmetadatos">
        <div class="form-group col-md-6 form-inline" id="test">
          <label id="'.$nombremetadato["idDocumentoPropiedad"].'_lbl" class= "col-md-4 control-label">'.str_replace('_', ' ',$nombremetadato["tituloMetadato"]).'</label>
          <div class="col-sm-8">
            <div class="input-group">
              <span '.$style.' class="input-group-addon">
                <i class="fa fa-pencil-square-o "></i>
              </span>'; 

      $estructura .= 
      '<input id="FNV_Radicado_idRadicado" name="FNV_Radicado_idRadicado" class="form-control" type="hidden" value="'.$nombremetadato["Radicado_idRadicado"].'">'; //Se guarda en un campo oculto el id del radicado con un V_ adelante para saber que hace parte de la nueva versión

      $estructura .= 
      '<input id="idRadicadoDocumentoPropiedad" name="idRadicadoDocumentoPropiedad[]" class="form-control" type="hidden" value="'.$nombremetadato["idRadicadoDocumentoPropiedad"].'">'; //En un campo oculto se guarda el id de radicad documento propiedad
     
      $estructura .= 
      '<input id="id_'.$nombremetadato["idDocumentoPropiedad"].'"  name="id_'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="hidden" value="'.$nombremetadato["idDocumentoPropiedad"].'">'; //Guardo en un campo hidden el id de documento propiedad poniendo la palabra id_ delante de este

      //Se abre un switch para construir el formulario de metadatos dependiendo de que campos (tipoMetadato) viene desde el maestro de documentos                                
      switch ($nombremetadato["tipoMetadato"]) 
      {
          case "Texto":
                $estructura .= '<input id="V_'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' '.$eventoblur.'  name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="text" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">'; 
          break;

          case "Fecha":
                $estructura .='<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' '.$eventoblur.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="date" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">
                    <script type="text/javascript">
                      $("#'.$nombremetadato["idDocumentoPropiedad"].'").datetimepicker(({format: "YYYY-MM-DD"}));
                    </script> ';
          break;

          case "Numero":
                $estructura .= '<input id="FNV_'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' '.$eventoblur.' name="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' class="form-control '.$clase.'" type="number" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">';
          break;

          case "Hora":
                $estructura .= '<input id="FNV_'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="date" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">
                    <script type="text/javascript">
                      $("#'.$nombremetadato["idDocumentoPropiedad"].'").datetimepicker(({format: "HH:mm:ss"}));
                    </script> ';
          break;

          case "Lista":
              $lista = DB::table('sublista')
              ->select (DB::raw('idSubLista, nombreSubLista, Lista_idLista'))
              ->where('idSubLista', "=", $nombremetadato["valorRadicadoDocumentoPropiedad"])
              ->get();

              $idLista = $nombremetadato['Lista_idLista'];

                $estructura .= '<script> $("#FNV_'.$nombremetadato["idDocumentoPropiedad"].' option:not(:selected)").attr("disabled",true) </script>';

                $estructura .= '<select id="FNV_'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" 
                                        class="form-control" >
                                      <option value="0">Seleccione</option>';
                for($c = 0; $c < count($idLista); $c++) 
                {
                  //Convertir array a string
                  $sublista = get_object_vars($lista[0]);
                  $estructura .= '<option value="'.$sublista["idSubLista"].'" '.($sublista["idSubLista"] == $nombremetadato["valorRadicadoDocumentoPropiedad"] ? 'selected="selected"' : '') .'>'.$sublista["nombreSubLista"].'</option>';
                  $estructura .= '<script> $("#FNV_'.$nombremetadato["idDocumentoPropiedad"].' option:not(:selected)").attr("disabled",true) </script>';

                  // $estructura .='<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.'  name="nombre'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="text" placeholder="Seleccione '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'" value="'.$sublista["nombreSubLista"].'" onchange="abrirModal('.$idLista.',this);">';

                  // $estructura .='<input id="cod'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="hidden" value="'.$sublista["idSubLista"].'">';
                }

                $estructura .= '</select>';
          break;

          case "Editor":
                $estructura .= '<textarea id="FNV_'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control">'.$nombremetadato["editorRadicadoDocumentoPropiedad"].'</textarea>';
          break;

          case "EleccionUnica":
                $defaultR = explode(",",$nombremetadato['valorBaseMetadato']);
                $valoresR = $nombremetadato["valorRadicadoDocumentoPropiedad"];
              
                for ($j=0; $j <count($defaultR); $j++) 
                {
                  $estructura .= '<label class="col-md-12"><input id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" '.($valoresR == $defaultR[$j] ? 'checked="checked"' : '').'  type="radio" value="'.$defaultR[$j].'" '.$disabled.'>&nbsp; &nbsp; &nbsp; &nbsp;'.$defaultR[$j].'</label>';
                }
          break;
          
          case "EleccionMultiple":
                $default = explode(",",$nombremetadato['valorBaseMetadato']);
                $valores = explode(",", $nombremetadato["valorRadicadoDocumentoPropiedad"]);

                for ($j=0; $j <count($default); $j++) 
                {
                  $estructura .= '<label class="col-md-12"><input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$disabled.' '.($valores[$j] == 1 ? 'checked="checked"' : '').'  type="checkbox" onclick="validarCheckbox(this, '.$nombremetadato["idDocumentoPropiedad"].$j.')">&nbsp; &nbsp; &nbsp; &nbsp;'.$default[$j].'</label>';
                  $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].$j.'" name="'.$nombremetadato["idDocumentoPropiedad"].'[]" value ="'.$valores[$j].'" type="hidden">';
                }
          break;

          default:
                $estructura .= '<input id="FNV_'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">';
      }

                $ids .= $nombremetadato['idDocumentoPropiedad'].','; 
                $estructura .= 
                '</div>
              </div>
            </div>
          </div>';
      
            $campos .=$nombremetadato['campoDocumentoPropiedad'].', '; //Se le envian a la variable campos los registros de campoDocumentPropiedad y se separan por comas (,)      
            if ($nombremetadato['indiceDocumentoPropiedad'] == 1) //Se pregunta si en el formulario hay un campo indice
            {
                $condicion .= $nombremetadato['campoDocumentoPropiedad'].' = ? and ';          
                 //Se guarda en la variable condicion el campo indice concatenado con = ? and por si hay mas de un campo indice
            } 
    }//Se cierra e for

    //
    $estructura .= //Guardo en un campo oculto los ids de los inputs y en otro el id del documento antecedido con V_
              '<input id="campos" name="campos" value="'.$ids.'" class="form-control" type="hidden">
              <input id="FNV_idDocumento" name="FNV_idDocumento" type="hidden" value="'.$nombremetadato["Documento_idDocumento"].'">   
        </div>
      </div>
    </div>';

    $campos = substr($campos, 0, strlen($campos)-2); //a los registros se le quita el ultimo caracter en este caso la ultima coma (,)
    $condicion = substr($condicion, 0, strlen($condicion)-4); //  se le hace un substr al campo condicion eliminando la palabra and y un espacio que hay despues de esta

    //Se guarda en un campo hidden la consulta
    $estructura .= '<input type="hidden" id="consulta" name="consulta" value="SELECT '.$campos.' from '.$nombremetadato['tablaDocumento'].' where '.$condicion.'">';

    //Se realiza una consulta para determinar el numero de la versión de radicado actual
    $numeroVersion = DB::Select('SELECT max(numeroRadicadoVersion) as version from radicadoversion
    where Radicado_idRadicado = '.$idRadicado.'');
    $numeroVersion = get_object_vars($numeroVersion[0]);

    $estructura .='<input id="FNV_numeroFormularioVersion" name="FNV_numeroFormularioVersion" value="'.$numeroVersion['version'].'" type="hidden">'; 

    // $estructura .='<script type="text/javascript">
    //       $(document).ready(function (){
    //         var config = {
    //           ".chosen-select"           : {},
    //           ".chosen-select-deselect"  : {allow_single_deselect:true},
    //           ".chosen-select-no-single" : {disable_search_threshold:10},
    //           ".chosen-select-no-results": {no_results_text:"Oops, nothing found!"},
    //           ".chosen-select-width"     : {width:"95%"}
    //         }
    //         for (var selector in config) {
    //           $(selector).chosen(config[selector]);
    //         }
    //     });
    //     </script>';

    //En un array guardo lo que voy a devolver en el json_encode       
    $respuesta = array("estructura"=>$estructura,"divpropiedades"=>$estructura,"divclasificacion"=>$estructura);

    echo json_encode($respuesta);
?>  

