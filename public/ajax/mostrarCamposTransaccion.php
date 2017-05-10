
<?php



$campostransaccion = DB::select("select * from campotransaccion ");

$row = array();

foreach ($campostransaccion as $key => $value) 
{  
    $valores = get_object_vars($value);
   
    $row[$key][] = $valores['idCampoTransaccion'];
    $row[$key][] = $valores['tipoCampoTransaccion'];
    $row[$key][] = $valores['nombreCampoTransaccion']; 
    $row[$key][] = $valores['descripcionCampoTransaccion'];

      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>