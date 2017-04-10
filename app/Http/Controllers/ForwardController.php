<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\ForwardRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class ForwardController extends Controller
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
            return view('forwardgrid', compact('datos'));
        else
            return view('accesodenegado');
    }

    public function compraforward()
    {
        return view('compraforwardgrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $forwardP = \App\Forward::All()->lists('numeroForward', 'idForward');
        return view('forward',compact('forwardP'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ForwardRequest $request)
    {
        if($request['respuesta'] != 'falso')
        {
            \App\Forward::create([
            'numeroForward' => $request['numeroForward'],
            'descripcionForward' => $request['descripcionForward'],
            'descripcionForward' => $request['descripcionForward'],
            'fechaNegociacionForward' => $request['fechaNegociacionForward'],
            'fechaVencimientoForward' => $request['fechaVencimientoForward'],
            'modalidadForward' => $request['modalidadForward'],
            'valorDolarForward' => $request['valorDolarForward'],
            'tasaForward' => $request['tasaForward'],
            'tasaInicialForward' => $request['tasaInicialForward'],
            'valorPesosForward' => $request['valorPesosForward'],
            'bancoForward' => $request['bancoForward'],
            'Tercero_idBanco' => $request['Tercero_idBanco'],
            'rangeForward' => $request['rangeForward'],
            'devaluacionForwad' => $request['devaluacionForwad'],
            'spotForward' => $request['spotForward'],
            'estadoForward' => $request['estadoForward'],
            'ForwardPadre_idForwardPadre' => ($request['ForwardPadre_idForwardPadre'] == '' or $request['ForwardPadre_idForwardPadre'] == 0) ? null : $request['ForwardPadre_idForwardPadre']
            ]);

            $forward = \App\Forward::All()->last();
            for ($i=0; $i < count($request['valorForwardDetalle']); $i++) 
            { 
                \App\ForwardDetalle::create([
                'Forward_idForward' => $forward->idForward,
                'Temporada_idTemporada' => ($request['Temporada_idTemporada'][$i] == '' or $request['Temporada_idTemporada'][$i] == 0) ? null : $request['Temporada_idTemporada'][$i],
                'nombreTemporadaForwardDetalle' => $request['nombreTemporadaForwardDetalle'][$i],
                'Compra_idCompra' => ($request['Compra_idCompra'][$i] == '' or $request['Compra_idCompra'][$i] == 0) ? null : $request['Compra_idCompra'][$i],
                'numeroCompraForwardDetalle' => $request['numeroCompraForwardDetalle'][$i],
                'valorForwardDetalle' => $request['valorForwardDetalle'][$i],
                'valorRealForwardDetalle' => $request['valorRealForwardDetalle'][$i],
                ]);
            }

            return redirect('/forward');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $forward = DB::Select('
        SELECT 
            *
        FROM
            forward f
                LEFT JOIN
            forwarddetalle fd ON f.idForward = fd.Forward_idForward
        WHERE
            idForward = '.$id);

        $forwardp = DB::Select('
        SELECT 
            fp.numeroForward
        FROM
            forward f
                LEFT JOIN
            forward fp ON f.ForwardPadre_idForwardPadre = fp.idForward
        WHERE
            f.idForward = '.$id);

        $forwarddetalle = DB::Select('SELECT * from forwarddetalle where Forward_idForward = '.$id);

        return view('formatos.impresionForward',compact('forward','forwardp','forwarddetalle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $forward = \App\Forward::find($id);
        $forwardP = \App\Forward::All()->lists('numeroForward', 'idForward');
        return view('forward',compact('forwardP'), ['forward' => $forward]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ForwardRequest $request, $id)
    {
        if($request['respuesta'] != 'falso')
        {
            $forward = \App\Forward::find($id);
            $forward->fill($request->all());
            $forward->ForwardPadre_idForwardPadre = ($request['ForwardPadre_idForwardPadre'] == '' or $request['ForwardPadre_idForwardPadre'] == 0) ? null : $request['ForwardPadre_idForwardPadre'];
            $forward->save();

            $idsEliminar = explode(',', $request['eliminarForwardDetalle']);
            \App\ForwardDetalle::whereIn('idForwardDetalle',$idsEliminar)->delete();
            for($i = 0; $i < count($request['valorForwardDetalle']); $i++)
            {
                $indice = array(
                    'idForwardDetalle' => $request['idForwardDetalle'][$i]);

                $datos= array(
                'Forward_idForward' => $forward->idForward,
                'Temporada_idTemporada' => ($request['Temporada_idTemporada'][$i] == '' or $request['Temporada_idTemporada'][$i] == 0) ? null : $request['Temporada_idTemporada'][$i],
                'nombreTemporadaForwardDetalle' => $request['nombreTemporadaForwardDetalle'][$i],
                'Compra_idCompra' => ($request['Compra_idCompra'][$i] == '' or $request['Compra_idCompra'][$i] == 0) ? null : $request['Compra_idCompra'][$i],
                'numeroCompraForwardDetalle' => $request['numeroCompraForwardDetalle'][$i],
                'valorForwardDetalle' => $request['valorForwardDetalle'][$i],
                'valorRealForwardDetalle' => $request['valorRealForwardDetalle'][$i]);

                $guardar = \App\ForwardDetalle::updateOrCreate($indice, $datos);
            }

            return redirect('/forward');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Forward::destroy($id);
        return redirect('/forward');
    }
}
