<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\VentaEDIRequest;
use App\Http\Controllers\Controller;

//use Intervention\Image\ImageManagerStatic as Image;
use Input;
use File;
use Validator;
use Response;
use DB;
use Config;
use Excel;
include public_path().'/ajax/consultarPermisos.php';
// include composer autoload
//require '../vendor/autoload.php';
// import the Intervention Image Manager Class
use Intervention\Image\ImageManager ;

class VentaEDIController extends Controller
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

        return view('ventaedigrid', compact('datos'));
    }

    public function indexdropzone() 
    {
        return view('dropzone');
    }

    //Funcion para subir archivos con dropzone
    public function uploadFiles(Request $request) 
    {
 
        $input = Input::all();
 
        $rules = array(
        );
 
        $validation = Validator::make($input, $rules);
 
        if ($validation->fails()) {
            return Response::make($validation->errors->first(), 400);
        }
        
        $destinationPath = public_path() . '/imagenes/repositorio/temporal'; //Guardo en la carpeta  temporal

        $extension = Input::file('file')->getClientOriginalExtension(); 
        $fileName = Input::file('file')->getClientOriginalName(); // nombre de archivo
        $upload_success = Input::file('file')->move($destinationPath, $fileName);
 
        if ($upload_success) {
            return Response::json('success', 200);
        } 
        else {
            return Response::json('error', 400);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        \App\VentaEDI::destroy($id);
        return redirect('/ventaedi');
    }

    public function importarVentaEDIExcel()
    {
        set_time_limit(0);
        $destinationPath = public_path() . '/imagenes/repositorio/temporal'; 
        Excel::load($destinationPath.'/Plantilla VentaEDI.xlsx', function($reader) {

            $datos = $reader->getActiveSheet();
            
            $idSistemaInformacion = 1;

            $conexBD = DB::table('sistemainformacion')
            ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
            ->where('idSistemaInformacion', "=", $idSistemaInformacion)
            ->get();


            $conexBD = get_object_vars($conexBD[0]);
            // print_r($conexBD);
           
            Config::set( 'database.connections.'.$conexBD['bdSistemaInformacion'], array 
            ( 
                'driver'     =>  $conexBD['motorbdSistemaInformacion'], 
                'host'       =>  $conexBD['ipSistemaInformacion'], 
                'port'       =>  $conexBD['puertoSistemaInformacion'], 
                'database'   =>  $conexBD['bdSistemaInformacion'], 
                'username'   =>  $conexBD['usuarioSistemaInformacion'], 
                'password'   =>  $conexBD['claveSistemaInformacion'], 
                'charset'    =>  'utf8', 
                'collation'  =>  'utf8_unicode_ci', 
                'prefix'     =>  ''
            )); 

            $conexion = DB::connection($conexBD['bdSistemaInformacion'])->getDatabaseName();
            
            $datos = $reader->getActiveSheet();
            
            $ventaedi = array();
            $ventaediDet = array();
            $errores = array();
            $fila = 11;
            $posDet = 0;
            $posErr = 0;
            
            
            $ventaedi[0]["Tercero_idCliente"] = $datos->getCellByColumnAndRow(1, 11)->getValue();
            $ventaedi[0]["CLIENTE"] = $datos->getCellByColumnAndRow(3, 11)->getValue();

            //*****************************
            // Cliente
            //*****************************
            // Consultamos el ID en la base de datos, si no se encuentra reportamos el error,
            // si se encuentra, lo asignamos a la posicion del array
            $consulta = DB::connection($conexBD['bdSistemaInformacion'])->select(
                 "Select idTercero FROM Iblu.Tercero Where codigoBarrasTercero = '". trim($ventaedi[0]["Tercero_idCliente"])."'");
            $valorID = (count($consulta) > 0 ? get_object_vars($consulta[0])["idTercero"] : 0);

            if($valorID == 0)
            {
                $errores[$posErr]["linea"] = $fila;
                $errores[$posErr]["nombre"] = $ventaedi[0]["CLIENTE"];
                $errores[$posErr]["mensaje"] = 'El código EAN '.$ventaedi[0]["Tercero_idCliente"].' del cliente no Existe';
                
                $posErr++;
            }
            else
            {
                $ventaedi[0]["Tercero_idCliente"] = $valorID;
            }

            $fecha = $datos->getCellByColumnAndRow(7, 11)->getValue();
            $ventaedi[0]["fechaInicialVentaEDI"] = substr($fecha,6,4) .'-'. substr($fecha,3,2) .'-'. substr($fecha,0,2);
            $fecha = $datos->getCellByColumnAndRow(9, 11)->getValue();
            $ventaedi[0]["fechaFinalVentaEDI"] =  substr($fecha,6,4) .'-'. substr($fecha,3,2) .'-'. substr($fecha,0,2);;

            $consulta = DB::select(
                 "Select idVentaEDI 
                 FROM ventaedi 
                 Where  fechaInicialVentaEDI = '". trim($ventaedi[0]["fechaInicialVentaEDI"])."' and 
                        fechaFinalVentaEDI = '". trim($ventaedi[0]["fechaFinalVentaEDI"])."' and 
                        Tercero_idCliente = '". trim($ventaedi[0]["Tercero_idCliente"])."'");

            $ventaedi[0]["idVentaEDI"] = (count($consulta) > 0 ? get_object_vars($consulta[0])["idVentaEDI"] : 0);
// echo $ventaedi[0]["idVentaEDI"];

            
            
            while ($datos->getCellByColumnAndRow(1, $fila)->getValue() != '' and
                    $datos->getCellByColumnAndRow(1, $fila)->getValue() != NULL) {
                

                // para cada registro de ventaediDet recorremos las columnas 
                // desde la 12 hasta la 17 (producto, cantidad y precios)
                
                for ($columna = 12; $columna <= 17; $columna++) {
                    // en la fila 10 del archivo de excel estan los nombres de los campos de la tabla, les reemplazamos los espacions por underline
                    $campo = str_replace(' ','_',$datos->getCellByColumnAndRow($columna, 10)->getValue());

                    // si es una celda calculada, la ejecutamos, sino tomamos su valor
                    if ($datos->getCellByColumnAndRow($columna, $fila)->getDataType() == 'f')
                        $ventaediDet[$posDet][$campo] = $datos->getCellByColumnAndRow($columna, $fila)->getCalculatedValue();
                    else
                    {
                        $ventaediDet[$posDet][$campo] = 
                            ($datos->getCellByColumnAndRow($columna, $fila)->getValue() == null 
                                ? ''
                                : $datos->getCellByColumnAndRow($columna, $fila)->getValue());
                    }

                }

                


                // //*****************************
                // // Producto
                // //*****************************
                // // Consultamos el ID en la base de datos, si no se encuentra reportamos el error,
                // // si se encuentra, lo asignamos a la posicion del array
                // $consulta = DB::connection($conexBD['bdSistemaInformacion'])->select(
                //     "Select idProducto FROM Iblu.Producto Where codigoBarrasProducto = '". trim($ventaediDet[$posDet]["GTIN_PRODUCTO"])."'");
                // $valorID = (count($consulta) > 0 ? get_object_vars($consulta[0])["idProducto"] : 0);

                // if($valorID == 0)
                // {
                //     $errores[$posErr]["linea"] = $fila;
                //     $errores[$posErr]["nombre"] = $ventaediDet[$posDet]["PRODUCTO"];
                //     $errores[$posErr]["mensaje"] = 'El código EAN '.$ventaediDet[$posDet]["GTIN_PRODUCTO"].' del producto no Existe';
                    
                //     $posErr++;
                // }
                // else
                // {
                //     $ventaediDet[$posDet]["GTIN_PRODUCTO"] = $valorID;
                // }
                


                $posDet++;
                $fila++;
                
            }
            
            

            $totalErrores = count($errores);

            // if($totalErrores > 0)
            // {
            //     $mensaje = '<table cellspacing="0" cellpadding="1" style="width:100%;">'.
            //             '<tr>'.
            //                 '<td colspan="3">'.
            //                     '<h3>Informe de inconsistencias en Importacion de Ventas EDI</h3>'.
            //                 '</td>'.
            //             '</tr>'.
            //             '<tr>'.
            //                 '<td >No. Línea</td>'.
            //                 '<td >Nombre</td>'.
            //                 '<td >Mensaje</td>'.
            //             '</tr>';

            //     for($regErr = 0; $regErr < $totalErrores; $regErr++)
            //     {
            //          $mensaje .= '<tr>'.
            //                     '<td >'.$errores[$regErr]["linea"].'</td>'.
            //                     '<td >'.$errores[$regErr]["nombre"].'</td>'.
            //                     '<td >'.$errores[$regErr]["mensaje"].'</td>'.
            //                 '</tr>';
            //     }
            //     $mensaje .= '</table>';
            //     echo json_encode(array(false, $mensaje));
            // }
            // else
            {

                $indice = array(
                          'idVentaEDI' => $ventaedi[0]["idVentaEDI"]);

                $data = array(
                    'Tercero_idCliente' => $ventaedi[0]['Tercero_idCliente'], 
                    'nombreClienteVentaEDI' => $ventaedi[0]['CLIENTE'], 
                    'fechaInicialVentaEDI' => $ventaedi[0]['fechaInicialVentaEDI'], 
                    'fechaFinalVentaEDI' => $ventaedi[0]['fechaFinalVentaEDI'],
                    'Compania_idCompania' => \Session::get("idCompania")
                );

                $ventaedi = \App\VentaEDI::updateOrCreate($indice, $data);
                $ultimoID = \App\VentaEDI::All()->last();
                $id = $ventaedi[0]["idVentaEDI"] != 0 ? $ventaedi[0]["idVentaEDI"] : $ultimoID->idVentaEDI;

                // recorremos el array recibido para insertar o actualizar cada registro
                for($reg = 0; $reg < count($ventaediDet); $reg++)
                {
                    $indice = array(
                          'idVentaEDIDetalle' => 0);

                    $data = array(
                        'VentaEDI_idVentaEDI' => $id, 
                        'eanProductoVentaEDI'  => $ventaediDet[$reg]['GTIN_PRODUCTO'], 
                        'cantidadVentaEDIDetalle' => $ventaediDet[$reg]['CANTIDAD'], 
                        'precio1VentaEDIDetalle' => $ventaediDet[$reg]['PRECIO_PONDERADO'],
                        'precio2VentaEDIDetalle' => $ventaediDet[$reg]['VALOR_TOTAL_DE_VENTA']
                    );

                    $ventaedi = \App\VentaEDIDetalle::updateOrCreate($indice, $data);
                    
                }
                echo json_encode(array(true, 'Importacion Exitosa, por favor verifique'));
            }



        });
        unlink ( $destinationPath.'/Plantilla VentaEDI.xlsx');
        
    }


}