<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CompaniaRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Usuario;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class CompaniaController extends Controller
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
            return view('companiagrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    public function indexCompaniaGrid()
    {
        return view ('companiagridselect');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('compania');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(CompaniaRequest $request)
    {
        \App\Compania::create([
            'codigoCompania' => $request['codigoCompania'],
            'nombreCompania' => $request['nombreCompania'],
            'directorioCompania' => $request['directorioCompania']
            ]);

        return redirect('/compania');
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
        $compania = \App\Compania::find($id);
        
        return view('compania',['compania'=>$compania]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,CompaniaRequest $request)
    {
        $compania = \App\Compania::find($id);
        
        $compania->fill($request->all());
        $compania->save();
        
        return redirect('/compania');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

    public function destroy($id)
    {
        \App\Compania::destroy($id);
        return redirect('/compania');
    }
}
