<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenMantenimiento extends Model
{
    protected $table='ordenmantenimiento';
    protected $primaryKey='idOrdenMantenimiento';
    protected $fillable=['ProgramacionMantenimiento_idProgramacionMantenimiento', 'numeroOrdenMantenimiento', 'fechaElaboracionOrdenMantenimiento', 'asuntoOrdenMantenimiento', 'urlOrdenMantenimiento', 'fechaHoraInicioOrdenMantenimiento', 'fechaHoraFinOrdenMantenimiento', 'Localización_idLocalización', 'ProtocoloMantenimiento_idProtocoloMantenimiento', 'TipoAccion_idTipoAccion', 'TipoServicio_idTipoServicio', 'Tercero_idProveedor', 'estadoOrdenMantenimiento', 'observacionOrdenMantenimiento'
    ];

    public $timestamps=false;


}
