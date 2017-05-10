<?php 

?>

@include('alerts/request')

@if(isset($rechazoactivo))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::model($rechazoactivo,['route'=>['rechazoactivo.destroy',$rechazoactivo->idRechazoActivo],'method'=>'DELETE'])!!}
  @else
    {!!Form::model($rechazoactivo,['route'=>['rechazoactivo.update',$rechazoactivo->idRechazoActivo],'method'=>'PUT'])!!}
  @endif
@else
  {!!Form::open(['route'=>'rechazoactivo.store','method'=>'POST'])!!}
@endif

@extends('layouts.vista')
@section('titulo')<br><h4 id="titulo"><center>ASIGNACION ACTIVO</center></h4>@stop
@section('content')
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>ASIGNACION ACTIVO</title>

 <script>

 var movimientoactivodetalle="";

var valorDetalle = [0,0,''];
$(document).ready(function()
{

  detalle=new Atributos('detalle','contenedor-detalle','detalle-');
  detalle.campoid = 'idTransaccionActivoCampoE';
  detalle.campoEliminacion = 'encabezadoEliminar';
  detalle.campos=['idTransaccionActivoCampoE', 'CampoTransaccion_idCampoTransaccionE', 'descripcionCampoTransaccionE','gridTransaccionActivoCampoE','vistaTransaccionActivoCampoE','obligatorioTransaccionActivoCampoE'];
  detalle.etiqueta=['input','input','input','checkbox','checkbox','checkbox'];
  detalle.tipo=['hidden','hidden','','checkbox','checkbox','checkbox'];
  detalle.estilo=['','','width:200px; height:35px;','width:100px; height:31px;display: inline-block;','width:100px; height:31px;display: inline-block;','width:100px; height:31px;display: inline-block;'];
  detalle.clase=['','','','','',''];
  detalle.sololectura=[false,false,true,true,true];
  detalle.completar=['off','off','off','off','off'];
  detalle.opciones = [[],[],[],[],[]];      
  detalle.funciones=['','','','',''];

  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';

  for(var j=0; j < movimientoactivodetalle.length; j++)
  {
      detalle.agregarCampos(JSON.stringify(movimientoactivodetalle[j]),'L');
  }

});


 function abrirModalMovimiento()
  {
      $('#ModalMovimiento').modal('show');

    }

    function  abrirTransaccionActivo(id)
{
  //alert('entra');
  if ($('#TransaccionActivo_idTransaccionActivo').val()=="")
  {
    alert("Debe Seleccionar un Tipo de Documento");
  }
  else
  {
    //$('#ModalTransaccionActivo').modal('show');
    var lastIdx = null;
    $("#tmovimientoactivo").DataTable().ajax.url("http://"+location.host+"/datosTransaccionActivoSelect?id="+id).load();
    // Abrir modal
    $("#ModalTransaccionActivo").modal('show');

   /* var table = $('#tmovimientoactivo').DataTable( 
    {
      "order": [[ 1, "asc" ]],
      "aProcessing": true,
      "aServerSide": true,
      "stateSave":true,
      "ajax": "{!! URL::to ('/datosTransaccionActivoSelect?id="+id+"')!!}",
      "language": 
      {
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
            "oPaginate": 
            {
                "sFirst":    "Primero",
                "sLast":     "&Uacute;ltimo",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": 
            {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
      }
    });*/
           
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
        var title = $('#tmovimientoactivo thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
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
     $('#tmovimientoactivo tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );
 
     $('#botonActivo').click(function() {
        var datos = table.rows('.selected').data();

        for (var i = 0; i < datos.length; i++) 
        {
              
        }

        } );



  }
}//fin function abrirTransaccionActivo





 </script>


  
</head>
<body >

 <div class="container">
 

    <div class="form-group">
  <div class="col-sm-6" position="left">
    {!!Form::label('tipoNumeracionTransaccionActivo', 'N. Asignacion', array('class' => 'col-sm-6 control-label')) !!}
    <div class="col-sm-6">
     {!!Form::text('tipoNumeracionTransaccionActivo',null,['class' => 'form-control', 'style'=>'padding-left:2px;'])!!}
      </div>
      {!!Form::label('TransaccionActivo_idTransaccionActivo', 'Tipo Documento Referencia', array('class' => 'col-sm-6 control-label')) !!}
    <div class="col-sm-6">
      {!!Form::select('TransaccionActivo_idTransaccionActivo',@$transaccionactivo,null,['class'=>'form-control','placeholder'=>'Seleccione'])!!}
    </div>
      {!!Form::label('TransaccionGrupo_idTransaccionGrupo', 'Usuario Asigna Transaccion', array('class' => 'col-sm-6 control-label')) !!} 
    <div class="col-sm-6" >
     {!!Form::text('desdeTransaccionActivo',null,['class'=>'form-control','placeholder'=>''])!!}
    </div>
     
 </div>
  <div class="col-sm-6" position="right">
     {!!Form::label('longitudTransaccionActivo', 'Fecha/Hora Elaboracion', array('class' => 'col-sm-6 control-label')) !!}
     <div class="col-sm-6">
       {!!Form::text('TransaccionActivo_idTransaccionActivo',@$transaccionactivo->longitudTransaccionActivo,['class'=>'form-control','placeholder'=>''])!!}
     </div>
       {!!Form::label('TransaccionActivo_idTransaccionActivo', 'Documento Referencia', array('class' => 'col-sm-6 control-label')) !!}
     
     <div class="col-sm-6" onclick="abrirTransaccionActivo($('#TransaccionActivo_idDocumentoInterno').val());" style="cursor:pointer;">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-pencil-square-o"></i>
        </span>
          {!!Form::text('hastaTransaccionActivo',@$transaccionactivo->hastaTransaccionActivo,['class'=>'form-control','placeholder'=>''])!!}
         
      </div>
    </div>
       
  </div>

  

</div>
</div>
<br><br>
<div class="form-group">
          <fieldset id='varioslistachequeo-form-fieldset'>
            <div class="form-group"  id='test'>
              <div class="col-sm-12">
                <div class="row show-grid">
                  <div class="col-md-1" style="width: 40px;height: 35px;" >
                    <span class="glyphicon glyphicon-plus" onclick="abrirModalMovimiento();"></span> 

                  </div>
                  <div class="col-md-1" style="width: 200px;height: 35px;"><b>Referencia</b></div>
                  <div class="col-md-1" style="width: 200px;height: 35px; "><b>Serie</b></div>
                  <div class="col-md-1" style="width: 300px;height: 35px; "><b>Descripcion</b></div>
                  <div class="col-md-1" style="width: 200px;height: 35px; "><b>Localizacion</b></div>
                  <div class="col-md-1" style="width: 300px;height: 35px; "><b>Responsable</b></div>

                  <div id="contenedor-detalle"></div>
                </div>      
              </div>
            </div>
          </fieldset>
</div>

<center><br><br>
@if(isset($rechazoactivo))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  @else
    {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  @endif
@else
   {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
@endif
{!! Form::close() !!}  
</center>        
</body>
</html>
@stop
<div id="ModalTransaccionActivo" class="modal fade" role="dialog" style="display:none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <div style="width:100%; overflow: scroll;">
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
                          <li><a class="toggle-vis" data-column="0"><label>ID</label></a></li>
                          <li><a class="toggle-vis" data-column="1"><label>Numero</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Fecha Elaboracion</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Fecha Inicio</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Fecha Fin</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Tercero</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Estado</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Usuario Creador</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Usuario Aprobador</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tmovimientoactivo" name="tmovimientoactivo" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">

                                <th><b>ID</b></th>
                                <th><b>Numero</b></th>
                                <th><b>Fecha Elaboracion</b></th>
                                <th><b>Fecha Inicio</b></th>
                                <th><b>Fecha Fin</b></th>
                                <th><b>Tercero</b></th>
                                <th><b>Estado</b></th>
                                <th><b>Usuario Creador</b></th>
                                <th><b>Usuario Aprobador</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">

                                <th>ID</th>
                                <th>Numero</th>
                                <th>Fecha Elaboracion</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Tercero</th>
                                <th>Estado</th>
                                <th>Usuario Creador</th>
                                <th>Usuario Aprobador</th>
                                

                            </tr>
                        </tfoot> 
                    </table>

                    <div class="modal-footer">
                        <button id="botonActivo" name="botonActivo" type="button" class="btn btn-primary" >Seleccionar</button>
                    </div>

                </div>
            </div>
        </div>

          </div>
        </div><!--  Fin div modal-body  -->
    </div><!--  Fin div modal-content  -->
  </div><!--  Fin div modal-dialog  -->
</div><!--  Fin div ModaltransaccionActivo  -->



<div id="ModalMovimiento" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/ActivoMovimientoDetalleSelect"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>


