<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanMantenimiento extends Model
{
    protected $table='planmantenimiento';
    protected $primaryKey='idPlanMantenimiento';
    protected $fillable=['Activo_idActivo', 'actividadPlanMantenimiento', 'PlanMantenimientoAlerta_idPlanMantenimientoAlerta', 'TipoServicio_idTipoServicio', 'TipoAccion_idTipoAccion', 'prioridadPlanMantenimiento', 'tiempotareaPlanMantenimiento', 'diasparoPlanMantenimiento', 'procedimientoPlanMantenimiento'];
    public $timestamps=false;


    public function planmantenimientoparte()
	{
	    return $this->hasMany('App\PlanMantenimientoParte','idPlanMantenimientoParte');
	}

	 public function planmantenimientoalerta()
	{
	    return $this->hasone('App\PlanMantenimientoAlerta','idPlanMantenimientoAlerta');
	} 
}
