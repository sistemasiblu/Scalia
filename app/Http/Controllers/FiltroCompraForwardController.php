<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class FiltroCompraForwardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $compra = DB::Select('
            SELECT 
                idCompra as id,
                numeroCompra as nombre
            FROM
                (SELECT 
                    idCompra,
                    numeroCompra
                FROM
                    (SELECT 
                        idCompra,
                        numeroCompra
                FROM
                    compra c
                GROUP BY numeroCompra , numeroVersionCompra
                ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
                GROUP BY numeroCompra) AS comp');
        $compra = $this->convertirArray($compra);

        $forward = DB::Select('
            SELECT
                idForward as id,
                numeroForward as nombre
            FROM
                forward');
        $forward = $this->convertirArray($forward);

        return view('filtrocompraforward',
            compact('compra','forward'));
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

    public function consultarCompraForward()
    {
        $filtro = $_GET["filtroCompraForward"];

        $visualizacion = $_GET["visualizacionCompraForward"];

        $where = (isset($_GET["condicion"]) and $_GET["condicion"] != '') ? 'WHERE '.$_GET["condicion"] : '';

        if ($filtro == 'forward') 
        {
            $consulta = DB::Select('
            SELECT 
                idForward,
                numeroForward,
                descripcionForward,
                fechaVencimientoForward,
                valorDolarForward,
                numeroCompra,
                nombreProveedorCompra,
                ifnull(valorRealForwardDetalle, valorCompra) as valorCompra,
                ifnull(nombreTemporadaCompra, nombreTemporada) as nombreTemporadaCompra
            FROM
                forward f
                    LEFT JOIN
                forwarddetalle fd ON f.idForward = fd.Forward_idForward
                    LEFT JOIN
                compra c ON c.idCompra = fd.Compra_idCompra
                    LEFT JOIN
                Iblu.Temporada t on fd.Temporada_idTemporada = t.idTemporada '.
            $where.'
             ORDER BY numeroForward');

            return view('formatos.impresionCompraForward',compact('consulta','filtro','visualizacion'));    
        }
        else if($filtro == 'compra')
        {
            $consulta = DB::Select('
            SELECT 
                idForward,
                numeroForward,
                descripcionForward,
                fechaVencimientoForward,
                valorDolarForward,
                idCompra,
                numeroCompra,
                nombreProveedorCompra,
                IFNULL(valorRealForwardDetalle, valorCompra) AS valorCompra,
                nombreTemporadaCompra
            FROM
                (SELECT 
                    idCompra,
                        numeroCompra,
                        nombreProveedorCompra,
                        valorCompra,
                        nombreTemporadaCompra
                FROM
                    (SELECT 
                    idCompra,
                        numeroCompra,
                        nombreProveedorCompra,
                        valorCompra,
                        nombreTemporadaCompra
                FROM
                    compra c
                GROUP BY numeroCompra , numeroVersionCompra
                ORDER BY numeroCompra , numeroVersionCompra DESC) AS c
                GROUP BY numeroCompra) AS comp
                    LEFT JOIN
                forwarddetalle fd ON comp.idCompra = fd.Compra_idCompra
                    LEFT JOIN
                forward f ON fd.Forward_idForward = f.idForward '.
            $where.'
            ORDER BY numeroCompra');

            return view('formatos.impresionCompraForward',compact('consulta','filtro','visualizacion'));
        }
        
        
    }
}
