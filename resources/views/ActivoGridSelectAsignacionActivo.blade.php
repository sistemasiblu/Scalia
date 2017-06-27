@extends('layouts.modal')
@section('titulo')<h3 id="titulo"><center></center></h3>@stop
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
                            <li><a class="toggle-vis" data-column="1"><label> Referencia</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Codigo Barras</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Descripcion</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Marca</label></a></li>
                     
                        </ul>
                    </div>
                    
                    <table id="tactivoSelect" name="tactivoSelect" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">

                                <th><b>ID</b></th>
                                <th><b>Referencia</b></th>
                                <th><b>Codigo Barras</b></th>
                                <th><b>Descripcion</b></th>
                                <th><b>Marca</b></th>
                               
                            </tr>
                        </thead>
                        <!-- <tfoot>
                            <tr class="btn-default active">

                                <th>ID</th>
                                <th>Referencia</th>
                                <th>Codigo Barras</th>
                                <th>Descripcion</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Serial</th>
                                <th>Tipo Activo</th>
                                <th>Estado</th>

                            </tr>
                        </tfoot> -->
                    </table>

                    <div class="modal-footer">
                        <button id="botonActivo" name="botonActivo" type="button" class="btn btn-primary" >Seleccionar</button>
                    </div>

                </div>
            </div>
        </div>


<script type="text/javascript">

    $(document).ready( function () {

        
        /*$('#tactivoSelect').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosRolSelect')!!}",
        });*/
        var lastIdx = null;
        var table = $('#tactivoSelect').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/AgregarPendientesAsignacionActivoDetalle')!!}",
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

        $('#tactivoSelect tbody')
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
   /* $('#tactivoSelect tfoot th').each( function () {
        var title = $('#tactivoSelect thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
    } );*/
 
    // DataTable
    var table = $('#tactivoSelect').DataTable();
 
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
     $('#tactivoSelect tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );
 
     $('#botonActivo').click(function() {
        var datos = table.rows('.selected').data();
        
        for (var i = 0; i < datos.length; i++) 
        {
            //var valores = new Array(0,0,0,0,datos[i][2],datos[i][3],datos[i][4],datos[i][5],datos[i][6],0);
            var valores = new Array(0,0,datos[i][0],datos[i][1],datos[i][2],datos[i][3],datos[i][4],datos[i][5],datos[i][6],0);


            window.parent.detalle.agregarCampos(valores,'A');
        }

        window.parent.$("#ModalMovimiento").modal("hide");
        } );

    
});
    
</script>

@stop