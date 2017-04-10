<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupoEstado extends Model
{
    protected $table = 'grupoestado';
    protected $primaryKey = 'idGrupoEstado';

    protected $fillable = ['codigoGrupoEstado','nombreGrupoEstado','Compania_idCompania'];

    public $timestamps = false;

    public function estadoCRM()
    {
		return $this->hasMany('App\EstadoCRM','GrupoEstado_idGrupoEstado');
    }

    public function eventoCRM()
    {
		return $this->hasMany('App\EventoCRM','GrupoEstado_idGrupoEstado');
    }

    public function categoriaCRM()
    {
		return $this->hasMany('App\CategoriaCRM','GrupoEstado_idGrupoEstado');
    }

    public function origenCRM()
    {
		return $this->hasMany('App\OrigenCRM','GrupoEstado_idGrupoEstado');
    }

    public function acuerdoservicio()
    {
		return $this->hasMany('App\AcuerdoServicio','GrupoEstado_idGrupoEstado');
    }

     public function grupoestadoasesor()
    {
        return $this->hasMany('App\GrupoEstadoAsesor','GrupoEstado_idGrupoEstado');
    }

}
