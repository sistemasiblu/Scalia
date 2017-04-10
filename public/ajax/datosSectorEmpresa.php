<?php

    $sectorempresa = DB::table('sectorempresa')
            ->select(DB::raw('idSectorEmpresa, codigoSectorEmpresa, nombreSectorEmpresa'))
            ->where('Compania_idCompania','=',\Session::get("idCompania"))
            ->get();

    $row = array();

    foreach ($sectorempresa as $key => $value) 
    {  
        $row[$key][] = '<a href="sectorempresa/'.$value->idSectorEmpresa.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil"></span>'.
                        '</a>&nbsp;'.
                        '<a href="sectorempresa/'.$value->idSectorEmpresa.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash"></span>'.
                        '</a>';
        $row[$key][] = $value->idSectorEmpresa;
        $row[$key][] = $value->codigoSectorEmpresa;
        $row[$key][] = $value->nombreSectorEmpresa; 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>