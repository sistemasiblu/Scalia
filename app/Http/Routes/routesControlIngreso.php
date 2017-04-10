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
Route::get('datosControlIngreso', function()
{
    include public_path().'/ajax/datosControlIngreso.php';
});

Route::get('datosDispositivo', function()
{
    include public_path().'/ajax/datosDispositivo.php';
});

Route::get('datosMarca', function()
{
    include public_path().'/ajax/datosMarca.php';
});

Route::get('datosMensajeria', function()
{
    include public_path().'/ajax/datosMensajeria.php';
});


/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () 
{
    Route::resource('controlingreso','ControlIngresoController');
    Route::resource('dispositivo','DispositivoController');
    Route::resource('marca','MarcaController');
    Route::resource('mensajeria','MensajeriaController');
    Route::get('/controlingresogrid', function () {
        return view('controlingresogrid');
    });
});

/***************************Rutas AJAX**************************/
Route::post('consultarControlIngreso', function()
{
    include public_path().'/ajax/consultarControlIngreso.php';
});

Route::post('llenarCentroTrabajo', function()
{
    include public_path().'/ajax/llenarCentroTrabajo.php';
});

