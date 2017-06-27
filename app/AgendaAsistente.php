<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaAsistente extends Model
{
    protected $table = 'agendaasistente';
    protected $primaryKey = 'idAgendaAsistente';

    protected $fillable = ['Agenda_idAgenda', 'Tercero_idAsistente', 'nombreAgendaAsistente', 'correoElectronicoAgendaAsistente'];

    public $timestamps = false;

    public function agenda()
    {
    	return $this->hasOne('App\Agenda','Agenda_idAgenda');
    }

}