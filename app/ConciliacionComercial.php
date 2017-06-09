<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConciliacionComercial extends Model
{
    protected $table = 'conciliacioncomercial';
    protected $primaryKey = 'idConciliacionComercial';

    protected $fillable = ['fechaElaboracionConciliacionComercial', 'Users_idCrea', 'fechaInicialConciliacionComercial', 'fechaFinalConciliacionComercial', 'Documento_idDocumento'];

    public $timestamps = false;

    function ConciliacionComercialDetalle()
    {
    	return $this->hasMany('App\ConciliacionComercialDetalle','ConciliacionComercial_idConciliacionComercial');
    }

    function ConciliacionComercialDocumento()
    {
    	return $this->hasMany('App\ConciliacionComercialDocumento','ConciliacionComercial_idConciliacionComercial');
    }

    function ConciliacionComercialMovimiento()
    {
    	return $this->hasMany('App\ConciliacionComercialMovimiento','ConciliacionComercial_idConciliacionComercial');
    }
}
