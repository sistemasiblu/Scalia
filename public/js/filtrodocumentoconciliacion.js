$(document).ready( function () {

    $("#fechaElaboracionMovimientoInicial, #fechaElaboracionMovimientoFinal").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarInformacion(Documento, ValorConciliacion, fechaElaboracionMovimientoInicial, fechaElaboracionMovimientoFinal)
{
	condicionDocumento = "";
	condicionValorConciliacion = '';
	condicionFechas = '';

	if(Documento != null)
	{
		condicionDocumento = ' documentoconciliacion.Documento_idDocumento IN('+Documento+') ';
	}

	if(ValorConciliacion != null)
	{
		condicionValorConciliacion = ' idValorConciliacion IN('+ValorConciliacion+') ';
	}

	if(fechaElaboracionMovimientoInicial != '' && fechaElaboracionMovimientoFinal != '')
	{
		condicionFechas = ' (fechaElaboracionMovimiento >= "'+fechaElaboracionMovimientoInicial+'" AND fechaElaboracionMovimiento <= "'+fechaElaboracionMovimientoFinal+'") ';
	}
	else
	{
		alert('Debe seleccionar el rango de fechas de los documentos a conciliar');
		return;
	}

	condicionGeneral = ((condicionValorConciliacion != '' && condicionDocumento != '') ? condicionDocumento+" AND "+condicionValorConciliacion : condicionDocumento+condicionValorConciliacion);
	condicionGeneral += ((condicionGeneral != '' && condicionFechas != '') ? " AND " : "")+condicionFechas;
		
	condicionValorConciliacion = ((condicionValorConciliacion != '' && condicionDocumento != '') ? condicionDocumento+" AND "+condicionValorConciliacion : condicionDocumento+condicionValorConciliacion);
	
	window.open('consultarInformacion/?condicionGeneral='+condicionGeneral+'&condicionValorConciliacion='+condicionValorConciliacion,'_blank','width=2500px, height=700px, scrollbars=yes');
	
}