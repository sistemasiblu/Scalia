<?php 
$idDocumentoImportacion = $_GET['idDocumento'];

$docImportacion  = DB::Select('SELECT * from documentoimportacion where idDocumentoImportacion = '.$idDocumentoImportacion);

$importacion = get_object_vars($docImportacion[0]);

?>

@extends('layouts.grid') 
@section('titulo')<h3 class="pestana" id="titulo"><center>Consulta Embarque <?php echo $importacion['nombreDocumentoImportacion'];?></h3>@stop
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

<?php 
    if (isset($datos[0])) 
        $dato = get_object_vars($datos[0]);
?>  

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
                            <li><a class="toggle-vis" data-column="0"><label> Iconos</label></a></li>
                            <li><a class="toggle-vis" data-column="1"><label> Embarque</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Puerto de carga</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Agente de carga</label></a></li>
                            <li><a class="toggle-vis" data-column="4"><label> Fecha real embarque</label></a></li>
                            <li><a class="toggle-vis" data-column="5"><label> Proveedor</label></a></li>
                            <li><a class="toggle-vis" data-column="6"><label> PI</label></a></li>
                            <li><a class="toggle-vis" data-column="7"><label> Factura</label></a></li>
                            <li><a class="toggle-vis" data-column="8"><label> Contenedor</label></a></li>
                            <li><a class="toggle-vis" data-column="9"><label> Doc. Transporte</label></a></li>
                            <li><a class="toggle-vis" data-column="10"><label> File</label></a></li>
                        </ul>
                    </div>
                    <table id="tembarque" name="tembarque" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">
                                <th style="width:60px;padding: 1px 8px;" data-orderable="false">
                                <a><span class="glyphicon glyphicon-remove-sign" style="color:white; cursor:pointer;" id="btnLimpiarFiltros"></span></a>
                                </th>
                                <th><b>Embarque</b></th>
                                <th><b>Puerto de carga</b></th>
                                <th><b>Agente de carga</b></th>
                                <th><b>Fecha real embarque</b></th>
                                <th><b>Proveedor</b></th>
                                <th><b>PI</b></th>
                                <th><b>Factura</b></th>
                                <th><b>Contenedor</b></th>
                                <th><b>Doc. Transporte</b></th>
                                <th><b>File</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">
                                <th style="width:40px;padding: 1px 8px;">
                                    &nbsp;
                                </th>
                                <th>Embarque</th>
                                <th>Puerto de carga</th>
                                <th>Agente de carga</th>
                                <th>Fecha real embarque</th>
                                <th>Proveedor</th>
                                <th>PI</th>
                                <th>Factura</th>
                                <th>Contenedor</th>
                                <th>Doc. Transporte</th>
                                <th>File</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>


<script type="text/javascript">

    $(document).ready( function () {

        var lastIdx = null;
        var idDoc = "<?php echo $_GET['idDocumento'];?>";
        var modificar = '<?php echo (isset($datos[0]) ? $dato["modificarRolOpcion"] : 0);?>';
        var table = $('#tembarque').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosConsultaEmbarque?idDocumento="+idDoc+"&modificar="+modificar+"')!!}",
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

        $('#tembarque tbody')
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
    $('#tembarque tfoot th').each( function () {
        if($(this).index()>0){
        var title = $('#tembarque thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        }
    } );
 
    // DataTable
    var table = $('#tembarque').DataTable();
 
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