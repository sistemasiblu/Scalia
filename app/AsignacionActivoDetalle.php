<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AsignacionActivoDetalle extends Model
{
    protected $table='asignacionactivodetalle';
    protected $primaryKey='idAsignacionActivoDetalle';
    protected $fillable=['AsignacionActivo_idAsignacionActivo', 'MovimientoActivo_idMovimientoActivo', 'Activo_idActivo', 'documentoInternoAsignacionActivo', 'Localizacion_idLocalizacion','Tercero_idResponsable'];

    public $timestamps=false;
}
