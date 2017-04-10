//Obtener ip del servidor
var ip = ((location.href.split('/'))[0])+'//'+((location.href.split('/'))[2]);


$(document).ready( function () {

	$("#fechaEnvioMensajeria, #fechaEntregaMensajeria, #fechaLimiteMensajeria").datetimepicker
	(
		({
           format: "YYYY-MM-DD HH:mm:ss"
         })
	);
});

function abrirModalTercero(nombreTabla,nombreCampo,codigoCampo,objeto, tipotercero)
{
    var lastIdx = null;
    window.parent.$("#tlistaselecttercero").DataTable().ajax.url(ip+"/datosListaSelectImportacionTercero?nombreTabla="+nombreTabla+"&campo="+nombreCampo+"&codigo="+codigoCampo+"&value="+objeto.value+"&tipotercero="+tipotercero+"&campoTabla="+objeto.name).load();
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
                enviarDatosListaTercero(datos[0][0], datos[0][1], datos[0][2], datos[0][3], datos[0][4], datos[0][5], datos[0][6]);
            }

        window.parent.$("#ListaSelectTercero").modal("hide");

    } );

}

function enviarDatosListaTercero(id, nombre, nombreComercial, cod, objeto, pago, direccion)
{
    if (objeto == 'transportadorMensajeria') 
    {
        $("input[name='transportadorMensajeria']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Tercero_idTransportador']").each(function() 
        {
            $(this).val(id);
        });
    }

    if (objeto == 'destinatarioMensajeria') 
    {
        $("input[name='destinatarioMensajeria']").each(function() 
        {
            $(this).val(nombre);
        });

        $("input[id='Tercero_idDestinatario']").each(function() 
        {
            $(this).val(id);
        });

        $("input[id='direccionEntregaMensajeria']").each(function() 
        {
            $(this).val(direccion);
        });
    }    
}
