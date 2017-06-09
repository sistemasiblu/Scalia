@extends('layouts.vista')

<?php 
/*print_r($tercero);
return;*/
$idEstadoDefault = null;
$nombreEstadoDefault = '';
foreach ($estado as $key => $value) {
  $idEstadoDefault = $key;
  $nombreEstadoDefault = $value;
  break;
}

function mostrarCampo($arrayCampos, $campo, $rolUsuario, $atributo)
{
  // recorremos el array verificando si en la columna nombreCampoCRM existe el valor del parametro $campo
  $sololectura = '';
  
  for($i=0; $i < count($arrayCampos); $i++)
  {
    if(@$arrayCampos[$i]["nombreCampoTransaccion"] == $campo)
    {
      // con esta posicion, verificamos si el Sub-rol tiene permiso o no
      $sololectura = ($arrayCampos[$i][$rolUsuario."TransaccionActivoCampo"] == 0
        ? ($atributo == 'select' ? 'disabled' : 'readonly')
        : '');
    }
  }
  return $sololectura;
} 
?>
<script>
 function borrarIguales(id,valor)
 {

alert(valor);
  //$('#campo_select_append').append('<option value="opcion_nueva_1" selected="selected">Opción nueva 1</option>');

var idcampo=JSON.stringify(id).replace(/\D/g,'');
//$("#nombreLocalizacionD"+idcampo).append("option[value='"+valor+"'] selected='selected'>Opción nueva 1</option>");
var x = document.getElementById("#nombreLocalizacionD"+idcampo);
    var option = document.createElement("option");
    option.text = valor;
    x.add(option);


var dato=document.getElementById("#nombreLocalizacionO"+idcampo).value;
alert(dato);

//$("#nombreLocalizacionD"+idcampo).find("option[value='"+valor+"']").remove(); 
//$("#nombreLocalizacionD"+idcampo).find("option[value='"+valor+"']").remove(); 
 }


 $(document).ready(function() {
  $("#Tercero_idTercero").select2();
});




</script>
<?php
$id = isset($_GET["idTransaccionActivo"]) ? $_GET["idTransaccionActivo"] : 0;
$campos = DB::select(
"select idCampoTransaccion,codigoTransaccionActivo,nombreTransaccionActivo,tipoNumeracionTransaccionActivo,longitudTransaccionActivo,
desdeTransaccionActivo,hastaTransaccionActivo,tipoCampoTransaccion,nombreCampoTransaccion,
descripcionCampoTransaccion,relacionTablaCampoTransaccion,relacionIdCampoTransaccion,
relacionNombreCampoTransaccion, relacionAliasCampoTransaccion
from transaccionactivo
left join transaccionactivocampo
on transaccionactivo.idTransaccionActivo=transaccionactivocampo.TransaccionActivo_idTransaccionActivo
left join campotransaccion
on transaccionactivocampo.CampoTransaccion_idCampoTransaccion=campotransaccion.idCampoTransaccion
where transaccionactivo.idTransaccionActivo=".$id);
//print_r($campos);

$datos = array();
$camposVista = '';
for($i = 0; $i < count($campos); $i++)
{
    $datos = get_object_vars($campos[$i]); 
    $camposVista .= $datos["nombreCampoTransaccion"].',';
    $codTransaccion = $datos["codigoTransaccionActivo"];
    $nomTransaccion = $datos["nombreTransaccionActivo"];
}

$userAprob = DB::select(
"select users.id,users.name
from movimientoactivo
inner join Users
on movimientoactivo.Users_idCambioEstado=users.id
where TransaccionActivo_idTransaccionActivo=".$id);
//print_r($campos);

$aprob = array();
for($i = 0; $i < count($userAprob); $i++)
{
  $aprob = get_object_vars($userAprob[$i]); 
    
    
}

//$usercrea=\Session::get('nombreUsuario').\Session::get('idUsuario');
//print_r($usercrea);
//return;
$idMovimientoTransaccion = (isset($movimientoactivo->TransaccionActivo_idTransaccionActivo) ? $movimientoactivo->TransaccionActivo_idTransaccionActivo : 0);


// dependiendo del tipo de numeración debemos habilitar o no el campo de numero 
// Numeracion Automatica
// Numeración Manual
if(@$datos["tipoNumeracionTransaccionActivo"] == 'Automatica' )
  $ReadOnlyNumero = 'readonly';
else
  $ReadOnlyNumero = '';


// consultamos el tercero asociado al  usuario logueado, para 
// relacionarlo al campo de solicitante
/*$tercero  = DB::select(
    'SELECT idTercero, nombreCompletoTercero
    FROM tercero
    where idTercero = '.\Session::get('idTercero'));
$tercero = get_object_vars($tercero[0]); 
*/

$fechahora = Carbon\Carbon::now();
?>
@section('titulo')<h3 id="titulo">

<center>
<?php 
  echo '('.@$codTransaccion.') '.@$nomTransaccion.'<br>'.
  strtoupper(@$rolUsuario);
?></center></h3>@stop

<?php
$usercrea=\App\User::where('id','=',\Session::get('idUsuario'))->lists('name','id');


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

function autocompletarfila(id,campo) 
{
  //replace(/\D/g,'') quita las letras de una cadena 
  var idcampo=JSON.stringify(campo).replace(/\D/g,'');
  if ($('#codigoActivo'+idcampo).val()!="")
  {
      $('#idActivo'+idcampo).val("");
      $('#serieActivo'+idcampo).val("");
      $('#nombreActivo'+idcampo).val("");
      var token = document.getElementById('token').value;
      $.ajax(
      {
          headers: {'X-CSRF-TOKEN': token},
          dataType: "json",
          url:'/llamarDescripcionActivo',
          data:{codigoActivo: id},
          type:  'get',
          beforeSend: function(){
          },

          success: function(data)
          {
          
          //var idcampo=JSON.stringify(campo).replace(/\D/g,'');
          for (var i = 0; i < data.length; i++) 
          {
            var id=JSON.stringify(data[i]['idActivo']).replace(/"/g,"");
            var serie=JSON.stringify(data[i]['serieActivo']).replace(/"/g,"");
            var nombre=JSON.stringify(data[i]['nombreActivo']).replace(/"/g,"");
            $('#idActivo'+idcampo).val(id);
            $('#serieActivo'+idcampo).val(serie);
            $('#nombreActivo'+idcampo).val(nombre);
          }
             
          },
          error:    function(xhr,err)
          {
              alert('Se ha producido un error: ' +err);
          }
      })
  }

};//fin fuction autocompletarfila

function detalleactivos(serie,campo) 
{

    //replace(/\D/g,'') quita las letras de una cadena 
    var idcampo=JSON.stringify(campo).replace(/\D/g,'');
    if ($('#serieActivo'+idcampo).val()!="" || $('#serieActivo'+idcampo).val()!="")
    {

      idAct = $('#idActivo'+idcampo).val();

      if(idAct!="")

      {
          var token = document.getElementById('token').value;
          $.ajax(
          {
              headers: {'X-CSRF-TOKEN': token},
              dataType: "json",
              url:'/MostrarDetalleActivo',
              data:{idActivo: idAct},
              type:  'get',
              beforeSend: function()
              {
              },

              success: function(data)
              {
                 var c=1;
                 var tipoactivo='computador Portatil';
                 $('#contenidoDetalleActivo').html(data);
                 $('#ModalDetalleActivo').modal('show');
              },
              error:    function(xhr,err)
              {
                alert('Se ha producido un error: ' +err);
              }
          })
      }

    }

};//fin fuction detalleactivos


function fechaInicio() 
{
   //$('#fechaInicioMovimientoActivo').val(<?php //echo @$movimientoactivo->fechaInicioMovimientoActivo;?>);
/*$('#fechaInicioMovimientoActivo').datetimepicker((
{
  defaultDate: new Date(),
  format:'DD/MM/YYYY HH:mm'
}));*/
 $('#fechaInicioMovimientoActivo').datetimepicker();
              
}
  /*$(document).ready(function() 
  {
    $("#Tercero_idTercero").select2();
  });
*/
function consultaractivos()
{
  $('#ModalActivo').modal('show');
}


function  abrirTransaccionActivo(id)
{
  //alert('entra');
  if ($('#TransaccionActivo_idDocumentoInterno').val()=="")
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
          window.parent.$("#documentoInternoMovimientoActivo").val(docInterno);

          var token = document.getElementById('token').value;
          $.ajax(
          {
              headers: {'X-CSRF-TOKEN': token},
              dataType: "json",
              url:'/ConsultarPendientesMovimientoActivoDetalle',
              data:{idMovimientoActivo: idInterno},
              type:  'get',
              beforeSend: function(){
              },

              success: function(data)
              {
              for (var i = 0; i < data.length; i++) 
              {

              var valoresD = new Array(0,JSON.stringify(data[i]['nombreLocalizacionO']).replace(/"/g,""),JSON.stringify(data[i]['nombreLocalizacionD']).replace(/"/g,""),JSON.stringify(data[i]['Activo_idActivo']).replace(/"/g,""),JSON.stringify(data[i]['codigoActivo']).replace(/"/g,""),JSON.stringify(data[i]['serieActivo']).replace(/"/g,""),JSON.stringify(data[i]['nombreActivo']).replace(/"/g,""),JSON.stringify(data[i]['cantidadMovimientoActivoDetalle']).replace(/"/g,""),JSON.stringify(data[i]['observacionMovimientoActivoDetalle']).replace(/"/g,""),JSON.stringify(data[i]['MovimientoActivo_idMovimientoActivo']).replace(/"/g,""));
                movimiento.agregarCampos(valoresD,'A');
                calcularTotales();

              }
                console.log(valoresD);
                 
              },
              error:    function(xhr,err)
              {
                  alert('Se ha producido un error: ' +err);
              }
          })

          window.parent.$("#ModalTransaccionActivo").modal("hide");
          window.parent.calcularTotales();

    });


  }
}//fin function abrirTransaccionActivo




  
  var movimientoactivodetalle = '<?php echo (isset($movimientoactivodetalle) ? json_encode($movimientoactivodetalle) : "");?>';
  movimientoactivodetalle = (movimientoactivodetalle != '' ? JSON.parse(movimientoactivodetalle) : '');
  console.log(movimientoactivodetalle);


var valormovimiento = [0,0,''];
$(document).ready(function()
{

  movimiento=new Atributos('movimiento','contenedor-movimiento','movimiento_');
  movimiento.campoid = 'idMovimientoActivoDetalle';
  movimiento.campoEliminacion = 'movimientoEliminar';
  movimiento.campos=['idMovimientoActivoDetalle','nombreLocalizacionO', 
  'nombreLocalizacionD', 'idActivo','codigoActivo', 'serieActivo','nombreActivo', 'cantidadMovimientoActivoDetalle','observacionMovimientoActivoDetalle','MovimientoActivo_idDocumentoInterno'];
  movimiento.etiqueta=['input','select','select','input','input','input','input','input','input','input'];
  movimiento.tipo=['hidden','','','hidden','','','','','','hidden'];
  //movimiento.tipo=['','','','','','','','','','',''];
 //movimiento.value=['','','','','','','','','','','',];
  movimiento.estilo=['','width: 110px; height:35px;','width:110px; height:35px;','','width:100px;  height:35px;','width:210px; height:35px;','width:200px; height:35px;','width:90px; height:35px;','width:110px; height:35px;',''];
  movimiento.clase=['','','','','','','','','',''];
  movimiento.requerido=['','','','','','','','','',true];
  movimiento.sololectura=[false,false,false,false,false,true,true,false,false,false];
  movimiento.completar=['off','off','off','off','off','off','off','off','off','off'];
  movimiento.obligatorio=[false,true,true,false,false,false,false,false,false,false];


  var idLocalizacion = '<?php echo isset($idLocalizacion) ? $idLocalizacion : "";?>';
  var nombreLocalizacion = '<?php echo isset($nombreLocalizacion) ? $nombreLocalizacion : "";?>';
  var Localizacion = [JSON.parse(idLocalizacion),JSON.parse(nombreLocalizacion)];
  var codigoActivo = ['onblur','autocompletarfila(this.value,this.id,);'];
  var idcelda=$("#idActivo"+movimiento.campoid).val();
  var limpiartotales =['onblur',"calcularTotales();",'ondrop',"ensayo();"];
  var borrarIguales =['onblur',"borrarIguales(this.id,this.value);"];
  var cantidadMovimientoActivo = ['onblur',"calcularTotales();"];
  var detalleActivo=['onclick',"detalleactivos(this.value,this.id);'',"];
  movimiento.funciones=['',borrarIguales,'','',codigoActivo,detalleActivo,'',limpiartotales,'',''];
  movimiento.opciones = [[],Localizacion,Localizacion,[],[],[],[],[],[],[]];      
  var idActivo = '<?php echo isset($idActivo) ? $idActivo : "";?>';
  var nombreActivo = '<?php echo isset($nombreActivo) ? $nombreActivo : "";?>';

  for(var j=0; j < movimientoactivodetalle.length; j++)
  {
    movimiento.agregarCampos(JSON.stringify(movimientoactivodetalle[j]),'L');
  }

});

</script>


  @if(isset($movimientoactivo))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($movimientoactivo,['route'=>['movimientoactivo.destroy',@$movimientoactivo->idMovimientoActivo],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($movimientoactivo,['route'=>['movimientoactivo.update',@$movimientoactivo->idMovimientoActivo],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'movimientoactivo.store','method'=>'POST'])!!}
  @endif

  <div class="container">
  <br><br><br><br>

{!!Form::hidden('TransaccionActivo_idTransaccionActivo', $idTransaccion, array('id' => 'TransaccionActivo_idTransaccionActivo'))!!}
{!!Form::hidden('idMovimientoActivo', null, array('id' => 'idMovimientoActivo'))!!}
{!!Form::hidden('movimientoEliminar', null, array('id' => 'movimientoEliminar'))!!}

{!!Form::hidden('idUsuarioCrea',  @$solicitante['idUsuarioCrea'], array('id' => 'idUsuarioCrea'))!!}
{!!Form::hidden('Users_idCambioEstado',@$movimientoactivo->Users_idCambioEstado,["class" => " form-control"])!!}
                     



    <div id='form-section' >
        <fieldset id="movimientoactivo-form-fieldset"> 
          <div class="form-group" id='test'>
            <div class="col-sm-6">
            <input type="hidden" id="token" value="{{csrf_token()}}"/>
              <div class="col-sm-4">
                {!!Form::label('numeroMovimientoActivo', 'Número', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-barcode"></i>
                  </span>
                  {!!Form::text('numeroMovimientoActivo',(isset($movimientoactivo) ? $movimientoactivo->numeroMovimientoActivo : ($ReadOnlyNumero != '' ? 'Automatico' : null)),[$ReadOnlyNumero => $ReadOnlyNumero, 'class'=>'form-control','placeholder'=>'Ingresa el número del Documento'])!!}
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('fechaElaboracionMovimientoActivo', 'Fecha Elaboración', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>

                 {!!Form::text('fechaElaboracionMovimientoActivo',$fechahora,['readonly'=>'readonly', 'class'=>'form-control','placeholder'=>'Ingresa la fecha de Elaboración'])!!}
                </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('Tercero_idTercero', 'Tercero', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  
                  {!!Form::select('Tercero_idTercero',@$tercero, @$movimientoactivo->Tercero_idTercero,["required"=>"required","class" => "chosen-select form-control",'placeholder'=>'Selecciona'])!!}

                  </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('Users_idCrea', 'Usuario Crea', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                    {!!Form::text('nombreSolicitante',@$solicitante['nombreUsuarioCrea'],['class'=>' form-control', 'readonly'=>'readonly'])!!}
                </div>
              </div>
            </div>

             <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('TransaccionActivo_idDocumentoInterno', 'Tipo Documento', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-caret-down "></i>
                  </span>
                   {!!Form::select('TransaccionActivo_idDocumentoInterno',@$documentoInterno, @$movimientoactivo->TransaccionActivo_idDocumentoInterno,["class" => "chosen-select form-control",'placeholder'=>'Selecciona'])!!}
                   
                </div>
              </div>
            </div>
           

            

             <div class="col-sm-6">
              <div class="col-sm-4">
            {!!Form::label('documentoInternoMovimientoActivo', 'Numero Documento Int', array())!!}
              </div>
              <div class="col-sm-8" onclick="abrirTransaccionActivo($('#TransaccionActivo_idDocumentoInterno').val());" style="cursor:pointer;">
                <div class="input-group">
                <span class="input-group-addon">
               <i class="fa fa-list"></i>
               </span>
              {!!Form::text('documentoInternoMovimientoActivo',null,['class'=>'form-control','readonly'=>'true', 'style'=>'background-color:white;cursor:pointer;'])!!}
                      
                </div>
              </div>
            </div>

          

            <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('documentoExternoMovimientoActivo', 'Numero Documento Ext', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-keyboard-o "></i>
                  </span>
                   {!!Form::text('documentoExternoMovimientoActivo',null,["class" => " form-control"])!!}
                </div>
              </div>
            </div>

            
            
             
            <?php
            if(strpos($camposVista, 'fechaInicioMovimientoActivo') !== false)
            { 
              ?>
              <div class="col-sm-6">
                <div class="col-sm-4">
                  {!!Form::label('fechaInicioMovimientoActivo', 'Fecha Inicio', array('class' => 'col-sm-4 control-label')) !!}
                </div>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                    {!!Form::text('fechaInicioMovimientoActivo',@$movimientoactivo->fechaInicioMovimientoActivo,['class'=>'form-control','onclick'=>'fechaInicio();','style'=>'required:required','placeholder'=>'Ingresa la Fecha de Inicio'])!!}
                  </div>
                </div>
              </div>
              
            <?php   
            }
            ?>


            <?php
            if(strpos($camposVista, 'fechaFinMovimientoActivo') !== false)
            { 
              ?>
              <div class="col-sm-6">
                <div class="col-sm-4">
                  {!!Form::label('fechaFinMovimientoActivo', 'F. Vencimiento', array())!!}
                </div>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </span>
                    {!!Form::text('fechaFinMovimientoActivo',null,['class'=>'form-control','placeholder'=>'Ingresa la Fecha de Vencimiento'])!!}
                  </div>
                </div>
              </div>
              <script type="text/javascript">
              $('#fechaFinMovimientoActivo').val(<?php echo @$movimientoactivo->fechaFinMovimientoActivo;?>)
                $('#fechaFinMovimientoActivo').datetimepicker(({
                  defaultDate: new Date(),
                    format:'DD/MM/YYYY HH:mm'
                }));
              </script>
            <?php   
            }
            ?>


           <?php
            if(strpos($camposVista, 'estadoMovimientoActivo') !== false)
            { 
              ?>
              <div class="col-sm-6">
                <div class="col-sm-4">
                 {!!Form::label('estadoMovimientoActivo', 'Estado Movimiento' , array('class' => 'col-sm-4 control-label')) !!}
                </div>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-pencil-square-o"></i>
                    </span>
                  <!--   {!!Form::text('estadoMovimientoActivo',null,["readonly"=>"readonly","class" => "form-control"])!!} -->
                    {!!Form::text('estadoMovimientoActivo',(isset($movimientoactivo->estadoMovimientoActivo) ? $movimientoactivo->estadoMovimientoActivo : $nombreEstadoDefault),['readonly'=>'readonly', 'class'=>'form-control'])!!}
                 
                  </div>
                </div>
              </div>
            <?php  
            }
            ?>

            <?php
            if(strpos($camposVista, 'ConceptoActivo_idConceptoActivo') !== false)
            { 
              ?>
              <div class="col-sm-6">
                <div class="col-sm-4">
                 {!!Form::label('ConceptoActivo_idConceptoActivo', 'Concepto', array('class' => 'col-sm-4 control-label')) !!}
                </div>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-pencil-square-o"></i>
                    </span>
                    {!!Form::select('ConceptoActivo_idConceptoActivo',@$concepto, @$movimientoactivo->ConceptoActivo_idConceptoActivo,["class" => "chosen-select form-control",'placeholder'=>'Selecciona'])!!}

                   
                  </div>
                </div>
              </div>
            <?php   
            }
            ?>

             <?php
            if(strpos($camposVista, 'TransaccionActivo_idTransaccionActivo') !== false)
            { 
              ?>
              <div class="col-sm-6">
                <div class="col-sm-4">
                 {!!Form::label('TransaccionActivo_idTransaccionActivo', 'Transaccion Activo', array('class' => 'col-sm-4 control-label')) !!}
                </div>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-pencil-square-o"></i>
                    </span>
                     {!!Form::select('TransaccionActivo_idTransaccionActivo', @$transaccionactivo,@$movimientoactivo->TransaccionActivo_idTransaccionActivo,["class" => "chosen-select form-control", 'style'=>'padding-left:2px;','placeholder'=>'Selecciona'])!!}
                     
                  </div>
                </div>
              </div>
              <?php   
            }
              ?>
           

           
        </div>
      </fieldset>
    </div>  

         
<br><br><br><br>

  <div class="form-group">
    <fieldset id='fieldset-documentos'>
      <div class="form-group"  id='test'>
        <div class="col-sm-12">
          <div class="row show-grid">
             
             <div class="col-md-1" style="width: 40px;height: 55px;"><span class="glyphicon glyphicon-plus" title="Adicionar" style='cursor:pointer;' onclick="movimiento.agregarCampos(valormovimiento,'A')" ></span> 
             <span class="glyphicon glyphicon-search" title="Consultar Activos" style='cursor:pointer;' onclick="consultaractivos()" ></span> 
            <span class="glyphicon glyphicon-trash" title="Elimar Todo" style='cursor:pointer;' onclick="movimiento.borrarTodosCampos()" ></span> </div>

             <div class="col-md-1" style="width: 110px;height: 55px;"><b>Localizacion Origen</b></div>
             <div class="col-md-1" style="width: 110px;height: 55px;"><b>Localizacion Destino</b></div>
             <div class="col-md-1" style="width: 100px;height: 55px;"><b>Codigo</b></div>
             <div class="col-md-1" style="width: 210px;height: 55px;"><b>Serial</b></div>
             <div class="col-md-1" style="width: 200px;height: 55px;"><b>Descripcion</b></div>
             <div class="col-md-1" style="width: 90px;height: 55px;"><b>Cantidad</b></div>
             <div class="col-md-1" style="width: 110px;height: 55px;"><b>Observacion</b></div>
             <div  id="contenedor-movimiento"></div>
          </div>
         
          
        <!-- <label style="margin-left:4cm;font-size: 10pt;" class="col-sm-1">Total Articulos</label>
             <input type='text' class="col-xs-1"/> -->


             <br><br>
          <div class="col-sm-6">
          <div class="col-sm-3">  
          {!!Form::label('totalArticulosMovimientoActivo','Total Articulos',array())!!}
          </div>
          {!!Form::text('totalArticulosMovimientoActivo',null,['class'=>'col-sm-2','readonly'=>'readonly'])!!}   
          </div>
          <div class="col-sm-6">
           <div class="col-sm-4">  
          {!!Form::label('totalUnidadesMovimientoActivo','Total Unidades',array())!!}
          </div>
          {!!Form::text('totalUnidadesMovimientoActivo', null,['class'=>'col-sm-2','readonly'=>'readonly'])!!}  

          
         </div>




 
       <!--  <label style="margin-left:8.6cm;font-size: 10pt;" class="col-sm-1">Total Unidades</label>
             <input type='text' value="<?php //echo count('idMovimientoActivo');?>" class="col-xs-1"/> -->             
      </div>
  </div>

    </fieldset><br>
    {!!Form::label('observacionMovimientoActivo', 'Observaciones:', array('class' => 'col-sm-8 control-label')) !!}
    <div class="col-sm-10" align="center">
       {!!Form::textarea('observacionMovimientoActivo',null,['class' => 'ckeditor'])!!}
    </div>
    <br><br><br><br>
  


</div>
</div>


@if(isset($movimientoactivo))
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


<div id="ModalActivo" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Campos</h4>
      </div>
        <div class="modal-body">
          <?php 
          echo '<iframe style="width:100%; height:400px; " id="campos" name="campos" src="http://'.$_SERVER["HTTP_HOST"].'/ActivoGridSelect"></iframe>'
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
                          <li><a class="toggle-vis" data-column="2"><label>Tercero</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Tipo Movimiento</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Total Articulos</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Creador</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Compañia</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tmovimientoactivo" name="tmovimientoactivo" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">

                                <th><b>ID</b></th>
                                <th><b>Numero</b></th>
                                <th><b>Fecha Elaboracion</b></th>
                                <th><b>Tercero</b></th>
                                <th><b>Tipo Movimiento</b></th>
                                <th><b>Total Articulos</b></th>
                                <th><b>Creador</b></th>
                                <th><b>Compañia</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">

                                <th>ID</th>
                                <th>Numero</th>
                                <th>Fecha Elaboracion</th>
                                <th>Tercero</th>
                                <th>Tipo Movimiento</th>
                                <th>Total Articulos</th>
                                <th>Creador</th>
                                <th>Compañia</th>
                                

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


<div id="ModalAprobacionActivo" class="modal fade" role="dialog" style="display:none;">
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
                          <li><a class="toggle-vis" data-column="2"><label>Tercero</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Tipo Movimiento</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Total Articulos</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Creador</label></a></li>
                          <li><a class="toggle-vis" data-column="2"><label>Compañia</label></a></li>
                        </ul>
                    </div>
                    
                    <table id="tmovimientoactivo2" name="tmovimientoactivo2" class="display table-bordered" width="100%">
                        <thead>
                            <tr class="btn-primary active">

                                <th><b>ID</b></th>
                                <th><b>Numero</b></th>
                                <th><b>Fecha Elaboracion</b></th>
                                <th><b>Tercero</b></th>
                                <th><b>Tipo Movimiento</b></th>
                                <th><b>Total Articulos</b></th>
                                <th><b>Creador</b></th>
                                <th><b>Compañia</b></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr class="btn-default active">

                                <th>ID</th>
                                <th>Numero</th>
                                <th>Fecha Elaboracion</th>
                                <th>Tercero</th>
                                <th>Tipo Movimiento</th>
                                <th>Total Articulos</th>
                                <th>Creador</th>
                                <th>Compañia</th>
                                

                            </tr>
                        </tfoot> 
                    </table>

                    <div class="modal-footer">
                        <button id="botonActivo2" name="botonActivo2" type="button" class="btn btn-primary" >Seleccionar</button>
                    </div>

                </div>
            </div>
        </div>

          </div>
        </div><!--  Fin div modal-body  -->
    </div><!--  Fin div modal-content  -->
  </div><!--  Fin div modal-dialog  -->
</div><!--  Fin div ModalAprobacionActivo  -->
   

   
<div id="ModalDetalleActivo" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog" style="width:70%;">
    <div style="" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Información General del Activo</h4>
      </div>
        <div id="contenidoDetalleActivo" class="modal-body">
        </div>
    </div>
  </div>
</div>







