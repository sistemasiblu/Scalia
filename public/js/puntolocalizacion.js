function cargarEstanteDependencia(idDependencia, numeroEstante)
{
  var token = document.getElementById('token').value;

    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idDependencia': idDependencia, 'numeroEstante' : numeroEstante},
            url:   'http://'+location.host+'/cargarEstanteDependencia/',
            type:  'post',
            beforeSend: function(){
                //Lo que se hace antes de enviar el formulario
                },
            success: function(respuesta){
                $("#botones").html(respuesta['boton']);
                $("#contenidoEstante").html(respuesta['estructura']);
            },
            error:    function(xhr,err){ 
                alert("Error");
            }
        });
}

function ConsultarInformacion(i)
{
  alert(i);
}