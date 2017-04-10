<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class RetencionDocumental extends Model
{
    protected $table = 'retenciondocumental';
    protected $primaryKey = 'idRetencionDocumental';
    protected $fillable = ['Retencion_idRetencion', 'Dependencia_idDependencia', 'Serie_idSerie','SubSerie_idSubSerie','Documento_idDocumento','retencionGestionRetencionDocumental','retencionCentralRetencionDocumental','soporteRetencionDocumental','disposicionFinalRetencionDocumental','microfilmRetencionDocumental','procedimientoRetencionDocumental'];
    public $timestamps = false;
    
    public function Retencion()
    {
    	return $this->hasOne('App\Retencion','idRetencion');
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

    public function documento()
    {
    	return $this->hasOne('App\Documento','idDocumento');
    }
}