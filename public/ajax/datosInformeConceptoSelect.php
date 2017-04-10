<?php
$idCapa = (isset($_GET["idCapa"]) ? $_GET["idCapa"] : 6);
    $consulta = DB::Select(
        'SELECT idInformeConcepto, nombreInformeConcepto 
        FROM informeconcepto 
        Where InformeCapa_idInformeCapa = '.$idCapa);

   
    $row = array();

    foreach ($consulta as $key => $value) 
    {  
        
        foreach ($value as $key2 => $campo) 
        {
            $row[$key][] = $campo;
        }                        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>