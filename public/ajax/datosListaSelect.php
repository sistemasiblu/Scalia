<?php

    $output['aaData'] = [];

    if(isset($_GET['idLista']))
    {
        // $lista = DB::table('sublista')
        //               ->select (DB::raw('codigoSubLista, nombreSubLista, Lista_idLista, idSubLista'))
        //               ->where('Lista_idLista', "=", $_GET['idLista'])
        //               ->orwhere('nombreSubLista', 'like', '%'.$_GET['value'].'%')
        //               ->orWhere('codigoSubLista', 'like', '%'.$_GET['value'].'%')
        //               ->get();

        $lista = DB::select('SELECT codigoSubLista, nombreSubLista, Lista_idLista, idSubLista from sublista
                            where Lista_idLista = '.$_GET["idLista"]. ' and (nombreSubLista like "%'.$_GET['value'].'%" or codigoSubLista like "%'.$_GET['value'].'%")');
        // print_r($lista);
        // exit;
        $row = array();

        $tbody = '';

        foreach ($lista as $key => $value) 
        { 
            $value = get_object_vars($lista[$key]);

            $row[$key][] = $value['codigoSubLista'];
            $row[$key][] = $value['nombreSubLista'];
            $row[$key][] = $value['idSubLista'];

        }

        $output['aaData'] = $row;

    }

    echo json_encode($output);
?>