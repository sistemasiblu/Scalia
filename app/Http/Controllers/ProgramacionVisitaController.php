<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class ProgramacionVisitaController extends Controller
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
            return view('programacionvisitagrid', compact('datos'));
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
        return view('programacionvisita');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\ProgramacionVisita::create([
            'tipoDocumentoVisitanteProgramacionVisita' => $request['tipoDocumentoVisitanteProgramacionVisita'],
            'numeroDocumentoVisitanteProgramacionVisita' => $request['numeroDocumentoVisitanteProgramacionVisita'],
            'nombreVisitanteProgramacionVisita' => $request['nombreVisitanteProgramacionVisita'],
            'apellidoVisitanteProgramacionVisita' => $request['apellidoVisitanteProgramacionVisita'],
            'nombreResponsableProgramacionVisita' => $request['nombreResponsableProgramacionVisita'],
            'Tercero_idResponsable' => ($request['Tercero_idResponsable'] == '' ? NULL : $request['Tercero_idResponsable']),
            'dependenciaProgramacionVisita' => $request['dependenciaProgramacionVisita'],
            'fechaIngresoProgramacionVisita' => $request['fechaIngresoProgramacionVisita'],
            'tiempoEstimadoProgramacionVisita' => $request['tiempoEstimadoProgramacionVisita'],
            'detalleProgramacionVisita' => $request['detalleProgramacionVisita'],
        ]);

        $programacionvisita = \App\ProgramacionVisita::All()->last();
       
        return redirect('programacionvisita');
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
        $programacionvisita = \App\ProgramacionVisita::find($id);
        return view ('programacionvisita',['programacionvisita'=>$programacionvisita]);
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
        $programacionvisita = \App\ProgramacionVisita::find($id);
        $programacionvisita->fill($request->all());
        $programacionvisita->save();

        return redirect('programacionvisita');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\ProgramacionVisita::destroy($id);
        return redirect('programacionvisita');
    }
}
