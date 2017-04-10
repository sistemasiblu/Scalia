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

    $documento = DB::table('documento')
            ->leftJoin('sistemainformacion', 'SistemaInformacion_idSistemaInformacion', '=', 'idSistemaInformacion')
            ->select(DB::raw('idDocumento, codigoDocumento, nombreDocumento, directorioDocumento, nombreSistemaInformacion'))
            ->get();

   // print_r($documento);
 // exit;
    $row = array();

    foreach ($documento as $key => $value) 
    {  
        $row[$key][] = '<a href="documento/'.$value->idDocumento.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a onclick="duplicarDocumento('.$value->idDocumento.')">'.
                            '<span class="glyphicon glyphicon-duplicate" style="cursor:pointer; display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="documento/'.$value->idDocumento.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idDocumento;
        $row[$key][] = $value->codigoDocumento;
        $row[$key][] = $value->nombreDocumento;   
        $row[$key][] = $value->directorioDocumento;
        $row[$key][] = $value->nombreSistemaInformacion;
    }
     // print_r($row);
    // exit;
    $output['aaData'] = $row;
    echo json_encode($output);
?>