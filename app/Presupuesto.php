<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    protected $table = 'presupuesto';
    protected $primaryKey = 'idPresupuesto';

    protected $fillable = [
					    'fechaInicialPresupuesto', 
					    'fechaFinalPresupuesto',
                        'descripcionPresupuesto',
                        'DocumentoCRM_idDocumentoCRM'
					    ];
    public $timestamps = false;

    public function presupuestodetalle()
    {
        return $this->hasMany('App\PresupuestoDetalle','Presupuesto_idPresupuesto');
    }

    public function documentocrm()
    {
        return $this->hasOne('App\DocumentoCRM','idDocumentoCRM');
    }

    public function lineaNegocio()
    {
        return $this->hasOne('App\LineaNegocio','idLineaNegocio');
    }

    public function tercero()
    {
        return $this->hasOne('App\Tercero','idTercero');
    }

}