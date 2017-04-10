<?php 

$idCompra = $_POST['idCompra'];

$compra = DB::Select('
	SELECT 
	    numeroCompra,
	    nombreTemporadaCompra,
	    nombreProveedorCompra,
	    eventoCompra,
	    compradorVendedorCompra,
	    IFNULL(group_concat(facturaEmbarqueDetalle), "No tiene") as facturaEmbarqueDetalle,
	    IFNULL(proformaEmbarqueDetalle, "No tiene") as proformaEmbarqueDetalle,
	    valorCompra,
	    SUM(valorFacturaEmbarqueDetalle) AS valorFacturaEmbarqueDetalle,
	    IFNULL(group_concat(numeroDocumentoFinanciero), "No tiene") AS numeroDocumentoFinanciero,
	    IFNULL(group_concat(numeroReferenciaExternoMovimiento), "No tiene") AS numeroDocnumeroReferenciaExternoMovimientoumentoFinanciero
	FROM
	    compra c
	        LEFT JOIN
	    embarquedetalle ed ON c.idCompra = ed.Compra_idCompra
	        LEFT JOIN
	    documentofinancierodetalle dfd ON c.idCompra = dfd.Compra_idCompra
	        LEFT JOIN
	    documentofinanciero df ON dfd.DocumentoFinanciero_idDocumentoFinanciero = df.idDocumentoFinanciero
	    	LEFT JOIN
    	Iblu.Movimiento m ON ed.facturaEmbarqueDetalle = m.numeroMovimiento
	WHERE
	    idCompra = '.$idCompra.' 
	    AND m.Documento_idDocumento = 38');

$datosCompra = get_object_vars($compra[0]);

$comprahtml = '';

$comprahtml .= '
<center><h3><b>Compra No</b> <span style="color: #2b2301;">'.$datosCompra['numeroCompra'].'</span></h3></center>
<br>
<h4><b>Proforma:</b> '.$datosCompra['proformaEmbarqueDetalle'].'</h4>
<h4><b>Temporada:</b> '.$datosCompra['nombreTemporadaCompra'].'</h4>
<h4><b>Proveedor:</b> '.$datosCompra['nombreProveedorCompra'].'</h4>
<h4><b>IM:</b> '.$datosCompra['facturaEmbarqueDetalle'].'</h4>
<h4><b>Evento:</b> '.$datosCompra['eventoCompra'].'</h4>
<h4><b>Comprador/Vendedor:</b> '.$datosCompra['compradorVendedorCompra'].'</h4>
<h4><b>Valor compra:</b> '.number_format($datosCompra['valorCompra'],2,".",",").'</h4>
<h4><b>Valor IM:</b> '.number_format($datosCompra['valorFacturaEmbarqueDetalle'],2,".",",").'</h4>
<h4><b>Documento financiero:</b> '.$datosCompra['numeroDocumentoFinanciero'].'</h4>
<h4><b>RR:</b>'.$datosCompra['numeroDocnumeroReferenciaExternoMovimientoumentoFinanciero'].'</h4>';

echo json_encode($comprahtml);

?>