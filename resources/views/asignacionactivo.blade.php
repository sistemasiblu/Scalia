@extends('layouts.vista')
@section('titulo')<br><h4 id="titulo"><center>ASIGNACION ACTIVO</center></h4>@stop
@section('content')



<style type="text/css">
.select
{

  data-live-search="true"
}


</style>
<?php

if (isset($asignacionactivo)) 
{
  $numero=$asignacionactivo->numeroAsignacionActivo;
}

else
$numero='Automatico';


$usercrea=\App\User::where('id','=',\Session::get('idUsuario'))->lists('name','id');
$fechahora = Carbon\Carbon::now();

$solicitante = DB::select(
    'SELECT id as idUsuarioCrea, name as nombreUsuarioCrea
   from movimientoactivo
   inner join users
   on movimientoactivo.Users_idCrea=users.id
   where id= '.(isset($movimientoactivo) ? $movimientoactivo->Users_idCrea : \Session::get('idUsuario')));
if(count($solicitante) == 0)
{ 
 /* $solicitante['idUsuarioCrea']=null;
  $solicitante['nombreUsuarioCrea'] = null;*/
  $solicitante['idUsuarioCrea']=\Session::get('idUsuario');
  $solicitante['nombreUsuarioCrea'] = \Session::get('nombreUsuario');
}
else
{
  $solicitante = get_object_vars($solicitante[0]); 
}


?>
@section('content')
@include('alerts.request')
  {!!Html::script('/js/select2.min.js');!!}

{!!Html::script('js/movimientoactivo.js'); !!}
{!!Html::script('js/dropzone.js'); !!}<!--Llamo al dropzone-->
{!!Html::style('assets/dropzone/dist/min/dropzone.min.css'); !!}



   
<script>

 function abrirModalMovimiento()
  {
      $('#ModalMovimiento').modal('show');

  }

function consultaractivos()
{
  $('#ModalActivo').modal('show');
}


function  abrirTransaccionActivo(id)
{
  //alert('entra');
  if ($('#TransaccionActivo_idTransaccionActivo').val()=="")
  {
    alert("Debe Seleccionar un Tipo de Documento Referencia");
  }
  else
  {
    //$('#ModalTransaccionActivo').modal('show');
    var lastIdx = null;
    $("#tmovimientoactivo").DataTable().ajax.url("http://"+location.host+"/datosMovimientoActivoSelect?id="+id).load();
    // Abrir modal
    $("#ModalTransaccionActivo").modal('show');

           
    $('a.toggle-vis').on( 'click', function (e) 
    {
      e.preventDefault();

      // Get the column API object
      var column = table.column( $(this).attr('data-column') );

      // Toggle the visibility
      column.visible( ! column.visible() );
    });

    $('#tmovimientoactivo tbody').on( 'mouseover', 'td', function () 
    {
        var colIdx = table.cell(this).index.column();

        if ( colIdx !== lastIdx ) 
        {
            $( table.cells().nodes() ).removeClass( 'highlight' );
            $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
        }
    })
    .on( 'mouseleave', function () 
    {
        $( table.cells().nodes() ).removeClass( 'highlight' );
    });


  // Setup - add a text input to each footer cell
    /* $('#tmovimientoactivoSelect tfoot th').each( function () 
     {
          var title = $('#tmovimientoactivoSelect thead th').eq( $(this).index() ).text();
          $(this).html( '<input type="text" placeholder="Buscar por '+title+'" />' );
      });*/
   
      // DataTable
    var table = $('#tmovimientoactivo').DataTable();
   
      // Apply the search
    table.columns().every( function () 
    {
        var that = this;
 
        $( 'input', this.footer() ).on( 'blur change', function () 
        {
            if ( that.search() !== this.value ) 
            {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    })

    $('#tmovimientoactivo tbody').on( 'click', 'tr', function () 
    {
      $(this).toggleClass('selected');
    });
   
    $('#botonActivo').click(function() 
    {
          var datos = table.rows('.selected').data();
          var docInterno= "";
          var idInterno= "";
          for (var i = 0; i < datos.length; i++) 
          {
            docInterno+=datos[i][1]+',';
            idInterno+=datos[i][0]+',';
          }

          docInterno=docInterno.substring(0,docInterno.length-1);
          idInterno=idInterno.substring(0,idInterno.length-1);
          window.parent.$("#documentoInternoAsignacionActivo").val(docInterno);

          var token = document.getElementById('token').value;
          $.ajax(
          {
              headers: {'X-CSRF-TOKEN': token},
              dataType: "json",
              url:'/ConsultarPendientesAsignacionActivoDetalle',
              data:{idMovimientoActivo: idInterno},
              type:  'get',
              beforeSend: function(){
              },

              success: function(data)
              {
                alert(JSON.stringify(data));
              for (var i = 0; i < data.length; i++) 
              {

              var valoresD = new Array(0,0,JSON.stringify(data[i]['idMovimientoActivo']).replace(/"/g,""),JSON.stringify(data[i]['Activo_idActivo']).replace(/"/g,""),JSON.stringify(data[i]['codigoActivo']).replace(/"/g,""),JSON.stringify(data[i]['serieActivo']).replace(/"/g,""),JSON.stringify(data[i]['nombreActivo']).replace(/"/g,""),JSON.stringify(data[i]['idLocalizacion']).replace(/"/g,""),JSON.stringify(data[i]['nombreLocalizacion']).replace(/"/g,""),0);
                detalle.agregarCampos(valoresD,'A');
                //calcularTotales();

              }
                console.log(valoresD);
                 
              },
              error:    function(xhr,err)
              {
                  alert('Se ha producido un error: ' +err);
              }
          })

          window.parent.$("#ModalTransaccionActivo").modal("hide");
          //window.parent.calcularTotales();

    });


  }
}//fin function abrirTransaccionActivo




  
  var asignacionactivodetalle = '<?php echo (isset($asignacionactivodetalle) ? json_encode($asignacionactivodetalle) : "");?>';
  asignacionactivodetalle = (asignacionactivodetalle != '' ? JSON.parse(asignacionactivodetalle) : '');
  //console.log(movimientoactivodetalle);*/

var movimientoactivodetalle="";





  
var valorDetalle = [0,0,''];
$(document).ready(function()
{

  detalle=new Atributos('detalle','contenedor-detalle','detalle-');
  detalle.campoid = 'idAsignacionActivoDetalle';
  detalle.campoEliminacion = 'detalleEliminar';
  detalle.campos=['idAsignacionActivoDetalle', 'AsignacionActivo_idAsignacionActivo', 'MovimientoActivo_idMovimientoActivo', 'Activo_idActivo', 'codigoActivo','serieActivo','nombreActivo','idLocalizacion','nombreLocalizacion', 'Tercero_idResponsable'];
  detalle.etiqueta=['input','input','input','input','input','input','input','input','input','select'];
  detalle.tipo=['hidden','hidden','hidden','hidden','','','','hidden','',''];
  detalle.estilo=['','','','','width:150px; height:26px;','width:200px; height:26px;','width:350px; height:26px;','','width:200px; height:26px','width:350px; height:35px;'];
  detalle.clase=['','','','','','','','','','selectpicker'];
  detalle.sololectura=[false,false,false,false,true,true,true,true,true,false];
  detalle.completar=['off','off','off','off','off','off','off','off','off','off'];

  var idTercero = '<?php echo $idTercero;?>';
  var nombreTercero = '<?php echo $nombreTercero;?>';
  var Tercero = [JSON.parse(idTercero),JSON.parse(nombreTercero)];
  detalle.funciones=['','','','','','','','','',''];
  detalle.opciones=[[],[],[],[],[],[],[],[],[],Tercero]
  detalle.obligatorio=[[],[],[],[],[],[],[],[],[],true]


  for(var j=0; j < asignacionactivodetalle.length; j++)
  {
      detalle.agregarCampos(JSON.stringify(asignacionactivodetalle[j]),'L');
  }

});




</script>


  @if(isset($asignacionactivo))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($asignacionactivo,['route'=>['asignacionactivo.destroy',@$asignacionactivo->idAsignacionActivo],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($asignacionactivo,['route'=>['asignacionactivo.update',@$asignacionactivo->idAsignacionActivo],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'asignacionactivo.store','method'=>'POST'])!!}
  @endif

 
  <br><br><br><br>

                     
{!!Form::hidden('Users_idCrea',  @$solicitante['idUsuarioCrea'], array('id' => 'Users_idCrea'))!!}
{!!Form::hidden('detalleEliminar', null, array('id' => 'detalleEliminar'))!!}



 <div class="container">
  <input type="hidden" id="token" value="{{csrf_token()}}"/>

    <div class="form-group">
  <div class="col-sm-6" position="left">
    {!!Form::label('numeroAsignacionActivo', 'N. Asignacion', array('class' => 'col-sm-6 control-label')) !!}
    <div class="col-sm-6">
    {!!Form::text('numeroAsignacionActivo',$numero,['readonly'=>'readonly','class'=>'form-control','placeholder'=>'Ingresa el número del Documento'])!!}
      </div>
      {!!Form::label('TransaccionActivo_idTransaccionActivo', 'Tipo Documento Referencia', array('class' => 'col-sm-6 control-label')) !!}
    <div class="col-sm-6">
      {!!Form::select('TransaccionActivo_idTransaccionActivo',@$transaccionactivo,null,['class'=>'form-control','placeholder'=>'Seleccione'])!!}
    </div>
      {!!Form::label('Users_nombreCrea', 'Usuario Asigna Transaccion', array('class' => 'col-sm-6 control-label')) !!} 
    <div class="col-sm-6" >
     {!!Form::text('Users_nombreCrea',@$solicitante['nombreUsuarioCrea'],['readonly'=>'readonly','class'=>'form-control','placeholder'=>''])!!}
    </div>
     
 </div>
  <div class="col-sm-6" position="right">
     {!!Form::label('fechaHoraAsignacionActivo', 'Fecha/Hora Elaboracion', array('class' => 'col-sm-6 control-label')) !!}
     <div class="col-sm-6">
       {!!Form::text('fechaHoraAsignacionActivo',$fechahora,['readonly'=>'readonly', 'class'=>'form-control','placeholder'=>''])!!}
     </div>
       {!!Form::label('documentoInternoAsignacionActivo', 'Documento Referencia', array('class' => 'col-sm-6 control-label')) !!}
     
     <div class="col-sm-6" onclick="abrirTransaccionActivo($('#TransaccionActivo_idTransaccionActivo').val());" style="cursor:pointer;">
      <div class="input-group">
        <span class="input-group-addon">
          <i class="fa fa-pencil-square-o"></i>
        </span>
          {!!Form::text('documentoInternoAsignacionActivo',@$transaccionactivo->documentoInternoAsignacionActivo,['class'=>'form-control','placeholder'=>''])!!}
         
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
                   <span class="glyphicon glyphicon-minus" style="cursor:pointer;" onclick="detalle.agregarCampos(valorDetalle,'A')" ></span>
                   <span class="glyphicon glyphicon-plus" onclick="abrirModalMovimiento();"></span>
                     

                  </div>
                  <div class="col-md-1" style="width: 150px;height: 35px;"><b>Referencia</b></div>
                  <div class="col-md-1" style="width: 200px;height: 35px; "><b>Serie</b></div>
                  <div class="col-md-1" style="width: 350px;height: 35px; "><b>Descripcion</b></div>
                  <div class="col-md-1" style="width: 200px;height: 35px; "><b>Localizacion</b></div>
                  <div class="col-md-1" style="width: 350px;height: 35px; "><b>Responsable</b></div>

                  <div id="contenedor-detalle"></div>
                </div>      
              </div>
            </div>
          </fieldset>
</div>
         

@if(isset($asignacionactivo))
  @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
    {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  @else
    {!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  @endif
@else
   {!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
@endif
{!! Form::close() !!}

</body>
</html>
@stop

<div id="ModalMovimiento" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/ActivoGridSelectAsignacionActivo"></iframe>'
          ?>
        </div>
    </div>
  </div>
</div>




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



   







