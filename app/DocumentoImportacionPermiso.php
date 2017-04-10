<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoImportacionPermiso extends Model
{
	protected $table ='documentoimportacionpermiso';
	protected $primaryKey = 'idDocumentoImportacionPermiso';
	
	protected $fillable = ['Rol_idRol', 'agregarDocumentoImportacionPermiso', 'descargarDocumentoImportacionPermiso','consultarDocumentoImportacionPermiso', 'modificarDocumentoImportacionPermiso','imprimirDocumentoImportacionPermiso','correoDocumentoImportacionPermiso','eliminarDocumentoImportacionPermiso','DocumentoImportacion_idDocumentoImportacion'];

	public $timestamps = false;	

	public function rol()
    {
    	return $this->hasOne('App\Rol','idRol');
    }
    public function documentoimportacion()
    {
    	return $this->hasOne('App\DocumentoImportacion','idDocumentoImportacion');
    }
}