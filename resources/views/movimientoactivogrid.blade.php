    <?php
    $id = isset($_GET["idTransaccionActivo"]) ? $_GET["idTransaccionActivo"] : 0; 
    $TipoEstado = (isset($_GET["TipoEstado"]) ? $_GET["TipoEstado"] : 'Proceso');

    ?>

<?php
$visible = '';
     $aprobador = 0;
    if (isset($datos[0])) 
    {
       $dato = get_object_vars($datos[0]);

        $aprobador = (isset($dato['autorizarTransaccionRol']) ? $dato['autorizarTransaccionRol'] : 0);

         

        if ($dato['adicionarTransaccionRol'] == 1) 
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

$id = isset($_GET["idTransaccionActivo"]) ? $_GET["idTransaccionActivo"] : 0; 
$campos = DB::select(
    'SELECT codigoTransaccionActivo, nombreTransaccionActivo, nombreCampoTransaccion,descripcionCampoTransaccion, 
            gridTransaccionActivoCampo, relacionTablaCampoTransaccion, relacionNombreCampoTransaccion, relacionAliasCampoTransaccion
    FROM transaccionactivo
    left join transaccionactivocampo
    on transaccionactivo.idTransaccionActivo = transaccionactivocampo.TransaccionActivo_idTransaccionActivo
    left join campotransaccion
    on transaccionactivocampo.CampoTransaccion_idCampoTransaccion = campotransaccion.idCampoTransaccion
    where   transaccionactivo.idTransaccionActivo = '.$id.' and
            gridTransaccionActivoCampo = 1');

$camposGrid = 'idMovimientoActivo, numeroMovimientoActivo, fechaElaboracionMovimientoActivo, fechaInicioMovimientoActivo, fechaFinMovimientoActivo, Tercero_idTercero, estadoMovimientoActivo, Users_idCrea, Users_idCambioEstado';
//$camposBase = 'idMovimientoActivo,numeroMovimientoActivo,fechaElaboracionMovimientoActivo, Tercero_idTercero';
$camposBase = 'idMovimientoActivo, numeroMovimientoActivo, fechaElaboracionMovimientoActivo, fechaInicioMovimientoActivo, fechaFinMovimientoActivo, Tercero_idTercero, estadoMovimientoActivo, Users_idCrea, Users_idCambioEstado';

$titulosGrid = 'ID, Número, Fecha Elaboracion, Tercero';



for($i = 0; $i < count($campos); $i++)
{
    $datos = get_object_vars($campos[$i]); 
    
    $camposGrid .= ', '. $datos["relacionTablaCampoTransaccion"].'.'.$datos["relacionNombreCampoTransaccion"]  .
                     ($datos["relacionAliasCampoTransaccion"] == null 
                        ? ''
                        : ' As '. $datos["relacionAliasCampoTransaccion"]);

    $camposBase .= ','.($datos["relacionAliasCampoTransaccion"] == null 
                        ? $datos["relacionNombreCampoTransaccion"]
                        : $datos["relacionAliasCampoTransaccion"]);

    $titulosGrid .= ', '. $datos["descripcionCampoTransaccion"];
    $codTransaccion = $datos["codigoTransaccionActivo"];
    $nomTransaccion = $datos["nombreTransaccionActivo"];
}

//print_r($datos);
?>


@extends('layouts.grid')
@section('titulo')<h3 id="titulo"><center><?php 
echo @$nomTransaccion.'<br>['.
(@$TipoEstado == '' ? 'Todos' : @$TipoEstado).']';?></center></h3>@stop
@section('content')

{!!Html::script('js/movimientoactivo.js'); !!}

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


<script>

    $(document).ready( function() 
    {

    <?php  
    $id = isset($_GET["idTransaccionActivo"]) ? $_GET["idTransaccionActivo"] : 0; 
   
    $self= basename($_SERVER["PHP_SELF"]).'?idTransaccionActivo='.$id;
    header("refresh:300;  url=$self"); 
    ?>



});

    
/*
     var movimientoactivodetalle = '<?php echo (isset($movimientoactivodetalle) ? json_encode($movimientoactivodetalle) : "");?>';
  movimientoactivodetalle = (movimientoactivodetalle != '' ? JSON.parse(movimientoactivodetalle) : '');
  console.log(movimientoactivodetalle);*/


var valormovimiento = [0,0,''];
  equipos=new Atributos('equipos','equiposMovimientoActivo','equipos_');
  equipos.botonEliminacion = false;
  equipos.campoid = 'idMovimientoActivoDetalle';
  equipos.campoEliminacion = 'movimientoEliminar';
  equipos.campos=['idMovimientoActivoDetalle','idMovimientoActivo','idActivo','codigoActivo', 'serieActivo','nombreActivo', 'cantidadMovimientoActivoDetalle','observacionMovimientoActivoDetalle','estadoMovimientoActivoDetalle','RechazoActivo_idRechazoActivo'];
  equipos.etiqueta=['input','input','input','input','input','input','input','input','select','select'];
  equipos.tipo=['hidden','hidden','hidden','','','','','','',''];
  equipos.estilo=['','','','width:60px; height:35px;','width:100px;  height:35px;','width:260px; height:35px;','width:70px; height:35px;','width:200px; height:35px;','width:100px; height:35px;','width:200px; height:35px;'];
  equipos.clase=['','','','','','','','','',''];
  equipos.sololectura=[false,false,false,true,true,true,true,true,false,false];
  equipos.completar=['off','off','off','off','off','off','off','off','off','off'];
  

 var idRechazo = '<?php echo isset($idRechazo) ? $idRechazo : "";?>';
  var nombreRechazo = '<?php echo isset($nombreRechazo) ? $nombreRechazo : "";?>';
   var Rechazo = [JSON.parse(idRechazo),JSON.parse(nombreRechazo)];  
  var componentesActivo = ['onclick','VerificacionComponentes(this.id);'];
  equipos.funciones=['','','','','',componentesActivo,'','','',''];
  equipos.opciones = [[],[],[],[],[],[],[],[],[['1','2'],['Aprobado','Rechazado']],Rechazo];      
 //eliminarRegistro.tipo='hidden';

  /*for(var j=0; j < movimientoactivodetalle.length; j++)
  {
    equipos.agregarCampos(JSON.stringify(movimientoactivodetalle[j]),'L');
  }*/
     function recargarPage()
    {  
        location.reload();
    }

 
    var id ="<?php echo $id;?>";
    var modificar = "<?php echo (isset($dato['modificarTransaccionRol']) ? $dato['modificarTransaccionRol'] : 0);?>";
    var eliminar = "<?php echo (isset($dato['anularTransaccionRol']) ? $dato['anularTransaccionRol'] : 0);?>";
    var consultar = "<?php echo (isset($dato['consultarTransaccionRol']) ? $dato['consultarTransaccionRol'] : 0);?>";
    var aprobar = "<?php echo (isset($dato['autorizarTransaccionRol']) ? $dato['autorizarTransaccionRol'] : 0);?>";
   /* $(document).ready( function () {
        configurarGrid('tmovimientoactivo',"{!! URL::to ('/datosMovimientoActivo?idTransaccionActivo="+id+"&TipoEstado="+TipoEstado+"&modificar="+modificar+"&eliminar="+eliminar+"&consultar="+consultar+"&aprobar="+aprobar+"')!!}");
    });*/
</script>


        <div class="container">
            <div class="row">
                <div class="container">
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'', modificar, eliminar, consultar, aprobar);" title="Mostrar Todos">
                        <img  src='imagenes/iconoscrm/sin_filtro.png' style="width:28px; height:28px;">
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Proceso', modificar, eliminar, consultar, aprobar);" title="Mostrar Nuevas">
                        <img  src='imagenes/iconoscrm/estado_nuevo.png' style="width:28px; height:28px;">
                    </a>
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Aprobado Parcial', modificar, eliminar, consultar, aprobar);" title="Mostrar Aprobado Parcial">
                        <img  src='imagenes/iconoscrm/estado_pendiente.png' style="width:28px; height:28px;">
                    </a>
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Aprobado Total', modificar, eliminar, consultar, aprobar);" title="Mostrar Aprobado Total">
                        <img  src='imagenes/iconoscrm/estado_exitoso.png' style="width:28px; height:28px;">
                    </a>
                    <a href="#" onclick="cambiarEstado(<?php echo $id;?>,'Rechazado', modificar, eliminar, consultar, aprobar);" title="Mostrar Rechazados">
                        <img  src='imagenes/iconoscrm/estado_fallido.png' style="width:28px; height:28px;">
                    </a>
                   
                    <a style="float: right;" href="#" onclick="mostrarTableroCRM(<?php echo $id;?>);" title="Mostrar Nuevas">
                        <img  src='imagenes/iconoscrm/dashboardcrm.png' style="width:36px; height:36px;">
                    </a>
                                 
                    <div class="btn-group" style="margin-left: 94%;margin-bottom:4px" title="Columns">

                        <button type="button" class="btn btn-default dropdown-toggle"data-toggle="dropdown">
                            <i class="glyphicon glyphicon-th icon-th"></i> 
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            <li><a class="toggle-vis" data-column="0"><label> Iconos</label></a></li>
                            <?php 
                                $titulos = explode(',', $titulosGrid);
                                for($i = 0; $i < count($titulos); $i++)
                                {
                                    echo '<li><a class="toggle-vis" data-column="'.($i+1).'"><label> '.$titulos[$i].'</label></a></li>';
                                }
                            ?>

                           
                        </ul>
                    </div>
                    <div class="col-md-12" style="overflow: auto;">
                    <table id="tmovimientoactivo" name="tmovimientoactivo" class="display table-bordered" width="100%">
                        <thead >
                            <tr class="btn-primary">
                                <th style="width:40px;padding: 1px 8px;" data-orderable="false">
                                <a href=<?php echo "movimientoactivo/create?idTransaccionActivo=".$id."&aprobador=".$aprobador;?>><span style= "display: <?php echo $camposGrid;?> " class="glyphicon glyphicon-plus"></span></a>
                                 <a href=""><span onclick="recargarPage();" class="glyphicon glyphicon-refresh"></span></a>
                                </th>
                                <?php 
                                    for($i = 0; $i < count($titulos); $i++)
                                    {
                                        echo '<th><b>'.$titulos[$i].'</b></th>';
                                    }
                                ?>
                            </tr>
                        </thead>
                                        <tfoot>
                            <tr class="btn-default">
                                <th style="width:40px;padding: 1px 8px;">
                                    &nbsp;
                                </th>
                                <?php 
                                    for($i = 0; $i < count($titulos); $i++)
                                    {
                                        echo '<th>'.$titulos[$i].'</th>';
                                    }
                                ?>
                            </tr>
                        </tfoot>        
                    </table>
                    </div>
                </div>
            </div>
        </div>





<script type="text/javascript">





    $(document).ready( function () {
        var id = "<?php echo $id;?>";

        var camposBase = "<?php echo $camposBase;?>";
        var camposGrid = "<?php echo $camposGrid;?>";

        var lastIdx = null;
        
        var modificar = '<?php echo (isset($dato["modificarTransaccionRol"]) ? $dato["modificarTransaccionRol"] : 0);?>';
        var eliminar = '<?php echo (isset($dato["anularTransaccionRol"]) ? $dato["anularTransaccionRol"] : 0);?>';
        var consultar = '<?php echo (isset($dato["consultarTransaccionRol"]) ? $dato["consultarTransaccionRol"] : 0);?>';
        var aprobar = '<?php echo (isset($dato["autorizarTransaccionRol"]) ? $dato["autorizarTransaccionRol"] : 0);?>';
        var TipoEstado = '<?php echo $TipoEstado;?>';


        var table = $('#tmovimientoactivo').DataTable( {
            "order": [[ 2, "desc" ]],
            "aProcessing": true,
            "aServerSide": true,
            "stateSave":true,

            "ajax": "{!! URL::to ('/datosMovimientoActivo?idTransaccionActivo="+id+"&TipoEstado="+TipoEstado+"&modificar="+modificar+"&eliminar="+eliminar+"&consultar="+consultar+"&aprobar="+aprobar+"')!!}",
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

        $('#tmovimientoactivo tbody')
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
    $('#tmovimientoactivo tfoot th').each( function () {
        if($(this).index()>0){
        var title = $('#tmovimientoactivo thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
        }
    } );
 
    // DataTable
    var table = $('#tmovimientoactivo').DataTable();
 
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

    
});
    
    
</script>
<input type="hidden" id="token" value="{{csrf_token()}}"/> 

@stop

<div id="ModalAprobacionActivo"  class="modal fade" role="dialog" style="display:none;">

  <div class="modal-dialog" style="width:76%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Cambio de Estado</h4>
      </div>
        <div  class="modal-body">
            <div id="overflow" style="width:100%; overflow: none;">
          
                <div id="ContenidoAprobacionActivos" class="container">
                </div>

            </div><!--  Fin div over-flow  -->
        </div><!--  Fin div modal-body  -->
    </div><!--  Fin div modal-content  -->
  </div><!--  Fin div modal-dialog  -->
</div><!--  Fin div ModalAprobacionActivo  -->

<div id="ModalVerificacionComponentes"  class="modal fade" role="dialog" style="display:none;top:15%;">

  <div class="modal-dialog" style="width:40%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Verificación de Componentes</h4>
      </div>
        <div  class="modal-body">
            <div id="overflow" style="width:100%; overflow: none;">
          
                <div id="ContenidoVerificacionComponentes" class="container">
                </div>

            </div><!--  Fin div over-flow  -->
        </div><!--  Fin div modal-body  -->
    </div><!--  Fin div modal-content  -->
  </div><!--  Fin div modal-dialog  -->
</div><!--  Fin div ModalVerificacionComponentes  -->




