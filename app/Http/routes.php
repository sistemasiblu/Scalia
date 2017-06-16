<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/
//RUTAS PARA EL LOGIN
require app_path().'/Http/Routes/routesLogin.php';

Route::get('actualizarIdMovimientoCompra', function()
{
    include public_path().'/ajax/actualizarIdMovimientoCompra.php';
});

Route::group(['middleware' => 'auth'], function () {

    //RUTAS PARA MAESTROS DE SEGURIDAD
    require app_path().'/Http//Routes/routesSeguridad.php';

    //RUTAS MODULO GESTION DOCUMENTAL
    require app_path().'/Http//Routes/routesGestionDocumental.php';

    // RUTAS MODULO DISEÑADOR INFORMES
    require app_path().'/Http//Routes/routesInformes.php';

    // RUTAS MODULO CONSULTA PRODUCCION
    require app_path().'/Http//Routes/routesProduccion.php';


    // RUTAS MODULO IMPORTACIONES
    require app_path().'/Http//Routes/routesImportacion.php';

    // RUTAS MODULO INTERFACES
    require app_path().'/Http//Routes/routesInterfaces.php';

    // RUTAS MODULO FORWARD
    require app_path().'/Http//Routes/routesForward.php';

    // RUTAS MODULO CONTROL DE INGRESO
    require app_path().'/Http//Routes/routesControlIngreso.php';

    // RUTAS MODULO CRM
    require app_path().'/Http//Routes/routesCRM.php';

    // RUTAS MODULO ENCUESTA
    require app_path().'/Http//Routes/routesEncuesta.php';

    // RUTAS MODULO EDI
    require app_path().'/Http//Routes/routesEDI.php';

    // RUTAS MODULO KIOSKO
    require app_path().'/Http//Routes/routesKiosko.php';

    // RUTAS MODULO CONCILIACION CONTABLE
    require app_path().'/Http//Routes/routesConciliacion.php';
});


Route::get('output', function () {
 
 //return PDF::loadHTML(file_get_contents('HTTP://190.248.133.146:8000/scalia'))->stream('download.pdf');;
 $pdf = App::make('dompdf.wrapper');
$pdf->loadHTML(file_get_contents('http://190.248.133.146:8000/movimientocrm/31?accion=imprimir&idDocumentoCRM=2'));
return $pdf->stream();

});

Route::get('grafico', function () {
 
$idCompania = \Session::get("idCompania");

$estado = DB::select(
                            "SELECT nombreEstadoCRM, count(*) as Cantidad, SUM(valorMovimientoCRM) as Valor
                            FROM movimientocrm M 
                            left join estadocrm T 
                            on M.EstadoCRM_idEstadoCRM = T.idEstadoCRM
                            where   M.Compania_idCompania = ".$idCompania ." and 
                                    M.DocumentoCRM_idDocumentoCRM = 2 
                            group by nombreEstadoCRM");

                          //$estados=json_encode($estado);

                       for ($i=0; $i <count($estado) ; $i++) 
                        { 
                            $estados = json_encode($estado[$i]);

                            
                        }
                        



        //return json_encode($data);

return view('dashboardcrmHC', compact('estados'));

});

/*Route::get('listado_graficas', 'GraficasController@index');
Route::get('grafica_registros/{anio}/{mes}', 'GraficasController@registros_mes');
Route::get('grafica_publicaciones', 'GraficasController@total_publicaciones');*/


