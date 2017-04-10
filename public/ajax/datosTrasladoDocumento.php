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

    $consulta = DB::Select('SELECT idTrasladoDocumento, numeroTrasladoDocumento, descripcionTrasladoDocumento, fechaElaboracionTrasladoDocumento, name from trasladodocumento td left join users u on u.id = td.Users_id');

    $row = array();

    foreach ($consulta as $key => $value) 
    {  
        $row[$key][] = '<a href="trasladodocumento/'.$value->idTrasladoDocumento.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="trasladodocumento/'.$value->idTrasladoDocumento.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';

        $row[$key][] = $value->idTrasladoDocumento;
        $row[$key][] = $value->numeroTrasladoDocumento;
        $row[$key][] = $value->descripcionTrasladoDocumento; 
        $row[$key][] = $value->fechaElaboracionTrasladoDocumento;    
        $row[$key][] = $value->name;
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>