<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\AcuerdoServicioRequest;

use Illuminate\Routing\Route;

class AcuerdoServicioController extends Controller
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
        $this->acuerdoservicio = \App\AcuerdoServicio::find($route->getParameter('acuerdoservicio'));
        return $this->acuerdoservicio;
    }

    public function index()
    {
        return view('acuerdoserviciogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
       return view('acuerdoservicio',compact('departamento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(AcuerdoServicioRequest $request)
    {
        \App\AcuerdoServicio::create([
            'codigoAcuerdoServicio' => $request['codigoAcuerdoServicio'],
            'nombreAcuerdoServicio' => $request['nombreAcuerdoServicio'],
            'tiempoAcuerdoServicio'  => $request['tiempoAcuerdoServicio'],
            'unidadTiempoAcuerdoServicio' => $request['unidadTiempoAcuerdoServicio'], 
            'observacionAcuerdoServicio' => $request['observacionAcuerdoServicio']
            ]);
        return redirect('/acuerdoservicio');
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
        $acuerdoservicio = \App\AcuerdoServicio::find($id);
        return view('acuerdoservicio',['acuerdoservicio'=>$acuerdoservicio]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(AcuerdoServicioRequest $request, $id)
    {
        $acuerdoservicio = \App\AcuerdoServicio::find($id);
        $acuerdoservicio->fill($request->all());
        $acuerdoservicio->save();

        return redirect('/acuerdoservicio');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\AcuerdoServicio::destroy($id);
        return redirect('/acuerdoservicio');
    }
}
