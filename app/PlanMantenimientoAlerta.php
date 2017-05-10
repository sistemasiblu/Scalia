<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanMantenimientoAlerta extends Model
{
   protected $table='planmantenimientoalerta';
   protected $primaryKey='idPlanMantenimientoAlerta';
   protected $fillable=
   [  'idPlanMantenimientoAlerta', 'nombrePlanMantenimientoAlerta', 'correoParaPlanMantenimientoAlerta', 'correoCopiaPlanMantenimientoAlerta', 'correoCopiaOcultaPlanMantenimientoAlerta', 'correoAsuntoPlanMantenimientoAlerta', 'correoMensajePlanMantenimientoAlerta', 'tareaFechaInicioPlanMantenimientoAlerta', 'tareaHoraPlanMantenimientoAlerta', 'tareaDiaLaboralPlanMantenimientoAlerta', 'tareaIntervaloPlanMantenimientoAlerta','tareaDiasPlanMantenimientoAlerta', 'tareaMesesPlanMantenimientoAlerta'
   ];
   public $timestamps=false;

    public function planmantenimiento()
	{
	    return $this->hasMany('App\PlanMantenimiento','idPlanMantenimiento');
	}
}
