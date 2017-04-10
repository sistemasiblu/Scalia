<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoPermisoCompania extends Model
{
	protected $table ='documentopermisocompania';
	protected $primaryKey = 'idDocumentoPermisoCompania';
	
	protected $fillable = ['Compania_idCompania', 'Documento_idDocumento'];

	public $timestamps = false;	

    public function documento()
    {
    	return $this->hasOne('App\Documento','idDocumento');
    }
}