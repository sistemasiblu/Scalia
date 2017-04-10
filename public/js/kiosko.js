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