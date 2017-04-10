<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Paquete;
use App\Http\Requests;
use App\Http\Requests\PaqueteRequest;
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



class PaqueteController extends Controller
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
            return view('paquetegrid', compact('datos'));
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
        return view('paquete');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(PaqueteRequest $request)
    {
       
        if(null !== Input::file('iconoPaquete') )
        {
            $image = Input::file('iconoPaquete');
            $imageName = 'menu/'. $request->file('iconoPaquete')->getClientOriginalName();
        
            $manager = new ImageManager();
            $manager->make($image->getRealPath())->heighten(48)->save('imagenes/'. $imageName);
            //$manager->make($image->getRealPath())->widen(48)->save('images/'. $imageName);
            //$manager->make($image->getRealPath())->resize(48,48)->save('images/'. $imageName);
        }
        else
        {
            $imageName = "";
        }
        \App\Paquete::create([
            'ordenPaquete' => $request['ordenPaquete'],
            'nombrePaquete' => $request['nombrePaquete'],
            'iconoPaquete' => $imageName
            ]); 
        return redirect('/paquete');
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
        $paquete = \App\Paquete::find($id);
        return view('paquete',['paquete'=>$paquete]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id,PaqueteRequest $request)
    {
        
        $paquete = \App\Paquete::find($id);
        $paquete->fill($request->all());

        if(null !== Input::file('iconoPaquete') ){
            $image = Input::file('iconoPaquete');
            $imageName = $request->file('iconoPaquete')->getClientOriginalName();

            $manager = new ImageManager();
            $manager->make($image->getRealPath())->heighten(48)->save('imagenes/menu/'. $imageName);

            $paquete->iconoPaquete = 'menu/'. $imageName;
        }
       
        $paquete->save();

        return redirect('/paquete');

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
            $paquete = \App\Paquete::find($id);
            if($paquete) {
                \App\Paquete::destroy($id); 
                
                File::Delete( 'images/' . $paquete->iconoPaquete );
                
            }
        }

        return redirect('/paquete');
    }
}
