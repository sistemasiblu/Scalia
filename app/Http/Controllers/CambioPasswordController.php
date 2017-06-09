<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Flash;

use Crypt;
use Hash;
use decrypt;
use password_verify;

use App\Http\Requests\CambioPasswordRequest;


class CambioPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $password = Hash::make('toby2553');
         //echo $us;
         //echo $password;
         $user=\App\User::where('id','=',\Session::get('idUsuario'))->lists('password');
        echo "Password Base datos".$user;
        echo "Password hash".$password;

        
         

        

         //$password = Hash::make('toby2553');
         //echo $us;
         //echo $password;
        if($password=$user)
        {
            echo 'si coinciden';
        }
        else
        echo 'no coinciden';


        return;
        /*if(password_verify('1234567', $crypt_password_string)) 
        {
    // in case if "$crypt_password_string" actually hides "1234567"
        }*/


         return View('cambiopassword');
   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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


    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CambioPasswordRequest $request, $id)
    {

        
        $id=Session::get('idUsuario');
        $usuario = \App\User::find($id);
        $usuario->update(
        [
         'password'=>$request['password'],

        ]);
        $usuario->save();  
        echo "<br><br><center>
        <h1>Se ha Cambiado La Contraseña Exitosamente</h1>";
        ?>
        <script>
        setTimeout("location.href='http://190.248.133.146:8000/scalia'",1000)
        </script> 
        <?php
        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
