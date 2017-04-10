<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\ZonaRequest;

use Illuminate\Routing\Route;

class ZonaController extends Controller
{
    public function _construct(){
        $this->beforeFilter('@find',['only'=>['edit','update','destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function find(Route $route){
        $this->zona = \App\Zona::find($route->getParameter('zona'));
        return $this->zona;
    }

    public function index()
    {
        return view('zonagrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
       return view('zona',compact('departamento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(ZonaRequest $request)
    {
        \App\Zona::create([
            'codigoZona' => $request['codigoZona'],
            'nombreZona' => $request['nombreZona'],
            'Compania_idCompania' => \Session::get("idCompania")
            ]);
        return redirect('/zona');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $zona = \App\Zona::find($id);
        return view('zona',['zona'=>$zona]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ZonaRequest $request, $id)
    {
        $zona = \App\Zona::find($id);
        $zona->fill($request->all());
        $zona->save();

        return redirect('/zona');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\Zona::destroy($id);
        return redirect('/zona');
    }
}
