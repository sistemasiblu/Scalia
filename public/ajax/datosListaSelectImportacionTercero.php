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
    
    $formapago = '';
    $nombreForma = '';
    $sucursal = '';
    $centrotrabajo = '';
    $nombreCentro = '';
    
    // TIPO TERCERO DEL CLIENTE
    if ($tipotercero == '01') 
        {
            $formapago = ' left join Iblu.FormaPago on Iblu.Tercero.FormaPago_idFormaPago = Iblu.FormaPago.idFormaPago';
            $nombreForma = ' ,nombreFormaPago as Pago';
            $sucursal =    ' and tipoTercero not like "%18%"';
        }

    // TIPO TERCERO DEL PROVEEDOR
    if ($tipotercero == '02') 
        {
            $formapago = ' left join Iblu.FormaPago on Iblu.Tercero.FormaPago_idFormaPagoCompra = Iblu.FormaPago.idFormaPago';
            $nombreForma = ' ,nombreFormaPago as Pago, idFormaPago';
            $sucursal =  ' and tipoTercero not like "%18%"';
        }

    // TIPO TERCERO EMPLEADO
    if ($tipotercero == '05') 
    {
        $centrotrabajo = ' left join Iblu.CentroTrabajo on Iblu.Tercero.CentroTrabajo_idCentroTrabajo = Iblu.CentroTrabajo.idCentroTrabajo';
        $nombreCentro = ', nombreCentroTrabajo as CentroTrabajo';
    }

    $sql=DB::Select(
    'SELECT '.$nombre.' as Nombre, nombre2Tercero as NombreComercial, '.$codigo.' as Codigo, id'.$tabla.' as ID '.$nombreForma.', direccionTercero as Direccion'.$nombreCentro.'
        FROM Iblu.'.$tabla.' ' .$formapago.' '.$centrotrabajo.
        ' Where '.$nombre.' like "%'.$value.'%" and tipoTercero like "%'.$tipotercero.'%" '.$sucursal);

    $row = array();

    foreach ($sql as $key => $value) 
    { 
        $row[$key][] = $value->ID; 
        $row[$key][] = $value->Nombre;
        $row[$key][] = $value->NombreComercial;  
        $row[$key][] = $value->Codigo;
        $row[$key][] = $campoTabla;
        $row[$key][] = (isset($value->Pago) ? $value->Pago : '');
        $row[$key][] = $value->Direccion;
        $row[$key][] = (isset($value->CentroTrabajo) ? $value->CentroTrabajo : '');
        $row[$key][] = (isset($value->idFormaPago) ? $value->idFormaPago : '');
    }

    $output['aaData'] = $row;
    echo json_encode($output);

?>