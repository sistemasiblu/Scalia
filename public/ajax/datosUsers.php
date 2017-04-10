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

    $users = DB::table('users')
            ->leftJoin('compania', 'Compania_idCompania', '=', 'idCompania')
            ->leftJoin('rol', 'Rol_idRol', '=', 'idRol')
            ->leftJoin(\Session::get("baseDatosCompania")'.Tercero', 'Tercero_idAsociado', '=', 'idTercero')
            ->select(DB::raw('id, name, email, nombreCompania, nombreRol, nombre1Tercero'))
            ->where('Compania_idCompania','=',\Session::get("idCompania"))
            ->get();

    // print_r($users);
    // exit;
    $row = array();

    foreach ($users as $key => $value) 
    {  
        $row[$key][] = '<a href="users/'.$value->id.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style="display: '.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="users/'.$value->id.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style="display: '.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = $value->id;
        $row[$key][] = $value->name;
        $row[$key][] = $value->email;
        $row[$key][] = $value->nombre1Tercero;
        $row[$key][] = $value->nombreCompania;    
        $row[$key][] = $value->nombreRol;
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>