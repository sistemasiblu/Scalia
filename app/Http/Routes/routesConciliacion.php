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


Route::get('datosDocumentoConciliacion', function()
{
    include public_path().'/ajax/datosDocumentoConciliacion.php';
});
Route::get('datosValorConciliacionSelect', function()
{
    include public_path().'/ajax/datosValorConciliacionSelect.php';
});

Route::get('datosConciliacionComercial', function()
{
    include public_path().'/ajax/datosConciliacionComercial.php';
});

/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () 
{
    // Route::resource('sectorempresa','SectorEmpresaController');
    Route::resource('documentoconciliacion','DocumentoConciliacionController');
    Route::resource('conciliacioncomercial','ConciliacionComercialController');

});

/***************************Rutas AJAX**************************/
Route::get('valorconciliaciongridselect','DocumentoConciliacionController@indexValorConciliacionGrid');


Route::post('guardarConciliacionComercial', function()
{
    include public_path().'/ajax/guardarConciliacionComercial.php';
});

Route::post('guardarObservacionConciliacionComercial', function()
{
    include public_path().'/ajax/guardarObservacionConciliacionComercial.php';
});

Route::post('consultarInformacionConciliacionComercial', function()
{
    include public_path().'/ajax/consultarInformacionConciliacionComercial.php';
});

// Route::resource('consultarInformacion','FiltroDocumentoConciliacionController@consultarInformacion');
