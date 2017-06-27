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
<!-- DataTables -->
        {!!Html::script('DataTables/media/js/jquery.js'); !!}
        {!!Html::script('DataTables/media/js/jquery.dataTables.js'); !!}
        {!!Html::style('DataTables/media/css/jquery.dataTables.min.css'); !!}
        {!!Html::style('assets/bootstrap-v3.3.5/css/bootstrap.min.css'); !!}
        {!!Html::script('assets/bootstrap-v3.3.5/js/bootstrap.min.js'); !!} 

        <div class="container">
            <div class="row">
                <div class="container">
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">
                        <button  type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                       <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> Referencia</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Codigo Barras</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Descripcion</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Marca</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Modelo</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Serial</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Tipo Activo</label></a></li>
                            <li><a class="toggle-vis" data-column="0"><label> Estado</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tactivoselect" name="tactivoselect" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">

                                <th><b>Referencia</b></th>
                                <th><b>Codigo Barras</b></th>
                                <th><b>Descripcion</b></th>
                                <th><b>Marca</b></th>
                                <th><b>Modelo</b></th>
                                <th><b>Serial</b></th>
                                <th><b>Tipo Activo</b></th>
                                <th><b>Estado</b></th>

                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">
                              

                                <th>Referencia</th>
                                <th>Codigo Barras</th>
                                <th>Descripcion</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Serial</th>
                                <th>Tipo Activo</th>
                                <th>Estado</th>

                               
                            </tr>
                        </tfoot>
                    </table>

                    <div class="modal-footer">
                        <button id="botonActivo" name="botonCampo" type="button" class="btn btn-primary" >Seleccionar</button>
                    </div>

                

                </div>
            </div>
        </div>


<script type="text/javascript">

    $(document).ready( function () {

        
        
        var lastIdx = null;
        var table = $('#tactivoselect').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosActivoSelect')!!}",
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

        $('#tactivoselect tbody')
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
    $('#tactivoselect tfoot th').each( function () {
        if($(this).index()>0){
        var title = $('#tactivoselect thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        }
    } );
 
    // DataTable
    var table = $('#tactivoselect').DataTable();
 
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


    $('#tactivoselect tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');

        var datos = table.rows('.selected').data();


    } );
 
    
    
    $('#botonActivo').click(function() {
        var datos = table.rows('.selected').data();
        
        for (var i = 0; i < datos.length; i++) 
        {
            var valores = new Array(0,0,0,datos[i][0],datos[i][1],datos[i][6],datos[i][3],1,"","");

            window.parent.movimiento.agregarCampos(valores,'A');
            window.parent.calcularTotales();
        }

        window.parent.$("#ModalActivo").modal("hide");
        } );



    
});
    
</script>

