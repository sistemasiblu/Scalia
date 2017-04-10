
//Obtener ip del servidor
    var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);


function conectar(host, puerto, usuario, clave, bd, motorbd)
{
    var token = document.getElementById('token').value;
    $.ajax({
                headers: {'X-CSRF-TOKEN': token},
                dataType: "json",
                data: {host: host, puerto: puerto, usuario: usuario, clave: clave, bd: bd, motorbd: motorbd},
                url:   ip+'/conexion/',
                type:  'post',
                beforeSend: function(){
                    },
                success: function(respuesta){
                    alert(respuesta);
                },
                error:    function(xhr,err){ 
                    alert("No se ha podido conectar a la base de datos");
                }
            });
}



