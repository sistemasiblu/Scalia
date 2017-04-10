<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoriaCRMRequest;

use Illuminate\Routing\Route;

class CategoriaCRMController extends Controller
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
        $this->categoriacrm = \App\CategoriaCRM::find($route->getParameter('categoriacrm'));
        return $this->categoriacrm;
    }

    public function index()
    {
        return view('categoriacrmgrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
       return view('categoriacrm',compact('departamento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(CategoriaCRMRequest $request)
    {
        \App\CategoriaCRM::create([
            'codigoCategoriaCRM' => $request['codigoCategoriaCRM'],
            'nombreCategoriaCRM' => $request['nombreCategoriaCRM']
            ]);
        return redirect('/categoriacrm');
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
        $categoriacrm = \App\CategoriaCRM::find($id);
        return view('categoriacrm',['categoriacrm'=>$categoriacrm]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CategoriaCRMRequest $request, $id)
    {
        $categoriacrm = \App\CategoriaCRM::find($id);
        $categoriacrm->fill($request->all());
        $categoriacrm->save();

        return redirect('/categoriacrm');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\CategoriaCRM::destroy($id);
        return redirect('/categoriacrm');
    }
}
