<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\CollectionStdClass;
//use Illuminate\Support\Facades\DB;
use \App\localizacion;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class LocalizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('localizaciongrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $localizacionPadre=\App\Localizacion::lists('nombreLocalizacion','idLocalizacion');
        return view('localizacion',['localizacionPadre'=>$localizacionPadre]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       \App\Localizacion::create(
    [
      'codigoLocalizacion'=>$request['codigoLocalizacion'],
      'nombreLocalizacion'=>$request['nombreLocalizacion'],
      'Localizacion_idPadre'=>($request['Localizacion_idPadre']  != '' ? $request['Localizacion_idPadre']: null),
      'observacionLocalizacion'=>$request['observacionLocalizacion'],



    ]);

       return redirect('/localizacion');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
       
                      
        $localizacionPadre=\App\Localizacion::where('idLocalizacion','!=' ,$id)->lists('nombreLocalizacion','idLocalizacion')->prepend('Selecciona');
    
        $localizacion=\App\Localizacion::find($id);
        return view('localizacion',['localizacion'=>$localizacion],compact('localizacionPadre'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        
        $localizacion=\App\Localizacion::find($id);
        $localizacion->fill($request->all());
        $localizacion->save();
        return redirect('/localizacion');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Localizacion::destroy($id);
        return redirect('/localizacion');
    }
}
