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


/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () 
{
    // Route::resource('sectorempresa','SectorEmpresaController');
    Route::resource('documentoconciliacion','DocumentoConciliacionController');
    

});

/***************************Rutas AJAX**************************/
Route::get('valorconciliaciongridselect','DocumentoConciliacionController@indexValorConciliacionGrid');

