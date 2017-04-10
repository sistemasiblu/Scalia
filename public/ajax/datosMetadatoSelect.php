<?php

    $metadato = DB::table('metadato')
            ->leftJoin('lista', 'Lista_idLista', '=', 'idLista')
            ->select(DB::raw('idMetadato, tituloMetadato, tipoMetadato, nombreLista, opcionMetadato, longitudMetadato, valorBaseMetadato, idLista'))
            ->get();
    
    $row = array();

    foreach ($metadato as $key => $value) 
    {  
        $row[$key][] = $value->idMetadato;
        $row[$key][] = $value->tituloMetadato;
        $row[$key][] = $value->tipoMetadato;
        $row[$key][] = $value->nombreLista;
        $row[$key][] = $value->opcionMetadato;
        $row[$key][] = $value->longitudMetadato;
        $row[$key][] = $value->valorBaseMetadato;  
        $row[$key][] = $value->idLista;  
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>