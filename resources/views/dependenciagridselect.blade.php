@extends('layouts.modal') 
@section('titulo')<h3 id="titulo"><center></h3>@stop
@section('content')
<style>
    tfoot input {
                width: 100%;
                padding: 3px;
                background-color: #fff;
                background-image: none;
                border: 1px solid #ccc;
                border-radius: 4px;
            }
</style> 
        <div class="container">
            <div class="row">
                <div class="container">
                    <br>
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">
                        <button  type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> ID</label></a></li>
                            <li><a class="toggle-vis" data-column="1"><label> C&oacute;digo</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Nombre</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Avreviatura</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tdependenciaselect" name="tdependenciaselect" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">

                                <th><b>ID</b></th>
                                <th><b>C&oacute;digo</b></th>
                                <th><b>Nombre</b></th>
                                <th><b>Abreviatura</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">

                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Abreviatura</th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="modal-footer">
                        <button id="boton" name="boton" type="button" class="btn btn-primary" >Seleccionar</button>
                    </div>

                </div>
            </div>
        </div>


<script type="text/javascript">

    $(document).ready( function () {

        
        /*$('#tdependenciaselect').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosDependencia')!!}",
        });*/
        var lastIdx = null;
        var table = $('#tdependenciaselect').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosDependenciaSelect')!!}",
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
         
        $('a.toggle-vis').on( 'click', function (e) {
            e.preventDefault();
     
            // Get the column API object
            var column = table.column( $(this).attr('data-column') );
     
            // Toggle the visibility
            column.visible( ! column.visible() );
        } );

        $('#tdependenciaselect tbody')
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


        // Setup - add a text input to each footer cell
    

    $('#tdependenciaselect tfoot th').each( function () {
        var title = $('#tdependenciaselect thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#tdependenciaselect').DataTable();
 
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
    })

     $('#tdependenciaselect tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );
 
     $('#boton').click(function() {
        var datos = table.rows('.selected').data();
        var ids = "";
        var abrev = "";
        for (var i = 0; i < datos.length; i++) {
            ids += ' '+ datos[i][0]+',';
            abrev += ' '+ datos[i][3]+',';
        };
        window.parent.datosSelect(ids, abrev);
        window.parent.$("#myModal").modal("hide");

    } );

    
});
    
</script>

@stop