$(document).ready( function () {

	$("#fechaInicialPresupuesto, #fechaFinalPresupuesto").datetimepicker
	(
		({
           format: "YYYY-MM-DD"
         })
	);
});

function validarFormulario(event)
{
    var route = "http://"+location.host+"/presupuesto";
    var token = $("#token").val();
    var dato0 = document.getElementById('idPresupuesto').value;
    var dato1 = document.getElementById('fechaInicialPresupuesto').value;
    var dato2 = document.getElementById('fechaFinalPresupuesto').value;
    var dato3 = document.getElementById('descripcionPresupuesto').value;
    var dato4 = document.getElementById('DocumentoCRM_idDocumentoCRM').value;
    var datoVendedor = document.querySelectorAll("[name='Tercero_idVendedor[]']");
    var dato5 = [];

    var valor = '';
    var sw = true;
    
    for(var j=0,i=datoVendedor.length; j<i;j++)
    {
        dato5[j] = datoVendedor[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idPresupuesto: dato0,
                fechaInicialPresupuesto: dato1,
                fechaFinalPresupuesto: dato2,
                descripcionPresupuesto: dato3,
                DocumentoCRM_idDocumentoCRM: dato4, 
                Tercero_idVendedor: dato5
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

                (typeof msj.responseJSON.fechaInicialPresupuesto === "undefined" ? document.getElementById('fechaInicialPresupuesto').style.borderColor = '' : document.getElementById('fechaInicialPresupuesto').style.borderColor = '#a94442');

                (typeof msj.responseJSON.fechaFinalPresupuesto === "undefined" ? document.getElementById('fechaFinalPresupuesto').style.borderColor = '' : document.getElementById('fechaFinalPresupuesto').style.borderColor = '#a94442');

                (typeof msj.responseJSON.descripcionPresupuesto === "undefined" ? document.getElementById('descripcionPresupuesto').style.borderColor = '' : document.getElementById('descripcionPresupuesto').style.borderColor = '#a94442');

                (typeof msj.responseJSON.DocumentoCRM_idDocumentoCRM === "undefined" ? document.getElementById('DocumentoCRM_idDocumentoCRM').style.borderColor = '' : document.getElementById('DocumentoCRM_idDocumentoCRM').style.borderColor = '#a94442');

                
                for(var j=0,i=datoVendedor.length; j<i;j++)
                {
                    (typeof respuesta['Tercero_idVendedor'+j] === "undefined" 
                        ? document.getElementById('Tercero_idVendedor'+j).style.borderColor = '' 
                        : document.getElementById('Tercero_idVendedor'+j).style.borderColor = '#a94442');
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