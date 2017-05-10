<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivoDocumento extends Model
{
    protected $table='activodocumento';
    protected $primaryKey='idActivoDocumento';
    protected $fillable=['Activo_idActivo', 'TipoActivoDocumento_idTipoActivoDocumento', 'versionActivoDocumento', 'proveedorActivoDocumento', 'serialActivoDocumento', 'fechainicialActivoDocumento'];
    public $timestamps=false;


    public function tipoactivodocumento()
    {
        return $this->hasOne('App\TipoActivoDocumento','idTipoActivoDocumento');
    }

    public function activo()
    {
        return $this->hasOne('App\Activo','idActivo');
    }
}
