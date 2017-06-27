
<?php 


$fechahora = Carbon\Carbon::now();
?>


@extends('layouts.vista')
@section('titulo')<br><h4 id="titulo"><center>Orden Mantenimiento</center></h4>@stop
@section('content')


@section('content')
@include('alerts.request')
  {!!Html::script('/js/select2.min.js');!!}

{!!Html::script('js/ordenmantenimiento.js'); !!}
{!!Html::script('js/dropzone.js'); !!}<!--Llamo al dropzone-->
{!!Html::style('assets/dropzone/dist/min/dropzone.min.css'); !!}

<script>
function llamarActivos() 
{

var protocolo = document.getElementById('protocolo').value;
var localizacion = document.getElementById('localizacion').value;

alert(localizacion);
if(protocolo!="" & localizacion!="")
{
 


 
var token = document.getElementById('token').value;
$.ajax(
{
    headers: {'X-CSRF-TOKEN': token},
    dataType: "json",
    url:'/llamarActivos',
    data:{idLocalizacion: localizacion, idProtocolo: protocolo},
    type:  'get',
    beforeSend: function(){},
    success: function(data)
    {
      alert(JSON.stringify(data));
        activos.borrarTodosCampos();
        for (var i = 0;  i <= data.length; i++) 
        {
            //activos.agregarCampos(JSON.stringify(data[i]),'A');

             var valoresD = new Array(0,0,JSON.stringify(data[i]['idActivo']).replace(/"/g,""),JSON.stringify(data[i]['codigoActivo']).replace(/"/g,""),JSON.stringify(data[i]['nombreActivo']).replace(/"/g,""));
            activos.agregarCampos(valoresD ,'A');
        }
    },
           

    error:    function(xhr,err)
    {
        alert('Se ha producido un error: ' +err);
    }
});


}
};




  var ordenmantenimientodetalle = '<?php echo (isset($ordenmantenimientodetalle) ? json_encode($ordenmantenimientodetalle) : "");?>';
  ordenmantenimientodetalle = (ordenmantenimientodetalle != '' ? JSON.parse(ordenmantenimientodetalle) : '');
  console.log(ordenmantenimientodetalle);


var valorActivos = [0,0,''];
var valorTareas = [0,0,''];
var valorRecursos = [0,0,''];
var valorTecnicos = [0,0,''];
$(document).ready(function()
{

  activos=new Atributos('activos','contenedor-activos','activos_');
  activos.campoid = 'idOrdenMantenimientoActivo';
  activos.campoEliminacion = 'activoEliminar';
  activos.campos=['idOrdenMantenimientoActivo', 'OrdenMantenimiento_idOrdenMantenimiento', 'Activo_idActivo','codigoActivo','nombreActivo'];
  activos.etiqueta=['input','input','input','input','input'];
  activos.tipo=['hidden','hidden','hidden','',''];
  //movimiento.tipo=['','','','','','','','','','',''];
 //movimiento.value=['','','','','','','','','','','',];
  activos.estilo=['','','','width: 200px; height:35px;','width:700px; height:35px;'];
  activos.clase=['','','','',''];
  activos.requerido=['','','','',''];
  activos.sololectura=[false,false,false,true,true];
  activos.completar=['off','off','off','off','off'];
  activos.obligatorio=[false,false,true,true,true];


  

  for(var j=0; j < ordenmantenimientodetalle.length; j++)
  {
    movimiento.agregarCampos(JSON.stringify(ordenmantenimientodetalle[j]),'L');
  }

 

  tareas=new Atributos('tareas','contenedor-tareas','tareas_');
  tareas.campoid = 'idTareas';
  tareas.campoEliminacion = 'movimientoEliminar';
  tareas.campos=['','',''];
  tareas.etiqueta=['input','input','input'];
  tareas.tipo=['hidden','',''];
  //movimiento.tipo=['','','','','','','','','','',''];
 //movimiento.value=['','','','','','','','','','','',];
  tareas.estilo=['','width: 700px; height:35px;','width:200px; height:35px;'];
  tareas.clase=['','',''];
  tareas.requerido=['','',''];
  tareas.sololectura=[false,false,false];
  tareas.completar=['off','off','off'];
  tareas.obligatorio=[false,true,true];


 

  for(var j=0; j < ordenmantenimientodetalle.length; j++)
  {
    movimiento.agregarCampos(JSON.stringify(ordenmantenimientodetalle[j]),'L');
  }


  recursos=new Atributos('recursos','contenedor-recursos','recursos_');
  recursos.campoid = 'idOrdenMantenimientoRecurso';
  recursos.campoEliminacion = 'recursoEliminar';
  recursos.campos=['id1','idOrdenMantenimientoRecurso','nombreLocalizacionO', 
  'nombreLocalizacionD', 'idActivo','codigoActivo'];
  recursos.etiqueta=['input','input','input','input','input','input'];
  recursos.tipo=['hidden','','','','',''];
  //movimiento.tipo=['','','','','','','','','','',''];
 //movimiento.value=['','','','','','','','','','','',];
  recursos.estilo=['','width: 150px; height:35px;','width: 300px; height:35px;','width:200px; height:35px;','width:200px;  height:35px;','width:200px; height:35px;'];
  recursos.clase=['','','','','',''];
  recursos.requerido=['','','','','',''];
  recursos.sololectura=[false,false,false,false,false,false];
  recursos.completar=['off','off','off','off','off','off'];
  recursos.obligatorio=[false,true,true,false,true,true];



 
  for(var j=0; j < ordenmantenimientodetalle.length; j++)
  {
    movimiento.agregarCampos(JSON.stringify(ordenmantenimientodetalle[j]),'L');
  }



  tecnicos=new Atributos('tecnicos','contenedor-tecnicos','tecnicos_');
  tecnicos.campoid = 'idTecnico';
  tecnicos.campoEliminacion = 'movimientoEliminar';
  tecnicos.campos=['idordenmantenimientoDetalle','nombreLocalizacionO', 
  'nombreLocalizacionD', 'idActivo'];
  tecnicos.etiqueta=['input','input','input','input'];
  tecnicos.tipo=['hidden','','','',''];
  //movimiento.tipo=['','','','','','','','','','',''];
 //movimiento.value=['','','','','','','','','','','',];
  tecnicos.estilo=['','width: 150px; height:35px;','width:700px; height:35px;','width:150px;  height:35px;'];
  tecnicos.clase=['','','',''];
  tecnicos.requerido=['','','',''];
  tecnicos.sololectura=[false,false,false,false];
  tecnicos.completar=['off','off','off','off'];
  tecnicos.obligatorio=[false,true,true,false];


 

  for(var j=0; j < ordenmantenimientodetalle.length; j++)
  {
    movimiento.agregarCampos(JSON.stringify(ordenmantenimientodetalle[j]),'L');
  }




});

</script>


  @if(isset($ordenmantenimiento))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($ordenmantenimiento,['route'=>['ordenmantenimiento.destroy',@$ordenmantenimiento->idOrdenMantenimiento],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($ordenmantenimiento,['route'=>['ordenmantenimiento.update',@$ordenmantenimiento->idOrdenMantenimiento],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'ordenmantenimiento.store','method'=>'POST'])!!}
  @endif

  <div class="container">
  <br>

{!!Form::hidden('idordenmantenimiento', null, array('id' => 'idordenmantenimiento'))!!}
{!!Form::hidden('movimientoEliminar', null, array('id' => 'movimientoEliminar'))!!}

                     


    </div>
 </div>
     <input type="hidden" id="token" value="{{csrf_token()}}"/>

<div id='form-section' >
  <fieldset id="ordenmantenimiento-form-fieldset"> 
    <div class="form-group" id='test'>


           <div class="col-sm-12">
              <div class="col-sm-2">
                {!!Form::label('asuntoOrdenMantenimiento', 'Descripcion', array())!!}
              </div>
              <div class="col-sm-10">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-pencil-square-o"></i>
                  </span>

                 {!!Form::text('asuntoOrdenMantenimiento',null,[ 'class'=>'form-control','placeholder'=>'Ingresa la descripcion'])!!}
                </div>
              </div>
            </div>
            <br><br>

            <div class="col-sm-6">
            <input type="hidden" id="token" value="{{csrf_token()}}"/>
              <div class="col-sm-4">
                {!!Form::label('numeroOrdenMantenimiento', 'Número', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-barcode"></i>
                  </span>
                  {!!Form::text('numeroOrdenMantenimiento',@$ordenmantenimiento->numeroordenmantenimiento,['required'=>'required','class'=>'form-control','placeholder'=>'Ingresa el número de la Orden'])!!}
                </div>
              </div>
            </div>


            <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('fechaElaboracionOrdenMantenimiento', 'Fecha Solicitud', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>

                 {!!Form::text('fechaElaboracionOrdenMantenimiento',$fechahora,['required'=>'required','readonly'=>'readonly', 'class'=>'form-control','placeholder'=>'Ingresa la fecha de Elaboración'])!!}
                </div>
              </div>
            </div>
            <br><br>

            

            <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('fechaHoraInicioOrdenMantenimiento', 'Programado Desde', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                    {!!Form::text('fechaHoraInicioOrdenMantenimiento',null,['required'=>'required','class'=>' form-control'])!!}
                </div>
              </div>
            </div>
            <script type="text/javascript">
              $('#fechaHoraInicioOrdenMantenimiento').val(<?php echo @$ordenmantenimiento->fechaHoraInicioOrdenMantenimiento;?>)
                $('#fechaHoraInicioOrdenMantenimiento').datetimepicker(({
                  defaultDate: new Date(),
                    format:'DD/MM/YYYY HH:mm'
                }));
              </script>

            <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('fechaHoraFinOrdenMantenimiento', 'Programado Hasta', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                    {!!Form::text('fechaHoraFinOrdenMantenimiento',null,['required'=>'required','class'=>' form-control'])!!}
                </div>
              </div>
            </div>
            <script type="text/javascript">
              $('#fechaHoraFinOrdenMantenimiento').val(<?php echo @$ordenmantenimiento->fechaHoraFinOrdenMantenimiento;?>)
                $('#fechaHoraFinOrdenMantenimiento').datetimepicker(({
                  defaultDate: new Date(),
                    format:'DD/MM/YYYY HH:mm'
                }));
              </script>
             <br><br>

             <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('Localización_idLocalización', 'Localizacion', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-caret-down "></i>
                  </span>
                  
                  {!!Form::select('Localización_idLocalización',@$localizacion, @$ordenmantenimiento->Tercero_idTercero,["id"=>"localizacion","required"=>"required","class" => "chosen-select form-control",'placeholder'=>'Selecciona'])!!}

                  </div>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('ProtocoloMantenimiento_idProtocoloMantenimiento', 'Protocolo Mant.', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-caret-down "></i>
                  </span>
                   {!!Form::select('ProtocoloMantenimiento_idProtocoloMantenimiento',@$protocolo, @$ordenmantenimiento->TransaccionActivo_idDocumentoInterno,['onchange'=>'llamarActivos();','onready'=>'llamarActivos();',"id"=>"protocolo",'required'=>'required',"class" => "chosen-select form-control",'placeholder'=>'Selecciona'])!!}
                   
                </div>
              </div>
            </div>

             <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('TipoServicio_idTipoServicio', 'Tipo Servicio', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-caret-down "></i>
                  </span>
                   {!!Form::select('TipoServicio_idTipoServicio',@$tiposervicio, @$ordenmantenimiento->TransaccionActivo_idDocumentoInterno,['required'=>'required',"class" => "chosen-select form-control",'placeholder'=>'Selecciona'])!!}
                   
                </div>
              </div>
            </div>
           
             <div class="col-sm-6">
              <div class="col-sm-4">
                {!!Form::label('estadoOrdenMantenimiento', 'Estado', array())!!}
              </div>
              <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-caret-down "></i>
                  </span>
                   {!!Form::select('estadoOrdenMantenimiento',['Activo'=>'Activo','Anulado'=>'Anulado','Cerrado'=>'Cerrado'], @$ordenmantenimiento->TransaccionActivo_idDocumentoInterno,['required'=>'required',"class" => "chosen-select form-control",'placeholder'=>'Selecciona'])!!}
                   
                </div>
              </div>
            </div>
             <br><br>
            

           
              <div class="col-sm-6">
                <div class="col-sm-4">
                 {!!Form::label('TipoAccion_idTipoAccion', 'Tipo Accion', array('class' => 'col-sm-4 control-label')) !!}
                </div>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-caret-down "></i>
                    </span>
                    {!!Form::select('TipoAccion_idTipoAccion',@$tipoaccion, @$ordenmantenimiento->ConceptoActivo_idConceptoActivo,['required'=>'required',"class" => "chosen-select form-control",'placeholder'=>'Selecciona'])!!}

                   
                  </div>
                </div>
              </div>
          

              <div class="col-sm-6">
                <div class="col-sm-4">
                 {!!Form::label('Tercero_idProveedor', 'Proveedor', array('class' => 'col-sm-4 control-label')) !!}
                </div>
                <div class="col-sm-8">
                  <div class="input-group">
                    <span class="input-group-addon">
                      <i class="fa fa-caret-down "></i>
                    </span>
                     {!!Form::select('Tercero_idProveedor', @$tercero,@$ordenmantenimiento->TransaccionActivo_idTransaccionActivo,['required'=>'required',"class" => "chosen-select form-control", 'style'=>'padding-left:2px;','placeholder'=>'Selecciona'])!!}
                     
                  </div>
                </div>
              </div>
              <br><br><br><br><br>
            
           
 
           
        </div>
      </fieldset>
    </div>  

<div class="container">
  
  <div class="panel-group" id="accordion">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#detalles">Detalles</a>
        </h4>
      </div>
      <div id="detalles" class="panel-collapse collapse in">
        <div class="panel-body">
          
          <div class="col-sm-12" align="center">
          {!!Form::textarea('observacionOrdenMantenimiento',null,['required'=>'required','class' => 'ckeditor'])!!}
          </div>
        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#activos">Activos</a>
        </h4>
      </div>
      <div id="activos" class="panel-collapse collapse">
        <div class="panel-body">
          
          <div class="container">
            <div class="row show-grid">
               
               <div class="col-md-1" style="width: 40px;height: 55px;"><span class="glyphicon glyphicon-plus" title="Adicionar" style='cursor:pointer;' onclick="activos.agregarCampos(valorActivos,'A')" ></span> 
               <span class="glyphicon glyphicon-search" title="Consultar Activos" style='cursor:pointer;' onclick="consultaractivos()" ></span> 
              <span class="glyphicon glyphicon-trash" title="Elimar Todo" style='cursor:pointer;' onclick="activos.borrarTodosCampos()" ></span> 
              </div>

              <div class="col-md-1" style="width: 200px;height: 35px;"><b>Codigo</b></div>
              <div class="col-md-1" style="width: 700px;height: 35px; "><b>Nombre</b></div>
              <div id="contenedor-activos"></div>
              
            </div>
          </div>

        </div>
      </div>
    </div>


    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#tareas">Tareas</a>
        </h4>
      </div>
      <div id="tareas" class="panel-collapse collapse">
        <div class="panel-body">
          
          <div class="container">
            <div class="row show-grid">
               
               <div class="col-md-1" style="width: 40px;height: 55px;"><span class="glyphicon glyphicon-plus" title="Adicionar" style='cursor:pointer;' onclick="tareas.agregarCampos(valorTareas,'A')" ></span> 
               <span class="glyphicon glyphicon-search" title="Consultar Activos" style='cursor:pointer;' onclick="consultaractivos()" ></span> 
              <span class="glyphicon glyphicon-trash" title="Elimar Todo" style='cursor:pointer;' onclick="tareas.borrarTodosCampos()" ></span> 
              </div>

              <div class="col-md-1" style="width: 700px;height: 35px;"><b>Descripcion</b></div>
              <div class="col-md-1" style="width: 200px;height: 35px; "><b>Tiempo Estimado</b></div>
              
              <div id="contenedor-tareas"></div>
              
            </div>
          </div>

      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#recursos">Recursos</a>
        </h4>
      </div>
      <div id="recursos" class="panel-collapse collapse">
        <div class="panel-body">

           <div class="container">
            <div class="row show-grid">
               
               <div class="col-md-1" style="width: 40px;height: 55px;"><span class="glyphicon glyphicon-plus" title="Adicionar" style='cursor:pointer;' onclick="recursos.agregarCampos(valorRecursos,'A')" ></span> 
               <span class="glyphicon glyphicon-search" title="Consultar Activos" style='cursor:pointer;' onclick="consultaractivos()" ></span> 
              <span class="glyphicon glyphicon-trash" title="Elimar Todo" style='cursor:pointer;' onclick="recursos.borrarTodosCampos()" ></span> 
              </div>

              <div class="col-md-1" style="width: 150px;height: 35px;"><b>Codigo</b></div>
              <div class="col-md-1" style="width: 300px;height: 35px; "><b>Descripcion</b></div>
              <div class="col-md-1" style="width: 200px;height: 35px; "><b>Saldo</b></div>
              <div class="col-md-1" style="width: 200px;height: 35px; "><b>Cantidad</b></div>
              <div class="col-md-1" style="width: 200px;height: 35px; "><b>Costo</b></div>

              <div id="contenedor-recursos"></div>
              
            </div>
          </div>

        </div>
      </div>
    </div>
   

    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#tecnicos">Tecnicos</a>
        </h4>
      </div>
      <div id="tecnicos" class="panel-collapse collapse">
        <div class="panel-body">

          <div class="container">
            <div class="row show-grid">
               
               <div class="col-md-1" style="width: 40px;height: 55px;"><span class="glyphicon glyphicon-plus" title="Adicionar" style='cursor:pointer;' onclick="tecnicos.agregarCampos(valorTecnicos,'A')" ></span> 
               <span class="glyphicon glyphicon-search" title="Consultar Activos" style='cursor:pointer;' onclick="consultaractivos()" ></span> 
              <span class="glyphicon glyphicon-trash" title="Elimar Todo" style='cursor:pointer;' onclick="tecnicos.borrarTodosCampos()" ></span> 
              </div>

              <div class="col-md-1" style="width: 150px;height: 35px;"><b>Cedula</b></div>
              <div class="col-md-1" style="width: 700px;height: 35px; "><b>Nombre</b></div>
              <div class="col-md-1" style="width: 150px;height: 35px; "><b>Valor Hora</b></div>
              <div id="contenedor-tecnicos"></div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div> 
</div>   
<br><br><br><br>

  

    
@if(isset($ordenmantenimiento))
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
                    
                    <table id="tordenmantenimiento" name="tordenmantenimiento" class="display table-bordered" width="100%">
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
                    
                    <table id="tordenmantenimiento2" name="tordenmantenimiento2" class="display table-bordered" width="100%">
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







