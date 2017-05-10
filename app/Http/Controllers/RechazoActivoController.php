<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class RechazoActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rechazoactivogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rechazoactivo');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\RechazoActivo::create(
    [
      'codigoRechazoActivo'=>$request['codigoRechazoActivo'],
      'nombreRechazoActivo'=>$request['nombreRechazoActivo'],
      'observacionRechazoActivo'=>$request['observacionRechazoActivo'],

    ]);

       return redirect('/rechazoactivo');
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
       
        $rechazoactivo=\App\RechazoActivo::find($id);
        return view('rechazoactivo',compact('rechazoactivo'));
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
        $rechazoactivo=\App\RechazoActivo::find($id);
        $rechazoactivo->fill($request->all());
        $rechazoactivo->save();
        return redirect('/rechazoactivo');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\RechazoActivo::destroy($id);
        return redirect('/rechazoactivo');
    }
}
