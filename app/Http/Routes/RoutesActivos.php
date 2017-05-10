<?php

Route::resource('clasificacioncrm','ClasificacionCRMController');
Route::get('datosClasificacionCRM', function()
{
    include public_path().'/ajax/datosClasificacionCRM.php';
});


Route::resource('activo','ActivoController');
Route::resource('tipoaccion','TipoAccionController');
Route::resource('tiposervicio','TipoServicioController');
Route::resource('tipoactivo','TipoActivoController');
Route::resource('localizacion','LocalizacionController');
Route::resource('planmantenimiento','PlanMantenimientoController');
Route::resource('transaccionactivo','TransaccionActivoController');
Route::resource('rechazoactivo','RechazoActivoController');
Route::resource('asignacionactivo','AsignacionActivoController');



Route::group(['middleware' => 'auth'], function () 
{
  Route::resource('movimientoactivo','MovimientoActivoController');
 
});
Route::get('plan', function()
{
    return view('planmantenimiento1');
});
Route::get('datosTipoAccion', function()
{
    include public_path().'/ajax/datosTipoAccion.php';
});

Route::get('datosActivoMovimientoDetalle', function()
{
    include public_path().'/ajax/datosActivoMovimientoDetalle.php';
});
Route::get('datosTipoActivo', function()
{
    include public_path().'/ajax/datosTipoActivo.php';
});

Route::get('datosTipoServicio', function()
{
    include public_path().'/ajax/datosTipoServicio.php';
});

Route::get('datosAsignacionActivo', function()
{
    include public_path().'/ajax/datosAsignacionActivo.php';
});
Route::get('datosRechazoActivo', function()
{
    include public_path().'/ajax/datosRechazoActivo.php';
});

Route::get('datosActivo', function()
{
    include public_path().'/ajax/datosActivo.php';
});

Route::get('datosInventarioActivo', function()
{
    include public_path().'/ajax/datosInventarioActivo.php';
});

Route::get('datosLocalizacion', function()
{
    include public_path().'/ajax/datosLocalizacion.php';
});

Route::get('datosPlanMantenimiento', function()
{
    include public_path().'/ajax/datosPlanMantenimiento.php';
});
Route::get('datosMovimientoActivo', function()
{
    include public_path().'/ajax/datosMovimientoActivo.php';
});
Route::get('mostrarActivoPartes', function()
{
    include public_path().'/ajax/mostrarActivoPartes.php';

});

Route::get('datosCompaniaSelect', function()
{
    include public_path().'/ajax/datosCompaniaSelect.php';

});
Route::get('datosRolSelect', function()
{
    include public_path().'/ajax/datosRolSelect.php';

});
Route::get('datosActivoSelect', function()
{
    include public_path().'/ajax/datosActivoSelect.php';

});

Route::get('datosTransaccionActivoSelect', function()
{
    include public_path().'/ajax/datosTransaccionActivoSelect.php';

});

Route::get('ConsultarPendientesMovimientoActivoDetalle', function()
{
    include public_path().'/ajax/ConsultarPendientesMovimientoActivoDetalle.php';

});

Route::get('datosTransaccionActivo', function()
{
    include public_path().'/ajax/datosTransaccionActivo.php';

});
Route::get('datosConceptoActivo', function()
{
    include public_path().'/ajax/datosConceptoActivo.php';

});

Route::get('consultarPermisosActivos', function()
{
    include public_path().'/ajax/consultarPermisosActivos.php';
});

Route::get('mostrarpartesgridselect', function()
{
    return view('mostrarpartesgridselect');
    
});
Route::get('companiagridselect', function()
{
    return view('companiagridselect');
    
});
Route::get('campostransaccionconceptogridselect', function()
{
    return view('campostransaccionconceptogridselect');
    
});

Route::get('campostransaccionconceptogridselect', function()
{
    return view('campostransaccionconceptogridselect');
    
});
Route::get('ActivoMovimientoDetalleSelect', function()
{
    return view('ActivoMovimientoDetalleSelect');
    
});
Route::get('RolGridSelect', function()
{
    return view('RolGridSelect');
    
});
Route::get('ActivoGridSelect', function()
{
    return view('ActivoGridSelect');
    
});

Route::get('TransaccionActivoGridSelect', function()
{
    return view('TransaccionActivoGridSelect');
    
});

Route::get('MovimientoActivoDetalleGridSelect', function()
{
    return view('MovimientoActivoDetalleGridSelect');
    
});
Route::get('planmantenimientoalerta', function()
{
    return view('planmantenimientoalerta');
    
});

Route::get('mostrarActivoComponentes', function()
{
    include public_path().'/ajax/mostrarActivoComponentes.php';

});

Route::get('CamposTransaccionDetalle', function()
{
    include public_path().'/ajax/datosCamposTransaccionDetalle.php';

});

Route::get('CamposTransaccionEncabezado', function()
{
    include public_path().'/ajax/datosCamposTransaccionEncabezado.php';

});
Route::get('mostrarcomponentesgridselect', function()
{
    return view('mostrarcomponentesgridselect');
    
});

Route::get('campostransaccionencabezadogridselect', function()
{
    return view('campostransaccionencabezadogridselect');
    
}); 

Route::get('campostransacciondetallegridselect', function()
{
    return view('campostransacciondetallegridselect');
    
}); 
Route::get('tags', function (Illuminate\Http\Request  $request) 
{
        $term = $request->term ?: '';
        $tags = App\Activo::where('nombreActivo', 'like', $term.'%')->lists('nombreActivo', 'idActivo');
        $valid_tags = [];
        foreach ($tags as $id => $tag) 
        {
            $valid_tags[] = ['idActivo' => $id, 'text' => $tag];
        }
        return \Response::json($valid_tags);
});

Route::get('llamarCaracteristica','ActivoController@llamarCaracteristicasTipoActivo');
Route::get('llamarDocumento','ActivoController@llamarDocumentosTipoActivo');
Route::get('llamarDescripcionActivo','MovimientoActivoController@llamarDescripcionActivo');
Route::get('MostrarDetalleActivo','MovimientoActivoController@MostrarDetalleActivo');
Route::get('AprobacionActivos','MovimientoActivoController@AprobacionActivos');
Route::get('VerificacionComponentes','MovimientoActivoController@VerificacionComponentes');
Route::get('ActualizarMovimientoActivo','MovimientoActivoController@ActualizarMovimientoActivo');
Route::get('AfectarInventario','MovimientoActivoController@AfectarInventario');

Route::get('inventarioactivo', function()
{
    return view('inventarioactivogrid');
    
});



Route::get('password', 'UsersController@password');
Route::resource('passwords', 'CambioPasswordController');

Route::post('updatepassword', 'UsersController@updatePassword');
Route::get('pdf', 'PdfController@invoice');

Route::get('test', function () {
  $pdf = PDF::loadView('clasificacioncrm');
  return $pdf->download('invoice.pdf');

});

Route::get('output', function () {
 
 //return PDF::loadHTML(file_get_contents('HTTP://190.248.133.146:8000/scalia'))->stream('download.pdf');;
 $pdf = App::make('dompdf.wrapper');
$pdf->loadHTML(file_get_contents('http://localhost:8000/activo'));
return $pdf->stream();

});


