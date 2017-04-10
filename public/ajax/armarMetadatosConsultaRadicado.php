<?php

// INSERTO LOS METADATOS -->
$idRadicado = $_POST['Radicado_idRadicado'];
$numeroVersion = $_POST['version'];

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
//Consulto los metadatos llenos teniendo como condicion que pertenezcan al id del radicado y a la version que selecciono en el formulario
$metadatos = DB::table('radicadodocumentopropiedad')
->leftJoin('documentopropiedad','radicadodocumentopropiedad.DocumentoPropiedad_idDocumentoPropiedad', "=", 'documentopropiedad.idDocumentoPropiedad')
->leftjoin('documento','documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
->leftjoin('radicadoversion', 'radicadodocumentopropiedad.RadicadoVersion_idRadicadoVersion', "=", 'radicadoversion.idRadicadoVersion')
->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
->select(DB::raw('controlVersionDocumento,radicadodocumentopropiedad.*,documentopropiedad.*,documento.tablaDocumento,RadicadoVersion_idRadicadoVersion, metadato.*'))
->where('radicadodocumentopropiedad.Radicado_idRadicado', "=", $idRadicado)
->where('numeroRadicadoVersion', "=", $versionmaxima)
->get();

//Consulto el codigo y la fecha del radicado 
$encabezado = DB::table('radicado')
->leftjoin('radicadoversion','radicadoversion.Radicado_idRadicado', "=", "radicado.idRadicado")
->select(DB::raw('codigoRadicado, fechaRadicado'))
->where('idRadicado', "=", $idRadicado)
->where('numeroRadicadoVersion', "=", $versionmaxima)
->get();

$encabezadoRadicado = get_object_vars($encabezado[0]);

//Consulto los datos de clasificación del radicado 
$propiedades = DB::select('SELECT Dependencia_idDependencia, Serie_idSerie, SubSerie_idSubSerie, ubicacionEstanteRadicado, group_concat(idRadicadoEtiqueta) as idRadicadoEtiqueta, 
group_concat(Etiqueta_idEtiqueta) as Etiqueta_idEtiqueta, 
group_concat(nombreEtiqueta) as nombreEtiqueta
from radicado r
left join radicadoetiqueta re
on re.Radicado_idRadicado = r.idRadicado
left join etiqueta e
on re.Etiqueta_idEtiqueta = e.idEtiqueta
where idRadicado ='.$idRadicado.'
group by idRadicado');

//Consulto la dependencia, serie y sub serie
$dependencia = \App\Dependencia::All()->lists('nombreDependencia','idDependencia');
$serie = \App\Serie::All()->lists('nombreSerie','idSerie');
$subserie = \App\SubSerie::All()->lists('nombreSubSerie','idSubSerie');


//Se empieza a llenar el formulario en el siguiente orden
//1) Codigo 2) Fecha 3)Dependencia 4) Serie 5) Sub Serie 6) Etiquetas
//En la siguiente pestaña se llenan los metadatos
$estructura .= //Este es el codigo del radicado
'<div class="form-group col-md-4 form-inline" id="test">
  <label id="codigoRadicado" class= "col-sm-3 control-label">Código</label>
    <div class="col-sm-12">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-barcode "></i>
        </span>
        <input id="codigoRadicado" readonly name="codigoRadicado" class="form-control" type="text" value="'.$encabezadoRadicado["codigoRadicado"].'"></div>
      </div>
    </div>
</div>


<div class="form-group col-md-4 form-inline" id="test">
  <label id="fechaRadicado" class= "col-sm-3 control-label">Fecha</label>
    <div class="col-sm-12">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-calendar "></i>
        </span>
        <input id="fechaRadicado" readonly name="fechaRadicado" class="form-control" type="text" value="'.$encabezadoRadicado["fechaRadicado"].'">
      </div>
    </div>
</div>

</br> </br></br> </br>

<div class="tabbable">
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#clasificacion">Clasificacion</a></li>
    <li><a data-toggle="tab" href="#propiedad">Propiedades</a></li>
  </ul>
<div class="tab-content">';

$estructura .= 
'<div class="tab-pane active" id="clasificacion">
  <div class="form-group" id="test">
    <label id="lblDependencia_idDependencia" class= "col-sm-3 control-label">Dependencia</label>
      <div class="col-sm-12">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
            <select id="Dependencia_idDependencia" disabled name="Dependencia_idDependencia" onchange="buscarDependencia(this.value, document.getElementById(\'idDocumento\').value)" class="form-control" >
                <option value="0">Seleccione</option>';
                  $nombrepropiedades = get_object_vars($propiedades[0]);
                    foreach ($dependencia as $idDep => $nomDep) 
                    {
                       $estructura .= '<option value="'.$idDep.'"'.($idDep == $nombrepropiedades["Dependencia_idDependencia"] ? 'selected="selected"' : '') .' >'.$nomDep.'</option>';
                    }                      
                                          
$estructura .= '</select>
        </div>
      </div>

    <label id="lblSerie_idSerie" class= "col-sm-3 control-label">Serie</label>
      <div class="col-sm-12">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
            <select id="Serie_idSerie" disabled name="Serie_idSerie" onchange="buscarSubSerie(this.value, document.getElementById(\'idDocumento\').value)" class="form-control" >
                <option value="0">Seleccione</option>';
                  $nombrepropiedades = get_object_vars($propiedades[0]);
                  foreach ($serie as $idSerie => $nomSerie) 
                  {
                    $estructura .= '<option value="'.$idSerie.'"'.($idSerie == $nombrepropiedades["Serie_idSerie"] ? 'selected="selected"' : '') .' >'.$nomSerie.'</option>';
                  }                      
                                          
$estructura .= '</select>
        </div>
      </div>

    <label id="lblSubSerie_idSubSerie" class= "col-sm-3 control-label">Sub Serie</label>
      <div class="col-sm-12">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-pencil-square-o"></i>
          </span>
            <select id="SubSerie_idSubSerie" disabled name="SubSerie_idSubSerie" class="form-control" >
              <option value="0">Seleccione</option>';
                  $nombrepropiedades = get_object_vars($propiedades[0]);
                  foreach ($subserie as $idSubSerie => $nomSubSerie) 
                  {
                    $estructura .= '<option value="'.$idSubSerie.'"'.($idSubSerie == $nombrepropiedades["SubSerie_idSubSerie"] ? 'selected="selected"' : '') .' >'.$nomSubSerie.'</option>';
                  }                      
                                          
$estructura .= '</select>
        </div>
      </div>

    <label id="ubicacion" class= "col-sm-3 control-label">P.L</label>
      <div class="col-sm-12">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-sitemap "></i>
          </span>
          <input id="ubicacionEstanteRadicado" readonly name="ubicacionEstanteRadicado" class="form-control" type="text" value="'.$nombrepropiedades["ubicacionEstanteRadicado"].'">
        </div>
      </div>

    <label id="etiquetas" class= "col-sm-3 control-label">Etiquetas</label>
      <div class="col-sm-12">
        <div class="input-group">
          <span class="input-group-addon">
            <i class="fa fa-tags "></i>
          </span>
          <input id="nombreEtiqueta" readonly name="nombreEtiqueta" class="form-control" type="text" onclick="mostrarModalEtiquetaConsulta()" value="'.$nombrepropiedades["nombreEtiqueta"].'">
          <input id="etiquetaRadicado" name="etiquetaRadicado" class="form-control" type="hidden" value="'.$nombrepropiedades["Etiqueta_idEtiqueta"].'">   
          <input id="idRadicadoEtiqueta" name="idRadicadoEtiqueta" class="form-control" type="hidden" value="'.$nombrepropiedades["idRadicadoEtiqueta"].'">
        </div>
      </div>
    </div>
  </div>';


//Empiezo a llenar los metadatos 
$estructura .= '<div  class="tab-pane" id="propiedad">';
    //Ciclo que cuenta los metadatos y arma el formulario dependiendo de los campos que hayan en documentopropiedad
    for($i = 0; $i < count($metadatos); $i++)
    {
      //Convertir array a string
      $nombremetadato = get_object_vars($metadatos[$i]);
      $style = '';
      $eventoblur ='';
      if ($nombremetadato['indiceDocumentoPropiedad'] == 1) 
      {
        $style = 'style="background-color: #b7ffc3"';
        $eventoblur = 'onchange="llenarMetadatos(this.value, \'\');"';
      }

      if ($nombremetadato['tipoMetadato'] == 'Editor') 
      {
        $editor = $nombremetadato['idDocumentoPropiedad'].',';
      }

      $estructura .= 
      '<div id="camposmetadatos">
        <div class="form-group" id="test">
          <label id="'.$nombremetadato["idDocumentoPropiedad"].'_lbl" class= "col-sm-2 control-label">'.$nombremetadato["tituloMetadato"].'</label>
            <div class="col-sm-12">
              <div class="input-group">
                <span '.$style.' class="input-group-addon">
                  <i class="fa fa-pencil-square-o "></i>
                </span>';

      $estructura .= 
      '<input id="Radicado_idRadicado" name="Radicado_idRadicado" class="form-control" type="hidden" value="'.$nombremetadato["Radicado_idRadicado"].'">'; //Guardo en un campo hidden el id del radicado

      $estructura .= 
      '<input id="RadicadoVersion_idRadicadoVersion" name="RadicadoVersion_idRadicadoVersion" class="form-control" type="hidden" value="'.$nombremetadato["RadicadoVersion_idRadicadoVersion"].'">'; //Guardo en un campo hidden el id del radicado version

      $estructura .= 
      '<input id="idRadicadoDocumentoPropiedad" name="idRadicadoDocumentoPropiedad[]" class="form-control" type="hidden" value="'.$nombremetadato["idRadicadoDocumentoPropiedad"].'">'; //Guardo en un campo hidden el id del radicado documento propiedad
     
      $estructura .= 
      '<input id="id_'.$nombremetadato["idDocumentoPropiedad"].'" name="id_'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="hidden" value="'.$nombremetadato["idDocumentoPropiedad"].'">'; //Guardo en un campo hidden el id de documento propiedad poniendo la palabra id_ delante de este

      //Se abre un switch para construir el formulario de metadatos dependiendo de que campos (tipoMetadato) viene desde el maestro de documentos                                
      switch ($nombremetadato["tipoMetadato"]) 
      {
          case "Texto":
                $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' readonly name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="text" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">';
          break;

          case "Fecha":
                $estructura .='<input id="'.$nombremetadato["idDocumentoPropiedad"].'" readonly name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="date" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">
                    <script type="text/javascript">
                      $("#'.$nombremetadato["idDocumentoPropiedad"].'").datetimepicker(({format: "YYYY-MM-DD"}));
                    </script> ';
          break;

          case "Numero":
                $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' readonly name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="number" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">';
          break;

          case "Hora":
                $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" readonly name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="date" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">
                  <script type="text/javascript">
                    $("#'.$nombremetadato["idDocumentoPropiedad"].'").datetimepicker(({format: "HH:mm:ss"}));
                  </script> ';
          break;

          case "Lista":
          $lista = DB::table('sublista')
                  ->select (DB::raw('idSubLista, nombreSubLista, Lista_idLista'))
                  ->where('Lista_idLista', "=", $nombremetadato["Lista_idLista"])
                  ->get();

                    $estructura .= '<select id="'.$nombremetadato["idDocumentoPropiedad"].'" disabled name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" >
                        <option value="0">Seleccione</option>';
                          for($c = 0; $c < count($lista); $c++) 
                          {
                            //Convertir array a string
                            $sublista = get_object_vars($lista[$c]);
                            $estructura .= '<option value="'.$sublista["idSubLista"].'" '.($sublista["idSubLista"] == $nombremetadato["valorRadicadoDocumentoPropiedad"] ? 'selected="selected"' : '') .'>'.$sublista["nombreSubLista"].'</option>';
                          }
                    $estructura .= '</select>';
          break;

          case "Editor":
                $estructura .= '<textarea id="'.$nombremetadato["idDocumentoPropiedad"].'" readonly name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control">'.$nombremetadato["editorRadicadoDocumentoPropiedad"].'</textarea>';
          break;

          case "EleccionUnica":
                $defaultR = explode(",",$nombremetadato['valorBaseMetadato']);
                $valoresR = $nombremetadato["valorRadicadoDocumentoPropiedad"];
              
                for ($j=0; $j <count($defaultR); $j++) 
                {
                  $estructura .= '<label class="col-md-12"><input id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" '.($valoresR == $defaultR[$j] ? 'checked="checked"' : '').'  type="radio" value="'.$defaultR[$j].'" disabled>&nbsp; &nbsp; &nbsp; &nbsp;'.$defaultR[$j].'</label>';
                }
          break;
          
          case "EleccionMultiple":
                $default = explode(",",$nombremetadato['valorBaseMetadato']);
                $valores = explode(",", $nombremetadato["valorRadicadoDocumentoPropiedad"]);

                for ($j=0; $j <count($default); $j++) 
                {
                  $estructura .= '<label class="col-md-12"><input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.($valores[$j] == 1 ? 'checked="checked"' : '').'  type="checkbox" disabled onclick="validarCheckbox(this, '.$nombremetadato["idDocumentoPropiedad"].$j.')">&nbsp; &nbsp; &nbsp; &nbsp;'.$default[$j].'</label>';
                  $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].$j.'" name="'.$nombremetadato["idDocumentoPropiedad"].'[]" value ="'.$valores[$j].'" type="hidden">';
                }
          break;

          default:
                $estructura .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" readonly name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" value="'.$nombremetadato["valorRadicadoDocumentoPropiedad"].'">';
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
  <input id="idDocumento" name="idDocumento" type="hidden" value="'.$nombremetadato["Documento_idDocumento"].'">   
    </div>
  </div>
</div>';

//Consulto como se llevara el versionamiento y que cambios se le realizaran (control de version) y guardo en un campo oculto este valor
$version = DB::Select('SELECT controlVersionDocumento from documento
where idDocumento = '.$nombremetadato['Documento_idDocumento'].'');
$version = get_object_vars($version[0]);
$estructura .='<input id="controlVersion" name="controlVersion" value="'.$nombremetadato['controlVersionDocumento'].'" type="hidden">';

$campos = substr($campos, 0, strlen($campos)-2); //a los registros se le quita el ultimo caracter en este caso la ultima coma (,)
$condicion = substr($condicion, 0, strlen($condicion)-4); //  se le hace un substr al campo condicion eliminando la palabra and y un espacio que hay despues de esta

//Se guarda en un campo hidden la consulta
$estructura .= '<input type="hidden" id="consulta" name="consulta" value="SELECT '.$campos.' from '.$nombremetadato['tablaDocumento'].' where '.$condicion.'">';

// En este campo guado el tipo si es store (guardar) o es update (actualizar) dependiendo de lo que escoja el usuario en los botones
// Lapiz (update) o nueva versión (store)
$estructura .='<input id="tipo" name="tipo" type="hidden" value="">';

// Se crea un campo hidden con value 0 y 1.0 ya que al ser update no cambia ni el numero ni el tipo de la versión
$estructura .='<input id="numeroRadicadoVersionConsulta" name="numeroRadicadoVersionConsulta" type="hidden" value="1.0">';
$estructura .='<input id="tipoRadicadoVersioConsulta" name="tipoRadicadoVersioConsulta" type="hidden" value="0">';

// $estructura .='<script>
//   CKEDITOR.replace(("'.$editor.'"), {
//       fullPage: true,
//       allowedContent: true
//     });  
// </script>';

//En un array guardo lo que voy a devolver en el json_encode       
$respuesta = array("estructura"=>$estructura,"divpropiedades"=>$estructura,"divclasificacion"=>$estructura);

echo json_encode($respuesta);
?>  