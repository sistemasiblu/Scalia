<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoPropiedad extends Model
{
	protected $table ='documentopropiedad';
	protected $primaryKey = 'idDocumentoPropiedad';
	
	protected $fillable = ['ordenDocumentoPropiedad', 'Metadato_idMetadato','campoDocumentoPropiedad',
	'gridDocumentoPropiedad', 'indiceDocumentoPropiedad','validacionDocumentoPropiedad',
	'versionDocumentoPropiedad','Documento_idDocumento'];

	public $timestamps = false;	

	public function documento()
    {
    	return $this->hasOne('App\Documento','idDocumento');
    }

    public function radicadodocumentopropiedad() 
	{
		return $this->hasMany('App\RadicadoDocumentoPropiedad','DocumentoPropiedad_idDocumentoPropiedad');
	}

	public function lista()
    {
    	return $this->hasOne('App\Lista','idLista');
    }
}