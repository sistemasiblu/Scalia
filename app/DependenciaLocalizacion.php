<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DependenciaLocalizacion extends Model
{
	protected $table ='dependencialocalizacion';
	protected $primaryKey = 'idDependenciaLocalizacion';
	
	protected $fillable = ['Dependencia_idDependencia', 'numeroEstanteDependenciaLocalizacion', 'numeroNivelDependenciaLocalizacion', 'numeroSeccionDependenciaLocalizacion', 'codigoDependenciaLocalizacion', 'descripcionDependenciaLocalizacion', 'estadoDependenciaLocalizacion', 'capacidadDependenciaLocalizacion'];

	public $timestamps = false;	

	public function dependencia()
    {
    	return $this->hasOne('App\Dependencia','idDependencia');
    }
}