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

    $lineanegocio = DB::table('lineanegocio')
            ->select(DB::raw('idLineaNegocio, codigoLineaNegocio, nombreLineaNegocio'))
            ->where('Compania_idCompania','=',\Session::get("idCompania"))
            ->get();

    $row = array();

    foreach ($lineanegocio as $key => $value) 
    {  
        $row[$key][] = '<a href="lineanegocio/'.$value->idLineaNegocio.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="lineanegocio/'.$value->idLineaNegocio.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idLineaNegocio;
        $row[$key][] = $value->codigoLineaNegocio;
        $row[$key][] = $value->nombreLineaNegocio; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>