function validarCampos(event)
{
   var mensaje = ''
   if($("#nombreEncuestaPublicacion").val() == '')
    {
        $("#nombreEncuestaPublicacion").css('border-bottom','solid 2px red');
        mensaje += 'Debe digitar el nombre de la publicación<br>';
    }
    else
    {
        $("#nombreEncuestaPublicacion").css('border-bottom','solid 2px gray');
    }

    if($("#fechaEncuestaPublicacion").val() == '')
    {
        $("#fechaEncuestaPublicacion").css('border-bottom','solid 2px red');
        mensaje += 'Debe digitar la fecha de la publicación<br>';
    }
    else
    {
        $("#fechaEncuestaPublicacion").css('border-bottom','solid 2px gray');
    }

    if($("#Encuesta_idEncuesta").val() == '')
    {
        $("#Encuesta_idEncuesta").css('border-bottom','solid 2px red');
        mensaje += 'Debe seleccionar la encuesta a publicar<br>';
    }
    else
    {
        $("#Encuesta_idEncuesta").css('border-bottom','solid 2px gray');
    }

   // recorremos cada una de los LI de las preguntas verificando su informacion
    $(".destinatario").each(function (index) 
    { 
        var mensajePna = '';
        // si la pregunta esta vacia
        if($("#nombreEncuestaPublicacionDestino"+index).val() == '')
        {
            $("#nombreEncuestaPublicacionDestino"+index).css('border-bottom','solid 2px red');
            mensajePna += 'Debe digitar el nombre de la persona encuestada<br>';
        }
        else
        {
            $("#nombreEncuestaPublicacionDestino"+index).css('border-bottom','solid 2px gray');
        }


        // si el tipo de respuesta esta vacio
        if($("#correoEncuestaPublicacionDestino"+index).val() == '')
        {
            $("#correoEncuestaPublicacionDestino"+index).css('border-bottom','solid 2px red');
            mensajePna += 'Debe Digitar el correo electrónico<br>';
        }
        else
        {
            $("#correoEncuestaPublicacionDestino"+index).css('border-bottom','solid 2px gray');
        }

        if(mensajePna != '')
            mensaje += 'Destinatario No. '+(index+1)+'<br>' + mensajePna;
    }) ;

    

    if(mensaje != '')
    {
        $("#msj").html(mensaje);
        $("#msj-error").css("display","block");
    
        event.preventDefault();
    }
}

