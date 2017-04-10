<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClasificacionDocumental extends Model
{
	protected $table ='clasificaciondocumental';
	protected $primaryKey = 'idClasificacionDocumental';
	
	protected $fillable = ['dependenciaClasificacionDocumental','subdependenciaClasificacionDocumental','Serie_idSerie','SubSerie_idSubSerie','Retencion_idRetencion','estadoClasificacionDocumental','ClasificacionDocumentalEnc_idClasificacionDocumentalEnc'];

	public $timestamps = false;	

    public function clasificaciondocumentalenc()
    {
        return $this->hasOne('App\ClasificacionDocumentalEnc','idClasificacionDocumentalEnc');
    }

	public function serie()
    {
    	return $this->hasOne('App\Serie','idSerie');
    }

    public function subserie()
    {
    	return $this->hasOne('App\SubSerie','idSubSerie');
    }

    public function retencion()
    {   
        return $this->hasOne('App\Retencion','idRetencion');
    }
    

}