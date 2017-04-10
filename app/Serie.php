<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
	protected $table ='serie';
	protected $primaryKey = 'idSerie';
	
	protected $fillable = ['directorioSerie','codigoSerie', 'nombreSerie', 'Compania_idCompania'];

	public $timestamps = false;

	public function seriePermiso() 
	{
		return $this->hasMany('App\SeriePermiso','Serie_idSerie');
	}

	public function subseries() 
	{
		return $this->hasMany('App\SubSerie','Serie_idSerie');
	}

	public function retenciondocumental() 
	{
		return $this->hasMany('App\RetencionDocumental','Serie_idSerie');
	}

	public function clasificaciondocumental() 
	{
		return $this->hasMany('App\ClasificacionDocumental','Serie_idSerie');
	}

	public function radicado() 
	{
		return $this->hasMany('App\Radicado','Serie_idSerie');
	}
}
	