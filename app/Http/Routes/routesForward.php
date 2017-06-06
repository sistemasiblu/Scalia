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

Route::get('datosListaFinanciacion', function()
{
    include public_path().'/ajax/datosListaFinanciacion.php';
});

Route::get('datosForward', function()
{
    include public_path().'/ajax/datosForward.php';
});

Route::get('datosTemporadaForward', function()
{
    include public_path().'/ajax/datosTemporadaForward.php';
});

Route::get('datosCompraForward', function()
{
    include public_path().'/ajax/datosCompraForward.php';
});

Route::get('datosPagoForward', function()
{
    include public_path().'/ajax/datosPagoForward.php';
});

Route::get('datosPagoForwardDetalle', function()
{
    include public_path().'/ajax/datosPagoForwardDetalle.php';
});

Route::get('datosPagoForwardDocumento', function()
{
    include public_path().'/ajax/datosPagoForwardDocumento.php';
});

Route::get('datosPagoForwardIM', function()
{
    include public_path().'/ajax/datosPagoForwardIM.php';
});

Route::get('datosCompraDocumentoFinanciero', function()
{
    include public_path().'/ajax/datosCompraDocumentoFinanciero.php';
});

Route::get('datosDocumentoFinanciero', function()
{
    include public_path().'/ajax/datosDocumentoFinanciero.php';
});

Route::get('datosCierreCompra', function()
{
    include public_path().'/ajax/datosCierreCompra.php';
});

Route::get('datosCierreCompraSaldo', function()
{
    include public_path().'/ajax/datosCierreCompraSaldo.php';
});

Route::get('datosCierreCompraCartera', function()
{
    include public_path().'/ajax/datosCierreCompraCartera.php';
});

Route::get('datosCompraForwardGrid', function()
{
    include public_path().'/ajax/datosCompraForwardGrid.php';
});



/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () {
    Route::resource('listafinanciacion','ListaFinanciacionController');
    Route::resource('forward','ForwardController');
    Route::resource('pagoforward','PagoForwardController');

    Route::get('recalcularcartera', function () {
        return view('recalcularcartera');
    });
    Route::resource('documentofinanciero','DocumentoFinancieroController');
    Route::resource('filtrocompraforward','FiltroCompraForwardController');
    Route::resource('cierrecompra','CierreCompraController');
    Route::resource('compraforward','ForwardController@compraforward');
    Route::resource('filtroforward','FiltroForwardController');
    Route::resource('filtroimportacionforward','FiltroImportacionForwardController');
});

/**********************************Rutas de las Grids*********************************/

Route::post('consultarCamposForward', function()
{
    include public_path().'/ajax/consultarCamposForward.php';
});

Route::post('llenarTemporadaPagoForward', function()
{
    include public_path().'/ajax/llenarTemporadaPagoForward.php';
});

Route::post('llenarCompraPagoForward', function()
{
    include public_path().'/ajax/llenarCompraPagoForward.php';
});

/***************************************Rutas AJAX**************************************/

Route::post('recalculoCartera', function()
{
    include public_path().'/ajax/recalcularCartera.php';
});

Route::post('consultarDocumentoFinanciero', function()
{
    include public_path().'/ajax/consultarDocumentoFinanciero.php';
});

Route::post('consultarComprasForwardPadre', function()
{
    include public_path().'/ajax/consultarComprasForwardPadre.php';
});

Route::post('consultarDetalleCompraPagoForward', function()
{
    include public_path().'/ajax/consultarDetalleCompraPagoForward.php';
});

Route::resource('consultarCompraForward','FiltroCompraForwardController@consultarCompraForward');
Route::resource('consultarForward','FiltroForwardController@consultarForward');
Route::resource('consultarImportacionForward','FiltroImportacionForwardController@consultarImportacionForward');