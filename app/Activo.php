<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    protected $table='activo';
    protected $primaryKey='idActivo';
    protected $fillable=['codigoActivo', 'nombreActivo', 'TipoActivo_idTipoActivo', 'codigobarraActivo', 'estadoActivo', 'clasificacionActivo', 'marcaActivo', 'serieActivo', 'pesoActivo', 'altoActivo', 'anchoActivo', 'largoActivo', 'modeloActivo', 'volumenActivo'];
    public $timestamps=false;


    public function tipoactivo()
    {
    	return $this->hasOne('App\TipoActivo','idTipoActivo');
    }

 public function activocaracteristica()
    {
    	return $this->hasMany('App\ActivoCaracteristica','idActivoCaracteristica');
    }

    public function activodocumento()
    {
        return $this->hasMany('App\ActivoDocumento','idActivoDocumento');
    }

    public function activoparte()
    {
        return $this->hasMany('App\ActivoParte','idActivoParte');
    }

    public function activocomponente()
    {
        return $this->hasMany('App\ActivoComponente','idActivoComponente');
    }
}
