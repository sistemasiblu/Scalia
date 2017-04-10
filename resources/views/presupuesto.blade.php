@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center>Presupuesto</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/presupuesto.js')!!}

@if(isset($presupuesto))
	@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
		{!!Form::model($presupuesto,['route'=>['presupuesto.destroy',$presupuesto->idPresupuesto],'method'=>'DELETE'])!!}
	@else
		{!!Form::model($presupuesto,['route'=>['presupuesto.update',$presupuesto->idPresupuesto],'method'=>'PUT'])!!}
	@endif
@else
	{!!Form::open(['route'=>'presupuesto.store','method'=>'POST'])!!}
@endif

<?php 

$consulta = DB::Select('Select idLineaNegocio
						from lineanegocio');


$campos = '';

for ($i=0; $i <count($consulta) ; $i++) 
{
	$consultalinea = get_object_vars($consulta[$i]);

	$campos .= 'SUM(IF(LineaNegocio_idLineaNegocio = '.$consultalinea["idLineaNegocio"].', valorLineaNegocio, 0)) as LineaNegocio_'.$consultalinea["idLineaNegocio"].'_,';
}

$campos = substr($campos, 0,strlen($campos)-1);

$id = isset($presupuesto->idPresupuesto) ? $presupuesto->idPresupuesto : 0; 


$consultadetalle = DB::Select('SELECT 
Tercero_idVendedor, '.$campos.' 
FROM presupuestodetalle
left join lineanegocio on presupuestodetalle.LineaNegocio_idLineaNegocio = lineanegocio.idLineaNegocio
where Presupuesto_idPresupuesto = '.$id.'
group by Tercero_idVendedor');
?>

<script>
    var idTercero = '<?php echo isset($idTercero) ? $idTercero : "";?>';
    var nombreTercero = '<?php echo isset($nombreTercero) ? $nombreTercero : "";?>';

    var Tercero = [JSON.parse(idTercero), JSON.parse(nombreTercero)];

    var Presupuestos = '<?php echo (isset($presupuesto) ? json_encode($consultadetalle) : "");?>';
    Presupuestos = (Presupuestos != '' ? JSON.parse(Presupuestos) : '');
    var valorPresupuesto = ['',0,0,0,0,0,0];

    $(document).ready(function(){

    presupuesto = new Atributos('presupuesto','contenedor_presupuesto','presupuesto_');

	presupuesto.altura = '35px';
    presupuesto.campoid = 'idPresupuestoDetalle';
    presupuesto.campoEliminacion = 'eliminarPresupuesto';

    <?php
	
	$lineaNegocio = DB::Select('SELECT idLineaNegocio, nombreLineaNegocio from lineanegocio');    

    $campos = "presupuesto.campos   = ['Tercero_idVendedor',";
    $etiqueta = "presupuesto.etiqueta = ['select',";
    $tipo = "presupuesto.tipo = ['',";
    $estilo = "presupuesto.estilo = ['width: 200px;height:35px;',";
    $clase = "presupuesto.clase = ['chosen-select',";
    $sololectura = "presupuesto.sololectura = [true,";
    $opciones = "presupuesto.opciones = [Tercero,";
    $completar = "presupuesto.completar = ['off',";

    for ($i=0; $i < count($lineaNegocio); $i++) 
    { 
    	$lineaN = get_object_vars($lineaNegocio[$i]);
    	$campos .= "'LineaNegocio_".$lineaN["idLineaNegocio"]."_',";
    	$etiqueta .= "'input',";
    	$tipo .= "'text',";
    	$estilo .= "'width: 150px;height:35px; text-align:right;',";
    	$clase .= "'',";
    	$sololectura .= "false,";
    	$opciones .= "'',";
    	$completar .= "'off',";
    }

    $campos = substr($campos, 0,strlen($campos)-1) . "];";
    $etiqueta = substr($etiqueta, 0,strlen($etiqueta)-1) . "];";
    $tipo = substr($tipo, 0,strlen($tipo)-1) . "];";
    $estilo = substr($estilo, 0,strlen($estilo)-1) . "];";
    $clase = substr($clase, 0,strlen($clase)-1) . "];";
    $sololectura = substr($sololectura, 0,strlen($sololectura)-1) . "];";
    $opciones = substr($opciones, 0,strlen($opciones)-1) . "];";
    $completar = substr($completar, 0,strlen($completar)-1) . "];";

    echo $campos .' '. $etiqueta .' '. $tipo .' '. $estilo.' '.$clase.' '.$sololectura.' '.$opciones.' '.$completar;

	?>
      for(var j=0, k = Presupuestos.length; j < k; j++)
      {
        presupuesto.agregarCampos(JSON.stringify(Presupuestos[j]),'L');
        console.log(JSON.stringify(Presupuestos[j]))
      }
    });

  </script>
  		<input type="hidden" id="token" value="{{csrf_token()}}"/>
		<div id='form-section' >
			<fieldset id="presupuesto-form-fieldset">	
				<div class="form-group" id='test'>
					{!!Form::label('fechaInicialPresupuesto', 'Fecha Inicial', array('class' => 'col-sm-2 control-label'))!!}
					<div class="col-sm-10">
			            <div class="input-group">
			              	<span class="input-group-addon">
			                	<i class="fa fa-calendar"></i>
			              	</span>
			              	<input type="hidden" id="token" value="{{csrf_token()}}"/>
							{!!Form::text('fechaInicialPresupuesto',null,['class'=>'form-control','placeholder'=>'Ingresa la fecha inicial'])!!}
					      	{!!Form::hidden('idPresupuesto', null, array('id' => 'idPresupuesto'))!!}
					      	{!!Form::hidden('eliminarPresupuesto', null, array('id' => 'eliminarPresupuesto'))!!}
						</div>
					</div>
				</div>

				<div class="form-group" id='test'>
					{!!Form::label('fechaFinalPresupuesto', 'Fecha Final', array('class' => 'col-sm-2 control-label'))!!}
					<div class="col-sm-10">
			            <div class="input-group">
			              	<span class="input-group-addon">
			                	<i class="fa fa-calendar"></i>
			              	</span>
							{!!Form::text('fechaFinalPresupuesto',null,['class'=>'form-control','placeholder'=>'Ingresa la fecha final'])!!}
			    		</div>
			    	</div>
			    </div>

			    <div class="form-group" id='test'>
					{!!Form::label('descripcionPresupuesto', 'Descripción', array('class' => 'col-sm-2 control-label'))!!}
					<div class="col-sm-10">
			            <div class="input-group">
			              	<span class="input-group-addon">
			                	<i class="fa fa-pencil-square-o"></i>
			              	</span>
							{!!Form::text('descripcionPresupuesto',null,['class'=>'form-control','placeholder'=>'Ingresa la descripción'])!!}
			    		</div>
			    	</div>
			    </div>	

		        <div class="form-group" >
		          {!!Form::label('DocumentoCRM_idDocumentoCRM', 'Tipo', array('class' => 'col-sm-2 control-label'))!!}
		          <div class="col-sm-10" >
		            <div class="input-group">
		              <span class="input-group-addon">
		                <i class="fa fa-file" ></i>
		              </span>
		              {!!Form::select('DocumentoCRM_idDocumentoCRM',$documentocrm, (isset($presupuesto) ? $presupuesto->DocumentoCRM_idDocumentoCRM : 0),["class" => "chosen-select form-control","placeholder" =>"Seleccione un documento"])!!}
		            </div>
		          </div>
		        </div>
		        </div>

		        <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a>Lineas de negocio</a>
                      </h4>
                    </div>
                    <div id="contenido" class="panel-collapse collapse in">
				     <div class="panel-body">
				       <div class="form-group" id='test'>
				         <div class="col-sm-12">
				           <div class="panel-body" >
				             <div class="form-group" id='test'>
				               <div class="col-sm-12">
				                 <div class="row show-grid" style=" border: 1px solid #C0C0C0;">
				                   <div style="overflow:auto; height:350px;">
				                     <div style="width: 100%; display: inline-block;">
				                       <div class="col-md-1" style="width:40px;height: 42px; cursor:pointer;" onclick="presupuesto.agregarCampos(valorPresupuesto,'A')">
				                         <span class="glyphicon glyphicon-plus"></span>
				                       </div>
				                       <div class="col-md-1" style="width:200px;" >Vendedor</div>
				                       <?php
				                      	for ($i=0; $i < count($lineaNegocio); $i++) 
									    { 
									    	$lineaN = get_object_vars($lineaNegocio[$i]);
									    	echo '<div class="col-md-1" style="width:150px;">'.$lineaN["nombreLineaNegocio"].'</div>';
									    }
				                       ?>
				                       <div id="contenedor_presupuesto">
				                       </div>
				                     </div>
				                   </div>
				                 </div>
				               </div>
				             </div>
				           </div>
				         </div>
					    </div>
                      </div> 
                    </div>
                  </div>
			</fieldset>	

@if(isset($presupuesto))
	@if(isset($_GET['accion']) and $_GET['accion'] == 'eliminar')
        {!!Form::submit('Eliminar',["class"=>"btn btn-primary"])!!}
      @else
        {!!Form::submit('Modificar',["class"=>"btn btn-primary", 'id'=>'Modificar',"onclick"=>'validarFormulario(event);'])!!}
      @endif
  @else
      {!!Form::submit('Adicionar',["class"=>"btn btn-primary","onclick"=>'validarFormulario(event);'])!!}
  @endif
</div>


{!!Form::close()!!}		

@stop