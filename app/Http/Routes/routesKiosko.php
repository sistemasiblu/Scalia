<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**********************************Rutas de las Grids*********************************/
// Route::get('datosEncuesta', function()
// {
//     include public_path().'/ajax/datosEncuesta.php';
// });


/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () 
{
    Route::resource('kiosko','KioskoController');
    Route::resource('kioskoproduccion','KioskoController@Produccion');
    Route::resource('kioskoproduccionfichatecnica','KioskoController@ProduccionFichaTecnica');
    Route::resource('kioskoproduccionordenproduccion','KioskoController@ProduccionOrdenProduccion');
    Route::resource('kioskoproduccionordencompra','KioskoController@ProduccionOrdenCompra');
    Route::resource('kioskogestionhumana','KioskoController@GestionHumana');

});

/***************************Rutas AJAX**************************/
