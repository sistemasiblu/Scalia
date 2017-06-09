<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConciliacionComercialDetalle extends Model
{
    protected $table = 'conciliacioncomercialdetalle';
    protected $primaryKey = 'idConciliaionComercialDetalle';

    protected $fillable = ['ConciliacionComercial_idConciliacionComercial', 
                            'Movimiento_idMovimiento', 
                            'ValorConciliacion_idValorConciliacion', 
    						'valorComercialConciliacionComercialDetalle', 
                            'valorContableConciliacionComercialDetalle'];
    						
    public $timestamps = false;

    function ConciliacionComercial()
    {
    	return $this->hasOne('App\ConciliacionComercial','idConciliacioncomercial');
    }
}