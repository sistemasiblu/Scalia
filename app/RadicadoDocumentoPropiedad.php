<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadicadoDocumentoPropiedad extends Model
{
	protected $table ='radicadodocumentopropiedad';
	protected $primaryKey = 'idRadicadoDocumentoPropiedad';
	
	protected $fillable = ['Radicado_idRadicado','DocumentoPropiedad_idDocumentoPropiedad',
	'valorRadicadoDocumentoPropiedad','editorRadicadoDocumentoPropiedad','RadicadoVersion_idRadicadoVersion'];

	public $timestamps = false;

	public function radicado()
	{
		return $this->hasOne('App\Radicado','idRadicado');
	}

	public function radicadoversion()
	{
		return $this->hasOne('App\RadicadoVersion','idRadicadoVersion');
	}

	public function documentopropiedad()
	{
		return $this->hasOne('App\DocumentoPropiedad','idDocumentoPropiedad');
	}
}

	