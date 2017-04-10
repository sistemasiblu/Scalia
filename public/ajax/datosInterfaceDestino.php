<?php 

$idSistemaInformacion = $_GET['idBd'];
$tabla = $_GET['tabla'];
$idCampo = $_GET['idCampo'];
$nombreCampo = $_GET['nombreCampo'];

$datos = DB::table('sistemainformacion')
->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
->where('idSistemaInformacion', "=", $idSistemaInformacion)
->get();


$datos = get_object_vars($datos[0]);
// print_r($datos);
   
   Config::set( 'database.connections.'.$datos['bdSistemaInformacion'], array 
    ( 
        'driver'     =>  $datos['motorbdSistemaInformacion'], 
        'host'       =>  $datos['ipSistemaInformacion'], 
        'port'       =>  $datos['puertoSistemaInformacion'], 
        'database'   =>  $datos['bdSistemaInformacion'], 
        'username'   =>  $datos['usuarioSistemaInformacion'], 
        'password'   =>  $datos['claveSistemaInformacion'], 
        'charset'    =>  'utf8', 
        'collation'  =>  'utf8_unicode_ci', 
        'prefix'     =>  ''
    )); 

    $conexion = DB::connection($datos['bdSistemaInformacion'])->getDatabaseName();

    $tablas = DB::connection($datos['bdSistemaInformacion'])->select('SHOW FULL TABLES FROM '. $datos['bdSistemaInformacion']);

    $condicion = '';
    if ($tabla == 'Tercero') 
    {
        $condicion .= 'where (tipoTercero like "%*01*%" or tipoTercero like "%*02*%") and tipoTercero not like "%*18*%"';
    }
    
    //Consulto cual es el id del periodo dependiendo de la fecha actual para llevar este a la where de la consulta
    $consulta = DB::connection($datos['bdSistemaInformacion'])->Select('SELECT '.$nombreCampo.', '.$idCampo.', IF('.$idCampo.' IS NOT NULL, "'.$tabla.'", "") from '.$tabla.' '.$condicion);


    $row = array();

    foreach ($consulta as $key => $value) 
    {  
        
        foreach ($value as $datoscampo => $campo) 
        {
            $row[$key][] = $campo;
        }                        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>