<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Opcion;
use App\Http\Requests;
use App\Http\Requests\OpcionRequest;
use App\Http\Controllers\Controller;
//use Intervention\Image\ImageManagerStatic as Image;
use Input;
use File;
// include composer autoload
require '../vendor/autoload.php';
// import the Intervention Image Manager Class
use Intervention\Image\ImageManager ;
use DB;
include public_path().'/ajax/consultarPermisos.php';



class OpcionController extends Controller
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
            return view('opciongrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    public function select()
    {
        $opcion = \App\Opcion::All();
        return view('OpcionGridselect',compact('opcion'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $paquete = \App\Paquete::All()->lists('nombrePaquete','idPaquete');
        return view('opcion',compact('paquete','selected'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(OpcionRequest $request)
    {
        
        if(null !== Input::file('iconoOpcion') )
        {
            $image = Input::file('iconoOpcion');
            $imageName = 'menu/'.$request->file('iconoOpcion')->getClientOriginalName();
            
            $manager = new ImageManager();
            $manager->make($image->getRealPath())->heighten(48)->save('imagenes/'. $imageName);
            //$manager->make($image->getRealPath())->widen(48)->save('images/'. $imageName);
            //$manager->make($image->getRealPath())->resize(48,48)->save('images/'. $imageName);
        }
        else
        {
            $imageName = "";
        }
        \App\Opcion::create([
            'ordenOpcion' => $request['ordenOpcion'],
            'nombreOpcion' => $request['nombreOpcion'],
            'nombreCortoOpcion' => $request['nombreCortoOpcion'],
            'rutaOpcion' => $request['rutaOpcion'],
            'Paquete_idPaquete' => $request['Paquete_idPaquete'],
            'iconoOpcion' =>  $imageName
            ]); 
        return redirect('/opcion');
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
        $opcion = \App\Opcion::find($id);
        $paquete = \App\Paquete::All()->lists('nombrePaquete','idPaquete');
        return view('opcion',compact('paquete'),['opcion'=>$opcion]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,OpcionRequest $request)
    {
        
        $opcion = \App\Opcion::find($id);
        $opcion->fill($request->all());

        if(null !== Input::file('iconoOpcion') ){
            $image = Input::file('iconoOpcion');
            $imageName = $request->file('iconoOpcion')->getClientOriginalName();

            $manager = new ImageManager();
            $manager->make($image->getRealPath())->heighten(48)->save('imagenes/menu/'. $imageName);

            $opcion->iconoOpcion = 'menu/'. $imageName;
        }

       
        $opcion->save();

        return redirect('/opcion');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

    public function destroy($id)
    {

        if(isset($id)) {
            $opcion = \App\Opcion::find($id);
            if($opcion) {
                //Todo::find($id)->delete();
                \App\Opcion::destroy($id); 
                
                File::Delete( 'imagenes/' . $opcion->iconoOpcion );
                
            }
        }

        return redirect('/opcion');
    }
}
