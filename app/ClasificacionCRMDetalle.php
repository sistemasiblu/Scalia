<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClasificacionCRMDetalle extends Model
{
    protected $table='clasificacioncrmdetalle';
    protected $primaryKey='idClasificacionCRMDetalle';
    protected $fillable=['codigoClasificacionCRMDetalle', 'nombreClasificacionCRMDetalle', 'ClasificacionCRM_idClasificacionCRM'];
    public $timestamps=false;

    public function clasificacioncrm()
    {
		return $this->hasOne('\App\ClasificacionCRM','idClasificacionCRM');
	}
}
