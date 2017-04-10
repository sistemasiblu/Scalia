<?php 

$idSistemaInformacion = $_GET['idBd'];
$idDocumento = $_GET['documento'];
$idConcepto = $_GET['concepto'];
$idTercero = $_GET['tercero'];

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
    
    //Consulto cual es el id del periodo dependiendo de la fecha actual para llevar este a la where de la consulta
    $consulta = DB::connection($datos['bdSistemaInformacion'])->Select('SELECT nombreDocumento, nombreDocumentoConcepto, numeroMovimiento, nombre1Tercero, fechaElaboracionMovimiento, idDocumento, idDocumentoConcepto, idMovimiento, idTercero from Movimiento m 
    left join Documento d on m.Documento_idDocumento = d.idDocumento 
    left join DocumentoConcepto dp on m.DocumentoConcepto_idDocumentoConcepto = dp.idDocumentoConcepto
    left join Tercero t on m.Tercero_idTercero = t.idTercero
    where Documento_idDocumento = '.$idDocumento.' and DocumentoConcepto_idDocumentoConcepto = '.$idConcepto.' and idTercero = '.$idTercero.' and fechaElaboracionMovimiento BETWEEN  DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()');


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