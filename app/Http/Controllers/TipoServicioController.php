<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Redirect;
use App\Http\Requests;
use App\Http\Requests\tiposervicioRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TipoServicioController;

class TipoServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tiposerviciogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tiposervicio');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(tiposervicioRequest $request)
    {
        \App\TipoServicio::create(
        [
       'codigoTipoServicio'=>$request['codigoTipoServicio'],
       'nombreTipoServicio'=>$request['nombreTipoServicio']
      
        ]);
        return Redirect('tiposervicio');
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
        $tiposervicio=\App\TipoServicio::find($id);
        return view('tiposervicio',['tiposervicio'=>$tiposervicio]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(tiposervicioRequest $request, $id)
    {
        $tiposervicio=\App\TipoServicio::find($id);
        $tiposervicio->fill($request->All());
        $tiposervicio->save();
        return Redirect('tiposervicio');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\TipoServicio::destroy($id);
        return Redirect('tiposervicio');
    }
}
