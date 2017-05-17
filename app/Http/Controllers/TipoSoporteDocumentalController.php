<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\TipoSoporteDocumentalRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class TipoSoporteDocumentalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);

        if($datos != null)
            return view('tiposoportedocumentalgrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tiposoportedocumental');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TipoSoporteDocumentalRequest $request)
    {
        \App\TipoSoporteDocumental::create([
            'codigoTipoSoporteDocumental' => $request['codigoTipoSoporteDocumental'],
            'nombreTipoSoporteDocumental' => $request['nombreTipoSoporteDocumental']
        ]);

        return redirect('tiposoportedocumental');
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
        $tiposoportedocumental = \App\TipoSoporteDocumental::find($id);
        return view ('tiposoportedocumental',['tiposoportedocumental'=>$tiposoportedocumental]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TipoSoporteDocumentalRequest $request, $id)
    {
        $tiposoportedocumental = \App\TipoSoporteDocumental::find($id);
        $tiposoportedocumental->fill($request->all());
        $tiposoportedocumental->save();


        return redirect('/tiposoportedocumental');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\TipoSoporteDocumental::destroy($id);
        return redirect('/tiposoportedocumental');
    }
}
