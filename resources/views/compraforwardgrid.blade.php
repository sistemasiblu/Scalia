<?php 
    $tipo = $_GET['tipo'];
?>
@extends('layouts.grid')
@section('titulo')<h3 class="pestana" id="titulo"><center>Forwards - Compras</center></h3>@stop
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
                <a href="#" onclick="cambiarEstado('forward');" title="Mostrar forwards">
                        <img  src='imagenes/menu/forward.png' style="width:45px; height:45px;">
                </a>
                <a href="#" onclick="cambiarEstado('compra');" title="Mostrar compras">
                        <img  src='imagenes/menu/compra.png' style="width:45px; height:45px;">
                </a>
                    <br>
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:0px" title="Columns">
                        <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> Forward</label></a></li>
                            <li><a class="toggle-vis" data-column="1"><label> Descripcion</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Vencimiento</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Valor de forward</label></a></li>
                            <li><a class="toggle-vis" data-column="4"><label> Compra</label></a></li>
                            <li><a class="toggle-vis" data-column="5"><label> IM</label></a></li>
                            <li><a class="toggle-vis" data-column="6"><label> Temporada</label></a></li>
                            <li><a class="toggle-vis" data-column="7"><label> Proveedor</label></a></li>
                            <li><a class="toggle-vis" data-column="8"><label> Valor programado compra</label></a></li>
                        </ul>
                    </div>
                    <table id="tcompraforwardgrid" name="tcompraforwardgrid" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">
                                <th><b>Forward</b></th>
                                <th><b>Descripcion</b></th>
                                <th><b>Vencimiento</b></th>
                                <th><b>Valor de forward</b></th>
                                <th><b>Compra</b></th>
                                <th><b>IM</b></th>
                                <th><b>Temporada</b></th>
                                <th><b>Proveedor</b></th>
                                <th><b>Valor programado compra</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">
                                <th>Forward</th>
                                <th>Descripcion</th>
                                <th>Vencimiento</th>
                                <th>Valor de forward</th>
                                <th>Compra</th>
                                <th>IM</th>
                                <th>Temporada</th>
                                <th>Proveedor</th>
                                <th>Valor programado compra</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
{!!Form::button('Limpiar filtros',["class"=>"btn btn-primary","id"=>'btnLimpiarFiltros'])!!}

<script type="text/javascript">

    $(document).ready( function () {

        
        /*$('#tcompraforwardgrid').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosCompraForwardGrid')!!}",
        });*/
        var lastIdx = null;
        var tipo = '<?php echo $tipo?>';
        var table = $('#tcompraforwardgrid').DataTable( {
            "dom": 'Bfrtip',
            "buttons": ['excel'],
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosCompraForwardGrid?tipo="+tipo+"')!!}",
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

        $('#tcompraforwardgrid tbody')
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
    $('#tcompraforwardgrid tfoot th').each( function () {
        var title = $('#tcompraforwardgrid thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#tcompraforwardgrid').DataTable();
 
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
        });
    })
});

function cambiarEstado(tipo)
{
    location.href= 'http://'+location.host+"/compraforward?tipo="+tipo;
}
    
</script>

@stop