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
Route::resource('inventarioedi','InventarioEDIController');
Route::resource('ventaedi','VentaEDIController');
Route::resource('ventaediestimado','VentaEDIEstimadoController');
Route::resource('ventaediestimadoinfo','VentaEDIEstimadoInfoController');

Route::get('datosTrasladoDocumento', function()
{
    include public_path().'/ajax/datosTrasladoDocumento.php';
});


Route::get('datosInventarioEDI', function()
{
    include public_path().'/ajax/datosInventarioEDI.php';
});

Route::post('importarInventarioEDIExcel', [
        'as' => 'importarInventarioEDIExcel', 
        'uses' => 'InventarioEDIController@importarInventarioEDIExcel']);

Route::get('importarArchivoEDI/{tipo}', [
        'as' => 'importarArchivoEDI', 
        'uses' => 'EDIController@importarArchivoEDI']);


Route::get('datosVentaEDI', function()
{
    include public_path().'/ajax/datosVentaEDI.php';
});

Route::post('importarVentaEDIExcel', [
        'as' => 'importarVentaEDIExcel', 
        'uses' => 'VentaEDIController@importarVentaEDIExcel']);

Route::post('importarVentaEDI/{archivo}', [
        'as' => 'importarVentaEDI', 
        'uses' => 'EDIController@importarVentaEDI']);

Route::get('filtroRotacionEDI', [
        'as' => 'consultaVentaInventario', 
        'uses' => 'EDIController@filtroRotacionEDI']);

Route::get('consultaVentaInventario', [
        'as' => 'consultaVentaInventario', 
        'uses' => 'EDIController@consultaVentaInventario']);


Route::get('dropzone','InventarioEDIController@indexdropzone');
Route::post('dropzone/uploadFiles', 'InventarioEDIController@uploadFiles'); 