<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\UsersRequest;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CompaniaController;
// Indicamos que usamos el Modelo User.
use App\User;
// Hash de contraseñas.
use Hash;
 
// Redireccionamientos.
use Redirect;

use DB;
include public_path().'/ajax/consultarPermisos.php';

class UsersController extends Controller
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
            return view('usersgrid', compact('datos'));
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
        $tercero = DB::Select(
            "SELECT nombre1Tercero as nombre, idTercero as id
            FROM ".\Session::get("baseDatosCompania").".Tercero
            WHERE tipoTercero like '%*05*%'
            ORDER BY nombre1Tercero");
        $tercero = $this->convertirArray($tercero);

        $compania = \App\Compania::All()->lists('nombreCompania','idCompania');
        $rol = \App\Rol::All()->lists('nombreRol','idRol');
        return view('users',compact('compania','rol','selected','tercero'));
    }

    function convertirArray($dato)
    {
        $nuevo = array();
        $nuevo[0] = 'Seleccione un usuario';
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(UsersRequest $request)
    {
        \App\User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'Compania_idCompania'=> $request['Compania_idCompania'],
            'Rol_idRol'=> $request['Rol_idRol'],
            'Tercero_idAsociado'=> $request['Tercero_idAsociado'],
            ]);
        
        return redirect('/users');
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
        $tercero = DB::Select(
            "SELECT nombre1Tercero as nombre, idTercero as id
            FROM  ".\Session::get("baseDatosCompania").".Tercero
            WHERE tipoTercero like '%*05*%'
            ORDER BY nombre1Tercero");
        $tercero = $this->convertirArray($tercero);

        $usuario = \App\User::find($id);
        $compania = \App\Compania::All()->lists('nombreCompania','idCompania');
        $rol = \App\Rol::All()->lists('nombreRol','idRol');
        return view('users',compact('compania','rol','tercero'),['usuario'=>$usuario]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,UsersRequest $request)
    {
        
        $usuario = \App\User::find($id);
        $usuario->fill($request->all());
        $usuario->save();



        return redirect('/users');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

    public function destroy($id)
    {
        \App\User::destroy($id);
        return redirect('/users');
    }
}
