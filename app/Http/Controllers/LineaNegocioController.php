<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\LineaNegocioRequest;
use DB;
include public_path().'/ajax/consultarPermisos.php';
use Illuminate\Routing\Route;

class LineaNegocioController extends Controller
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
        $this->lineanegocio = \App\LineaNegocio::find($route->getParameter('lineanegocio'));
        return $this->lineanegocio;
    }

    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);
        
        if($datos != null)
            return view('lineanegociogrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
       return view('lineanegocio',compact('departamento'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(LineaNegocioRequest $request)
    {
        \App\LineaNegocio::create([
            'codigoLineaNegocio' => $request['codigoLineaNegocio'],
            'nombreLineaNegocio' => $request['nombreLineaNegocio'],
            'Compania_idCompania' => \Session::get("idCompania")
            ]);
        return redirect('/lineanegocio');
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
        $lineanegocio = \App\LineaNegocio::find($id);
        return view('lineanegocio',['lineanegocio'=>$lineanegocio]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(LineaNegocioRequest $request, $id)
    {
        $lineanegocio = \App\LineaNegocio::find($id);
        $lineanegocio->fill($request->all());
        $lineanegocio->save();

        return redirect('/lineanegocio');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\LineaNegocio::destroy($id);
        return redirect('/lineanegocio');
    }
}
