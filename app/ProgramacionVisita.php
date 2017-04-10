<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProgramacionVisita extends Model
{
    protected $table ='programacionvisita';
	protected $primaryKey = 'idProgramacionVisita';
	
	protected $fillable = ['tipoDocumentoVisitanteProgramacionVisita', 'numeroDocumentoVisitanteProgramacionVisita', 'nombreVisitanteProgramacionVisita', 'apellidoVisitanteProgramacionVisita', 'nombreResponsableProgramacionVisita', 'Tercero_idResponsable', 'dependenciaProgramacionVisita', 'fechaIngresoProgramacionVisita', 'tiempoEstimadoProgramacionVisita', 'detalleProgramacionVisita'];

	public $timestamps = false;	
}
