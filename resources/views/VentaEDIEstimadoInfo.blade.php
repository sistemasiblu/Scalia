@extends('layouts.vista')
@section('titulo')<br><h3 id="titulo"><center>Informe Estimado de Ventas</center></h3>@stop

@section('content')
@include('alerts.request')
{!!Html::script('js/ventaediestimadoinfo.js')!!}
 
{!!Form::open(['route'=>'ventaediestimadoinfo.store','method'=>'POST'])!!}
<br><br>
<div id='form-section' >
	<div class="container-fluid">
  		<fieldset id="ventaediestimadoinfo-form-fieldset">
			<input type="hidden" id="token" value="{{csrf_token()}}"/>
			<div class="container-fluid">
				<div class="form-group">
					{!!Form::label('referenciaProductoInicial', 'Referencia Inicial:', array('class' => 'col-sm-2 col-md-2 control-label')) !!}
					<div class="col-sm-4 col-md-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!!Form::text('referenciaProductoInicial',null,['class'=>'form-control','placeholder'=>'','autocomplete' => 'off'])!!}
						</div>
					</div>
					
				</div>
				<br>
		    	<div class="form-group">
					{!!Form::label('Periodo_idPeriodo', 'Periodo', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-4">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar "></i>
							</span>
							{!!Form::select('Periodo_idPeriodo',$periodo,null,['class'=>'chosen-select form-control','id'=>'Periodo_idPeriodo'])!!}
						</div>
					</div>

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
					
					{!!Form::label('Bodega_idBodega', 'Bodega', array('class' => 'col-sm-2 control-label')) !!}
					<div class="col-sm-10">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar "></i>
							</span>
							{!!Form::select('Bodega_idBodega',$bodega,null,['class'=>'chosen-select form-control','id'=>'Bodega_idBodega','placeholder'=>'TODOS', 'multiple'=>''])!!}
						</div>
					</div>
					<div class="col-sm-6 col-md-6">
						&nbsp;
					</div>
		        </div>	
		    	<br><br>

				{!!Form::button('Generar Informe',["class" => "btn btn-success", "onclick" => "consultarInforme()"])!!}
			</div>    
			{!!Form::close()!!}
    </fieldset>
</div>
	</div>
</div>
@stop