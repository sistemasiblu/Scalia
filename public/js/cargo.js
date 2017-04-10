function validarFormulario(event)
{
    
    //Antes de validar los campos del formulario, validamos los campos de porcentajes, llamando a la funcion que los verifica
    // Esta funcion nos debe decir si a validacion es correcta o incorrecta y la almacenamos en una variable
    var PorcentajeTotal = validacionPorcentajePeso();
    if(PorcentajeTotal == false)
    {
        alert ('la sumatoria de %Peso Educacion no debe ser mayor o menor que 100 ');
        // si hay porcentajes malos, evitamos que el formulario se cierre
        event.preventDefault();
    }



    var route = "http://"+location.host+"/cargo";
    var token = $("#token").val();
    var dato0 = document.getElementById('idCargo').value;
    var dato1 = document.getElementById('codigoCargo').value;
    var dato2 = document.getElementById('nombreCargo').value;
    var dato3 = document.getElementById('salarioBaseCargo').value;
    var datoTarea = document.querySelectorAll("[name='ListaGeneral_idTareaAltoRiesgo[]']");
    var datoVacuna = document.querySelectorAll("[name='ListaGeneral_idVacuna[]']");
    var datoElemento = document.querySelectorAll("[name='ElementoProteccion_idElementoProteccion[]']");
    var datoExamen = document.querySelectorAll("[name='FrecuenciaMedicion_idFrecuenciaMedicion[]']");
    var datoTipoExamen = document.querySelectorAll("[name='TipoExamenMedico_idTipoExamenMedico[]']");
    var dato4 = [];
    var dato5 = [];
    var dato6 = [];
    var dato7 = [];
    var dato8 = [];
    
    var valor = '';
    var sw = true;
    
    for(var j=0,i=datoTarea.length; j<i;j++)
    {
        dato4[j] = datoTarea[j].value;
    }

    for(var j=0,i=datoVacuna.length; j<i;j++)
    {
        dato5[j] = datoVacuna[j].value;
    }

    for(var j=0,i=datoElemento.length; j<i;j++)
    {
        dato6[j] = datoElemento[j].value;
    }

    for(var j=0,i=datoExamen.length; j<i;j++)
    {
        dato7[j] = datoExamen[j].value;
    }

    for(var j=0,i=datoTipoExamen.length; j<i;j++)
    {
        dato8[j] = datoTipoExamen[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idCargo: dato0,
                codigoCargo: dato1,
                nombreCargo: dato2,
                salarioBaseCargo: dato3,
                ListaGeneral_idTareaAltoRiesgo: dato4, 
                ListaGeneral_idVacuna: dato5, 
                ElementoProteccion_idElementoProteccion: dato6, 
                FrecuenciaMedicion_idFrecuenciaMedicion: dato7,
                TipoExamenMedico_idTipoExamenMedico: dato8
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

                (typeof msj.responseJSON.codigoCargo === "undefined" ? document.getElementById('codigoCargo').style.borderColor = '' : document.getElementById('codigoCargo').style.borderColor = '#a94442');

                (typeof msj.responseJSON.nombreCargo === "undefined" ? document.getElementById('nombreCargo').style.borderColor = '' : document.getElementById('nombreCargo').style.borderColor = '#a94442');

                (typeof msj.responseJSON.salarioBaseCargo === "undefined" ? document.getElementById('salarioBaseCargo').style.borderColor = '' : document.getElementById('salarioBaseCargo').style.borderColor = '#a94442');
                // if (typeof msj.responseJSON.salarioBaseCargo === "undefined")
                // {
                //     document.getElementById('salarioBaseCargo').style.borderColor = '' ;
                // }
                // else
                // {
                //     document.getElementById('salarioBaseCargo').value = '';
                //     document.getElementById('salarioBaseCargo').style.borderColor = '#a94442';
                //     document.getElementById('salarioBaseCargo').placeholder = 'Digite valor numerico';
                // }

                for(var j=0,i=datoTarea.length; j<i;j++)
                {
                    (typeof respuesta['ListaGeneral_idTareaAltoRiesgo'+j] === "undefined" 
                        ? document.getElementById('ListaGeneral_idTareaAltoRiesgo'+j).style.borderColor = '' 
                        : document.getElementById('ListaGeneral_idTareaAltoRiesgo'+j).style.borderColor = '#a94442');
                }

                for(var j=0,i=datoVacuna.length; j<i;j++)
                {
                    (typeof respuesta['ListaGeneral_idVacuna'+j] === "undefined" ? document.getElementById('ListaGeneral_idVacuna'+j).style.borderColor = '' : document.getElementById('ListaGeneral_idVacuna'+j).style.borderColor = '#a94442');
                }

                for(var j=0,i=datoElemento.length; j<i;j++)
                {
                    (typeof respuesta['ElementoProteccion_idElementoProteccion'+j] === "undefined" ? document.getElementById('ElementoProteccion_idElementoProteccion'+j).style.borderColor = '' : document.getElementById('ElementoProteccion_idElementoProteccion'+j).style.borderColor = '#a94442');
                }

                for(var j=0,i=datoExamen.length; j<i;j++)
                {
                    (typeof respuesta['FrecuenciaMedicion_idFrecuenciaMedicion'+j] === "undefined" ? document.getElementById('FrecuenciaMedicion_idFrecuenciaMedicion'+j).style.borderColor = '' : document.getElementById('FrecuenciaMedicion_idFrecuenciaMedicion'+j).style.borderColor = '#a94442');
                }

                for(var j=0,i=datoTipoExamen.length; j<i;j++)
                {
                    (typeof respuesta['TipoExamenMedico_idTipoExamenMedico'+j] === "undefined" ? document.getElementById('TipoExamenMedico_idTipoExamenMedico'+j).style.borderColor = '' : document.getElementById('TipoExamenMedico_idTipoExamenMedico'+j).style.borderColor = '#a94442');
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

function abrirModalEducacion()
{
    $('#ModalEducacion').modal('show');

}


function abrirModalFormacion()
{
    $('#ModalFormacion').modal('show');

}


function abrirModalHabilidad()
{
    $('#ModalHabilidad').modal('show');

}


function abrirModalCompetencia()
{
    $('#ModalCompetencia').modal('show');

}

// funciones por separadas 
function validacionesPorcentajeEducacion()
{
    // Se crea una variable para que inicie en 0 
    var valida = 0;

    

    for (var i = 0; i < Educacion.contador; i++) {
        valida = valida + parseFloat($('#porcentajeCargoEducacion'+[i]).val());
    }

    if (valida >100 || valida < 100)
    {
        alert('La suma de los porcentajes no debe ser mayor o menor que 100')
    } 

}


function validacionesPorcentajeFormacion()
{
    // Se crea una variable para que inicie en 0 
    var validaF = 0;

    for (var i = 0; i < Formacion.contador; i++) {
        validaF = validaF + parseFloat($('#porcentajeCargoFormacion'+[i]).val());
    }

    if (validaF >100 || validaF < 100)
    {
        alert('La suma de los porcentajes no debe ser mayor o menor que 100')
    } 

}


function validacionesPorcentajeHabilidad()
{
    // Se crea una variable para que inicie en 0 
    var validaH = 0;

    
    for (var i = 0; i < Habilidad.contador; i++) {
        validaH = validaH + parseFloat($('#porcentajeCargoHabilidad'+[i]).val());
    }

    if (validaH >100 || validaH < 100)
    {
        alert('La suma de los porcentajes no debe ser mayor o menor que 100')
    } 

}



function validacionesPorcentajeResponsabilidad()
{
    // Se crea una variable para que inicie en 0 
    var validaH = 0;

    
    for (var i = 0; i < Responsabilidades.contador; i++) {
        validaH = validaH + parseFloat($('#porcentajeCargoResponsabilidad'+[i]).val());
    }

    if (validaH >100 || validaH < 100)
    {
        alert('La suma de los porcentajes no debe ser mayor o menor que 100')
    } 

}

   
// esta funcion verifica que los % de Peso no excedan 100%
function validacionPorcentajePeso()
{
     

    sumatoria = 0;
    sumatoria +=  parseFloat($('#porcentajeEducacionCargo').val()) + parseFloat($('#porcentajeFormacionCargo').val()) +
    parseFloat($('#porcentajeExperienciaCargo').val()) +parseFloat($('#porcentajeHabilidadCargo').val())+parseFloat($('#porcentajeResponsabilidadCargo').val());


    if (sumatoria != 100)
    {
        // devolvemos un FALSE para indicar que esta mala
        return false;
    }

    // devolvemos un TRUE para indicar que esta buena (No entró al if)
    return true;

}










