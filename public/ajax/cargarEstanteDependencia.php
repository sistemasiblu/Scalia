<?php

$idDependencia = $_POST['idDependencia'];
$numeroEstante = $_POST['numeroEstante'];

$localizacion = DB::Select('
  SELECT 
    idDependencia, nombreDependencia, dl.*
FROM
    dependencialocalizacion dl
        LEFT JOIN
    dependencia d ON dl.Dependencia_idDependencia = d.idDependencia
WHERE idDependencia = '.$idDependencia.'
AND numeroEstanteDependenciaLocalizacion = '.$numeroEstante.'
ORDER BY nombreDependencia , numeroEstanteDependenciaLocalizacion , numeroNivelDependenciaLocalizacion DESC , numeroSeccionDependenciaLocalizacion
');


$clocalizacion = array();
// por facilidad de manejo convierto el stdclass a tipo array con un cast (array)
for ($i = 0, $c = count($localizacion); $i < $c; ++$i) 
{
  $clocalizacion[$i] = (array) $localizacion[$i];
}

$estante = DB::Select('
  SELECT 
    numeroEstanteDependenciaLocalizacion, Dependencia_idDependencia
  FROM
    dependencialocalizacion
  WHERE Dependencia_idDependencia = '.$idDependencia.'
  GROUP BY numeroEstanteDependenciaLocalizacion');

$boton = '<b>Estantes: </b>';
for ($i=0; $i < count($estante); $i++) 
{ 
  $posEstante = get_object_vars($estante[$i]);

  $boton .= '<a onclick="cargarEstanteDependencia('.$posEstante['Dependencia_idDependencia'].','.$posEstante['numeroEstanteDependenciaLocalizacion'].')" class="btn btn-default"><span>'.$posEstante['numeroEstanteDependenciaLocalizacion'].'</span></a>';
}

    $i = 0;
    $estructura = '';
    $total = count($localizacion);

    while ($i < $total) 
    {
      $dependencia = $clocalizacion[$i]['idDependencia'];

        $estructura .= '<div id="'.$clocalizacion[$i]["idDependencia"].'">';

        while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia']) 
        {
          $estante = $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'];

          $estructura .= "<table class='table table-bordered' style='width:100%;font-size: 12px;padding: 3px 10px;'>
                            <tr>
                              <td style='background-color: #F2F2F2;vertical-align: inherit;'>
                                <center><b>Estante ".strtoupper($clocalizacion[$i]["numeroEstanteDependenciaLocalizacion"])."</b></center>
                              </td>
                            </tr>";

            while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia'] and $estante == $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion']) 
            {

              $nivel = $clocalizacion[$i]['numeroNivelDependenciaLocalizacion'];

              $estructura .= "<tr>
                                <td style='width:60px;vertical-align: inherit;background-color: #F2F2F2;'>
                                    <center><b>Nivel ".$clocalizacion[$i]["numeroNivelDependenciaLocalizacion"]."</b></center>
                                </td>";

                while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia'] and $estante == $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'] and $nivel == $clocalizacion[$i]['numeroNivelDependenciaLocalizacion']) 
                {

                  if ($clocalizacion[$i]['estadoDependenciaLocalizacion'] == 'Inactivo') 
                  {
                      $estructura .= "<td style='vertical-align: inherit; border:1px solid; background-color:#C0C0C0'>
                                      &nbsp;
                                      </td>";                    
                  }
                  else
                  {
                      $estructura .= "<td style='vertical-align: inherit; border:1px solid;'>
                                    <div id=".$clocalizacion[$i]['numeroSeccionDependenciaLocalizacion']." onclick='ConsultarInformacion(".$clocalizacion[$i]['idDependenciaLocalizacion'].");' style='cursor:pointer; heigth:100%;'>
                                        Seccion ".$clocalizacion[$i]['numeroSeccionDependenciaLocalizacion']."
                                    </div>
                                    <input type='hidden' value='".$clocalizacion[$i]['idDependenciaLocalizacion']."'>
                                  </td>";
                  }

                  $i++;
                }

              $estructura .= "</tr>";

            }

          $estructura .= "</table>";
        }

        $estructura .= '</div>';
    }

  $respuesta = array('estructura' => $estructura, 'boton' => $boton);

  echo json_encode($respuesta)

?>