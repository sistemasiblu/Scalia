<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
class InformeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate()
    {
        $idInforme = $_POST["idInforme"];
        $idCategoria = $_POST["idCategoria"];

        $idInformeDestino = DB::Insert(
            "INSERT INTO informe
            SELECT 
                null as idInforme, 
                concat('Copia de ', nombreInforme) as nombreInforme, 
                descripcionInforme, 
                vistaPreviaInforme, 
                $idCategoria as CategoriaInforme_idCategoriaInforme 
            FROM 
                informe 
            WHERE idInforme = $idInforme;");

        $idInformeDestino = DB::getPdo()->lastInsertId();

        $respuesta = DB::Insert(
            "INSERT INTO informepropiedad
            SELECT 
                NULL as idInformePropiedad, 
                $idInformeDestino as Informe_idInforme, 
                colorFondoParInformePropiedad, 
                colorFondoImparInformePropiedad, 
                colorBordeParInformePropiedad, 
                colorBordeImparInformePropiedad, 
                colorTextoParInformePropiedad, 
                colorTextoImparInformePropiedad, 
                fuenteTextoParInformePropiedad, 
                fuenteTextoImparInformePropiedad, 
                tamañoTextoParInformePropiedad, 
                tamañoTextoImparInformePropiedad, 
                negrillaParInformePropiedad, 
                negrillaImparInformePropiedad, 
                italicaParInformePropiedad, 
                italicaImparInformePropiedad, 
                subrayadoParInformePropiedad, 
                subrayadoImparInformePropiedad 
            FROM 
                informepropiedad 
            WHERE Informe_idInforme = $idInforme;");

        $respuesta = DB::Insert(
            "INSERT INTO informerol
            SELECT 
                NULL as idInformeRol, 
                $idInformeDestino as Informe_idInforme, 
                Rol_idRol 
            FROM 
                informerol 
            WHERE Informe_idInforme = $idInforme; ");
     
        $idCapaDestino = DB::Insert(
            "INSERT INTO informecapa
            SELECT 
                NULL as idInformeCapa, 
                $idInformeDestino as Informe_idInforme, 
                nombreInformeCapa, 
                tipoInformeCapa, 
                SistemaInformacion_idSistemaInformacion, 
                tablaInformeCapa,
                tituloInformeCapa
            FROM 
                informecapa
            WHERE Informe_idInforme = $idInforme;");

        $idCapaDestino = DB::getPdo()->lastInsertId();

        $respuesta = DB::Insert(
            "INSERT INTO informeconcepto
            SELECT 
                NULL as idInformeConcepto, 
                $idCapaDestino as InformeCapa_idInformeCapa, 
                ordenInformeConcepto, 
                nombreInformeConcepto, 
                nombreNIIFInformeConcepto, 
                tipoMovimientoInformeConcepto, 
                tipoValorInformeConcepto, 
                valorInformeConcepto, 
                valorNIIFInformeConcepto, 
                EstiloInforme_idEstiloInforme, 
                detalleInformeConcepto, 
                resumenInformeConcepto, 
                graficoInformeConcepto, 
                excluirTerceroInformeConcepto 
            FROM 
                informeconcepto
                LEFT JOIN informecapa
                ON informeconcepto.InformeCapa_idInformeCapa = informecapa.idInformeCapa
            WHERE Informe_idInforme = $idInforme;");

        
        $respuesta = DB::Insert(
            "INSERT INTO informeobjeto
            SELECT 
                NULL as idInformeObjeto, 
                $idCapaDestino as InformeCapa_idInformeCapa, 
                bandaInformeObjeto, 
                nombreInformeObjeto, 
                estiloInformeObjeto, 
                EstiloInforme_idEstiloInforme, 
                tipoInformeObjeto, 
                etiquetaInformeObjeto, 
                campoInformeObjeto 
            FROM 
                informeobjeto 
                LEFT JOIN informecapa
                ON InformeCapa_idInformeCapa = informecapa.idInformeCapa
            WHERE Informe_idInforme = $idInforme;");
        echo json_encode(true);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function move()
    {
        $idInforme = $_POST["idInforme"];
        $idCategoria = $_POST["idCategoria"];

        $idInformeDestino = DB::Update(
            "UPDATE informe
            SET CategoriaInforme_idCategoriaInforme = $idCategoria
            WHERE idInforme = $idInforme;");

        echo json_encode(true);
    }
}
