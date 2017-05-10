<?php

$localizacion = DB::select("select idLocalizacion,codigoLocalizacion,nombreLocalizacion from localizacion");
$row = array();

foreach ($localizacion as $key => $value) 
{  
    $valores = get_object_vars($value);
    //$valores = $value;
    $row[$key][] = '<a href="localizacion/'.$valores['idLocalizacion'].'/edit">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="localizacion/'.$valores['idLocalizacion'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idLocalizacion'];
    $row[$key][] = $valores['codigoLocalizacion'];
    $row[$key][] = $valores['nombreLocalizacion'];   
}

    $output['aaData'] = $row;
    echo json_encode($output);
?>