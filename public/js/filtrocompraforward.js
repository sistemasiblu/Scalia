$(document).ready( function () {

    $("#fechaInicialCompra, #fechaFinalCompra, #fechaInicialForward, #fechaFinalForward").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarCompraForward(Forward, Compra, FechaInicioCompra, FechaFinCompra, FechaInicioForward, FechaFinForward, filtroCompraForward, visualizacionCompraForward)
{
	if (FechaInicioCompra != '' && FechaFinCompra == '' ||  FechaInicioForward != '' && FechaFinForward == '') 
		alert('Debe llenar el campo "Hasta" en el Forward o en la Compra.');
	else
	{
		condicion = '';

		if (Forward != 0)
			condicion = condicion + 'idForward = '+Forward+'';

		if (Compra !=0) 
			condicion = condicion + ((condicion !='' && Compra !=0) ? ' and ' : '') + 'idCompra = "'+Compra+'"';
		
		if (FechaInicioCompra != '' && FechaFinCompra != '')
			condicion = condicion + ((condicion !='' && FechaInicioCompra !='') ? ' and ' : '') + 'fechaCompra >= "'+FechaInicioCompra+'" and fechaCompra <= "'+FechaFinCompra+'"';

		if (FechaInicioForward != '' && FechaFinForward != '')
			condicion = condicion + ((condicion !='' && FechaInicioForward !='') ? ' and ' : '') + 'fechaNegociacionForward >= "'+FechaInicioForward+'" and fechaVencimientoForward <= "'+FechaFinForward+'"';

		window.open('consultarCompraForward/?condicion='+condicion+'&filtroCompraForward='+filtroCompraForward+'&visualizacionCompraForward='+visualizacionCompraForward,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
}