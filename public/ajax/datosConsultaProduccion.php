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
        Group_concat(CAST(IFNULL(cantidadRecibo,0) AS UNSIGNED) SEPARATOR "  /  ") as cantidadRecibo,
        Group_concat(observacionMovimiento SEPARATOR "  /  ") as observacionMovimiento 
    FROM
    (
      select IPP.OrdenProduccion_idOrdenProduccion, MAX(ordenOrdenProduccionCentroProduccion) as ultimoCentroProduccion
      from '.$BD.'.InventarioProductoProceso IPP 
      LEFT JOIN '.$BD.'.OrdenProduccion OP
      ON IPP.OrdenProduccion_idOrdenProduccion = OP.idOrdenProduccion 
      LEFT JOIN '.$BD.'.OrdenProduccionCentroProduccion ocp 
      ON IPP.OrdenProduccion_idOrdenProduccion = ocp.OrdenProduccion_idOrdenProduccion and 
        IPP.CentroProduccion_idCentroProduccion = ocp.CentroProduccion_idCentroProduccion
      where fechaElaboracionOrdenProduccion >= "2016-01-01" AND 
            Periodo_idPeriodo =  (SELECT idPeriodo FROM '.$BD.'.Periodo WHERE fechaInicialPeriodo <= CURDATE() AND fechaFinalPeriodo >= CURDATE())
      Group By IPP.OrdenProduccion_idOrdenProduccion
    ) UltCP
    LEFT JOIN
    (
      -- detalle de Ordenes de Produccion
      select OPP.OrdenProduccion_idOrdenProduccion, OPP.Producto_idProducto, OP.Tercero_idTercero, numeroOrdenProduccion, fechaElaboracionOrdenProduccion, documentoReferenciaOrdenProduccion, SUM(OPP.cantidadOrdenProduccionProducto) as cantidadOrdenProduccion
      from  
      (
        select OrdenProduccion_idOrdenProduccion
        from '.$BD.'.InventarioProductoProceso 
        where Periodo_idPeriodo =  (SELECT idPeriodo FROM '.$BD.'.Periodo WHERE fechaInicialPeriodo <= CURDATE() AND fechaFinalPeriodo >= CURDATE())
        Group By OrdenProduccion_idOrdenProduccion
      ) IPP
      LEFT JOIN '.$BD.'.OrdenProduccion OP
      ON IPP.OrdenProduccion_idOrdenProduccion = OP.idOrdenProduccion
      LEFT JOIN '.$BD.'.OrdenProduccionProducto OPP 
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
        from '.$BD.'.InventarioProductoProceso 
        where Periodo_idPeriodo =  (SELECT idPeriodo FROM '.$BD.'.Periodo WHERE fechaInicialPeriodo <= CURDATE() AND fechaFinalPeriodo >= CURDATE())
        Group By OrdenProduccion_idOrdenProduccion, CentroProduccion_idCentroProduccion
      ) IPP
      LEFT JOIN '.$BD.'.ProduccionEntrega PE
      ON  IPP.OrdenProduccion_idOrdenProduccion = PE.OrdenProduccion_idOrdenProduccion and 
          IPP.CentroProduccion_idCentroProduccion = PE.CentroProduccion_idCentroProduccion
      LEFT JOIN '.$BD.'.ProduccionEntregaProducto PEP 
      ON PE.idProduccionEntrega = PEP.ProduccionEntrega_idProduccionEntrega  
      LEFT JOIN '.$BD.'.OrdenProduccionCentroProduccion ocp 
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
        from '.$BD.'.InventarioProductoProceso 
        where Periodo_idPeriodo =  (SELECT idPeriodo FROM '.$BD.'.Periodo WHERE fechaInicialPeriodo <= CURDATE() AND fechaFinalPeriodo >= CURDATE())
        Group By OrdenProduccion_idOrdenProduccion, CentroProduccion_idCentroProduccion
      ) IPP
      LEFT JOIN  '.$BD.'.ProduccionEntrega PE
      ON  IPP.OrdenProduccion_idOrdenProduccion = PE.OrdenProduccion_idOrdenProduccion and 
          IPP.CentroProduccion_idCentroProduccion = PE.CentroProduccion_idCentroProduccion
      LEFT JOIN '.$BD.'.ProduccionRecibo PR
      ON PE.idProduccionEntrega = PR.ProduccionEntrega_idProduccionEntrega 
      LEFT JOIN '.$BD.'.ProduccionReciboProducto PRP 
      ON PR.idProduccionRecibo = PRP.ProduccionRecibo_idProduccionRecibo 
      LEFT JOIN '.$BD.'.OrdenProduccionCentroProduccion ocp 
      ON  IPP.OrdenProduccion_idOrdenProduccion = ocp.OrdenProduccion_idOrdenProduccion and 
          IPP.CentroProduccion_idCentroProduccion = ocp.CentroProduccion_idCentroProduccion
      Group By IPP.OrdenProduccion_idOrdenProduccion, IPP.CentroProduccion_idCentroProduccion
    ) Rec
    ON  Rem.OrdenProduccion_idOrdenProduccion = Rec.OrdenProduccion_idOrdenProduccion and 
        Rem.CentroProduccion_idCentroProduccion = Rec.CentroProduccion_idCentroProduccion and  
        UltCP.ultimoCentroProduccion = Rec.ordenOrdenProduccionCentroProduccion
    LEFT JOIN '.$BD.'.OrdenProduccionDocumentoRef opdf ON opdf.OrdenProduccion_idOrdenProduccion = IPP.OrdenProduccion_idOrdenProduccion
    LEFT JOIN '.$BD.'.Producto p ON IPP.Producto_idProducto = p.idProducto
    LEFT JOIN '.$BD.'.TipoNegocio tn ON p.TipoNegocio_idTipoNegocio = tn.idTipoNegocio
    LEFT JOIN '.$BD.'.TipoProducto tp ON p.TipoProducto_idTipoProducto = tp.idTipoProducto
    LEFT JOIN '.$BD.'.Movimiento m ON opdf.Movimiento_idDocumentoRef = m.idMovimiento
    LEFT JOIN '.$BD.'.Tercero t ON IPP.Tercero_idTercero = t.idTercero
    LEFT JOIN '.$BD.'.MacroCanal mc ON mc.idMacroCanal = t.MacroCanal_idMacroCanal
    LEFT JOIN '.$BD.'.CentroProduccion cp ON Rem.CentroProduccion_idCentroProduccion = cp.idCentroProduccion
    WHERE numeroOrdenProduccion IS NOT NULL

    GROUP BY numeroOrdenProduccion

    UNION 

    -- consulta de las unidades Cortadas en produccion

    SELECT "" as Fecha_proceso,
        "" as fechaMaximaMovimiento,
        codigoAlternoProducto,
        numeroOrdenProduccion,
        fechaElaboracionOrdenProduccion,
        documentoReferenciaOrdenProduccion,
        "" as nombre1Tercero,
        IFNULL(nombreMacroCanal, "") AS nombreMacroCanal,
        nombreTipoProducto,
        nombreTipoNegocio,
        "" AS observacionOrdenProduccion,
        (opp.cantidadOrdenProduccionProducto - PR.cantidadProduccionReciboProducto) as cantidadOrdenProduccionProducto, 
        "Liberación" as  nombreCentroProduccion, 
        0 as cantidadRemision, 
        0 as cantidadRecibo,
        "" as observacionMovimiento 
    FROM  
    (
      -- Consultamos las OP, excluyendo las Explosiones BOM y las ANULADAS
      SELECT OrdenProduccion_idOrdenProduccion, op.numeroOrdenProduccion, op.fechaElaboracionOrdenProduccion, 
        op.estadoOrdenProduccion, observacionOrdenProduccion, documentoReferenciaOrdenProduccion,
        opp.Producto_idProducto, Tercero_idTercero,
        opp.cantidadOrdenProduccionProducto
      FROM   '.$BD.'.OrdenProduccion op 
      LEFT JOIN '.$BD.'.OrdenProduccionProducto opp 
        ON  op.idOrdenProduccion = opp.OrdenProduccion_idOrdenProduccion 
      WHERE conceptoOrdenProduccion != "BOM" and 
        estadoOrdenProduccion != "ANULADO" and 
        fechaElaboracionOrdenProduccion >= "2016-01-01" and 
          opp.Movimiento_idDocumentoRef != 0
    ) opp
    left join '.$BD.'.ProduccionEntrega E
    on opp.OrdenProduccion_idOrdenProduccion = E.OrdenProduccion_idOrdenProduccion
    left join '.$BD.'.ProduccionRecibo R
    on E.idProduccionEntrega = R.ProduccionEntrega_idProduccionEntrega
    left join '.$BD.'.ProduccionReciboProducto PR 
    ON R.idProduccionRecibo = PR.ProduccionRecibo_idProduccionRecibo and opp.Producto_idProducto = PR.Producto_idProducto
    left join '.$BD.'.CentroProduccion CP 
    on E.CentroProduccion_idCentroProduccion = CP.idCentroProduccion
    left join '.$BD.'.Producto P
    on PR.Producto_idProducto = P.idProducto
    LEFT JOIN '.$BD.'.TipoNegocio tn 
    ON P.TipoNegocio_idTipoNegocio = tn.idTipoNegocio
    LEFT JOIN '.$BD.'.TipoProducto tp 
    ON P.TipoProducto_idTipoProducto = tp.idTipoProducto
    LEFT JOIN '.$BD.'.Tercero t ON opp.Tercero_idTercero = t.idTercero
    LEFT JOIN '.$BD.'.MacroCanal mc 
    ON mc.idMacroCanal = t.MacroCanal_idMacroCanal
    where determinanteCorteCentroProduccion = 1 and 
          opp.cantidadOrdenProduccionProducto > PR.cantidadProduccionReciboProducto 


    UNION 


    SELECT 
        "" as Fecha_proceso,
            fechaMaximaMovimiento,
            codigoAlternoProducto,
            "" as numeroOrdenProduccion,
            "" as fechaElaboracionOrdenProduccion,
            numeroMovimiento as documentoReferenciaOrdenProduccion,
            t.nombre1Tercero,
            IFNULL(nombreMacroCanal, "") AS nombreMacroCanal,
            nombreTipoProducto,
            nombreTipoNegocio,
            "" AS observacionOrdenProduccion,
            SUM(md.cantidadMovimientoDetalle - IFNULL(opp.cantidadOrdenProduccionProducto,0) ) as cantidadOrdenProduccionProducto, 
            "Sin Programar" as  nombreCentroProduccion, 
            0 as cantidadRemision, 
            0 as cantidadRecibo,
            CONCAT("Cant Pedida: ",SUM(md.cantidadMovimientoDetalle), "   Cant Programada: ", SUM(IFNULL(opp.cantidadOrdenProduccionProducto,0)), "  Observaciones: ", observacionMovimiento ) as observacionMovimiento 
    FROM '.$BD.'.MovimientoDetalle md
    LEFT JOIN '.$BD.'.Movimiento m
    on md.Movimiento_idMovimiento = m.idMovimiento
    LEFT JOIN 
    (
      -- Consultamos las OP, excluyendo las Explosiones BOM y las ANULADAS
      SELECT OrdenProduccion_idOrdenProduccion, op.numeroOrdenProduccion, op.fechaElaboracionOrdenProduccion, 
        op.estadoOrdenProduccion, Movimiento_idDocumentoRef, 
        opp.Producto_idProducto,
        SUM(opp.cantidadOrdenProduccionProducto) as cantidadOrdenProduccionProducto
      FROM   '.$BD.'.OrdenProduccion op 
      LEFT JOIN '.$BD.'.OrdenProduccionProducto opp 
        ON  op.idOrdenProduccion = opp.OrdenProduccion_idOrdenProduccion 
      WHERE conceptoOrdenProduccion != "BOM" and 
        estadoOrdenProduccion != "ANULADO" and 
        fechaElaboracionOrdenProduccion >= "2016-01-01" and 
          opp.Movimiento_idDocumentoRef != 0
      GROUP BY Movimiento_idDocumentoRef, Producto_idProducto
    ) opp   
    ON md.Movimiento_idMovimiento = opp.Movimiento_idDocumentoRef and md.Producto_idProducto = opp.Producto_idProducto 
    LEFT JOIN '.$BD.'.Producto P
    on md.Producto_idProducto = P.idProducto
    LEFT JOIN '.$BD.'.TipoNegocio tn ON P.TipoNegocio_idTipoNegocio = tn.idTipoNegocio
    LEFT JOIN '.$BD.'.TipoProducto tp ON P.TipoProducto_idTipoProducto = tp.idTipoProducto
    LEFT JOIN '.$BD.'.Tercero t ON m.Tercero_idTercero = t.idTercero
    LEFT JOIN '.$BD.'.MacroCanal mc ON mc.idMacroCanal = t.MacroCanal_idMacroCanal
    WHERE m.Documento_idDocumento = 14 and 
        fechaElaboracionMovimiento >= "2016-01-01" and 
        estadoWMSMovimiento = "AUTORIZADO" and 
        (md.cantidadMovimientoDetalle - IFNULL(opp.cantidadOrdenProduccionProducto,0) ) > 0
    GROUP BY idMovimiento, codigoAlternoProducto
    ORDER BY numeroOrdenProduccion

    ');


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
        $row[$key][] = $value->observacionMovimiento;
    }
    $output['aaData'] = $row;
    echo json_encode($output);

?>