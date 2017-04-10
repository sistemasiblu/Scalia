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
    
    $documentoimportacion = DB::table('documentoimportacion')
            ->leftJoin('sistemainformacion', 'SistemaInformacion_idSistemaInformacion', '=', 'idSistemaInformacion')
            ->where('Compania_idCompania', "=", \Session::get("idCompania"))
            ->select(DB::raw('idDocumentoImportacion, codigoDocumentoImportacion, nombreDocumentoImportacion, tipoDocumentoImportacion, nombreSistemaInformacion'))
            ->get();

   // print_r($documentoimportacion);
 // exit;
    $row = array();

    foreach ($documentoimportacion as $key => $value) 
    {  
        $row[$key][] = '<a href="documentoimportacion/'.$value->idDocumentoImportacion.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        // '<a onclick="duplicarDocumento('.$value->idDocumentoImportacion.')">'.
                        //     '<span class="glyphicon glyphicon-duplicate" style="cursor:pointer;"></span>'.
                        // '</a>&nbsp;'.
                        '<a href="documentoimportacion/'.$value->idDocumentoImportacion.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idDocumentoImportacion;
        $row[$key][] = $value->codigoDocumentoImportacion;
        $row[$key][] = $value->nombreDocumentoImportacion;   
        $row[$key][] = $value->nombreSistemaInformacion;
        $row[$key][] = $value->tipoDocumentoImportacion;
    }
     // print_r($row);
    // exit;
    $output['aaData'] = $row;
    echo json_encode($output);
?>