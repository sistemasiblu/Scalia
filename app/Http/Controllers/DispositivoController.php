<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DispositivoRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Usuario;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class DispositivoController extends Controller
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
            return view('dispositivogrid', compact('datos'));
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
        return view('dispositivo');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(DispositivoRequest $request)
    {
        \App\Dispositivo::create([
            'codigoDispositivo' => $request['codigoDispositivo'],
            'nombreDispositivo' => $request['nombreDispositivo'],
            ]);

        return redirect('/dispositivo');
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
        $dispositivo = \App\Dispositivo::find($id);
        
        return view('dispositivo',['dispositivo'=>$dispositivo]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,DispositivoRequest $request)
    {
        $dispositivo = \App\Dispositivo::find($id);
        
        $dispositivo->fill($request->all());
        $dispositivo->save();
        
        return redirect('/dispositivo');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

    public function destroy($id)
    {
        \App\Dispositivo::destroy($id);
        return redirect('/dispositivo');
    }
}
