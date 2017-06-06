function validarFormulario(event)
{
  
    var route = "http://"+location.host+"/dependencia";
    var token = $("#token").val();
    var dato0 = document.getElementById('idDependencia').value;
    var dato1 = document.getElementById('codigoDependencia').value;
    var dato2 = document.getElementById('nombreDependencia').value;
    var dato3 = document.getElementById('abreviaturaDependencia').value;
    var dato4 = document.getElementById('directorioDependencia').value;
    var datoLocalizacion = document.querySelectorAll("[name='estadoDependenciaLocalizacion[]']");
    var dato5 = [];
    
    var valor = '';
    var sw = true;
    
    for(var j=0,i=datoLocalizacion.length; j<i;j++)
    {
        dato5[j] = datoLocalizacion[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idDependencia: dato0,
                codigoDependencia: dato1,
                nombreDependencia: dato2,
                abreviaturaDependencia: dato3,
                directorioDependencia: dato4, 
                estadoDependenciaLocalizacion: dato5
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

                (typeof msj.responseJSON.codigoDependencia === "undefined" ? document.getElementById('codigoDependencia').style.borderColor = '' : document.getElementById('codigoDependencia').style.borderColor = '#a94442');

                (typeof msj.responseJSON.nombreDependencia === "undefined" ? document.getElementById('nombreDependencia').style.borderColor = '' : document.getElementById('nombreDependencia').style.borderColor = '#a94442');

                (typeof msj.responseJSON.abreviaturaDependencia === "undefined" ? document.getElementById('abreviaturaDependencia').style.borderColor = '' : document.getElementById('abreviaturaDependencia').style.borderColor = '#a94442');

                (typeof msj.responseJSON.directorioDependencia === "undefined" ? document.getElementById('directorioDependencia').style.borderColor = '' : document.getElementById('directorioDependencia').style.borderColor = '#a94442');

                for(var j=0,i=datoLocalizacion.length; j<i;j++)
                {
                    (typeof respuesta['estadoDependenciaLocalizacion'+j] === "undefined" ? document.getElementById('estadoDependenciaLocalizacion'+j).style.borderColor = '' : document.getElementById('estadoDependenciaLocalizacion'+j).style.borderColor = '#a94442');
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

function abrirModalRol()
{
	$("#myModalRol").modal()
}

function generarUbicaciones()
{
	estantes = $("#estanteDependenciaPuntoLocalizaCion").val();
	niveles = $("#nivelDependenciaPuntoLocalizacion").val();
	secciones = $("#seccionDependenciaPuntoLocalizacion").val();

	if ($("#codigoDependencia").val() == '') 
	{
		alert('Debe llenar el codigo de la dependencia.');
		return;
	}

    reg = localizacion.contador;
    reg = reg - 1;
    regActEst = $("#numeroEstanteDependenciaLocalizacion"+reg).val();


    est = 0;

	for (var i = 1; i <= estantes; i++) 
	{
        est = (est > 0 ? est+1 : parseFloat(regActEst) + 1);
        valEst = (est > 0 ? est : i);
		for (var j = 1; j <= niveles; j++) 
		{
			for (var k = 1; k <= secciones; k++) 
			{
				// El String().slice(-2) funciona igual que el strpad de php. En este caso llena con 2 digitos a la izquierda 
				codigo = $("#codigoDependencia").val()+''+String("0" + valEst).slice(-2)+''+String("0" + j).slice(-2)+''+String("0" + k).slice(-2);
				descripcion = $("#codigoDependencia").val()+' '+String("0" + valEst).slice(-2)+' '+String("0" + j).slice(-2)+' '+String("0" + k).slice(-2);
				var valores = new Array(0, String("0" + valEst).slice(-2), String("0" + j).slice(-2), String("0" + k).slice(-2), codigo, descripcion, 'Activo', 0);
				localizacion.agregarCampos(valores,'A');	
			}
		}
	}
}