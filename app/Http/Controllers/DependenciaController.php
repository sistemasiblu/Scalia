<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\DependenciaRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class DependenciaController extends Controller
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
            return view('dependenciagrid', compact('datos'));
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
        $dependenciaP = \App\Dependencia::All()->lists('nombreDependencia', 'idDependencia');
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');

        return view('dependencia',compact('dependenciaP','idRol','nombreRol'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DependenciaRequest $request)
    {
        \App\Dependencia::create([
        'codigoDependencia' => $request['codigoDependencia'],
        'nombreDependencia' => $request['nombreDependencia'],
        'abreviaturaDependencia' => $request['abreviaturaDependencia'],
        'directorioDependencia' => $request['directorioDependencia'],
        'Dependencia_idPadre' => ($request['Dependencia_idPadre'] == '' or $request['Dependencia_idPadre'] == 0) ? null : $request['Dependencia_idPadre'],
        ]);

        $dependencia = \App\Dependencia::All()->last();
        for($i = 0; $i < count($request['Rol_idRol']); $i++)
        {
            \App\DependenciaPermiso::create([
            'Dependencia_idDependencia' => $dependencia->idDependencia,
            'Rol_idRol' => $request['Rol_idRol'][$i],
            ]);
        }

        return redirect('/dependencia');
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
        $dependencia = \App\Dependencia::find($id);
        $dependenciaP = \App\Dependencia::All()->lists('nombreDependencia', 'idDependencia');
        $idRol = \App\Rol::All()->lists('idRol');
        $nombreRol = \App\Rol::All()->lists('nombreRol');
        return view('dependencia',compact('dependenciaP','idRol','nombreRol'), ['dependencia' => $dependencia]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DependenciaRequest $request, $id)
    {
        $dependencia = \App\Dependencia::find($id);
        $dependencia->fill($request->all());
        $dependencia->Dependencia_idPadre = ($request['Dependencia_idPadre'] == '' or $request['Dependencia_idPadre'] == 0) ? null : $request['Dependencia_idPadre'];
        $dependencia->save();

        $idsEliminar = explode(',', $request['eliminarDependenciaPermiso']);
        \App\DependenciaPermiso::whereIn('idDependenciaPermiso',$idsEliminar)->delete();
        for($i = 0; $i < count($request['Rol_idRol']); $i++)
        {
            $indice = array(
                'idDependenciaPermiso' => $request['idDependenciaPermiso'][$i]);

            $datos= array(
                'Dependencia_idDependencia' => $dependencia->idDependencia,
                'Rol_idRol' => $request['Rol_idRol'][$i]
                );

            $guardar = \App\DependenciaPermiso::updateOrCreate($indice, $datos);
        }

        return redirect('/dependencia');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Dependencia::destroy($id);
        return redirect('/dependencia');
    }
}
