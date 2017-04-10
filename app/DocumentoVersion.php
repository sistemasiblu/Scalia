<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoVersion extends Model
{
	protected $table ='documentoversion';
	protected $primaryKey = 'idDocumentoVersion';
	
	protected $fillable = ['nivelDocumentoVersion','tipoDocumentoVersion','longitudDocumentoVersion','inicioDocumentoVersion','rellenoDocumentoVersion','Documento_idDocumento'];

	public $timestamps = false;	

    public function documento()
    {
    	return $this->hasOne('App\Documento','idDocumento');
    }
}