<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoActivo extends Model
{
    protected $table='tipoactivo';
    protected $primaryKey='idTipoActivo';
    protected $fillable=['codigoTipoActivo','nombreTipoActivo'];
    public $timestamps=false;

    public function tipoactivocaracteristica()
    {
		return $this->hasMany('\App\TipoActivoCaracteristica','TipoActivo_idTipoActivo');
	}

    public function tipoactivoparte()
    {
        return $this->hasMany('\App\TipoActivoParte','TipoActivo_idTipoActivo');
    }

    public function tipoactivocomponente()
    {
        return $this->hasMany('\App\tipoactivocomponente','TipoActivo_idTipoActivo');
    }

	public function tipoactivodocumento()
    {
		return $this->hasMany('\App\TipoActivoDocumento','TipoActivo_idTipoActivo');
	}


    public function activo()
    {
        return $this->hasMany('App\Activo','idActivo');
    }
}
