<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoCRM extends Model
{
    protected $table = 'movimientocrm';
    protected $primaryKey = 'idMovimientoCRM';

    protected $fillable = [
 	'numeroMovimientoCRM', 'asuntoMovimientoCRM', 'fechaSolicitudMovimientoCRM', 'fechaEstimadaSolucionMovimientoCRM', 'fechaVencimientoMovimientoCRM', 'fechaRealSolucionMovimientoCRM', 'prioridadMovimientoCRM', 'diasEstimadosSolucionMovimientoCRM', 'diasRealesSolucionMovimientoCRM', 'Tercero_idSolicitante', 'Tercero_idSupervisor', 'Tercero_idAsesor', 'CategoriaCRM_idCategoriaCRM', 'DocumentoCRM_idDocumentoCRM', 'LineaNegocio_idLineaNegocio', 'OrigenCRM_idOrigenCRM', 'EstadoCRM_idEstadoCRM', 'AcuerdoServicio_idAcuerdoServicio', 'EventoCRM_idEventoCRM','ClasificacionCRM_idClasificacionCRM','ClasificacionCRMDetalle_idClasificacionCRMDetalle', 'detallesMovimientoCRM', 'solucionMovimientoCRM', 'valorMovimientoCRM', 'Compania_idCompania'
    ];

    public $timestamps = false;


    public function AcuerdoServicio()
    {
        return $this->hasOne('App\AcuerdoServicio','idAcuerdoServicio','AcuerdoServicio_idAcuerdoServicio');
    }

    public function OrigenCRM()
    {
        return $this->hasOne('App\OrigenCRM','idOrigenCRM','OrigenCRM_idOrigenCRM');
    }
    
    
    public function EventoCRM()
    {
        return $this->hasOne('App\EventoCRM','idEventoCRM','EventoCRM_idEventoCRM');
    }

    public function CategoriaCRM()
    {
        return $this->hasOne('App\CategoriaCRM','idCategoriaCRM','CategoriaCRM_idCategoriaCRM');
    }

    public function LineaNegocio()
    {
        return $this->hasOne('App\LineaNegocio','idLineaNegocio','LineaNegocio_idLineaNegocio');
    }

     public function EstadoCRM()
    {
        return $this->hasOne('App\EstadoCRM','idEstadoCRM','EstadoCRM_idEstadoCRM');
    }



    public function movimientoCRMAsistentes()
    {
        return $this->hasMany('App\MovimientoCRMAsistente','MovimientoCRM_idMovimientoCRM');
    }

    public function movimientoCRMArchivos()
    {
        return $this->hasMany('App\MovimientoCRMArchivo','MovimientoCRM_idMovimientoCRM');
    }
    //nueva pestaña Relacion cargo (VACANTES)
     public function MovimientoCRMCargos()
    {
        return $this->hasMany('App\MovimientoCRMCargos','MovimientoCRM_idMovimientoCRM');
    }

    
}
