<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dependencia extends Model
{
	protected $table ='dependencia';
	protected $primaryKey = 'idDependencia';
	
	protected $fillable = ['codigoDependencia', 'nombreDependencia', 'abreviaturaDependencia', 'directorioDependencia', 'Dependencia_idPadre'];

	public $timestamps = false;	

	public function dependenciaPermiso() 
	{
		return $this->hasMany('App\DependenciaPermiso','Dependencia_idDependencia');
	}

	public function retenciondocumental() 
	{
		return $this->hasMany('App\RetencionDocumental','Dependencia_idDependencia');
	}

	public function radicado() 
	{
		return $this->hasMany('App\Radicado','Dependencia_idDependencia');
	}
}