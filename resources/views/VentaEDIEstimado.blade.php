@extends('layouts.vista')
@section('titulo')<br><h3 id="titulo"><center>Tiempo Estimado de Venta</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/ventaediestimado.js')!!}
<!-- DataTables -->
{!!Html::script('DataTables/media/js/jquery.dataTables.js'); !!}
{!!Html::style('DataTables/media/css/jquery.dataTables.min.css'); !!}

{!!Form::open(['route'=>'ventaediestimado.store','method'=>'POST'])!!}
<br><br>
<div id='form-section' >
	<div class="container-fluid">
  		<fieldset id="ventaediestimado-form-fieldset">
			<input type="hidden" id="token" value="{{csrf_token()}}"/>
			<div class="container-fluid">
				<div class="form-group">
					<input id="token" name="token" type="hidden"/>
					{!!Form::label('referenciaProductoInicial', 'Referencia Inicial:', array('class' => 'col-sm-2 col-md-2 control-label')) !!}
					<div class="col-sm-4 col-md-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!!Form::text('referenciaProductoInicial',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off', 'onchange' => "condicionRango('referenciaProducto')"])!!}
						</div>
					</div>
					{!!Form::label('referenciaProductoFinal', 'Referencia Final:', array('class' => 'col-sm-2 col-md-2 control-label')) !!}
					<div class="col-sm-4 col-md-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!!Form::text('referenciaProductoFinal',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off', 'onchange' => "condicionRango('referenciaProducto')"])!!}
						</div>
					</div>
				</div>
				<br>
		    	<div class="form-group">
					{!!Form::label('Marca_idMarca', 'Marca', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar "></i>
							</span>
							{!!Form::select('Marca_idMarca',$marca,null,['class'=>'chosen-select form-control','id'=>'Marca_idMarca','placeholder'=>'TODAS'])!!}
						</div>
					</div>
					{!!Form::label('TipoProducto_idTipoProducto', 'Tipo de Producto', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-list "></i>
							</span>
							{!!Form::select('TipoProducto_idTipoProducto',$tipoproducto,null,['class'=>'chosen-select form-control','id'=>'TipoProducto_idTipoProducto','placeholder'=>'TODOS'])!!}
						</div>
					</div>
		        </div>

		    	<div class="form-group">
					{!!Form::label('TipoNegocio_idTipoNegocio', 'Tipo de Negocio', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar "></i>
							</span>
							{!!Form::select('TipoNegocio_idTipoNegocio',$tiponegocio,null,['class'=>'chosen-select form-control','id'=>'TipoNegocio_idTipoNegocio','placeholder'=>'TODOS'])!!}
						</div>
					</div>
					{!!Form::label('Temporada_idTemporada', 'Temporada', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-list "></i>
							</span>
							{!!Form::select('Temporada_idTemporada',$temporada,null,['class'=>'chosen-select form-control','id'=>'Temporada_idTemporada','placeholder'=>'TODAS'])!!}
						</div>
					</div>
		        </div>
		        
		    	<div class="form-group">
					{!!Form::label('Tercero_idTercero', 'Cliente', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar "></i>
							</span>
							{!!Form::select('Tercero_idTercero',$tercero,null,['class'=>'chosen-select form-control','id'=>'Tercero_idTercero','placeholder'=>'TODOS'])!!}
						</div>
					</div>
					{!!Form::label('Categoria_idCategoria', 'Categoria', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-list "></i>
							</span>
							{!!Form::select('Categoria_idCategoria',$categoria,null,['class'=>'chosen-select form-control','id'=>'Categoria_idCategoria','placeholder'=>'TODAS'])!!}
						</div>
					</div>
		        </div>
		        
		    	<div class="form-group">
					{!!Form::label('EsquemaProducto_idEsquemaProducto', 'Esquema', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar "></i>
							</span>
							{!!Form::select('EsquemaProducto_idEsquemaProducto',$esquemaproducto,null,['class'=>'chosen-select form-control','id'=>'EsquemaProducto_idEsquemaProducto','placeholder'=>'TODOS'])!!}
						</div>
					</div>
					<div class="col-sm-6 col-md-6">
						&nbsp;
					</div>
		        </div>	 
		    	<br><br>
				<div class="col-md-12">
					<!-- <div onclick="producto.agregarCampos(arrayProducto,'A')"> -->
						<b><a onclick="consultarProducto(1)" style="color:#000;"><span class="fa fa-check-square-o fa-lg"></span>&nbsp;&nbsp;Cargar Productos</a></b>
						&nbsp;&nbsp;
						<b><a onclick="consultarProducto(2)" style="color:#000;"><span class="fa fa-list fa-lg"></span>&nbsp;&nbsp;Seleccionar Productos</a></b>
						&nbsp;&nbsp;
					<!-- </div> -->
				</div>
				</br>
				</br>
				<div class="panel-body">
					<div class="form-group" id='test'>
						<div class="col-sm-12">
							<div class="row show-grid" style="width:1420px;">
								<div class="col-md-1" style="width:180px;height:48px;">ID</div>
								<div class="col-md-2" style="width:234px;height:48px;">Código Alterno</div>
								<div class="col-md-2" style="width:234px;height:48px;">Referencia</div>
								<div class="col-md-2" style="width:292px;height:48px;">Descripción</div>
								<div class="col-md-2" style="width:234px;height:48px;">
									<input type="number" id="diasVentaEDIEstimado" name="diasVentaEDIEstimado" value="" placeholder="Días Venta Masivo" onchange="llenarMasivo(this.value,'diasVentaEDIEstimado')"/>
								</div>
								<div class="col-md-2" style="width:234px;height:48px;">
									<input type="date" id="fechaInicioVentaEDIEstimado" name="fechaInicioVentaEDIEstimado" value="" placeholder="Fecha Inicio Masivo" onchange="llenarMasivo(this.value,'fechaInicioVentaEDIEstimado')"/>
								</div>
								<div id="contenedor_productos"> 
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- modal consulta de productos -->
				<div id="modalProductos" class="modal fade" role="dialog">
				  <div class="modal-dialog" style="width:100%;">

				    <!-- Modal content-->
				    <div style="" class="modal-content">
						<div class="modal-header">				        
							<h4 class="modal-title">Productos</h4>
						</div>
						<div class="modal-body">
					        <div class="container">
					            <div class="row">
					                <div class="container col-md-12">
										<table id="tmodalProductos" name="tmodalProductos" class="display table-bordered" width="100%">
											<thead>
											  <tr class="btn-default active">
											      <th><b>Producto</b></th>
											      <th><b>Codigo</b></th>
											      <th><b>Referencia</b></th>
											      <th><b>Descripcion</b></th>
											  </tr>
											</thead>
											<tfoot>
											  <tr class="btn-default active">
											      <th>Producto</th>
											      <th>Codigo</th>
											      <th>Referencia</th>
											      <th>Descripcion</th>
											  </tr>
											</tfoot>
										</table>                
									</div>
					            </div>
					        </div>
				        </div>
				        <div class="modal-footer">
				            <button id="btnSeleccionar" name="btnSeleccionar" type="button" class="btn btn-primary">Seleccionar</button>
				            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
				        </div>
				    </div>
				  </div>
				</div>
				<!-- fin modal consulta de productos -->
				{!!Form::submit('Guardar',["class"=>"btn btn-primary"])!!}
				{!!Form::button('Cancelar',["class"=>"btn btn-primary","onclick"=>"Limpiar()"])!!}
			</div>    
			{!!Form::close()!!}
    </fieldset>
</div>
	</div>
</div>
@stop