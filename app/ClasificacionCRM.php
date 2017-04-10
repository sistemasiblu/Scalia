<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClasificacionCRM extends Model
{
    protected $table='clasificacioncrm';
    protected $primaryKey='idClasificacionCRM';
    protected $fillable=['codigoClasificacionCRM', 'nombreClasificacionCRM','GrupoEstado_idGrupoEstado', 'Compania_idCompania'];

    public $timestamps=false;

    public function clasificacioncrmdetalle()
    {
		return $this->hasMany('\App\ClasificacionCRMDetalle','ClasificacionCRM_idClasificacionCRM');
	}
}
