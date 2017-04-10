<?php
    $modificar = $_GET['modificar'];
    $eliminar = $_GET['eliminar'];

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

    $DB = \Session::get("baseDatosCompania");

    $documentoconciliacion = DB::select(
        "SELECT DC.idDocumentoConciliacion, D.codigoAlternoDocumento, D.nombreDocumento, G.nombreGrupoDocumento
        FROM documentoconciliacion DC 
        LEFT JOIN $DB.Documento D 
        ON DC.Documento_idDocumento = D.idDocumento
        LEFT JOIN $DB.GrupoDocumento G 
        ON D.GrupoDocumento_idGrupoDocumento = G.idGrupoDocumento
        WHERE Compania_idCompania = ".\Session::get("idCompania"));
    
    $row = array();

    foreach ($documentoconciliacion as $key => $value) 
    {  
        $row[$key][] = '<a href="documentoconciliacion/'.$value->idDocumentoConciliacion.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="documentoconciliacion/'.$value->idDocumentoConciliacion.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idDocumentoConciliacion;
        $row[$key][] = $value->codigoAlternoDocumento;
        $row[$key][] = $value->nombreDocumento;   
        $row[$key][] = $value->nombreGrupoDocumento;   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>