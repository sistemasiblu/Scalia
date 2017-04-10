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

    $grupoestado = DB::table('grupoestado')
            ->select(DB::raw('idGrupoEstado, codigoGrupoEstado, nombreGrupoEstado'))
            ->where('Compania_idCompania','=',\Session::get("idCompania"))
            ->get();

    $row = array();

    foreach ($grupoestado as $key => $value) 
    {  
        $row[$key][] = '<a href="grupoestado/'.$value->idGrupoEstado.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="grupoestado/'.$value->idGrupoEstado.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idGrupoEstado;
        $row[$key][] = $value->codigoGrupoEstado;
        $row[$key][] = $value->nombreGrupoEstado; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>