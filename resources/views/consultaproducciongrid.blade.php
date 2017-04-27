@extends('layouts.grid')
@section('titulo')<h3 class="pestana" id="titulo"><center>Informe de producción</center></h3>@stop
@section('content')
{!!Html::script('js/paginacionDataTables.js'); !!}
{!!Html::script('js/consultaproduccion.js'); !!}
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
                    <br>
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:0px" title="Columns">
                        <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> Referencia</label></a></li>
                            <li><a class="toggle-vis" data-column="1"><label> No. Op</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> No. Pedido</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Cliente</label></a></li>
                            <li><a class="toggle-vis" data-column="4"><label> Canal</label></a></li>
                            <li><a class="toggle-vis" data-column="5"><label> Estado</label></a></li>
                            <li><a class="toggle-vis" data-column="6"><label> Cantidad Op</label></a></li>
                            <li><a class="toggle-vis" data-column="7"><label> Cantidad remisionada</label></a></li>
                            <li><a class="toggle-vis" data-column="8"><label> Cantidad recibida</label></a></li>
                            <li><a class="toggle-vis" data-column="9"><label> Fecha de entrega</label></a></li>
                            <li><a class="toggle-vis" data-column="10"><label> Fecha Op</label></a></li>
                            <li><a class="toggle-vis" data-column="11"><label> Fecha de proceso</label></a></li>
                            <li><a class="toggle-vis" data-column="12"><label> Tipo de tejido</label></a></li>
                            <li><a class="toggle-vis" data-column="13"><label> Tipo de negocio</label></a></li>
                            <li><a class="toggle-vis" data-column="14"><label> Observaciones OP</label></a></li>
                            <li><a class="toggle-vis" data-column="14"><label> Observaciones Pedido</label></a></li>

                        </ul>
                    </div>
                    <table id="tproduccion" name="tproduccion" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">
                                <th><b>Referencia</b></th>
                                <th><b>No. Op</b></th>
                                <th><b>No. Pedido</b></th>
                                <th><b>Cliente</b></th>
                                <th><b>Canal</b></th>
                                <th><b>Estado</b></th>
                                <th><b>Cantidad Op</b></th>
                                <th><b>Cantidad remisionada</b></th>
                                <th><b>Cantidad recibida</b></th>
                                <th><b>Fecha de entrega</b></th>
                                <th><b>Fecha Op</b></th>
                                <th><b>Fecha de proceso</b></th>
                                <th><b>Tipo de tejido</b></th>
                                <th><b>Tipo de negocio</b></th>
                                <th><b>Observaciones OP</b></th>
                                <th><b>Observaciones Pedido</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">
                                <th>Referencia</th>
                                <th>No. Op</th>
                                <th>No. Pedido</th>
                                <th>Cliente</th>
                                <th>Canal</th>
                                <th>Estado</th>
                                <th>Cantidad OP</th>
                                <th>Cantidad remisionada</th>
                                <th>Cantidad recibida</th>
                                <th>Fecha de entrega</th>
                                <th>Fecha Op</th>
                                <th>Fecha de proceso</th>
                                <th>Tipo de tejido</th>
                                <th>Tipo de negocio</th>
                                <th>Observaciones OP</th>
                                <th>Observaciones Pedido</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

        <input type="hidden" id="token" value="{{csrf_token()}}"/>

        <div id="modalObservacion" class="modal fade" role="dialog">
          <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Observación</h4>
              </div>
              <div class="modal-body">
                <div id="observacion">
                    
                </div>
                <!-- <textarea class="ckeditor" style="width:570px" id="observacion"></textarea> -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="actualizarObservacion(document.getElementById('contObservacion').value)">Actualizar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
              </div>
            </div>

          </div>
          
        </div>
{!!Form::button('Limpiar filtros',["class"=>"btn btn-primary","id"=>'btnLimpiarFiltros'])!!}
        <script>
  // CKEDITOR.replace(('observacion'), {
  //     fullPage: true,
  //     allowedContent: true
  //   });  
</script>


<script type="text/javascript">

    $(document).ready( function () {

        
        /*$('#tproduccion').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosCompania')!!}",
        });*/
        var lastIdx = null;
        var table = $('#tproduccion').DataTable( {
            "dom": 'Bfrtip',
            "buttons": ['excel'],
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "bSortClasses": false,
            "deferRender" : true,
            "pagingType": "listbox",
            "pagingType": "full_numbers",
            "ajax": "{!! URL::to ('/datosConsultaProduccion')!!}",
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

        $('#tproduccion tbody')
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
    $('#tproduccion tfoot th').each( function () {
        var title = $('#tproduccion thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
    } );
 
    // DataTable
    var table = $('#tproduccion').DataTable();
 
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

    
</script>

@stop