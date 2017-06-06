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

Route::get('datosListaSelectImportacion', function()
{
    include public_path().'/ajax/datosListaSelectImportacion.php';
});

Route::get('datosListaSelectImportacionTercero', function()
{
    include public_path().'/ajax/datosListaSelectImportacionTercero.php';
});

Route::get('datosDocumentoImportacion', function()
{
    include public_path().'/ajax/datosDocumentoImportacion.php';
});

Route::get('datosRolSelect', function()
{
    include public_path().'/ajax/datosRolSelect.php';
});

Route::get('datosCompra', function()
{
    include public_path().'/ajax/datosCompra.php';
});

Route::get('datosCompraSelect', function()
{
    include public_path().'/ajax/datosCompraSelect.php';
});

Route::get('datosEmbarque', function()
{
    include public_path().'/ajax/datosEmbarque.php';
});

Route::get('datosCorreoEmbarque', function()
{
    include public_path().'/ajax/datosCorreoEmbarque.php';
});

Route::get('datosConsultaEmbarque', function()
{
    include public_path().'/ajax/datosConsultaEmbarque.php';
});

Route::get('datosFiltroImportacionGrid', function()
{
    include public_path().'/ajax/datosFiltroImportacionGrid.php';
});

/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () {
    Route::resource('documentoimportacion','DocumentoImportacionController');
    Route::resource('rolselect','RolSelectController');
    Route::resource('compra','CompraController');
    Route::resource('embarque','EmbarqueController');
    Route::resource('correoembarque','CorreoEmbarqueController');
    Route::resource('filtroimportacion','FiltroImportacionController');
    Route::resource('filtroimportaciondetallado','FiltroImportacionDetalladoController');
    Route::get('consultaembarque','EmbarqueController@indexConsultaEmbarque');
    Route::get('filtroimportaciongrid','FiltroImportacionController@gridFiltroImportacion');
    Route::resource('filtrotemporada','FiltroTemporadaController');
});


/****************************Rutas AJAX***********************************/

Route::post('consultaMetadatosCompra', function()
{
    include public_path().'/ajax/consultaMetadatosCompra.php';
});

Route::post('listarVersionesCompra', function()
{
    include public_path().'/ajax/listarVersionesCompra.php';
});

Route::post('llenarDatosVersionCompra', function()
{
    include public_path().'/ajax/llenarDatosVersionCompra.php';
});

Route::post('consultarCamposCompraEmbarque', function()
{
    include public_path().'/ajax/consultarCamposCompraEmbarque.php';
});

Route::post('validarNumeroEmbarque', function()
{
    include public_path().'/ajax/validarNumeroEmbarque.php';
});

Route::post('listarUnidadMedida', function()
{
    include public_path().'/ajax/listarUnidadMedida.php';
});

Route::post('actualizarEstadoCompra', function()
{
    include public_path().'/ajax/actualizarEstadoCompra.php';
});

Route::post('consultarCodigoTemporada', function()
{
    include public_path().'/ajax/consultarCodigoTemporada.php';
});

Route::post('guardarTemporada', function()
{
    include public_path().'/ajax/guardarTemporada.php';
});

Route::post('guardarEvento', function()
{
    include public_path().'/ajax/guardarEvento.php';
});

Route::post('guardarProveedor', function()
{
    include public_path().'/ajax/guardarProveedor.php';
});

Route::post('mostrarDetalleTemporada', function()
{
    include public_path().'/ajax/mostrarDetalleTemporada.php';
});

Route::get('actualizarIdMovimientoCompra', function()
{
    include public_path().'/ajax/actualizarIdMovimientoCompra.php';
});

Route::post('llenarMetadatosCompraTemporada', function()
{
    include public_path().'/ajax/llenarMetadatosCompraTemporada.php';
});

Route::resource('consultarImportacion','FiltroImportacionController@consultarImportacion');
Route::resource('consultarImportacionDetallado','FiltroImportacionDetalladoController@consultarImportacionDetallado');
Route::resource('consultarTemporada','FiltroTemporadaController@consultarTemporada');

//********************RUTAS DEL MISMO CONTROLADOR**********************
Route::get('compragridselect','EmbarqueController@indexCompraGrid');
