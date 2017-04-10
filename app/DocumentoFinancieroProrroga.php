<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoFinancieroProrroga extends Model
{
	protected $table ='documentofinancieroprorroga';
	protected $primaryKey = 'idDocumentoFinancieroProrroga';
	
	protected $fillable = ['DocumentoFinanciero_idDocumentoFinanciero', 'fechaProrrogaDocumentoFinancieroProrroga', 'observacionDocumentoFinancieroProrroga'];

	public $timestamps = false;

	public function documentofinanciero()
    {
    	return $this->hasOne('App\DocumentoFinanciero','idDocumentoFinanciero');
    }
}
	