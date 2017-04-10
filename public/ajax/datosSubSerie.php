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

    $subserie = DB::table('subserie')
            ->leftJoin('serie', 'Serie_idSerie', '=', 'idSerie')
            ->where('subserie.Compania_idCompania', "=", \Session::get("idCompania"))
            ->select(DB::raw('idSubSerie, codigoSubSerie, nombreSubSerie, nombreSerie'))
            ->get();

   // print_r($subserie);
 // exit;
    $row = array();

    foreach ($subserie as $key => $value) 
    {  
        $row[$key][] = '<a href="subserie/'.$value->idSubSerie.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="subserie/'.$value->idSubSerie.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idSubSerie;
        $row[$key][] = $value->codigoSubSerie;
        $row[$key][] = $value->nombreSubSerie;   
        $row[$key][] = $value->nombreSerie;
    }
     // print_r($row);
    // exit;
    $output['aaData'] = $row;
    echo json_encode($output);
?>