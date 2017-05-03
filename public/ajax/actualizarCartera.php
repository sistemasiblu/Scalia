<?php 

function actualizarCartera($accion, $modulo, $idCompra, $idDocumentoFinanciero, $fecha, $valor)
{ 
    #Consulto cual es el id del periodo dependiendo de la fecha actual para llevar este a la where de la consulta
    $consultaperiodo = DB::Select('SELECT idPeriodo FROM Iblu.Periodo where fechaInicialPeriodo <= "'.$fecha.'" and fechaFinalPeriodo >= "'.$fecha.'"');

    if (count($consultaperiodo) == 0) 
    {
      return true;
    }
    $periodo = get_object_vars($consultaperiodo[0]);

   if ($accion == 'carga') 
   {
        if ($modulo == 'compra') 
        {
          $actualizar = DB::Select('INSERT INTO carteraforward (Compra_idCompra, Periodo_idPeriodo, DocumentoFinanciero_idDocumentoFinanciero, saldoInicialCarteraForward, abonoCarteraForward, saldoFinalCarteraForward) VALUES ('.$idCompra.','.$periodo['idPeriodo'].', NULL, '.$valor.',0,'.$valor.') ON DUPLICATE KEY 
          UPDATE saldoInicialCarteraForward = '.$valor.',
          saldoFinalCarteraForward = saldoInicialCarteraForward - abonoCarteraForward');
        }

        else if ($modulo == 'pago')
        {
            $clave = ($idCompra == '' ? 'DocumentoFinanciero_idDocumentoFinanciero = '.$idDocumentoFinanciero : 'Compra_idCompra = '.$idCompra);

            $actualizar = DB::Select('UPDATE carteraforward 
              SET abonoCarteraForward = abonoCarteraForward + '.$valor.', 
                  saldoFinalCarteraForward = saldoInicialCarteraForward - abonoCarteraForward 
              WHERE '.$clave.' and Periodo_idPeriodo = '.$periodo['idPeriodo']);
        }

        else if ($modulo == 'documentofinanciero') 
        {
          $actualizar = DB::Select('INSERT INTO carteraforward (Compra_idCompra, Periodo_idPeriodo, DocumentoFinanciero_idDocumentoFinanciero, saldoInicialCarteraForward, abonoCarteraForward, saldoFinalCarteraForward) VALUES (NULL,'.$periodo['idPeriodo'].', '.$idDocumentoFinanciero.', '.$valor.',0,'.$valor.') ON DUPLICATE KEY 
          UPDATE saldoInicialCarteraForward = '.$valor.',
          saldoFinalCarteraForward = saldoInicialCarteraForward - abonoCarteraForward');     
        }
   }
   else
   {
        if ($modulo == 'compra') 
        {
          $actualizar = DB::Select('UPDATE carteraforward 
          SET saldoInicialCarteraForward = saldoInicialCarteraForward -'.$valor.', 
              saldoFinalCarteraForward = saldoInicialCarteraForward - abonoCarteraForward 
          WHERE Compra_idCompra = '.$idCompra.' and Periodo_idPeriodo = '.$periodo['idPeriodo']);
        }

        else if($modulo == 'pago')
        {

           $clave = ($idCompra == '' ? 'DocumentoFinanciero_idDocumentoFinanciero = '.$idDocumentoFinanciero : 'Compra_idCompra = '.$idCompra);

            $actualizar = DB::Select('UPDATE carteraforward 
              SET abonoCarteraForward = abonoCarteraForward - '.$valor.', 
                  saldoFinalCarteraForward = saldoInicialCarteraForward - abonoCarteraForward 
              WHERE '.$clave.' and Periodo_idPeriodo = '.$periodo['idPeriodo']);
        }

        else if($modulo == 'documentofinanciero')
        {
          $actualizar = DB::Select('UPDATE carteraforward 
          SET saldoInicialCarteraForward = saldoInicialCarteraForward -'.$valor.', 
              saldoFinalCarteraForward = saldoInicialCarteraForward - abonoCarteraForward 
          WHERE DocumentoFinanciero_idDocumentoFinanciero = '.$idDocumentoFinanciero.' and Periodo_idPeriodo = '.$periodo['idPeriodo']);
        }
   }

   # llevamos los valores hasta el mes actual
   # si el mes-año de la fecha es menor que el mes-año del date(), a nuevafecha le sumo 1 mes y ejecuto de nuevo la funcion con la nueva fecha y el saldo final

   $date = date("Y-m");
   $fecha2 = date("Y-m",strtotime($fecha));

   if ($fecha2 < $date) 
   {
      #Le sumo un mes a la fecha
      $nuevaFecha = date("Y-m-d", strtotime("+1 MONTH", strtotime($fecha)));

      if ($idCompra !== '') 
      {
        # consulto el saldo final de la compra 
        $nuevoSaldoCompra = DB::Select('SELECT  saldoFinalCarteraForward
        FROM carteraforward 
            WHERE Compra_idCompra = '.$idCompra.' and Periodo_idPeriodo = '.$periodo['idPeriodo']);

        if(count($nuevoSaldoCompra) > 0)
        {
          $nSaldoC = get_object_vars($nuevoSaldoCompra[0])['saldoFinalCarteraForward'];
          #ejecuto de nuevo la funcion con la nueva fecha y el saldo final
          actualizarCartera($accion, 'compra', $idCompra, '', $nuevaFecha, $nSaldoC);
        }
      }

      if ($idDocumentoFinanciero !== '') 
      {
          # consulto el saldo final del documento
        $nuevoSaldoDocumento = DB::Select('SELECT  saldoFinalCarteraForward
        FROM carteraforward 
            WHERE DocumentoFinanciero_idDocumentoFinanciero = '.$idDocumentoFinanciero.' and Periodo_idPeriodo = '.$periodo['idPeriodo']);

        if(count($nuevoSaldoDocumento) > 0)
        {
          $nSaldoD = get_object_vars($nuevoSaldoDocumento[0])['saldoFinalCarteraForward'];

          #ejecuto de nuevo la funcion con la nueva fecha y el saldo final
          actualizarCartera($accion, 'documentofinanciero', '', $idDocumentoFinanciero, $nuevaFecha, $nSaldoD);
        } 
      }
    }

   return($actualizar);
}