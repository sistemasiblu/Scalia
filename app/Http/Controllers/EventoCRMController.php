<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventoCRMRequest;

use Illuminate\Routing\Route;

class EventoCRMController extends Controller
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
        $this->eventocrm = \App\EventoCRM::find($route->getParameter('eventocrm'));
        return $this->eventocrm;
    }

    public function index()
    {
        return view('eventocrmgrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
       return view('eventocrm',compact('departamento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(EventoCRMRequest $request)
    {
        \App\EventoCRM::create([
            'codigoEventoCRM' => $request['codigoEventoCRM'],
            'nombreEventoCRM' => $request['nombreEventoCRM']
            ]);
        return redirect('/eventocrm');
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
        $eventocrm = \App\EventoCRM::find($id);
        return view('eventocrm',['eventocrm'=>$eventocrm]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(EventoCRMRequest $request, $id)
    {
        $eventocrm = \App\EventoCRM::find($id);
        $eventocrm->fill($request->all());
        $eventocrm->save();

        return redirect('/eventocrm');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\EventoCRM::destroy($id);
        return redirect('/eventocrm');
    }
}
