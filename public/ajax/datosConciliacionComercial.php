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

    $DB = \Session::get("baseDatosCompania");

    $conciliacioncomercial = DB::select(
        "SELECT idConciliacionComercial, fechaElaboracionConciliacionComercial, name, fechaInicialConciliacionComercial, fechaFinalConciliacionComercial, GROUP_CONCAT(DISTINCT nombreDocumento) AS nombreDocumento
        FROM conciliacioncomercial CC 
        LEFT JOIN ".\Session::get("baseDatosCompania").".Documento D 
        ON D.idDocumento IN(CC.Documento_idDocumento)
        LEFT JOIN users U 
        ON CC.Users_idCrea = U.id 
        WHERE U.Compania_idCompania = ".\Session::get("idCompania").
        " GROUP BY idConciliacionComercial");
    
    // echo "SELECT idConciliacionComercial, fechaElaboracionConciliacionComercial, name, fechaInicialConciliacionComercial, fechaFinalConciliacionComercial, GROUP_CONCAT(DISTINCT nombreDocumento) AS nombreDocumento
    //     FROM conciliacioncomercial CC 
    //     LEFT JOIN ".\Session::get("baseDatosCompania").".Documento D 
    //     ON D.idDocumento IN(CC.Documento_idDocumento)
    //     LEFT JOIN users U 
    //     ON CC.Users_idCrea = U.id 
    //     WHERE U.Compania_idCompania = ".\Session::get("idCompania").
    //     " GROUP BY idConciliacionComercial";
    
    $row = array();

    foreach ($conciliacioncomercial as $key => $value) 
    {  
        $row[$key][] = '<a href="conciliacioncomercial/'.$value->idConciliacionComercial.'/edit">'.
                            '<span class="glyphicon glyphicon-pencil" style = "display:'.$visibleM.'"></span>'.
                        '</a>&nbsp;'.
                        '<a href="conciliacioncomercial/'.$value->idConciliacionComercial.'/edit?accion=eliminar">'.
                            '<span class="glyphicon glyphicon-trash" style = "display:'.$visibleE.'"></span>'.
                        '</a>';
        $row[$key][] = utf8_encode($value->idConciliacionComercial);
        $row[$key][] = utf8_encode($value->fechaElaboracionConciliacionComercial);
        $row[$key][] = utf8_encode($value->name);   
        $row[$key][] = utf8_encode($value->fechaInicialConciliacionComercial);   
        $row[$key][] = utf8_encode($value->fechaFinalConciliacionComercial); 
        $row[$key][] = utf8_encode($value->nombreDocumento); 
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>