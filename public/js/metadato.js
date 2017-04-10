function validarCampos()
{
	if ($('#tipoMetadato').val() == 'Lista') 
	{
		$('#Lista_idLista').attr('disabled',false);
	}
	else
	{
		$('#Lista_idLista').attr('disabled',true);	
	}

	if ($('#tipoMetadato').val() == 'EleccionUnica' || $('#tipoMetadato').val() == 'EleccionMultiple') 
	{
		$('#opcionMetadato').attr('readonly',false);
	}
	else
	{
		$('#opcionMetadato').attr('readonly',true);	
	}
}