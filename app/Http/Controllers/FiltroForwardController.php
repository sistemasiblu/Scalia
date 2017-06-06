<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class FiltroForwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $forward = DB::Select(
            "SELECT numeroForward as nombre, idForward as id
            FROM forward
            ORDER BY numeroForward");
        $forward = $this->convertirArray($forward);

        return view('filtroforward', 
            compact( 'forward'));
    }

    function convertirArray($dato)
    {
        $nuevo = array();
        $nuevo[0] = 'Todos';
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

    function consultarForward()
    {
        $where = (isset($_GET["condicion"]) and $_GET["condicion"] != '') ? 'WHERE '.$_GET["condicion"] : '';

        $consulta = DB::Select("
            SELECT 
                f.numeroForward as numeroForward,
                f.fechaNegociacionForward,
                f.modalidadForward,
                nombre1Tercero,
                f.fechaVencimientoForward,
                f.valorDolarForward,
                f.tasaForward,
                f.tasaInicialForward,
                f.valorPesosForward,
                f.devaluacionForward,
                f.spotForward,
                f.ForwardPadre_idForwardPadre,
                fp.numeroForward as numeroForwardPadre
            FROM
                forward f
                    LEFT JOIN
                Iblu.Tercero t ON f.Tercero_idBanco = t.idTercero
                    LEFT JOIN
                forward fp ON f.ForwardPadre_idForwardPadre = fp.idForward
            $where");

        return view('formatos.impresionForwardInforme',compact('consulta'));
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
        //
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
        //
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
