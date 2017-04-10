
<?php

        $cargo = DB::table('cargo')
            ->select(DB::raw('idcargo, nombreCargo,salarioBaseCargo'))
            ->get();
    
        $row = array();

    foreach ($cargo as $key => $value) 
    {  
        
        $row[$key][] = $value->idcargo;
        $row[$key][] = $value->nombreCargo;
        $row[$key][] = $value->salarioBaseCargo;
        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>

