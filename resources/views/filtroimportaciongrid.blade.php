<?php 
    // $tipo = $_GET['tipo'];
?>
@extends('layouts.grid')
@section('titulo')<h3 class="pestana" id="titulo"><center>Consulta de importación</center></h3>@stop
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
    <div style="overflow:auto;">
        <div class="container">
            <div class="row">
                <div class="container">
                <!-- <a href="#" onclick="cambiarEstado('forward');" title="Mostrar forwards">
                        <img  src='imagenes/menu/forward.png' style="width:45px; height:45px;">
                </a>
                <a href="#" onclick="cambiarEstado('compra');" title="Mostrar compras">
                        <img  src='imagenes/menu/compra.png' style="width:45px; height:45px;">
                </a> -->
                    <br>
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:0px" title="Columns">
                        <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> Cliente</label></a></li>
                            <li><a class="toggle-vis" data-column="1"><label> Compra</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Proveedor</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Valor FOB</label></a></li>
                            <li><a class="toggle-vis" data-column="4"><label> Valor Embarcado</label></a></li>
                            <li><a class="toggle-vis" data-column="5"><label> Unidades</label></a></li>
                            <li><a class="toggle-vis" data-column="6"><label> Unidades Embarcadas</label></a></li>
                            <li><a class="toggle-vis" data-column="7"><label> Puerto Embarque</label></a></li>
                            <li><a class="toggle-vis" data-column="8"><label> Volumen</label></a></li>
                            <li><a class="toggle-vis" data-column="9"><label> Delivery</label></a></li>
                            <li><a class="toggle-vis" data-column="10"><label> Fecha de Forward</label></a></li>
                            <li><a class="toggle-vis" data-column="11"><label> Tiempo en bodega</label></a></li>
                            <li><a class="toggle-vis" data-column="12"><label> Dias pagos</label></a></li>
                            <li><a class="toggle-vis" data-column="13"><label> Reserva</label></a></li>
                            <li><a class="toggle-vis" data-column="14"><label> Fecha de Embarque</label></a></li>
                            <li><a class="toggle-vis" data-column="15"><label> Arribo a puerto</label></a></li>
                            <li><a class="toggle-vis" data-column="16"><label> Dias de transito</label></a></li>
                            <li><a class="toggle-vis" data-column="17"><label> Fecha maximo de despacho</label></a></li>
                            <li><a class="toggle-vis" data-column="18"><label> Fecha maxima de embarque</label></a></li>
                            <li><a class="toggle-vis" data-column="19"><label> Fecha de ingreso a bodega</label></a></li>
                        </ul>
                    </div>
                    <table id="tfiltroimportaciongrid" name="tfiltroimportaciongrid" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">
                                <th><b>Cliente</b></th>
                                <th><b>Compra</b></th>
                                <th><b>Proveedor</b></th>
                                <th><b>Valor FOB</b></th>
                                <th><b>Valor Embarcado</b></th>
                                <th><b>Unidades</b></th>
                                <th><b>Unidades Embarcadas</b></th>
                                <th><b>Puerto Embarque</b></th>
                                <th><b>Volumen</b></th>
                                <th><b>Delivery</b></th>
                                <th><b>Fecha de Forward</b></th>
                                <th><b>Tiempo en bodega</b></th>
                                <th><b>Dias pagos</b></th>
                                <th><b>Reserva</b></th>
                                <th><b>Fecha de Embarque</b></th>
                                <th><b>Arribo a puerto</b></th>
                                <th><b>Dias de transito</b></th>
                                <th><b>Fecha maximo de despacho</b></th>
                                <th><b>Fecha maxima de embarque</b></th>
                                <th><b>Fecha de ingreso a bodega</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">
                                <th>Cliente</th>
                                <th>Compra</th>
                                <th>Proveedor</th>
                                <th>Valor FOB</th>
                                <th>Valor Embarcado</th>
                                <th>Unidades</th>
                                <th>Unidades Embarcadas</th>
                                <th>Puerto Embarque</th>
                                <th>Volumen</th>
                                <th>Delivery</th>
                                <th>Fecha de Forward</th>
                                <th>Tiempo en bodega</th>
                                <th>Dias pagos</th>
                                <th>Reserva</th>
                                <th>Fecha de Embarque</th>
                                <th>Arribo a puerto</th>
                                <th>Dias de transito</th>
                                <th>Fecha maximo de despacho</th>
                                <th>Fecha maxima de embarque</th>
                                <th>Fecha de ingreso a bodega</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
{!!Form::button('Limpiar filtros',["class"=>"btn btn-primary","id"=>'btnLimpiarFiltros'])!!}

<script type="text/javascript">

    $(document).ready( function () {

        
        /*$('#tfiltroimportaciongrid').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosFiltroImportacionGrid')!!}",
        });*/
        var lastIdx = null;
        var table = $('#tfiltroimportaciongrid').DataTable( {
            "dom": 'Bfrtip',
            "buttons": ['excel'],
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosFiltroImportacionGrid')!!}",
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

        $('#tfiltroimportaciongrid tbody')
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
    $('#tfiltroimportaciongrid tfoot th').each( function () {
        var title = $('#tfiltroimportaciongrid thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#tfiltroimportaciongrid').DataTable();
 
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

        $('input', that.footer() ).each( function () {
            $(this).val('');
        });
    })
});

function cambiarEstado(tipo)
{
    location.href= 'http://'+location.host+"/compraforward?tipo="+tipo;
}
    
</script>

@stop