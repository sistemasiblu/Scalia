$(document).ready( function () {

    $("#fechaInicialTemporada, #fechaFinalTemporada").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarImportacion(Temporada, fechaInicial, fechaFinal)
{
	if (fechaInicial != '' && fechaFinal == '') 
		alert('Debe llenar la fecha final de la temporada');
	else
	{
		condicion = '';
		join = '';

		if (Temporada != null)
			condicion = condicion + 'comp.Temporada_idTemporada IN ('+Temporada+')';

		if (fechaInicial != '' && fechaFinal != '')
			condicion = condicion + ((condicion !='' && fechaInicial !='') ? ' and ' : '') + 'fechaInicialTemporada >= "'+fechaInicial+'" and fechaInicialTemporada <= "'+fechaFinal+'"';

		window.open('consultarTemporada/?condicion='+condicion,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
}