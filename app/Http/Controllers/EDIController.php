<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Config;
use Storage;

class EDIController extends Controller
{

    public function filtroRotacionEDI()
    {
            //******************************************
            //
            // CONEXION A LA BASE DE DATOS DE SAYA IBLU
            //
            //******************************************
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

            $marca = DB::connection($conexBD['bdSistemaInformacion'])->select(
                "SELECT idMarca as id, nombreMarca as nombre
                FROM Marca
                ORDER BY nombreMarca");
            $marca = $this->convertirArray($marca);

            $tipoproducto = DB::connection($conexBD['bdSistemaInformacion'])->select(
                "SELECT idTipoProducto as id, nombreTipoProducto as nombre
                FROM TipoProducto
                ORDER BY nombreTipoProducto");
            $tipoproducto = $this->convertirArray($tipoproducto);

            $categoria = DB::connection($conexBD['bdSistemaInformacion'])->select(
                "SELECT codigoAlterno1Categoria as id, nombreCategoria as nombre
                FROM Categoria
                ORDER BY codigoAlterno1Categoria");
            $categoria = $this->convertirArray($categoria);

            $esquema = DB::connection($conexBD['bdSistemaInformacion'])->select(
                "SELECT idEsquemaProducto as id, nombreEsquemaProducto as nombre
                FROM EsquemaProducto
                ORDER BY nombreEsquemaProducto");
            $esquema = $this->convertirArray($esquema);

            $tiponegocio = DB::connection($conexBD['bdSistemaInformacion'])->select(
                "SELECT idTipoNegocio as id, nombreTipoNegocio as nombre
                FROM TipoNegocio
                ORDER BY nombreTipoNegocio");
            $tiponegocio = $this->convertirArray($tiponegocio);

            $temporada = DB::connection($conexBD['bdSistemaInformacion'])->select(
                "SELECT idTemporada as id, nombreTemporada as nombre
                FROM Temporada
                ORDER BY nombreTemporada");
            $temporada = $this->convertirArray($temporada);

            $periodoVenta = DB::select(
                "SELECT idVentaEDI as id, concat(nombreClienteVentaEDI, ' - ', fechaInicialVentaEDI, ' a ', fechaFinalVentaEDI) as nombre
                FROM ventaedi
                ORDER BY nombreClienteVentaEDI, fechaInicialVentaEDI");
            $periodoVenta = $this->convertirArray($periodoVenta);

            $periodoInventario = DB::select(
                "SELECT idInventarioEDI as id, concat(nombreClienteInventarioEDI, ' - ', fechaInicialInventarioEDI, ' a ', fechaFinalInventarioEDI) as nombre
                FROM inventarioedi
                ORDER BY nombreClienteInventarioEDI, fechaInicialInventarioEDI");
            $periodoInventario = $this->convertirArray($periodoInventario);

        return view('RotacionEDIFiltro', 
           compact( 'marca', 'tipoproducto', 'categoria', 'esquema', 'tiponegocio', 'temporada', 'periodoVenta', 'periodoInventario'));        
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function consultaVentaInventario()
    {
        $condicionRot =  $_GET["condicionRot"];
        $condicionDatos =  (isset($_GET["condicionDatos"]) and $_GET["condicionDatos"] != '') 
                            ? ' WHERE '. $_GET["condicionDatos"]
                            : '';
        $grupo =  $_GET["grupo"]; 
                           
        $consulta = DB::select(
            "SELECT 
                nombreMarca as nombreMarca,
                nombreEsquemaProducto as nombreEsquema,
                nombreTipoNegocio as nombreTipoNegocio,
                nombreTipoProducto as nombreTipoProducto,
                nombreTemporada as nombreTemporada,
                nombreLargoProducto as nombreLargoProducto,
                Categoria.codigoAlterno1Categoria as Categoria, 
                Categoria.nombreCategoria as nombreCategoria,
                Linea.codigoAlterno1Categoria as Linea,
                Linea.nombreCategoria as nombreLinea,
                Sublinea.codigoAlterno1Categoria as Sublinea,
                Sublinea.nombreCategoria as nombreSublinea,
                SubCategoria.codigoAlterno1Categoria as Subcategoria,
                SubCategoria.nombreCategoria as nombreSubcategoria,
                Res.codigoAlternoProducto,
                Res.cantidadVenta,
                Res.cantidadInventario,
                Res.precio1Venta,
                Res.precio1Inventario
            FROM
            (
                
                SELECT 
                    $grupo as codigoAlternoProducto,
                    nombreLargoProducto,
                    Categoria_idCategoria,
                    TipoProducto_idTipoProducto,
                    TipoNegocio_idTipoNegocio,
                    Marca_idMarca,
                    EsquemaProducto_idEsquemaProducto,
                    Temporada_idTemporada,
                    SUM(cantidadVentaEDIDetalle) as cantidadVenta,
                    SUM(cantidadInventarioEDIDetalle) as cantidadInventario,
                    SUM(precio1VentaEDIDetalle) as precio1Venta,
                    SUM(precio1InventarioEDIDetalle) as precio1Inventario
                FROM 
                (
                
                    SELECT eanProductoVentaEDI, 
                            cantidadVentaEDIDetalle, cantidadInventarioEDIDetalle, precio1VentaEDIDetalle, precio1InventarioEDIDetalle
                    FROM 
                        scalia.ventaedidetalle 
                        left join scalia.inventarioedidetalle 
                        on  ventaedidetalle.eanProductoVentaEDI = inventarioedidetalle.eanProductoInventarioEDI
                    " . ($condicionRot != '' ? ' WHERE '.$condicionRot : '' ). "
                    UNION

                    SELECT eanProductoInventarioEDI as eanProductoVentaEDI, 
                            cantidadVentaEDIDetalle, cantidadInventarioEDIDetalle, precio1VentaEDIDetalle, precio1InventarioEDIDetalle
                    FROM 
                        scalia.ventaedidetalle 
                        right  join scalia.inventarioedidetalle 
                        on  ventaedidetalle.eanProductoVentaEDI = inventarioedidetalle.eanProductoInventarioEDI
                    Where cantidadVentaEDIDetalle IS NULL " . ($condicionRot != '' ? ' AND '.$condicionRot : '' ). "

                ) Rotacion
                left join Iblu.Producto on Rotacion.eanProductoVentaEDI = Producto.codigoBarrasProducto 
                group by Iblu.Producto.".$grupo."
            ) Res
            left join Iblu.EsquemaProducto  
                on Res.EsquemaProducto_idEsquemaProducto = EsquemaProducto.idEsquemaProducto 
            left join Iblu.TipoProducto 
                on Res.TipoProducto_idTipoProducto = TipoProducto.idTipoProducto 
            left join Iblu.TipoNegocio  
                on Res.TipoNegocio_idTipoNegocio = TipoNegocio.idTipoNegocio 
            left join Iblu.Marca  
                on Res.Marca_idMarca = Marca.idMarca 
            left join Iblu.Temporada  
                on Res.Temporada_idTemporada = Temporada.idTemporada 
            left join Iblu.Categoria Categoria 
                on Res.Categoria_idCategoria = Categoria.idCategoria 
            left join Iblu.Categoria Linea 
                on SUBSTRING(Categoria.codigoAlterno1Categoria, 1, 2) = Linea.codigoAlterno1Categoria
            left join Iblu.Categoria Sublinea 
                on SUBSTRING(Categoria.codigoAlterno1Categoria, 1, 4) =  Sublinea.codigoAlterno1Categoria
            left join Iblu.Categoria SubCategoria 
                on SUBSTRING(Categoria.codigoAlterno1Categoria, 1, 6) =  SubCategoria.codigoAlterno1Categoria
            $condicionDatos
            
            ");
            
        return view('formatos.informeVentaInventarioEDI',compact('consulta'));

    }


    protected function moverArchivo($origen)
    {
        $destino = str_replace('nuevos', 'procesados', $origen);

        // antes de mover el archivo de Nuevos  Procesados, verificamos que NO exista en Procesados
        if(Storage::disk('local')->has($destino))
        {
            Storage::disk('local')->delete($destino);
        }

        // luego de verificar qu eno exista en Procesados, movemos el archivo
        Storage::move($origen, $destino);
    }

    //-------------------------------
    //  Importar Archivo E D I 
    //-------------------------------
    // 1. consultamos en la carpeta correspondiente al tipo de archivo edi
    // los archivos que se han recibido
    // 2. por cada archivo ejecutamos el proceso de interface correspondiente
    //--------------------------
    // Parámetros:
    // $tipo : indica el tipo de documento EDI, asi:
    // ORDERS : Orden de compra
    // INVRPT : Reporte de Inventarios
    // SLSRPT : Reporte de Ventas
    public function importarArchivoEDI($tipo)
    {
        // $idSistemaInformacion = 1;

        // $conexBD = DB::table('sistemainformacion')
        // ->select(DB::raw('ipSistemaInformacion, puertoSistemaInformacion, usuarioSistemaInformacion, claveSistemaInformacion, bdSistemaInformacion, motorbdSistemaInformacion'))
        // ->where('idSistemaInformacion', "=", $idSistemaInformacion)
        // ->get();


        // $conexBD = get_object_vars($conexBD[0]);
        // // print_r($conexBD);
       
        // Config::set( 'database.connections.'.$conexBD['bdSistemaInformacion'], array 
        // ( 
        //     'driver'     =>  $conexBD['motorbdSistemaInformacion'], 
        //     'host'       =>  $conexBD['ipSistemaInformacion'], 
        //     'port'       =>  $conexBD['puertoSistemaInformacion'], 
        //     'database'   =>  $conexBD['bdSistemaInformacion'], 
        //     'username'   =>  $conexBD['usuarioSistemaInformacion'], 
        //     'password'   =>  $conexBD['claveSistemaInformacion'], 
        //     'charset'    =>  'utf8', 
        //     'collation'  =>  'utf8_unicode_ci', 
        //     'prefix'     =>  ''
        // )); 

        // $conexion = DB::connection($conexBD['bdSistemaInformacion']);


        
        switch ($tipo) {
            case 'ORDERS':
                $this->importarOrdenCompraEDI($archivo);
                break;
            
            case 'INVRPT':
                // establecemos el medio de almacenamiento (Local =  storage/app)
                $disco = Storage::disk('local');
                
                // consultamos los archivos en el directorio de inventarios nuevos
                $archivosEdi = Storage::files("/edi/inventario/nuevos");

                // recorremos la lista de archivos encontrados
                foreach ($archivosEdi as $edi)
                {
                    // procesamos el archivo actual
                    $this->importarInventarioEDI('storage/'.$edi);

                    // luego de importarlo, lo movemos a la carpeta de procesados
                    $this->moverArchivo($edi);
                    
                }

                echo 'Fin del proceso';

                break;

            case 'SLSRPT':
                // establecemos el medio de almacenamiento (Local =  storage/app)
                $disco = Storage::disk('local');
                
                // consultamos los archivos en el directorio de ventas nuevas
                $archivosEdi = Storage::files("/edi/venta/nuevos");
                
                // recorremos la lista de archivos encontrados
                foreach ($archivosEdi as $edi)
                {
                    // procesamos el archivo actual
                    $this->importarVentaEDI('storage/'.$edi);

                    // luego de importarlo, lo movemos a la carpeta de procesados
                    $this->moverArchivo($edi);
                }

                echo 'Fin del proceso';
                break;

        }
    }

    //-------------------------------
    //  I n v e n t a r i o   E D I 
    //-------------------------------
    // 1. abrimos el archivo y lo leemos linea por linea hasta el fin de archivo
    // 2. por cada linea verificamos que segmento contiene
    // 3. por cada segmento verificamos su contenido y lo almacenamos en el array de datos que será llevado a la BD
    // 4. ejecutamos el proceso de grabado del array de datos
    public function importarInventarioEDI($archivo)
    {   
        // este proceso puede tardar mas de 30 minutos o 1 hora, dependiendo de la cantidad de productos 
        // y ubicaciones reportadas en el archivo, por eso no podemos limitar el tiempo de ejecución
        set_time_limit(0);

        
        // 2. abrimos el archivo 
        $file = fopen($archivo, "r") or die("No fue posible abrir el archivo!");
        // $contenido = fread($file,filesize($archivo));
        
        $encabezado = array();
        $enc = -1;
        $detalle = array();
        $det = -1;

        // lo recorremos hasta el final del archivo
        while (!feof($file)) 
        {
            // leemos la línea
            $linea = trim(fgets($file));

            // separamos los campos del segmento (separador (+))
            $segmento = explode('+', substr($linea, 0, strlen($linea)-1));

            // verificamos que segmento es (primer dato del registro)
            switch ($segmento[0]) 
            {

                // BGM - M 1 - Beginning of message 
                // BGM+35+2016090503'
                case 'BGM' :
                    
                    $encabezado[$enc]['idInventarioEDI'] = 0;
                    $encabezado[$enc]['numeroInventarioEDI'] = $segmento[2];
                    break;

                // DTM - M 10 - Date/time/period 
                // DTM+137:20160905:102'
                // DTM+206:20160904:102'
                case 'DTM' :
                    $valores = explode(':',$segmento[1]);

                    $encabezado[$enc]['fechaInicialInventarioEDI'] = '';
                    $encabezado[$enc]['fechaFinalInventarioEDI'] = '';

                    switch ($valores[0]) 
                    {
                        // 137 = Fecha y Hora del Documento 
                        // 194 = Fecha de inicio del reporte 
                        // 206 = Fecha de fin del reporte 
                        case '194' :
                            $encabezado[$enc]['fechaInicialInventarioEDI'] = substr($valores[1],0,4).'-'.substr($valores[1],4,2).'-'.substr($valores[1],6,2);
                            break;

                        case '206' :
                            $encabezado[$enc]['fechaFinalInventarioEDI'] = substr($valores[1],0,4).'-'.substr($valores[1],4,2).'-'.substr($valores[1],6,2);
                            break;
                    }

                    if($encabezado[$enc]['fechaInicialInventarioEDI'] == '' and $encabezado[$enc]['fechaFinalInventarioEDI'] != '')
                        $encabezado[$enc]['fechaInicialInventarioEDI'] = $encabezado[$enc]['fechaFinalInventarioEDI'];
                    
                    break;

                // NAD - M 1 - Name and address 
                // NAD+BY+7701001000008::9'
                case 'NAD' :
                    $valores = explode(':',$segmento[2]);

                    switch ($segmento[1]) 
                    {
                        // BY = Comprador 
                        // SU = Proveedor
                        case 'BY' :

                            $consulta = DB::select(
                                     "Select idTercero, nombre1Tercero 
                                     FROM Iblu.Tercero 
                                     Where codigoBarrasTercero = '". trim($valores[0])."'");
                            
                            $encabezado[$enc]['Tercero_idCliente'] = (count($consulta) > 0 ? get_object_vars($consulta[0])["idTercero"] : 0);
                            $encabezado[$enc]['nombreClienteInventarioEDI'] = (count($consulta) > 0 ? get_object_vars($consulta[0])["nombre1Tercero"] : 'Cliente no existe');

                            $consulta = DB::select(
                             "Select idInventarioEDI 
                             FROM inventarioedi 
                             Where  numeroInventarioEDI = '".$encabezado[$enc]['numeroInventarioEDI']."' and
                                    fechaInicialInventarioEDI = '". trim($encabezado[$enc]['fechaInicialInventarioEDI'])."' and 
                                    fechaFinalInventarioEDI = '". trim($encabezado[$enc]['fechaFinalInventarioEDI'])."' and 
                                    Tercero_idCliente = '". trim($encabezado[$enc]["Tercero_idCliente"])."'");

                            $encabezado[$enc]["idInventarioEDI"] = (count($consulta) > 0 ? get_object_vars($consulta[0])["idInventarioEDI"] : 0);

                            break;

                        case 'SU' :
                            $encabezado[$enc]['Tercero_idProveedor'] = $valores[0];
                            $encabezado[$enc]['nombreProveedorInventarioEDI'] = '';
                            break;
                    }
                    break;

                // LIN - M 1 - Line item 
                // LIN+1++7702382607367:EN'
                case 'LIN' :
                    $swQTY = false;

                    $valores = explode(':',$segmento[3]);
                    $det++;
                    $detalle[$det]['idInventarioEDIDetalle'] = 0;
                    $detalle[$det]['InventarioEDI_idInventarioEDI'] = $encabezado[$enc]['idInventarioEDI'] ;
                    $detalle[$det]['eanProductoInventarioEDI'] = $valores[0];

                    // inicializamos los demas campos
                    $detalle[$det]['cantidadInventarioEDIDetalle'] = 0;
                    $detalle[$det]['eanAlmacen'] = '';
                    $detalle[$det]['precio1InventarioEDIDetalle'] = 0;
                    $detalle[$det]['precio2InventarioEDIDetalle'] = 0;

                    break;

                // QTY - M 1 - Quantity 
                // QTY+145:1:NAR'
                case 'QTY' :
                    
                    // cuando se encuenta un QTY, se debe verificar si ya existe (esta inicializado)
                    // el campo de cantidad del array, si NO esta, es porque estan reportando cantidad
                    // en otra localizacion pero para el ultimo LIN que procesamos, o sea que los siguientes 
                    // registros van a repetir el mismo LIN
                    if($swQTY == true)
                    {    
                        $det++;
                        $detalle[$det]['idInventarioEDIDetalle'] = 0;
                        $detalle[$det]['InventarioEDI_idInventarioEDI'] = $encabezado[$enc]['idInventarioEDI'] ;
                        $detalle[$det]['eanProductoInventarioEDI'] = $detalle[$det-1]['eanProductoInventarioEDI'];

                        // inicializamos los demas campos
                        $detalle[$det]['cantidadInventarioEDIDetalle'] = 0;
                        $detalle[$det]['eanAlmacen'] = '';
                        $detalle[$det]['precio1InventarioEDIDetalle'] = 0;
                        $detalle[$det]['precio2InventarioEDIDetalle'] = 0;
                    }

                    $valores = explode(':',$segmento[1]);

                    switch ($valores[0]) 
                    {
                        // 145 = Inventario Actual 
                        // 97 = Inventario Mínimo 
                        // 98 = Inventario Máximo 
                        // 124 = Bienes Dañados 
                        // 197 = Nivel de Reorden 
                        // 198 = Cantidad en Transito 
                        // 31E = Stock Promocional 
                        case '145' :
                            $detalle[$det]['cantidadInventarioEDIDetalle'] = $valores[1];
                            break;
                    }
                    $swQTY = true;
                    break;

                // LOC - C 5 - Place/location identification 
                case 'LOC' :
                    $valores = explode(':',$segmento[2]);

                    switch ($segmento[1]) 
                    {
                        // 14 = Localizacion de los bienes 
                        case '14' :
                            // $consulta = $conexion->select(
                            //          "Select idTercero FROM Tercero Where codigoBarrasTercero = '". trim($valores[0])."'");
                            
                            $detalle[$det]['eanAlmacen'] = $valores[0]; //(count($consulta) > 0 ? get_object_vars($consulta[0])["idTercero"] : 0);
                            break;
                    }
                    break;

                // PRI - M 1 - Price details 
                case 'PRI' :
                    
                    $valores = explode(':',$segmento[1]);

                    switch ($valores[0]) 
                    {
                        // AAB = Precio de Lista: no incluye descuentos, cargos ni impuestos 
                        // AAA = Precio Neto: incluye descuentos y cargos, excluye impuestos 
                        case 'AAB' :
                            $detalle[$det]['precio1InventarioEDIDetalle'] = $valores[1];
                            break;

                        case 'AAA' :
                            $detalle[$det]['precio2InventarioEDIDetalle'] = $valores[1];
                            break;

                    }
                    break;
            }
 
        }

        // Cerramos el archivo EDI
        fclose($file);


        // *****************************
        //  GUARDAR ENCABEZADO
        // *****************************
        $indice = array(
                      'idInventarioEDI' => $encabezado[$enc]["idInventarioEDI"]);

        $data = array(
            'numeroInventarioEDI' => $encabezado[$enc]['numeroInventarioEDI'],
            'Tercero_idCliente' => $encabezado[$enc]['Tercero_idCliente'], 
            'nombreClienteInventarioEDI' => $encabezado[$enc]['nombreClienteInventarioEDI'], 
            'fechaInicialInventarioEDI' => $encabezado[$enc]['fechaInicialInventarioEDI'], 
            'fechaFinalInventarioEDI' => $encabezado[$enc]['fechaFinalInventarioEDI'],
            'Compania_idCompania' => \Session::get("idCompania")
        );

        $inventarioedi = \App\InventarioEDI::updateOrCreate($indice, $data);
        $ultimoID = \App\InventarioEDI::All()->last();
        $id = $encabezado[$enc]["idInventarioEDI"] != 0 ? $encabezado[$enc]["idInventarioEDI"] : $ultimoID->idInventarioEDI;


        // *****************************
        //  GUARDAR DETALLE
        // *****************************
        // Si el ID no es cero, eliminamos el detalle para evitar duplicados
        \App\InventarioEDIDetalle::where('InventarioEDI_idInventarioEDI','=', $id)->delete();

        // recorremos el array recibido para insertar o actualizar cada registro
        for($reg = 0; $reg < count($detalle); $reg++)
        {
            $indice = array(
                  'idInventarioEDIDetalle' => 0);

            $data = array(
                'InventarioEDI_idInventarioEDI' => $id, 
                'eanProductoInventarioEDI'  => $detalle[$reg]['eanProductoInventarioEDI'], 
                'cantidadInventarioEDIDetalle' => $detalle[$reg]['cantidadInventarioEDIDetalle'], 
                'eanAlmacenInventarioEDIDetalle' => $detalle[$reg]['eanAlmacen'], 
                'precio1InventarioEDIDetalle' => $detalle[$reg]['precio1InventarioEDIDetalle'],
                'precio2InventarioEDIDetalle' => $detalle[$reg]['precio2InventarioEDIDetalle']
            );

            $inventarioedi = \App\InventarioEDIDetalle::updateOrCreate($indice, $data);
            
        }

               
        
    }


    //-------------------------------
    //  V e n t a   E D I 
    //-------------------------------
    // 1. abrimos el archivo y lo leemos linea por linea hasta el fin de archivo
    // 2. por cada linea verificamos que segmento contiene
    // 3. por cada segmento verificamos su contenido y lo almacenamos en el array de datos que será llevado a la BD
    // 4. ejecutamos el proceso de grabado del array de datos
    public function importarVentaEDI($archivo)
    {   
        // este proceso puede tardar mas de 30 minutos o 1 hora, dependiendo de la cantidad de productos 
        // y ubicaciones reportadas en el archivo, por eso no podemos limitar el tiempo de ejecución
        set_time_limit(0);

        // 2. abrimos el archivo 
        $file = fopen($archivo, "r") or die("No fue posible abrir el archivo!");
        // $contenido = fread($file,filesize($archivo));
        
        $encabezado = array();
        $enc = -1;
        $detalle = array();
        $det = -1;

        // lo recorremos hasta el final del archivo
        while (!feof($file)) 
        {
            // leemos la línea
            $linea = trim(fgets($file));

            // separamos los campos del segmento (separador (+))
            $segmento = explode('+', substr($linea, 0, strlen($linea)-1));

            // verificamos que segmento es (primer dato del registro)
            switch ($segmento[0]) 
            {
                // BGM - M 1 - Beginning of message 
                // BGM+73E::9+2016090602+9'
                case 'BGM' :
                    $enc++;
                    $encabezado[$enc]['idVentaEDI'] = 0;
                    $encabezado[$enc]['numeroVentaEDI'] = $segmento[2];
                    break;

                // DTM - M 10 - Date/time/period 
                // DTM+137:20160906:102'
                // DTM+356:2016082920160904:718'
                case 'DTM' :
                    $valores = explode(':',$segmento[1]);

                    $encabezado[$enc]['fechaInicialVentaEDI'] = '';
                    $encabezado[$enc]['fechaFinalVentaEDI'] = '';

                    switch ($valores[0]) 
                    {
                        // 137 = Fecha del Documento 
                        // 356 = Fecha y/o Periodo de Venta    
                        case '356' :
                            $encabezado[$enc]['fechaInicialVentaEDI'] = substr($valores[1],0,4).'-'.substr($valores[1],4,2).'-'.substr($valores[1],6,2);
                            $encabezado[$enc]['fechaFinalVentaEDI'] = substr($valores[1],8,4).'-'.substr($valores[1],12,2).'-'.substr($valores[1],14,2);
                            break;
                    }
                    break;

                // NAD+SE+7701001000008::9'
                case 'NAD' :
                    $valores = explode(':',$segmento[2]);

                    switch ($segmento[1]) 
                    {
                        // SE = Vendedor 
                        // SU = Proveedor
                        case 'SE' :

                        	$consulta = DB::select(
                                     "Select idTercero, nombre1Tercero 
                                     FROM Iblu.Tercero 
                                     Where codigoBarrasTercero = '". trim($valores[0])."'");
                            
                            $encabezado[$enc]['Tercero_idCliente'] = (count($consulta) > 0 ? get_object_vars($consulta[0])["idTercero"] : 0);
                            $encabezado[$enc]['nombreClienteVentaEDI'] = (count($consulta) > 0 ? get_object_vars($consulta[0])["nombre1Tercero"] : 'Cliente no existe');

                            $consulta = DB::select(
                             "Select idVentaEDI 
                             FROM ventaedi 
                             Where  numeroVentaEDI =  '". trim($encabezado[$enc]['numeroVentaEDI'])."' and 
                                    fechaInicialVentaEDI = '". trim($encabezado[$enc]['fechaInicialVentaEDI'])."' and 
                                    fechaFinalVentaEDI = '". trim($encabezado[$enc]['fechaFinalVentaEDI'])."' and 
                                    Tercero_idCliente = '". trim($encabezado[$enc]["Tercero_idCliente"])."'");

                            $encabezado[$enc]["idVentaEDI"] = (count($consulta) > 0 ? get_object_vars($consulta[0])["idVentaEDI"] : 0);

                            break;

                        case 'SU' :
                            $encabezado[$enc]['Tercero_idProveedor'] = $valores[0];
                            $encabezado[$enc]['nombreProveedorVentaEDI'] = '';
                            break;
                    }
                    break;

                // LOC - C 5 - Place/location identification
                // LOC+162+7701001003405::9' 
                case 'LOC' :
                    $swLIN = false;

                    $det++;
                    $detalle[$det]['idVentaEDIDetalle'] = 0;
                    $detalle[$det]['VentaEDI_idVentaEDI'] = $encabezado[$enc]['idVentaEDI'] ;

                    // inicializamos los demas campos
                    $detalle[$det]['eanProductoVentaEDI'] = '';
                    $detalle[$det]['cantidadVentaEDIDetalle'] = 0;
                    $detalle[$det]['precio1VentaEDIDetalle'] = 0;
                    $detalle[$det]['precio2VentaEDIDetalle'] = 0;

                    $valores = explode(':',$segmento[2]);

                    switch ($segmento[1]) 
                    {
                        //  162 = Lugar de Venta 
                        case '162' :
                            // $consulta = $conexion->select(
                            //          "Select idTercero FROM Tercero Where codigoBarrasTercero = '". trim($valores[0])."'");
                            
                            $detalle[$det]['eanAlmacen'] = $valores[0]; //(count($consulta) > 0 ? get_object_vars($consulta[0])["idTercero"] : 0);
                            break;
                    }
                    break;

                // LIN - M 1 - Line item 
                // LIN+1++7450050508477:EN'
                case 'LIN' :
                    
                    // cuando se encuenta un LIN, se debe verificar si ya existe (esta inicializado)
                    // el campo de EAN del array, si NO esta, es porque estan reportando productos
                    // en otra localizacion pero para el ultimo LOC que procesamos, o sea que los siguientes 
                    // registros van a repetir el mismo LOC
                    if($swLIN == true)
                    {    
                        $det++;
                        $detalle[$det]['idVentaEDIDetalle'] = 0;
                        $detalle[$det]['VentaEDI_idVentaEDI'] = $encabezado[$enc]['idVentaEDI'] ;
                        $detalle[$det]['eanAlmacen'] = $detalle[$det-1]['eanAlmacen'];

                        // inicializamos los demas campos
                        $detalle[$det]['eanProductoVentaEDI'] = '';
                        $detalle[$det]['cantidadVentaEDIDetalle'] = 0;
                        $detalle[$det]['precio1VentaEDIDetalle'] = 0;
                        $detalle[$det]['precio2VentaEDIDetalle'] = 0;
                    }

                    $valores = explode(':',$segmento[3]);
                    
                    $detalle[$det]['eanProductoVentaEDI'] = $valores[0];

                    $swLIN = true;
                    break;
              

                // PRI - M 1 - Price details 
                // PRI+AAA:124495.69'
                case 'PRI' :
                    
                    $valores = explode(':',$segmento[1]);

                    switch ($valores[0]) 
                    {
                        // AAB = Precio de Lista: no incluye descuentos, cargos ni impuestos 
                        // AAA = Precio Neto: incluye descuentos y cargos, excluye impuestos 
                        case 'AAB' :
                            $detalle[$det]['precio1VentaEDIDetalle'] = $valores[1];
                            break;

                        case 'AAA' :
                            $detalle[$det]['precio2VentaEDIDetalle'] = $valores[1];
                            break;

                    }
                    break;

                // QTY - M 1 - Quantity 
                // QTY+153:2'
                case 'QTY' :
                    
                    $valores = explode(':',$segmento[1]);

                    switch ($valores[0]) 
                    {
                        // 153 = Cantidad Vendida 
                        case '153' :
                            $detalle[$det]['cantidadVentaEDIDetalle'] = $valores[1];
                            break;
                    }
                    break;

            }
 
        }

        // Cerramos el archivo EDI
        fclose($file);

        // *****************************
        //  GUARDAR ENCABEZADO
        // *****************************
        $indice = array(
                      'idVentaEDI' => $encabezado[$enc]["idVentaEDI"]);

        $data = array(
            'numeroVentaEDI' => $encabezado[$enc]['numeroVentaEDI'],
            'Tercero_idCliente' => $encabezado[$enc]['Tercero_idCliente'], 
            'nombreClienteVentaEDI' => $encabezado[$enc]['nombreClienteVentaEDI'], 
            'fechaInicialVentaEDI' => $encabezado[$enc]['fechaInicialVentaEDI'], 
            'fechaFinalVentaEDI' => $encabezado[$enc]['fechaFinalVentaEDI'],
            'Compania_idCompania' => \Session::get("idCompania")
        );

        $inventarioedi = \App\VentaEDI::updateOrCreate($indice, $data);
        $ultimoID = \App\VentaEDI::All()->last();
        $id = $encabezado[$enc]["idVentaEDI"] != 0 ? $encabezado[$enc]["idVentaEDI"] : $ultimoID->idVentaEDI;


        // *****************************
        //  GUARDAR DETALLE
        // *****************************
        // Si el ID no es cero, eliminamos el detalle para evitar duplicados
        \App\VentaEDIDetalle::where('VentaEDI_idVentaEDI','=', $id)->delete();

        // recorremos el array recibido para insertar o actualizar cada registro
        for($reg = 0; $reg < count($detalle); $reg++)
        {
            $indice = array(
                  'idVentaEDIDetalle' => 0);

            $data = array(
                'VentaEDI_idVentaEDI' => $id, 
                'eanProductoVentaEDI'  => $detalle[$reg]['eanProductoVentaEDI'], 
                'cantidadVentaEDIDetalle' => $detalle[$reg]['cantidadVentaEDIDetalle'], 
                'eanAlmacenVentaEDIDetalle' => $detalle[$reg]['eanAlmacen'], 
                'precio1VentaEDIDetalle' => $detalle[$reg]['precio1VentaEDIDetalle'],
                'precio2VentaEDIDetalle' => $detalle[$reg]['precio2VentaEDIDetalle']
            );
            $ventaedi = \App\VentaEDIDetalle::updateOrCreate($indice, $data);
            
        }

               
        
    }

}
