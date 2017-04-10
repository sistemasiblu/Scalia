<?php

// INSERTO LOS METADATOS
//Se recibe por post el id del documento para saber que documento se debe radicar
$idDocumento = $_POST['Documento_idDocumento'];
//Se realiza una consulta a la tabla documentopropiedad para traer los campos y con ellos realizar el formulario de metadatos en el radicado
$metadatos = DB::table('documentopropiedad')
->leftjoin('documento','documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
->select(DB::raw('documentopropiedad.*,documento.tablaDocumento, metadato.*'))
->where('Documento_idDocumento', "=", $idDocumento)
->get();

$sistemainformacion = DB::Select('SELECT idSistemaInformacion from sistemainformacion left join documento on documento.SistemaInformacion_idSistemaInformacion = sistemainformacion.idSistemaInformacion
where idDocumento = '.$idDocumento);

if ($sistemainformacion == null || $sistemainformacion == '') 
  $sistemainformacion = 0;
else
  $sistemainformacion = get_object_vars($sistemainformacion[0]);


$divpropiedades = ''; //Se inicializa este campo vacío para concatenar luego el formulario de metadatos en el radicado
$campos = ''; //Se inicializa vacío para luego concatenar los campos de documento propiedad en la consulta para el llenado de los metadatos dinamicamente
$condicion = ''; //Se inicializa vacío para concatenar el where del campo índice que viene desde el formulario de documentopropiedad

// Defino la fecha actual
$fechahoy = Carbon\Carbon::now();

$divpropiedades .='<div class="form-group col-md-6 form-inline" id="test">
  <label id="fechaFormulario" name="fechaFormulario" class="col-md-12 control-label"> Fecha de creación </label>
  <div class="col-sm-10">
    <div class="input-group">
      <span class="input-group-addon">
        <i class="fa fa-calendar "></i>
      </span>
        <input type"date" id="fechaFormulario" name="fechaFormulario" value="'.$fechahoy->toDateTimeString().'" class= "form-control" readonly placeholder = "Fecha de creación"/>
    </div>
  </div>
</div>

<br> <br> <br> <br> <br>';

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

        $divpropiedades .= '<div id="metadatos">
                        <div class="form-group col-md-6 form-inline" id="test">
                            <label id="'.$nombremetadato["idDocumentoPropiedad"].'_lbl" class= "col-md-4 control-label">'.str_replace('_', ' ',$nombremetadato["tituloMetadato"]).'</label>
                            <div class="col-sm-8">
                              <div class="input-group">
                                <span '.$style.' class="input-group-addon">
                                  <i class="fa fa-pencil-square-o "></i>
                                </span>';  

      
        //Se abre un switch para construir el formulario de metadatos dependiendo de que campos (tipoMetadato) viene desde el maestro de documentos                                
        switch ($nombremetadato["tipoMetadato"]) { 
          case "Texto":
                    $divpropiedades .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.'  name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="text" placeholder="Digite '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'">';
              break;
          case "Fecha":
                    $divpropiedades .='<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.'  name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="date" placeholder="Digite ">
                    <script type="text/javascript">
                      $("#'.$nombremetadato["idDocumentoPropiedad"].'").datetimepicker(({
                       format: "YYYY-MM-DD"
                      }));
                    </script>';
              break;
          case "Numero":
                    $divpropiedades .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'"  '.$clase.' name="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' class="form-control '.$eventoblur.'" type="number" placeholder="Digite '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'">';
              break;
          case "Hora":
                    $divpropiedades .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="date" placeholder="Digite ">
                    <script type="text/javascript">
                      $("#'.$nombremetadato["idDocumentoPropiedad"].'").datetimepicker(({
                       format: "HH:mm:ss"
                      }));
                    </script> ';
              break;
          case "Lista":
          $lista = DB::table('sublista')
                  ->select (DB::raw('idSubLista, nombreSubLista, Lista_idLista'))
                  ->where('Lista_idLista', "=", $nombremetadato["Lista_idLista"])
                  ->get();

          $idLista = $nombremetadato['Lista_idLista'];

                  // $divpropiedades .='<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.'  name="nombre'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control '.$clase.'" type="text" placeholder="Seleccione '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'" value="" onchange="abrirModal('.$idLista.',this);">';

                  $divpropiedades .='<input id="cod'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="hidden" value="">';

                    $divpropiedades .= '<select id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" 
                                            class="chosen-select form-control" >
                                          <option value="0">Seleccione</option>';
                    for($c = 0; $c < count($lista); $c++) 
                    {
                      $sublista = get_object_vars($lista[$c]);
                      $divpropiedades .= '<option value="'.$sublista["idSubLista"].'">'.$sublista["nombreSubLista"].'</option>';
                    }

                    $divpropiedades .= '</select>';
              break;
          case "Editor":
                    $divpropiedades .= '<textarea id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control" type="text" placeholder="Digite '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'"></textarea>';
              break;
           case "EleccionUnica":
                $defaultR = explode(",", $nombremetadato['valorBaseMetadato']);

                for ($i=0; $i < count($defaultR); $i++) 
                { 
                  $divpropiedades .= '<label class="col-md-12"><input id="'.$nombremetadato["idDocumentoPropiedad"].'" type="radio" name="'.$nombremetadato["idDocumentoPropiedad"].'" value="'.$defaultR[$i].'">&nbsp; &nbsp; &nbsp; &nbsp;'.$defaultR[$i].'</label>';
                }
                       
              break;
           case "EleccionMultiple":
                $default = explode(",",$nombremetadato['valorBaseMetadato']);

                for ($i=0; $i <count($default); $i++) 
                {
                  $divpropiedades .= '<label class="col-md-12"><input id="'.$nombremetadato["idDocumentoPropiedad"].'" type="checkbox" onclick="validarCheckbox(this, '.$nombremetadato["idDocumentoPropiedad"].$i.')">&nbsp; &nbsp; &nbsp; &nbsp;'.$default[$i].'</label>';
                  $divpropiedades .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].$i.'" name="'.$nombremetadato["idDocumentoPropiedad"].'[]" value = 0  type="hidden">';
                  //Se crea un campo hidden el cual contiene el valor del checkbox (1 o 0), este es el que se guarda en la base de datos y se inicializa en 0 porque al inicio del formulario el check no esta seleccionado
                }
              break;
          default:
                    $divpropiedades .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" type="text" placeholder="Seleccione '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'">';
      } //Se cierra el switch
      
      $divpropiedades .= '</div>
          </div>
        </div>
      </div>'; //Se cierra el formulario de metadatos
      $campos .=$nombremetadato['campoDocumentoPropiedad'].', '; //Se le envian a la variable campos los registros de campoDocumentPropiedad y se separan por comas (,)      
      if ($nombremetadato['indiceDocumentoPropiedad'] == 1) //Se pregunta si en el formulario hay un campo indice
      {
          $condicion .= $nombremetadato['campoDocumentoPropiedad'].' = ? and ';          
           //Se guarda en la variable condicion el campo indice concatenado con = ? and por si hay mas de un campo indice
      } 
    } //Se cierra el for

    $campos = substr($campos, 0, strlen($campos)-2); //a los registros se le quita el ultimo caracter en este caso la ultima coma (,)
    $condicion = substr($condicion, 0, strlen($condicion)-4); //  se le hace un substr al campo condicion eliminando la palabra and y un espacio que hay despues de esta

    //Se guarda en un campo hidden la consulta
    $divpropiedades .= '<input type="hidden" id="consulta" name="consulta" value="SELECT '.$campos.' from '.$nombremetadato['tablaDocumento'].' where '.$condicion.'">';
    $divpropiedades .= '<input type="hidden" id="idDocumentoFormulario" name="idDocumentoFormulario" value="'.$idDocumento.'">';

    //Consulto el inicio de la versión
    $numeroVersionFormulario = DB::Select('SELECT inicioDocumentoVersion from documentoversion
      where Documento_idDocumento = '.$idDocumento);

    $numeroVF = '';
    
    for ($i=0; $i <count($numeroVersionFormulario) ; $i++) 
    { 
      $VersionFormulario = get_object_vars($numeroVersionFormulario[$i]);

      $numeroVF .= $VersionFormulario['inicioDocumentoVersion'].'.';
    }

    $numeroVF = substr($numeroVF, 0, strlen($numeroVF)-1);

    $divpropiedades .= '<input type="hidden" id="numeroVersionInicial" name="numeroVersionInicial" value="'.$numeroVF.'" /input>';
    $divpropiedades .= '<input type="hidden" id="tipoVersionFormulario" name="tipoVersionFormulario" value="0" /input>';

    // $divpropiedades .='<script type="text/javascript">
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
    
    echo json_encode($divpropiedades);
?>  