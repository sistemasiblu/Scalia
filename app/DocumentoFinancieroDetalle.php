<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoFinancieroDetalle extends Model
{
	protected $table ='documentofinancierodetalle';
	protected $primaryKey = 'idDocumentoFinancieroDetalle';
	
	protected $fillable = ['DocumentoFinanciero_idDocumentoFinanciero', 'Compra_idCompra', 'numeroCompraDocumentoFinancieroDetalle', 'Factura_idFactura', 'numeroFacturaDocumentoFinancieroDetalle', 'valorFobDocumentoFinancieroDetalle', 'valorPagoDocumentoFinancieroDetalle'];

	public $timestamps = false;

	public function documentofinanciero()
    {
    	return $this->hasOne('App\DocumentoFinanciero','idDocumentoFinanciero');
    }
}
	