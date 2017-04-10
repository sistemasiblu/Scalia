<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SistemaInformacion extends Model
{
	protected $table ='sistemainformacion';
	protected $primaryKey = 'idSistemaInformacion';

	protected $fillable = ['codigoSistemaInformacion', 'nombreSistemaInformacion', 'webSistemaInformacion','ipSistemaInformacion','puertoSistemaInformacion','usuarioSistemaInformacion','claveSistemaInformacion','bdSistemaInformacion','motorbdSistemaInformacion'];

	public $timestamps = false;

	    public function documento() 
	{
		return $this->hasMany('App\Documento','SistemaInformacion_idSistemaInformacion');
	}
}
	