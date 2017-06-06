$(document).ready( function () {

    $("#fechaNegociacionForward, #fechaVencimientoForward").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarImportacion(forward, fechaInicial, fechaFinal)
{
	if (fechaInicial != '' && fechaFinal == '') 
		alert('Debe llenar la fecha de vencimiento');
	else
	{
		condicion = '';
		join = '';

		if (forward != null)
			condicion = condicion + 'f.idForward IN ('+forward+')';

		if (fechaInicial != '' && fechaFinal != '')
			condicion = condicion + ((condicion !='' && fechaInicial !='') ? ' and ' : '') + 'f.fechaNegociacionForward >= "'+fechaInicial+'" and f.fechaVencimientoForward <= "'+fechaFinal+'"';

		window.open('consultarForward/?condicion='+condicion,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
}