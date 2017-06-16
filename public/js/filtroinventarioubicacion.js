$(document).ready( function () {

    $("#fechaInicial, #fechaFinalCompra, #fechaFinal").datetimepicker
    (
        ({
           format: "YYYY-MM-DD"
         })
    );
});

function consultarInventario(Tipo, FechaInicial, FechaFinal)
{
	if (FechaInicial != '' && FechaFinal == '') 
		alert('Debe llenar la fecha final');
	else
	{
		tipoInv = '';
		fechaIni = '';
		fechaFin = '';

		if (Tipo != '')
			tipoInv = Tipo;
		else
		{
			alert('Debe seleccionar un tipo.');
			return;
		}

		if (FechaFinal != '' && Tipo == 'Historias')
		{
			fechaIni = "'"+FechaInicial+"'";
			fechaFin = "'"+FechaFinal+"'";
		}

		if (FechaFinal != '' && Tipo == 'Otros')
		{
			fechaIni = "'"+FechaInicial+"'";
			fechaFin = "'"+FechaFinal+"'";
		}

		window.open('consultarInventarioUbicacion/?tipoInv='+tipoInv+'&fechaIni='+fechaIni+'&fechaFin='+fechaFin,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
}