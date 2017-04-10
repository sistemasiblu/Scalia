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


Route::resource('trasladodocumento','TrasladoDocumentoController');

Route::get('datosTrasladoDocumento', function()
{
    include public_path().'/ajax/datosTrasladoDocumento.php';
});

Route::get('datosInterface', function()
{
    include public_path().'/ajax/datosInterface.php';
});

Route::get('datosInterfaceDestino', function()
{
    include public_path().'/ajax/datosInterfaceDestino.php';
});

/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () {
    Route::resource('trasladodocumento','TrasladoDocumentoController');
});


/****************************Rutas AJAX***********************************/

// Route::post('armarMetadatosDocumento', function()
// {
//     include public_path().'/ajax/armarMetadatosDocumento.php';
// });



//********************RUTAS DEL MISMO CONTROLADOR**********************

// Route::get('documentoselect','DocumentoController@indexDocumentoGrid');