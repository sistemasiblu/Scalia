
<?php

$campostransaccion = DB::select("select idCampoTransaccion, descripcionCampoTransaccion from campotransaccion where tipoCampoTransaccion='Detalle'");

$row = array();

foreach ($campostransaccion as $key => $value) 
{  
    $valores = get_object_vars($value);
   
    $row[$key][] = $valores['idCampoTransaccion'];
    $row[$key][] = $valores['descripcionCampoTransaccion'];
      
}

    $output['aaData'] = $row;
    echo json_encode($output);
   
?>