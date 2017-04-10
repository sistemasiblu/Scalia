<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrasladoDocumento extends Model
{
	protected $table ='trasladodocumento';
	protected $primaryKey = 'idTrasladoDocumento';
	
	protected $fillable = ['numeroTrasladoDocumento', 'descripcionTrasladoDocumento', 'Users_id', 'fechaElaboracionTrasladoDocumento', 'estadoTrasladoDocumento', 'fechaTrasladoDocumento', 'SistemaInformacion_idOrigen', 'SistemaInformacion_idDestino'];

	public $timestamps = false;	

	public function TrasladoDocumentoDetalle() 
	{
		return $this->hasMany('App\TrasladoDocumentoDetalle','TrasladoDocumento_idTrasladoDocumento');
	}

}