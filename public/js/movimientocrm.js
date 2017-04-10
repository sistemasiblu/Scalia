function abrirModalVacante()
{
    $('#ModalVacante').modal('show');

}
function cambiarEstado(id, TipoEstado, modificar, eliminar, consultar, aprobar)
{

    

    //$("#tmovimientocrm").DataTable().ajax.url('http://'+location.host+"/datosMovimientoCRM?idDocumento="+id+"&TipoEstado="+TipoEstado+"&modificar="+modificar+"&eliminar="+eliminar+"&consultar="+consultar+"&aprobar="+aprobar).load();
    location.href= 'http://'+location.host+"/movimientocrm?idDocumentoCRM="+id+"&TipoEstado="+TipoEstado+"&modificar="+modificar+"&eliminar="+eliminar+"&consultar="+consultar+"&aprobar="+aprobar;
}

function imprimirFormato(idMov, idDoc)
{
    window.open('movimientocrm/'+idMov+'?idDocumentoCRM='+idDoc+'&accion=imprimir','movimientocrm','width=5000,height=5000,scrollbars=yes, status=0, toolbar=0, location=0, menubar=0, directories=0');
}

function mostrarTableroCRM(idDoc)
{
    window.open('movimientocrm/0?idDocumentoCRM='+idDoc+'&accion=dashboard','dashboardcrm','width=5000,height=5000,scrollbars=yes, status=0, toolbar=0, location=0, menubar=0, directories=0');
}


function llamarsubclasificacion(id, valor) 
{

    
    var select = document.getElementById('ClasificacionCRM_idClasificacionCRM').value;
    var token = document.getElementById('token').value;
    $.ajax(
    {
        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:'/llamarsubclasificacion',
        data:{idClasificacionCRM: id},
        type:  'get',
        beforeSend: function(){},
        success: function(data)
        {

            $('#ClasificacionCRMDetalle_idClasificacionCRMDetalle').html('');
            var select = document.getElementById('ClasificacionCRMDetalle_idClasificacionCRMDetalle');

            option = document.createElement('option');
            option.value = '';
            option.text = 'Seleccione';
            select.appendChild(option);
            for (var i = 0;  i <= data.length; i++) 
            {
                option = document.createElement('option');
                option.value = data[i]['idClasificacionCRMDetalle'];
                option.text = data[i]['nombreClasificacionCRMDetalle'];

                option.selected = (valor == data[i]['idClasificacionCRMDetalle'] ? true : false);

                select.appendChild(option);
            }

        },
        error:    function(xhr,err)
        {
            alert('Se ha producido un error: ' +err);
        }
    });
};


function validarFormulario(event)
{
    var route = "http://"+location.host+"/tercero";
    var token = $("#token").val();
    var dato0 = document.getElementById('idTercero').value;
    var dato1 = document.getElementById('documentoTercero').value;
    var dato2 = document.getElementById('nombre1Tercero').value;
    var dato3 = document.getElementById('apellido1Tercero').value;
    var dato4 = document.getElementById('fechaCreacionTercero').value;
    var dato5 = document.getElementById('tipoTercero').value;
    var dato6 = document.getElementById('direccionTercero').value;
    var dato7 = document.getElementById('Ciudad_idCiudad').value;
    var dato8 = document.getElementById('telefonoTercero').value;
    var dato9 = document.getElementById('TipoIdentificacion_idTipoIdentificacion').value;
    var dato10 = document.getElementById('Cargo_idCargo').value;
   
    var valor = '';
    var sw = true;
    
    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idTercero: dato0,
                documentoTercero: dato1,
                nombre1Tercero: dato2,
                apellido1Tercero: dato3,
                fechaCreacionTercero: dato4, 
                tipoTercero: dato5, 
                direccionTercero: dato6, 
                Ciudad_idCiudad: dato7,
                telefonoTercero: dato8,
                TipoIdentificacion_idTipoIdentificacion: dato9                
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

                (typeof msj.responseJSON.documentoTercero === "undefined" ? document.getElementById('documentoTercero').style.borderColor = '' : document.getElementById('documentoTercero').style.borderColor = '#a94442');

                (typeof msj.responseJSON.nombre1Tercero === "undefined" ? document.getElementById('nombre1Tercero').style.borderColor = '' : document.getElementById('nombre1Tercero').style.borderColor = '#a94442');

                (typeof msj.responseJSON.apellido1Tercero === "undefined" ? document.getElementById('apellido1Tercero').style.borderColor = '' : document.getElementById('apellido1Tercero').style.borderColor = '#a94442');

                (typeof msj.responseJSON.fechaCreacionTercero === "undefined" ? document.getElementById('fechaCreacionTercero').style.borderColor = '' : document.getElementById('fechaCreacionTercero').style.borderColor = '#a94442');

                (typeof msj.responseJSON.tipoTercero === "undefined" ? document.getElementById('tipoTercero').style.borderColor = '' : document.getElementById('tipoTercero').style.borderColor = '#a94442');

                (typeof msj.responseJSON.direccionTercero === "undefined" ? document.getElementById('direccionTercero').style.borderColor = '' : document.getElementById('direccionTercero').style.borderColor = '#a94442');

                (typeof msj.responseJSON.Ciudad_idCiudad === "undefined" ? document.getElementById('Ciudad_idCiudad').style.borderColor = '' : document.getElementById('Ciudad_idCiudad').style.borderColor = '#a94442');

                (typeof msj.responseJSON.telefonoTercero === "undefined" ? document.getElementById('telefonoTercero').style.borderColor = '' : document.getElementById('telefonoTercero').style.borderColor = '#a94442');

                (typeof msj.responseJSON.TipoIdentificacion_idTipoIdentificacion === "undefined" ? document.getElementById('TipoIdentificacion_idTipoIdentificacion').style.borderColor = '' : document.getElementById('TipoIdentificacion_idTipoIdentificacion').style.borderColor = '#a94442');

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



function validarTipoTercero()
{
    document.getElementById("tipoTercero").value = '';

    for (tipo = 1; tipo <= 2; tipo++)
    {
        document.getElementById("tipoTercero").value = document.getElementById("tipoTercero").value + ((document.getElementById("tipoTercero" + (tipo)).checked) ? '*' + document.getElementById("tipoTercero" + (tipo)).value + '*' : '');
    }
    mostrarPestanas();
}

function seleccionarTipoTercero()
{
    for (tipo = 1; tipo <= 2; tipo++)
    {
        if (document.getElementById("tipoTercero").value.indexOf('*' + document.getElementById("tipoTercero" + (tipo)).value + '*') >= 0)
        {
            document.getElementById("tipoTercero" + (tipo)).checked = true;
        }
        else
        {
            document.getElementById("tipoTercero" + (tipo)).checked = false;
        }
    }

    mostrarPestanas();

}

function llenaNombreTercero()
{
    nombre1 = document.getElementById('nombre1Tercero').value;
    nombre2 = document.getElementById('nombre2Tercero').value;
    apellido1 = document.getElementById('apellido1Tercero').value;
    apellido2 = document.getElementById('apellido2Tercero').value;

    document.getElementById('nombreCompletoTercero').value = nombre1 + ' ' + nombre2 + ' ' + apellido1 + ' ' + apellido2;
}

function mostrarPestanas()
{
    if(document.getElementById('tipoTercero1').checked)
    {
        document.getElementById('cargo').style.display = 'inline';
        document.getElementById('pestanaProducto').style.display = 'none';
        document.getElementById('pestanaEducacion').style.display = 'block';
        document.getElementById('pestanaExperiencia').style.display = 'block';
        document.getElementById('pestanaFormacion').style.display = 'block';
        document.getElementById('pestanaPersonal').style.display = 'block';
        document.getElementById('pestanaLaboral').style.display = 'block';
    }
    /*else
    {
        document.getElementById('cargo').style.display = 'none';
        document.getElementById('pestanaProducto').style.display = 'block';   
        document.getElementById('pestanaEducacion').style.display = 'none';
        document.getElementById('pestanaExperiencia').style.display = 'none';
        document.getElementById('pestanaFormacion').style.display = 'none';
        document.getElementById('pestanaPersonal').style.display = 'none';
        document.getElementById('pestanaLaboral').style.display = 'none';
    }*/

    if(document.getElementById('tipoTercero2').checked)
    {
        document.getElementById('cargo').style.display = 'none';
        document.getElementById('pestanaProducto').style.display = 'block';
        document.getElementById('pestanaEducacion').style.display = 'none';
        document.getElementById('pestanaExperiencia').style.display = 'none';
        document.getElementById('pestanaFormacion').style.display = 'none';
        document.getElementById('pestanaPersonal').style.display = 'none';
        document.getElementById('pestanaLaboral').style.display = 'none';
    }
    /*else
    {
        document.getElementById('cargo').style.display = 'inline';
        document.getElementById('pestanaProducto').style.display = 'none';
        document.getElementById('pestanaEducacion').style.display = 'block';
        document.getElementById('pestanaExperiencia').style.display = 'block';
        document.getElementById('pestanaFormacion').style.display = 'block';
        document.getElementById('pestanaPersonal').style.display = 'block';
        document.getElementById('pestanaLaboral').style.display = 'block';
    }*/    
}


function mostrarModalAsesor(idMovimientoCRM)
{   
    // con el id del movimiento debemos consultar los datos a
    // mostrar en el modal
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            url:   'http://'+location.host+'/consultarAsesorMovimientoCRM',
            type:  'post',
            data: {idMovimientoCRM : idMovimientoCRM},
            beforeSend: function(){
                
                },
            success: function(respuesta)
            {
                // asignamos los valores a los campos del modal
                if(respuesta["Tercero_idSupervisor"] !== null)
                {   
                    $("#Tercero_idSupervisor").val(respuesta["Tercero_idSupervisor"]);
                    $("#nombreCompletoSupervisor").val(respuesta["nombreCompletoSupervisor"]);
                }
                $('#Tercero_idAsesor > option[value="'+respuesta["Tercero_idAsesor"]+'"]').attr('selected', 'selected');
                $('#AcuerdoServicio_idAcuerdoServicio > option[value="'+respuesta["AcuerdoServicio_idAcuerdoServicio"]+'"]').attr('selected', 'selected');
                $("#diasEstimadosSolucionMovimientoCRM").val(respuesta["diasEstimadosSolucionMovimientoCRM"]);

            },
            error: function(xhr,err)
            { 
                console.log(xhr);
                alert("Error "+xhr);
            }
        });
    
    $("#idMovimientoCRM").val(idMovimientoCRM);
    $("#ModalAsesor").modal("show");
}

function mostrarDiasAcuerdo(idAcuerdo)
{   
    // con el id del movimiento debemos consultar los datos a
    // mostrar en el modal
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            url:   'http://'+location.host+'/consultarDiasAcuerdoServicio',
            type:  'post',
            data: {idAcuerdo : idAcuerdo},
            beforeSend: function(){
                
                },
            success: function(respuesta)
            {
                
                // asignamos los valores a los campos del modal
                if(respuesta["tiempoAcuerdoServicio"] !== null)
                {   
                    $("#diasEstimadosSolucionMovimientoCRM").val(respuesta["tiempoAcuerdoServicio"]);
                }

            },
            error: function(xhr,err)
            { 
                console.log(xhr);
                alert("Error "+xhr);
            }
        });
}

function guardarAsesor()
{   
    var idMovimientoCRM = $("#idMovimientoCRM").val();
    var idSupervisor = $("#Tercero_idSupervisor").val();
    var idAsesor = $("#Tercero_idAsesor").val();
    var idAcuerdo = $("#AcuerdoServicio_idAcuerdoServicio").val();
    var diasAcuerdo = $("#diasEstimadosSolucionMovimientoCRM").val();
    
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            url:   'http://'+location.host+'/guardarAsesorMovimientoCRM',
            type:  'post',
            data: {idMovimientoCRM : idMovimientoCRM,
                    idSupervisor: idSupervisor,
                    idAsesor: idAsesor,
                    idAcuerdo: idAcuerdo,
                    diasAcuerdo: diasAcuerdo},
            beforeSend: function(){
                
                },
            success: function(respuesta)
            {
                
                alert(respuesta[1]);
                $("#ModalAsesor").modal("hide");
                
            },
            error: function(xhr,err)
            { 
                console.log(xhr);
                alert("Error "+xhr);
            }
        });
}


function abrirModal(file)
{
    // $("#myModal").modal("show");
    if(file != '')
    {
        PreviewImage(file); //Vista previa en tamaño mayor

         $("input[id='archivoTercero']").each(function() 
        {
            $(this).val(file["name"]);
        });
    }
    else
    {
      $("input[id='archivoTercero']").each(function() 
        {
            $(this).val('');
        });
    }           
}

function PreviewImage(archivo) 
{
    pdffile=archivo;
    pdffile_url=URL.createObjectURL(pdffile);
    $('#viewer').attr('src',pdffile_url);
}

function eliminarDiv(idDiv)
{
    eliminar=confirm("¿Deseas eliminar este archivo?");
    if (eliminar)
    {
        $("#"+idDiv ).remove();  
        $("#eliminarArchivo").val( $("#eliminarArchivo").val() + idDiv + ",");  
    }
}


function ejecutarInterface(tipo)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            url:   'http://'+location.host+'/'+tipo,
            type:  'post',
            beforeSend: function(){
                
                },
            success: function(respuesta)
            {
                if(respuesta[0] == true)
                {
                    alert(respuesta[1]);
                    $("#ModalImportacion").modal("hide");
                }
                else
                {
                    $("#reporteError").html(respuesta[1]);
                    $("#ModalErrores").modal("show");
                }
            },
            error: function(xhr,err)
            { 
                console.log(xhr);
                alert("Error "+xhr);
            }
        });
    $("#dropzoneTerceroArchivo .dz-preview").remove();
    $("#dropzoneTerceroArchivo .dz-message").html('Seleccione o arrastre los archivos a subir.');
}

function mostrarModalInterface()
{
    $("#ModalImportacion").modal("show");
}

