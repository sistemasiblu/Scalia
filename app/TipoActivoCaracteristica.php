<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoActivoCaracteristica extends Model
{
    protected $table='tipoactivocaracteristica';
    protected $primaryKey='idTipoActivoCaracteristica';
    protected $fillable=['TipoActivo_idTipoActivo','nombreTipoActivoCaracteristica'];
	public $timestamps=false;

	public function tipoactivo()
	{
    	return $this->hasOne('\App\TipoActivo','idTipoActivo');
	}

	public function activocaracteristica()
	{
    	return $this->hasMany('\App\ActivoCaracteristica','idActivoCaracteristica');
	}

}
