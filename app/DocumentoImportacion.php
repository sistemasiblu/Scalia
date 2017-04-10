<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoImportacion extends Model
{
	protected $table ='documentoimportacion';
	protected $primaryKey = 'idDocumentoImportacion';
	
	protected $fillable = ['codigoDocumentoImportacion', 'nombreDocumentoImportacion',
	'origenDocumentoImportacion','SistemaInformacion_idSistemaInformacion','tipoDocumentoImportacion', 'Compania_idCompania'];

	public $timestamps = false;

	public function DocumentoImportacionCorreo() 
	{
		return $this->hasMany('App\DocumentoImportacionCorreo','DocumentoImportacion_idDocumentoImportacion');
	}

	public function DocumentoImportacionPermiso() 
	{
		return $this->hasMany('App\DocumentoImportacionPermiso','DocumentoImportacion_idDocumentoImportacion');
	}

	public function Compra() 
	{
		return $this->hasMany('App\Compra','DocumentoImportacion_idDocumentoImportacion');
	}

	public function Embarque() 
	{
		return $this->hasMany('App\Embarque','DocumentoImportacion_idDocumentoImportacion');
	}

}
	