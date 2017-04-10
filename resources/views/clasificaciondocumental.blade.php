@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center></center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/clasificaciondocumental.js')!!}

<?php 
//Creo una variable en la cuaal guardo los datos que me llegan del modal
$datos =  isset($clasificaciondocumental) ? $clasificaciondocumental->clasificaciondocumentales : array();
//print_r($datos);
//Creo un ciclo y mediante un explode separo los datos por comas (",")
for($i = 0; $i < count($datos); $i++)
{
  $ids = explode(',', $datos[$i]["dependenciaClasificacionDocumental"]);
//Consulta a la bd para saber cual es el iD de la dependencia 
   $abreviaturas = DB::table('dependencia')
             ->select(DB::raw('group_concat(abreviaturaDependencia) AS abreviaturaDependencia')) //Concateno los datos
            ->whereIn('idDependencia',$ids)
            ->get();
  $vble = get_object_vars($abreviaturas[0] ); //Convierto el array en string 
  $datos[$i]["abreviaturaClasificacionDocumental"] = $vble["abreviaturaDependencia"];

  $ids = explode(',', $datos[$i]["subdependenciaClasificacionDocumental"]);

   $abreviaturas = DB::table('dependencia')
             ->select(DB::raw('group_concat(abreviaturaDependencia) AS abreviaturaDependencia')) //Concateno los datos
            ->whereIn('idDependencia',$ids)
            ->get();
  $vble = get_object_vars($abreviaturas[0] ); //Convierto el array en string
  $datos[$i]["abreviaturasubClasificacionDocumental"] = $vble["abreviaturaDependencia"];

}

?>

<script>
//Envio al formulario los ids respectivos
function datosSelect(ids,abrev)
{
  //Envio los ids concatenados
  document.getElementById(document.getElementById('campo').value+'dependenciaClasificacionDocumental'+document.getElementById('registro').value).value=ids;
  document.getElementById('abreviatura'+document.getElementById('campo').value+'ClasificacionDocumental'+document.getElementById('registro').value).value=abrev;
}

    var idSerie = '<?php echo isset($idSerie) ? $idSerie : "";?>';
    var nombreSerie = '<?php echo isset($nombreSerie) ? $nombreSerie : "";?>';
    var idSubSerie = '<?php echo isset($idSubSerie) ? $idSubSerie : "";?>';
    var nombreSubSerie = '<?php echo isset($nombreSubSerie) ? $nombreSubSerie : "";?>';
    var idRetencion = '<?php echo isset($idRetencion) ? $idRetencion : "";?>';
    var anioRetencion = '<?php echo isset($anioRetencion) ? $anioRetencion : "";?>';

    var clasificaciondocumentales = '<?php echo (isset($clasificaciondocumental) ? json_encode($clasificaciondocumental->clasificaciondocumentales) : "");?>';
    clasificaciondocumentales = (clasificaciondocumentales != '' ? JSON.parse(clasificaciondocumentales) : '');
    
    var valorClasificacionDocumental = ['','','','','','','',''];

    $(document).ready(function(){ 
      clasificaciondocumental = new Atributos('clasificaciondocumental','contenedor_clasificaciondocumental','clasificaciondocumental_');
      clasificaciondocumental.campos   = ['dependenciaClasificacionDocumental','abreviaturaClasificacionDocumental','subdependenciaClasificacionDocumental','abreviaturasubClasificacionDocumental', 'Serie_idSerie','SubSerie_idSubSerie','Retencion_idRetencion','estadoClasificacionDocumental'];
      clasificaciondocumental.etiqueta = ['input','input', 'input','input','select1','select2','select3','select4'];
      clasificaciondocumental.tipo     = ['hidden','text', 'hidden','text','','','',''];
      clasificaciondocumental.estilo   = ['width: 250px;height:35px;','width: 250px;height:35px;','width: 250px;height:35px;','width: 250px;height:35px;','width: 200px;height:35px;','width: 200px;height:35px;','width: 100px;height:35px;','width: 200px;height:35px;'];
      clasificaciondocumental.clase    = ['','','','','chosen-select form-control','chosen-select form-control','chosen-select form-control','chosen-select form-control'];      
      clasificaciondocumental.nombreSerie =  JSON.parse(nombreSerie);
      clasificaciondocumental.idSerie =  JSON.parse(idSerie);
      clasificaciondocumental.nombreSubSerie =  JSON.parse(nombreSubSerie); 
      clasificaciondocumental.idSubSerie =  JSON.parse(idSubSerie);
      clasificaciondocumental.anioRetencion =  JSON.parse(anioRetencion);
      clasificaciondocumental.idRetencion =  JSON.parse(idRetencion);
      clasificaciondocumental.sololectura = [false,false,false,false,false,false,false,false];
      clasificaciondocumental.eventoclick = ['','mostrarModalDependencia(this.id)','','mostrarModalDependencia(this.id)','','','',''];

      clasificaciondocumental.valorEstado =  Array("Activo", "Inactivo");
      clasificaciondocumental.nombreEstado =  Array("Activo", "Inactivo");
      for(var j=0, k = clasificaciondocumentales.length; j < k; j++)
      {
        clasificaciondocumental.agregarCampos(JSON.stringify(clasificaciondocumentales[j]),'L');
      }

    });

  </script>


	 @if(isset($clasificaciondocumental))
    @if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
      {!!Form::model($clasificaciondocumental,['route'=>['clasificaciondocumental.destroy',$clasificaciondocumental->idClasificacionDocumentalEnc],'method'=>'DELETE'])!!}
    @else
      {!!Form::model($clasificaciondocumental,['route'=>['clasificaciondocumental.update',$clasificaciondocumental->idClasificacionDocumentalEnc],'method'=>'PUT'])!!}
    @endif
  @else
    {!!Form::open(['route'=>'clasificaciondocumental.store','method'=>'POST'])!!}
  @endif


<div id='form-section' >

  <fieldset id="clasificaciondocumental-form-fieldset">   
        <div class="panel-body">
          <div class="form-group" id='test'>
            <div class="col-sm-12">
              <div class="row show-grid">
               <div style="width: 1240px; display: inline-block; ">
               <div class="pestana">Clasificaci&oacute;n documental</div>
                <div class="col-md-1" style="width: 40px; height:42px; cursor:pointer;" onclick="clasificaciondocumental.agregarCampos(valorClasificacionDocumental,'A')">
                  <span class="glyphicon glyphicon-plus"></span>
                </div>
                <div class="col-md-1" style="width: 250px;">Dependencia</div>
                <div class="col-md-1" style="width: 250px;">Sub Dependencia</div>
                <div class="col-md-1" style="width: 200px;">Serie</div>
                <div class="col-md-1" style="width: 200px;">Sub Serie</div>
                <div class="col-md-1" style="width: 100px;">TRD</div>
                <div class="col-md-1" style="width: 200px;">Estado</div>
                <div id="contenedor_clasificaciondocumental"> 
                </div>
                </div>
              </div>
            </div>
          </div>
        </div>
       {!!Form::hidden('idClasificacionDocumentalEnc', '1', array('id' => 'idClasificacionDocumentalEnc'))!!}
       {!!Form::hidden('registro', null, array('id' => 'registro'))!!}
       {!!Form::hidden('campo', null, array('id' => 'campo'))!!}
    </fieldset>

	@if(isset($clasificaciondocumental))
 		@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
   			{!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
  		@else
   			{!!Form::submit('Modificar',["class"=>"btn btn-primary"])!!}
  		@endif
 	@else
  		{!!Form::submit('Adicionar',["class"=>"btn btn-primary"])!!}
 	@endif
	{!! Form::close() !!}
	</div>
</div>
@stop

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:1000px;">

    <!-- Modal content-->
    <div style="" class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Selecci&oacute;n de Dependencias</h4>
      </div>
      <div class="modal-body">
        <iframe style="width:100%; height:500px; " id="dependencia" name="dependencia" src="{!! URL::to ('dependenciaselect')!!}"> </iframe> 
      </div>
    </div>
  </div>
</div>