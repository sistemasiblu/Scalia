<?php 
$idDocumentoImportacion = $_GET['idDocumento'];

$docImportacion  = DB::Select('SELECT * from documentoimportacion where idDocumentoImportacion = '.$idDocumentoImportacion);

$importacion = get_object_vars($docImportacion[0]);

?>

@extends('layouts.grid') 

@section('titulo')<h3 class="pestana" id="titulo"><center>Compra <?php echo $importacion['nombreDocumentoImportacion'];?></h3>@stop
@section('content')
{!!Html::script('js/compra.js')!!}
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
    $visible = '';

    if (isset($datos[0])) 
    {
        $dato = get_object_vars($datos[0]);
        if ($dato['adicionarRolOpcion'] == 1) 
        {
            $visible = 'inline-block;';    
        }
        else
        {
            $visible = 'none;';
        }
    }
    else
    {
        $visible = 'none;';
    }
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
                            <li><a class="toggle-vis" data-column="1"><label> Versión</label></a></li>
                            <li><a class="toggle-vis" data-column="2"><label> Temporada</label></a></li>
                            <li><a class="toggle-vis" data-column="3"><label> Proveedor</label></a></li>
                            <li><a class="toggle-vis" data-column="4"><label> Cliente</label></a></li>
                            <li><a class="toggle-vis" data-column="5"><label> PI</label></a></li>
                            <li><a class="toggle-vis" data-column="6"><label> Valor</label></a></li>
                            <li><a class="toggle-vis" data-column="7"><label> Unidades</label></a></li>
                            <li><a class="toggle-vis" data-column="8"><label> Embarcadas</label></a></li>
                            <li><a class="toggle-vis" data-column="9"><label> Faltantes</label></a></li>
                            <li><a class="toggle-vis" data-column="10"><label> Estado</label></a></li>
                            <li><a class="toggle-vis" data-column="11"><label> Estado</label></a></li>
                        </ul>
                    </div>
                    <table id="tcompra" name="tcompra" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">
                                <th style="width:120px;padding: 1px 8px;" data-orderable="false">
                                <a href=<?php echo "compra/create?idDocumento=".$importacion['idDocumentoImportacion'].'&accion=crear';?>><span style="display: <?php echo $visible;?> color:white" class="glyphicon glyphicon-plus"></span></a>
                                <a href="#"><span style="color:white" class="glyphicon glyphicon-refresh"></span></a>
                                <a><span class="glyphicon glyphicon-remove-sign" style="color:white; cursor:pointer;" id="btnLimpiarFiltros"></span></a>
                                </th>
                                <th><b>Versión</b></th>
                                <th><b>Temporada</b></th>
                                <th><b>Proveedor</b></th>
                                <th><b>Cliente</b></th>
                                <th><b>PI</b></th>
                                <th><b>Valor</b></th>
                                <th><b>Unidades</b></th>
                                <th style="background:#A9F5A9;"><b>Embarcadas</b></th>
                                <th style="background:#F5A9A9;"><b>Faltantes</b></th>
                                <th><b>Estado</b></th>
                                <th><b>Usuario creador</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">
                                <th style="width:40px;padding: 1px 8px;">
                                    &nbsp;
                                </th>
                                <th>Versión</th>
                                <th>Temporada</th>
                                <th>Proveedor</th>
                                <th>Cliente</th>
                                <th>PI</th>
                                <th>Valor</th>
                                <th>Unidades</th>
                                <th>Embarcadas</th>
                                <th>Faltantes</th>
                                <th>Estado</th>
                                <th>Usuario creador</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    <input type="hidden" id="token" value="{{csrf_token()}}"/>
    
<script type="text/javascript">

    $(document).ready( function () {

        
        /*$('#tcompra').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosCompra')!!}",
        });*/
        var lastIdx = null;
        var idDoc = "<?php echo $_GET['idDocumento'];?>";
        var modificar = '<?php echo (isset($datos[0]) ? $dato["modificarRolOpcion"] : 0);?>';
        var eliminar = '<?php echo (isset($datos[0]) ? $dato["eliminarRolOpcion"] : 0);?>';
        var actualizar = '<?php echo $dato["consultarRolOpcion"];?>';
        var table = $('#tcompra').DataTable( {
            "order": [[ 1, "asc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,
            "ajax": "{!! URL::to ('/datosCompra?idDocumento="+idDoc+"&modificar="+modificar+"&eliminar="+eliminar+"&actualizar="+actualizar+"')!!}",
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

        $('#tcompra tbody')
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
    $('#tcompra tfoot th').each( function () {
        if($(this).index()>0){
        var title = $('#tcompra thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        }
    } );
 
    // DataTable
    var table = $('#tcompra').DataTable();
 
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
    <!-- ABRO EL MODAL Y DENTRO DE EL ESTAN LOS DATOS DETALLADOS DE LA COMPRA -->
<div id="modalDetalleTemporada" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content" style="width:100%x;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Detalles de la temporada</h4>
      </div>
        <div class="modal-body">
          <div id="detalleTemporada"></div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>