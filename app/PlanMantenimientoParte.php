<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanMantenimientoParte extends Model
{
    protected $table='planmantenimientoparte';
    protected $primaryKey='idPlanMantenimientoParte';
    protected $fillable=['Activo_idActivo', 'Activo_idParte', 'PlanMantenimiento_idPlanMantenimiento'];
    public $timestamps=false;

    public function activo()
	{
	    return $this->hasMany('App\Activo','idActivo');
	}  

	public function planmantenimiento()
	{
	    return $this->hasOne('App\PlanMantenimiento','idPlanMantenimiento');
	}  

}
