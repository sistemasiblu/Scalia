function mostrarCamposTipoUbicacion(tipo)
{
    if(tipo == 'historiaslaborales')
    {
        $("#documento").css('display','block');
        $("#nombre").css('display','block');

        $("#descripcion").css('display','none');
        $("#folio").css('display','none');
        $("#fechaInicial").css('display','none');
        $("#fechaFinal").css('display','none');
    }
    else if(tipo == 'otros')
    {
        $("#descripcion").css('display','block');
        $("#folio").css('display','block');
        $("#fechaInicial").css('display','block');
        $("#fechaFinal").css('display','block');

        $("#documento").css('display','none');
        $("#nombre").css('display','none');
    }
    else
    {
        $("#documento").css('display','none');
        $("#nombre").css('display','none');
        $("#descripcion").css('display','none');
        $("#folio").css('display','none');
        $("#fechaInicial").css('display','none');
        $("#fechaFinal").css('display','none');
    }
}