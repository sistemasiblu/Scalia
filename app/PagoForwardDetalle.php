<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagoForwardDetalle extends Model
{
	protected $table ='pagoforwarddetalle';
	protected $primaryKey = 'idPagoForwardDetalle';
	
	protected $fillable = ['PagoForward_idPagoForward', 'Temporada_idTemporada', 'nombreTemporadaPagoForwardDetalle', 'Compra_idCompra', 'numeroCompraPagoForwardDetalle', 'DocumentoFinanciero_idDocumentoFinanciero', 'numeroDocumentoFinancieroPagoForwardDetalle', 'facturaPagoForwardDetalle', 'fechaFacturaPagoForwardDetalle', 'valorFacturaPagoForwardDetalle', 'valorPagadoPagoForwardDetalle'];

	public $timestamps = false;	

	public function pagoforward() 
	{
		return $this->hasOne('App\PagoForward','idPagoForward');
	}
}