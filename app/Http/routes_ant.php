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

/**********************************Rutas sin controlador*********************************/
// Route::get('consultaproduccion', function () {
//     return view('ConsultaProduccionGrid');
// });

Route::get('accesodenegado', function () {
    return view('accesodenegado');
});

Route::get('/controlingresogrid', function () {
    return view('controlingresogrid');
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

Route::get('datosCompania', function()
{
    include public_path().'/ajax/datosCompania.php';
});

Route::get('datosUsers', function()
{
    include public_path().'/ajax/datosUsers.php';
});

Route::get('datosDocumento', function()
{
    include public_path().'/ajax/datosDocumento.php';
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

Route::get('datosCompaniaSelect', function()
{
    include public_path().'/ajax/datosCompaniaSelect.php';
});

Route::get('datosMetadatoSelect', function()
{
    include public_path().'/ajax/datosMetadatoSelect.php';
});

Route::get('datosLista', function()
{
    include public_path().'/ajax/datosLista.php';
});

Route::get('datosListaSelect', function()
{
    include public_path().'/ajax/datosListaSelect.php';
});

Route::get('datosListaSelectImportacion', function()
{
    include public_path().'/ajax/datosListaSelectImportacion.php';
});

Route::get('datosListaSelectImportacionTercero', function()
{
    include public_path().'/ajax/datosListaSelectImportacionTercero.php';
});

Route::get('datosConsultaProduccion', function()
{
    include public_path().'/ajax/datosConsultaProduccion.php';
});

Route::get('datosDocumentoImportacion', function()
{
    include public_path().'/ajax/datosDocumentoImportacion.php';
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

Route::get('datosListaFinanciacion', function()
{
    include public_path().'/ajax/datosListaFinanciacion.php';
});

Route::get('datosListaFinanciacion', function()
{
    include public_path().'/ajax/datosListaFinanciacion.php';
});

Route::get('datosForward', function()
{
    include public_path().'/ajax/datosForward.php';
});

Route::get('datosCompraForward', function()
{
    include public_path().'/ajax/datosCompraForward.php';
});

Route::get('datosTemporadaForward', function()
{
    include public_path().'/ajax/datosTemporadaForward.php';
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

Route::get('datosMensajeria', function()
{
    include public_path().'/ajax/datosMensajeria.php';
});

Route::get('datosControlIngreso', function()
{
    include public_path().'/ajax/datosControlIngreso.php';
});

Route::get('datosDispositivo', function()
{
    include public_path().'/ajax/datosDispositivo.php';
});

Route::get('datosMarca', function()
{
    include public_path().'/ajax/datosMarca.php';
});

Route::get('datosDocumentoFinanciero', function()
{
    include public_path().'/ajax/datosDocumentoFinanciero.php';
});

/***************************Rutas de los controladores**************************/
Route::group(['middleware' => 'auth'], function () {
    Route::resource('sistemainformacion','SistemaInformacionController');
    Route::resource('normograma','NormogramaController');
    Route::resource('sitioweb','SitioWebController');
    Route::resource('dependencia','DependenciaController');
    Route::resource('serie','SerieController');
    Route::resource('subserie','SubSerieController');
    Route::resource('metadato','MetadatoController');
    Route::resource('scalia','IndexController');
    Route::resource('users','UsersController');
    Route::resource('rol','RolController');
    Route::resource('opcion','OpcionController');
    Route::resource('paquete','PaqueteController');
    Route::resource('compania','CompaniaController');
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
    Route::resource('consultaproduccion','ConsultaProduccionController');
    Route::resource('compra','CompraController');
    Route::resource('documentoimportacion','DocumentoImportacionController');
    Route::resource('embarque','EmbarqueController');
    Route::get('consultaembarque','EmbarqueController@indexConsultaEmbarque');
    Route::get('filtroimportacion','FiltroImportacionController@index');
    Route::get('filtroimportaciondetallado','FiltroImportacionDetalladoController@index');
    Route::resource('correoembarque','CorreoEmbarqueController');
    Route::resource('consultaradicadofiltro','ConsultaRadicadoFiltroController');
    Route::resource('trasladodocumento','TrasladoDocumentoController');
    Route::resource('inventarioedi','InventarioEDIController');
    Route::resource('listafinanciacion','ListaFinanciacionController');
    Route::resource('listafinanciacion','ListaFinanciacionController');
    Route::resource('forward','ForwardController');
    Route::resource('pagoforward','PagoForwardController');
    Route::get('recalcularcartera', function () {
        return view('recalcularcartera');
    });
    Route::resource('mensajeria','MensajeriaController');
    Route::resource('controlingreso','ControlIngresoController');
    Route::resource('dispositivo','DispositivoController');
    Route::resource('marca','MarcaController');
    Route::resource('documentofinanciero','DocumentoFinancieroController');
});


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

Route::post('consultaMetadatosCompra', function()
{
    include public_path().'/ajax/consultaMetadatosCompra.php';
});

Route::post('consultaObservacionOP', function()
{
    include public_path().'/ajax/consultaObservacionOP.php';
});

Route::post('actualizarObservacionOP', function()
{
    include public_path().'/ajax/actualizarObservacionOP.php';
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

Route::post('consultarCamposMetadatoDocumento', function()
{
    include public_path().'/ajax/consultarCamposMetadatoDocumento.php';
});

Route::post('consultarCamposForward', function()
{
    include public_path().'/ajax/consultarCamposForward.php';
});

Route::post('llenarTemporadaPagoForward', function()
{
    include public_path().'/ajax/llenarTemporadaPagoForward.php';
});

Route::post('consultarDocumentoFinanciero', function()
{
    include public_path().'/ajax/consultarDocumentoFinanciero.php';
});

//********************RUTAS DEL MISMO CONTROLADOR**********************
Route::get('dropzone','RadicadoController@indexdropzone');
Route::post('dropzone/uploadFilesRadicado', 'RadicadoController@uploadFiles'); 

Route::get('dropzone','InventarioEDIController@indexdropzone');
Route::post('dropzone/uploadFiles', 'InventarioEDIController@uploadFiles'); 

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

Route::get('compragridselect','EmbarqueController@indexCompraGrid');

Route::get('documentoselect','DocumentoController@indexDocumentoGrid');

Route::get('companiaselect','CompaniaController@indexCompaniaGrid');

Route::get('metadatoselect','MetadatoController@indexMetadatoGrid');

Route::get('radicar', [
        'as' => 'radicar', 
        'uses' => 'RadicadoController@radicar']);

// Route::get('listaselect', 'ListaController@ListaSelect');

//RUTAS PARA EL LOGIN

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', ['as' =>'auth/login', 'uses' => 'Auth\AuthController@postLogin']);
Route::get('auth/logout', ['as' => 'auth/logout', 'uses' => 'Auth\AuthController@getLogout']);




// RUTAS MODULO DISEÑADOR INFORMES
Route::get('/disenadorinforme', function () {
    return view('disenadorinforme');
});
Route::get('/visorinforme', function () {
    return view('visorinforme');
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


Route::post('consultarCategoriaInforme', function()
{
    include public_path().'/ajax/consultarCategoriaInforme.php';
});
Route::post('guardarCategoriaInforme', function()
{
    include public_path().'/ajax/guardarCategoriaInforme.php';
});

Route::post('mostrarInformesCategoria', function()
{
    include public_path().'/ajax/mostrarInformesCategoria.php';
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

Route::post('consultarInformeGrupo', function()
{
    include public_path().'/ajax/consultarInformeGrupo.php';
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

Route::get('generarInforme2', function()
{
    include public_path().'/ajax/generarInforme2.php';
});

Route::post('eliminarInforme', function()
{
    include public_path().'/ajax/eliminarInforme.php';
});
Route::post('duplicarInforme', [
        'as' => 'duplicarInforme', 
        'uses' => 'InformeController@duplicate']);

Route::post('moverInforme', [
        'as' => 'moverInforme', 
        'uses' => 'InformeController@move']);

Route::get('datosInformeConceptoSelect', function()
{
    include public_path().'/ajax/datosInformeConceptoSelect.php';
});

Route::post('recalculoCartera', function()
{
    include public_path().'/ajax/recalcularCartera.php';
});

Route::post('llenarCentroTrabajo', function()
{
    include public_path().'/ajax/llenarCentroTrabajo.php';
});

Route::post('consultarControlIngreso', function()
{
    include public_path().'/ajax/consultarControlIngreso.php';
});

 
Route::get('categoriainformegridselect','InformeConceptoController@indexCategoriaInformeGrid');
Route::get('datosCategoriaSelect', function()
{
    include public_path().'/ajax/datosCategoriaSelect.php';
});


// INFORME DE IMPORTACION

Route::get('consultarImportacion', [
        'as' => 'consultarImportacion', 
        'uses' => 'FiltroImportacionController@consultarImportacion']);

Route::get('consultarImportacionDetallado', [
        'as' => 'consultarImportacionDetallado', 
        'uses' => 'FiltroImportacionDetalladoController@consultarImportacionDetallado']);


///---------------------------------------------------------

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

Route::resource('ventaedi','VentaEDIController');

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

Route::get('consultaVentaInventario', [
        'as' => 'consultaVentaInventario', 
        'uses' => 'EDIController@consultaVentaInventario']);


Route::get('filtroRotacionEDI', [
        'as' => 'consultaVentaInventario', 
        'uses' => 'EDIController@filtroRotacionEDI']);

// Route::get('dropzone','InventarioEDIController@indexdropzone');
// Route::post('dropzone/uploadFiles', 'InventarioEDIController@uploadFiles'); 