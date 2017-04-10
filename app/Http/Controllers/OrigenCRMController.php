<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrigenCRMRequest;

use Illuminate\Routing\Route;

class OrigenCRMController extends Controller
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
        $this->origencrm = \App\OrigenCRM::find($route->getParameter('origencrm'));
        return $this->origencrm;
    }

    public function index()
    {
        return view('origencrmgrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
       return view('origencrm',compact('departamento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(OrigenCRMRequest $request)
    {
        \App\OrigenCRM::create([
            'codigoOrigenCRM' => $request['codigoOrigenCRM'],
            'nombreOrigenCRM' => $request['nombreOrigenCRM']
            ]);
        return redirect('/origencrm');
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
        $origencrm = \App\OrigenCRM::find($id);
        return view('origencrm',['origencrm'=>$origencrm]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(OrigenCRMRequest $request, $id)
    {
        $origencrm = \App\OrigenCRM::find($id);
        $origencrm->fill($request->all());
        $origencrm->save();

        return redirect('/origencrm');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\OrigenCRM::destroy($id);
        return redirect('/origencrm');
    }
}
