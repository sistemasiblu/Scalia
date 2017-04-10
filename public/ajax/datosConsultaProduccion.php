<?php

        
    $BD = \Session::get("baseDatosCompania");
   

    $sql = DB::select(
    'SELECT 
        MAX(IF(fechaElaboracionProduccionRecibo IS NOT NULL, 
          fechaElaboracionProduccionRecibo,
          fechaElaboracionProduccionEntrega)) AS Fecha_proceso, 
        MIN(fechaMaximaMovimiento) AS fechaMaximaMovimiento,
        codigoAlternoProducto,
        numeroOrdenProduccion,
        fechaElaboracionOrdenProduccion,
        documentoReferenciaOrdenProduccion,
        nombre1Tercero,
        IFNULL(nombreMacroCanal, "") AS nombreMacroCanal,
        nombreTipoProducto,
        nombreTipoNegocio,
        "" AS observacionOrdenProduccion,
        IPP.cantidadOrdenProduccion, 
        Group_concat(nombreCentroProduccion SEPARATOR "  /  ") as nombreCentroProduccion, 
        Group_concat(CAST(cantidadRemision AS UNSIGNED) SEPARATOR "  /  ") as cantidadRemision, 
        Group_concat(CAST(IFNULL(cantidadRecibo,0) AS UNSIGNED) SEPARATOR "  /  ") as cantidadRecibo
    FROM
    (
      select IPP.OrdenProduccion_idOrdenProduccion, MAX(ordenOrdenProduccionCentroProduccion) as ultimoCentroProduccion
      from Iblu.InventarioProductoProceso IPP 
      LEFT JOIN Iblu.OrdenProduccion OP
      ON IPP.OrdenProduccion_idOrdenProduccion = OP.idOrdenProduccion 
      LEFT JOIN Iblu.OrdenProduccionCentroProduccion ocp ON IPP.OrdenProduccion_idOrdenProduccion = ocp.OrdenProduccion_idOrdenProduccion and 
        IPP.CentroProduccion_idCentroProduccion = ocp.CentroProduccion_idCentroProduccion
      where fechaElaboracionOrdenProduccion >= "2016-01-01" AND 
            Periodo_idPeriodo =  (SELECT idPeriodo FROM Iblu.Periodo WHERE fechaInicialPeriodo <= CURDATE() AND fechaFinalPeriodo >= CURDATE())
      Group By IPP.OrdenProduccion_idOrdenProduccion
    ) UltCP
    LEFT JOIN
    (
      -- detalle de Ordenes de Produccion
      select OPP.OrdenProduccion_idOrdenProduccion, OPP.Producto_idProducto, OP.Tercero_idTercero, numeroOrdenProduccion, fechaElaboracionOrdenProduccion, documentoReferenciaOrdenProduccion, SUM(OPP.cantidadOrdenProduccionProducto) as cantidadOrdenProduccion
      from  
      (
        select OrdenProduccion_idOrdenProduccion
        from Iblu.InventarioProductoProceso 
        where Periodo_idPeriodo =  (SELECT idPeriodo FROM Iblu.Periodo WHERE fechaInicialPeriodo <= CURDATE() AND fechaFinalPeriodo >= CURDATE())
        Group By OrdenProduccion_idOrdenProduccion
      ) IPP
      LEFT JOIN Iblu.OrdenProduccion OP
      ON IPP.OrdenProduccion_idOrdenProduccion = OP.idOrdenProduccion
      LEFT JOIN Iblu.OrdenProduccionProducto OPP 
      on OP.idOrdenProduccion = OPP.OrdenProduccion_idOrdenProduccion
      Group By OPP.OrdenProduccion_idOrdenProduccion
    ) IPP
    ON UltCP.OrdenProduccion_idOrdenProduccion = IPP.OrdenProduccion_idOrdenProduccion 
    LEFT JOIN 
    (
      -- detalle de remisiones
      select IPP.OrdenProduccion_idOrdenProduccion, IPP.CentroProduccion_idCentroProduccion, ocp.ordenOrdenProduccionCentroProduccion, MAX(fechaElaboracionProduccionEntrega) as fechaElaboracionProduccionEntrega, SUM(PEP.cantidadProduccionEntregaProducto) as cantidadRemision
      from  
      (
        select OrdenProduccion_idOrdenProduccion, CentroProduccion_idCentroProduccion
        from Iblu.InventarioProductoProceso 
        where Periodo_idPeriodo =  (SELECT idPeriodo FROM Iblu.Periodo WHERE fechaInicialPeriodo <= CURDATE() AND fechaFinalPeriodo >= CURDATE())
        Group By OrdenProduccion_idOrdenProduccion, CentroProduccion_idCentroProduccion
      ) IPP
      LEFT JOIN Iblu.ProduccionEntrega PE
      ON  IPP.OrdenProduccion_idOrdenProduccion = PE.OrdenProduccion_idOrdenProduccion and 
          IPP.CentroProduccion_idCentroProduccion = PE.CentroProduccion_idCentroProduccion
      LEFT JOIN Iblu.ProduccionEntregaProducto PEP 
      ON PE.idProduccionEntrega = PEP.ProduccionEntrega_idProduccionEntrega  
      LEFT JOIN Iblu.OrdenProduccionCentroProduccion ocp 
      ON  IPP.OrdenProduccion_idOrdenProduccion = ocp.OrdenProduccion_idOrdenProduccion and 
          IPP.CentroProduccion_idCentroProduccion = ocp.CentroProduccion_idCentroProduccion
      Group By IPP.OrdenProduccion_idOrdenProduccion, IPP.CentroProduccion_idCentroProduccion
    ) Rem
    ON  IPP.OrdenProduccion_idOrdenProduccion = Rem.OrdenProduccion_idOrdenProduccion and 
        UltCP.ultimoCentroProduccion = Rem.ordenOrdenProduccionCentroProduccion
    LEFT JOIN 
    (
      -- detalle de recibos
      select IPP.OrdenProduccion_idOrdenProduccion, IPP.CentroProduccion_idCentroProduccion, ocp.ordenOrdenProduccionCentroProduccion, MAX(fechaElaboracionProduccionRecibo) as fechaElaboracionProduccionRecibo, SUM(PRP.cantidadProduccionReciboProducto) as cantidadRecibo
      from  
      (
        select OrdenProduccion_idOrdenProduccion, CentroProduccion_idCentroProduccion
        from Iblu.InventarioProductoProceso 
        where Periodo_idPeriodo =  (SELECT idPeriodo FROM Iblu.Periodo WHERE fechaInicialPeriodo <= CURDATE() AND fechaFinalPeriodo >= CURDATE())
        Group By OrdenProduccion_idOrdenProduccion, CentroProduccion_idCentroProduccion
      ) IPP
      LEFT JOIN  Iblu.ProduccionEntrega PE
      ON  IPP.OrdenProduccion_idOrdenProduccion = PE.OrdenProduccion_idOrdenProduccion and 
          IPP.CentroProduccion_idCentroProduccion = PE.CentroProduccion_idCentroProduccion
      LEFT JOIN Iblu.ProduccionRecibo PR
      ON PE.idProduccionEntrega = PR.ProduccionEntrega_idProduccionEntrega 
      LEFT JOIN Iblu.ProduccionReciboProducto PRP 
      ON PR.idProduccionRecibo = PRP.ProduccionRecibo_idProduccionRecibo 
      LEFT JOIN Iblu.OrdenProduccionCentroProduccion ocp 
      ON  IPP.OrdenProduccion_idOrdenProduccion = ocp.OrdenProduccion_idOrdenProduccion and 
          IPP.CentroProduccion_idCentroProduccion = ocp.CentroProduccion_idCentroProduccion
      Group By IPP.OrdenProduccion_idOrdenProduccion, IPP.CentroProduccion_idCentroProduccion
    ) Rec
    ON  Rem.OrdenProduccion_idOrdenProduccion = Rec.OrdenProduccion_idOrdenProduccion and 
        Rem.CentroProduccion_idCentroProduccion = Rec.CentroProduccion_idCentroProduccion and  
        UltCP.ultimoCentroProduccion = Rec.ordenOrdenProduccionCentroProduccion
    LEFT JOIN Iblu.OrdenProduccionDocumentoRef opdf ON opdf.OrdenProduccion_idOrdenProduccion = IPP.OrdenProduccion_idOrdenProduccion
    LEFT JOIN Iblu.Producto p ON IPP.Producto_idProducto = p.idProducto
    LEFT JOIN Iblu.TipoNegocio tn ON p.TipoNegocio_idTipoNegocio = tn.idTipoNegocio
    LEFT JOIN Iblu.TipoProducto tp ON p.TipoProducto_idTipoProducto = tp.idTipoProducto
    LEFT JOIN Iblu.Movimiento m ON opdf.Movimiento_idDocumentoRef = m.idMovimiento
    LEFT JOIN Iblu.Tercero t ON IPP.Tercero_idTercero = t.idTercero
    LEFT JOIN Iblu.MacroCanal mc ON mc.idMacroCanal = t.MacroCanal_idMacroCanal
    LEFT JOIN Iblu.CentroProduccion cp ON Rem.CentroProduccion_idCentroProduccion = cp.idCentroProduccion
    WHERE numeroOrdenProduccion IS NOT NULL

    GROUP BY numeroOrdenProduccion
    ORDER BY numeroOrdenProduccion');


    $row = array();



    foreach ($sql as $key => $value) 
    {  

        $row[$key][] = '<p style="cursor:pointer;" onclick="imprimirFormato(\''.$value->codigoAlternoProducto.'\', \'FichaTecnica\');">'.$value->codigoAlternoProducto.'</p>';
        $row[$key][] = '<p style="cursor:pointer;" onclick="imprimirFormato(\''.$value->numeroOrdenProduccion.'\', \'Produccion\');">'.$value->numeroOrdenProduccion.'</p>';
        $row[$key][] = '<p style="cursor:pointer;" onclick="imprimirFormato(\''.$value->documentoReferenciaOrdenProduccion.'\', \'Movimiento\');">'.$value->documentoReferenciaOrdenProduccion.'</p>';
        $row[$key][] = $value->nombre1Tercero;
        $row[$key][] = $value->nombreMacroCanal;
        $row[$key][] = $value->nombreCentroProduccion;
        $row[$key][] = $value->cantidadOrdenProduccion; 
        $row[$key][] = 
            ($value->cantidadRemision < $value->cantidadOrdenProduccion 
                ? '<p style="color: red;">'.$value->cantidadRemision.'</p>' 
                : 
                    ($value->cantidadRemision > $value->cantidadOrdenProduccion 
                        ? '<p style="color: green;">'.$value->cantidadRemision.'</p>' 
                        : $value->cantidadRemision));
        $row[$key][] = $value->cantidadRecibo;
        $row[$key][] = $value->fechaMaximaMovimiento;
        $row[$key][] = $value->fechaElaboracionOrdenProduccion;    
        $row[$key][] = $value->Fecha_proceso;
        $row[$key][] = $value->nombreTipoProducto;
        $row[$key][] = $value->nombreTipoNegocio;
        $row[$key][] = '<p style="cursor:pointer;" onclick="abrirModalObservaciones('.$value->numeroOrdenProduccion.')" title="'.$value->observacionOrdenProduccion.'">'.substr($value->observacionOrdenProduccion,0,30).'</p>';
    }
    $output['aaData'] = $row;
    echo json_encode($output);

?>