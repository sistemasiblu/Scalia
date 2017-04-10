<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadicadoEtiqueta extends Model
{
	protected $table ='radicadoetiqueta';
	protected $primaryKey = 'idRadicadoEtiqueta';
	
	protected $fillable = ['Radicado_idRadicado','Etiqueta_idEtiqueta'];

	public $timestamps = false;

	public function radicado()
	{
		return $this->hasOne('App\Radicado','idRadicado');
	}

	public function etiqueta()
	{
		return $this->hasOne('App\Etiqueta','idEtiqueta');
	}
}

	