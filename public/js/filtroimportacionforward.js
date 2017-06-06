$(document).ready( function () {

    $("#fechaInicialCompra, #fechaFinalCompra, #fechaInicialEmbarque, #fechaFinalEmbarque").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarImportacion(Temporada, Compra, Cliente, Proveedor, FechaInicioCompra, FechaFinCompra, Documento)
{
	if (FechaInicioCompra != '' && FechaFinCompra == '' || FechaInicioCompra == '' && FechaFinCompra != '') 
		alert('Verifique que las fechas estén llenas');
	else
	{
		condicion = '';

		if (Temporada != 0)
			condicion = condicion + 'comp.Temporada_idTemporada = "'+Temporada+'"';

		if (Compra !=0) 
			condicion = condicion + ((condicion !='' && Compra !=0) ? ' and ' : '') + 'numeroCompra = "'+Compra+'"';
		
		if (Cliente != 0)
			condicion = condicion + ((condicion !='' && Cliente !=0) ? ' and ' : '') + 'Tercero_idCliente = "'+Cliente+'"';

		if (Proveedor != 0)
			condicion = condicion + ((condicion !='' && Proveedor !=0) ? ' and ' : '') + 'Tercero_idProveedor = "'+Proveedor+'"';

		if (FechaInicioCompra != '' && FechaFinCompra != '')
			condicion = condicion + ((condicion !='' && FechaInicioCompra !='') ? ' and ' : '') + 'fechaCompra >= "'+FechaInicioCompra+'" and fechaCompra <= "'+FechaFinCompra+'"';

		if (Documento != 0)
			condicion = condicion + 'DocumentoImportacion_idDocumentoImportacion = "'+Documento+'"';

		window.open('consultarImportacionForward/?condicion='+condicion,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
}