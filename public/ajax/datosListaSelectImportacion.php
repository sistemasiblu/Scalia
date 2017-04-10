<?php

$tabla = $_GET['nombreTabla'];
$nombre = $_GET['campo'];
$codigo = $_GET['codigo'];
$value = $_GET['value'];
$tipotercero = $_GET['tipotercero'];
$campoTabla = $_GET['campoTabla'];

// $idSistemaInformacion = 1;

// $datos = DB::table('sistemainformacion')
// ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
// ->where('idSistemaInformacion', "=", $idSistemaInformacion)
// ->get();


// $datos = get_object_vars($datos[0]);
// // print_r($datos);
   
//    Config::set( 'database.connections.'.$datos['bdSistemaInformacion'], array 
//     ( 
//         'driver'     =>  $datos['motorbdSistemaInformacion'], 
//         'host'       =>  $datos['ipSistemaInformacion'], 
//         'port'       =>  $datos['puertoSistemaInformacion'], 
//         'database'   =>  $datos['bdSistemaInformacion'], 
//         'username'   =>  $datos['usuarioSistemaInformacion'], 
//         'password'   =>  $datos['claveSistemaInformacion'], 
//         'charset'    =>  'utf8', 
//         'collation'  =>  'utf8_unicode_ci', 
//         'prefix'     =>  ''
//     )); 

//     $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();

//     $tablas = DB::connection($datos['bdSistemaInformacion'])->select('SHOW FULL TABLES FROM '. $datos['bdSistemaInformacion']);
    
    $tercero = '';
    $formapago = '';
    $nombreForma = '';
    if ($tipotercero != '9999') 
    {
        $tercero = 'and tipoTercero like "%'.$tipotercero.'%" and tipoTercero not like "%18%"';
    }
    
    // TIPO TERCERO DEL CLIENTE
    if ($tipotercero == '01') 
        {
            $formapago = ' left join Iblu.FormaPago on Iblu.Tercero.FormaPago_idFormaPago = Iblu.FormaPago.idFormaPago';
            $nombreForma = ' ,nombreFormaPago as Pago';
        }

    // TIPO TERCERO DEL PROVEEDOR
    if ($tipotercero == '02') 
        {
            $formapago = ' left join Iblu.FormaPago on Iblu.Tercero.FormaPago_idFormaPagoCompra = Iblu.FormaPago.idFormaPago';
            $nombreForma = ' ,nombreFormaPago as Pago';
        }

    $sql=DB::Select(
    'SELECT '.$nombre.' as Nombre, '.$codigo.' as Codigo, id'.$tabla.' as ID '.$nombreForma.' 
        FROM Iblu.'.$tabla.' ' .$formapago.
        ' Where '.$nombre.' like "%'.$value.'%" '. $tercero);

    $row = array();

    foreach ($sql as $key => $value) 
    { 
        $row[$key][] = $value->ID; 
        $row[$key][] = $value->Nombre;  
        $row[$key][] = $value->Codigo;
        $row[$key][] = $campoTabla;
        $row[$key][] = (isset($value->Pago) ? $value->Pago : '');
    }

    $output['aaData'] = $row;
    echo json_encode($output);

?>