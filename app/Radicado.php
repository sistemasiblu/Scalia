<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Radicado extends Model
{
	protected $table ='radicado';
	protected $primaryKey = 'idRadicado';
	
	protected $fillable = ['codigoRadicado','Dependencia_idDependencia', 'Serie_idSerie', 'SubSerie_idSubSerie','Documento_idDocumento', 'ubicacionEstanteRadicado', 'Compania_idCompania'];

	public $timestamps = false;

	public function radicadoetiqueta() 
	{
		return $this->hasMany('App\RadicadoEtiqueta','Radicado_idRadicado');
	}

	public function radicadodocumentopropiedad() 
	{
		return $this->hasMany('App\RadicadoDocumentoPropiedad','Radicado_idRadicado');
	}

	public function radicadoversion() 
	{
		return $this->hasMany('App\RadicadoVersion','Radicado_idRadicado');
	}

	public function dependencia()
	{
		return $this->hasOne('App\Dependencia','idDependencia');
	}

	public function serie()
	{
		return $this->hasOne('App\Serie','idSerie');
	}

	public function subserie()
	{
		return $this->hasOne('App\SubSerie','idSubSerie');
	}
}	