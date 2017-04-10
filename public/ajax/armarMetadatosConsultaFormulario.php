<?php

// INSERTO LOS METADATOS -->
$idRadicado = $_POST['Radicado_idRadicado'];
$numeroVersion = $_POST['version'];
$idDocumento = $_POST['idDocumento'];

$estructura = '';
$campos = ''; //Se inicializa vacío para luego concatenar los campos de documento propiedad en la consulta para el llenado de los metadatos dinamicamente
$condicion = ''; //Se inicializa vacío para concatenar el where del campo índice que viene desde el formulario de documentopropiedad
$divpropiedades = '';
$divclasificacion = '';
$ids = '';

if ($numeroVersion == '') 
{
  
  //Consulto para saber el numero de version maxima osea la ultima
  $versionmaxima = DB::Select('SELECT max(numeroRadicadoVersion) as version from radicadoversion
  where Radicado_idRadicado = '.$idRadicado.'');
  $tmp = get_object_vars($versionmaxima[0]);
  $versionmaxima = $tmp['version'];
}
else
{
  $versionmaxima = $numeroVersion;
}
$estructura .='<input id="versionMaxima" name="versionMaxima" type="hidden" value="'.$versionmaxima.'">';
//Consulto los metadatos llenos teniendo como condicion que pertenezcan al id del radicado y a la versio que selecciono en el formulario
$metadatos = DB::table('radicadodocumentopropiedad')
->leftJoin('documentopropiedad','radicadodocumentopropiedad.DocumentoPropiedad_idDocumentoPropiedad', "=", 'documentopropiedad.idDocumentoPropiedad')
->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
->leftjoin('documento','documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
->leftjoin('radicadoversion', 'radicadodocumentopropiedad.RadicadoVersion_idRadicadoVersion', "=", 'radicadoversion.idRadicadoVersion')
->select(DB::raw('versionDocumentoPropiedad,controlVersionDocumento,radicadodocumentopropiedad.*,documentopropiedad.*,documento.tablaDocumento,RadicadoVersion_idRadicadoVersion, metadato.*'))
->where('radicadodocumentopropiedad.Radicado_idRadicado', "=", $idRadicado)
->where('numeroRadicadoVersion', "=", $versionmaxima)
->get();


$sistemainformacion = DB::Select('SELECT idSistemaInformacion from sistemainformacion left join documento on documento.SistemaInformacion_idSistemaInformacion = sistemainformacion.idSistemaInformacion
where idDocumento = '.$idDocumento);

if ($sistemainformacion == null || $sistemainformacion == '') 
  $sistemainformacion = 0;
else
  $sistemainformacion = get_object_vars($sistemainformacion[0]);


//Consulto el codigo y la fecha del radicado 
$encabezado = DB::table('radicadoversion')
->select(DB::raw('fechaRadicado'))
->where('Radicado_idRadicado', "=", $idRadicado)
->where('numeroRadicadoVersion', "=", $versionmaxima)
->get();

$encabezadoRadicado = get_object_vars($encabezado[0]);

//Se empieza a llenar el formulario en el siguiente orden
//1) Codigo 2) Fecha 3)Dependencia 4) Serie 5) Sub Serie 6) Etiquetas
//En la siguiente pestaña se llenan los metadatos
$estructura .= 
'<div class="form-group col-md-4 form-inline" id="test">
  <label id="fechaFormularioA" class= "col-sm-3 control-label">Fecha de creación</label>
    <div class="col-sm-12">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-calendar "></i>
        </span>
        <input id="fechaFormularioA" readonly name="fechaFormularioA" class="form-control" type="text" value="'.$encabezadoRadicado["fechaRadicado"].'">
      </div>
    </div>
</div>

</br> </br></br> </br> </br>';



//Empiezo a llenar los metadatos 
$estructura .= '<div  class="tab-pane" id="propiedad">';
    //Ciclo que cuenta los metadatos y arma el formulario dependiendo de los campos que hayan en documentopropiedad
    for($i = 0; $i < count($metadatos); $i++)
    {
      //Convertir array a string
      $nombremetadato = get_object_vars($metadatos[$i]);
      $style = '';
      $eventoblur ='';
      $clase = '';
      if ($nombremetadato['indiceDocumentoPropiedad'] == 1) 
      {
        $style = 'style="background-color: #b7ffc3"';
        $eventoblur = 'onchange="llenarMetadatos(this.value,\'\',\''.$sistemainformacion["idSistemaInformacion"].'\');"';
        $clase = 'campoBusqueda';
      }

      $readonly = '';
      $disabled = '';
      //El metadato que tiene chulo en version sera modificable mientras el que no lo tenga se mostrara en pantalla como solo lectura
      if ($nombremetadato['controlVersionDocumento'] != 1 and $nombremetadato['versionDocumentoPropiedad'] == 1)
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
      '<input id="F_Radicado_idRadicado" name="F_Radicado_idRadicado" class="form-control" type="hidden" value="'.$nombremetadato["Radicado_idRadicado"].'">'; //Guardo en un campo hidden el id del radicado

      $estructura .= 
      '<input id="F_RadicadoVersion_idRadicadoVersion" name="F_RadicadoVersion_idRadicadoVersion" class="form-control" type="hidden" value="'.$nombremetadato["RadicadoVersion_idRadicadoVersion"].'">'; //Guardo en un campo hidden el id del radicado version

      $estructura .= 
      '<input id="idRadicadoDocumentoPropiedad" name="idRadicadoDocumentoPropiedad[]" class="form-control" type="hidden" value="'.$nombremetadato["idRadicadoDocumentoPropiedad"].'">'; //Guardo en un campo hidden el id del radicado documento propiedad
     
      $estructura .= 
      '<input id="id_'.$nombremetadato["idDocumentoPropiedad"].'" name="id_'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="hidden" value="'.$nombremetadato["idDocumentoPropiedad"].'">'; //Guardo en un campo hidden el id de documento propiedad poniendo la palabra id_ delante de este

      //Se abre un switch para construir el formulario de metadatos dependiendo de que campos (tipoMetadato) viene desde el maestro de documentos                                
      switch ($nombremetadato["tipoMetadato"]) 
      {
          case "Texto":
                $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="text" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">';
          break;

          case "Fecha":
                $estructura .='<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="date" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">
                    <script type="text/javascript">
                      $("#'.$nombremetadato["idDocumentoPropiedad"].'").datetimepicker(({format: "YYYY-MM-DD"}));
                    </script> ';
          break;

          case "Numero":
                $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' class="form-control '.$clase.'" type="number" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">';
          break;

          case "Hora":
                $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="date" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">
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

                  $estructura .= '<script> $("#FEV_'.$nombremetadato["idDocumentoPropiedad"].' option:not(:selected)").attr("disabled",true) </script>';

                    $estructura .= '<select id="FEV_'.$nombremetadato["idDocumentoPropiedad"].'"  name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" >
                        <option value="0">Seleccione</option>';
                          for($c = 0; $c < count($idLista); $c++) 
                          {
                            //Convertir array a string
                            $sublista = get_object_vars($lista[0]);
                            $estructura .= '<option value="'.$sublista["idSubLista"].'" '.($sublista["idSubLista"] == $nombremetadato["valorRadicadoDocumentoPropiedad"] ? 'selected="selected"' : '') .'>'.$sublista["nombreSubLista"].'</option>';
                            $estructura .= '<script> $("#FEV_'.$nombremetadato["idDocumentoPropiedad"].' option:not(:selected)").attr("disabled",true) </script>';

                            // $estructura .='<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.'  name="nombre'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="text" placeholder="Seleccione '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'" value="'.$sublista["nombreSubLista"].'" onchange="abrirModal('.$idLista.',this);">';

                            // $estructura .='<input id="cod'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="hidden" value="'.$sublista["idSubLista"].'">';
                          }
                    $estructura .= '</select>';
          break;

          case "Editor":
                $estructura .= '<textarea id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control">'.$nombremetadato["editorRadicadoDocumentoPropiedad"].'</textarea>';
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
                  $estructura .= '<label class="col-md-12"><input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.($valores[$j] == 1 ? 'checked="checked"' : '').'  type="checkbox" '.$disabled.' onclick="validarCheckbox(this, '.$nombremetadato["idDocumentoPropiedad"].$j.')">&nbsp; &nbsp; &nbsp; &nbsp;'.$default[$j].'</label>';
                  $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].$j.'" name="'.$nombremetadato["idDocumentoPropiedad"].'[]" value ="'.$valores[$j].'" type="hidden">';
                }
          break;

          default:
                $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$readonly.' name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">';
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
    
$estructura .= //Guardo en un campo oculto los ids de los inputs y en otro el id del documento
'<input id="campos" name="campos" value="'.$ids.'" class="form-control" type="hidden">
  <input id="idDocumentoFC" name="idDocumentoFC" type="hidden" value="'.$nombremetadato["Documento_idDocumento"].'">   
    </div>
  </div>
</div>';

//Consulto como se llevara el versionamiento y que cambios se le realizaran (control de version) y guardo en un campo oculto este valor
$version = DB::Select('SELECT controlVersionDocumento from documento
where idDocumento = '.$nombremetadato['Documento_idDocumento'].'');
$versionF = get_object_vars($version[0]);

$estructura .='<input id="controlVersionFormulario" name="controlVersionFormulario" value="'.$versionF['controlVersionDocumento'].'" type="hidden">';

$campos = substr($campos, 0, strlen($campos)-2); //a los registros se le quita el ultimo caracter en este caso la ultima coma (,)
$condicion = substr($condicion, 0, strlen($condicion)-4); //  se le hace un substr al campo condicion eliminando la palabra and y un espacio que hay despues de esta

//Se guarda en un campo hidden la consulta
$estructura .= '<input type="hidden" id="consulta" name="consulta" value="SELECT '.$campos.' from '.$nombremetadato['tablaDocumento'].' where '.$condicion.'">';

// En este campo guado el tipo si es store (guardar) o es update (actualizar) dependiendo de lo que escoja el usuario en los botones
// Lapiz (update) o nueva versión (store)
$estructura .='<input id="tipo" name="tipo" type="hidden" value="">';

// Se crea un campo hidden con value 0 y 1.0 ya que al ser update no cambia ni el numero ni el tipo de la versión
$estructura .='<input id="F_numeroRadicadoVersionFormulario" name="F_numeroRadicadoVersionFormulario" type="hidden" value="1.0">';
$estructura .='<input id="F_tipoRadicadoVersioFormulario" name="F_tipoRadicadoVersioFormulario" type="hidden" value="0">';

$estructura .='<input id="controlVersionDocumento" name="controlVersionDocumento" type="hidden" value="'.$nombremetadato['controlVersionDocumento'].'">';

// $estructura .='<script type="text/javascript">
//           $(document).ready(function (){
//             var config = {
//               ".chosen-select"           : {},
//               ".chosen-select-deselect"  : {allow_single_deselect:true},
//               ".chosen-select-no-single" : {disable_search_threshold:10},
//               ".chosen-select-no-results": {no_results_text:"Oops, nothing found!"},
//               ".chosen-select-width"     : {width:"95%"}
//             }
//             for (var selector in config) {
//               $(selector).chosen(config[selector]);
//             }
//         });
//         </script>';

//En un array guardo lo que voy a devolver en el json_encode       
$respuesta = array("estructura"=>$estructura,"divpropiedades"=>$estructura,"divclasificacion"=>$estructura);

echo json_encode($respuesta);
?>  