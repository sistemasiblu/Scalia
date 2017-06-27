<?php

$prmantenimiento = \App\ProgramacionMantenimiento::All();
$row = array();

foreach ($prmantenimiento as $key => $value) 
{  
   /*$valores = get_object_vars($value);*/
    $valores = $value;
    $row[$key][] = '<a href="programacionmantenimiento/'.$valores['idProgramacionMantenimiento'].'/edit?accion=editar">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="programacionmantenimiento/'.$valores['idProgramacionMantenimiento'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idProgramacionMantenimiento'];
    $row[$key][] = $valores['ProtocoloMantenimiento_idProtocoloMantenimiento'];
    $row[$key][] = $valores['TipoActivo_idTipoActivo']; 
    $row[$key][] = $valores['TipoAccion_idTipoAccion'];  
    $row[$key][] = $valores['Dependencia_idDependencia'];  
    $row[$key][] = $valores['fechaInicialProgramacionMantenimiento'];  
    $row[$key][] = $valores['fechaMaximaProgramacionMantenimiento'];  
    $row[$key][] = $valores['Compania_idCompania'];  

    
}


    $output['aaData'] = $row;
    echo json_encode($output);
?>