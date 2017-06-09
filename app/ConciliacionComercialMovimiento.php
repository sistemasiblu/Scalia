<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConciliacionComercialMovimiento extends Model
{
    protected $table = 'conciliacioncomercialmovimiento';
    protected $primaryKey = 'idConciliacionComercialMovimiento';

    protected $fillable = ['ConciliacionComercial_idConciliacionComercial', 
                            'Movimiento_idMovimiento', 
                            'observacionConciliacionComercialMovimiento'];
    						
    public $timestamps = false;

    function ConciliacionComercial()
    {
    	return $this->hasOne('App\ConciliacionComercial','idConciliacioncomercial');
    }
}