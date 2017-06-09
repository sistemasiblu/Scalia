<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProtocoloMantenimiento extends Model
{
    protected $table='protocolomantenimiento';
    protected $primaryKey='idProtocoloMantenimiento';
    protected $fillable=[ 'nombreProtocoloMantenimiento', 'TipoActivo_idTipoActivo', 'TipoAccion_idTipoAccion'];
    public $timestamps=false;
}
