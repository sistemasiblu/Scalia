<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class VentaEDIEstimadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marca = DB::select("SELECT idMarca AS id, nombreMarca AS nombre FROM Iblu.Marca ORDER BY nombreMarca");
        $marca = $this->convertirArray($marca);

        $tipoproducto = DB::select("SELECT idTipoProducto AS id, nombreTipoProducto AS nombre FROM Iblu.TipoProducto ORDER BY nombreTipoProducto");
        $tipoproducto = $this->convertirArray($tipoproducto);

        $tiponegocio = DB::select("SELECT idTipoNegocio AS id, nombreTipoNegocio AS nombre FROM Iblu.TipoNegocio ORDER BY nombreTipoNegocio");
        $tiponegocio = $this->convertirArray($tiponegocio);

        $temporada = DB::select("SELECT idTemporada AS id, nombreTemporada AS nombre FROM Iblu.Temporada ORDER BY nombreTemporada");
        $temporada = $this->convertirArray($temporada);

        $tercero = DB::select("SELECT idTercero AS id, CONCAT(nombre1Tercero, ' ', nombre2Tercero) AS nombre FROM Iblu.Tercero WHERE tipoTercero LIKE '%*01*%' ORDER BY nombre1Tercero, nombre2Tercero");
        $tercero = $this->convertirArray($tercero);

        $categoria = DB::select("SELECT idCategoria AS id, nombreCategoria AS nombre FROM Iblu.Categoria ORDER BY nombreCategoria");
        $categoria = $this->convertirArray($categoria);

        $esquemaproducto = DB::select("SELECT idEsquemaProducto AS id, nombreEsquemaProducto AS nombre FROM Iblu.EsquemaProducto ORDER BY nombreEsquemaProducto");
        $esquemaproducto = $this->convertirArray($esquemaproducto);

        return view('VentaEDIEstimado',compact('marca','tipoproducto','tiponegocio','temporada','tercero','categoria','esquemaproducto'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contadorDetalle = count($request['Producto_idProducto']);
            
        for($i = 0; $i < $contadorDetalle; $i++)
        {
            $indice = array(
             'Producto_idProducto' => $request['Producto_idProducto'][$i]);

             $data = array(
             'diasVentaEDIEstimado' => $request['diasVentaEDIEstimado'][$i],
             'fechaInicioVentaEDIEstimado' =>  $request['fechaInicioVentaEDIEstimado'][$i]);

            $respuesta = \App\VentaEDIEstimado::updateOrCreate($indice, $data);
        }

        return redirect('/ventaediestimado');
    }

    function convertirArray($dato)
    {
        $nuevo = array();
        // $nuevo[0] = 'Todos';
        for($i = 0; $i < count($dato); $i++) 
        {
          $nuevo[get_object_vars($dato[$i])["id"]] = get_object_vars($dato[$i])["nombre"] ;
        }
        return $nuevo;
    }

}
