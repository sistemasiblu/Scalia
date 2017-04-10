<?php
    $consulta = DB::Select(
        'SELECT idCategoriaInforme, nombreCategoriaInforme 
        FROM categoriainforme ');


    $row = array();

    foreach ($consulta as $key => $value) 
    {  
        
        foreach ($value as $key2 => $campo) 
        {
            $row[$key][] = $campo;
        }                        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>