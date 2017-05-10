
function abrirModalValor(tipo)
{
         window.parent.$("#tvalorSelect tbody tr").each( function () 
    {
        $(this).removeClass('selected');
    });
	var lastIdx = null;
    window.parent.$("#tvalorSelect").DataTable().ajax.url('http://'+location.host+"/datosValorConciliacionSelect?tipo="+tipo).load();
     // Abrir modal
    window.parent.$("#ModalValor").modal()

    

    $("a.toggle-vis").on( "click", function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column( $(this).attr("data-column") );
 
        // Toggle the visibility
        column.visible( ! column.visible() );
    } );

    window.parent.$("#tvalorSelect tbody").on( "mouseover", "td", function () 
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
    window.parent.$("#tvalorSelect tfoot th").each( function () 
    {
        var title = window.parent.$("#tvalorSelect thead th").eq( $(this).index() ).text();
        $(this).html( "<input type='text' placeholder='Buscar por "+title+"'/>" );
    });
 
    // DataTable
    var table = window.parent.$("#tvalorSelect").DataTable();
 
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

    window.parent.$('#tvalorSelect tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');

        var datos = table.rows('.selected').data();


    } );

    window.parent.$('#botonCampo').click(function() {
        var datos = table.rows('.selected').data();  

        for (var i = 0; i < datos.length; i++) 
        {
            var valores = new Array(0, datos[i][0],datos[i][1],'','');
            if(tipo == 'comercial')
                window.parent.comercial.agregarCampos(valores,'A');  
            else
                window.parent.cartera.agregarCampos(valores,'A');  
        }
        window.parent.$("#ModalValor").modal("hide");
    });

}