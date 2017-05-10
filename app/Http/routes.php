<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
*/
//RUTAS PARA EL LOGIN
require '/Routes/routesLogin.php';

Route::group(['middleware' => 'auth'], function () {

	//RUTAS PARA MAESTROS DE SEGURIDAD
	require '/Routes/routesSeguridad.php';

	//RUTAS MODULO GESTION DOCUMENTAL
	require '/Routes/routesGestionDocumental.php';

	// RUTAS MODULO DISEÑADOR INFORMES
	require '/Routes/routesInformes.php';

	// RUTAS MODULO CONSULTA PRODUCCION
	require '/Routes/routesProduccion.php';


	// RUTAS MODULO IMPORTACIONES
	require '/Routes/routesImportacion.php';

	// RUTAS MODULO INTERFACES
	require '/Routes/routesInterfaces.php';

    // RUTAS MODULO FORWARD
    require '/Routes/routesForward.php';

    // RUTAS MODULO CONTROL DE INGRESO
    require '/Routes/routesControlIngreso.php';

    // RUTAS MODULO CRM
    require '/Routes/routesCRM.php';

    // RUTAS MODULO ENCUESTA
    require '/Routes/routesEncuesta.php';

    // RUTAS MODULO KIOSKO
    require '/Routes/routesKiosko.php';

    require '/Routes/routesActivos.php';

});

Route::resource('log','LogController');
Route::resource('mail','MailController');

