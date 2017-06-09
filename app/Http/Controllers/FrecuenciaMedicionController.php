<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FrecuenciaMedicionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('frecuenciamediciongrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('frecuenciamedicion');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        \App\FrecuenciaMedicion::create(
        [
           
            'codigoFrecuenciaMedicion'=>$request['codigoFrecuenciaMedicion'],
            'nombreFrecuenciaMedicion'=>$request['nombreFrecuenciaMedicion'],
            'valorFrecuenciaMedicion'=>$request['valorFrecuenciaMedicion'], 
            'unidadFrecuenciaMedicion'=>$request['unidadFrecuenciaMedicion']
        ]);

        return redirect('/frecuenciamedicion');

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
        $frecuenciamedicion=\App\FrecuenciaMedicion::find($id);
       return view('frecuenciamedicion',compact('frecuenciamedicion'));
       
       //return redirect('/frecuenciamedicion');
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
        $frecuenciamedicion=\App\FrecuenciaMedicion::find($id);
        $frecuenciamedicion->fill($request->all());
        $frecuenciamedicion->save();

        return redirect('/frecuenciamedicion');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       \App\FrecuenciaMedicion::destroy($id);
        return redirect('/frecuenciamedicion');
    }
}
