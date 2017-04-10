<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadicadoVersion extends Model
{
	protected $table ='radicadoversion';
	protected $primaryKey = 'idRadicadoVersion';
	
	protected $fillable = ['Radicado_idRadicado','fechaRadicado','numeroRadicadoVersion','tipoRadicadoVersion',
	'archivoRadicadoVersion'];

	public $timestamps = false;

	public function radicado()
	{
		return $this->hasOne('App\Radicado','idRadicado');
	}

	public function radicadodocumentopropiedad() 
	{
		return $this->hasMany('App\RadicadoDocumentoPropiedad','RadicadoVersion_idRadicadoVersion');
	}
}	