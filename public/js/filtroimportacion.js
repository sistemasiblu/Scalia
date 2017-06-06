$(document).ready( function () {

    $("#fechaInicialCompra, #fechaFinalCompra, #fechaInicialEmbarque, #fechaFinalEmbarque").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarImportacion(Temporada, Compra, Cliente, Proveedor, Puerto, FechaInicioCompra, FechaFinCompra, FechaInicioEmbarque, FechaFinEmabrque, bodega, Documento)
{
	if (FechaInicioCompra != '' && FechaFinCompra == '' || FechaInicioEmbarque != '' && FechaFinEmabrque == '') 
		alert('Debe llenar el campo "Hasta" en la Compra o en el Embarque.');
	else
	{
		condicion = '';
		join = '';

		if (Temporada != 0)
			condicion = condicion + 'comp.Temporada_idTemporada = "'+Temporada+'"';

		if (Compra !=0) 
			condicion = condicion + ((condicion !='' && Compra !=0) ? ' and ' : '') + 'numeroCompra = "'+Compra+'"';
		
		if (Cliente != 0)
			condicion = condicion + ((condicion !='' && Cliente !=0) ? ' and ' : '') + 'Tercero_idCliente = "'+Cliente+'"';

		if (Proveedor != 0)
			condicion = condicion + ((condicion !='' && Proveedor !=0) ? ' and ' : '') + 'Tercero_idProveedor = "'+Proveedor+'"';

		if (Puerto != 0)
			condicion = condicion + ((condicion !='' && Puerto !=0) ? ' and ' : '') + 'Ciudad_idPuerto = "'+Puerto+'"';

		if (FechaInicioCompra != '' && FechaFinCompra != '')
			condicion = condicion + ((condicion !='' && FechaInicioCompra !='') ? ' and ' : '') + 'fechaCompra >= "'+FechaInicioCompra+'" and fechaCompra <= "'+FechaFinCompra+'"';

		if (FechaInicioEmbarque != '' && FechaFinEmabrque != '')
			condicion = condicion + ((condicion !='' && FechaInicioEmbarque !='') ? ' and ' : '') + 'fechaRealEmbarque >= "'+FechaInicioEmbarque+'" and fechaRealEmbarque <= "'+FechaFinEmabrque+'"';

		if (bodega == 1)
			condicion = condicion + ((condicion != '' && bodega == 1) ? ' and ' : '') + 'idMercanciaExtranjeraDetalle IS NOT NULL';

		if (bodega == 0)
			condicion = condicion + ((condicion != '' && bodega == 0) ? ' and ' : '') + 'idMercanciaExtranjeraDetalle IS NULL';

		if (Documento != 0)
			condicion = condicion + ((condicion != '' && Documento != 0) ? ' and ' : '') + 'DocumentoImportacion_idDocumentoImportacion = "'+Documento+'"';

		join = join + 'LEFT JOIN Iblu.Movimiento m ON ed.facturaEmbarqueDetalle = m.numeroReferenciaExternoMovimiento and m.Documento_idDocumento = 20 LEFT JOIN (select Movimiento_idMovimiento, idMercanciaExtranjeraDetalle  from Iblu.MercanciaExtranjeraDetalle group by Movimiento_idMovimiento ) med ON m.idMovimiento = med.Movimiento_idMovimiento';

		// if (bodega == 0)
		// condicion = condicion + 'LEFT JOIN Iblu.Movimiento m ON ed.facturaEmbarqueDetalle = m.numeroMovimiento and m.Documento_idDocumento = 20 LEFT JOIN Iblu.MercanciaExtranjeraDetalle med ON m.idMovimiento = med.Movimiento_idMovimiento';

		window.open('consultarImportacion/?condicion='+condicion+'&join='+join,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
}