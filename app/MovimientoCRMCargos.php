<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoCRMCargos extends Model
{
    protected $table = 'movimientocrmcargo';
    protected $primaryKey = 'idMovimientoCRMCargo';

	protected $fillable = ['idMovimientoCRMCargo', 'Cargo_idCargo', 'vacantesMovimientoCRMCargo','fechaEstimadaMovimientoCRMCargo','MovimientoCRM_idMovimientoCRM'];


    public $timestamps = false;

    //Relacion con el Padre Movimiento Crm Pestaña (VACANTES)
public function MovimientoCRM()
    {
        return $this->hasOne('App\MovimientoCRM','idMovimientoCRM');
    }

}

