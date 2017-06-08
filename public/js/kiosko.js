function volverAtras()
{
	javascript:history.back(-1);
}

function imprimirFichaTecnica(referencia, modulo, formato)
{
	if (referencia == '') 
	{
		alert('Debe digitar un número de referencia.');
	}
	else
	{
		window.open('kiosko/'+referencia+'?referencia='+referencia+'&modulo='+modulo+'&formato='+formato,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
	
}

function imprimirOrdenProduccion(referencia, formato)
{
	if (referencia == '') 
	{
		alert('Debe digitar un número de la OP.');
	}
	else
	{
		window.open('kiosko/'+referencia+'?referencia='+referencia+'&formato='+formato,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
}

function imprimirOrdenCompra(referencia, formato)
{
	if (referencia == '') 
	{
		alert('Debe digitar un número de la OC.');
	}
	else
	{
		window.open('kiosko/'+referencia+'?referencia='+referencia+'&formato='+formato,'_blank','width=2500px, height=700px, scrollbars=yes');
	}
	
}

function abrirModalCertificadoLaboral()
{
	$("#modalFiltroCertificado").modal();
}

function generarCertificadoLaboral(formato, email)
{
	if ($("#destinatarioCertificadoLaboral").val() == '' || $("#fechaNacimientoTercero").val() == '') 
	{
		alert('Verifique que los datos estén llenos.');
	}
	else
	{
		if ($("#documentoUsuario").val() == '')  
		{
			var doc = prompt("Digite su número de cédula por favor.", "");
		    if (doc != null) 
		    {
		        $("#documentoUsuario").val(doc);
		    }
		}
		else
		{
			mail = (email != 'noemail' ? 'si' : 'no');
			documentoU = $("#documentoUsuario").val();
			destinatario = $("#destinatarioCertificadoLaboral").val();
			condicion = $("#fechaNacimientoTercero").val();
			window.open('kiosko/'+formato+'?destinatario='+destinatario+'&formato='+formato+'&documentoU='+documentoU+'&condicion='+condicion+'&mail='+mail,'_blank','width=2500px, height=700px, scrollbars=yes');
			$("#documentoUsuario").val('');
	    	$("#modalFiltroCertificado").modal("hide");
		}
	}
}

function abrirModalReciboPago()
{
	$("#modalFiltroRecibo").modal();
}

function generarReciboPago(formato, email)
{
	if ($("#documentoUsuario").val() == '') 
	{
		var doc = prompt("Digite su número de cédula por favor.", "");
	    if (doc != null) 
	    {
	        $("#documentoUsuario").val(doc);
	    }
	}
	else
	{
		documentoU = $("#documentoUsuario").val();
		// Valor del check box de las quincenas seleccionadas
	    var condicion = '';
	    $("#checkgestionhumana input[type='checkbox']" ).each(function()
	    {
	        if($(this).prop('checked'))
	        {
	        	id = $(this).prop('id').split("-"); 
	        	id[1];

	        	dias = diasDelMes(id[1], id[2]);

	        	condicion = condicion + (id[0] == 'Q1' ? '(fechaInicioLiquidacionNomina >= "'+id[2]+'-'+id[1]+'-01" and fechaFinLiquidacionNomina <= "'+id[2]+'-'+id[1]+'-15") or ' : '(fechaInicioLiquidacionNomina >= "'+id[2]+'-'+id[1]+'-16" and fechaFinLiquidacionNomina <= "'+id[2]+'-'+id[1]+'-'+dias+'") or ');
	        }
	    });

	    if (condicion == '') 
	    {
	    	alert('Debe seleccionar una quincena a generar');
	    }
	    else	    	
	    {
	    	mail = (email != 'noemail' ? 'si' : 'no');
			window.open('kiosko/'+formato+'?condicion='+condicion+'&formato='+formato+'&documentoU='+documentoU+'&mail='+mail,'_blank','width=2500px, height=700px, scrollbars=yes');
	    	$("#documentoUsuario").val('');
	    	$("#modalFiltroRecibo").modal("hide");
	    }
	}

}

function diasDelMes(mes, año) 
{
  	return new Date(año || new Date().getFullYear(), mes, 0).getDate();
}