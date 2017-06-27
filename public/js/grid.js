function configurarGrid(idTabla, rutaAjax)
{ 

        var table = $('#'+idTabla).DataTable( {
            "order": [[ 2, "desc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            /*fixedHeader:
            {
            header: true,
            footer: true
            },*/
            "dom": 'Blfrtip',
            "buttons": 
            [
                { "extend": 'excel', "text":' <i class="fa fa-download" aria-hidden="true"></i>',"className": 'btn-primary',

                }

            ],
            "lengthMenu": [ 10, 25, 50,100,200] ,
            "ajax": rutaAjax, 
            "language": {
                        "sProcessing":     "Procesando...",
                        "sLengthMenu":     "Mostrar _MENU_ registros",
                        "sZeroRecords":    "No se encontraron resultados",
                        "sEmptyTable":     "Ning&uacute;n dato disponible en esta tabla",
                        "sInfo":           "Registros del _START_ al _END_ de un total de _TOTAL_ ",
                        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix":    "",
                        "sSearch":         "Buscar:",
                        "sUrl":            "",
                        "sInfoThousands":  ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst":    "Primero",
                            "sLast":     "&Uacute;ltimo",
                            "sNext":     "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    }
        });
         
      //new $.fn.DataTable.FixedHeader( table );
        
        $('a.toggle-vis').on( 'click', function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr('data-column') );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        $('#'+idTabla+' tbody')
        .on( 'mouseover', 'td', function () {
            var colIdx = table.cell(this).index().column;

            if ( colIdx !== lastIdx ) {
                $( table.cells().nodes() ).removeClass( 'highlight' );
                $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
            }
        } )
        .on( 'mouseleave', function () {
            $( table.cells().nodes() ).removeClass( 'highlight' );
        } );


        $('#'+idTabla).on('draw.dt', function () {
                    $('[data-toggle="tooltip"]').tooltip();
                }); 


        // Setup - add a text input to each footer cell
    $('#'+idTabla+' tfoot th').each( function () {
        if($(this).index()>0){
        var title = $('#'+idTabla+' thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        }
    } );

    
    // DataTable
    var table = $('#'+idTabla).DataTable();

     // table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
     //        //var cell = table.cell({ row: rowIdx, column: 0 }).node();
     //          alert('dsaf');
     //        //$(cell).addClass('warning');
     //    });

    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        
        $( 'input', this.footer() ).on( 'blur change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );

        $('#btnLimpiarFiltros').click(function() 
        {
            
                that
                    .search('')
                    .draw();



            $( 'input', that.footer() ).each( function () {
                $(this).val('');
            });

        });
    })
}

function recargaPagina() 
{
    location.reload();
}