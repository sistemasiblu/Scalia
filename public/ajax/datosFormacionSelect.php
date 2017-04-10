<?php

        $educacion = DB::table('perfilcargo')
            ->select(DB::raw('idPerfilCargo,nombrePerfilCargo'))   
            ->where('tipoPerfilCargo','=','Formacion')
            ->get();
    
        $row = array();

    foreach ($educacion as $key => $value) 
    {  
        
        $row[$key][] = $value->idPerfilCargo;
        $row[$key][] = $value->nombrePerfilCargo;
        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>