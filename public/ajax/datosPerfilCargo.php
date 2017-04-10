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

    $perfilcargo = DB::table('perfilcargo')
            ->select(DB::raw('idPerfilCargo, tipoPerfilCargo, nombrePerfilCargo'))
           
            //Se pone un where para que consulte solo los registros de la compañia que esta logueada
            ->where('perfilcargo.Compania_idCompania',"=", \Session::get("idCompania"))
            ->get();
        $row = array();

    foreach ($perfilcargo as $key => $value) 
    {  
        $row[$key][] = '<a href="perfilcargo/'.$value->idPerfilCargo.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil " style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="perfilcargo/'.$value->idPerfilCargo.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>&nbsp;';
        $row[$key][] = $value->idPerfilCargo;
        $row[$key][] = $value->tipoPerfilCargo;
        $row[$key][] = $value->nombrePerfilCargo;
        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>