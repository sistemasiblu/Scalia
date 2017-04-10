<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rol;
use App\Http\Requests;
use App\Http\Requests\RolRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';



class RolController extends Controller
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
            return view('rolgrid', compact('datos'));
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
       
        $idOpcion = \App\Opcion::All()->lists('idOpcion');
        $nombreOpcion = \App\Opcion::All()->lists('nombreOpcion');
        
        return view('rol',compact('idOpcion','nombreOpcion'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(RolRequest $request)
    {
        
        \App\Rol::create([
            'codigoRol' => $request['codigoRol'],
            'nombreRol' => $request['nombreRol']
            ]); 

        $rol = \App\Rol::All()->last();
        $contadorPermiso = count($request['Opcion_idOpcion']);
        

        for($i = 0; $i < $contadorPermiso; $i++)
        {
            \App\RolOpcion::create([
            'Rol_idRol' => $rol->idRol,
            'Opcion_idOpcion' => $request['Opcion_idOpcion'][$i],
            'adicionarRolOpcion' => $request['adicionarRolOpcion'][$i],
            'modificarRolOpcion' => $request['modificarRolOpcion'][$i],
            'eliminarRolOpcion' => $request['eliminarRolOpcion'][$i],
            'consultarRolOpcion' => $request['consultarRolOpcion'][$i]
           ]);
        }

        return redirect('/rol');
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
        $rol = \App\Rol::find($id);
        $idOpcion = \App\Opcion::All()->lists('idOpcion');
        $nombreOpcion = \App\Opcion::All()->lists('nombreOpcion');
        return view('rol',compact('idOpcion','nombreOpcion'),['rol'=>$rol]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,RolRequest $request)
    {
        
        $rol = \App\Rol::find($id);
        $rol->fill($request->all());

        $rol->save();

        \App\RolOpcion::where('Rol_idRol',$id)->delete();

        $contadorPermiso = count($request['Opcion_idOpcion']);
        for($i = 0; $i < $contadorPermiso; $i++)
        {
            \App\RolOpcion::create([
            'Rol_idRol' => $id,
            'Opcion_idOpcion' => $request['Opcion_idOpcion'][$i],
            'adicionarRolOpcion' => $request['adicionarRolOpcion'][$i],
            'modificarRolOpcion' => $request['modificarRolOpcion'][$i],
            'eliminarRolOpcion' => $request['eliminarRolOpcion'][$i],
            'consultarRolOpcion' => $request['consultarRolOpcion'][$i]
           ]);
        }


        return redirect('/rol');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

    public function destroy($id)
    {

        \App\Rol::destroy($id);
        return redirect('/rol');
    }
}
