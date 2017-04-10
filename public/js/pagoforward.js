var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);

function validarFormulario(event)
{
    var route = "http://"+location.host+"/pagoforward";
    var token = $("#token").val();
    var dato0 = document.getElementById('idPagoForward').value;
    var dato1 = document.getElementById('Forward_idForward').value;
    var dato2 = document.getElementById('valorTotalPagoForward').value;
    var dato3 = document.getElementById('valorDolarPagoForward').value;
    var dato7 = document.getElementById('fechaPagoForward').value;
    var datoFinanciacion = document.querySelectorAll("[name='ListaFinanciacion_idListaFinanciacion[]']");
    var datoValorFactura = document.querySelectorAll("[name='valorFacturaPagoForwardDetalle[]']");
    var datoValorPagado = document.querySelectorAll("[name='valorPagadoPagoForwardDetalle[]']");
    var dato4 = [];
    var dato5 = [];
    var dato6 = [];

    var valor = '';
    var sw = true;

    for(var j=0,i=datoFinanciacion.length; j<i;j++)
    {
        dato4[j] = datoFinanciacion[j].value;
    }

    for(var j=0,i=datoValorFactura.length; j<i;j++)
    {
        dato5[j] = datoValorFactura[j].value;
    }

    for(var j=0,i=datoValorPagado.length; j<i;j++)
    {
        dato6[j] = datoValorPagado[j].value;
    }

    $.ajax({
        async: false,
        url:route,
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        dataType: 'json',
        data: {respuesta: 'falso',
                idPagoForward: dato0,
                Forward_idForward: dato1,
                valorTotalPagoForward: dato2,
                valorDolarPagoForward: dato3,
                ListaFinanciacion_idListaFinanciacion: dato4,
                valorFacturaPagoForwardDetalle: dato5,
                valorPagadoPagoForwardDetalle: dato6,
                fechaPagoForward: dato7
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

                (typeof msj.responseJSON.Forward_idForward === "undefined" ? document.getElementById('Forward_idForward').style.borderColor = '' : document.getElementById('Forward_idForward').style.borderColor = '#a94442');

                (typeof msj.responseJSON.valorTotalPagoForward === "undefined" ? document.getElementById('valorTotalPagoForward').style.borderColor = '' : document.getElementById('valorTotalPagoForward').style.borderColor = '#a94442');

                (typeof msj.responseJSON.fechaPagoForward === "undefined" ? document.getElementById('fechaPagoForward').style.borderColor = '' : document.getElementById('fechaPagoForward').style.borderColor = '#a94442');

                
                for(var j=0,i=datoValorPagado.length; j<i;j++)
                {
                    (typeof respuesta['valorPagadoPagoForwardDetalle'+j] === "undefined" ? document.getElementById('valorPagadoPagoForwardDetalle'+j).style.borderColor = '' : document.getElementById('valorPagadoPagoForwardDetalle'+j).style.borderColor = '#a94442');
                }

                for(var j=0,i=datoFinanciacion.length; j<i;j++)
                {
                    (typeof respuesta['ListaFinanciacion_idListaFinanciacion'+j] === "undefined" ? document.getElementById('ListaFinanciacion_idListaFinanciacion'+j).style.borderColor = '' : document.getElementById('ListaFinanciacion_idListaFinanciacion'+j).style.borderColor = '#a94442');
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

function consultarDatosForward(idForward)
{
	var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idForward': idForward},
            url:   ip+'/consultarCamposForward/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                document.getElementById("contenedor_pagoforward").innerHTML = '';
            	//Hago un replace del guion bajo por un vacío ya que con el registro en la bd esta con guion bajo (_)
            	modalidad = respuesta['modalidadForward'].replace("_"," ");
            	range = respuesta['rangeForward'].replace("_", " ");

            	$("#descripcionPagoForward").val(respuesta['descripcionForward']);
                $("#fechaNegociacionPagoForward").val(respuesta['fechaNegociacionForward']);
                $("#fechaVencimientoPagoForward").val(respuesta['fechaVencimientoForward']);
                $("#modalidadPagoForward").val(modalidad); 
                $("#valorDolarPagoForward").val(respuesta['valorDolarForward']);
                $("#tasaPagoForward").val(respuesta['tasaForward']);
                $("#tasaInicialPagoForward").val(respuesta['tasaInicialForward']);
                $("#valorPesosPagoForward").val(respuesta['valorPesosForward']);
                $("#bancoPagoForward").val(respuesta['bancoForward']);
                $("#rangePagoForward").val(range); 
                $("#devaluacionPagoForward").val(respuesta['devaluacionForward']);
                $("#spotPagoForward").val(respuesta['spotForward']);
                $("#estadoPagoForward").val(respuesta['estadoForward']); 
                $("#ForwardPadre_idForwardPadre").val(respuesta['numeroForward']);
            },
            error: function(xhr,err)
            { 
                document.getElementById("contenedor_pagoforward").innerHTML = '';
                $("#descripcionPagoForward").val('');
                $("#fechaNegociacionPagoForward").val('');
                $("#fechaVencimientoPagoForward").val('');
                $("#modalidadPagoForward").val('');
                $("#valorDolarPagoForward").val('');
                $("#tasaPagoForward").val('');
                $("#tasaInicialPagoForward").val('');
                $("#valorPesosPagoForward").val('');
                $("#bancoPagoForward").val('');
                $("#rangePagoForward").val('');
                $("#devaluacionPagoForward").val('');
                $("#spotPagoForward").val('');
                $("#estadoPagoForward").val('');
                $("#ForwardPadre_idForwardPadre").val('');
                $("#estadoModalForward").val(0)
            }
        });
}

function calcularTotales()
{
    valor = 0;
    for (var i = 0; i < window.parent.pagosforward.contador; i++) 
    {
        if(typeof $("#valorPagadoPagoForwardDetalle"+i, window.parent.document).val() != 'undefined' &&
            $("#valorPagadoPagoForwardDetalle"+i, window.parent.document).val() > 0)
        {
            valor += parseFloat($("#valorPagadoPagoForwardDetalle"+i, window.parent.document).val());
        }    


        if (parseFloat($("#valorPagadoPagoForwardDetalle"+i, window.parent.document).val()) > $("#valorFacturaPagoForwardDetalle"+i, window.parent.document).val()) 
        {
            $("#valorPagadoPagoForwardDetalle"+i, window.parent.document).css('background-color', '#F5A9A9');
            $("#Modificar").prop("disabled",true);
            $("#Adicionar").prop("disabled",true);
        }
        else
        {   
            $("#valorPagadoPagoForwardDetalle"+i, window.parent.document).css('background-color', '');
            $("#Modificar").prop("disabled",false);
            $("#Adicionar").prop("disabled",false);
        }
    }

    $('#valorTotalPagoForward', window.parent.document).val(valor);

    if (parseFloat($("#valorDolarPagoForward").val()) < parseFloat($('#valorTotalPagoForward').val())) 
    {
    	alert('Está excediendo la cantidad del forward.');
    	$('#valorTotalPagoForward').css('background-color', '#F5A9A9');
    }
    else
    {	
    	$('#valorTotalPagoForward').css('background-color', '');
    }
}

function abrirModalForward()
{
    idForward = $("#Forward_idForward").val();

	if ($("#estadoModalForward").val() == 0) 
	{
	   if (idForward == '' || idForward == 0) 
       {
            alert('Debe seleccionar un forward');
       }
       else
       {	
    		var lastIdx = null;
    	    window.parent.$("#tforward").DataTable().ajax.url(ip+"/datosPagoForwardDetalle?idForward="+idForward).load();
    	     // Abrir modal
    	    window.parent.$("#modalForward").modal()

    	    $("a.toggle-vis").on( "click", function (e) {
    	        e.preventDefault();
    	 
    	        // Get the column API object
    	        var column = table.column( $(this).attr("data-column") );
    	 
    	        // Toggle the visibility
    	        column.visible( ! column.visible() );
    	    } );

    	    window.parent.$("#tforward tbody").on( "mouseover", "td", function () 
    	    {
    	        var colIdx = table.cell(this).index().column;

    	        if ( colIdx !== lastIdx ) {
    	            $( table.cells().nodes() ).removeClass( "highlight" );
    	            $( table.column( colIdx ).nodes() ).addClass( "highlight" );
    	        }
    	    }).on( "mouseleave", function () 
    	    {
    	        $( table.cells().nodes() ).removeClass( "highlight" );
    	    } );


    	    // Setup - add a text input to each footer cell
    	    window.parent.$("#tforward tfoot th").each( function () 
    	    {
    	        var title = window.parent.$("#tforward thead th").eq( $(this).index() ).text();
    	        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    	    });
    	 
    	    // DataTable
    	    var table = window.parent.$("#tforward").DataTable();
    	 
    	    // Apply the search
    	    table.columns().every( function () 
    	    {
    	        var that = this;
    	 
    	        $( "input", this.footer() ).on( "blur change", function () {
    	            if ( that.search() !== this.value ) {
    	                that
    	                    .search( this.value )
    	                    .draw();
    	            }
    	        } );
    	    })

    	    $('#tforward tbody').on( 'click', 'tr', function () {
    	        $(this).toggleClass('selected');
    	    } );

    	    $('#botonForward').click(function() {
    	        var datos = table.rows('.selected').data();
                
                // Al presionar el botón "Seleccionar" recorro cuantos registros fueron seleccionados en la grid
    	        for (var i = 0; i < datos.length; i++) 
    	        {
                    // Armo el array con los valores para insertar en la multiregistro
    	            var valores = new Array(0,datos[i][5], datos[i][0],datos[i][6], datos[i][9], datos[i][7], datos[i][2], '', '', datos[i][3], datos[i][4]);
                    // Si el id de la compra o el id del documento financiero son nulos es porque selecciono una
                    // temporada y por ende voy a llenar todas las compras relacionadas a esa temporada
                    if (datos[i][6] == null && datos[i][7] == null) 
                    {
                        
                        idForward = $("#Forward_idForward").val();
                        idTemporada = datos[i][5];
                        var token = document.getElementById('token').value;
                        $.ajax({
                                headers: {'X-CSRF-TOKEN': token},
                                dataType: "json",
                                data: {'idTemporada': idTemporada, 'idForward': idForward},
                                url:   ip+'/llenarTemporadaPagoForward/',
                                type:  'post',
                                beforeSend: function(){
                                    },
                                success: function(respuesta)
                                {
                                   for (var cont = 0; cont < respuesta.length; cont++) 
                                   {
                                        var valores = new Array(0, respuesta[cont]['idTemporada'], respuesta[cont]['nombreTemporada'], respuesta[cont]['idCompra'], respuesta[cont]['numeroCompra'],'','',respuesta[cont]['facturaEmbarqueDetalle'],respuesta[cont]['fechaRealEmbarqueDetalle'],respuesta[cont]['valorFacturaEmbarqueDetalle'],respuesta[cont]['valorFacturaEmbarqueDetalle'],0);                                    
                                        window.parent.pagosforward.agregarCampos(valores,'A');                   
                                   }
                                   calcularTotales();
                                },
                                error: function(xhr,err)
                                { 
                                    alert('Error');
                                }
                            });
                    }
                    // Si el id de la compra no está nulo, consulto las im de esa compra y las inserto
                    // en la multiregistro
                    else if(datos[i][6] != null)
                    {
                        idForward = $("#Forward_idForward").val();
                        idCompra = datos[i][6];
                        var token = document.getElementById('token').value;
                        $.ajax({
                                headers: {'X-CSRF-TOKEN': token},
                                dataType: "json",
                                data: {'idCompra': idCompra, 'idForward': idForward},
                                url:   ip+'/llenarCompraPagoForward/',
                                type:  'post',
                                beforeSend: function(){
                                    },
                                success: function(respuesta)
                                {
                                    if (respuesta == '') 
                                    {
                                        alert('No hay facturas relacionadas a esta compra en el embarque.');
                                        $('#estadoModalForward').val(0);
                                    }
                                    else
                                    {
                                       for (var cont = 0; cont < respuesta.length; cont++) 
                                       {
                                            var valores = new Array(0, respuesta[cont]['idTemporada'], respuesta[cont]['nombreTemporada'], respuesta[cont]['idCompra'], respuesta[cont]['numeroCompra'],'','',respuesta[cont]['facturaEmbarqueDetalle'],respuesta[cont]['fechaRealEmbarqueDetalle'],respuesta[cont]['valorFacturaEmbarqueDetalle'],respuesta[cont]['valorFacturaEmbarqueDetalle'],0);                                    
                                            window.parent.pagosforward.agregarCampos(valores,'A');                   
                                       }
                                       calcularTotales();
                                   }
                                },
                                error: function(xhr,err)
                                { 
                                    alert('Error');
                                }
                            });
                    }
                    // Si el documento financiero no está nulo, inserto el registro del documento
                    // financiero seleccionado
                    else if(datos[i][7] != null)
                    {
                        window.parent.pagosforward.agregarCampos(valores,'A');   
                    }
    	            
    	        }
                // Calculo totales de los campos insertados en la multiregistro 
    	        calcularTotales();
                // Le pongo datetimepicker al campo fecha factura que hay en la multiregistro
    	        for (var i = 0; i < window.parent.pagosforward.contador; i++) 
    	        {
    		        $("#fechaFacturaPagoForwardDetalle"+i).datetimepicker
    		        (
    		            ({
    		               format: "YYYY-MM-DD"
    		             })
    		        );  
    	        }
    	        $("#estadoModalForward").val(1)
    	        window.parent.$("#modalForward").modal("hide");
    	    });

    	    $('#botonCloseForward').click(function() {
    	    	$("#estadoModalForward").val(1)
    	    });
        }
	}
	else
	{
		window.parent.$("#tforward tbody tr").each( function () 
	    {
	    	$(this).removeClass('selected');
	    });

		window.parent.$("#modalForward").modal()
	}
}

function abrirModalIM()
{
	
	if ($("#estadoModalIm").val() == 0) 
	{
		var lastIdx = null;
	    window.parent.$("#tim").DataTable().ajax.url(ip+"/datosPagoForwardIM").load();
	     // Abrir modal
	    window.parent.$("#modalIM").modal()

	    $("a.toggle-vis").on( "click", function (e) {
	        e.preventDefault();
	 
	        // Get the column API object
	        var column = table.column( $(this).attr("data-column") );
	 
	        // Toggle the visibility
	        column.visible( ! column.visible() );
	    } );

	    window.parent.$("#tim tbody").on( "mouseover", "td", function () 
	    {
	        var colIdx = table.cell(this).index().column;

	        if ( colIdx !== lastIdx ) {
	            $( table.cells().nodes() ).removeClass( "highlight" );
	            $( table.column( colIdx ).nodes() ).addClass( "highlight" );
	        }
	    }).on( "mouseleave", function () 
	    {
	        $( table.cells().nodes() ).removeClass( "highlight" );
	    } );


	    // Setup - add a text input to each footer cell
	    window.parent.$("#tim tfoot th").each( function () 
	    {
	        var title = window.parent.$("#tim thead th").eq( $(this).index() ).text();
	        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
	    });
	 
	    // DataTable
	    var table = window.parent.$("#tim").DataTable();
	 
	    // Apply the search
	    table.columns().every( function () 
	    {
	        var that = this;
	 
	        $( "input", this.footer() ).on( "blur change", function () {
	            if ( that.search() !== this.value ) {
	                that
	                    .search( this.value )
	                    .draw();
	            }
	        } );
	    })

	    $('#tim tbody').on( 'click', 'tr', function () {
	        $(this).toggleClass('selected');
	    } );

	    $('#botonIM').click(function() {
	        var datos = table.rows('.selected').data();

	        for (var i = 0; i < datos.length; i++) 
	        {
	            var valores = new Array(0,0,'',0,'',0,'',datos[i][0],datos[i][1],datos[i][3],0,0);
	            window.parent.pagosforward.agregarCampos(valores,'A');
	        }

	        calcularTotales();
	        for (var i = 0; i < window.parent.pagosforward.contador; i++) 
	        {
		        $("#fechaFacturaPagoForwardDetalle"+i+", #fechaPagoForwardDetalle"+i+", #fechaGiroPagoForwardDetalle"+i+", #fechaPagoGiroPagoForwardDetalle"+i).datetimepicker
		        (
		            ({
		               format: "YYYY-MM-DD"
		             })
		        );  
	        }
	        $("#estadoModalIm").val(1)
	        window.parent.$("#modalIM").modal("hide");
	    });

	    $('#botonCloseIM').click(function() {
	    	$("#estadoModalIm").val(1)
	    });
	}
	else
	{
		window.parent.$("#tim tbody tr").each( function () 
	    {
	    	$(this).removeClass('selected');
	    });

		window.parent.$("#modalIM").modal()
	}
}

function mostrarDetalleCompras(idCompra)
{
    var token = document.getElementById('token').value;
    $.ajax({
            headers: {'X-CSRF-TOKEN': token},
            dataType: "json",
            data: {'idCompra': idCompra},
            url:   ip+'/consultarDetalleCompraPagoForward/',
            type:  'post',
            beforeSend: function(){
                },
            success: function(respuesta)
            {
                window.parent.$("#modalDetallesCompras").modal();
                $("#detalleCompra").html(respuesta);
            },
            error: function(xhr,err)
            { 
                alert('Error.');
            }
        });
}