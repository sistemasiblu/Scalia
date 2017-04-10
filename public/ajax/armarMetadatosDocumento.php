<?php

// INSERTO LOS METADATOS
//Se recibe por post el id del documento para saber que documento se debe radicar
$idDocumento = $_POST['Documento_idDocumentoP'];
//Se realiza una consulta a la tabla documentopropiedad para traer los campos y con ellos realizar el formulario de metadatos en el radicado
$metadatos = DB::table('documentopropiedad')
->leftjoin('documento','documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
->select(DB::raw('documentopropiedad.*,metadato.*,documento.tablaDocumento'))
->where('Documento_idDocumento', "=", $idDocumento)
->get();

// Realizo una consulta a la tabla documento para traer el campo del filtro en la conexion del documento
$filtroDocumento = DB::Select('SELECT filtroDocumento from documento where idDocumento = '.$idDocumento);

// Convierto de array a string
$filtroD = get_object_vars($filtroDocumento[0]);


$divpropiedades = ''; //Se inicializa este campo vacío para concatenar luego el formulario de metadatos en el radicado
$campos = ''; //Se inicializa vacío para luego concatenar los campos de documento propiedad en la consulta para el llenado de los metadatos dinamicamente

$condicion = ($filtroD['filtroDocumento'] == '' ? '' : $filtroD['filtroDocumento'].' and '); //Se inicializa la variable con el filtro de la conexión del documento siempre y cuando esté lleno, sino se encuentra lleno se inicializa vacío

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
        $eventoblur = 'onchange="llenarMetadatos(this.value);"';
      }

      $divpropiedades .= '<div id="metadatos">
                        <div class="form-group" id="test">
                            <label id="'.$nombremetadato["idDocumentoPropiedad"].'_lbl" class= "col-sm-2 control-label">'.$nombremetadato["tituloMetadato"].'</label>
                            <div class="col-sm-10">
                              <div class="input-group">
                                <span '.$style.' class="input-group-addon">
                                  <i class="fa fa-pencil-square-o "></i>
                                </span>';
        //Se abre un switch para construir el formulario de metadatos dependiendo de que campos (tipoMetadato) viene desde el maestro de documentos                                
        switch ($nombremetadato["tipoMetadato"]) { 
          case "Texto":
                    $divpropiedades .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.' name="'.$nombremetadato["idDocumentoPropiedad"].'" value="'.$nombremetadato["valorBaseMetadato"].'" class="form-control" type="text" placeholder="Digite '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'">';
              break;
          case "Fecha":
                    $divpropiedades .='<input id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" value="'.$nombremetadato["valorBaseMetadato"].'" class="form-control" type="date" placeholder="Digite ">
                    <script type="text/javascript">
                      $("#'.$nombremetadato["idDocumentoPropiedad"].'").datetimepicker(({
                       format: "YYYY-MM-DD"
                      }));
                    </script>';
              break;
          case "Numero":
                    $divpropiedades .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" '.$eventoblur.'  name="'.$nombremetadato["idDocumentoPropiedad"].'" value="'.$nombremetadato["valorBaseMetadato"].'" class="form-control" type="number" placeholder="Digite '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'">';
              break;
          case "Hora":
                    $divpropiedades .= '<input id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" value="'.$nombremetadato["valorBaseMetadato"].'" class="form-control" type="date" placeholder="Digite ">
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

                    $divpropiedades .= '<select id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" 
                                            class="form-control" >
                                          <option value="0">Seleccione</option>';
                    for($c = 0; $c < count($lista); $c++) 
                    {
                      $sublista = get_object_vars($lista[$c]);
                      $divpropiedades .= '<option value="'.$sublista["idSubLista"].'">'.$sublista["nombreSubLista"].'</option>';
                    }

                    $divpropiedades .= '</select>';
              break;
          case "Editor":
                    $divpropiedades .= '<textarea id="'.$nombremetadato["idDocumentoPropiedad"].'" name="'.$nombremetadato["idDocumentoPropiedad"].'" class="form-control " type="text" placeholder="Digite '.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'"></textarea>';
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
                    $divpropiedades .= '<input id="'.str_replace('_', ' ', $nombremetadato["tituloMetadato"]).'" name="'.$nombremetadato["idDocumentoPropiedad"].'" type="text" placeholder="Seleccione '.$nombremetadato["tituloMetadato"].'">';
      } //Se cierra el switch
      
      $divpropiedades .= '</div>
          </div>
        </div>
      </div>'; //Se cierra el formulario de metadatos
      $campos .=$nombremetadato['campoDocumentoPropiedad'].', '; //Se le envian a la variable campos los registros de campoDocumentPropiedad y se separan por comas (,)      
      if ($nombremetadato['indiceDocumentoPropiedad'] == 1) //Se pregunta si en el formulario hay un campo indice
      {
          $condicion .= $nombremetadato['campoDocumentoPropiedad'].' =  and';          
           //Se guarda en la variable condicion el campo indice concatenado con = ? and por si hay mas de un campo indice
      } 

    } //Se cierra el for

    $campos = substr($campos, 0, strlen($campos)-2); //a los registros se le quita el ultimo caracter en este caso la ultima coma (,)
    $condicion = substr($condicion, 0, strlen($condicion)-3); //  se le hace un substr al campo condicion eliminando la palabra and y un espacio que hay despues de esta

    //Se guarda en un campo hidden la consulta
    $divpropiedades .= '<input type="hidden" id="campos" name="campos" value="'.$campos.'">';
    $divpropiedades .= '<input type="hidden" id="tablaDocumento" name="tablaDocumento" value="'.$nombremetadato['tablaDocumento'].'">';
    $divpropiedades .= '<input type="hidden" id="condicion" name="condicion" value="'.$condicion.'">';

    $divpropiedades .= '<input type="hidden" id="idDocumento" name="idDocumento" value="'.$idDocumento.'">';
    
    echo json_encode($divpropiedades);
?>  
