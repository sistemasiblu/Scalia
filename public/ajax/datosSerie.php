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

    $serie = \App\Serie::where('Compania_idCompania', "=", \Session::get("idCompania"))->get();
    // print_r($serie);
    // exit;
    $row = array();

    foreach ($serie as $key => $value) 
    {  
        $row[$key][] = '<a href="serie/'.$value['idSerie'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="serie/'.$value['idSerie'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idSerie'];
        $row[$key][] = $value['nombreSerie'];
        $row[$key][] = $value['codigoSerie'];
        $row[$key][] = $value['directorioSerie'];   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>