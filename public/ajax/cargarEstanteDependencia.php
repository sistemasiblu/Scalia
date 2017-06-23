<?php

$idDependencia = $_POST['idDependencia'];
$numeroEstante = $_POST['numeroEstante'];
$tipoInventario = $_POST['tipoInventario'];

$localizacion = DB::Select('
  SELECT 
    idDependencia, nombreDependencia, codigoDependencia, capacidadDependenciaLocalizacion, posicionUbicacionDocumento, estadoUbicacionDocumento, idUbicacionDocumento, dl.*, reemplazoUbicacionDocumento
FROM
    dependencialocalizacion dl
        LEFT JOIN
    dependencia d ON dl.Dependencia_idDependencia = d.idDependencia
      LEFT JOIN
    ubicaciondocumento ud ON dl.idDependenciaLocalizacion = ud.DependenciaLocalizacion_idDependenciaLocalizacion
WHERE idDependencia = '.$idDependencia.'
AND numeroEstanteDependenciaLocalizacion = '.$numeroEstante.'
AND (reemplazoUbicacionDocumento IS NULL or reemplazoUbicacionDocumento = 0)
ORDER BY nombreDependencia , numeroEstanteDependenciaLocalizacion , numeroNivelDependenciaLocalizacion DESC , numeroSeccionDependenciaLocalizacion, posicionUbicacionDocumento 
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

  $boton .= '<a onclick="llenarCampoEstante('.$posEstante['numeroEstanteDependenciaLocalizacion'].'); cargarEstanteDependencia('.$posEstante['Dependencia_idDependencia'].','.$posEstante['numeroEstanteDependenciaLocalizacion'].',\''.$tipoInventario.'\')" class="btn btn-default"><span>'.$posEstante['numeroEstanteDependenciaLocalizacion'].'</span></a>';
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
                              <td style='width:10%;background-color: #F2F2F2;vertical-align: inherit;'>
                                <center><b>Estante ".strtoupper($clocalizacion[$i]["numeroEstanteDependenciaLocalizacion"])."</b></center>
                              </td>
                            </tr>";

            while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia'] and $estante == $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion']) 
            {

              $nivel = $clocalizacion[$i]['numeroNivelDependenciaLocalizacion'];

              $estructura .= "<tr>
                                <td style='width:10%;vertical-align: inherit;background-color: #F2F2F2;'>
                                    <center><b>Nivel ".$clocalizacion[$i]["numeroNivelDependenciaLocalizacion"]."</b></center>
                                </td>";

                while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia'] and $estante == $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'] and $nivel == $clocalizacion[$i]['numeroNivelDependenciaLocalizacion']) 
                {

                    $seccion = $clocalizacion[$i]['numeroSeccionDependenciaLocalizacion'];
                    $registros = 1;

                      $estructura .= "
                        <td style='width:10%; height:100px; padding:0; border:1px solid;'>";

                        if ($clocalizacion[$i]['estadoDependenciaLocalizacion'] == 'Inactivo') 
                        {
                          $estructura .= "
                            <div style='background-color:#C0C0C0; display:inline-block; height:100%; width:100%; position:relative;'>
                              <center>INACTIVO</center>";
                        }
                        else if($clocalizacion[$i]['capacidadDependenciaLocalizacion'] == 'Disponible')
                        {
                          $localizacion = '"'.$clocalizacion[$i]['descripcionDependenciaLocalizacion'].'"';
                          
                          $estructura .= "
                            <div title='Ubicaciones disponibles' style='background-color:#A9F5A9; display:inline-block; cursor:pointer; height:100%; width:100%' onclick='abrirUbicacion(".$clocalizacion[$i]['idDependenciaLocalizacion'].', 0, event, "inicial", '.$localizacion.");'>
                                <a onclick='cerrarCaja(".$clocalizacion[$i]['idDependenciaLocalizacion'].',event'.")'><img src='http://".$_SERVER['HTTP_HOST']."/imagenes/cambiarestado.png' style='width:5%; float:right; cursor:help' title='Abrir o Cerrar Caja'></a>";
                        }
                        else if($clocalizacion[$i]['capacidadDependenciaLocalizacion'] == 'NoDisponible')
                        {
                          $localizacion = '"'.$clocalizacion[$i]['descripcionDependenciaLocalizacion'].'"';

                          $estructura .= "
                            <div title='Caja llena' style='background-color:white; display:inline-block; height:100%; width:100%'>
                              <a onclick='cerrarCaja(".$clocalizacion[$i]['idDependenciaLocalizacion'].',event'.")'><img src='http://".$_SERVER['HTTP_HOST']."/imagenes/cambiarestado.png' style='width:5%; float:right; cursor:help' title='Abrir o Cerrar Caja'></a>";
                        }

                          $ubicacion = DB::Select('
                            SELECT 
                              count(idUbicacionDocumento) + 1 as registrosUbicacionDocumento
                            FROM 
                              ubicaciondocumento 
                            WHERE 
                              DependenciaLocalizacion_idDependenciaLocalizacion = '.$clocalizacion[$i]['idDependenciaLocalizacion'].'
                            GROUP BY DependenciaLocalizacion_idDependenciaLocalizacion');

                          if (count($ubicacion) > 0) 
                          {
                            $registros = get_object_vars($ubicacion[0])['registrosUbicacionDocumento'];
                          }

                          if ($registros > 4)
                            $ancho = (100/$registros)-2;
                          else
                            $ancho = 20;

                    while ($i < $total and $dependencia == $clocalizacion[$i]['idDependencia'] and $estante == $clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'] and $nivel == $clocalizacion[$i]['numeroNivelDependenciaLocalizacion'] and $seccion == $clocalizacion[$i]['numeroSeccionDependenciaLocalizacion']) 
                    {     
                        $color = ($clocalizacion[$i]['estadoUbicacionDocumento'] == 'Activa') ? '#F78181' : '#F3E2A9';


                        $color = '';
                        $onclick =  '';
                        $title = '';

                        if ($clocalizacion[$i]['estadoUbicacionDocumento'] == 'Activa') 
                        {
                            $color = '#F5A9A9';   
                            $title = "title='Carpeta ".$clocalizacion[$i]['posicionUbicacionDocumento']." ocupada'";

                            $pl = '"'.$clocalizacion[$i]['codigoDependencia'].' '.$clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'].' '.$clocalizacion[$i]['numeroNivelDependenciaLocalizacion']. ' '.$clocalizacion[$i]['numeroSeccionDependenciaLocalizacion'].' '. $clocalizacion[$i]['posicionUbicacionDocumento'].'"';
                            $onclick = ($tipoInventario == 'manual' ? "onclick='abrirUbicacion(".$clocalizacion[$i]['idDependenciaLocalizacion'].','.$clocalizacion[$i]['idUbicacionDocumento'].',event, "Activa",'.$localizacion.");'" : "onclick='asignarPLRadicado(".$pl.", event);'");
                        }

                        else if ($clocalizacion[$i]['estadoUbicacionDocumento'] == 'Destruida') 
                        {
                            $color = '#F2F5A9';   
                            $title = "title='Carpeta ".$clocalizacion[$i]['posicionUbicacionDocumento']." destruída'";

                            $localizacion = '"'.$clocalizacion[$i]['codigoDependencia'].' '.$clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'].' '.$clocalizacion[$i]['numeroNivelDependenciaLocalizacion']. ' '.$clocalizacion[$i]['numeroSeccionDependenciaLocalizacion'].'"';
                            $onclick = "onclick='abrirUbicacion(".$clocalizacion[$i]['idDependenciaLocalizacion'].','.$clocalizacion[$i]['idUbicacionDocumento'].',event, "Destruida",'.$localizacion.");'";
                        }

                        else if ($clocalizacion[$i]['estadoUbicacionDocumento'] == 'Prestada') 
                        {
                          $color = '#A9BCF5';   
                            $title = "title='Carpeta ".$clocalizacion[$i]['posicionUbicacionDocumento']." prestada'";

                            $localizacion = '"'.$clocalizacion[$i]['codigoDependencia'].' '.$clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'].' '.$clocalizacion[$i]['numeroNivelDependenciaLocalizacion']. ' '.$clocalizacion[$i]['numeroSeccionDependenciaLocalizacion'].'"';
                            $onclick = "onclick='abrirUbicacion(".$clocalizacion[$i]['idDependenciaLocalizacion'].','.$clocalizacion[$i]['idUbicacionDocumento'].',event, "Prestada",'.$localizacion.");'";
                        }

                        else if ($clocalizacion[$i]['estadoUbicacionDocumento'] == 'Extraviada') 
                        {
                          $color = '#E6E6E6';   
                            $title = "title='Carpeta ".$clocalizacion[$i]['posicionUbicacionDocumento']." extraviada'";

                            $localizacion = '"'.$clocalizacion[$i]['codigoDependencia'].' '.$clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'].' '.$clocalizacion[$i]['numeroNivelDependenciaLocalizacion']. ' '.$clocalizacion[$i]['numeroSeccionDependenciaLocalizacion'].'"';
                            $onclick = "onclick='abrirUbicacion(".$clocalizacion[$i]['idDependenciaLocalizacion'].','.$clocalizacion[$i]['idUbicacionDocumento'].',event, "Extraviada",'.$localizacion.");'";
                        }

                        else if ($clocalizacion[$i]['estadoUbicacionDocumento'] == 'Deteriorada') 
                        {
                          $color = '#A9F5F2';   
                            $title = "title='Carpeta ".$clocalizacion[$i]['posicionUbicacionDocumento']." deteriorada'";

                            $localizacion = '"'.$clocalizacion[$i]['codigoDependencia'].' '.$clocalizacion[$i]['numeroEstanteDependenciaLocalizacion'].' '.$clocalizacion[$i]['numeroNivelDependenciaLocalizacion']. ' '.$clocalizacion[$i]['numeroSeccionDependenciaLocalizacion'].'"';
                            $onclick = "onclick='abrirUbicacion(".$clocalizacion[$i]['idDependenciaLocalizacion'].','.$clocalizacion[$i]['idUbicacionDocumento'].',event, "Deteriorada",'.$localizacion.");'";
                        }



                        if ($clocalizacion[$i]['posicionUbicacionDocumento'] != '') 
                        {
                            $estructura .="
                            <div style='background-color:".$color."; display:inline-block; cursor:pointer; height:100%; width:".$ancho."%' ".$title." ".$onclick.">
                                <a onclick='abrirUbicacion(".$clocalizacion[$i]['idDependenciaLocalizacion'].','.$clocalizacion[$i]['idUbicacionDocumento'].',event, "Activa",'.$localizacion.");'>".$clocalizacion[$i]['posicionUbicacionDocumento']."</a>
                            </div>";
                        }


                        $i++;
                    }

                    
                    $estructura .= "  </div>
                                    </td>";

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