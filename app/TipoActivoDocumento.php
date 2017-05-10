<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoActivoDocumento extends Model
{
    protected $table='tipoactivodocumento';
    protected $primaryKey='idTipoActivoDocumento';
    protected $fillable=['TipoActivo_idTipoActivo','descripcionTipoActivoDocumento','tipoTipoActivoDocumento','vigenciaTipoActivoDocumento','costoTipoActivoDocumento'];
	public $timestamps=false;

	public function tipoactivo()
	{
    	return $this->hasOne('\App\TipoActivo','idTipoActivo');
	}

	public function activodocumento()
	{
    	return $this->hasMany('\App\ActivoDocumento','idActivoDocumento');
	}

}
