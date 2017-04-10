<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ListaRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class ListaController extends Controller
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
            return view('listagrid', compact('datos'));
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
    	$sistemainformacion = \App\SistemaInformacion::All()->lists('nombreSistemaInformacion','idSistemaInformacion');
        return view('lista',compact('sistemainformacion'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ListaRequest $request)
    {
        \App\Lista::create([
        'codigoLista' => $request['codigoLista'],
        'nombreLista' => $request['nombreLista'],
        ]);

        $lista = \App\Lista::All()->last();
        $contadorSubLista = count($request['codigoSubLista']);
        for($i = 0; $i < $contadorSubLista; $i++)
        {
            \App\SubLista::create([
            'Lista_idLista' => $lista->idLista,
            'codigoSubLista' => $request['codigoSubLista'][$i],
            'nombreSubLista' => $request['nombreSubLista'][$i],
            'dato1SubLista' => $request['dato1SubLista'][$i],
            'dato2SubLista' => $request['dato2SubLista'][$i],
            'dato3SubLista' => $request['dato3SubLista'][$i],
            ]);
        }

        return redirect('/lista');
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
        $lista = \App\Lista::find($id);
        $sistemainformacion = \App\SistemaInformacion::All()->lists('nombreSistemaInformacion','idSistemaInformacion');
        return view ('lista',compact('sistemainformacion'),['lista'=>$lista]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ListaRequest $request, $id)
    {
        $lista = \App\Lista::find($id);
        $lista->fill($request->all());
        $lista->save();

        $idsEliminar = explode(',', $request['eliminarSubLista']);
        \App\SubLista::whereIn('idSubLista',$idsEliminar)->delete();
        $contadorSubLista = count($request['codigoSubLista']);
        for($i = 0; $i < $contadorSubLista; $i++)
        {
            $index = array(
                'idSubLista' => $request['idSubLista'][$i]);

            $data= array(
                'Lista_idLista' => $id,
                'codigoSubLista' => $request['codigoSubLista'][$i],
                'nombreSubLista' => $request['nombreSubLista'][$i],
                'dato1SubLista' => $request['dato1SubLista'][$i],
                'dato2SubLista' => $request['dato2SubLista'][$i],
                'dato3SubLista' => $request['dato3SubLista'][$i]);

            $save = \App\SubLista::updateOrCreate($index, $data);
        }


        return redirect('/lista');    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Lista::destroy($id);
        return redirect('/lista');
    }
}
