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

    $encuesta = DB::table('encuesta as E')
        ->leftjoin('users as UC','E.Users_idCrea','=','UC.id')
        ->leftjoin('users as UM','E.Users_idModifica','=','UM.id')
        ->select(DB::raw('idEncuesta, tituloEncuesta, descripcionEncuesta, UC.name as usuarioCrea, E.created_at, UM.name as usuarioModifica, E.updated_at'))
        ->where('E.Compania_idCompania','=', \Session::get('idCompania'))->get();
    $row = array();

    foreach ($encuesta as $key => $value) 
    {  
        $row[$key][] = '<a href="encuesta/'.$value->idEncuesta.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="encuesta/'.$value->idEncuesta.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->idEncuesta;
        $row[$key][] = $value->tituloEncuesta;
        $row[$key][] = $value->descripcionEncuesta;   
        $row[$key][] = $value->usuarioCrea;   
        $row[$key][] = $value->created_at;   
        $row[$key][] = $value->usuarioModifica;   
        $row[$key][] = $value->updated_at;   
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>