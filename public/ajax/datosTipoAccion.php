<?php

$tipoaccion = DB::select("select * from tipoaccion");
$row = array();

foreach ($tipoaccion as $key => $value) 
{  
    $valores = get_object_vars($value);
    //$valores = $value;
    $row[$key][] = '<a href="tipoaccion/'.$valores['idTipoAccion'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="tipoaccion/'.$valores['idTipoAccion'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idTipoAccion'];
    $row[$key][] = $valores['codigoTipoAccion'];
    $row[$key][] = $valores['nombreTipoAccion'];   
}

    $output['aaData'] = $row;
    echo json_encode($output);
?>