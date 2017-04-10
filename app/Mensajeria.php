<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mensajeria extends Model
{
	protected $table ='mensajeria';
	protected $primaryKey = 'idMensajeria';
	
	protected $fillable = ['tipoCorrespondenciaMensajeria', 'tipoEnvioMensajeria', 'prioridadMensajeria', 'Radicado_idRadicado', 'fechaEnvioMensajeria', 'descripcionMensajeria', 'transportadorMensajeria', 'Tercero_idTransportador', 'estadoEntregaMensajeria', 'destinatarioMensajeria', 'Tercero_idDestinatario', 'direccionEntregaMensajeria', 'seccionEntregaMensajeria', 'fechaLimiteMensajeria', 'fechaEntregaMensajeria', 'observacionMensajeria', 'numeroGuiaMensajeria', 'Users_idCrea', 'Users_idModifica', 'created_at', 'updated_at'];

	public $timestamps = true;
}
	