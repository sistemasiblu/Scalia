<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\SectorEmpresaRequest;

use Illuminate\Routing\Route;

class SectorEmpresaController extends Controller
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
        $this->sectorempresa = \App\SectorEmpresa::find($route->getParameter('sectorempresa'));
        return $this->sectorempresa;
    }

    public function index()
    {
        return view('sectorempresagrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
       return view('sectorempresa',compact('departamento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(SectorEmpresaRequest $request)
    {
        \App\SectorEmpresa::create([
            'codigoSectorEmpresa' => $request['codigoSectorEmpresa'],
            'nombreSectorEmpresa' => $request['nombreSectorEmpresa'],
            'Compania_idCompania' => \Session::get("idCompania")
            ]);
        return redirect('/sectorempresa');
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
        $sectorempresa = \App\SectorEmpresa::find($id);
        return view('sectorempresa',['sectorempresa'=>$sectorempresa]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(SectorEmpresaRequest $request, $id)
    {
        $sectorempresa = \App\SectorEmpresa::find($id);
        $sectorempresa->fill($request->all());
        $sectorempresa->save();

        return redirect('/sectorempresa');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\SectorEmpresa::destroy($id);
        return redirect('/sectorempresa');
    }
}
