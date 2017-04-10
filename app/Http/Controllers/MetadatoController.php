<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class MetadatoController extends Controller
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
            return view('metadatogrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    public function indexMetadatoGrid()
    {
        return view ('metadatogridselect');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lista = \App\Lista::All()->lists('nombreLista','idLista');

        return view('metadato',compact('lista'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\Metadato::create([
        'tituloMetadato' => $request['tituloMetadato'],
        'tipoMetadato' => $request['tipoMetadato'],
        'Lista_idLista' => $request['Lista_idLista'],
        'opcionMetadato' => $request['opcionMetadato'],
        'longitudMetadato' => $request['longitudMetadato'],
        'valorBaseMetadato' => $request['valorBaseMetadato']
        ]);  

        return redirect('/metadato');
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
        $metadato = \App\Metadato::find($id);
        $lista = \App\Lista::All()->lists('nombreLista','idLista');
        return view('metadato',compact('lista'), ['metadato' => $metadato]);
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
        $metadato = \App\Metadato::find($id);
        $metadato->fill($request->all());
        $metadato->save();    

        return redirect('/metadato');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Metadato::destroy($id);
        return redirect('/metadato');
    }
}
