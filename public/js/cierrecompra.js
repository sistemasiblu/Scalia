// function validarFormulario(event)
// {
//     var route = "http://"+location.host+"/cierrecompra";
//     var token = $("#token").val();
//     var dato0 = document.getElementById('idCierreCompra').value;
//     var dato1 = document.getElementById('numeroCierreCompra').value;
//     var dato2 = document.getElementById('fechaCierreCompra').value;
//     var dato3 = document.getElementById('Tercero_idProveedor').value;

//     var valor = '';
//     var sw = true;

//     $.ajax({
//         async: false,
//         url:route,
//         headers: {'X-CSRF-TOKEN': token},
//         type: 'POST',
//         dataType: 'json',
//         data: {respuesta: 'falso',
//                 idCierreCompra: dato0,
//                 numeroCierreCompra: dato1,
//                 fechaCierreCompra: dato2,
//                 Tercero_idProveedor: dato3
//                 },
//         success:function(){
//             //$("#msj-success").fadeIn();
//             //console.log(' sin errores');
//         },
//         error:function(msj){
//             var mensaje = '';
//             var respuesta = JSON.stringify(msj.responseJSON); 
//             alert(respuesta);
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

//                 (typeof msj.responseJSON.numeroCierreCompra === "undefined" ? document.getElementById('numeroCierreCompra').style.borderColor = '' : document.getElementById('numeroCierreCompra').style.borderColor = '#a94442');

//                 (typeof msj.responseJSON.fechaCierreCompra === "undefined" ? document.getElementById('fechaCierreCompra').style.borderColor = '' : document.getElementById('fechaCierreCompra').style.borderColor = '#a94442');

//                 (typeof msj.responseJSON.Tercero_idProveedor === "undefined" ? document.getElementById('nombreProveedorCierreCompra').style.borderColor = '' : document.getElementById('nombreProveedorCierreCompra').style.borderColor = '#a94442');

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

function abrirModalTercero(nombreTabla,nombreCampo,codigoCampo,objeto, tipotercero)
{
    var lastIdx = null;
    window.parent.$("#tlistaselecttercero").DataTable().ajax.url('http://'+location.host+"/datosListaSelectImportacionTercero?nombreTabla="+nombreTabla+"&campo="+nombreCampo+"&codigo="+codigoCampo+"&value="+objeto.value+"&tipotercero="+tipotercero+"&campoTabla="+objeto.name).load();
     // Abrir modal
    window.parent.$("#ListaSelectTercero").modal()

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tlistaselecttercero tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#tlistaselecttercero tfoot th").each( function () 
    {
        var title = window.parent.$("#tlistaselecttercero thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tlistaselecttercero").DataTable();
 
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

    window.parent.$("#tlistaselecttercero tbody").on( "dblclick", "tr", function () 
    {
        if ( $(this).hasClass("selected") ) {
            $(this).removeClass("selected");
        }
        else {
            table.$("tr.selected").removeClass("selected");
            $(this).addClass("selected");
        }

        var datos = table.rows('.selected').data();
        console.log(datos);

            if (datos.length > 0) 
            {
                enviarDatosListaTercero(datos[0][0], datos[0][1], datos[0][2], datos[0][3], datos[0][4], datos[0][5]);
            }

        window.parent.$("#ListaSelectTercero").modal("hide");

    } );

}

function enviarDatosListaTercero(id, nombre, nombreComercial, cod, objeto, pago)
{
    if (objeto == 'nombreProveedorCierreCompra') 
    {
        $("input[name='nombreProveedorCierreCompra']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Tercero_idProveedor']").each(function() 
        {
            $(this).val(id);
        });
    }        
}

function abrirModalCierreCompra()
{
    if ($("#estadoModalCompra").val() == 0) 
    {   
        idProveedor = $("#Tercero_idProveedor").val();
        var lastIdx = null;
        window.parent.$("#tcierrecompra").DataTable().ajax.url("http://"+location.host+"/datosCierreCompraSaldo?idProveedor="+idProveedor).load();
         // Abrir modal
        window.parent.$("#modalCierreCompra").modal()

        $("a.toggle-vis").on( "click", function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr("data-column") );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        window.parent.$("#tcierrecompra tbody").on( "mouseover", "td", function () 
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
        window.parent.$("#tcierrecompra tfoot th").each( function () 
        {
            var title = window.parent.$("#tcierrecompra thead th").eq( $(this).index() ).text();
            $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
        });
     
        // DataTable
        var table = window.parent.$("#tcierrecompra").DataTable();
     
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

        $('#tcierrecompra tbody').on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
        } );

        $('#btnCompra').click(function() {
            var datos = table.rows('.selected').data();
            
            // Al presionar el botón "Seleccionar" recorro cuantos registros fueron seleccionados en la grid
            for (var i = 0; i < datos.length; i++) 
            {
                if (saldocartera.contador > 0) 
                {
                    for (var j = 0; j < saldocartera.contador; j++) 
                    {
                        alert('Grid: '+datos[i][4]);
                        alert('Multi'+j+':'+$("#Compra_idCompra"+j).val());
                        if (datos[i][4] == $("#Compra_idCompra"+j).val())
                        {
                            alert('Esta compra ya fue agregada.');
                            // j = saldocartera.contador;
                        }
                        else
                        {
                            // Armo el array con los valores para insertar en la multiregistro
                            var valores = new Array(0,datos[i][4], datos[i][0],datos[i][1], datos[i][2], datos[i][3], datos[i][5], 0);
                            window.parent.saldocartera.agregarCampos(valores,'A');
                        }
                    }
                }
                else
                {
                    // Armo el array con los valores para insertar en la multiregistro
                    var valores = new Array(0,datos[i][4], datos[i][0],datos[i][1], datos[i][2], datos[i][3], datos[i][5], 0);
                    window.parent.saldocartera.agregarCampos(valores,'A'); 
                }
            }
            
            $("#estadoModalCompra").val(1)
            window.parent.$("#modalCierreCompra").modal("hide");
        });

        $('#btnCloseCompra').click(function() {
            $("#estadoModalCompra").val(1)
        });
    
    }
    else
    {
        window.parent.$("#tcierrecompra tbody tr").each( function () 
        {
            $(this).removeClass('selected');
        });

        window.parent.$("#modalCierreCompra").modal()
    }
}

function abrirModalCierreCartera()
{
    if ($("#estadoModalCartera").val() == 0) 
    {   
        idProveedor = $("#Tercero_idProveedor").val();
        var lastIdx = null;
        window.parent.$("#tcierrecartera").DataTable().ajax.url("http://"+location.host+"/datosCierreCompraCartera?idProveedor="+idProveedor).load();
         // Abrir modal
        window.parent.$("#modalCierreCartera").modal()

        $("a.toggle-vis").on( "click", function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr("data-column") );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        window.parent.$("#tcierrecartera tbody").on( "mouseover", "td", function () 
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
        window.parent.$("#tcierrecartera tfoot th").each( function () 
        {
            var title = window.parent.$("#tcierrecartera thead th").eq( $(this).index() ).text();
            $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
        });
     
        // DataTable
        var table = window.parent.$("#tcierrecartera").DataTable();
     
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

        $('#tcierrecartera tbody').on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
        } );

        $('#btnCartera').click(function() {
            var datos = table.rows('.selected').data();
            
            // Al presionar el botón "Seleccionar" recorro cuantos registros fueron seleccionados en la grid
            for (var i = 0; i < datos.length; i++) 
            {
                // Armo el array con los valores para insertar en la multiregistro
                var valores = new Array(0,datos[i][4], datos[i][0],datos[i][5], datos[i][1], datos[i][2], datos[i][6], datos[i][3], 0);
                window.parent.abonocartera.agregarCampos(valores,'A');                   
            }
            
            $("#estadoModalCartera").val(1)
            window.parent.$("#modalCierreCartera").modal("hide");
        });

        $('#btnCloseCartera').click(function() {
            $("#estadoModalCartera").val(1)
        });
    
    }
    else
    {
        window.parent.$("#tcierrecartera tbody tr").each( function () 
        {
            $(this).removeClass('selected');
        });

        window.parent.$("#modalCierreCartera").modal()
    } 
}