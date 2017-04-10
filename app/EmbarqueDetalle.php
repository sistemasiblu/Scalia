<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmbarqueDetalle extends Model
{
	protected $table ='embarquedetalle';
	protected $primaryKey = 'idEmbarqueDetalle';
	
	protected $fillable = ['Embarque_idEmbarque','Compra_idCompra','proformaEmbarqueDetalle','volumenEmbarqueDetalle','valorEmbarqueDetalle','unidadEmbarqueDetalle','pesoEmbarqueDetalle','bultoEmbarqueDetalle','facturaEmbarqueDetalle','volumenFacturaEmbarqueDetalle','valorFacturaEmbarqueDetalle','unidadFacturaEmbarqueDetalle','pesoFacturaEmbarqueDetalle','bultoFacturaEmbarqueDetalle', 'fechaReservaEmbarqueDetalle', 'fechaRealEmbarqueDetalle', 'fechaMaximaEmbarqueDetalle' ,'fechaLlegadaZonaFrancaEmbarqueDetalle','compradorEmbarqueDetalle','eventoEmbarqueDetalle','dolarEmbarqueDetalle','fechaArriboPuertoEstimadaEmbarqueDetalle', 'fechaArriboPuertoEmbarqueDetalle', 'soportePagoEmbarqueDetalle','compradorVendedorEmbarqueDetalle','cantidadContenedorEmbarqueDetalle','tipoContenedorEmbarqueDetalle','numeroContenedorEmbarqueDetalle','blEmbarqueDetalle','numeroCourrierEmbarqueDetalle','pagoEmbarqueDetalle','originalEmbarqueDetalle','descripcionEmbarqueDetalle','pagoCorreoEmbarqueDetalle','fileEmbarqueDetalle','observacionEmbarqueDetalle'];

	public $timestamps = false;	

	public function embarque()
	{
		return $this->hasOne('App\Embarque','idEmbarque');
	}

	public function compra()
	{
		return $this->hasOne('App\Compra','idCompra');
	}

}