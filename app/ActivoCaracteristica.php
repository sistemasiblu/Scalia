<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivoCaracteristica extends Model
{
    protected $table='activocaracteristica';
    protected $primaryKey='idActivoCaracteristica';
    protected $fillable=['Activo_idActivo','TipoActivoCaracteristica_idTipoActivoCaracteristica', 'descripcionActivoCaracteristica'];
    public $timestamps=false;


 public function tipoactivocaracteristica()
    {
    	return $this->hasOne('App\TipoActivoCaracteristica','idTipoActivoCaracteristica');
    }


public function activo()
    {
    	return $this->hasOne('App\Activo','idActivo');
    }
}
