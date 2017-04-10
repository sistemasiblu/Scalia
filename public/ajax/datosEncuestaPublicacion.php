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

    $encuestapublicacion = DB::table('encuestapublicacion as EP')
        ->leftjoin('encuesta as E', 'EP.Encuesta_idEncuesta','=','E.idEncuesta')
        ->select(DB::raw('idEncuestaPublicacion, nombreEncuestaPublicacion, fechaEncuestaPublicacion, tituloEncuesta'))
        ->where('E.Compania_idCompania','=', \Session::get('idCompania'))->get();
    $row = array();

    foreach ($encuestapublicacion as $key => $value) 
    {  
        $row[$key][] = '<a href="encuestapublicacion/'.$value->idEncuestaPublicacion.'/edit">
                            <span class="glyphicon glyphicon-pencil" style = "display:'.$visibleM.'"></span>
                        </a>&nbsp;
                        <a href="encuestapublicacion/'.$value->idEncuestaPublicacion.'/edit?accion=eliminar">
                            <span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>
                        </a>
                        <a href="#" onclick="mostrarTabulacionEncuesta('.$value->idEncuestaPublicacion.');" title="Ver Tabulación">
                            <span class="glyphicon glyphicon-print" style = "display:'.$visibleM.'"></span>
                        </a>

                        ';
        $row[$key][] = $value->idEncuestaPublicacion;
        $row[$key][] = $value->nombreEncuestaPublicacion;
        $row[$key][] = $value->fechaEncuestaPublicacion;
        $row[$key][] = $value->tituloEncuesta;
        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>