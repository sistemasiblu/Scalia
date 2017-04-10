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


    $cargo = \App\Cargo::where('Compania_idCompania','=', \Session::get('idCompania'))->get();
    $row = array();

    foreach ($cargo as $key => $value) 
    {  
        $row[$key][] = '<a href="cargo/'.$value['idCargo'].'/edit">'.
                            '<span class="glyphicon glyphicon-pencil"  style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="cargo/'.$value['idCargo'].'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value['idCargo'];
        $row[$key][] = $value['codigoCargo'];
        $row[$key][] = $value['nombreCargo'];   
        $row[$key][] = $value['salarioBaseCargo'];
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>