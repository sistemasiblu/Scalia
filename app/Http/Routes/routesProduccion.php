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

Route::get('datosConsultaProduccion', function()
{
    include public_path().'/ajax/datosConsultaProduccion.php';
});

/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () {
    Route::resource('consultaproduccion','ConsultaProduccionController');
});

/****************************Rutas AJAX***********************************/

Route::post('consultaObservacionOP', function()
{
    include public_path().'/ajax/consultaObservacionOP.php';
});

Route::post('actualizarObservacionOP', function()
{
    include public_path().'/ajax/actualizarObservacionOP.php';
});
