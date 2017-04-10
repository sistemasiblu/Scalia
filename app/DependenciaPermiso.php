<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DependenciaPermiso extends Model
{
	protected $table ='dependenciapermiso';
	protected $primaryKey = 'idDependenciaPermiso';
	
	protected $fillable = ['Dependencia_idDependencia', 'Rol_idRol'];

	public $timestamps = false;	

	public function dependencia()
    {
    	return $this->hasOne('App\Dependencia','idDependencia');
    }
}