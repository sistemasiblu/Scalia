<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\MarcaRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Usuario;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);

        if($datos != null)
            return view('marcagrid', compact('datos'));
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
        return view('marca');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(MarcaRequest $request)
    {
        \App\Marca::create([
            'codigoMarca' => $request['codigoMarca'],
            'nombreMarca' => $request['nombreMarca'],
            ]);

        return redirect('/marca');
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
        $marca = \App\Marca::find($id);
        
        return view('marca',['marca'=>$marca]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,MarcaRequest $request)
    {
        $marca = \App\Marca::find($id);
        
        $marca->fill($request->all());
        $marca->save();
        
        return redirect('/marca');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

    public function destroy($id)
    {
        \App\Marca::destroy($id);
        return redirect('/marca');
    }
}
