<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
	protected $table ='documento';
	protected $primaryKey = 'idDocumento';
	
	protected $fillable = ['codigoDocumento', 'nombreDocumento','directorioDocumento', 'tipoDocumento',
	'origenDocumento','SistemaInformacion_idSistemaInformacion','tipoConsultaDocumento',
	'tablaDocumento','consultaDocumento','filtroDocumento','controlVersionDocumento','trazabilidadMetadatosDocumento',
	'concatenarNombreDocumento'];

	public $timestamps = false;

	public function SubSerie() 
	{
		return $this->hasMany('App\SubSerieDetalle','Documento_idDocumento');
	}

	public function sistemainformacion()
	{
		return $this->hasOne('App\SistemaInformacion','idSistemaInformacion');
	}

	public function Documentopermiso() 
	{
		return $this->hasMany('App\DocumentoPermiso','Documento_idDocumento');
	}

	public function documentoPermisoCompania() 
	{
		return $this->hasMany('App\DocumentoPermisoCompania','Documento_idDocumento');
	}

	public function Documentoversion() 
	{
		return $this->hasMany('App\DocumentoVersion','Documento_idDocumento');
	}

	public function Documentopropiedad() 
	{
		return $this->hasMany('App\DocumentoPropiedad','Documento_idDocumento');
	}

	public function retenciondocumental() 
	{
		return $this->hasMany('App\RetencionDocumental','Documento_idDocumento');
	}

}
	