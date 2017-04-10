//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

function validarFormulario(event)
{
    var route = "http://"+location.host+"/documento";
    var token = $("#token").val();
    var dato0 = document.getElementById('idDocumento').value;
    var dato1 = document.getElementById('codigoDocumento').value;
    var dato2 = document.getElementById('nombreDocumento').value;
    var dato3 = document.getElementById('directorioDocumento').value;
    var dato4 = document.getElementById('tipoDocumento').value;

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idDocumento: dato0,
                codigoDocumento: dato1,
                nombreDocumento: dato2,
                directorioDocumento: dato3,
                tipoDocumento: dato4
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

                (typeof msj.responseJSON.codigoDocumento === "undefined" ? document.getElementById('codigoDocumento').style.borderColor = '' : document.getElementById('codigoDocumento').style.borderColor = '#a94442');

                (typeof msj.responseJSON.nombreDocumento === "undefined" ? document.getElementById('nombreDocumento').style.borderColor = '' : document.getElementById('nombreDocumento').style.borderColor = '#a94442');

                (typeof msj.responseJSON.directorioDocumento === "undefined" ? document.getElementById('directorioDocumento').style.borderColor = '' : document.getElementById('directorioDocumento').style.borderColor = '#a94442');

                (typeof msj.responseJSON.tipoDocumento === "undefined" ? document.getElementById('tipoDocumento').style.borderColor = '' : document.getElementById('tipoDocumento').style.borderColor = '#a94442');

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

function consultarTablaVista(idSistema, tablaDocumento)
{
	 var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idSistema: idSistema},
            url:   ip+'/conexionDocumento/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
				var tablas = respuesta[0];
				var nombreDB = respuesta[1];
            	
            	var select = document.getElementById('tablaDocumento');
	               
                select.options.length = 0;
                var option = '';

                option = document.createElement('option');
                option.value = null;
                option.text = 'Seleccione la tabla';
                select.appendChild(option);


            	for (var i = 0; i < tablas.length ; i++)
            	{
                    option = document.createElement('option');
                    option.value = tablas[i]["Tables_in_"+nombreDB];
                    option.text = tablas[i]["Tables_in_"+nombreDB];
                    option.selected = (tablaDocumento ==  tablas[i]["Tables_in_"+nombreDB] ? true : false);
                    select.appendChild(option);		               
	            }
            },
            error:    function(xhr,err){ 
                alert("No se ha podido conectar a la base de datos");
            }
        });
}


function consultarCampos(idSistema, nombreTabla)
{
	 var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idSistema: idSistema, nombreTabla: nombreTabla},
            url:   ip+'/conexionDocumentoCampos/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
                var valores = new Array();
                var nombres = new Array();
                
            	for (var i = 0; i < respuesta.length ; i++)
            	{
                    valores[i] = respuesta[i]["Campo"];
                    nombres[i] = respuesta[i]["Campo"];
                }

                respuesta = [valores,nombres];
	            documentopropiedades.opciones[4] = respuesta;

	            // alert(respuesta);
	            
            },
            error:    function(xhr,err){ 
                alert("No se ha podido conectar a la base de datos");
            }
        });
}

function llenarCampos(idSistema, nombreTabla, campo, reg)
{
     var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idSistema: idSistema, nombreTabla: nombreTabla},
            url:   ip+'/conexionDocumentoCampos/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){

                var tablas = respuesta;
               
                // for (var cont = 0; cont < documentopropiedades.contador; cont++) 
                // {                
                    var select = document.getElementById('campoDocumentoPropiedad'+reg);
                       
                    select.options.length = 0;
                    var option = '';

                    option = document.createElement('option');
                    option.value = null;
                    option.text = 'Seleccione...';
                    select.appendChild(option);

                    for (var i = 0; i < tablas.length ; i++)
                    {
                        option = document.createElement('option');
                        option.value = tablas[i]["Campo"];
                        option.text = tablas[i]["Campo"];
                        option.selected = (campo ==  tablas[i]["Campo"] ? true : false);
                        select.appendChild(option);                    
                    }
                // }

            },
            error:    function(xhr,err){ 
                alert("No se ha podido conectar a la base de datos");
            }
        });
}

function duplicarDocumento(idDocumento)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {idDocumento: idDocumento},
            url:   ip+'/duplicarDocumento/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta){
                alert(respuesta);
                location.reload(); //Recargar la página
            },
            error:    function(xhr,err){ 
                alert("No se ha podido duplicar el documento");
            }
        });
}