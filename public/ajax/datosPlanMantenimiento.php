<?php

$plmantenimiento = \App\PlanMantenimiento::All();
$row = array();

foreach ($plmantenimiento as $key => $value) 
{  
   /*$valores = get_object_vars($value);*/
    $valores = $value;
    $row[$key][] = '<a href="planmantenimiento/'.$valores['idPlanMantenimiento'].'/edit?accion=editar">'.
                        '<span class="glyphicon glyphicon-pencil" ></span>'.
                    '</a>&nbsp;'.
                    '<a href="planmantenimiento/'.$valores['idPlanMantenimiento'].'/edit?accion=eliminar">'.
                        '<span class="glyphicon glyphicon-trash" ></span>'.
                    '</a>';
    $row[$key][] = $valores['idPlanMantenimiento'];
    $row[$key][] = $valores['Activo_idActivo'];
    $row[$key][] = $valores['Activo_idParte']; 
    $row[$key][] = $valores['actividadPlanMantenimiento'];  
}

    $output['aaData'] = $row;
    echo json_encode($output);
?>