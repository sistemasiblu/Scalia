//saber ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);


function buscarSubSerie(idSerie, registro, idSelect)
{
    var token = document.getElementById('token').value;

    $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': token},
            url: '/buscarSubSerie/',
            type: 'POST',
            dataType: 'JSON',
            method: 'GET',
            data: {Serie_idSerie: idSerie},
            success: function(respuesta){

                reg = registro.replace("Serie_idSerie", '', registro);
                    
                var select = document.getElementById('SubSerie_idSubSerie'+reg);
                    
                select.options.length = 0;
                var option = '';

                option = document.createElement('option');
                option.value = null;
                option.text = 'Seleccione...';
                select.appendChild(option);

                for (var i = 0; i < respuesta.length ; i++)
                {
                    option = document.createElement('option');
                    option.value = respuesta[i]["idSubSerie"];
                    option.text = respuesta[i]["nombreSubSerie"];
                    option.selected = (idSelect ==  respuesta[i]["idSubSerie"] ? true : false);
                    select.appendChild(option);
                }

            },
            error:    function(xhr,err){ 
                alert("Error");
            }
        });
}

function buscarDocumento(idSubSerie, registro, idSelect)
{
    var token = document.getElementById('token').value;

    $.ajax({
            async: true,
            headers: {'X-CSRF-TOKEN': token},
            url: '/buscarDocumento/',
            type: 'POST',
            dataType: 'JSON',
            method: 'GET',
            data: {SubSerie_idSubSerie: idSubSerie},
            success: function(respuesta){
                reg = registro.replace("SubSerie_idSubSerie", '', registro);
                    
                var select = document.getElementById('Documento_idDocumento'+reg);
                    
                select.options.length = 0;
                var option = '';

                option = document.createElement('option');
                option.value = null;
                option.text = 'Seleccione...';
                select.appendChild(option);

                for (var i = 0; i < respuesta.length ; i++)
                {
                    option = document.createElement('option');
                    option.value = respuesta[i]["idDocumento"];
                    option.text = respuesta[i]["nombreDocumento"];
                    option.selected = (idSelect ==  respuesta[i]["idDocumento"] ? true : false);
                    select.appendChild(option);
                }

            },
            error:    function(xhr,err){ 
                alert("Error");
            }
        });
}
