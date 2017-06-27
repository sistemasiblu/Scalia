<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';
    protected $primaryKey = 'idAgenda';

    protected $fillable = ['asuntoAgenda', 'fechaHoraInicioAgenda', 'fechaHoraFinAgenda', 'urlAgenda', 'Tercero_idSupervisor', 'Tercero_idResponsable', 'MovimientoCRM_idMovimientoCRM', 'ubicacionAgenda', 'porcentajeEjecucionAgenda', 'detallesAgenda','CategoriaAgenda_idCategoriaAgenda','Compania_idCompania'];

    public $timestamps = false;

    public function agendaseguimiento()
    {
    	return $this->hasMany('App\AgendaSeguimiento','Agenda_idAgenda');
    }

    public function agendaasistente()
    {
    	return $this->hasMany('App\AgendaAsistente','Agenda_idAgenda');
    }
}