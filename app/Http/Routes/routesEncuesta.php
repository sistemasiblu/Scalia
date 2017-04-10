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
Route::get('datosEncuesta', function()
{
    include public_path().'/ajax/datosEncuesta.php';
});
Route::get('datosEncuestaPublicacion', function()
{
    include public_path().'/ajax/datosEncuestaPublicacion.php';
});



/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () 
{
    Route::resource('encuesta','EncuestaController');
    Route::resource('encuestapublicacion','EncuestaPublicacionController');

});

/***************************Rutas AJAX**************************/
Route::post('grabarRespuesta', [
            'as' => 'grabarRespuesta', 
            'uses' => 'EncuestaPublicacionController@grabarRespuesta']);