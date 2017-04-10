$(document).ready( function () {

    $("#fechaInicialCompra, #fechaFinalCompra, #fechaInicialEmbarque, #fechaFinalEmbarque").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarImportacion(Temporada, Compra, Cliente, Proveedor, Puerto, FechaInicioCompra, FechaFinCompra, FechaInicioEmbarque, FechaFinEmabrque, agrupado, bodega)
{
	if (FechaInicioCompra == '') 
	{
		alert('Debe seleccionar al menos la fecha inicial de las compras.');
	}
	else
	{
		if (FechaInicioCompra != '' && FechaFinCompra == '' || FechaInicioEmbarque != '' && FechaFinEmabrque == '') 
			alert('Debe llenar el campo "Hasta" en la Compra o en el Embarque.');
		else
		{
			join = '';
			condicion = '';

			if (Temporada != 0)
				condicion = condicion + 'comp.Temporada_idTemporada = "'+Temporada+'"';

			if (Compra !=0) 
				condicion = condicion + ((condicion !='' && Compra !=0) ? ' and ' : '') + 'numeroCompra = "'+Compra+'"';
			
			if (Cliente != 0)
				condicion = condicion + ((condicion !='' && Cliente !=0) ? ' and ' : '') + 'comp.Tercero_idCliente = "'+Cliente+'"';

			if (Proveedor != 0)
				condicion = condicion + ((condicion !='' && Proveedor !=0) ? ' and ' : '') + 'comp.Tercero_idProveedor = "'+Proveedor+'"';

			if (Puerto != 0)
				condicion = condicion + ((condicion !='' && Puerto !=0) ? ' and ' : '') + 'comp.Ciudad_idPuerto = "'+Puerto+'"';

			if (FechaInicioCompra != '' && FechaFinCompra != '')
				condicion = condicion + ((condicion !='' && FechaInicioCompra !='') ? ' and ' : '') + 'fechaCompra >= "'+FechaInicioCompra+'" and fechaCompra <= "'+FechaFinCompra+'"';

			if (FechaInicioEmbarque != '' && FechaFinEmabrque != '')
				condicion = condicion + ((condicion !='' && FechaInicioEmbarque !='') ? ' and ' : '') + 'fechaRealEmbarque >= "'+FechaInicioEmbarque+'" and fechaRealEmbarque <= "'+FechaFinEmabrque+'"';
			
			if (bodega == 1)
			condicion = condicion + ((condicion != '' && bodega == 1) ? ' and ' : '') + 'idMercanciaExtranjeraDetalle IS NOT NULL';

			if (bodega == 0)
				condicion = condicion + ((condicion != '' && bodega == 0) ? ' and ' : '') + 'idMercanciaExtranjeraDetalle IS NULL';

			join = join + 'LEFT JOIN Iblu.Movimiento m ON ed.facturaEmbarqueDetalle = m.numeroReferenciaExternoMovimiento and m.Documento_idDocumento = 20 LEFT JOIN Iblu.MercanciaExtranjeraDetalle med ON m.idMovimiento = med.Movimiento_idMovimiento';

			// if (bodega == 0)
			// condicion = condicion + ' and bodegaEmbarque = '+bodega;


			condicionfactura = '';

			if (Temporada != 0)
				condicionfactura = condicionfactura + 'Temporada_idTemporada = "'+Temporada+'"';

			if (Cliente != 0)
				condicion = condicion + ((condicion !='' && Cliente !=0) ? ' and ' : '') + 'Pedido.Tercero_idEntrega = "'+Cliente+'"';

			if (Proveedor != 0)
				condicionfactura = condicionfactura + ((condicionfactura !='' && Proveedor !=0) ? ' and ' : '') + 'Tercero_idTercero = "'+Proveedor+'"';

			if (FechaInicioCompra != '')
				condicionfactura = condicionfactura + ((condicionfactura !='' && FechaInicioCompra !='') ? ' and ' : '') + 'fechaElaboracionMovimiento >= "'+FechaInicioCompra+'"';

				window.open('consultarImportacionDetallado?condicion='+condicion+'&condicionFactura='+condicionfactura+'&agrupado='+agrupado+'&join='+join,'_blank','width=2500px, height=700px, scrollbars=yes');
		}
	}
}