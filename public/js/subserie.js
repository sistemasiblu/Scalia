function abrirModalRol()
{
	$('#myModalRol').modal();
}

function abrirModalDocumento()
{
    $('#myModalDocumento').modal();
}

function validarFormulario(event)
{
    var route = "http://"+location.host+"/subserie";
    var token = $("#token").val();
    var dato0 = document.getElementById('idSubSerie').value;
    var dato1 = document.getElementById('codigoSubSerie').value;
    var dato2 = document.getElementById('nombreSubSerie').value;
    var dato3 = document.getElementById('directorioSubSerie').value;
    var dato4 = document.getElementById('Serie_idSerie').value;
    var datoDocumento = document.querySelectorAll("[name='Documento_idDocumento[]']");
    var dato5 = [];

    var valor = '';
    var sw = true;

    for(var j=0,i=datoDocumento.length; j<i;j++)
    {
        dato5[j] = datoDocumento[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idSubSerie: dato0,
                codigoSubSerie: dato1,
                nombreSubSerie: dato2,
                directorioSubSerie: dato3,
                Serie_idSerie: dato4,
                Documento_idDocumento: dato5

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

                (typeof msj.responseJSON.codigoSubSerie === "undefined" ? document.getElementById('codigoSubSerie').style.borderColor = '' : document.getElementById('codigoSubSerie').style.borderColor = '#a94442');

                (typeof msj.responseJSON.nombreSubSerie === "undefined" ? document.getElementById('nombreSubSerie').style.borderColor = '' : document.getElementById('nombreSubSerie').style.borderColor = '#a94442');

                (typeof msj.responseJSON.directorioSubSerie === "undefined" ? document.getElementById('directorioSubSerie').style.borderColor = '' : document.getElementById('directorioSubSerie').style.borderColor = '#a94442');

                (typeof msj.responseJSON.Serie_idSerie === "undefined" ? document.getElementById('Serie_idSerie').style.borderColor = '' : document.getElementById('Serie_idSerie').style.borderColor = '#a94442');

                for(var j=0,i=datoDocumento.length; j<i;j++)
                {
                    (typeof respuesta['Documento_idDocumento'+j] === "undefined" ? document.getElementById('Documento_idDocumento'+j).style.borderColor = '' : document.getElementById('Documento_idDocumento'+j).style.borderColor = '#a94442');

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