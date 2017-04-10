<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\PresupuestoRequest;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class PresupuestoController extends Controller
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
            return view('presupuestogrid', compact('datos'));
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
        $documentocrm = \App\DocumentoCRM::All()->lists('nombreDocumentoCRM','idDocumentoCRM');
        $idLineaNegocio = \App\LineaNegocio::All()->lists('idLineaNegocio');
        $nombreLineaNegocio = \App\LineaNegocio::All()->lists('nombreLineaNegocio');
        $idTercero = \App\Tercero::where("tipoTercero","like","%03%")->lists('idTercero');
        $nombreTercero = \App\Tercero::where("tipoTercero","like","%03%")->lists('nombreCompletoTercero');
        return view('presupuesto',compact('documentocrm','idLineaNegocio','nombreLineaNegocio','idTercero','nombreTercero'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PresupuestoRequest $request)
    {
        if($request['respuesta'] != 'falso')
        { 
            \App\Presupuesto::create([
                'fechaInicialPresupuesto' => $request['fechaInicialPresupuesto'],
                'fechaFinalPresupuesto' => $request['fechaFinalPresupuesto'],
                'descripcionPresupuesto' => $request['descripcionPresupuesto'],
                'DocumentoCRM_idDocumentoCRM' => $request['DocumentoCRM_idDocumentoCRM'],
                ]);

            $lineaNegocio = DB::Select('SELECT idLineaNegocio from lineanegocio');
            $presupuesto = \App\Presupuesto::All()->last();
            for ($ven=0; $ven < count($request['Tercero_idVendedor']); $ven++) 
            { 
                for ($lin=0; $lin < count($lineaNegocio); $lin++) 
                { 
                    $idLN = get_object_vars($lineaNegocio[$lin]);
                    
                    \App\PresupuestoDetalle::create([
                    'Tercero_idVendedor' => $request['Tercero_idVendedor'][$ven],
                    'valorLineaNegocio' => $request['LineaNegocio_'.$idLN["idLineaNegocio"].'_'][$ven],
                    'Presupuesto_idPresupuesto' => $presupuesto->idPresupuesto,
                    'LineaNegocio_idLineaNegocio' => $idLN["idLineaNegocio"]
                    ]);
                }
            }
        }

        return redirect('/presupuesto');
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
        $presupuesto = \App\Presupuesto::find($id);
        $documentocrm = \App\DocumentoCRM::All()->lists('nombreDocumentoCRM','idDocumentoCRM');
        $idLineaNegocio = \App\LineaNegocio::All()->lists('idLineaNegocio');
        $nombreLineaNegocio = \App\LineaNegocio::All()->lists('nombreLineaNegocio');
        $idTercero = \App\Tercero::where("tipoTercero","like","%03%")->lists('idTercero');
        $nombreTercero = \App\Tercero::where("tipoTercero","like","%03%")->lists('nombreCompletoTercero');
        return view('presupuesto',compact('documentocrm','idLineaNegocio','nombreLineaNegocio','idTercero','nombreTercero'),['presupuesto'=>$presupuesto]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PresupuestoRequest $request, $id)
    {
        if($request['respuesta'] != 'falso')
        { 
            $presupuesto = \App\Presupuesto::find($id);
            $presupuesto->fill($request->all());
            $presupuesto->save();

            $lineaNegocio = DB::Select('SELECT idLineaNegocio from lineanegocio');
            \App\PresupuestoDetalle::where('Presupuesto_idPresupuesto',$id)->delete();
            for ($ven=0; $ven < count($request['Tercero_idVendedor']); $ven++) 
            { 
                for ($lin=0; $lin < count($lineaNegocio); $lin++) 
                { 
                    $idLN = get_object_vars($lineaNegocio[$lin]);
                    
                    \App\PresupuestoDetalle::create([
                    'Tercero_idVendedor' => $request['Tercero_idVendedor'][$ven],
                    'valorLineaNegocio' => $request['LineaNegocio_'.$idLN["idLineaNegocio"].'_'][$ven],
                    'Presupuesto_idPresupuesto' => $presupuesto->idPresupuesto,
                    'LineaNegocio_idLineaNegocio' => $idLN["idLineaNegocio"]
                    ]);
                }
            }
        }

        return redirect('/presupuesto');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Presupuesto::destroy($id);
        return redirect('/presupuesto');
    }
}
