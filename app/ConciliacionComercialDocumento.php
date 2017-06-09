<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConciliacionComercialDocumento extends Model
{
    protected $table = 'conciliacioncomercialdocumento';
    protected $primaryKey = 'idConciliacionComercialDocumento';

    protected $fillable = ['ConciliacionComercial_idConciliacionComercial', 
                            'Documento_idDocumento', 
                            'observacionConciliacionComercialDocumento'];
    						
    public $timestamps = false;

    function ConciliacionComercial()
    {
    	return $this->hasOne('App\ConciliacionComercial','idConciliacioncomercial');
    }
}