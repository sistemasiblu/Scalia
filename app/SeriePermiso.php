<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SeriePermiso extends Model
{
	protected $table ='seriepermiso';
	protected $primaryKey = 'idSeriePermiso';
	
	protected $fillable = ['Serie_idSerie', 'Rol_idRol'];

	public $timestamps = false;	

	public function dependencia()
    {
    	return $this->hasOne('App\Serie','idSerie');
    }
}