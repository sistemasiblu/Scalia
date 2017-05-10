
<?php

$conceptoActivo = DB::select("select * from conceptoactivo");

$row = array();

foreach ($conceptoActivo as $key => $value) 
{  
    $valores = get_object_vars($value);
   
    $row[$key][] = $valores['idConceptoActivo'];
    $row[$key][] = $valores['codigoConceptoActivo'];
    $row[$key][] = $valores['nombreConceptoActivo']; 
   
      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>