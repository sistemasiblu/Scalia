<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UbicacionDocumento extends Model
{
	protected $table ='ubicaciondocumento';
	protected $primaryKey = 'idUbicacionDocumento';
	
	protected $fillable = ['tipoUbicacionDocumento', 'DependenciaLocalizacion_idDependenciaLocalizacion', 'posicionUbicacionDocumento', 'numeroLegajoUbicacionDocumento', 'numeroFolioUbicacionDocumento', 'descripcionUbicacionDocumento', 'Tercero_idTercero', 'fechaInicialUbicacionDocumento', 'fechaFinalUbicacionDocumento', 'TipoSoporteDocumental_idTipoSoporteDocumental', 'Dependencia_idProductora', 'Compania_idCompania', 'observacionUbicacionDocumento','estadoUbicacionDocumento'];

	public $timestamps = false;
}
	