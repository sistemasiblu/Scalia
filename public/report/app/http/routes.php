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

Route::get('/prueba', function () {
    return view('pruebascampos');
});

Route::get('/disenadorinforme', function () {
    return view('disenadorinforme');
});


//Ajax de Maestros
Route::post('consultarSistemaInformacion', function()
{
    include public_path().'/ajax/consultarSistemaInformacion.php';
});

Route::post('guardarSistemaInformacion', function()
{
    include public_path().'/ajax/guardarSistemaInformacion.php';
});

Route::post('consultarEstiloInforme', function()
{
    include public_path().'/ajax/consultarEstiloInforme.php';
});

Route::post('guardarEstiloInforme', function()
{
    include public_path().'/ajax/guardarEstiloInforme.php';
});



Route::post('consultarInforme', function()
{
    include public_path().'/ajax/consultarInforme.php';
});

Route::post('consultarInformeCapa', function()
{
    include public_path().'/ajax/consultarInformeCapa.php';
});

Route::post('consultarInformeConcepto', function()
{
    include public_path().'/ajax/consultarInformeConcepto.php';
});

Route::post('consultarInformeObjeto', function()
{
    include public_path().'/ajax/consultarInformeObjeto.php';
});

Route::post('guardarInforme', function()
{
    include public_path().'/ajax/guardarInforme.php';
});


Route::post('conexionDocumento', function()
{
    include public_path().'/ajax/conexionDocumento.php';
});


Route::post('conexionDocumentoCampos', function()
{
    include public_path().'/ajax/conexionDocumentoCampos.php';
});

Route::get('generarInforme', function()
{
    include public_path().'/ajax/generarInforme.php';
});



