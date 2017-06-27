function validarFormulario(event)
{
    var route = "http://"+location.host+"/agendapermiso";
    var token = $("#token").val();
    var dato0 = document.getElementById('idAgendaPermiso').value;
    var dato1 = document.getElementById('Users_idAutorizado').value;
    var datoUsuario = document.querySelectorAll("[name='Users_idPropietario[]']");
    var datoCategoria = document.querySelectorAll("[name='CategoriaAgenda_idCategoriaAgenda[]']");
    var dato2 = [];
    var dato3 = [];
    
    var valor = '';
    var sw = true;
    
    for(var j=0,i=datoUsuario.length; j<i;j++)
    {
        dato2[j] = datoUsuario[j].value;
    }

    for(var j=0,i=datoCategoria.length; j<i;j++)
    {
        dato3[j] = datoCategoria[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idCargo: dato0,
                Users_idAutorizado: dato1,
                Users_idPropietario: dato2,
                CategoriaAgenda_idCategoriaAgenda: dato3
                },
        success:function(){
            //$("#msj-success").fadeIn();
            //console.log(' sin errores');
        },
        error:function(msj){
            var mensaje = '';
            var respuesta = JSON.stringify(msj.responseJSON); 
            if(typeof respuesta === "undefined")
            {
                sw = false;
                $("#msj").html('');
                $("#msj-error").fadeOut();
            }
            else
            {
                sw = true;
                respuesta = JSON.parse(respuesta);

                (typeof msj.responseJSON.Users_idAutorizado === "undefined" ? document.getElementById('Users_idAutorizado').style.borderColor = '' : document.getElementById('Users_idAutorizado').style.borderColor = '#a94442');

                for(var j=0,i=datoUsuario.length; j<i;j++)
                {
                    (typeof respuesta['Users_idPropietario'+j] === "undefined" ? document.getElementById('Users_idPropietario'+j).style.borderColor = '' : document.getElementById('Users_idPropietario'+j).style.borderColor = '#a94442');
                }

                for(var j=0,i=datoCategoria.length; j<i;j++)
                {
                    (typeof respuesta['CategoriaAgenda_idCategoriaAgenda'+j] === "undefined" ? document.getElementById('CategoriaAgenda_idCategoriaAgenda'+j).style.borderColor = '' : document.getElementById('CategoriaAgenda_idCategoriaAgenda'+j).style.borderColor = '#a94442');
                }


                var mensaje = 'Por favor verifique los siguientes valores <br><ul>';
                $.each(respuesta,function(index, value){
                    mensaje +='<li>' +value+'</li><br>';
                });
                mensaje +='</ul>';
               
                $("#msj").html(mensaje);
                $("#msj-error").fadeIn();
            }

        }
    });

    if(sw === true)
        event.preventDefault();
}
