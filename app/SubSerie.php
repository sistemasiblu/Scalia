<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubSerie extends Model
{
	protected $table ='subserie';
	protected $primaryKey = 'idSubSerie';
	
	protected $fillable = ['codigoSubSerie', 'nombreSubSerie','directorioSubSerie', 'Serie_idSerie', 'Compania_idCompania'];

	public $timestamps = false;	

	public function serie()
	{
		return $this->hasOne('App\Serie','idSerie');
	}

	public function subseriedetalle() 
	{
		return $this->hasMany('App\SubSerieDetalle','SubSerie_idSubSerie');
	}

	public function subseriepermiso() 
	{
		return $this->hasMany('App\SubSeriePermiso','SubSerie_idSubSerie');
	}

	public function retenciondocumental() 
	{
		return $this->hasMany('App\RetencionDocumental','SubSerie_idSubSerie');
	}

	public function clasificaciondocumental() 
	{
		return $this->hasMany('App\ClasificacionDocumental','SubSerie_idSubSerie');
	}

	public function radicado() 
	{
		return $this->hasMany('App\Radicado','SubSerie_idSubSerie');
	}
}