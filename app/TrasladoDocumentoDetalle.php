<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrasladoDocumentoDetalle extends Model
{
	protected $table ='trasladodocumentodetalle';
	protected $primaryKey = 'idTrasladoDocumentoDetalle';
	
	protected $fillable = ['TrasladoDocumento_idTrasladoDocumento', 'Documento_idOrigen', 'documentoOrigenTrasladoDocumentoDetalle', 'DocumentoConcepto_idOrigen', 'documentoConceptoOrigenTrasladoDocumentoDetalle', 'Movimiento_idOrigen', 'numeroOrigenTrasladoDocumentoDetalle', 'Tercero_idOrigen', 'terceroOrigenTrasladoDocumentoDetalle', 'fechaOrigenTrasladoDocumentoDetalle', 'Documento_idDestino', 'documentoDestinoTrasladoDocumentoDetalle', 'DocumentoConcepto_idDestino', 'documentoConceptoDestinoTrasladoDocumentoDetalle', 'Tercero_idDestino', 'terceroDestinoTrasladoDocumentoDetalle', 'observacionTrasladoDocumentoDetalle'];

	public $timestamps = false;	

	public function TrasladoDocumento()
    {
    	return $this->hasOne('App\TrasladoDocumento','idTrasladoDocumento');
    }

}