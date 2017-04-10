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


Route::get('datosClasificacionCRM', function()
{
    include public_path().'/ajax/datosClasificacionCRM.php';
});

Route::get('datosCampoCRMSelect', function()
{
    include public_path().'/ajax/datosCampoCRMSelect.php';
});
Route::get('datosCompaniaSelect', function()
{
    include public_path().'/ajax/datosCompaniaSelect.php';
});
Route::get('datosRolSelect', function()
{
    include public_path().'/ajax/datosRolSelect.php';
});


Route::get('datosHabilidadSelect', function()
{
    include public_path().'/ajax/datosHabilidadSelect.php';
});

Route::get('datosFormacionSelect', function()
{
    include public_path().'/ajax/datosFormacionSelect.php';
});

Route::get('datosEducacionSelect', function()
{
    include public_path().'/ajax/datosEducacionSelect.php';
}); 



Route::post('llenarFormacionCargo', function()
{
    include public_path().'/ajax/llenarFormacionCargo.php';
});
Route::post('llenarEducacionCargo', function()
{
    include public_path().'/ajax/llenarEducacionCargo.php';
});

Route::post('llenarHabilidadCargo', function()
{
include public_path().'/ajax/llenarHabilidadCargo.php';
});


Route::get('datosPresupuesto', function()
{
    include public_path().'/ajax/datosPresupuesto.php';
});

// Route::get('datosSectorEmpresa', function()
// {
//     include public_path().'/ajax/datosSectorEmpresa.php';
// });

Route::get('datosLineaNegocio', function()
{
    include public_path().'/ajax/datosLineaNegocio.php';
});

Route::get('datosOrigenCRM', function()
{
    include public_path().'/ajax/datosOrigenCRM.php';
});

// Route::get('datosZona', function()
// {
//     include public_path().'/ajax/datosZona.php';
// });

/*Ruta Grid selct Asesor Grupo Estado*/
Route::get('mostrarAsesoresGrupoEstado', function()
{
    include public_path().'/ajax/mostrarAsesoresGrupoEstado.php';

});


Route::get('datosCategoriaCRM', function()
{
    include public_path().'/ajax/datosCategoriaCRM.php';
});
Route::get('datosEventoCRM', function()
{
    include public_path().'/ajax/datosEventoCRM.php';
});
Route::get('datosAcuerdoServicio', function()
{
    include public_path().'/ajax/datosAcuerdoServicio.php';
});
Route::get('datosGrupoEstado', function()
{
    include public_path().'/ajax/datosGrupoEstado.php';
});

Route::get('datosDocumentoCRM', function()
{
    include public_path().'/ajax/datosDocumentoCRM.php';
});

Route::get('datosMovimientoCRM', function()
{
    include public_path().'/ajax/datosMovimientoCRM.php';
});
Route::post('llenarCampo', function()
{
    include public_path().'/ajax/llenarCampo.php';
});
Route::post('llenarCompania', function()
{
    include public_path().'/ajax/llenarCompania.php';
});
Route::post('llenarRol', function()
    {
        include public_path().'/ajax/llenarRol.php';
    });

//************************************
// Rutas de Ajax de Encuestas
//************************************

Route::get('datosEncuesta', function()
{
    include public_path().'/ajax/datosEncuesta.php';
});
Route::get('datosEncuestaPublicacion', function()
{
    include public_path().'/ajax/datosEncuestaPublicacion.php';
});



/***************************Rutas de los controladores**************************/

Route::group(['middleware' => 'auth'], function () 
{
    // Route::resource('sectorempresa','SectorEmpresaController');
    Route::resource('lineanegocio','LineaNegocioController');
    Route::resource('origencrm','OrigenCRMController');
    // Route::resource('zona','ZonaController');
    Route::resource('categoriacrm','CategoriaCRMController');
    Route::resource('eventocrm','EventoCRMController');
    Route::resource('acuerdoservicio','AcuerdoServicioController');
    Route::resource('grupoestado','GrupoEstadoController');
    Route::resource('documentocrm','DocumentoCRMController');
    Route::resource('movimientocrm','MovimientoCRMController');
    Route::resource('presupuesto','PresupuestoController');
    Route::resource('clasificacioncrm','ClasificacionCRMController');


    // *************************************
    // Rutas de Encuestas
    // *************************************
    Route::resource('encuesta','EncuestaController');
    Route::resource('encuestapublicacion','EncuestaPublicacionController');

    

});

/***************************Rutas AJAX**************************/
Route::get('grupoapoyogridSelect','GrupoApoyoController@indexGrupoApoyoGrid');
Route::get('informeconceptogridselect','VisorInformeController@indexInformeConceptoGrid');
Route::get('campocrmgridselect','DocumentoCRMController@indexCampoCRMGrid');
Route::get('companiagridselect','DocumentoCRMController@indexCompaniaGrid');
Route::get('rolgridselect','DocumentoCRMController@indexRolGrid');
Route::get('actagrupoapoyoselect','ActaGrupoApoyoController@indexActaGrid');

/*Grid Select Asesores Grupo Estado*/
Route::get('mostrarasesoresgridselect', function()
{
    return view('mostrarasesoresgridselect');
    
});


Route::get('MovimientocrmVacantegridselect', 'MovimientoCRMController@indexMovimientocrmVacantegridselect');

Route::get('datosMovimientocrmVacantegridselect', function()
{
    include public_path().'/ajax/datosMovimientocrmVacantegridselect.php';
});

/*RUTA CONTROLADOR MOVIMIENTO CRM*/
Route::get('llamarsubclasificacion','MovimientoCRMController@Subclasificacion');




Route::post('guardarAsesorMovimientoCRM', [
            'as' => 'guardarAsesorMovimientoCRM', 
            'uses' => 'MovimientoCRMController@guardarAsesorMovimientoCRM']);

Route::post('consultarAsesorMovimientoCRM', [
            'as' => 'consultarAsesorMovimientoCRM', 
            'uses' => 'MovimientoCRMController@consultarAsesorMovimientoCRM']);

Route::post('consultarDiasAcuerdoServicio', [
            'as' => 'consultarDiasAcuerdoServicio', 
            'uses' => 'MovimientoCRMController@consultarDiasAcuerdoServicio']);

Route::post('grabarRespuesta', [
            'as' => 'grabarRespuesta', 
            'uses' => 'EncuestaPublicacionController@grabarRespuesta']);