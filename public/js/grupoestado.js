
function validarFormulario(event)
{
    var route = "http://"+location.host+"/grupoestado/";
    var token = $("#token").val();
    var dato1 = document.getElementById('codigoGrupoEstado').value;
    var dato2 = document.getElementById('nombreGrupoEstado').value;

    var datoNombre = document.querySelectorAll("[name='nombreEstadoCRM[]']");
    var datoTipo = document.querySelectorAll("[name='tipoEstadoCRM[]']");
    
    var dato3 = [];
    var dato4 = [];
    
    
    var valor = '';
    var sw = true;
    
    for(var j=0,i=datoNombre.length; j<i;j++)
    {
        dato3[j] = datoNombre[j].value;
        dato4[j] = datoTipo[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                codigoGrupoEstado: dato1,
                nombreGrupoEstado: dato2,
                nombreEstadoCRM: dato3, 
                tipoEstadoCRM: dato4
                },
        success:function(){
            //$("#msj-success").fadeIn();
            console.log(' sin errores');
        },
        error:function(msj){
            var mensaje = '';
            var respuesta = JSON.stringify(msj.responseJSON); 
            
            if(typeof respuesta === "undefined")
            {
                sw = true;
                $("#msj").html('');
                $("#msj-error").fadeOut();
            }
            else
            {
                sw = false;
                respuesta = JSON.parse(respuesta);
                mensaje = '';

                if(respuesta['codigoGrupoEstado'])
                        mensaje += '<li>'+ respuesta['codigoGrupoEstado']+"</li>";
                if(respuesta['nombreGrupoEstado'])
                        mensaje += '<li>'+ respuesta['nombreGrupoEstado']+"</li>";
                
                
                for(var j=0,i=datoNombre.length; j<i;j++)
                {
                    if(respuesta['nombreEstadoCRM'+j])
                        mensaje += '<li>'+ respuesta['nombreEstadoCRM'+j]+"</li>";

                    if(respuesta['tipoEstadoCRM'+j])
                        mensaje += '<li>'+ respuesta['tipoEstadoCRM'+j]+"</li>";
                }

                $("#msj").html('Verifique los siguientes campos:<br>'+mensaje);
                $("#msj-error").fadeIn();
            }

        }
    });

    if(sw === false)
        event.preventDefault();
    
    
}