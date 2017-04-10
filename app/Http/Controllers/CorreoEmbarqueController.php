<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class CorreoEmbarqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);

        if($datos != null)
            return view('correoembarquegrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('correoembarque');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\CorreoEmbarque::create([
        'tipoCorreoEmbarque' => $request['tipoCorreoEmbarque'],
        'destinatarioCorreoEmbarque' => $request['destinatarioCorreoEmbarque'],
        'copiaCorreoEmbarque' => $request['copiaCorreoEmbarque'],
        'asuntoCorreoEmbarque' => $request['asuntoCorreoEmbarque'],
        'mensajeCorreoEmbarque' => $request['mensajeCorreoEmbarque'],
        ]);

        return redirect('/correoembarque');
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
        $correoembarque = \App\CorreoEmbarque::find($id);
        return view ('correoembarque',['correoembarque'=>$correoembarque]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $correoembarque = \App\CorreoEmbarque::find($id);
        $correoembarque->fill($request->all());
        $correoembarque->save();

        return redirect('/correoembarque');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\CorreoEmbarque::destroy($id);
        return redirect('/correoembarque');
    }
}
