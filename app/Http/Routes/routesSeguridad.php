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

Route::get('error', function () {
    abort(500);
});

Route::get('accesodenegado', function () {
    return view('accesodenegado');
});

/**********************************Rutas de las Grids*********************************/
Route::get('datosUsers', function()
{
    include public_path().'/ajax/datosUsers.php';
});

Route::get('datosRol', function()
{
    include public_path().'/ajax/datosRol.php';
});

Route::get('datosPaquete', function()
{
    include public_path().'/ajax/datosPaquete.php';
});

Route::get('datosOpcion', function()
{
    include public_path().'/ajax/datosOpcion.php';
});

Route::get('datosCompania', function()
{
    include public_path().'/ajax/datosCompania.php';
});


/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () 
{
    Route::resource('scalia','IndexController');
    Route::resource('users','UsersController');
    Route::resource('rol','RolController');
    Route::resource('paquete','PaqueteController');
    Route::resource('opcion','OpcionController');
    Route::resource('compania','CompaniaController');
});

