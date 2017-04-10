<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retencion extends Model
{
	protected $table ='retencion';
	protected $primaryKey = 'idRetencion';
	
	protected $fillable = ['anioRetencion', 'Compania_idCompania'];

	public $timestamps = false;	

	public function Retenciondocumental() 
	{
		return $this->hasMany('App\RetencionDocumental','Retencion_idRetencion');
	}

	public function clasificaciondocumental() 
	{
		return $this->hasMany('App\ClasificacionDocumental','Retencion_idRetencion');
	}
}