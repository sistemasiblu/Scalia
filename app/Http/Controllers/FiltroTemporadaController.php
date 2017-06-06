<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class FiltroTemporadaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $temporada = DB::Select(
            "SELECT nombreTemporadaCompra as nombre, Temporada_idTemporada as id
            FROM compra
            WHERE Temporada_idTemporada IS NOT NULL
            ORDER BY nombreTemporadaCompra");
        $temporada = $this->convertirArray($temporada);

        return view('filtrotemporada', 
            compact( 'temporada'));
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

    function consultarTemporada()
    {
        $where = (isset($_GET["condicion"]) and $_GET["condicion"] != '') ? 'WHERE '.$_GET["condicion"] : '';

        $consulta = DB::Select("
            SELECT 
                idTemporada,
                nombreTemporada,
                SUM(valorCompra) AS valorCompra,
                fechaInicialTemporada,
                fechaFinaltemporada
            FROM
                Iblu.Temporada temp
                    INNER JOIN
                (SELECT 
                    Temporada_idTemporada,
                        numeroCompra,
                        valorCompra,
                        idCompra,
                        numeroVersionCompra
                FROM
                    (SELECT 
                    Temporada_idTemporada,
                        numeroCompra,
                        valorCompra,
                        idCompra,
                        numeroVersionCompra
                FROM
                    compra c
                GROUP BY numeroCompra , numeroVersionCompra
                ORDER BY numeroCompra , numeroVersionCompra DESC) AS temp
                GROUP BY numeroCompra) AS comp ON temp.idTemporada = comp.Temporada_idTemporada
            $where
            GROUP BY idTemporada");

        return view('formatos.impresionTemporada',compact('consulta'));
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
