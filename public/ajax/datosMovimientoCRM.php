<?php

    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];
    $consultar = $_GET['consultar'];
    $aprobar = $_GET['aprobar'];

    $visibleM = '';
    $visibleE = '';
    if ($modificar == 1) 
        $visibleM = 'inline-block;';
    else
        $visibleM = 'none;';

    if ($eliminar == 1) 
        $visibleE = 'inline-block;';
    else
        $visibleE = 'none;';

    if ($consultar == 1) 
        $visibleC = 'inline-block;';
    else
        $visibleC = 'none;';

    if ($aprobar == 1) 
        $visibleA = 'inline-block;';
    else
        $visibleA = 'none;';



    $id = isset($_GET["idDocumento"])
                ? $_GET["idDocumento"] 
                : 0;

    $TipoEstado = isset($_GET["TipoEstado"])
                ? $_GET["TipoEstado"] 
                : '';

    $campos = DB::select(
    'SELECT codigoDocumentoCRM, nombreDocumentoCRM, nombreCampoCRM,descripcionCampoCRM, tipoCampoCRM,
            mostrarGridDocumentoCRMCampo, relacionTablaCampoCRM, relacionNombreCampoCRM, relacionAliasCampoCRM
    FROM documentocrm
    left join documentocrmcampo
    on documentocrm.idDocumentoCRM = documentocrmcampo.DocumentoCRM_idDocumentoCRM
    left join campocrm
    on documentocrmcampo.CampoCRM_idCampoCRM = campocrm.idCampoCRM
    where   documentocrm.idDocumentoCRM = '.$id.' and
            relacionTablaCampoCRM != "" and 
            mostrarGridDocumentoCRMCampo = 1 and tipoCampoCRM="campo"');

$camposGrid = 'IF((fechaVencimientoMovimientoCRM != "0000-00-00 00:00:00" and tipoEstadoCRM NOT IN ("Exitoso","Fallido","Cancelado")), DATEDIFF(fechaVencimientoMovimientoCRM, CURDATE()), 3) as diasFaltantes, detallesMovimientoCRM, idMovimientoCRM, numeroMovimientoCRM, asuntoMovimientoCRM, IF((tipoEstadoCRM NOT IN ("Exitoso","Fallido","Cancelado")),DATEDIFF(CURDATE(), fechaSolicitudMovimientoCRM), diasRealesSolucionMovimientoCRM) as diasProceso';
$camposBase = 'diasFaltantes, idMovimientoCRM,numeroMovimientoCRM,asuntoMovimientoCRM, diasProceso';
for($i = 0; $i < count($campos); $i++)
{
    $datos = get_object_vars($campos[$i]); 
    
    $camposGrid .= ', '. $datos["relacionTablaCampoCRM"].'.'.$datos["relacionNombreCampoCRM"]  .
                     ($datos["relacionAliasCampoCRM"] == null 
                        ? ''
                        : ' As '. $datos["relacionAliasCampoCRM"]);

    $camposBase .= ','.($datos["relacionAliasCampoCRM"] == null 
                        ? $datos["relacionNombreCampoCRM"]
                        : $datos["relacionAliasCampoCRM"]);

}

    $movimientocrm = DB::select(
        'Select
          '.$camposGrid.'
        From
          movimientocrm 
          left join documentocrm
          On movimientocrm.DocumentoCRM_idDocumentoCRM = documentocrm.idDocumentoCRM
          Left Join '.\Session::get("baseDatosCompania").'.Tercero solicitante
            On movimientocrm.Tercero_idSolicitante = solicitante.idTercero 
          Left Join '.\Session::get("baseDatosCompania").'.Tercero supervisor
            On movimientocrm.Tercero_idSupervisor = supervisor.idTercero 
          Left Join '.\Session::get("baseDatosCompania").'.Tercero asesor
            On movimientocrm.Tercero_idAsesor = asesor.idTercero 
          Left Join categoriacrm
            On movimientocrm.CategoriaCRM_idCategoriaCRM = categoriacrm.idCategoriaCRM
          Left Join lineanegocio
            On movimientocrm.LineaNegocio_idLineaNegocio = lineanegocio.idLineaNegocio
          Left Join origencrm
            On movimientocrm.OrigenCRM_idOrigenCRM = origencrm.idOrigenCRM 
          Left Join estadocrm
            On movimientocrm.EstadoCRM_idEstadoCRM = estadocrm.idEstadoCRM
          Left Join eventocrm
            On movimientocrm.EventoCRM_idEventoCRM = eventocrm.idEventoCRM 
          Left Join acuerdoservicio
            On movimientocrm.AcuerdoServicio_idAcuerdoServicio =
            acuerdoservicio.idAcuerdoServicio
          LEFT JOIN
            clasificacioncrm ON movimientocrm.ClasificacionCRM_idClasificacionCRM = clasificacioncrm.idClasificacionCRM
          LEFT JOIN
            clasificacioncrmdetalle ON movimientocrm.ClasificacionCRMDetalle_idClasificacionCRMDetalle = clasificacioncrmdetalle.idClasificacionCRMDetalle
        Where  idDocumentoCRM = '.$id.  ' and 
                movimientocrm.Compania_idCompania = '.\Session::get('idCompania'). ' and 
                
                ((movimientocrm.Tercero_idSolicitante = '.\Session::get('idTercero'). ' or 
                 movimientocrm.Tercero_idSupervisor = '.\Session::get('idTercero'). ' or 
                 movimientocrm.Tercero_idAsesor = '.\Session::get('idTercero'). ') OR 
                 '.$aprobar.' = 1) '. 
                ($TipoEstado != '' ? ' and estadocrm.tipoEstadoCRM = "'.$TipoEstado.'"' : ''));


    $row = array();

    for($i = 0; $i < count($movimientocrm); $i++)
    {  
        $datoValor = get_object_vars($movimientocrm[$i]); 
        $row[$i][] = '<a href="movimientocrm/'.$datoValor["idMovimientoCRM"].'/edit?idDocumentoCRM='.$id.'&aprobador='.$aprobar.'">'.
                            '<span class="glyphicon glyphicon-pencil" style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="movimientocrm/'.$datoValor["idMovimientoCRM"].'/edit?idDocumentoCRM='.$id.'&aprobador='.$aprobar.'&accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="javascript:mostrarModalAsesor('.$datoValor["idMovimientoCRM"].');">'.
                            '<span class="glyphicon glyphicon-check" style = "display:'.$visibleA.'" ></span>'.
                        '</a>&nbsp;'.
                        '<a href="#" onclick="imprimirFormato('.$datoValor["idMovimientoCRM"].','.$id.');">'.
                            '<span class="glyphicon glyphicon-print" style = "display:'.$visibleC.'" ></span>'.
                        '</a>';

        $campos = explode(',', $camposBase);
        $estilo = '';
        for($j = 0; $j < count($campos); $j++)
        {
          
          if(trim($campos[$j]) == 'diasFaltantes')
          {
            
            if($datoValor[trim($campos[$j])] <= 2 )
                $estilo = 'style="color: red;"';
             else
                $estilo = '';

            // if($datoValor[trim($campos[$j])] <= 0)
              
          }
            
          else
          {
            if(trim($campos[$j]) != 'detallesMovimientoCRM')
            {

              if(trim($campos[$j]) == 'asuntoMovimientoCRM')
              $row[$i][] = '<a href="#" data-toggle="tooltip" data-html="true" data-placement="bottom"  title=\''.$datoValor['detallesMovimientoCRM'].'\'>'.$datoValor[trim($campos[$j])].'</a>';
              else
                $row[$i][] = '<span '.$estilo.'>'.$datoValor[trim($campos[$j])].'</span>';
            }
          }
          
        }

    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>