<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubSeriePermiso extends Model
{
	protected $table ='subseriepermiso';
	protected $primaryKey = 'idSubSeriePermiso';
	
	protected $fillable = ['SubSerie_idSubSerie', 'Rol_idRol'];

	public $timestamps = false;	

	public function subserie()
    {
    	return $this->hasOne('App\SubSerie','idSubSerie');
    }
}