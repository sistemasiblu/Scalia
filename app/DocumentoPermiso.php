<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoPermiso extends Model
{
	protected $table ='documentopermiso';
	protected $primaryKey = 'idDocumentoPermiso';
	
	protected $fillable = ['Rol_idRol', 'cargarDocumentoPermiso', 'descargarDocumentoPermiso','eliminarDocumentoPermiso',
	'modificarDocumentoPermiso','consultarDocumentoPermiso','correoDocumentoPermiso','imprimirDocumentoPermiso','Documento_idDocumento'];

	public $timestamps = false;	
	public function rol()
    {
    	return $this->hasOne('App\Rol','idRol');
    }
    public function documento()
    {
    	return $this->hasOne('App\Documento','idDocumento');
    }
}