<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
	protected $table ='etiqueta';
	protected $primaryKey = 'idEtiqueta';
	
	protected $fillable = ['nombreEtiqueta'];

	public $timestamps = false;

	public function radicadoetiqueta() 
	{
		return $this->hasMany('App\RadicadoEtiqueta','Etiqueta_idEtiqueta');
	}
}
	