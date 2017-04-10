<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoFinanciero extends Model
{
	protected $table ='documentofinanciero';
	protected $primaryKey = 'idDocumentoFinanciero';
	
	protected $fillable = ['ListaFinanciacion_idListaFinanciacion', 'numeroDocumentoFinanciero', 'fechaNegociacionDocumentoFinanciero', 'fechaVencimientoDocumentoFinanciero', 'nombreEntidadDocumentoFinanciero', 'valorDocumentoFinanciero'];

	public $timestamps = false;

	public function documentofinancierodetalle() 
	{
		return $this->hasMany('App\DocumentoFinancieroDetalle','DocumentoFinanciero_idDocumentoFinanciero');
	}

	public function documentofinancieroprorroga() 
	{
		return $this->hasMany('App\DocumentoFinancieroProrroga','DocumentoFinanciero_idDocumentoFinanciero');
	}
}
	