<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AsignacionActivo extends Model
{
    protected $table='asignacionactivo';
    protected $primaryKey='idAsignacionActivo';
    protected $fillable=['numeroAsignacionActivo', 'fechaHoraAsignacionActivo', 'TransaccionActivo_idTransaccionActivo', 'documentoInternoAsignacionActivo', 'Users_idCrea'];

    public $timestamps=false;
}
