<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoImportacionCorreo extends Model
{
    protected $table ='documentoimportacioncorreo';
	protected $primaryKey = 'idDocumentoImportacionCorreo';
	
	protected $fillable = ['DocumentoImportacion_idDocumentoImportacion', 'tipoDocumentoImportacionCorreo','Documento_idDocumento'];

	public $timestamps = false;

	public function documentoimportacion()
    {
    	return $this->hasOne('App\DocumentoImportacion','idDocumentoImportacion');
    }
}
