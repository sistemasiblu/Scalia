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

    $documentocrm = DB::table('documentocrm')
            ->select(DB::raw('idDocumentoCRM, codigoDocumentoCRM, nombreDocumentoCRM, numeracionDocumentoCRM, longitudDocumentoCRM, desdeDocumentoCRM, hastaDocumentoCRM'))
            ->where('Compania_idCompania','=',\Session::get("idCompania"))
            ->get();

    $row = array();

    foreach ($documentocrm as $key => $value) 
    {  
        $row[$key][] = '<a href="documentocrm/'.$value->idDocumentoCRM.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="documentocrm/'.$value->idDocumentoCRM.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idDocumentoCRM;
        $row[$key][] = $value->codigoDocumentoCRM;
        $row[$key][] = $value->nombreDocumentoCRM;
        $row[$key][] = $value->numeracionDocumentoCRM;
        $row[$key][] = $value->longitudDocumentoCRM;   
        $row[$key][] = $value->desdeDocumentoCRM;
        $row[$key][] = $value->hastaDocumentoCRM;
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>