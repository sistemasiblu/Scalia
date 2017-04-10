<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoCRM extends Model
{
    protected $table = 'estadocrm';
    protected $primaryKey = 'idEstadoCRM';

    protected $fillable = ['nombreEstadoCRM','tipoEstadoCRM','GrupoEstado_idGrupoEstado'];

    public $timestamps = false;

    public function grupoEstado()
    {
		return $this->hasOne('App\GrupoEstado','idGrupoEstado');
    }
}
