<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AcuerdoServicio extends Model
{
    protected $table = 'acuerdoservicio';
    protected $primaryKey = 'idAcuerdoServicio';

    protected $fillable = ['codigoAcuerdoServicio', 'nombreAcuerdoServicio', 'tiempoAcuerdoServicio', 'unidadTiempoAcuerdoServicio', 'observacionAcuerdoServicio', 'GrupoEstado_idGrupoEstado'];

    public $timestamps = false;
}
