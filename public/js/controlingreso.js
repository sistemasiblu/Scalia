//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);


// function validarFormulario(event)
// {
//     var route = "http://"+location.host+"/controlingreso";
//     var token = $("#token").val();
//     var dato0 = document.getElementById('idControlIngreso').value;
//     var dato1 = document.getElementById('TipoIdentificacion_idTipoIdentificacion').value;
//     var dato2 = document.getElementById('numeroDocumentoVisitanteControlIngreso').value;
//     var dato3 = document.getElementById('nombreVisitanteControlIngreso').value;
//     var dato4 = document.getElementById('apellidoVisitanteControlIngreso').value;
//     var dato5 = document.getElementById('Tercero_idResponsable').value;
//     var datoDispositivo = document.querySelectorAll("[name='Dispositivo_idDispositivo[]']");
//     var datoMarca = document.querySelectorAll("[name='Marca_idMarca[]']");
//     var datoRetiro = document.querySelectorAll("[name='retiraDispositivoControlIngresoDetalle[]']");
//     var datoObservacion = document.querySelectorAll("[name='observacionControlIngresoDetalle[]']");
//     var dato6 = [];
//     var dato7 = [];
//     var dato8 = [];
//     var dato9 = [];

//     var valor = '';
//     var sw = true;

//     for(var j=0,i=datoDispositivo.length; j<i;j++)
//     {
//         dato6[j] = datoDispositivo[j].value;
//     }

//     for(var j=0,i=datoMarca.length; j<i;j++)
//     {
//         dato7[j] = datoMarca[j].value;
//     }

//     for(var j=0,i=datoRetiro.length; j<i;j++)
//     {
//         dato8[j] = datoRetiro[j].value;
//     }

//     for(var j=0,i=datoObservacion.length; j<i;j++)
//     {
//         dato9[j] = datoObservacion[j].value;
//     }

//     $.ajax({
//         async: false,
//         url:route,
//         headers: {'X-CSRF-TOKEN': token},
//         type: 'POST',
//         dataType: 'json',
//         data: {respuesta: 'falso',
//                 idControlIngreso: dato0,
//                 TipoIdentificacion_idTipoIdentificacion: dato1,
//                 numeroDocumentoVisitanteControlIngreso: dato2,
//                 nombreVisitanteControlIngreso: dato3,
//                 apellidoVisitanteControlIngreso: dato4,
//                 Tercero_idResponsable: dato5,
//                 Dispositivo_idDispositivo: dato6,
//                 Marca_idMarca: dato7,
//                 retiraDispositivoControlIngresoDetalle: dato8,
//                 observacionControlIngresoDetalle: dato9,

//                 },
//         success:function(){
//             //$("#msj-success").fadeIn();
//             //console.log(' sin errores');
//         },
//         error:function(msj){
//             var mensaje = '';
//             var respuesta = JSON.stringify(msj.responseJSON); 
//             if(typeof respuesta === "undefined")
//             {
//                 sw = false;
//                 $("#msj").html('');
//                 $("#msj-error").fadeOut();
//             }
//             else
//             {
//                 sw = true;
//                 respuesta = JSON.parse(respuesta);

//                 (typeof msj.responseJSON.TipoIdentificacion_idTipoIdentificacion === "undefined" ? document.getElementById('TipoIdentificacion_idTipoIdentificacion').style.borderColor = '' : document.getElementById('TipoIdentificacion_idTipoIdentificacion').style.borderColor = '#a94442');

//                 (typeof msj.responseJSON.numeroDocumentoVisitanteControlIngreso === "undefined" ? document.getElementById('numeroDocumentoVisitanteControlIngreso').style.borderColor = '' : document.getElementById('numeroDocumentoVisitanteControlIngreso').style.borderColor = '#a94442');

//                 (typeof msj.responseJSON.nombreVisitanteControlIngreso === "undefined" ? document.getElementById('nombreVisitanteControlIngreso').style.borderColor = '' : document.getElementById('nombreVisitanteControlIngreso').style.borderColor = '#a94442');

//                 (typeof msj.responseJSON.apellidoVisitanteControlIngreso === "undefined" ? document.getElementById('apellidoVisitanteControlIngreso').style.borderColor = '' : document.getElementById('apellidoVisitanteControlIngreso').style.borderColor = '#a94442');

//                 (typeof msj.responseJSON.Tercero_idResponsable === "undefined" ? document.getElementById('Tercero_idResponsable').style.borderColor = '' : document.getElementById('Tercero_idResponsable').style.borderColor = '#a94442');

//                 for(var j=0,i=datoDispositivo.length; j<i;j++)
//                 {
//                     (typeof respuesta['Dispositivo_idDispositivo'+j] === "undefined" ? document.getElementById('Dispositivo_idDispositivo'+j).style.borderColor = '' : document.getElementById('Dispositivo_idDispositivo'+j).style.borderColor = '#a94442');
//                 }

//                 for(var j=0,i=datoMarca.length; j<i;j++)
//                 {
//                     (typeof respuesta['Marca_idMarca'+j] === "undefined" ? document.getElementById('Marca_idMarca'+j).style.borderColor = '' : document.getElementById('Marca_idMarca'+j).style.borderColor = '#a94442');
//                 }

//                 for(var j=0,i=datoObservacion.length; j<i;j++)
//                 {
//                     (typeof respuesta['observacionControlIngresoDetalle'+j] === "undefined" ? document.getElementById('observacionControlIngresoDetalle'+j).style.borderColor = '' : document.getElementById('observacionControlIngresoDetalle'+j).style.borderColor = '#a94442');
//                 }

//                 var mensaje = 'Por favor verifique los siguientes valores <br><ul>';
//                 $.each(respuesta,function(index, value){
//                     mensaje +='<li>' +value+'</li><br>';
//                 });
//                 mensaje +='</ul>';
               
//                 $("#msj").html(mensaje);
//                 $("#msj-error").fadeIn();
//             }

//         }
//     });

//     if(sw === true)
//         event.preventDefault();
// }

function consultarControlIngreso(numeroDocumento)
{
    // Consulto la fecha y hora actual
    hoy = new Date(); 
    fechaActual = hoy.getFullYear() + "-" + (hoy.getMonth() +1) + "-" + hoy.getDate() + " " + hoy.getHours() + ':' + hoy.getMinutes() + ':' + hoy.getSeconds();

    var token = document.getElementById('token').value;

        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'numeroDocumento': numeroDocumento},
            url:   'http://'+location.host+'/consultarControlIngreso/',
            type:  'post',
            beforeSend: function(){
                //Lo que se hace antes de enviar el formulario
                },
            success: function(respuesta){

                    // Limpio todos los campos del formulario
                    // Limpio el div contenedor de la multiregistro
                    document.getElementById("contenedor_control").innerHTML = '';

                    // Pongo invisible el titulo del checkbox que valida si el dispositivo fue retirado
                    // Y los checkbox de la multiregistro
                    $("#check").css("display", "none");

                    for (var i = 0; i < window.parent.control.contador; i++) 
                    {
                        // $("#retiraDispositivoControlIngresoDetalle"+i).parent().css("display", "none");
                        $("#retiraDispositivoControlIngresoDetalleC"+i).css("display", "none");
                    }

                    $("#accionControlIngreso").val('ENTRADA');
                    $("#entrada").css("display", "block");
                    $("#fechaIngresoControlIngreso").val(fechaActual);
                    $("#fechaSalidaControlIngreso").val('');
                    $("#Tercero_idResponsable option[value=0]").prop("selected", true).trigger("chosen:updated");
                    $("#nombreVisitanteControlIngreso").val('');
                    $("#apellidoVisitanteControlIngreso").val('');
                    $("#apellidoVisitanteControlIngreso").val('');
                    $("#dependenciaControlIngreso").val('');
                    $("#observacionControlIngreso").val('');
                    $("#salida").css("display", "none");

                // *************************
                // S A L I D A
                // *************************
                if (respuesta.encabezado['fechaSalidaControlIngreso'] == '0000-00-00 00:00:00') 
                {

                    $("#entrada").css("display", "block");
                    $("#salida").css("display", "block");
                    $("#idControlIngreso").val(respuesta.encabezado['idControlIngreso']);
                    $("#TipoIdentificacion_idTipoIdentificacion option[value="+respuesta.encabezado['TipoIdentificacion_idTipoIdentificacion']+"]").prop("selected", true).trigger("chosen:updated");
                    $("#nombreVisitanteControlIngreso").val(respuesta.encabezado['nombreVisitanteControlIngreso']);
                    $("#apellidoVisitanteControlIngreso").val(respuesta.encabezado['apellidoVisitanteControlIngreso']);
                    $("#Tercero_idResponsable option[value="+respuesta.encabezado['Tercero_idResponsable']+"]").prop("selected", true).trigger("chosen:updated");
                    $("#dependenciaControlIngreso").val(respuesta.encabezado['dependenciaControlIngreso']);
                    $("#fechaIngresoControlIngreso").val(respuesta.encabezado['fechaIngresoControlIngreso']);
                    $("#fechaSalidaControlIngreso").val(fechaActual);
                    $("#observacionControlIngreso").val(respuesta.encabezado['observacionControlIngreso']);
                    $("#accionControlIngreso").val('SALIDA');

                    var idDetalle = new Array();
                    var idEncabezado = new Array();
                    var dispositivo = new Array();
                    var marca = new Array();
                    var referencia = new Array();
                    var observacion = new Array();

                    // Limpio el div contenedor de la multiregistro y agrego los nuevos valores
                    document.getElementById("contenedor_control").innerHTML = '';
                    // Pongo visible el titulo del checkbox que valida si el dispositivo fue retirado
                    $("#check").css("display", "block");
                    // Desactivo el onclick para que no se puedan agregar registros si la persona esta saliendo
                    $("#agregarRegistro").prop('onclick',null).off('click'); 

                    for (var i = 0; i < respuesta.detalle.length; i++) 
                    {
                        idDetalle[i] = respuesta.detalle[i]["idControlIngresoDetalle"];
                        idEncabezado[i] = respuesta.detalle[i]["ControlIngreso_idControlIngreso"];
                        dispositivo[i] = respuesta.detalle[i]["Dispositivo_idDispositivo"];
                        marca[i] = respuesta.detalle[i]["Marca_idMarca"];
                        referencia[i] = respuesta.detalle[i]["referenciaDispositivoControlIngresoDetalle"];
                        observacion[i] = respuesta.detalle[i]["observacionControlIngresoDetalle"];
                        
                        var valores = new Array(idEncabezado[i],dispositivo[i],marca[i],referencia[i],observacion[i],0,idDetalle[i]);
                        window.parent.control.agregarCampos(valores,'A'); 
                        $("#Dispositivo_idDispositivo"+i+" option:not(:selected)").prop('disabled',true).css("background-color", "#EEEEEE");
                        $("#Marca_idMarca"+i+" option:not(:selected)").prop('disabled',true).css("background-color", "#EEEEEE");
                        $("#referenciaDispositivoControlIngresoDetalle"+i).prop('readonly', true);
                        $("#retiraDispositivoControlIngresoDetalle"+i).parent().css("display", "inline-block");
                        $("#retiraDispositivoControlIngresoDetalleC"+i).css("display", "inline-block");
                        $("#eliminarRegistro"+i).prop('onclick',null).off('click'); 

                    }  
                }
                else
                {
                    // *************************
                    // E N T R A D A
                    // *************************

                    // Limpio el div contenedor de la multiregistro
                    document.getElementById("contenedor_control").innerHTML = '';

                    // Pongo invisible el titulo del checkbox que valida si el dispositivo fue retirado
                    // Y los checkbox de la multiregistro
                    $("#check").css("display", "none");

                    for (var i = 0; i < window.parent.control.contador; i++) 
                    {
                        // $("#retiraDispositivoControlIngresoDetalle"+i).parent().css("display", "none");
                        $("#retiraDispositivoControlIngresoDetalleC"+i).css("display", "none");
                    }

                    $("#TipoIdentificacion_idTipoIdentificacion option[value="+respuesta.encabezado['TipoIdentificacion_idIdentificacion']+"]").prop("selected", true).trigger("chosen:updated");
                    $("#nombreVisitanteControlIngreso").val(respuesta.encabezado['nombreTercero']);
                    $("#apellidoVisitanteControlIngreso").val(respuesta.encabezado['apellidoTercero']);
                    $("#accionControlIngreso").val('ENTRADA');
                    $("#fechaIngresoControlIngreso").css("display", "block");
                    $("#fechaIngresoControlIngreso").val(fechaActual);
                    $("#fechaSalidaControlIngreso").val('');
                    $("#Tercero_idResponsable option[value=0]").prop("selected", true).trigger("chosen:updated");
                    $("#entrada").css("display", "block");
                    $("#salida").css("display", "none");
                    $("#dependenciaControlIngreso").val('');
                    $("#observacionControlIngreso").val('');
                }
            },
            error:    function(xhr,err){ 
                alert("Error");
            }
        });
}

function consultarCentroTrabajo(idTercero)
{
    // Le asigno al campo oculto el id del Tercero
    $("#Tercero_idResponsable").val(idTercero);

    var token = document.getElementById('token').value;

        $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idTercero': idTercero},
            url:   'http://'+location.host+'/llenarCentroTrabajo/',
            type:  'post',
            beforeSend: function(){
                //Lo que se hace antes de enviar el formulario
                },
            success: function(respuesta){
                //lo que se si el destino devuelve algo
                $("#dependenciaControlIngreso").val(respuesta["nombreCentroTrabajo"]); 
            },
            error:    function(xhr,err){ 
                alert("Error");
            }
        });
}