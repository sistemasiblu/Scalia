<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProtocoloMantenimientoTarea extends Model
{
    protected $table='protocolomantenimientotarea';
    protected $primaryKey='idProtocoloMantenimientoTarea';
    protected $fillable=[ 'ProtocoloMantenimiento_idProtocoloMantenimiento', 'descripcionProtocoloMantenimientoTarea', 'minutosProtocoloMantenimientoTarea', 'FrecuenciaMedicion_idFrecuenciaMedicion', 'TipoServicio_idTipoServicio', 'requiereParoProtocoloMantenimientoTarea'];

    public $timestamps=false;
}
