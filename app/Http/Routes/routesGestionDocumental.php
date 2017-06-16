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

/**********************************Rutas sin controlador*********************************/

Route::get('puntolocalizacion', function () {
    return view('puntolocalizacion');
});

/**********************************Rutas de las Grids*********************************/

Route::get('datosDependencia', function()
{
    include public_path().'/ajax/datosDependencia.php';
});

Route::get('datosSerie', function()
{
    include public_path().'/ajax/datosSerie.php';
});

Route::get('datosSubSerie', function()
{
    include public_path().'/ajax/datosSubSerie.php';
});

Route::get('datosMetadato', function()
{
    include public_path().'/ajax/datosMetadato.php';
});

Route::get('datosSistemaInformacion', function()
{
    include public_path().'/ajax/datosSistemaInformacion.php';
});

Route::get('datosSitioWeb', function()
{
    include public_path().'/ajax/datosSitioWeb.php';
});

Route::get('datosNormograma', function()
{
    include public_path().'/ajax/datosNormograma.php';
});

Route::get('datosFuncion', function()
{
    include public_path().'/ajax/datosFuncion.php';
});

Route::get('datosDocumento', function()
{
    include public_path().'/ajax/datosDocumento.php';
});

Route::get('datosRetencion', function()
{
    include public_path().'/ajax/datosRetencion.php';
});

Route::get('datosDependenciaSelect', function()
{
    include public_path().'/ajax/datosDependenciaSelect.php';
});

Route::get('datosEtiqueta', function()
{
    include public_path().'/ajax/datosEtiqueta.php';
});

Route::get('datosRolSelect', function()
{
    include public_path().'/ajax/datosRolSelect.php';
});

Route::get('datosEtiquetaSelect', function()
{
    include public_path().'/ajax/datosEtiquetaSelect.php';
});

Route::get('datosDocumentoSelect', function()
{
    include public_path().'/ajax/datosDocumentoSelect.php';
});

Route::get('datosLista', function()
{
    include public_path().'/ajax/datosLista.php';
});

Route::get('datosListaSelect', function()
{
    include public_path().'/ajax/datosListaSelect.php';
});

Route::get('datosCompaniaSelect', function()
{
    include public_path().'/ajax/datosCompaniaSelect.php';
});

Route::get('datosMetadatoSelect', function()
{
    include public_path().'/ajax/datosMetadatoSelect.php';
});

Route::get('datosTipoSoporteDocumental', function()
{
    include public_path().'/ajax/datosTipoSoporteDocumental.php';
});

Route::get('datosUbicacionDocumento', function()
{
    include public_path().'/ajax/datosUbicacionDocumento.php';
});

/***************************Rutas de los controladores**************************/

    Route::resource('sistemainformacion','SistemaInformacionController');
    Route::resource('normograma','NormogramaController');
    Route::resource('sitioweb','SitioWebController');
    Route::resource('dependencia','DependenciaController');
    Route::resource('serie','SerieController');
    Route::resource('subserie','SubSerieController');
    Route::resource('metadato','MetadatoController');
    Route::resource('documento','DocumentoController');
    Route::resource('retencion','RetencionController');
    Route::resource('clasificaciondocumental','ClasificacionDocumentalController');
    Route::resource('dependenciaselect','DependenciaSelectController');
    Route::resource('etiqueta','EtiquetaController');
    Route::resource('etiquetaselect','EtiquetaSelectController');
    Route::resource('rolselect','RolSelectController');
    Route::resource('radicado','RadicadoController');
    Route::resource('lista','ListaController');
    Route::resource('consultaradicado','ConsultaRadicadoController');
    Route::resource('gridMetadatos','GridMetadatosController');
    Route::resource('formulario','FormularioController');
    Route::resource('gridFormulario','GridFormularioController');
    Route::resource('consultaradicadofiltro','ConsultaRadicadoFiltroController');
    Route::resource('tiposoportedocumental','TipoSoporteDocumentalController');
    Route::resource('ubicaciondocumento','UbicacionDocumentoController');
    Route::resource('filtroinventarioubicacion','FiltroInventarioUbicacionController');



/****************************Rutas AJAX***********************************/

Route::post('armarMetadatosDocumento', function()
{
    include public_path().'/ajax/armarMetadatosDocumento.php';
});

Route::get('datosMetadatos', function()
{
    include public_path().'/ajax/datosMetadatos.php';
});

Route::get('datosFormulario', function()
{
    include public_path().'/ajax/datosFormulario.php';
});

Route::post('llamarPreview', function()
{
    include public_path().'/ajax/llamarPreview.php';
});

Route::post('armarMetadatosConsultaRadicado', function()
{
    include public_path().'/ajax/armarMetadatosConsultaRadicado.php';
});

Route::post('armarMetadatosConsultaFormulario', function()
{
    include public_path().'/ajax/armarMetadatosConsultaFormulario.php';
});

Route::get('imprimir', function()
{
    include public_path().'/ajax/imprimir.php';
});

Route::get('armarGrid', function()
{
    include public_path().'/ajax/armarGrid.php';
});

Route::post('enviarEmail', function()
{
    include public_path().'/ajax/enviarEmail.php';
});

Route::post('descargaMasiva', function()
{
    include public_path().'/ajax/descargaMasiva.php';
});

Route::post('impresionMasiva', function()
{
    include public_path().'/ajax/impresionMasiva.php';
});

Route::post('emailMasivo', function()
{
    include public_path().'/ajax/emailMasivo.php';
});

Route::post('eliminarMasivo', function()
{
    include public_path().'/ajax/eliminarMasivo.php';
});

Route::get('cargar', 'RadicadoController@cargar');

Route::get('download', 'RadicadoController@download');

Route::get('eliminarRadicado/delete/{id}', 'RadicadoController@destroy');

Route::post('conexion', function()
{
    include public_path().'/ajax/conexion.php';
});

Route::post('conexionDocumento', function()
{
    include public_path().'/ajax/conexionDocumento.php';
});

Route::post('conexionDocumentoCampos', function()
{
    include public_path().'/ajax/conexionDocumentoCampos.php';
});

Route::post('consultaMetadatos', function()
{
    include public_path().'/ajax/consultaMetadatos.php';
});

Route::post('armarMetadatosVersion', function()
{
    include public_path().'/ajax/armarMetadatosVersion.php';
});

Route::post('numeroRadicadoVersion', function()
{
    include public_path().'/ajax/numeroRadicadoVersion.php';
});

Route::post('numeroFormularioVersion', function()
{
    include public_path().'/ajax/numeroFormularioVersion.php';
});

Route::post('listarVersiones', function()
{
    include public_path().'/ajax/listarVersiones.php';
});

Route::post('armarMetadatosFormulario', function()
{
    include public_path().'/ajax/armarMetadatosFormulario.php';
});

Route::post('armarMetadatosVersionFormulario', function()
{
    include public_path().'/ajax/armarMetadatosVersionFormulario.php';
});

Route::post('duplicarDocumento', function()
{
    include public_path().'/ajax/duplicarDocumento.php';
});

Route::post('llenarDatosMultiregistro', function()
{
    include public_path().'/ajax/llenarDatosMultiregistro.php';
});

Route::post('consultarCamposMetadatoDocumento', function()
{
    include public_path().'/ajax/consultarCamposMetadatoDocumento.php';
});

Route::post('cargarEstanteDependencia', function()
{
    include public_path().'/ajax/cargarEstanteDependencia.php';
});

Route::post('cerrarCapacidadDependencia', function()
{
    include public_path().'/ajax/cerrarCapacidadDependencia.php';
});

Route::post('llenarCamposUbicacion', function()
{
    include public_path().'/ajax/llenarCamposUbicacion.php';
});

Route::post('asignarPLRadicado', function()
{
    include public_path().'/ajax/asignarPLRadicado.php';
});

Route::post('consultaMetadatosUbicacion', function()
{
    include public_path().'/ajax/consultaMetadatosUbicacion.php';
});


//********************RUTAS DEL MISMO CONTROLADOR**********************
Route::get('dropzone','RadicadoController@indexdropzone');
Route::post('dropzone/uploadFilesRadicado', 'RadicadoController@uploadFiles'); 

Route::get('etiquetaselect}', [
    'as' => 'etiquetaselect', 'uses' => 'RadicadoController@indexEtiquetaGrid'
]);

Route::get('etiquetaselect}', [
    'as' => 'etiquetaselect', 'uses' => 'ConsultarRadicadoController@indexEtiquetaGridConsulta'
]);
Route::get('gridMetadatos}', [
    'as' => 'gridMetadatos', 'uses' => 'ConsultarRadicadoController@indexGridMetadatos'
]);

Route::get('gridFormulario}', [
    'as' => 'gridFormulario', 'uses' => 'FormularioController@indexGridFormulario'
]);

Route::get('documentoselect','DocumentoController@indexDocumentoGrid');

Route::get('companiaselect','CompaniaController@indexCompaniaGrid');

Route::get('metadatoselect','MetadatoController@indexMetadatoGrid');

Route::get('ubicaciondocumentomodal','UbicacionDocumentoController@indexModal');

Route::get('eliminarUbicacion/delete/{id}', 'UbicacionDocumentoController@destroy');

Route::resource('consultarInventarioUbicacion','FiltroInventarioUbicacionController@consultarInventarioUbicacion');